<?php
	$wh			= $_GET['id_wh'];
	$from 		= fromDate($_GET['from_date'], true);
	$to 			= toDate($_GET['to_date'], true);
	$reorder		= reorder($_GET['p_from'], $_GET['p_to']);
	$p_from  	= $reorder['from'];
	$p_to			= $reorder['to'];
	
	$bf_date 	= date('Y-m-d', strtotime("-1day $from"));
	$wh_name 	= $wh == 0 ? "รวมทุกคลัง" : get_warehouse_name_by_id($wh);
	
	$excel = new PHPExcel();
	$excel->getProperties()->setCreator("Samart Invent 1.0");
	$excel->getProperties()->setLastModifiedBy("Samart Invent 1.0");
	$excel->getProperties()->setTitle("Report PO Backlog By Product");
	$excel->getProperties()->setSubject("Report PO Backlog By Product");
	$excel->getProperties()->setDescription("This file was generate by Smart invent web application via PHPExcel v.1.8");
	$excel->getProperties()->setKeywords("Smart Invent 1.0");
	$excel->getProperties()->setCategory("Stock Report");	
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('Stock Card');
	
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานความเคลื่อนไหวสินค้า');
	$excel->getActiveSheet()->mergeCells('A1:F1');
	
	$excel->getActiveSheet()->setCellValue('A2', 'คลัง :');
	$excel->getActiveSheet()->setCellValue('B2', $wh_name);
	$excel->getActiveSheet()->mergeCells('B2:F2');
	
	$excel->getActiveSheet()->setCellValue('A3', 'สินค้า :');
	$excel->getActiveSheet()->setCellValue('B3', $p_from.' - '.$p_to);
	$excel->getActiveSheet()->mergeCells('B3:F3');
	
	$qs 			= dbQuery("SELECT id_product_attribute, id_product, reference, barcode FROM tbl_product_attribute WHERE reference BETWEEN '".$p_from."' AND '".$p_to."' ORDER BY reference ASC");
	
	$row 	= 5;
	
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$pd 	= new product();
			$balance		= fifoBeforeBalance($rs->id_product_attribute, $wh, $from);
			$header 	 	= $rs->reference .' | '. get_product_name($rs->id_product) .' | '. $rs->barcode;
			$excel->getActiveSheet()->setCellValue('A'.$row, $header);
			$excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
			$row++;
			$excel->getActiveSheet()->setCellValue('A'.$row, 'วันที่');
			$excel->getActiveSheet()->setCellValue('B'.$row, 'เลขที่เอกสาร');
			$excel->getActiveSheet()->setCellValue('C'.$row, 'คลังสินค้า');
			$excel->getActiveSheet()->setCellValue('D'.$row, 'เข้า');
			$excel->getActiveSheet()->setCellValue('E'.$row, 'ออก');
			$excel->getActiveSheet()->setCellValue('F'.$row, 'คงเหลือ');
			$excel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal('center');
			$row++;
			
			$excel->getActiveSheet()->setCellValue('A'.$row, thaiDate($bf_date));
			$excel->getActiveSheet()->setCellValue('B'.$row, 'ยอดยกมา');
			$excel->getActiveSheet()->setCellValue('F'.$row, $balance);
			$row++;
			
			if( $wh != 0 )
			{
				//---- กรณีระบุคลัง
				$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh ";
				$sql .= "FROM tbl_stock_movement ";
				$sql .= "WHERE id_product_attribute =".$rs->id_product_attribute." AND id_warehouse = ".$wh." AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
				$sql .= "GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
			}
			else //-- if wh != 0
			{
				//----- กรณีไม่ระบุคลัง
				$sql = "SELECT date_upd, SUM(move_in) AS move_in, SUM(move_out) AS move_out, reference, id_warehouse AS wh ";
				$sql .= "FROM tbl_stock_movement ";
				$sql .= "WHERE id_product_attribute =".$rs->id_product_attribute." AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
				$sql .= "GROUP BY reference, id_warehouse ORDER BY date_upd ASC";
			}//--- end wh != 0
			
			$qr = dbQuery($sql);
			if( dbNumRows($qr) > 0 )
			{
				while( $rd = dbFetchObject($qr) )
				{
					$balance = $balance + $rd->move_in - $rd->move_out;
					$excel->getActiveSheet()->setCellValue('A'.$row, thaiDate($rd->date_upd));
					$excel->getActiveSheet()->setCellValue('B'.$row, $rd->reference);
					$excel->getActiveSheet()->setCellValue('C'.$row, get_warehouse_name_by_id($rd->wh));
					$excel->getActiveSheet()->setCellValue('D'.$row, $rd->move_in);
					$excel->getActiveSheet()->setCellValue('E'.$row, $rd->move_out);
					$excel->getActiveSheet()->setCellValue('F'.$row, $balance);
					$excel->getActiveSheet()->getStyle('D'.$row.':F'.$row)->getNumberFormat()->setFormatCode('#,##0');
					
					$row++;
					
				}//---- endwhile $rd
				$row++;
			}//--- end if dbNumRows($qr)
			
			$row++;
		}//---- End while
	}//-- end if
	
	setToken($_GET['token']);
	$file_name = "Stock Card.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
		
