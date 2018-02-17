<?php
	$p_rank 	= $_GET['po'];
	$s_rank 	= $_GET['sup'];
	$t_rank	= $_GET['rank'];
	
	//---  PO Query
	$pQuery	= $p_rank == 2 ? "AND po_reference = '" . trim($_GET['reference']) . "' " : "";
	
	//---- Supplier Query
	$sQuery	= $s_rank == 2 ? "AND id_supplier = " . $_GET['id_sup'] . " " : "";
	
	//---- Time Query
	$from		= $t_rank == 2 ? fromDate($_GET['from_date']) : date('Y-01-01 00:00:00');
	$to		= $t_rank == 2 ? toDate($_GET['to_date']) : date('Y-12-31 23:59:59');
	
	//---- Title
	$topTitle	= 'รายงานการรับสินค้าจากการขายแสดงรายการสินค้า ใบสั่งซื้อ และ ผู้ขาย';
	$poTitle	= $p_rank == 2 ? trim($_GET['reference']) : 'ทั้งหมด';
	$supTitle	= $s_rank == 2 ? supplier_name($_GET['id_sup']) : 'ทั้งหมด';
	$preTitle	= thaiDate($from,'/') .' - ' . thaiDate($to, '/'); 
	
	//$pGroup	= getConfig('ITEMS_GROUP');
	$excel	= new PHPExcel();

	$excel->getProperties()->setCreator("Samart Invent");
	$excel->getProperties()->setLastModifiedBy(date('Y-m-d H:i:s'));
	$excel->getProperties()->setTitle("Report Received Product With PO");
	$excel->getProperties()->setSubject("Report Received Product With PO");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Samart Invent");
	$excel->getProperties()->setCategory("Stock Report");
	
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานการรับสินค้าเข้า');
	
	$excel->getActiveSheet()->setCellValue('A1', $topTitle);
	$excel->getActiveSheet()->mergeCells('A1:J1');
	$excel->getActiveSheet()->setCellValue('A2', 'ใบสั่งซื้อ');
	$excel->getActiveSheet()->setCellValue('B2', $poTitle);
	$excel->getActiveSheet()->setCellValue('A3', 'ผู้ขาย');
	$excel->getActiveSheet()->setCellValue('B3', $supTitle);
	$excel->getActiveSheet()->setCellValue('A4', 'วันที่');
	$excel->getActiveSheet()->setCellValue('B4', $preTitle);

	//---------  หัวตาราง  ------------//
	$excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B6', 'วันที่');
	$excel->getActiveSheet()->setCellValue('C6', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('D6', 'เลขที่เอกสาร');
	$excel->getActiveSheet()->setCellValue('E6', 'ใบสั่งซื้อ');
	$excel->getActiveSheet()->setCellValue('F6', 'ใบส่งสินค้า');
	$excel->getActiveSheet()->setCellValue('G6', 'ผู้ขาย');
	$excel->getActiveSheet()->setCellValue('H6', 'ราคา');
	$excel->getActiveSheet()->setCellValue('I6', 'จำนวน');
	$excel->getActiveSheet()->setCellValue('J6', 'มูลค่า');
	

	$qr = "SELECT tbl_receive_product.date_add AS date_add, tbl_receive_product_detail.id_product_attribute AS id_pa, tbl_product_attribute.reference AS item, ";
	$qr .= "tbl_receive_product.reference AS reference, tbl_receive_product.invoice, tbl_po.id_po, po_reference AS po, tbl_po.id_supplier, SUM( tbl_receive_product_detail.qty ) AS qty ";
	$qr .= "FROM tbl_receive_product_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_receive_product_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qr .= "JOIN tbl_po ON tbl_receive_product.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_receive_product.date_add >= '".$from."' AND tbl_receive_product.date_add <= '".$to."' AND tbl_receive_product_detail.status = 1 ";
	$qr .= $pQuery . $sQuery;
	$qr .= "GROUP BY tbl_receive_product_detail.id_product_attribute, tbl_receive_product.reference";
	$qs = dbQuery($qr);
	
	if( dbNumRows($qs) > 0 )
	{
		$row	= 7;  //------ เริ่มต้นแถวที่ 7
		$no	= 1;
		while( $rs = dbFetchObject($qs) )
		{			
			$y		= date('Y', strtotime($rs->date_add) );
			$m		= date('m', strtotime($rs->date_add) );
			$d		= date('d', strtotime($rs->date_add) );
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);	//--- ลำดับ
			$excel->getActiveSheet()->setCellValue('B'.$row, $date);  //----- วันที่
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->item); //------ สินค้า
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference ); //-----  เลขที่เอกสารรับสินค้าเข้า
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->po); //------- ใบสั่งซื้อ
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs->invoice); //----- ใบส่งสินค้า
			$excel->getActiveSheet()->setCellValue('G'.$row, supplier_name($rs->id_supplier) ); //----- ชื่อผู้ขาย
			$excel->getActiveSheet()->setCellValue('H'.$row, getPoPriceItem($rs->id_po, $rs->id_pa) ); //----- ราคาตามใบสั่งซื้อ
			$excel->getActiveSheet()->setCellValue('I'.$row, $rs->qty); //----- จำนวนที่รับ
			$excel->getActiveSheet()->setCellValue('J'.$row, '=H'.$row.'*I'.$row );  //----- มูลค่า
			
			$no++;
			$row++;

		}//----- end while
		
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':H'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I7:I'.($row-1).')');
		$excel->getActiveSheet()->setCellValue('J'.$row, '=SUM(J7:J'.($row-1).')');
		$excel->getActiveSheet()->getStyle('B7:B'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$excel->getActiveSheet()->getStyle('H7:H'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('I7:I'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('J7:J'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	}
	
	//echo '<pre>'; print_r($excel); echo '</pre>';
	setToken($_GET['token']);
	
	$file_name = "รายงานการรับสินค้าเข้า .xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output'); 
	

?>
