<?php
//-------- Export 	รายงานสินค้าคงเหลือแยกตามโซน -------------//
	$p_rank 		= $_GET['product_rank'];
	$zone_rank	= $_GET['zone_rank'];
	$id_wh		= $_GET['wh'];
	
	$excel = new PHPExcel();
	$excel->getProperties()->setCreator("Samart Invent 1.0");
	$excel->getProperties()->setLastModifiedBy("Samart Invent 1.0");
	$excel->getProperties()->setTitle("Report PO Backlog By Product");
	$excel->getProperties()->setSubject("Report PO Backlog By Product");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Smart Invent 1.0");
	$excel->getProperties()->setCategory("Stock Report");	
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('Stock By Zone');
	
	
	$qs	= "SELECT s.id_product_attribute, z.zone_name, pa.barcode, pa.reference, pa.cost, s.qty ";
	$qs 	.= "FROM tbl_stock AS s JOIN tbl_zone AS z ON s.id_zone = z.id_zone ";
	$qs 	.= "JOIN tbl_product_attribute AS pa ON s.id_product_attribute = pa.id_product_attribute ";
	$qs 	.= "JOIN tbl_product AS pd ON pa.id_product = pd.id_product ";
	
	if( $p_rank == 1 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "ORDER BY pa.id_product ASC";
		$p = "ทั้งหมด";  $w = "ทั้งหมด"; $z = "ทั้งหมด";
	}
	else if( $p_rank == 1 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE z.id_warehouse = ".$id_wh." ORDER BY pa.id_product ASC";
		$p = "ทั้งหมด";  $w = get_warehouse_name_by_id($id_wh); $z = "ทั้งหมด";
	}
	else if( $p_rank == 2 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE 	pd.product_code >= '".$_GET['from']."' AND pd.product_code <= '".$_GET['to']."' ORDER BY pa.id_product ASC";
		$p = "จาก ".$_GET['from']."  ถึง ".$_GET['to'];  $w = "ทั้งหมด"; $z = "ทั้งหมด";
	}
	else if( $p_rank == 2 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE z.id_warehouse = ".$id_wh." AND	(pd.product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."') ORDER BY pa.id_product ASC";
		$p = "จาก ".$_GET['from']."  ถึง ".$_GET['to']; $w = get_warehouse_name_by_id($id_wh); $z = "ทั้งหมด"; 
	}
	else if( $p_rank == 1 && $zone_rank == 2 )
	{
		$qs .= "WHERE z.zone_name = '".$_GET['zone_name']."' ORDER BY pa.id_product ASC";
		$p = "ทั้งหมด"; if($id_wh == 0 ){ $w = "ทั้งหมด"; }else{ $w = get_warehouse_name_by_id($id_wh); }  $z = $_GET['zone_name'];
	}
	else if( $p_rank == 2 &&$zone_rank == 2 )
	{
		$qs .= "WHERE 	z.zone_name = '".$_GET['zone_name']."' AND (pd.product_code BETWEEN '".$_GET['from']."' AND '".$_GET['to']."') ORDER BY pa.id_product ASC";
		$p = "จาก ".$_GET['from']."  ถึง ".$_GET['to'];  if($id_wh == 0 ){ $w = "ทั้งหมด"; }else{ $w = get_warehouse_name_by_id($id_wh); }  $z = $_GET['zone_name'];
	}
	
	
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าคงเหลือ แยกตามโซน');
	$excel->getActiveSheet()->mergeCells('A1:G1');
	
	$excel->getActiveSheet()->setCellValue('A2', 'คลัง :');
	$excel->getActiveSheet()->setCellValue('B2', $w);
	$excel->getActiveSheet()->mergeCells('B2:G2');
	
	$excel->getActiveSheet()->setCellValue('A3', 'โซน :');
	$excel->getActiveSheet()->setCellValue('B3', $z);
	$excel->getActiveSheet()->mergeCells('B3:G3');
	
	$excel->getActiveSheet()->setCellValue('A4', 'สินค้า :');
	$excel->getActiveSheet()->setCellValue('B4', $p);
	$excel->getActiveSheet()->mergeCells('B4:G4');
	
	$excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B6', 'โซน');
	$excel->getActiveSheet()->setCellValue('C6', 'บาร์โค้ด');
	$excel->getActiveSheet()->setCellValue('D6', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('E6', 'ทุน');
	$excel->getActiveSheet()->setCellValue('F6', 'คงเหลือ');
	$excel->getActiveSheet()->setCellValue('G6', 'มูลค่า');

	$qs = dbQuery($qs);
	
	if(dbNumRows($qs) > 0 )
	{
		$row = 7;
		$no = 1;
		while($rs = dbFetchObject($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->zone_name);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->barcode);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->cost);
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs->qty);
			$excel->getActiveSheet()->setCellValue('G'.$row, '=E'.$row.'*F'.$row);
			$row++;
			$no++;
		}
		$re = $row -1;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F7:F'.$re.')');
		$excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G7:G'.$re.')');
		
		$excel->getActiveSheet()->getStyle('E7:E'.$re)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('F7:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('G7:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}
	
	setToken($_GET['token']);
	$file_name = "Stock Zone Report - ".date('Ymd').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');

?>