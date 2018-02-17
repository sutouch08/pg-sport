<?php
//------ ใช้กับ รายงานสินค้าคงเหลือ
function getTransectionQty($id_pa, $wh, $from, $to)
{
	$sc = array("move_in" => 0, "move_out" => 0);
	$qr = "SELECT SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement ";
	$qr .= "WHERE id_product_attribute = ".$id_pa." AND id_warehouse IN(".$wh.") AND date_upd >= '".$from."' AND date_upd <= '".$to."'";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchObject($qs);	
		$sc['move_in']	= is_null($rs->move_in) ? 0 : $rs->move_in;
		$sc['move_out'] = is_null($rs->move_out) ? 0 : $rs->move_out;
	}
	return $sc;
}
//------ ใช้กับ รายงานสินค้าคงเหลือ
function warehouseIn($whs, $all = FALSE)
{
	$sc = FALSE;
	$ds = '';
	if( $all === TRUE)
	{
		$qs = dbQuery("SELECT id_warehouse FROM tbl_warehouse");
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchObject($qs) )
			{
				$ds .= $rs->id_warehouse . ',';
			}
			$sc = trim($ds, ',');
		}
	}
	else if( is_array($whs) === TRUE)
	{
		$ds = '';
		foreach( $whs as $id )
		{
			$ds .= $id.','; 
		}
		$sc = trim($ds, ',');
	}
	else
	{
		$sc = $whs;
	}
	
	return $sc;
}

//------ ใช้ใน export stock balance	
function warehouseNameList($whList)
{
	$sc = '';
	$qs = dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse IN(".$whList.")");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= $rs->warehouse_name . ', ';	
		}
	}
	return trim($sc, ', ');
}
	
function removeVAT($amount, $vat=7)
{
	$re_vat	= ($vat + 100) / 100;
	return $amount/$re_vat;
}

function addVAT($amount, $vat = 7)
{
	$re_vat = $vat * 0.01;
	$sc = ($amount * $re_vat) + $amount;
	return $sc;
}

function getProductAttribute($id_pa)
{
	$sc = array('id_color' => 0, 'id_size' => 0, 'id_attribute' => 0);
	$qs = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);
	}
	return $sc;
}


function getDefaultCategoryName($id_pd)
{
	$sc = '';
	$qs = dbQuery("SELECT category_name FROM tbl_product JOIN tbl_category ON tbl_product.default_category_id = tbl_category.id_category WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}


function customerDefaultGroupName($id_customer)
{
	$sc = '';
	$qs = dbQuery("SELECT group_name FROM tbl_customer JOIN tbl_group ON tbl_customer.id_default_group = tbl_group.id_group WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) == 1 )	
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getPaymentText($id_order)
{
	$sc = 'เครดิต';
	$qs = dbQuery("SELECT payment, role FROM tbl_order WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $payment, $role ) = dbFetchArray($qs);
		if( $payment == '' )
		{
			switch($role)
			{
				case '1' :
					$payment = 'เครดิต';
					break;	
				case '4' :
					$payment = 'สปอนเซอร์สโมสร';
					break;
				case '5' : 
					$payment = 'ฝากขาย';
					break;
				case '7' :
					$payment = 'อภินันทนาการ';
					break;
				default :
					$payment = 'เครดิต';
			}
		}
		$sc = $payment;
	}
	return $sc;
}




function id_product_attribute_in_product($id_product)
{
	$in = "";
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product);
	$rows = dbNumRows($qs);
	if( $rows > 0 )
	{
		$i = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$in	 .= $rs['id_product_attribute'];
			if($i < $rows){ $in .= ", "; }
			$i++;
		}
	}
	else
	{
		$in .= 0;
	}
	return $in;
}

function move_in($id_product_attribute, $from, $to, $id_warehouse = 0)
{
	$qty = 0;
	$from 	= fromDate($from);
	$to 		= toDate($to);
	if( $id_warehouse == 0 )
	{
		$qs = dbQuery("SELECT SUM(move_in) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	}
	else
	{
		$qs = dbQuery("SELECT SUM(move_in) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse = ".$id_warehouse." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	}
	list($move_in) = dbFetchArray($qs);
	if( !is_null($move_in) ){ $qty = $move_in; }
	return $qty;
}

function move_out($id_product_attribute, $from, $to, $id_warehouse = 0)
{
	$qty = 0;
	$from 	= fromDate($from);
	$to 		= toDate($to);
	if( $id_warehouse == 0 )
	{
		$qs = dbQuery("SELECT SUM(move_out) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	}
	else
	{
		$qs = dbQuery("SELECT SUM(move_out) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse = ".$id_warehouse." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	}
	list($move) = dbFetchArray($qs);
	if( !is_null($move) ){ $qty = $move; }
	return $qty;
}

function stock_qty_by_warehouse($id, $wh = 0 )
{
	$product = new product();
	if( $wh == 0 )
	{
		$qty 	= $product->all_available_qty($id);	
	}
	else
	{
		$stock_qty 	= $product->stock_qty_by_warehouse($id, $wh);
		$move_qty 	= $product->move_qty_by_warehouse($id, $wh);
		$cancle_qty	= $product->cancle_qty_by_warehouse($id, $wh);
		$buffer_qty 	= $product->buffer_qty_by_warehouse($id, $wh);
		$qty 			= $stock_qty + $move_qty + $cancle_qty + $buffer_qty;
	}
	return $qty;
}

function get_id_product_attribute_by_reference($id)
{
	$rs = "";
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE reference ='".$reference."'");
	if(dbNumRows($qs) == 1 )
	{
		list($rs) = dbFetchArray($qs);
	}
	return $rs;
}
/******************** Warehouse Select *********************/

function product_from($code)
{
	$reference = "";
	$qs = dbQuery("SELECT MIN(reference) FROM tbl_product_attribute	WHERE reference LIKE '%".$code."%'");
	if( dbNumRows($qs) == 1 )
	{
		list($reference) = dbFetchArray($qs);	
	}
	return $reference;
}

function product_to($code)
{
	$reference = "";
	$qs = dbQuery("SELECT MAX(reference) FROM tbl_product_attribute WHERE reference LIKE '%".$code."%'");
	if( dbNumRows($qs) == 1 )
	{
		list($reference) = dbFetchArray($qs);	
	}
	return $reference;
}

function product_in_code($fromCode, $toCode)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code >= '".$fromCode."' AND product_code 	<= '".$toCode."'");
	$row = dbNumRows($qs);	
	if( $row > 0 )
	{
		$n = 1;
		$in = '';
		while( $rs = dbFetchArray($qs) )
		{
			$in .= $rs['id_product'];
			if( $n < $row ){ $in .= ', '; }
			$n++;	
		}
		$sc = $in;
	}
	return $sc;
}

function select_warehouse()
{
	$option = "<option value='0'>ทั้งหมด</option>";
	$qs 		= dbQuery("SELECT * FROM tbl_warehouse");
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) ) :
			$option 	.= "<option value='".$rs['id_warehouse']."'>".$rs['warehouse_name']."</option>";
		endwhile;	
	}
	return $option;
}

function warehouse_name($id)
{
	$wh = "";
	$qs = dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		list($wh) = dbFetchArray($qs);
	}
	return $wh;
}

function product_stock_qty($id_product, $wh)
{
	$qty	= 0;
	if($wh == 0 )
	{ 
		$wh 	= "id_warehouse != 0"; 
	}else{ 
		$wh 	= "id_warehouse = ".$_GET['wh']; 
	}
	$qr	= "SELECT SUM(qty) AS qty FROM tbl_stock ";
	$qr 	.= "JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr 	.= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	$qr 	.= "JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone ";
	$qr	.= "WHERE tbl_product.id_product = ".$id_product." AND ".$wh;
	$qr 	= dbQuery($qr);
	if(dbNumRows($qr) == 1)
	{
		$rs = dbFetchArray($qr);
		if($rs['qty'] != NULL )
		{
			$qty = $rs['qty'];
		}
	}
	return $qty;
}

function get_product_cost_by_po($id_po, $id_pa)
{
	$cost = 0;
	$qs	= dbQuery("SELECT price FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_pa);
	if(dbNumRows($qs))
	{
		list($cost) = dbFetchArray($qs);		
	}
	return $cost;
}


function get_id_product_by_product_code($code)
{
	$id_product = 0;
	$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code = '".$code."'");	
	if( dbNumRows($qs) == 1 )
	{
		list($id_product) = dbFetchArray($qs);
	}
	return $id_product;
}

function sum_sold_qty_by_id_product_attribute($id, $from, $to, $option)
{
	/// $option เป็น 0 หรือ 1      0 คือ ไม่นำยอดลดหนี้มาคำนวน  1 คือ นำยอดลดหนี้มาหักออกด้วย
	$qty = 0;
	$qs = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_product_attribute = ".$id." AND id_role IN(1,5) AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	list($qtyx) = dbFetchArray($qs);
	if( !is_null($qtyx) ){ $qty = $qtyx; }
	if( $option )
	{
		$qr = dbQuery("SELECT SUM(qty) AS qty FROM tbl_return_order_detail JOIN tbl_return_order ON tbl_return_order_detail.id_return_order = tbl_return_order.id_return_order WHERE id_product_attribute = ".$id." AND (tbl_return_order.date_upd BETWEEN '".$from."' AND '".$to."')");
		list($re) = dbFetchArray($qr);
		if( !is_null($re) )
		{
			$qty -= $re;
		}
	}
	return $qty;
}

function getProductPrice($id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT price FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function getProductCost($id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT cost FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function getProductGroupName($id_pd)
{
	$sc = '';
	$qs = dbQuery("SELECT name FROM tbl_product_group JOIN tbl_product ON tbl_product_group.id = tbl_product.id_product_group WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function setToken($token)
{
	setcookie("file_download_token", $token, time() +3600,"/");	
}

?>