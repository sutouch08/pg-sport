<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";
require "../../function/report_helper.php";

//---------------------------------------------  Report  ----------------------------//
if( isset( $_GET['fifo_report'] ) && isset( $_GET['export'] ) )
{
	include '../../function/fifo_helper.php';
	include 'export/exportStockCard.php';
}


//-----------  รายงานสินค้าคงเหลือ
if( isset( $_GET['reportStockBalance'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportStockBalance.php';
}


//-----------  รายงานสินค้าคงเหลือปัจจุบัน
if( isset( $_GET['reportStockCurrent'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportStockCurrent.php';	
}

if( isset( $_GET['stock_by_zone'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportStockZone.php';
}


//--------------------------------------------- Export  ---------------------------//
//---------- Export รายงานสินค้าคงเหลือ

if( isset( $_GET['reportStockBalance'] ) && isset( $_GET['export'] ) )
{
	
	include 'export/exportStockBalance.php';
}

if( isset( $_GET['stock_by_zone'] ) && isset( $_GET['export'] ) )
{
	include 'export/exportStockZone.php';
}

if( isset( $_GET['stock_product_deep_analyz'] ) && isset( $_GET['export'] ) )
{
	include '../../function/group_helper.php';
	include 'export/exportStockBalanceAnalyz.php';	
}

if( isset( $_GET['received_product_deep_analyz'] ) && isset( $_GET['export'] ) )
{
	include '../../function/group_helper.php';
	include '../../function/po_helper.php';
	include 'export/exportReceivedAnalyz.php';	
}


//------------------------------- Print  -------------------------------//

if( isset( $_GET['reportStockBalance'] ) && isset( $_GET['print'] ) )
{
	include 'print/printStockBalance.php';	
}

if( isset( $_GET['stock_by_zone'] ) && isset( $_GET['export_csv'] ) )
{
	$p_rank 		= $_GET['product_rank'];
	$zone_rank	= $_GET['zone_rank'];
	$id_wh		= $_GET['wh'];
	$qs			= "SELECT tbl_stock.id_zone, tbl_stock.id_product_attribute, qty ";
	$qs 			.= "FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qs 			.= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	if( $p_rank == 1 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 1 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE id_warehouse = ".$id_wh." ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 2 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE 	product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."' ORDER BY tbl_product_attribute.id_product ASC";		
	}
	else if( $p_rank == 2 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE id_warehouse = ".$id_wh." AND	(product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."') ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 1 && $zone_rank == 2 )
	{
		$qs .= "WHERE zone_name = '".$_GET['zone_name']."' ORDER BY tbl_product_attribute.id_product ASC";		
	}
	else if( $p_rank == 2 &&$zone_rank == 2 )
	{
		$qs .= "WHERE 	zone_name = '".$_GET['zone_name']."' AND (product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."') ORDER BY tbl_product_attribute.id_product ASC";		
	}
	
	$qs = dbQuery($qs);
	if(dbNumRows($qs) > 0 )
	{
		$file_name = "import_stock_zone.csv";
		header('Content-Type: text/csv');
    	header('Content-Disposition: attachment; filename='. $file_name);
    	header('Pragma: no-cache');
   		header("Expires: 0");
		$csv = fopen("php://output", "w");
		while($rs = dbFetchArray($qs) ) :
			$arr = array( $rs['id_zone'], $rs['id_product_attribute'], $rs['qty'] );
			fputcsv($csv, $arr);
		endwhile;
		fclose($csv);
	}
	else
	{
		echo "nodata";
	}
	setToken($_GET['token']);
	exit();
}


if( isset( $_GET['stock_summary'] ) && isset( $_GET['report'] ) )
{
	$rank 	= $_POST['rank'];
	$wh		= $_POST['wh'];
	if($wh == 0 ){ $wh = "id_warehouse != 0"; }else{ $wh = "id_warehouse = ".$_POST['wh']; }
	if($rank == 1 )
	{
		$qs = "SELECT product_code,cost, price, SUM(qty)AS qty FROM tbl_stock ";
		$qs .= "JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qs .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qs .= "JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone ";
		$qs .= "WHERE ".$wh."  GROUP BY tbl_product.id_product ORDER BY product_code ASC";
	}else if($rank == 2 ){
		$qs =	 "SELECT product_code ,cost, price, SUM(qty) AS qty FROM tbl_stock ";
		$qs .= "JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qs .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qs .= "JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone ";
		$qs .= "WHERE ".$wh." AND (product_code BETWEEN '".$_POST['from']."' AND '".$_POST['to']."') GROUP BY tbl_product.id_product ORDER BY product_code ASC";	
	}
	$data = array();
	$qr = dbQuery($qs);
	if(dbNumRows($qr) > 0 ) :
		$n = 1;
		$total_qty = 0;
		$total_cost_amount = 0;
		$total_price_amount = 0;
		while($rs = dbFetchArray($qr) ) :
			$arr = array(
							"no"				=> $n, 
							"code"			=> $rs['product_code'], 
							"qty"				=> number_format($rs['qty']), 
							"cost"				=> number_format($rs['cost'],2), 
							"price"			=> number_format($rs['price'], 2),
							"cost_amount"	=> number_format($rs['qty']*$rs['cost'], 2),
							"price_amount"	=> number_format($rs['qty']*$rs['price'],2)
							);
			array_push($data, $arr);	
			$total_qty += $rs['qty'];
			$total_cost_amount += $rs['qty']*$rs['cost'];
			$total_price_amount += $rs['qty']*$rs['price'];			
			$n++;
		endwhile;
		$arr = array("no"=>"", "code"=>"Grand Total", "qty"=>number_format($total_qty), "cost"=>"", "price"=>"", "cost_amount"=>number_format($total_cost_amount,2), "price_amount"=>number_format($total_price_amount,2));
		array_push($data, $arr);
		echo json_encode($data);
	else :
		echo "fail";
	endif;
	
}

if( isset( $_GET['stock_summary'] ) && isset( $_GET['export'] ) )
{
	$rank 	= $_GET['rank'];
	$wh		= $_GET['wh'];
	if($wh == 0 )
	{ 
		$wh_title = "รวมทุกคลัง";
	}else{ 
		$wh_title = warehouse_name($_GET['wh']);
	}
	if($rank == 1 )
	{
		$p_title 	= "ทุกรายการ";
		$qr 		= dbQuery("SELECT id_product, product_code, product_cost AS cost, product_price AS price FROM tbl_product ORDER BY product_code ASC");
		
	}else if($rank == 2 ){
		$p_title 	= "ตั้งแต่ ".$_GET['from']." ถึง ".$_GET['to']." ";
		$qr 		 = dbQuery("SELECT id_product, product_code, product_cost AS cost, product_price AS price FROM tbl_product WHERE product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."' ORDER BY product_code ASC");	
	}
	$data = array();
	$arr 	= array("==========  รายงานสรุปสินค้าคงเหลือแยกตามรุ่นสินค้า  ==========");
	array_push($data, $arr);
	$arr 	= array("ช่วงสินค้า : ", $p_title, $wh_title);
	array_push($data, $arr);
	
	if(dbNumRows($qr) > 0 ) :
		$arr = array("ลำดับ", "รหัสสินค้า", "ทุน", "ราคา", "คงเหลือ", "มูลค่าทุน", "มูลค่าราคา");
		array_push($data, $arr);
		$n = 1;
		$total_qty = 0;
		$total_cost_amount = 0;
		$total_price_amount = 0;
		while($rs = dbFetchArray($qr) ) :
			$qty = product_stock_qty($rs['id_product'], $_GET['wh']);
			$arr = array($n, $rs['product_code'], $rs['cost'], $rs['price'], $qty, $qty*$rs['cost'], $qty*$rs['price']);
			array_push($data, $arr);	
			$total_qty += $qty;
			$total_cost_amount += $qty*$rs['cost'];
			$total_price_amount += $qty*$rs['price'];			
			$n++;
		endwhile;
		$arr = array("", "Grand Total", "", "", $total_qty, $total_cost_amount, $total_price_amount);
		array_push($data, $arr);
		
	else :
		$arr = array("================= ไม่มีรายการสินค้าคงเหลือ ================");
		array_push($data, $arr);
	endif;
	$sheet_name 	= "Stock_summary";
	$Excel			= new Excel_XML("UTF-8", false, $sheet_name);
	$Excel->addArray($data);
	$Excel->generateXML($sheet_name);
	setToken($_GET['token']);
	
}

if( isset( $_GET['summary_by_category'] ) && isset( $_GET['report'] ) )
{
	$rank 	= $_GET['rank'];
	$wh		= $_GET['wh'];
	if($wh == 0 )
	{ 
		$wh 		= "id_warehouse != 0"; 
	}else{ 
		$wh 		= "id_warehouse = ".$_GET['wh']; 
	}
	if($rank == 2 )
	{
		$in		= "";
		$c 	= count($_POST['p_selected']);
		foreach($_POST['p_selected'] as $id) :
			$c--;
			$in		.= $id;
			if($c > 0){ $in .= ", "; }
		endforeach;
		$qs = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category IN(".$in.")");
	}
	else
	{
		$qs = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category != 0");
	}
	$data = array();
	if(dbNumRows($qs) > 0 ) :
		while($rs = dbFetchArray($qs) ) :
		$arr = array("no"=>"", "code"=>$rs['category_name'], "cost"=>"", "price"=>"", "qty"=>"", "cost_amount"=>"", "price_amount"=>"");
		array_push($data, $arr);
		$qr 		= "SELECT product_code,cost, price, SUM(qty) AS qty FROM tbl_stock ";
		$qr 		.= "JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qr 		.= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr 		.= "JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone ";
		$qr 		.= "WHERE ".$wh." AND default_category_id = ".$rs['id_category']." GROUP BY tbl_product.id_product ORDER BY product_code ASC";  
		$qr		= dbQuery($qr);
		$n			= 1;
		$total_qty	= 0;	$total_cost_amount = 0;  $total_price_amount = 0;
		if(dbNumRows($qr) > 0 )
		{
			while($rd = dbFetchArray($qr) )
			{
				$arr = array(
							"no"				=> $n, 
							"code"			=> $rd['product_code'], 
							"cost"				=> number_format($rd['cost'],2), 
							"price"			=> number_format($rd['price'],2), 
							"qty"				=> number_format($rd['qty']), 
							"cost_amount"	=> number_format($rd['qty']*$rd['cost'],2), 
							"price_amount"	=> number_format($rd['qty']*$rd['price'],2)
							);
				array_push($data, $arr);
				$total_qty 				+= $rd['qty'];
				$total_cost_amount	+= $rd['qty']*$rd['cost'];
				$total_price_amount	+= $rd['qty']*$rd['price'];
				$n++;	
			}
		}
		$arr = array("no"=>"", "code"=>"Grand Total", "cost"=>"", "price"=>"", "qty"=>number_format($total_qty), "cost_amount"=>number_format($total_cost_amount,2), "price_amount"=>number_format($total_price_amount,2));
		array_push($data, $arr);
		endwhile;
		echo json_encode($data);
	else :
		echo "fail";
	endif;
	
}


if( isset( $_GET['summary_by_category'] ) && isset( $_GET['export'] ) )
{
	$rank 	= $_GET['rank'];
	$wh		= $_GET['wh'];
	if($wh == 0 )
	{ 
		$wh 		= "id_warehouse != 0"; 
		$wh_title	= "รวมทุกคลัง";
	}else{ 
		$wh 		= "id_warehouse = ".$_GET['wh']; 
		$wh_title	= warehouse_name($_GET['wh']);
	}
	if($rank == 2 )
	{
		$in		= "";
		$c 	= count($_POST['p_selected']);
		foreach($_POST['p_selected'] as $id) :
			$c--;
			$in		.= $id;
			if($c > 0){ $in .= ", "; }
		endforeach;
		$qs = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category IN(".$in.")");
		$p_title	= "บางหมวดหมู่สินค้า";
	}
	else
	{
		$qs = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category != 0");
		$p_title 	= "ทุกหมวดหมู่สินค้า";
	}
	$data = array();
	$arr 	 = array("==========  รายงานสรุปสินค้าคงเหลือ แยกตามหมวดหมู่สินค้า  ==========");
	array_push($data, $arr);
	$arr 	= array("หมวดหมู่ : ", $p_title, $wh_title);
	array_push($data, $arr);
	$arr	= array("ลำดับ", "รหัส", "ทุน", "ราคา", "คงเหลือ", "มูลค่าทุน", "มูลค่าราคา");
	array_push($data, $arr);
	if(dbNumRows($qs) > 0 ) :
		while($rs = dbFetchArray($qs) ) :
		$arr 	= array("===============  ".$rs['category_name']."  ===============");
		array_push($data, $arr);
		$qr = dbQuery("SELECT id_product, product_code, product_cost AS cost, product_price AS price FROM tbl_product WHERE default_category_id = ".$rs['id_category']);
		$n			= 1;
		$total_qty	= 0;	$total_cost_amount = 0;  $total_price_amount = 0;
		if(dbNumRows($qr) > 0 )
		{
			while($rd = dbFetchArray($qr) )
			{
				$qty = product_stock_qty($rd['id_product'], $_GET['wh']);
				$arr = array($n, $rd['product_code'], $rd['cost'], $rd['price'], $qty, $qty*$rd['cost'], $qty*$rd['price'] );
				array_push($data, $arr);
				$total_qty 				+= $qty;
				$total_cost_amount	+= $qty*$rd['cost'];
				$total_price_amount	+= $qty*$rd['price'];
				$n++;	
			}
		}
		$arr = array("","Grand Total","", "", $total_qty, $total_cost_amount, $total_price_amount);
		array_push($data, $arr);
		endwhile;
	endif;
	$sheet_name	= "Stock_summary_by_category";
	$Excel 			= new Excel_XML("UTF-8", false, $sheet_name);
	$Excel->addArray($data);
	$Excel->generateXML($sheet_name);	
	setToken($_GET['token']);
}

/***********************  รายงานความเคลื่อนไหวสินค้าแต่ละตัว  *************************************/
if( isset( $_GET['fifo_report']) && isset( $_GET['report'] ) )
{
	include '../../function/fifo_helper.php';
	$wh			= $_POST['id_wh'];
	$from 		= fromDate($_POST['from_date'], true);
	$to 			= toDate($_POST['to_date'], true);
	$reorder		= reorder($_POST['p_from'], $_POST['p_to']);
	$p_from  	= $reorder['from'];
	$p_to			= $reorder['to'];
	$qs 			= dbQuery("SELECT id_product_attribute, id_product, reference, barcode FROM tbl_product_attribute WHERE reference BETWEEN '".$p_from."' AND '".$p_to."' ORDER BY reference ASC");
	
	$data 		= array();
	$bf_date 	= date('Y-m-d', strtotime("-1day $from"));
	$wh_name 	= $wh == 0 ? "รวมทุกคลัง" : get_warehouse_name_by_id($wh);
	$title			=  "รายงานความเคลื่อนไหวสินค้า วันที่ ".thaiDate($from)." ถึงวันที่ ".thaiDate($to)."  คลัง : ".$wh_name;
	$arr 			= array("title" =>$title, "p_range" => "สินค้าตั้งแต่ ".$p_from."  ถึง  ".$p_to);
	array_push($data, $arr);	
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) ) :
			$product 	= new product();
			$id 			= $rs['id_product_attribute'];	
			$balance		= fifoBeforeBalance($id, $wh, $from);
			$title 			= "Fifo report : ".$rs['reference']." : ".thaiDate($from, "/")." - ".thaiDate($to, "/")." : ".$wh_name;
			$btn 			= "report/reportController/stockReportController.php?print_stock_card&id_product_attribute=$id&from_date=$from&to_date=$to&before_date=$bf_date&id_warehouse=$wh&title=".$title;
			$arr 			= array("sub_header" => $rs['reference']."  :  ".get_product_name($rs['id_product'])."  :  ".$rs['barcode'], "btn" => $btn);
			array_push($data, $arr);
			$arr 			= array("date" => thaiDate($bf_date), 	"reference" =>"ยอดยกมา", "wh" => "", "in"=>"", "out" => "", "balance" => number_format($balance));
			array_push($data, $arr);
			if($wh)
			{
				$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh FROM tbl_stock_movement ";
				$sql .= "WHERE id_product_attribute =".$id." AND id_warehouse = ".$wh." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
			}
			else
			{
				$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh FROM tbl_stock_movement ";
				$sql .= "WHERE id_product_attribute =".$id." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
			}
			$qr 	= dbQuery($sql);
			if(dbNumRows($qr) > 0 )
			{
				while($rd = dbFetchArray($qr)) :
					$balance 	= $balance + $rd['move_in'] - $rd['move_out'];		
					$arr 			= array(
											"date" 		=> thaiDate($rd['date_upd']), 
											"reference" 	=>	$rd['reference'], 
											"wh" 			=> get_warehouse_name_by_id($rd['wh']), 
											"in"				=> number_format($rd['move_in']), 
											"out" 			=> number_format($rd['move_out']),  
											"balance" 	=> number_format($balance)
											);
					array_push($data, $arr);
				endwhile;
			}
			else
			{
				$arr = array("nomovement" => "nomovement");
				array_push($data, $arr);	
			}
			$arr = array("blank_line" => "x");
			array_push($data, $arr);			
		endwhile;
	}
	else
	{
		$arr = array("nocontent" => "nocontent");
		array_push($data, $arr);	
	}
	echo json_encode($data);
}



//********************************** ปริ้น สต็อกการ์ด *****************************//
if( isset( $_GET['print_stock_card'] ) && isset( $_GET['id_product_attribute'] ) )
{
	$id 			= $_GET['id_product_attribute'];
	$from			= $_GET['from_date'];
	$to 			= $_GET['to_date'];
	$bf_date 	= $_GET['before_date'];
	$wh 			= $_GET['id_warehouse'];
	$title 			= $_GET['title'];
	$print 		= new printer();
	echo $print->doc_header();
	$print->add_title($title);
	//$header	= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "รหัสผู้ขาย"=>supplier_code($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date), "ชื่อผู้ขาย"=>supplier_name($po->id_supplier));
	//$print->add_header($header);
	
	$product 	= new product();
	$qty 			= stock_qty_by_warehouse($id, $wh);
	$balance		= $qty - (move_in($id, $from, date("Y-m-d H:i:s"), $wh) - move_out($id, $from, date("Y-m-d H:i:s"), $wh));
	if($wh)
	{
		$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh FROM tbl_stock_movement ";
		$sql .= "WHERE id_product_attribute =".$id." AND id_warehouse = ".$wh." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
	}
	else
	{
		$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh FROM tbl_stock_movement ";
		$sql .= "WHERE id_product_attribute =".$id." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
	}
	$detail 			= dbQuery($sql);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>1, "header_rows"=>0, "title_size" =>"strong", "header"=>false, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("วันที่", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่เอกสาร", "width:25%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คลังสินค้า", "width:25%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("เข้า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ออก", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คงเหลือ", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"text-align: center; border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้จัดทำ", "","วันที่............................."), 
						array("ผู้ตรวจสอบ", "","วันที่............................."),
						array("ผู้อนุมัติ", "","วันที่.............................")
						);						
	$print->set_footer($footer);		
	
	$n = 1;
	while($total_page > 0 )
	{
		echo $print->page_start();
			echo $print->top_page();
			echo $print->content_start();
				echo $print->table_start();
				$i = 0;
				//$product = new product();
				while($i<$row) : 
					$rs = dbFetchArray($detail);
					if(count($rs) != 0) :
						$balance = $balance + $rs['move_in'] - $rs['move_out'];		
						$data		= array($n, thaiDate($rs['date_upd']), $rs['reference'], get_warehouse_name_by_id($rs['wh']), number_format($rs['move_in']), number_format($rs['move_out']), number_format($balance) );
					else :
						$data = array(
											"<input type='text' style='border:0px; width:100%' />", 
											"<input type='text' style='border:0px; width:100%' />",
											"<input type='text' style='border:0px; width:100%' />",
											"<input type='text' style='border:0px; width:100%' />",
											"<input type='text' style='border:0px; width:100%' />",
											"<input type='text' style='border:0px; width:100%' />",
											"<input type='text' style='border:0px; width:100%' />"
											);
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
			

			$sub_total = array( array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; border-bottom-left-radius:10px; border-bottom-right-radius:10px;width:100%; text-align:right;'>
										 	<input type='text' style='width:100%; border:0px; text-align:center;' /> </td>"));
			echo $print->print_sub_total($sub_total);				
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();

}

if( isset( $_GET['stock_in_zone'] ) && isset( $_GET['report'] ) )
{
	$id_zone 	= $_POST['id_zone'];
	$zone			= get_zone($id_zone);
	$data 		= array();
	$arr 			= array("zone" => $zone);
	array_push($data, $arr);
	$qs			= "SELECT barcode, reference, cost, qty ";
	$qs 			.= "FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qs			.= "WHERE tbl_stock.id_zone = ".$id_zone;
	$qs 			= dbQuery($qs);
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs) ) :
			$arr = array(
						"no" 			=> $n,
						"barcode" 	=> $rs['barcode'],
						"reference" 	=> $rs['reference'],
						"cost" 		=> number_format($rs['cost'],2),
						"qty"			=> number_format($rs['qty']),
						"amount" 		=> number_format($rs['cost'] * $rs['qty'],2)
						);
				array_push($data, $arr);
				$n++; 
				$total_qty += $rs['qty'];
				$total_amount += $rs['qty'] * $rs['cost'];
		endwhile;
		$arr = array("total_qty" => number_format($total_qty),"total_amount" => number_format($total_amount,2));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nodata" => "nodata");
		array_push($data, $arr);
	}
	echo json_encode($data);
}

?>