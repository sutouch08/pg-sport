<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";

if( isset( $_GET['lend_not_return'] ) && isset( $_GET['report'] ) )
{
	$emp_range		= $_POST['emp_range'];
	$item_range		= $_POST['item_range'];
	$id_emp			= $_POST['id_employee'];
	$id_pa			= $_POST['id_product_attribute'];
	$id_product		= $_POST['id_product'];
	$from				= fromDate($_POST['from_date']);
	$to				= toDate($_POST['to_date']);
	$data				= array();
	$in					= $item_range == 2 ? id_product_attribute_in_product($id_product) : "";
	if( $emp_range == 0 && $item_range == 0 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 0 && $item_range == 1 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_product_attribute = ".$id_pa." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 0 && $item_range == 2 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_product_attribute IN(".$in.") AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 1 && $item_range == 0 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 1 && $item_range == 2 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND id_product_attribute IN(".$in.") AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";	
	}
	else
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND id_product_attribute = ".$id_pa." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";	
	}
	$qs = dbQuery($sql);
	if(dbNumRows($qs) > 0 )
	{
		$no 				= 1;
		$total_qty 		= 0; 
		$total_amount 	= 0;
		$product 		= new product();
		while( $rs = dbFetchArray($qs) )
		{
			$nr_qty 	= $rs['qty'] - $rs['return_qty'];
			$amount 	= $product->get_product_price($rs['id_product_attribute']) * $nr_qty;
			$arr = array(
							"no" => $no,
							"date"		=> thaiDate($rs['date_add']),
							"reference"	=> get_order_reference($rs['id_order']),
							"employee"	=> employee_name($rs['id_employee']),
							"item"			=> $product->get_product_reference($rs['id_product_attribute'])." : ".$product->product_name($product->getProductId($rs['id_product_attribute'])),
							"qty"			=> number_format($nr_qty),
							"amount"		=> number_format($amount, 2)
						);
			array_push($data, $arr);
			$total_qty 		+= $nr_qty;
			$total_amount	+= $amount;
			$no++;	
		}
		$arr = array("last" => "last", "total_qty" => number_format($total_qty), "total_amount" => number_format($total_amount, 2));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent" => "nocontent");
		array_push($data, $arr);	
	}
	echo json_encode($data);
}

if( isset( $_GET['lend_not_return'] ) && isset( $_GET['export'] ) )
{
	$emp_range		= $_POST['lender_range'];
	$item_range		= $_POST['item_range'];
	$id_emp			= $_POST['id_employee'];
	$id_pa			= $_POST['id_item'];
	$id_product		= $_POST['id_product'];
	$from				= fromDate($_POST['from_date']);
	$to				= toDate($_POST['to_date']);
	$in					= $item_range == 2 ? id_product_attribute_in_product($id_product) : "";
	if( $item_range == 2 ){ $p_range = $_POST['product']; }else if( $item_range == 1 ){ $p_range = $_POST['item']; }else{ $p_range = "ทุกรายการ"; }
	if( $emp_range == 1 ){ $e_range = $_POST['lender']; }else{ $e_range = "ทั้งหมด"; }
	if( $emp_range == 0 && $item_range == 0 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 0 && $item_range == 1 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_product_attribute = ".$id_pa." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 0 && $item_range == 2 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_product_attribute IN(".$in.") AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 1 && $item_range == 0 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";
	}
	else if( $emp_range == 1 && $item_range == 2 )
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND id_product_attribute IN(".$in.") AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";	
	}
	else
	{
		$sql = "SELECT id_order, id_employee, id_product_attribute, qty, return_qty, tbl_lend.date_add FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE ";
		$sql .= "status = 1 AND valid = 0 AND id_employee = ".$id_emp." AND id_product_attribute = ".$id_pa." AND ( tbl_lend.date_add BETWEEN '".$from."' AND '".$to."')";	
	}
	$qs = dbQuery($sql);
	
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("Lend not return");
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth(7);
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(50);
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth(10);
	
	$excel->getActiveSheet()->setCellValue("A1", "รายงานสินค้าค้างรับ (คืน) จากการยืม แสดง ผู้ยืม / เอกสาร / รายการสินค้า");
	$excel->getActiveSheet()->mergeCells("A1:H1");
	$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal("center")->setVertical("center");
	
	$excel->getActiveSheet()->setCellValue("A2", "ผู้ยืม : ".$e_range);
	$excel->getActiveSheet()->mergeCells("A2:C2");
	$excel->getActiveSheet()->getStyle("A2")->getAlignment()->setVertical("center");
	
	$excel->getActiveSheet()->setCellValue("D2", "สินค้า : ".$p_range);
	$excel->getActiveSheet()->mergeCells("D2:E2");
	$excel->getActiveSheet()->getStyle("D2")->getAlignment()->setVertical("center");
	
	$excel->getActiveSheet()->setCellValue("F2", "วันที่  ".thaiDate($from, "/"). "  -  ".thaiDate($to, "/"));
	$excel->getActiveSheet()->mergeCells("F2:H2");
	$excel->getActiveSheet()->getStyle("F2")->getAlignment()->setHorizontal("center")->setVertical("center");
	
	$excel->getActiveSheet()->setCellValue("A3", "ลำดับ");
	$excel->getActiveSheet()->setCellValue("B3", "วันที่");
	$excel->getActiveSheet()->setCellValue("C3", "เอกสาร");
	$excel->getActiveSheet()->setCellValue("D3", "ผู้ยืม");
	$excel->getActiveSheet()->setCellValue("E3", "สินค้า");
	$excel->getActiveSheet()->setCellValue("F3", "ราคา");
	$excel->getActiveSheet()->setCellValue("G3", "จำนวน");
	$excel->getActiveSheet()->setCellValue("H3", "มูลค่า");
	
	$excel->getActiveSheet()->getStyle("A3:H3")->getAlignment()->setHorizontal("center");
	
	$row = 4;
	if(dbNumRows($qs) > 0 )
	{
		$no 				= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$nr_qty 	= $rs['qty'] - $rs['return_qty'];
			$price 	= get_product_price($rs['id_product_attribute']);
			
			$excel->getActiveSheet()->setCellValue("A".$row, $no)
											->setCellValue("B".$row, thaiDate($rs['date_add'], "/"))
											->setCellValue("C".$row, get_order_reference($rs['id_order']))
											->setCellValue("D".$row, employee_name($rs['id_employee']))
											->setCellValue("E".$row, get_product_reference( $rs['id_product_attribute'] )." : ".get_product_name( get_id_product( $rs['id_product_attribute'] ) ) )
											->setCellValue("F".$row, $price)
											->setCellValue("G".$row, $nr_qty)
											->setCellValue("H".$row, "=F".$row." * G".$row);
			$no++;	$row++;
		}
		$rx = $row - 1;
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->mergeCells("A".$row.":F".$row);
		$excel->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal("right");
		$excel->getActiveSheet()->setCellValue("G".$row, "=SUM(G4:G".$rx.")")->setCellValue("H".$row, "=SUM(H4:H".$rx.")");
		
		$excel->getActiveSheet()->getStyle("F4:F".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("G4:G".$row)->getNumberFormat()->setFormatCode("#,##0");
		$excel->getActiveSheet()->getStyle("H4:H".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("A3:H".$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}
	//echo "<pre>"; print_r($excel); echo "</pre>";
	
	setToken($_GET['token']);
	$file_name = "Lend_not_return.xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");	
	
}

?>