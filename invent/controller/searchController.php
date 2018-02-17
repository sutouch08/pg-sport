<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

if( isset($_GET['find_product']) && isset($_POST['search_text']) )
{
	$txt 	= trim($_POST['search_text']);
	$rs	= array();
	$qs 	= "SELECT id_product_attribute, reference, product_name FROM ";
	$qs 	.= "tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	$qs	.= "WHERE reference LIKE '%".$txt."%' OR product_name LIKE '%".$txt."%' OR barcode = '".$txt."'";
	$sql 	= dbQuery($qs);
	$row 	= dbNumRows($sql);;
	if($row > 0 )
	{
		$total_rw = 0;
		while($data = dbFetchArray($sql)) :
			$sqr = dbQuery("SELECT qty, zone_name FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE tbl_stock.id_product_attribute = ".$data['id_product_attribute']);
			$rw = dbNumRows($sqr);
			$total_qty = 0;
			$in_zone = "";
			if($rw>0)
			{	
				while($r = dbFetchArray($sqr))
				{
					$total_qty += $r['qty'];	
					$in_zone .= $r['zone_name']." : ".$r['qty']." <br/>";
				}
			}
			list($qty_moving) = dbFetchArray(dbQuery("SELECT SUM(qty_move) AS qty_move FROM tbl_move WHERE id_product_attribute = '".$data['id_product_attribute']."'"));
			if( $qty_moving != 0){ $in_zone .= "moving zone : ".$qty_moving."<br/>";  $total_qty += $qty_moving; }
			list($buffer) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_buffer WHERE id_product_attribute = ".$data['id_product_attribute']));
			if( $buffer != 0){ $in_zone .= "BUFFER : ".$buffer."<br/>"; $total_qty += $buffer;; }
			list($cancle) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_cancle WHERE id_product_attribute = ".$data['id_product_attribute']));
			if($cancle != 0){ $in_zone .= "CANCLE : ".$cancle."<br/>";  $total_qty += $cancle; }
			$product = new product();
			if($total_qty > 0 )
			{
				$total_rw ++;
				$arr = array(
							"id"				=> $data['id_product_attribute'],
							"img"			=> "<img src='".$product->get_product_attribute_image($data['id_product_attribute'],1)."' />",
							"product"		=> $data['reference']." : ".$data['product_name'],
							"total_qty"	=> number_format($total_qty),
							"in_zone"		=> $in_zone,
							);
				array_push($rs, $arr);
			}
		endwhile;
	}
	else
	{
		$arr = array("nodata" =>"nocontent");
		array_push($rs, $arr);	
	}
	echo json_encode($rs);
}

if( isset($_GET['checkBarcode']) && isset($_GET['barcode']) )
{
	$txt 	= trim($_GET['barcode']);
	$ds	= array();
	$qs 	= "SELECT id_product_attribute, reference, product_code, product_name, barcode FROM ";
	$qs 	.= "tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	$qs	.= "WHERE barcode LIKE '%".$txt."%'";
	$sql 	= dbQuery($qs);
	if(dbNumRows($sql) > 0)
	{
		$product = new product();
		while( $rs = dbFetchObject($sql) )
		{
			$arr = array(
								"img"			=> "<img src='".$product->get_product_attribute_image($rs->id_product_attribute,1)."' />",
								"barcode"	=> $rs->barcode,
								"product"		=> $rs->reference." : ".$rs->product_name,
								"style"			=> $rs->product_code
								);
			array_push($ds, $arr);
		}
	}
	else
	{
		$arr = array("nodata" =>"nocontent");
		array_push($ds, $arr);
	}
	echo json_encode($ds);
}

?>

			