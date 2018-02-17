<?php 
require_once "../../library/config.php";
require_once "../../library/functions.php";
require_once "../function/tools.php";
require_once "../function/qc_helper.php";
require_once "../function/prepare_helper.php";

if( isset( $_GET['getTopTable'] ) )
{
	$sc = 'fail';
	$id_order 	= $_POST['id_order'];
	$qs = dbQuery("SELECT tbl_order_detail.* FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = ".$id_order." AND valid_detail = 0 AND is_visual = 0 ORDER BY id_product_attribute ASC");
	if( dbNumRows($qs) > 0 )
	{
		$ds 		= array();
		$product	= new product();
		$show 	= getCookie('showZone') === FALSE ? 0 : getCookie('showZone');
		while( $rs = dbFetchArray($qs)	)
		{
			$id_pa 		= $rs['id_product_attribute'];
			$orderQty	= $rs['product_qty'];	
			$prepared	= getBufferQty($id_order, $id_pa);
			$balance		= $orderQty - $prepared;
			$inZone		= $product->stock_in_zone($id_pa);
			$arr = array(
						"id_pa"		=> $id_pa,
						"image"		=> $product->get_product_attribute_image($id_pa, 1),
						"barcode"	=> $rs['barcode'],
						"product"		=> $rs['product_reference']. ' : '. $rs['product_name'],
						"orderQty"	=> number_format($orderQty),
						"prepared"	=> number_format($prepared),
						"balance"		=> number_format($balance),
						"inZone"		=> $inZone
						);
			if( $show ){ $arr['show'] = $show; }
			array_push($ds, $arr);			
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}

if( isset( $_GET['getLastTable'] ) )
{
	$sc = 'fail';
	$id_order 	= $_POST['id_order'];
	$qs = dbQuery("SELECT tbl_order_detail.* FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = ".$id_order." AND (valid_detail = 1 OR is_visual = 1) ORDER BY id_product_attribute ASC");
	if( dbNumRows($qs) > 0 )
	{
		$ds 		= array();
		$product	= new product();
		while( $rs = dbFetchArray($qs)	)
		{
			$id_pa 		= $rs['id_product_attribute'];
			$orderQty	= $rs['product_qty'];	
			$prepared	= getBufferQty($id_order, $id_pa);
			$balance		= $orderQty - $prepared;
			$fromZone	= product_from_zone($id_order, $id_pa);
			$arr = array(
						"image"		=> $product->get_product_attribute_image($id_pa, 1),
						"barcode"	=> $rs['barcode'],
						"product"		=> $rs['product_reference']. ' : '. $rs['product_name'],
						"orderQty"	=> number_format($orderQty),
						"prepared"	=> number_format($prepared),
						"balance"		=> number_format($balance),
						"fromZone"	=> $fromZone
						);
			array_push($ds, $arr);			
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}

//------------------------------------   บันทึกการจัด   --------------------//
if( isset( $_GET['perparedItem'] ) )
{
	$sc 				= 'สินค้าผิดกรุณาตรวจสอบ';
	$id_order 		= $_POST['id_order'];
	$id_emp		 	= getCookie('user_id');
	$id_zone 		= $_POST['id_zone'];
	$barcode 		= $_POST['barcode'];
	$input_qty 		= $_POST['qty'];
	$product 		= new product();
	$arr 				= $product->check_barcode($barcode);
	$id_pa 			= $arr['id_product_attribute'];
	$qty 				= $input_qty * $arr['qty'];
	$id_pd 			= $product->getProductId($id_pa);
	$id_wh			= get_warehouse_by_zone($id_zone);
	$valid				= check_product_in_order($id_pa, $id_order);
	if( $valid === TRUE )
	{
		
		$order_qty 		= sumOrderQty($id_order, $id_pa);  //--- ยอดที่สั่ง
		$current_qty 	= getBufferQty($id_order, $id_pa);	//---- ยอดที่จัดไปแล้ว
		$final_qty		= $current_qty + $qty;  //---- หากรวมกับยอดที่จัดมาครั้งนี้จะได้เท่านี้ 
		//-------------  ถ้าจัดสินค้าเกิน  ------------//
		if( $final_qty > $order_qty )
		{
			$err_qty =  $final_qty -$order_qty;
			$sc = "สินค้าเกิน $err_qty ตัว กรุณาคืนที่เดิม $qty ตัว แล้วค่อยจัดใหม่";
			$valid = FALSE;
		}
		
		//-----------------  ถ้าในโซนไม่มีสินค้าอยู่  ------------//
		$stock	= getStockInZone($id_zone, $id_pa);  //---- จำนวนสินค้าในโซนที่ยิงมา
		if( $stock == 0 )
		{
			$sc = "ไม่มีสินค้าที่เลือกในโซนนี้ กรุณาตรวจสอบ";
			$valid = FALSE;
		}
		
		//------------------  ถ้าจำนวนที่ยิงมาน้อยกว่าที่มีในโซน  -----------//
		else if( $stock < $qty )
		{
			$sc = "สินค้าในโซนนี้ มีน้อยกว่ายอดที่คุณป้อน กรุณาตรวจสอบ";
			$valid = FALSE;
		}
		
		//------------------  ตรวจสอบอีกครั้งว่ามีข้อผิดพลาดอะไรอีกมั้ย  ----------------//
		if( $valid !== FALSE )
		{
			$sd = TRUE;
			startTransection();
			//-------------------  ถ้าไม่มีข้อผิดพลาด  ---------------//
			$ra = insert_to_temp($id_order, $id_pa, $qty, $id_wh, $id_zone, 1, $id_emp);
			$rb = update_stock_zone( ( $qty * -1 ), $id_zone, $id_pa);
			$rc = update_buffer_zone( $qty, $id_pd, $id_pa, $id_order, $id_zone, $id_wh, $id_emp);
			if( $ra === TRUE && $rb === TRUE && $rc === TRUE )
			{
				if( $final_qty == $order_qty)
				{
					$rd = setValidDetail($id_order, $id_pa, 1);
				}
				commitTransection();
				$sc = 'success';
			}
			else
			{
				dbRollback();
				if( $ra === FALSE ){ $sc = 'Cannot Insert item(s) to temp ( insert_to_temp ) กรุณาติดต่อผู้ดูแลระบบ'; }
				if( $rb === FALSE ){ $sc = 'Cannot Update stock in zone ( update_stock_zone ) กรุณาติดต่อผู้ดูแลระบบ'; }
				if( $rc === FALSE ){ $sc = 'Cannot Update buffer zone ( update_buffer_zone ) กรุณาติดต่อผู้ดูแลระบบ'; }
			}
		}			
	}
	echo $sc;
}


//---------------------------- ปิดการจัดเมื่อจัดสินค้าครบแล้ว  --------------------------//
if( isset( $_GET['closeJob'] ) )
{
	$sc 			= 'success';
	$id_order 	= $_POST['id_order'];
	$id_emp		= getCookie('user_id');	
	$c_state		= getCurrentState($id_order);
	if( $c_state == 4 )
	{
		$rs = order_state_change($id_order, 5, $id_emp);
		if( $rs )
		{
			$rb = endPrepare($id_order);
			$rc = setValidAllDetail($id_order, 1);
		}
		else
		{
			$sc = 'fail';	
		}
	}
	echo $sc;
}

	
if( isset($_GET['bring_it_back']) && isset($_GET['id_prepare']) )
{
	$qs = dbQuery("UPDATE tbl_prepare SET id_employee = -1 WHERE id_prepare = ".$_GET['id_prepare']);
	if($qs)
	{
		$message = "ดึงรายการกลับเรียบร้อยแล้ว";
		header("location: ../index.php?content=prepare&view_handle&message=".$message);
	}else{
		$message = "ดึงรายการกลับไม่สำเร็จ";
		header("location: ../index.php?content=prepare&view_handle&error=".$message);
	}
}

if( isset( $_GET['checkPrepared'] ) )
{
	$sc 			= 'ok';
	$id_order 	= $_POST['id_order'];
	$id_user		= getCookie('user_id');
	$qs 			= dbQuery("SELECT id_prepare FROM tbl_prepare WHERE id_order = ".$id_order." AND id_employee != ".$id_user." AND id_employee != -1");
	if( dbNumRows($qs) > 0 )
	{
		$sc = 'notok';
	}
	echo $sc;
}

if( isset( $_GET['toggleZone'] ) )
{
	createCookie('showZone', $_GET['show'], time()+3600*24*365);	
}

if( isset( $_GET['getZone'] ) )
{
	$sc = 'fail';
	$barcode 	= $_POST['barcode'];
	$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$barcode."'");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	echo $sc;
}
?>