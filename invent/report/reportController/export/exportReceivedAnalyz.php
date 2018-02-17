<?php
	$from = fromDate($_GET['from']);
	$to	= toDate($_GET['to']);
	$disc	= is_numeric($_GET['discount']) === FALSE ? 0 : $_GET['discount'] * 0.01; //---- if discount = 40  will be retur  0.4
	$vat	= getConfig('VAT'); //---- 7
	$pred = date("dmY", strtotime($from)) .' - '. date("dmY", strtotime($to));
	//$pGroup	= getConfig('ITEMS_GROUP');
	$excel	= new PHPExcel();

	$excel->getProperties()->setCreator("Samart Invent")
							 ->setLastModifiedBy("Samart Invent")
							 ->setTitle("Report Sold Deep Analyz")
							 ->setSubject("Report Sold Deep Analyz")
							 ->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8")
							 ->setKeywords("Samart Invent")
							 ->setCategory("Stock Report");
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานการรับสินค้าเข้า');

	//---------  หัวตาราง  ------------//
	$excel->getActiveSheet()->setCellValue('A1', 'date');
	$excel->getActiveSheet()->setCellValue('B1', 'product');
	$excel->getActiveSheet()->setCellValue('C1', 'color');
	$excel->getActiveSheet()->setCellValue('D1', 'size');
	$excel->getActiveSheet()->setCellValue('E1', 'attribute');
	$excel->getActiveSheet()->setCellValue('F1', 'category');
	$excel->getActiveSheet()->setCellValue('G1', 'group');
	$excel->getActiveSheet()->setCellValue('H1', 'PO');
	$excel->getActiveSheet()->setCellValue('I1', 'supplier');
	$excel->getActiveSheet()->setCellValue('J1', 'qty');
	$excel->getActiveSheet()->setCellValue('K1', 'cost_ex');
	$excel->getActiveSheet()->setCellValue('L1', 'cost_inc');
	$excel->getActiveSheet()->setCellValue('M1', 'price_ex');
	$excel->getActiveSheet()->setCellValue('N1', 'price_inc');
	$excel->getActiveSheet()->setCellValue('O1', 'discount_amount');
	$excel->getActiveSheet()->setCellValue('P1', 'cost_amount_ex');
	$excel->getActiveSheet()->setCellValue('Q1', 'cost_amount_inc');
	$excel->getActiveSheet()->setCellValue('R1', 'price_amount_ex');
	$excel->getActiveSheet()->setCellValue('S1', 'price_amount_inc');
	$excel->getActiveSheet()->setCellValue('T1', 'sell_amount_ex');
	$excel->getActiveSheet()->setCellValue('U1', 'sell_amount_inc');
	$excel->getActiveSheet()->setCellValue('V1', 'margin_ex');
	$excel->getActiveSheet()->setCellValue('W1', 'margin_inc');

	$qr = "SELECT id_po, po_reference, product_code, default_category_id AS id_category, id_product_group, tbl_receive_product_detail.id_product_attribute, ";
	$qr .= "id_color, id_size, id_attribute, price, SUM( qty ) AS qty, tbl_receive_product.date_add ";
	$qr .= "FROM tbl_receive_product_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_receive_product_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qr .= "WHERE tbl_receive_product.date_add >= '".$from."' AND tbl_receive_product.date_add <= '".$to."' AND tbl_receive_product_detail.status = 1 ";
	$qr .= "GROUP BY tbl_receive_product_detail.id_product_attribute, tbl_receive_product.reference";
	
	//echo $qr;
	
	$qs = dbQuery($qr);
	
	if( dbNumRows($qs) > 0 )
	{
		$row	= 2;  //------ เริ่มต้นแถวที่ 2
		while( $rs = dbFetchObject($qs) )
		{
			$y		= date('Y', strtotime($rs->date_add) );
			$m		= date('m', strtotime($rs->date_add) );
			$d		= date('d', strtotime($rs->date_add) );
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			
			$cost						= getPoPriceItem($rs->id_po, $rs->id_product_attribute);
			$cost_ex				= $cost;
			$cost_inc 				= addVAT($cost, $vat);
			$price_ex 				= removeVAT($rs->price, $vat);
			$price_inc				= $rs->price;
			$cost_amount_ex		= $rs->qty * $cost_ex;
			$cost_amount_inc		= $rs->qty * $cost_inc;
			$price_amount_ex		= $rs->qty * $price_ex;
			$price_amount_inc		= $rs->qty * $price_inc;
			$discount				= $price_amount_inc * $disc;
			$sell_amount_ex 		= removeVAT( ($price_amount_inc - $discount), $vat);
			$sell_amount_inc 		= $price_amount_inc - $discount;
			$margin_ex				= $sell_amount_ex - $cost_amount_ex;
			$margin_inc				= $sell_amount_inc - $cost_amount_inc;
			
			$excel->getActiveSheet()->setCellValue('A'.$row, $date);  //----- วันที่
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code); //------ รุ่นสินค้า
			$excel->getActiveSheet()->setCellValue('C'.$row, get_color_code($rs->id_color) ); //-----  สี
			$excel->getActiveSheet()->setCellValue('D'.$row, get_size_name($rs->id_size) ); //------- Size
			$excel->getActiveSheet()->setCellValue('E'.$row, get_attribute_name($rs->id_attribute) ); //----- คุณลักษระอื่นๆ
			$excel->getActiveSheet()->setCellValue('F'.$row, get_category_name($rs->id_category) ); //----- หมวดหมู่สินค้า
			$excel->getActiveSheet()->setCellValue('G'.$row, productGroupName($rs->id_product_group) ); //----- กลุ่มสินค้า
			$excel->getActiveSheet()->setCellValue('H'.$row, $rs->po_reference); //----- PO No.
			$excel->getActiveSheet()->setCellValue('I'.$row, getSupplierNameByPO($rs->id_po) );  //----- Supplier
			$excel->getActiveSheet()->setCellValue('J'.$row, $rs->qty);		//----- Qty
			$excel->getActiveSheet()->setCellValue('K'.$row, $cost_ex ); //-----  ทุนไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('L'.$row, $cost_inc ); //----- ทุนรวม VAT
			$excel->getActiveSheet()->setCellValue('M'.$row, $price_ex ); //----- ราคาป้าย ไม่ราม VAT
			$excel->getActiveSheet()->setCellValue('N'.$row, $price_inc ); //----- ราคาป้าย รวม VAT
			$excel->getActiveSheet()->setCellValue('O'.$row, $discount ); //----- ส่วนลดรวม
			$excel->getActiveSheet()->setCellValue('P'.$row, $cost_amount_ex ); //----- มูลค่าทุน ไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('Q'.$row, $cost_amount_inc ); //----- มูลค่าทุนรวม VAT
			$excel->getActiveSheet()->setCellValue('R'.$row, $price_amount_ex ); //----- มูลค่าป้าย ไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('S'.$row, $price_amount_inc ); //----- มูลค่าป้ายรวม VAT
			$excel->getActiveSheet()->setCellValue('T'.$row, $sell_amount_ex ); //----- มูลค่าขายไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('U'.$row, $sell_amount_inc ); //----- มูลค่าขายรวม VAT
			$excel->getActiveSheet()->setCellValue('V'.$row, $margin_ex ); //----- กำไรขั้นต้นไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('W'.$row, $margin_inc ); //----- กำไรขั้นต้นรวม VAT

			$row++;

		}//----- end while

		$excel->getActiveSheet()->getStyle('A2:A'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$excel->getActiveSheet()->getStyle('J2:J'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('K2:W'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

	}

	//echo '<pre>'; print_r($excel); echo '</pre>';
	
	setToken($_GET['token']);
	$file_name = "รายงานการรับสินค้าเข้าแบบละเอียด ".$pred.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	

?>
