<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

//---------------------------------------------  NEW CODE  ----------------------------------//

//---------------------------  ตรวจสอบออเดอร์ที่เปิดค้างไว้ยังไม่บันทึกของพนักงานคนนี้  --------------------------//
if( isset( $_GET['checkOrderNotSave'] ) )
{
	$sc = 'ok';
	$uid	= $_COOKIE['user_id'];
	$cm	= new consignment();
	$rs	= $cm->isSaved($uid);
	if( $rs !== FALSE )
	{
		$sc = $rs;	
		setError('warning', 'ยังไม่ได้บันทึกออเดอร์นี้');
	}
	echo $sc;
}

//------------------------  เพิ่มออเดอร์ใหม่  ----------------------------//
if( isset( $_GET['addNewOrder'] ) )
{
	$ss	= 'fail';
	$sc 	= TRUE;
	$ds	= array(
						"reference"		=> get_max_role_reference("PREFIX_CONSIGNMENT", 5),
						"id_customer"	=> $_POST['id_customer'],
						"id_employee"	=> getCookie('user_id'),
						"id_cart"			=> 0,
						"current_state"	=> 1,
						"payment"		=> 'ฝากขาย',
						"comment"		=> $_POST['remark'],
						"role"				=> 5,
						"date_add"		=> dbDate($_POST['date'], TRUE)		
					);
	
	$order	= new order();
	startTransection();
	$rs 		= $order->add($ds); /// If success will get id_order back
	if( $rs === FALSE )
	{
		$sc = FALSE;
	}
	else
	{
		$data 	= array("id_order" => $rs, "id_customer" => $_POST['id_customer'], "id_zone" => $_POST['id_zone']);	
		$cm		= new consignment();
		$rd		= $cm->addConsignment($data);
		if( $rd === FALSE ){ $sc = FALSE; }
	}
	if( $sc === FALSE )
	{ 
		dbRollback(); 
	}
	else
	{
		$ss = $rs;
		commitTransection();	
	}
	echo $ss;		
}


//---------------------------  แก้ไขเอกสาร  ---------------------------//
if( isset( $_GET['updateOrder'] ) )
{
	$sc 		= 'fail';
	$id_order = $_POST['id_order'];
	$ds		= array(
						"id_customer"	=> $_POST['id_customer'],
						"id_employee"	=> getCookie('user_id'),
						"comment"		=> $_POST['remark'],
						"date_add"		=> dbDate($_POST['date'], TRUE)		
					);
	$dr		= array("id_customer" => $_POST['id_customer'], "id_zone" => $_POST['id_zone']);
	$order	= new order();
	$cm		= new consignment();
	startTransection();
	$rs = $order->updateOrder($id_order, $ds);
	$rd = $cm->updateConsignment($id_order, $dr);
	if( $rs === TRUE && $rd === TRUE )
	{
		$sc = 'success';
		commitTransection();
	}
	else
	{
		dbRollback();
	}
	echo $sc;	
}


//-----------------------------------------  State Change  ---------------------//
if( isset( $_GET['edit'] ) && isset( $_GET['state_change'] ) )
{
	$id_order	= $_POST['id_order'];
	$id_emp 		= $_POST['id_employee'];
	$state 		= $_POST['order_state'];
	
	if( $state != 0 )
	{
		startTransection();
		$rs = order_state_change( $id_order, $state, $id_emp);
		if( $rs === TRUE )
		{
			commitTransection();
			setError('message', 'เปลี่ยนสถานะเรียบร้อย');
		}
		else
		{
			dbRollback();	
			setError('error', 'เปลี่ยนสถานะไม่สำเร็จ');
		}
	}
	else
	{
		setError('error', 'คุณไม่ได้เลือกสถานะ');
	}
	header("location: ../index.php?content=consignment&id_order=$id_order&view_detail");
}


//---------------------------------------------  END NEW CODE  ----------------------------------//


//*********************************** เพิ่มสินค้าในออเดอร์ (add order detail ) ******************************************//
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_customer = $order->id_customer;
	$order_qty = $_POST['order_qty'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$i = 0;
	$n = 1;
	foreach ($id_product_attribute as $id ){
		if($order_qty[$i] !=""){
			$product = new product();
			$customer = new customer($id_customer);
			$id_product = $product->getProductId($id_product_attribute);
			$product->product_detail($id_product, $order->id_customer);
			$product->product_attribute_detail($id);
			$total_amount = $order_qty[$i]*$product->product_sell;
				if($order->insertDetail($id, $order_qty[$i])){
						$message = "เพิ่ม $n รายการเรียบร้อย";
					$i++;
					$n++;
						}else{
					$message = "ทำรายการสำเร็จ $n รายการแรกเท่านั้น";
					header("location: ../index.php?content=consignment&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
					exit;
						}
		}else{
			$i++;
		}
	}
	header("location: ../index.php?content=consignment&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
}



if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	$now = date("Y-m-d H:i:s");
	dbQuery("UPDATE tbl_order SET order_status = 1, date_add='$now' WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=consignment&message=$message");
}


if( isset($_GET['edit_doc_head']) && isset($_GET['id_order']) )
{
	$id_order = $_GET['id_order'];
	$remark = $_GET['remark'];
	$id_customer = $_GET['id_customer'];
	$id_zone = $_GET['id_zone'];
	$doc_date = dbDate($_GET['doc_date'], true);
	$qs = dbQuery("UPDATE tbl_order SET id_customer = ".$id_customer." , comment = '".$remark."' , date_add = '".$doc_date."' WHERE id_order = ".$id_order);
	if($qs)
	{
		$qr = dbQuery("UPDATE tbl_order_consignment SET id_customer = ".$id_customer." , id_zone = ".$id_zone." WHERE id_order = ".$id_order);
		echo "success";
	}else{
		echo "fail";
	}	
}

if(isset($_GET['clear_filter']))
{
	setcookie("consign_from_date","",time()-3600,"/");
	setcookie("consign_to_date","",time()-3600, "/");
	setcookie("consign_search_text", "", time()-3600, "/");
	setcookie("consign_filter", "", time()-3600, "/");
	echo "success";	
}
?>