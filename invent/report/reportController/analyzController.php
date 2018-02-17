<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";

if( isset($_GET['attribute_analyz']) && isset($_POST['product']) )
{
	$p_rank = $_POST['product'];
	$d_rank = $_POST['date'];
	$p_selected = $_POST['id_product'];
	$p_checked = isset($_POST['p_checked']) ? $_POST['p_checked'] : "";
	
	switch( $p_rank ) {
		case 0 :
			$p_q = "id_product != 0";
		break;
		case 1 :
			$in_cuase = "";
			$i = 1;
			$c = count($p_checked);
			foreach($p_checked as $id =>$value) {
				if($value != "") {
					$in_cuase .= $id;
					if($i<$c) { $in_cuase .= ", "; }
				}
				$i++;
			}
			$p_q = "id_product IN(".$in_cuase.")";
		break;
		case 2 :
			$p_q = "id_product = ".$p_selected;
		break;
		}
	switch( $d_rank ) {
		case 0 :
			$from = date("Y-01-01 00:00:00");
			$to 	= date("Y-12-31 23:59:59");
			break;
		case 1 :
			$from = date("Y-m-d 00:00:00", strtotime($_POST['from_date']));
			$to 	= date("Y-m-d 23:59:59", strtotime($_POST['to_date']));
			break;
	}
	$result = array();
	$qr = dbQuery("SELECT id_product_attribute, id_product, reference, id_color, id_size, id_attribute FROM tbl_product_attribute WHERE ".$p_q." ORDER BY id_product ASC");
	$n = 1;
	while($rm = dbFetchArray($qr) )
	{
		$id_product_attribute = $rm['id_product_attribute'];
		$id_product = $rm['id_product'];
		$product_code = get_product_code($id_product);
		$id_color 		= $rm['id_color'];
		$id_size 			= $rm['id_size'];
		$id_attribute 	= $rm['id_attribute'];
		$reference		= $rm['reference'];
		$qs 	= dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
		$rs 	= dbFetchArray($qs); 
		$color 	= get_color_code($id_color);
		$size 	= get_size_name($id_size);
		$attr 		= get_attribute_name($id_attribute);
		$qty 		= $rs['qty'] > 0 ? $rs['qty'] : 0 ;
		$arr = array("n"=>$n, "product_code"=>$product_code, "reference"=>$reference,"color"=>$color, "size"=>$size, "attribute"=>$attr, "qty"=>$qty);
		array_push($result, $arr);		
		$n++;
		}
		echo json_encode($result);
	
}

if( isset($_GET['export_attribute_analyz']) && isset($_POST['product']) )
{
	$p_rank = $_POST['product'];
	$d_rank = $_POST['date'];
	$p_selected = $_POST['id_product'];
	$p_checked = isset($_POST['p_checked']) ? $_POST['p_checked'] : "";
	
	switch( $p_rank ) {
		case 0 :
			$p_q = "id_product != 0";
		break;
		case 1 :
			$in_cuase = "";
			$i = 1;
			$c = count($p_checked);
			foreach($p_checked as $id =>$value) {
				if($value != "") {
					$in_cuase .= $id;
					if($i<$c) { $in_cuase .= ", "; }
				}
				$i++;
			}
			$p_q = "id_product IN(".$in_cuase.")";
		break;
		case 2 :
			$p_q = "id_product = ".$p_selected;
		break;
		}
	switch( $d_rank ) {
		case 0 :
			$from = date("Y-01-01 00:00:00");
			$to 	= date("Y-12-31 23:59:59");
			break;
		case 1 :
			$from = date("Y-m-d 00:00:00", strtotime($_POST['from_date']));
			$to 	= date("Y-m-d 23:59:59", strtotime($_POST['to_date']));
			break;
	}
	$result = array();
	$title = array("รายงานสรุปยอดจำนวนขาย แสดงคุณลักษณะสินค้า   วันที่  ".thaiDate($from,"/")."  ถึง   ".thaiDate($to, "/"));
	$header = array("ลำดับ","รุ่นสินค้า","รายการสินค้า","สี","ไซด์","อื่นๆ","จำนวนขาย");
	array_push($result, $title);
	array_push($result, $header);
	$qr = dbQuery("SELECT id_product_attribute, id_product, reference, id_color, id_size, id_attribute FROM tbl_product_attribute WHERE ".$p_q." ORDER BY id_product ASC");
	$n = 1;
	while($rm = dbFetchArray($qr) )
	{
		$id_product_attribute = $rm['id_product_attribute'];
		$id_product = $rm['id_product'];
		$product_code = get_product_code($id_product);
		$id_color 		= $rm['id_color'];
		$id_size 			= $rm['id_size'];
		$id_attribute 	= $rm['id_attribute'];
		$reference		= $rm['reference'];
		$qs 	= dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
		$rs 	= dbFetchArray($qs); 
		$color 	= get_color_code($id_color);
		$size 	= get_size_name($id_size);
		$attr 		= get_attribute_name($id_attribute);
		$qty 		= $rs['qty'] > 0 ? $rs['qty'] : 0 ;
		$arr = array("n"=>$n, "product_code"=>$product_code, "reference"=>$reference,"color"=>$color, "size"=>$size, "attribute"=>$attr, "qty"=>$qty);
		array_push($result, $arr);		
		$n++;
		}
	$xml = new Excel_XML();
	$xml->setEncoding("UTF-8");
	$xml->setWorksheetTitle("Sale_attribute_analyz");
	$xml->addArray($result);
	$xml->generateXML("Sale_attribute_analyz");	
	
}

?>