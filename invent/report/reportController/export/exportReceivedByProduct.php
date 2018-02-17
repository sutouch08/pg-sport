<?php

//----- รายงานการรับสินค้าแยกตามสินค้า
	$pRange		= $_GET['pRange'];
	$tRange		= $_GET['tRange'];
	$pFrom		= trim($_GET['pFrom']);
	$pTo			= trim($_GET['pTo']);
	$from			= $tRange == 2 ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-01-01');
	$to			= $tRange == 2 ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-12-31');
	$qp			= $pRange == 1 ? "AND product_code >= '".$pFrom."' AND product_code <= '".$pTo."' ": "";
	
	$excel = new PHPExcel();
	$excel->getProperties()->setCreator("Samart Invent 1.0");
	$excel->getProperties()->setLastModifiedBy("Samart Invent 1.0");
	$excel->getProperties()->setTitle("Report stock balance");
	$excel->getProperties()->setSubject("Report stock balance");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Smart Invent 1.0");
	$excel->getProperties()->setCategory("Stock Report");
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานการรับสินค้า');
	
	//------- Report name Row 1
	$title = 'รายงานการรับสินค้าแยกตามรุ่นสินค้า วันที่ ' . thaiDate($from, '/') .' - ' . thaiDate($to,'/');
	$excel->getActiveSheet()->setCellValue('A1', $title);
	$excel->getActiveSheet()->mergeCells('A1:D1');
	
	//-------- Report Conditions Row 2
	$excel->getActiveSheet()->setCellValue('A2', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('B2', $pRange == 0 ? 'ทั้งหมด' : $pFrom.' - '.$pTo);
	
	//--------- Report Table header
	$excel->getActiveSheet()->setCellValue('A3', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B3', 'รุ่นสินค้า');
	$excel->getActiveSheet()->setCellValue('C3', 'ชื่อสินค้า');
	$excel->getActiveSheet()->setCellValue('D3', 'จำนวน');
	
	$qr	= "SELECT tbl_receive_product_detail.id_product, product_code, product_name, SUM(tbl_receive_product_detail.qty) AS qty FROM tbl_receive_product_detail ";
	$qr 	.= "JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qr	.= "JOIN tbl_product ON tbl_receive_product_detail.id_product = tbl_product.id_product ";
	$qr 	.= "WHERE tbl_receive_product_detail.status = 1 ".$qp." AND tbl_receive_product.date_add >= '".$from."' AND tbl_receive_product.date_add <= '".$to."' ";
	$qr	.= "GROUP BY tbl_receive_product_detail.id_product ORDER BY tbl_product.product_code ASC";

	$qs 	= dbQuery($qr);
	
	$row = 4;
	if( dbNumRows($qs) > 0 )
	{
		$no	= 1;
		while( $rs = dbFetchObject($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_name);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->qty);
			$row++;
			$no++;
		}
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
		$excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D4:D'.( $row-1).')');
		$excel->getActiveSheet()->getStyle('D4:D'.$row)->getNumberFormat()->setFormatCode('#,##0');
	}
	
	setToken($_GET['token']);
	$file_name = "รายงานสรุปการรับสินค้าแยกตามสินค้า.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	

?>