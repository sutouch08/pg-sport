<?php

/*********************************  consign helper *****************/
function getConsignReference($id)
{
	$reference = '';
	$qs = dbQuery("SELECT reference FROM tbl_order_consign WHERE id_order_consign = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $reference ) = dbFetchArray($qs);	
	}
	return $reference;
}

function getConsignCustomer($id)
{
	$id_customer 	= 0;
	$qs 				= dbQuery("SELECT id_customer FROM tbl_order_consign WHERE id_order_consign = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $id_customer ) = dbFetchArray($qs);	
	}
	return $id_customer;
}

function getConsignZone($id)
{
	$id_zone 	= 0;
	$qs 			= dbQuery("SELECT id_zone FROM tbl_order_consign WHERE id_order_consign = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $id_zone ) = dbFetchArray($qs);	
	}
	return $id_zone;
}

function getIdSaleByCustomer($id_customer)
{
	$id_sale = 0;
	$qs 		= dbQuery("SELECT id_sale FROM tbl_customer WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) == 1 )
	{
		list($id_sale) = dbFetchArray($qs);		
	}
	return $id_sale;
}

function consign_check_error_type($id)
{
	$err = "";
	$qs = dbQuery("SELECT name FROM tbl_consign_check_error_type WHERE id_type = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		list($err) = dbFetchArray($qs);
	}
	return $err;
}

function customer_incause($value)
{
	$incause = "";
	$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE customer_code LIKE '%".$value."%' OR first_name LIKE '%".$value."%' OR last_name LIKE '%".$value."%' OR company LIKE '%".$value."%'");
	$row = dbNumRows($qs);
	$i 		= 0 ;
	if($row > 0 )
	{
		while($i<$row)
		{
			$i++;
			list($id_customer) = dbFetchArray($qs);
			$incause .= $id_customer;
			if( $i < $row ){ $incause .= ", "; }
		}
	}
	else
	{
		$incause .= "0";
	}
	return $incause;
}

function get_consign_box_detail($id)
{
	$qs = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_consign_box_detail WHERE id_consign_box = ".$id." GROUP BY id_product_attribute");
	return $qs;
}
function get_consign_check_reference($id)
{
	$reference = "";
	$qs = dbQuery("SELECT reference FROM tbl_consign_check WHERE id_consign = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		list($reference) = dbFetchArray($qs);		
	}	
	return $reference;
}

function insert_consign_check($id_consign_check, $barcode, $id_product_attribute, $qty, $id_box)
{
	$qs = dbQuery("SELECT id_consign_check_detail FROM tbl_consign_check_detail WHERE id_consign_check = ".$id_consign_check." AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) == 1 )
	{
		list($id) = dbFetchArray($qs);
		$qr = dbQuery("UPDATE tbl_consign_check_detail SET qty_check = qty_check + ".$qty." WHERE id_consign_check_detail = ".$id);
	}
	else
	{
		$error_type = 1; /// 1 = ไม่มีสินค้านี้ในโซน  2 = ไม่มีสินค้าในฐานข้อมูล
		insert_consign_check_error($id_consign_check, $error_type, $barcode, $id_product_attribute, $qty, $id_box);
		$qr = 0;
	}
	if($qr)
	{
		$rs = dbQuery("INSERT INTO tbl_consign_box_detail (id_consign_box, id_consign_check, id_product_attribute, qty) VALUES (".$id_box.", ".$id_consign_check.", ".$id_product_attribute.", ".$qty.")");
	}
	return $qr;
}

function insert_consign_check_error($id_consign_check, $error_type, $barcode, $id_product_attribute, $qty, $id_box)
{
	$qr = dbQuery("INSERT INTO tbl_consign_check_error (id_consign_check, error_type, barcode, id_product_attribute, qty, id_box ) VALUES (".$id_consign_check.", ".$error_type.", '".$barcode."', ".$id_product_attribute.", ".$qty.", ".$id_box.")");	
	return $qr;
}

function get_consign_box($barcode, $id)
{
	$id_box = 0;
	$qs = dbQuery("SELECT id_box FROM tbl_consign_box WHERE barcode = '".$barcode."' AND id_consign_check = ".$id." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		list($id_box) = dbFetchArray($qs);
	}
	return $id_box;
}

function max_box_no($id)
{
	$no = 0;
	$qs = dbQuery("SELECT MAX(box_no) AS box_no FROM tbl_consign_box WHERE id_consign_check = ".$id);
	list($no) = dbFetchArray($qs);
	if($no == NULL)
	{
		$no = 0;
	}
	return $no;
}

function box_no($id_box)
{
	$box_no = 0;
	$qs = dbQuery("SELECT box_no FROM tbl_consign_box WHERE id_box = ".$id_box);
	if(dbNumRows($qs) == 1 )
	{
		list($box_no) = dbFetchArray($qs);
	}
	return $box_no;
}

function add_consign_box($barcode, $id)
{
	$no = max_box_no($id);
	$no += 1;
	$qs = dbQuery("INSERT INTO tbl_consign_box (barcode, id_consign_check, box_no) VALUES ('".$barcode."', ".$id.", ".$no.")");
	if($qs)
	{
		$id_box = get_consign_box($barcode, $id);
	}
	else
	{
		$id_box = 0;
	}
	return $id_box;	
}

function showDiscount($p_dis, $a_dis)
{
	$dis 	= $p_dis > 0 ? number_format($p_dis, 2) . ' %' : number_format($a_dis, 2);
	return $dis;
}

function getConsignSumDiscount($qty, $price, $p_dis, $a_dis)
{
	$dis 		= $p_dis > 0 ? $price * ( $p_dis * 0.01 ) : $a_dis;
	return $qty * $dis;
	
}

function getConsignSumAmount($qty, $price, $p_dis, $a_dis)
{
	$dis 		= getConsignSumDiscount($qty, $price, $p_dis, $a_dis);
	$amount 	= ($qty * $price) - $dis;
	return $amount;
}

function newConsignReference($config_name, $date=""){
		$prefix = getConfig($config_name);
		if($date ==""){ $date = date("Y-m-d"); }
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		$sql="SELECT  MAX(reference) AS max FROM tbl_order_consign WHERE reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"; 
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){  
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}
		
		return $reference_no;
}

?>