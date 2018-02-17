<?php 
function new_event_order_reference($date=""){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'PREFIX_ORDER_EVENT'"));
		if($date =="")
		{ 
			$date = date("Y-m-d"); 
		}
		$sumtdate 	= date("y", strtotime($date));
		$m 			= date("m", strtotime($date));
		$Qtotal		= dbQuery("SELECT  MAX(reference) AS max FROM tbl_order_event WHERE reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"); 
		$rs			= dbFetchArray($Qtotal);
		$num 			= "00001";
		$str 			= $rs['max'];
		$s 			= 7; // start from "0" (nth) char
		$l 				= 7; // get "3" chars
		$str2 			= substr_unicode($str, $s ,5)+1;
		$str1 			= substr_unicode($str, 0 ,$l);
		if(	$str1	==	 $prefix ."-". $sumtdate . $m)
		{  
			$reference = $prefix."-".$sumtdate.$m.sprintf("%05d",$str2);
		}
		else
		{
			$reference = $prefix."-".$sumtdate.$m.$num;
		}
		
		return $reference;
}

function available_qty($id_product, $id_zone)
{
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = ".$id_product." AND id_zone = ".$id_zone);
	$qr = dbQuery("SELECT SUM(product_qty) AS qty FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE id_product = ".$id_product." AND role = 8 AND valid_detail = 0");
	if(dbNumRows($qs) > 0 ){ list($stock) = dbFetchArray($qs); }else{ $stock = 0; }
	if(dbNumRows($qr) > 0 ){ 	list($order) = dbFetchArray($qr); }else{ $order = 0; }
	$rs = $stock - $order;
	return $rs;
}

function event_name($id_order)
{
	$name = "";
	$qs = dbQuery("SELECT event_name FROM tbl_order_event WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list($name) = dbFetchArray($qs);	
	}
	return $name;
}

function get_id_event_sale($id_employee)
{
	$id = 0;
	$qs = dbQuery("SELECT id_event_sale FROM tbl_event_sale WHERE id_employee = ".$id_employee);
	if(dbNumRows($qs) > 0 )
	{
		list($id) = dbFetchArray($qs);
	}
	return $id;
}

function event_sale_name($id_event_sale)
{
	$name = "";
	$qs = dbQuery("SELECT first_name, last_name FROM tbl_event_sale JOIN tbl_employee ON tbl_event_sale.id_employee = tbl_employee.id_employee WHERE id_event_sale = ".$id_event_sale);
	if(dbNumRows($qs) == 1 )
	{
		list($first_name, $last_name) = dbFetchArray($qs);
		$name = $first_name." ".$last_name;
	}
	return $name;
}

function state_name($id)
{
	list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = ".$id));
	return $status;
}
function get_event_zone($id_event_sale)
{
	$zone = "";
	$qs = dbQuery("SELECT id_zone FROM tbl_event_sale WHERE id_event_sale = ".$id_event_sale);
	if(dbNumRows($qs) == 1 )
	{
		list($zone) = dbFetchArray($qs);	
	}
	return $zone;
}
?>