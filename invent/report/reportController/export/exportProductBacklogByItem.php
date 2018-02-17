<?php 
	$pdRange	= $_GET['pdRange'];
	$pFrom		= $_GET['pFrom'];
	$pTo			= $_GET['pTo'];
	$tRange		= $_GET['tRange'];
	$from			= $tRange == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to			= $tRange == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	$pQuery		= $pdRange == 0 ? "" : "product_code >= '".$pFrom."' AND product_code <= '".$pTo."' AND ";
	
	$title			= 'รายงานสรุปสินค้าค้างรับแยกตามรายการสินค้า';
	$pTitle		= $pdRange == 1 ? $pFrom . ' - ' .$pTo : 'ทั้งหมด';
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
	
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าค้างรับแยกตามรายการสินค้า');
	$excel->getActiveSheet()->mergeCells('A1:G1');
	$excel->getActiveSheet()->setCellValue('A2', 'สินค้า :');
	$excel->getActiveSheet()->setCellValue('B2', $pTitle);
	$excel->getActiveSheet()->mergeCells('B2:G2');
	$excel->getActiveSheet()->setCellValue('A3', 'วันที่ :');
	$excel->getActiveSheet()->setCellValue('B3', $tTitle);
	$excel->getActiveSheet()->mergeCells('B3:G3');
	
	$excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B4', 'รหัสสินค้า');
	$excel->getActiveSheet()->setCellValue('C4', 'รุ่นสินค้า');
	$excel->getActiveSheet()->setCellValue('D4', 'ชื่อสินค้า');
	$excel->getActiveSheet()->setCellValue('E4', 'สั่งซื้อ');
	$excel->getActiveSheet()->setCellValue('F4', 'รับแล้ว');
	$excel->getActiveSheet()->setCellValue('G4', 'ค้างรับ');
	
	//-------------  Set Column Width
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	
	$excel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical('center');
	
	$qr = "SELECT tbl_product_attribute.reference AS item_code, product_code, product_name, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_po_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "LEFT JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qr .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE ".$pQuery;
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' GROUP BY tbl_po_detail.id_product_attribute ";
	$qr .= "ORDER BY product_code ASC, tbl_color.position ASC, tbl_size.position ASC, tbl_attribute.position ASC";
	
	$qs = dbQuery($qr);
	$row = 5; ///---- Start with row 5th
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		while( $rs = dbFetchObject($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->item_code);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_code);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->product_name);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->qty);
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs->received);
			$excel->getActiveSheet()->setCellValue('G'.$row, '=E'.$row.'-F'.$row);
			
			//echo $row . ' | ' .$rs->product_code . ' | ' .$rs->product_name . ' | ' . $rs->qty . ' | ' . $rs->received .'<br/>';
			$row++;
			$no++;
		}
		
		$last = $row > 5 ? $row - 1 : $row;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E5:E'.$last.')');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$last.')');
		$excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$last.')');
		$excel->getActiveSheet()->getStyle('A5:A'.$last)->getAlignment()->setHorizontal('center');
		$excel->getActiveSheet()->getStyle('E5:G'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('A4:G'.$row)->getBorders()->getAllBorders()->setBorderStyle('thin');
		
	}	
	
	setToken($_GET['token']);
	$file_name = "รายงานสินค้าค้างรับแยกตามรายการสินค้า.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output'); 
	
?>
	