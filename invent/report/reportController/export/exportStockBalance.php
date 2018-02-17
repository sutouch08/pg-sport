<?php
	$pdOption 	= $_GET['pdOption'];
	$whOption	= $_GET['whOption'];
	$dateOption	= $_GET['dateOption'];
	$pdFrom		= $pdOption == 1 ? $_GET['pdFrom'] : FALSE;
	$pdTo		= $pdOption == 1 ? $_GET['pdTo'] : FALSE;
	$wh			= $whOption == 1 ? $_GET['wh'] : FALSE;
	$date 		= $dateOption == 1 ? dbDate($_GET['date']) : date('Y-m-d');
	$whList		= $whOption == 1 ? warehouseIn($wh) : warehouseIn($wh, TRUE);
	
	$pdQuery 	= $pdOption == 1 ? "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' " : "";
	$whQuery	= $whOption == 1 && $whList !== FALSE ? "AND id_warehouse IN(".$whList.") " : '';
	
	if( $pdOption == 0 )
	{
		$qr = "SELECT id_product_attribute, barcode, reference, product_name, cost FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	else
	{
		$qr = "SELECT id_product_attribute, barcode, reference, product_name, cost FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= "WHERE tbl_product_attribute.id_product != 0 ";
		$qr .= $pdQuery;
		$qr .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	
	$qs = dbQuery($qr);
	
	//-------  
	$excel = new PHPExcel();
	$excel->getProperties()->setCreator("Samart Invent 1.0");
	$excel->getProperties()->setLastModifiedBy("Samart Invent 1.0");
	$excel->getProperties()->setTitle("Report stock balance");
	$excel->getProperties()->setSubject("Report stock balance");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Smart Invent 1.0");
	$excel->getProperties()->setCategory("Stock Report");
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานยอดสินค้าคงเหลือ');
	
	//------- Report name Row 1
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าคงเหลือ ณ วันที่ ' . thaiDate($date, '/'));
	$excel->getActiveSheet()->mergeCells('A1:G1');
	
	//-------- Report Conditions Row 2
	$excel->getActiveSheet()->setCellValue('A2', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('B2', $pdOption == 0 ? 'ทั้งหมด' : $pdFrom.' - '.$pdTo);
	$excel->getActiveSheet()->setCellValue('C2', 'คลังสินค้า');
	$excel->getActiveSheet()->setCellValue('D2', warehouseNameList($whList));
	
	//--------- Report Table header
	$excel->getActiveSheet()->setCellValue('A3', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B3', 'บาร์โค้ด');
	$excel->getActiveSheet()->setCellValue('C3', 'รหัสสินค้า');
	$excel->getActiveSheet()->setCellValue('D3', 'ชื่อสินค้า');
	$excel->getActiveSheet()->setCellValue('E3', 'ทุน');
	$excel->getActiveSheet()->setCellValue('F3', 'คงเหลือ');
	$excel->getActiveSheet()->setCellValue('G3', 'มูลค่า');
	
	//-------------  Set Column Width
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('E')->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	
	$excel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal('center');
	
	$row = 4;  //---- Start at row4
	//echo dbNumRows($qs);
	if( dbNumRows($qs) > 0 )
	{
		$today	= date('Y-m-d');
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		$product = new product();
		$time = dbNumRows($qs)/100;
		$time = $time < 30 ? 30 : ceil($time);
		set_time_limit($time);
		while( $rs = dbFetchObject($qs) )
		{
			$currentQty 	= $whOption == 0 ? $product->all_available_qty($rs->id_product_attribute) : $product->stock_qty_by_warehouse($rs->id_product_attribute, $whList);
			$moveQty 	= $product->move_qty_by_warehouse($rs->id_product_attribute, $whList);
			$cancleQty	= $product->cancle_qty_by_warehouse($rs->id_product_attribute, $whList);
			$bufferQty 	= $product->buffer_qty_by_warehouse($rs->id_product_attribute, $whList);
			$qty = $currentQty + $moveQty + $cancleQty + $bufferQty;
			if( $dateOption == 1 && $date < $today) //---- ถ้าดูย้อนหลัง
			{
				$today = date('Y-m-d H:i:s');
				$viewDate = fromDate(date("Y-m-d", strtotime("+1 day", strtotime($date) ) ) ); //---- วันที่คำนวน transection
				$transectionQty = getTransectionQty($rs->id_product_attribute, $whList, $viewDate, $today);
				$movement = ( $transectionQty['move_in'] - $transectionQty['move_out'] ) * (-1);  //---- (ยอดรับเข้า - ยอดจ่ายออก) * -1 เพื่อนำไปบวกกลับ
				$qty = $qty + $movement;
			}
			if( $qty != 0 )
			{
				$excel->getActiveSheet()->setCellValue('A'.$row, $n);
				$excel->getActiveSheet()->setCellValue('B'.$row, $rs->barcode);
				$excel->getActiveSheet()->setCellValue('C'.$row, $rs->reference);
				$excel->getActiveSheet()->setCellValue('D'.$row, $rs->product_name);
				$excel->getActiveSheet()->setCellValue('E'.$row, $rs->cost);
				$excel->getActiveSheet()->setCellValue('F'.$row, $qty);
				$excel->getActiveSheet()->setCellValue('G'.$row, '=E'.$row.'*F'.$row);
				$n++;	
				$row++;
			}
		}//--- end while
		
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F4:F'.($row-1).')');
		$excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G4:G'.($row-1).')');
		$excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal('right');	
		$excel->getActiveSheet()->getStyle('B4:B'.($row-1))->getNumberFormat()->setFormatCode('0');
		$excel->getActiveSheet()->getStyle('E4:E'.($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('F4:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('G4:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}
	
	setToken($_GET['token']);
	$file_name = "Stock Balance.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
?>