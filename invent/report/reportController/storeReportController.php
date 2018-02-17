<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";
require "../../function/support_helper.php";
require "../../function/report_helper.php";

//***********************************************************************  รายงานการรับสินค้าเข้าจากการแปรสภาพ แยกตามเอกสาร *********************************************************//

if( isset( $_GET['transform_by_document'] ) && isset( $_GET['report'] ) )
{
	$range 	= $_POST['range'];
	$from_d 	= $_POST['from_doc'];
	$to_d 	= $_POST['to_doc'];
	$from		= dbDate($_POST['from_date']);
	$to 		= dbDate($_POST['to_date']);
	$re 		= new receive_tranform();
	$data 	= array();
	if( $range == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_tranform WHERE status = 1 AND ( reference BETWEEN '".$from_d."' AND '".$to_d."') AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference");	
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_tranform WHERE status = 1 AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference");	
	}
	
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array(
								"id"						=> $rs['id_receive_tranform'],
								"date"					=> thaiDate($rs['date_add']),
								"reference"			=> $rs['reference'],
								"order_reference"	=> $rs['order_reference'],
								"qty"					=> number_format($re->total_qty($rs['id_receive_tranform'])),
								"employee"			=> employee_name($rs['id_employee']),
								"remark"				=> $rs['remark']
								);
			array_push($data, $arr);								
		}		
	}
	else
	{
		$arr = array("nocontent" => "----- ไม่พบรายการตามเงื่อนไขที่กำหนด  -----");
		array_push($data, $arr);			
	}
	
	echo json_encode($data);	
}

//********************  Export Received transform ***********************//
if( isset( $_GET['transform_by_document'] ) && isset( $_GET['export'] ) )
{
	$range 	= $_GET['range'];
	$from_d 	= $_GET['from_doc'];
	$to_d 	= $_GET['to_doc'];
	$from		= dbDate($_GET['from_date']);
	$to 		= dbDate($_GET['to_date']);
	$re 		= new receive_tranform();
	$data 	= array();
	if( $range == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_tranform WHERE status = 1 AND ( reference BETWEEN '".$from_d."' AND '".$to_d."') AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference");	
		$doc_range 	= $from_d."  -  ".$to_d;
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_tranform WHERE status = 1 AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference");	
		$doc_range 	= "ทั้งหมด";
	}
	
	$arr = array("รายงานการรับสินค้าจากการแปรสภาพ แยกตามเลขที่เอกสาร");
	array_push($data, $arr);
	
	$arr = array("ช่วงเอกสาร : ".$doc_range."   วันที่   ".thaiDate($from)."  ถึงวันที่  ".thaiDate($to));
	array_push($data, $arr);
	
	$arr = array("ลำดับ", "วันที่", "เลขที่เอกสาร", "เลขที่อ้างอิง", "จำนวนรวม", "พนักงาน", "หมายเหตุ");
	array_push($data, $arr);
	
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array($n, thaiDate($rs['date_add']), $rs['reference'], $rs['order_reference'], $re->total_qty($rs['id_receive_tranform']), employee_name($rs['id_employee']), $rs['remark']);
			array_push($data, $arr);
			$n++;
		}		
	}
	else
	{
		$arr = array("----- ไม่พบรายการตามเงื่อนไขที่กำหนด  -----");
		array_push($data, $arr);			
	}
	
	$excel 	= new Excel_XML("UTF-8", true, "transform_by_document");
	$excel->addArray($data);
	$excel->generateXML("Transform_by_document");
	setToken($_GET['token']);
}

if( isset($_GET['get_received_tranform_detail']) && isset( $_POST['id_received_tranform'] ) )
{
	$id 	= $_POST['id_received_tranform'];
	$rd 	= new receive_tranform($id);
	$data = array();
	$arr 	= array(
					"reference"			=> $rd->reference,
					"order_reference"	=> $rd->order_reference,
					"date"					=> thaiDate($rd->date_add),
					"employee"			=> employee_name($rd->id_employee),
					"remark"				=> $rd->remark
					);
	array_push($data, $arr);
	
	$qs 			= $rd->get_items($id);
	if( dbNumRows($qs) > 0 )
	{
		$n				= 1;
		$total_qty 	= 0;
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array(
							"no"					=> $n,
							"product_reference" 	=> get_product_reference($rs['id_product_attribute']),
							"product_name"			=> get_product_name($rs['id_product']),
							"zone"						=> get_zone($rs['id_zone']),
							"qty"						=> number_format($rs['qty']),
							"status"					=> isActived($rs['status'])
							);
			$total_qty += $rs['qty'];
			$n++;	
			array_push($data, $arr);
		}
		$arr = array("total_qty" => number_format($total_qty));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent" => "----- ไม่พบรายการ -----");
		array_push($data, $arr);	
	}
	
	echo json_encode($data);	
}

//***********************************************************************************  รับเข้าจากการแปรสภาพ  ******************************************************************//
if( isset( $_GET['receive_tranform_product'] ) && isset( $_GET['report'] ) )
{
	$p_rank 	= $_POST['po'];
	$t_rank	= $_POST['rank'];
	$data 	= array();
	if( $p_rank == 2 ){ $pq = "AND order_reference = '".$_POST['reference']."' "; }else{ 	$pq = "AND order_reference != '' "; }
	if(  $t_rank == 2 ){ $tq = " AND ( tbl_receive_tranform.date_add BETWEEN '".fromDate($_POST['from_date'], true)."' AND '".toDate($_POST['to_date'], true)."')"; }else{ $tq = ""; }
	$qs = "SELECT tbl_receive_tranform_detail.id_product, tbl_receive_tranform_detail.id_product_attribute, tbl_receive_tranform.reference, ";
	$qs .= "tbl_receive_tranform.order_reference, tbl_receive_tranform.date_add, qty, tbl_receive_tranform_detail.id_zone ";
	$qs .= "FROM tbl_receive_tranform_detail JOIN tbl_receive_tranform ON tbl_receive_tranform_detail.id_receive_tranform = tbl_receive_tranform.id_receive_tranform ";
	$qs .= "WHERE tbl_receive_tranform.status = 1 ".$pq.$tq." ORDER BY tbl_receive_tranform.date_add ASC";
	$qs = dbQuery($qs);
	if( dbNumRows($qs) > 0 ) :
		$n = 1;
		while( $rs = dbFetchArray($qs) ) :
			$arr = array(
							"no" 				=> $n,
							"date_add" 		=> thaiDate($rs['date_add']),
							"product_code"	=> get_product_reference($rs['id_product_attribute']),
							"product_name"	=> get_product_name($rs['id_product']),
							"reference" 		=> $rs['reference'],
							"order_reference" => $rs['order_reference'],
							"qty" 				=> number_format($rs['qty']),
							"zone" 			=> get_zone($rs['id_zone'])
						);
			array_push($data, $arr);		
			$n++;
		endwhile;
		echo json_encode($data);
	else :
		echo "fail";	
	endif;
}

if( isset( $_GET['receive_tranform_product'] ) && isset( $_GET['export'] ) )
{
	$p_rank 	= $_GET['po'];
	$t_rank	= $_GET['rank'];
	$data 	= array();
	if( $p_rank == 2 ){ $pq = "AND order_reference = '".$_GET['reference']."' "; $p_title = $_GET['reference']; }else{ 	$pq = "AND order_reference != '' ";  $p_title = "ทั้งหมด"; }
	if(  $t_rank == 2 )
	{ 
		$tq 		= " AND ( tbl_receive_tranform.date_add BETWEEN '".fromDate($_GET['from_date'], true)."' AND '".toDate($_GET['to_date'], true)."')"; 
		$t_from 	=  $_GET['from_date'];
		$t_to		= $_GET['to_date'];
	}
	else
	{ 
		$tq 		= ""; 
		$t_from 	=  "ทั้งหมด";
		$t_to		=  "ทั้งหมด";
	}
	$arr = array("รายงานการรับสินค้าเข้าจากการแปรสภาพแสดงรายการสินค้า ");
	array_push($data, $arr);
	$arr = array("เลขที่อ้างอิง", $p_title,  "วันที่", $t_from, "ถึง", $t_to);
	array_push($data, $arr);
	$arr = array("********************************************************************************************************************");
	array_push($data, $arr);
	$arr = array("ลำดับ", "วันที่", "รหัส", "สินค้า", "เลขที่เอกสาร", "เลขที่อ้างอิง", "จำนวน", "โซน");
	array_push($data, $arr);
	$qs = "SELECT tbl_receive_tranform_detail.id_product, tbl_receive_tranform_detail.id_product_attribute, tbl_receive_tranform.reference, ";
	$qs .= "tbl_receive_tranform.order_reference, tbl_receive_tranform.date_add, qty, tbl_receive_tranform_detail.id_zone ";
	$qs .= "FROM tbl_receive_tranform_detail JOIN tbl_receive_tranform ON tbl_receive_tranform_detail.id_receive_tranform = tbl_receive_tranform.id_receive_tranform ";
	$qs .= "WHERE tbl_receive_tranform.status = 1 ".$pq.$tq." ORDER BY tbl_receive_tranform.date_add ASC";
	$qs = dbQuery($qs);
	if( dbNumRows($qs) > 0 ) :
		$n = 1;
		while( $rs = dbFetchArray($qs) ) :
			$arr = array( $n, thaiDate($rs['date_add']), get_product_reference($rs['id_product_attribute']), get_product_name($rs['id_product']), $rs['reference'], $rs['order_reference'], $rs['qty'], get_zone($rs['id_zone']) );
			array_push($data, $arr);		
			$n++;
		endwhile;
	else :
		$arr = array("ไม่มีข้อมูล");
		array_push($data, $arr);	
	endif;
	
	$excel 	= new Excel_XML("UTF-8", false, "received_tranform_detail");
	$excel->addArray($data);
	$excel->generateXML("received_tranform_detail");
	setToken($_GET['token']);
}

if( isset( $_GET['receive_tranform_product'] ) && isset( $_GET['print_report'] ) )
{
	$p_rank 	= $_GET['po'];
	$t_rank	= $_GET['rank'];
	$data 	= array();
	if( $p_rank == 2 ){ $pq = "AND order_reference = '".$_GET['reference']."' "; }else{ 	$pq = "AND order_reference != '' "; }
	if(  $t_rank == 2 ){ $tq = " AND ( tbl_receive_tranform.date_add BETWEEN '".fromDate($_GET['from_date'], true)."' AND '".toDate($_GET['to_date'], true)."')"; }else{ $tq = ""; }
	$qs = "SELECT tbl_receive_tranform_detail.id_product, tbl_receive_tranform_detail.id_product_attribute, tbl_receive_tranform.reference, ";
	$qs .= "tbl_receive_tranform.order_reference, tbl_receive_tranform.date_add, qty, tbl_receive_tranform_detail.id_zone ";
	$qs .= "FROM tbl_receive_tranform_detail JOIN tbl_receive_tranform ON tbl_receive_tranform_detail.id_receive_tranform = tbl_receive_tranform.id_receive_tranform ";
	$qs .= "WHERE tbl_receive_tranform.status = 1 ".$pq.$tq." ORDER BY tbl_receive_tranform.date_add ASC";
		
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("รายงานการรับสินค้าจากการแปรสภาพแสดงรายการสินค้า");
	//$header			= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "รหัสผู้ขาย"=>supplier_code($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date), "ชื่อผู้ขาย"=>supplier_name($po->id_supplier));
	//$print->add_header($header);
	$detail 			= dbQuery($qs);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>1, "header_rows"=>0, "header"=>false, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("วันที่", "width:10%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("รหัส", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:30%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่เอกสาร", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่อ้างอิง", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("โซน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
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
						$product_code 	= get_product_reference($rs['id_product_attribute']);
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".get_product_name($rs['id_product'])."' />";
						$data 				= array($n, thaiDate($rs['date_add']), $product_code, $product_name, $rs['reference'], $rs['order_reference'], ac_format($rs['qty']), get_zone($rs['id_zone']) );
					else :
						$data = array(
											"<input type='text' style='border:0px; width:100%' />", 
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

//***********************************************************************************  รับเข้าจากการซื้อ  ******************************************************************//

if( isset( $_GET['receive_product'] ) && isset( $_GET['report'] ) )
{
	include_once '../../function/po_helper.php';
	include 'report/reportReceivedProduct.php';
}

if( isset( $_GET['receive_product'] ) && isset( $_GET['export'] ) )
{
	include_once '../../function/po_helper.php';
	include 'export/exportReceivedProduct.php';
}


if( isset( $_GET['receive_product'] ) && isset( $_GET['print_report'] ) )
{
	$p_rank 	= $_GET['po'];
	$s_rank 	= $_GET['sup'];
	$t_rank	= $_GET['rank'];
	$data 	= array();
	if( $p_rank == 2 ){ $pq = "AND po_reference = '".$_GET['reference']."' "; }else{ 	$pq = "AND po_reference != '' "; }
	if( $s_rank == 2 ){ $sq = "AND id_supplier = ".$_GET['id_sup']." "; }else	{ $sq	= "AND id_supplier != '' "; }
	if(  $t_rank == 2 ){ $tq = " AND ( tbl_receive_product.date_add BETWEEN '".fromDate($_GET['from_date'], true)."' AND '".toDate($_GET['to_date'], true)."')"; }else{ $tq = ""; }
	$qs = "SELECT tbl_receive_product_detail.id_product, tbl_receive_product_detail.id_product_attribute, tbl_receive_product.reference, ";
	$qs .= "tbl_receive_product.po_reference, tbl_receive_product.invoice, tbl_receive_product.date_add, qty, tbl_receive_product_detail.id_zone ";
	$qs .= "FROM tbl_receive_product_detail JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qs .= "JOIN tbl_po ON tbl_receive_product.po_reference = tbl_po.reference ";
	$qs .= "WHERE tbl_receive_product.status = 1 ".$pq.$sq.$tq." ORDER BY tbl_receive_product.po_reference DESC";
	
	
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("รายงานการรับสินค้าจากการซื้อ");
	//$header			= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "รหัสผู้ขาย"=>supplier_code($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date), "ชื่อผู้ขาย"=>supplier_name($po->id_supplier));
	//$print->add_header($header);
	$detail 			= dbQuery($qs);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>1, "header_rows"=>0, "header"=>false, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("วันที่", "width:10%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("รหัส", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่เอกสาร", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่ใบสั่งซื้อ", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่ใบส่งสินค้า", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"text-align: center; border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
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
						$product_code 	= get_product_reference($rs['id_product_attribute']);
						$invoice 	= "<input type='text' style='border:0px; width:100%;' value='".$rs['invoice']."' />";
						$data 				= array($n, thaiDate($rs['date_add']), $product_code, $rs['reference'], $rs['po_reference'], $invoice, ac_format($rs['qty']));
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

if( isset($_GET['received_by_document']) && isset($_GET['report']) )
{
	$from		= date("Y-m-d", strtotime($_POST['from_date']));
	$to		= date("Y-m-d", strtotime($_POST['to_date']));
	$range 	= $_POST['range'];
	$from_d	= $_POST['from_doc'];
	$to_d		= $_POST['to_doc'];
	$data		= array();
	if( $range == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_product WHERE status = 1 AND (reference BETWEEN '".$from_d."' AND '".$to_d."') AND (date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference ASC");
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_product WHERE status = 1 AND (date_add BETWEEN '".$from."' AND '".$to."') ORDER BY date_add ASC");
	}
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$re = new receive_product();
			$arr = array(
							"id"				=> $rs['id_receive_product'],
							"date" 		=> thaiDate($rs['date_add']),
							"reference" 	=> $rs['reference'],
							"po"			=> $rs['po_reference'],
							"id_po"		=> $rs['id_po'],
							"invoice"		=> $rs['invoice'],
							"qty"			=> number_format($re->total_qty($rs['id_receive_product'])),
							"amount"		=> number_format($re->total_amount($rs['id_receive_product']), 2),
							"employee"	=> employee_name($rs['id_employee']),
							"remark"		=> $rs['remark']
							);
			array_push($data, $arr);
		}	
	}
	else
	{
		$arr = array("nocontent" => "----- ไม่พบรายการตามเงื่อนไขที่กำหนด  -----");
		array_push($data, $arr);
	}
	echo json_encode($data);
}


if( isset($_GET['received_by_document']) && isset($_GET['export']) )
{
	$from		= date("Y-m-d", strtotime($_GET['from_date']));
	$to		= date("Y-m-d", strtotime($_GET['to_date']));
	$range 	= $_GET['range'];
	$from_d	= $_GET['from_doc'];
	$to_d		= $_GET['to_doc'];
	$data		= array();
	if( $range == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_product WHERE status = 1 AND (reference BETWEEN '".$from_d."' AND '".$to_d."') AND (date_add BETWEEN '".$from."' AND '".$to."') ORDER BY reference ASC");
		$doc_range = $from_d." - ".$to_d;
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_receive_product WHERE status = 1 AND (date_add BETWEEN '".$from."' AND '".$to."') ORDER BY date_add ASC");
		$doc_range = "ทั้งหมด";
	}
	
	$arr = array("รายงานการรับสินค้า แยกตามเลขที่เอกสาร");
	array_push($data, $arr);
	
	$arr = array("ช่วงเอกสาร : ".$doc_range."    วันที่  ". thaiDate($from). "   ถึงวันที่ ". thaiDate($to));
	array_push($data, $arr);
	
	$arr = array("ลำดับ", "วันที่", "เลขที่เอกสาร", "ใบสั่งซื้อ", "ใบส่งสินค้า", "จำนวนรวม", "มูลค่า", "พนักงาน","หมายเหตุ");
	array_push($data, $arr);
	
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while($rs = dbFetchArray($qs) )
		{
			$re = new receive_product();
			$arr = array($n, thaiDate($rs['date_add']), $rs['reference'], $rs['po_reference'], $rs['invoice'], $re->total_qty($rs['id_receive_product']), $re->total_amount($rs['id_receive_product']), employee_name($rs['id_employee']), $rs['remark']);
			array_push($data, $arr);
		}	
	}
	else
	{
		$arr = array("nocontent" => "----- ไม่พบรายการตามเงื่อนไขที่กำหนด  -----");
		array_push($data, $arr);
	}
	$excel 	= new Excel_XML("UTF-8", true, "Received_by_document");
	$excel->addArray($data);
	$excel->generateXML("received_by_document");
	setToken($_GET['token']);	
}


?>