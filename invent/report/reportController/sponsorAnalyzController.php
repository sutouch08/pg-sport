<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";


if( isset( $_GET['sponsorProductDeepAnalyz'] ) )
{
	$from = fromDate($_GET['from']);
	$to	= toDate($_GET['to']);
	$vat	= getConfig('VAT');
	$pred = date("dmY", strtotime($from)) .' - '. date("dmY", strtotime($to));
	//$pGroup	= getConfig('ITEMS_GROUP');
	$excel	= new PHPExcel();
	
	$excel->getProperties()->setCreator("Samart Invent")
							 ->setLastModifiedBy("Samart Invent")
							 ->setTitle("Report Sold Deep Analyz")
							 ->setSubject("Report Sold Deep Analyz")
							 ->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8")
							 ->setKeywords("Samart Invent")
							 ->setCategory("Sale Report");
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('วิเคราะห์สปอนเซอร์แบบละเอียด');
	
	//---------  หัวตาราง  ------------//
	$excel->getActiveSheet()->setCellValue('A1', 'sold_date');
	$excel->getActiveSheet()->setCellValue('B1', 'product');
	$excel->getActiveSheet()->setCellValue('C1', 'color');
	$excel->getActiveSheet()->setCellValue('D1', 'size');
	$excel->getActiveSheet()->setCellValue('E1', 'attribute');
	$excel->getActiveSheet()->setCellValue('F1', 'category');
	$excel->getActiveSheet()->setCellValue('G1', 'product_group');
	$excel->getActiveSheet()->setCellValue('H1', 'receiver');
	$excel->getActiveSheet()->setCellValue('I1', 'emp');
	$excel->getActiveSheet()->setCellValue('J1', 'cost_ex');
	$excel->getActiveSheet()->setCellValue('K1', 'cost_inc');
	$excel->getActiveSheet()->setCellValue('L1', 'price_ex');
	$excel->getActiveSheet()->setCellValue('M1', 'price_inc');
	$excel->getActiveSheet()->setCellValue('N1', 'qty');
	$excel->getActiveSheet()->setCellValue('O1', 'amount_ex');
	$excel->getActiveSheet()->setCellValue('P1', 'amount_inc');
	$excel->getActiveSheet()->setCellValue('Q1', 'total_cost_ex');
	$excel->getActiveSheet()->setCellValue('R1', 'total_cost_inc');
	
	
	$qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_role = 4 AND date_upd > '".$from."' AND date_upd < '".$to."' ORDER BY id_product ASC");
	
	if( dbNumRows($qs) > 0 )
	{
		$row	= 2;  //------ เริ่มต้นแถวที่ 2
		while( $rs = dbFetchArray($qs) )
		{
			$pa	= getProductAttribute($rs['id_product_attribute']);  //------  return as array $pa['id_color'], $pa['id_size'], $pa['id_attribute']
			$y		= date('Y', strtotime($rs['date_upd']) );
			$m		= date('m', strtotime($rs['date_upd']) );
			$d		= date('d', strtotime($rs['date_upd']) );
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			$excel->getActiveSheet()->setCellValue('A'.$row, $date);  //----- วันที่
			$excel->getActiveSheet()->setCellValue('B'.$row, get_product_code($rs['id_product'])); //------ รุ่นสินค้า
			$excel->getActiveSheet()->setCellValue('C'.$row, get_color_code($pa['id_color']) ); //-----  สี
			$excel->getActiveSheet()->setCellValue('D'.$row, get_size_name($pa['id_size']) ); //------- Size
			$excel->getActiveSheet()->setCellValue('E'.$row, get_attribute_name($pa['id_attribute']) ); //----- คุณลักษระอื่นๆ
			$excel->getActiveSheet()->setCellValue('F'.$row, getDefaultCategoryName($rs['id_product']) ); //----- กลุ่มสินค้า
			$excel->getActiveSheet()->setCellValue('G'.$row, getProductGroupName($rs['id_product']));
			$excel->getActiveSheet()->setCellValue('H'.$row, customer_name($rs['id_customer']) ); //--- ผู้รับอภินันท์
			$excel->getActiveSheet()->setCellValue('I'.$row, employee_name($rs['id_employee'])); //---- พนักงานผู้ทำรายการ
			$excel->getActiveSheet()->setCellValue('J'.$row, removeVAT($rs['cost'], $vat) ); //-----  ทุนไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('K'.$row, $rs['cost'] ); //----- ทุนรวม VAT
			$excel->getActiveSheet()->setCellValue('L'.$row, removeVAT($rs['product_price'], $vat) ); //----- ราคาป้าย ไม่ราม VAT
			$excel->getActiveSheet()->setCellValue('M'.$row, $rs['product_price'] ); //----- ราคาป้าย
			$excel->getActiveSheet()->setCellValue('N'.$row, $rs['sold_qty'] ); //----- จำนวน
			$excel->getActiveSheet()->setCellValue('O'.$row, removeVAT($rs['total_amount'], $vat) ); //----- มูลค่าขายไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('P'.$row, $rs['total_amount'] ); //----- มูลค่าขาย
			$excel->getActiveSheet()->setCellValue('Q'.$row, removeVAT($rs['total_cost'], $vat) ); //---- ต้นทุนรวม (ไม่รวม VAT)
			$excel->getActiveSheet()->setCellValue('R'.$row, $rs['total_cost']); //---- ต้นทุนรวม (รวม VAT)
			
			$row++;			
			
		}//----- end while 		
		
		$excel->getActiveSheet()->getStyle('A2:A'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$excel->getActiveSheet()->getStyle('J2:M'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('N2:N'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('O2:R'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
	}

	setToken($_GET['token']);
	$file_name = "รายงานวิเคราะห์สปอนเซอร์แบบละเอียด".$pred.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');	
}

?>