<?php
	ini_set('memory_limit', '256M');
	$disc = is_numeric($_GET['discount']) == FALSE ? 0 : $_GET['discount'];
	$vat = getConfig('VAT');
	$qr = "SELECT tbl_stock.id_product_attribute, barcode, reference, product_code, product_name, default_category_id, id_product_group, cost, price, id_color, id_size, id_attribute, SUM( qty ) AS qty FROM tbl_stock ";
	$qr .= "JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	$qr .= "GROUP BY tbl_stock.id_product_attribute ";
	$qr .= "ORDER BY tbl_product_attribute.id_product ASC";
	
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
	$excel->getActiveSheet()->setTitle('รายงานสินค้าคงเหลือ');
	
	//--------- Report Table header
	$excel->getActiveSheet()->setCellValue('A1', 'date');
	$excel->getActiveSheet()->setCellValue('B1', 'barcode');
	$excel->getActiveSheet()->setCellValue('C1', 'product_code');
	$excel->getActiveSheet()->setCellValue('D1', 'style');	
	$excel->getActiveSheet()->setCellValue('E1', 'product_name');
	$excel->getActiveSheet()->setCellValue('F1', 'color');
	$excel->getActiveSheet()->setCellValue('G1', 'size');
	$excel->getActiveSheet()->setCellValue('H1', 'attribute');
	$excel->getActiveSheet()->setCellValue('I1', 'category');
	$excel->getActiveSheet()->setCellValue('J1', 'group');
	$excel->getActiveSheet()->setCellValue('K1', 'cost_ex');
	$excel->getActiveSheet()->setCellValue('L1', 'cost_inc');
	$excel->getActiveSheet()->setCellValue('M1', 'price_ex');
	$excel->getActiveSheet()->setCellValue('N1', 'price_inc');
	$excel->getActiveSheet()->setCellValue('O1', 'qty');
	$excel->getActiveSheet()->setCellValue('P1', 'cost_amount_ex');
	$excel->getActiveSheet()->setCellValue('Q1', 'cost_amount_inc');
	$excel->getActiveSheet()->setCellValue('R1', 'price_amount_ex');
	$excel->getActiveSheet()->setCellValue('S1', 'price_amount_inc');
	$excel->getActiveSheet()->setCellValue('T1', 'discount');
	$excel->getActiveSheet()->setCellValue('U1', 'sale_after_discount_ex');
	$excel->getActiveSheet()->setCellValue('V1', 'sale_after_discount_inc');
	$excel->getActiveSheet()->setCellValue('W1', 'margin_ex');
	$excel->getActiveSheet()->setCellValue('X1', 'margin_inc');
		
	$row = 2;  //---- Start at row4
	
	if( dbNumRows($qs) > 0 )
	{
		$today	= date('d-m-Y');
		$n = 1;
		while( $rs = dbFetchObject($qs) )
		{
			set_time_limit(150);
			$cost_ex	= $rs->cost;
			$cost_inc 	= addVAT($rs->cost, $vat);
			$price_ex 	= removeVAT($rs->price, $vat);
			$price_inc	= $rs->price;
			$cost_amount_ex	= $rs->qty * $cost_ex;
			$cost_amount_inc	= $rs->qty * $cost_inc;
			$price_amount_ex	= $rs->qty * $price_ex;
			$price_amount_inc	= $rs->qty * $price_inc;
			$discount	= $price_amount_inc * ($disc * 0.01);
			$sale_after_discount_ex = removeVAT( ($price_amount_inc - $discount), $vat);
			$sale_after_discount_inc = $price_amount_inc - $discount;
			$margin_ex	= $sale_after_discount_ex - $cost_amount_ex;
			$margin_inc	= $sale_after_discount_inc - $cost_amount_inc;
			
			$excel->getActiveSheet()->setCellValue('A'.$row, $today);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->barcode);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->reference);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->product_code);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_name);
			$excel->getActiveSheet()->setCellValue('F'.$row, get_color_code($rs->id_color));
			$excel->getActiveSheet()->setCellValue('G'.$row, get_size_name($rs->id_size));
			$excel->getActiveSheet()->setCellValue('H'.$row, get_attribute_name($rs->id_attribute));
			$excel->getActiveSheet()->setCellValue('I'.$row, get_category_name($rs->default_category_id));
			$excel->getActiveSheet()->setCellValue('J'.$row, productGroupName($rs->id_product_group));
			$excel->getActiveSheet()->setCellValue('K'.$row, $cost_ex); //---- cost_ex
			$excel->getActiveSheet()->setCellValue('L'.$row, $cost_inc); //---- cost_inc
			$excel->getActiveSheet()->setCellValue('M'.$row, $price_ex); //----- price_ex
			$excel->getActiveSheet()->setCellValue('N'.$row, $price_inc); //----- price_inc
			$excel->getActiveSheet()->setCellValue('O'.$row, $rs->qty);  //----- qty
			$excel->getActiveSheet()->setCellValue('P'.$row, $cost_amount_ex);  //----- cost_amount_ex
			$excel->getActiveSheet()->setCellValue('Q'.$row, $cost_amount_inc);  //----- cost_amount_inc
			$excel->getActiveSheet()->setCellValue('R'.$row, $price_amount_ex);  //----- price_amount_ex
			$excel->getActiveSheet()->setCellValue('S'.$row, $price_amount_inc);  //----- price_amount_inc
			$excel->getActiveSheet()->setCellValue('T'.$row, $discount);  //----- discount
			$excel->getActiveSheet()->setCellValue('U'.$row, $sale_after_discount_ex);  //----- sale_after_discount_ex
			$excel->getActiveSheet()->setCellValue('V'.$row, $sale_after_discount_inc);  //----- sale_after_discount_inc
			$excel->getActiveSheet()->setCellValue('W'.$row, $margin_ex);  //----- margin_ex
			$excel->getActiveSheet()->setCellValue('X'.$row, $margin_inc);  //----- margin_inc
			$row++;
		}
		$excel->getActiveSheet()->getStyle('A2:A'.$row)->getNumberFormat()->setFormatCode('dd/mm/yy');
		$excel->getActiveSheet()->getStyle('B2:B'.$row)->getNumberFormat()->setFormatCode('0');
		$excel->getActiveSheet()->getStyle('K2:N'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('O2:O'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('P2:X'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}
	//echo '<pre>'; print_r($excel); echo '</pre>';
	setToken($_GET['token']);
	$file_name = "รายงานสินค้าคงเหลือแบบละเอียด ".date('dmY').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	
?>