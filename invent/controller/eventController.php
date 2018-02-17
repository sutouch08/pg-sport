<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/event_helper.php";
 
 if( isset( $_GET['save_order'] ) && isset( $_POST['id_order'] ) )
 {
	$ev = new event();
	$rs = $ev->save_order($_POST['id_order']);
	if($rs)
	{ 
		echo "success";
	}
	else
	{
		echo "fail";
	}
 }
 
 if( isset( $_GET['delete_item'] ) && isset( $_POST['id_order_detail'] ) )
 {
	$ev = new event();
	$rs = $ev->delete_item($_POST['id_order_detail']);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
 }
 
 if( isset( $_GET['add_to_order']) && isset( $_POST['id_order'] ) )
 {
	$id_order 	= $_POST['id_order'];
	$id_zone		= $_GET['id_zone'];
	$items		= $_POST['qty'];
	$ev 			= new event();
	$is				= 0;
	$suc			= 0;
	foreach($items as $id => $qty)
	{	
		$is++;
		$rd = $ev->isExists($id_order, $id);
		if($rd)
		{
			$rs = $ev->update_detail($rd, $id, $qty);
		}
		else
		{
			$rs = $ev->insert_detail($id_order, $id, $qty);	
		}
		if($rs){ $suc++; }
	}
	$data = array();
	$qs = $ev->get_detail_data($id_order);
	$no = 1;
	$total_qty = 0; $total_amount = 0;
	while($rs = dbFetchArray($qs) )
	{
		$arr = array(
						"id"						=> $rs['id_order_detail'],
						"no" 					=> $no, 
						"barcode" 			=> $rs['barcode'], 
						"reference" 			=> $rs['product_reference'], 
						"product_name" 	=> $rs['product_name'], 
						"price" 				=> number_format($rs['product_price'],2),
						"qty"					=> number_format($rs['product_qty']),
						"amount"				=> number_format($rs['total_amount'], 2)
						);
		array_push($data, $arr);
		$total_qty += $rs['product_qty']; $total_amount += $rs['total_amount'];
		$no++;
	}
	$arr = array("qty" => number_format($total_qty), "amount" => number_format($total_amount,2));
	array_push($data, $arr);
	echo json_encode($data);	
	
 }
 
 
if( isset( $_GET['getData'] ) && isset( $_POST['id_product'] ) ) 
{
		$ev = new event();
		$id_product = $_POST['id_product'];
		$id_zone = $_POST['id_zone'];
		$product = new product();
		$product->product_detail($id_product);
		$co 	= dbNumRows(dbQuery("SELECT id_color FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color != 0 GROUP BY id_color"));
		$si 	= dbNumRows(dbQuery("SELECT id_size FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_size != 0 GROUP BY id_size"));
		$at 	= dbNumRows(dbQuery("SELECT id_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_attribute != 0 GROUP BY id_attribute"));
		if($co != 0){ $colums = $co; }else if($si != 0 ){ $colums = $si; }else if($at != 0 ){ $colums = $at;}
		$table_w = (70*($colums+1)+100); 
		if($table_w < 500){ $table_w = 500; }
		$dataset = $ev->event_attribute_grid($id_product, $id_zone);
		$dataset .= "|".$table_w;
		$dataset .= "|".$_POST['product_code'];
		$dataset .= "|".$id_product;
		$dataset .= "|".$product->product_cost;
	echo $dataset;
}

if( isset($_GET['update_order']) && isset( $_POST['id_order_event'] ) && isset( $_POST['id_order'] ) )
{
	$data = array("id_order"=>$_POST['id_order'], "event_name" => $_POST['event_name'], "remark" => $_POST['remark']);
	$ev = new event();
	$rs = $ev->update_order($_POST['id_order_event'], $data);
	if($rs)
	{
		echo "ok";
	}
	else
	{
		echo "fail";
	}	
}

if( isset($_GET['new_order']) && isset($_POST['id_employee']) )
{
	$reference = get_max_role_reference("PREFIX_ORDER_EVENT", 8);
	$comment = $_POST['remark'];
	$valid = 0;
	$data = array(
				"reference" 					=> $reference,
				"id_customer" 				=> 0,
				"id_employee" 				=> $_POST['id_employee'],
				"id_cart" 						=> 0,
				"id_address_delivery" 	=> 0,
				"current_state" 				=> 1,
				"payment" 					=> "อีเว้นท์",
				"comment" 					=> $_POST['remark'],
				"valid" 						=> 0,
				"role" 							=> 8,
				"date_add" 					=> date("Y-m-d H:i:s"),
				"order_status" 				=> 0
				);
				
	$ev = new event();
	$rs = $ev->add_order($data);		
	if($rs)
	{
		$id_event_sale = get_id_event_sale($_POST['id_employee']);
		$rd = array(
					"id_order" 		=> $rs, 
					"id_event_sale" => $id_event_sale, 
					"id_employee" 	=> $_POST['id_employee'], 
					"id_zone" 		=> get_event_zone($id_event_sale), 
					"event_name" 	=> $_POST['event_name'], 
					"date_add" 		=> date("Y-m-d H:i:s"), 
					"status" 			=> 0
					);
		$ro = $ev->add_order_event($rd);
		if($ro)
		{
			echo $ro;
		}
		else
		{
			echo "error";
		}
	}
	else
	{
		echo "error";
	}	
}

if( isset( $_GET['check_event_user']) && isset( $_POST['id_employee'] ) )
{
	$qs = dbQuery("SELECT id_event_sale FROM tbl_event_sale WHERE id_employee = ".$_POST['id_employee']);
	if( dbNumRows($qs) == 0 )
	{
		echo "not_found";
	}else{
		$qr = dbQuery("SELECT id_order_event FROM tbl_order_event WHERE id_employee = ".$_POST['id_employee']." AND status != 2");
		if( dbNumRows($qr) == 0 )
		{
			echo "ok";
		}else{
			echo "not_clear";
		}
	}
}

if(isset($_GET['clear_filter']))
{
	setcookie("order_from_date","",time()-3600,"/");
	setcookie("order_to_date","",time()-3600,"/");
	setcookie("order_search-text", $text, time() - 3600, "/");
	setcookie("order_filter",$filter, time() - 3600,"/");
	header("location: ../index.php?content=order");
	}
	

	
?>
