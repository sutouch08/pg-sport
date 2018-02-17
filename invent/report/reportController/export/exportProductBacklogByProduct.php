<?php 
	$pdRange	= isset( $_GET['pdRange'] ) ? $_GET['pdRange'] : 0;	
	$pFrom		= isset( $_GET['pFrom'] ) ? trim($_GET['pFrom']) : FALSE;
	$pTo			= isset( $_GET['pTo'] ) ? trim($_GET['pTo']) : FALSE;
	$tRank		= isset( $_GET['tRange'] ) ? $_GET['tRange'] : 1;
	$from			= isset( $_GET['from'] ) && $tRank == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to			= isset( $_GET['to'] ) && $tRank == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	$pQuery		= $pdRange == 0 ? "" : "product_code >= '".$pFrom."' AND product_code <= '".$pTo."' AND ";
	$pTitle		= $pdRange == 1 ? $pFrom . ' - ' . $pTo : 'ทั้งหมด';
	$tTitle			= thaiDate($from, '/') . ' - ' . thaiDate($to, '/');
	
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
	
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าค้างรับแยกตามรุ่นสินค้า');
	$excel->getActiveSheet()->mergeCells('A1:F1');
	$excel->getActiveSheet()->setCellValue('A2', 'สินค้า :');
	$excel->getActiveSheet()->setCellValue('B2', $pTitle);
	$excel->getActiveSheet()->mergeCells('B2:F2');
	$excel->getActiveSheet()->setCellValue('A3', 'วันที่ :');
	$excel->getActiveSheet()->setCellValue('B3', $tTitle);
	$excel->getActiveSheet()->mergeCells('B3:F3');
	
	$excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B4', 'รหัสสินค้า');
	$excel->getActiveSheet()->setCellValue('C4', 'ชื่อสินค้า');
	$excel->getActiveSheet()->setCellValue('D4', 'สั่งซื้อ');
	$excel->getActiveSheet()->setCellValue('E4', 'รับแล้ว');
	$excel->getActiveSheet()->setCellValue('F4', 'ค้างรับ');
	
	//-------------  Set Column Width
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	
	$excel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical('center');
		
	$qr = "SELECT tbl_po_detail.id_product, product_code, product_name, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE ".$pQuery;
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' GROUP BY tbl_po_detail.id_product";
	
	$qs = dbQuery($qr);
	$row = 5; ///---- Start with row 5th
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		while( $rs = dbFetchObject($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_name);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->qty);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->received);
			$excel->getActiveSheet()->setCellValue('F'.$row, '=D'.$row.'-E'.$row);
			
			//echo $row . ' | ' .$rs->product_code . ' | ' .$rs->product_name . ' | ' . $rs->qty . ' | ' . $rs->received .'<br/>';
			$row++;
			$no++;
		}
		
		$last = $row > 5 ? $row - 1 : $row;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D5:D'.$last.')');
		$excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E5:E'.$last.')');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$last.')');
		$excel->getActiveSheet()->getStyle('A5:A'.$last)->getAlignment()->setHorizontal('center');
		$excel->getActiveSheet()->getStyle('D5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('A4:F'.$row)->getBorders()->getAllBorders()->setBorderStyle('thin');
		
	}	
	
	setToken($_GET['token']);
	$file_name = "รายงานสินค้าค้างรับแยกตามสินค้า.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output'); 
	
?>
	