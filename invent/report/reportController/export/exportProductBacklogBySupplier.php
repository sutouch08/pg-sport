<?php
	
	$pdRange	= isset( $_GET['pdRange'] ) ? $_GET['pdRange'] : 0;
	$pdFrom		= isset( $_GET['pdFrom'] ) ? trim($_GET['pdFrom']) : FALSE;
	$pdTo		= isset( $_GET['pdTo'] ) ? trim($_GET['pdTo']) : FALSE;
	$supRange	= isset( $_GET['supRange'] ) ? $_GET['supRange'] : 0;
	$id_sup		= isset( $_GET['id_sup'] ) ? $_GET['id_sup'] : 0;
	$tRank		= isset( $_GET['timeRange'] ) ? $_GET['timeRange'] : 1;
	$from			= isset( $_GET['from'] ) && $tRank == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to			= isset( $_GET['to'] ) && $tRank == 2 ? toDate($_GET['to']) : date('Y-12-31 23:59:59');
	$poOption	= isset( $_GET['poOption'] ) ? $_GET['poOption'] : 0 ;
	
	$pQuery		= $pdRange == 0 ? "" : "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' ";
	$sQuery		= $supRange == 1 ? "" : "AND tbl_po.id_supplier = ".$id_sup." ";
	$poQuery	= $poOption == 2 ? "" : ($poOption == 0 ? "AND tbl_po.valid = 0 " : "AND tbl_po.valid = 1 ");
	
	$reportTitle = 'รายงานสินค้าค้างส่ง แยกตามผู้ขาย แสดงใบสั่งซื้อ';
	$pTitle		= $pdRange == 0 ? 'ทั้งหมด' : $pdFrom .'  -  '. $pdTo;
	$sTitle		= $supRange == 1 ? 'ทั้งหมด' : supplier_name($id_sup);
	$tTitle			= thaiDate($from, '/') .' - '.thaiDate($to, '/');
	
	$excel	= new PHPExcel();
	
	$excel->getProperties()->setCreator("Samart Invent 1.0");
	$excel->getProperties()->setLastModifiedBy("Samart Invent 1.0");
	$excel->getProperties()->setTitle("Report PO Backlog By Product");
	$excel->getProperties()->setSubject("Report PO Backlog By Product");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Smart Invent 1.0");
	$excel->getProperties()->setCategory("Purchase Report");	
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('สินค้าค้างรับ');
	
	$excel->getActiveSheet()->setCellValue('A1', $reportTitle);
	$excel->getActiveSheet()->mergeCells('A1:J1');
	$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->setCellValue('A2', 'สินค้า :');
	$excel->getActiveSheet()->setCellValue('B2', $pTitle);
	$excel->getActiveSheet()->mergeCells('B2:J2');
	$excel->getActiveSheet()->setCellValue('A3', 'ผู้ขาย :');
	$excel->getActiveSheet()->setCellValue('B3', $sTitle);
	$excel->getActiveSheet()->mergeCells('B3:J3');
	$excel->getActiveSheet()->setCellValue('A4', 'วันที่ :');
	$excel->getActiveSheet()->setCellValue('B4', $tTitle);
	$excel->getActiveSheet()->mergeCells('B4:J4');
	
	
	$excel->getActiveSheet()->setCellValue('A5', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B5', 'วันที่');
	$excel->getActiveSheet()->setCellValue('C5', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('D5', 'เลขที่เอกสาร');
	$excel->getActiveSheet()->setCellValue('E5', 'ผู้ขาย');
	$excel->getActiveSheet()->setCellValue('F5', 'กำหนดรับ');
	$excel->getActiveSheet()->setCellValue('G5', 'จำนวน');
	$excel->getActiveSheet()->setCellValue('H5', 'รับแล้ว');
	$excel->getActiveSheet()->setCellValue('I5', 'ค้างรับ');
	$excel->getActiveSheet()->setCellValue('J5', 'หมายเหตุ');
	
	//-------------  Set Column Width
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	
	$excel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setVertical('center');
	
	$qr = "SELECT tbl_po_detail.id_product, ";
	$qr .= "product_code, ";
	$qr .= "tbl_po.reference, ";
	$qr .= "tbl_supplier.name, ";
	$qr .= "tbl_po.due_date, ";
	$qr .= "tbl_po.date_add, ";
	$qr .= "SUM(qty) AS qty, ";
	$qr .= "SUM(received) AS received, ";
	$qr .= "tbl_po.valid ";
	$qr .= "FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id ";
	$qr .= "WHERE tbl_po.id_po != 0 ";
	$qr .= $pQuery;
	$qr .= $sQuery;
	$qr .= $poQuery;
	$qr .= "AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po_detail.id_product, tbl_po_detail.id_po";

	$qs = dbQuery($qr);
	
	$row = 6;
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		while( $rs = dbFetchObject($qs) )
		{
			$balance = $rs->qty - $rs->received;
			$balance = $balance > 0 && $rs->valid == 0 ? $balance : 0;
			$closed = $rs->valid == 0 ? '' : 'closed';
			$y		= date('Y', strtotime($rs->date_add));
			$m		= date('m', strtotime($rs->date_add));
			$d 	= date('d', strtotime($rs->date_add));
			$dy		= date('Y', strtotime($rs->due_date));
			$dm		= date('m', strtotime($rs->due_date));
			$dd 	= date('d', strtotime($rs->due_date));
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			$dueDate = PHPExcel_Shared_Date::FormattedPHPToExcel($dy, $dm, $dd);
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $date);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_code);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->name);
			$excel->getActiveSheet()->setCellValue('F'.$row, $dueDate);
			$excel->getActiveSheet()->setCellValue('G'.$row, $rs->qty);
			$excel->getActiveSheet()->setCellValue('H'.$row, $rs->received);
			$excel->getActiveSheet()->setCellValue('I'.$row, $balance);
			$excel->getActiveSheet()->setCellValue('J'.$row, $closed);
			$row++;
			$no++;
		}
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
		$excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G6:G'.($row-1).')');
		$excel->getActiveSheet()->setCellValue('H'.$row, '=SUM(H6:H'.($row-1).')');
		$excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I6:I'.($row-1).')');
		
		$excel->getActiveSheet()->getStyle('B6:B'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');	
		$excel->getActiveSheet()->getStyle('F6:F'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');	
		$excel->getActiveSheet()->getStyle('G6:I'.$row)->getNumberFormat()->setFormatCode('#,##0');	
		$excel->getActiveSheet()->getStyle('B6:B'.$row)->getAlignment()->setHorizontal('center');
		$excel->getActiveSheet()->getStyle('F6:F'.$row)->getAlignment()->setHorizontal('center');
		$excel->getActiveSheet()->getStyle('J6:J'.$row)->getAlignment()->setHorizontal('center');
	}
	
	setToken($_GET['token']);
	$file_name = "รายงานสินค้าค้างรับ แยกตามผู้ขาย.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output'); 


?>