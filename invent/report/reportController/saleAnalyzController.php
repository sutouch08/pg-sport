<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";


if( isset( $_GET['soldProductDeepAnalyz'] ) )
{
	ini_set('memory_limit', '1024M');
	$role	= $_GET['role'];
	$role_in = $role == 0 ? '1,5' : $role;
	$roleName = $role == 1 ? 'ขายปกติ' : ( $role == 5 ? 'ฝากขาย' : 'ทั้งหมด' );
	$from = fromDate($_GET['from']);
	$to	= toDate($_GET['to']);
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
							 ->setCategory("Sale Report");
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานวิเคราะห์ขายแบบละเอียด');
	
	//---------  หัวตาราง  ------------//
	$excel->getActiveSheet()->setCellValue('A1', 'sold_date');
	$excel->getActiveSheet()->setCellValue('B1', 'product');
	$excel->getActiveSheet()->setCellValue('C1', 'color');
	$excel->getActiveSheet()->setCellValue('D1', 'size');
	$excel->getActiveSheet()->setCellValue('E1', 'attribute');
	$excel->getActiveSheet()->setCellValue('F1', 'category');
	$excel->getActiveSheet()->setCellValue('G1', 'cost_ex');
	$excel->getActiveSheet()->setCellValue('H1', 'cost_inc');
	$excel->getActiveSheet()->setCellValue('I1', 'price_ex');
	$excel->getActiveSheet()->setCellValue('J1', 'price_inc');
	$excel->getActiveSheet()->setCellValue('K1', 'sell_ex');
	$excel->getActiveSheet()->setCellValue('L1', 'sell_inc');
	$excel->getActiveSheet()->setCellValue('M1', 'qty');
	$excel->getActiveSheet()->setCellValue('N1', 'discount');
	$excel->getActiveSheet()->setCellValue('O1', 'amount_ex');
	$excel->getActiveSheet()->setCellValue('P1', 'amount_inc');
	$excel->getActiveSheet()->setCellValue('Q1', 'type');
	$excel->getActiveSheet()->setCellValue('R1', 'customer');
	$excel->getActiveSheet()->setCellValue('S1', 'saleman');
	$excel->getActiveSheet()->setCellValue('T1', 'area');
	$excel->getActiveSheet()->setCellValue('U1', 'group');
	$excel->getActiveSheet()->setCellValue('V1', 'emp');
	$excel->getActiveSheet()->setCellValue('W1', 'total_cost_ex');
	$excel->getActiveSheet()->setCellValue('X1', 'total_cost_inc');
	$excel->getActiveSheet()->setCellValue('Y1', 'margin_ex');
	$excel->getActiveSheet()->setCellValue('Z1', 'margin_inc');
	
	
	$qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role_in.") AND date_upd > '".$from."' AND date_upd < '".$to."' ORDER BY id_product ASC");
	
	if( dbNumRows($qs) > 0 )
	{
		$row	= 2;  //------ เริ่มต้นแถวที่ 2
		while( $rs = dbFetchArray($qs) )
		{
			set_time_limit(150);
			$pa	= getProductAttribute($rs['id_product_attribute']);  //------  return as array $pa['id_color'], $pa['id_size'], $pa['id_attribute']
			$con	= $rs['id_role'] == 5 ? 'ฝากขาย' : getPaymentText($rs['id_order']);
			$y		= date('Y', strtotime($rs['date_upd']) );
			$m		= date('m', strtotime($rs['date_upd']) );
			$d		= date('d', strtotime($rs['date_upd']) );
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			$excel->getActiveSheet()->setCellValue('A'.$row, $date);  //----- วันที่
			$excel->getActiveSheet()->setCellValue('B'.$row, get_product_code($rs['id_product'])); //------ รุ่นสินค้า
			$excel->getActiveSheet()->setCellValue('C'.$row, get_color_code($pa['id_color']) ); //-----  สี
			$excel->getActiveSheet()->setCellValue('D'.$row, get_size_name($pa['id_size']) ); //------- Size
			$excel->getActiveSheet()->setCellValue('E'.$row, get_attribute_name($pa['id_attribute']) ); //----- คุณลักษระอื่นๆ
			$excel->getActiveSheet()->setCellValue('F'.$row, getDefaultCategoryName($rs['id_product']) ); //----- กลุ่มสินค้า
			$excel->getActiveSheet()->setCellValue('G'.$row, $rs['cost'] ); //-----  ทุนไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('H'.$row, addVAT($rs['cost'], $vat) ); //----- ทุนรวม VAT
			$excel->getActiveSheet()->setCellValue('I'.$row, removeVAT($rs['product_price'], $vat) ); //----- ราคาป้าย ไม่ราม VAT
			$excel->getActiveSheet()->setCellValue('J'.$row, $rs['product_price'] ); //----- ราคาป้าย
			$excel->getActiveSheet()->setCellValue('K'.$row, removeVAT($rs['final_price'], $vat) ); //----- ขายไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('L'.$row, $rs['final_price'] ); //-----  ขาย
			$excel->getActiveSheet()->setCellValue('M'.$row, $rs['sold_qty'] ); //----- จำนวนขาย
			$excel->getActiveSheet()->setCellValue('N'.$row, $rs['discount_amount'] ); //----- ส่วนลดรวม
			$excel->getActiveSheet()->setCellValue('O'.$row, removeVAT($rs['total_amount'], $vat) ); //----- มูลค่าขายไม่รวม VAT
			$excel->getActiveSheet()->setCellValue('P'.$row, $rs['total_amount'] ); //----- มูลค่าขาย
			$excel->getActiveSheet()->setCellValue('Q'.$row, $con); //---- ช่องทางการขาย
			$excel->getActiveSheet()->setCellValue('R'.$row, customer_name($rs['id_customer']) ); //--- ร้านค้า
			$excel->getActiveSheet()->setCellValue('S'.$row, sale_name($rs['id_sale']) ); //--- พนักงานขาย
			$excel->getActiveSheet()->setCellValue('T'.$row, customerDefaultGroupName($rs['id_customer']) ); //---- เขตการขาย
			$excel->getActiveSheet()->setCellValue('U'.$row, getProductGroupName($rs['id_product']));
			$excel->getActiveSheet()->setCellValue('V'.$row, employee_name($rs['id_employee'])); //---- พนักงานผู้ทำรายการ
			$excel->getActiveSheet()->setCellValue('W'.$row, $rs['total_cost']); //---- ต้นทุนรวม (ไม่รวม VAT)
			$excel->getActiveSheet()->setCellValue('X'.$row, addVAT($rs['total_cost'], $vat)); //---- ต้นทุนรวม (รวม VAT)
			$excel->getActiveSheet()->setCellValue('Y'.$row, removeVAT($rs['total_amount'], $vat) - $rs['total_cost'] ); //---- กำไรขั้นต้น (ไม่รวม VAT)
			$excel->getActiveSheet()->setCellValue('Z'.$row, $rs['total_amount'] - addVAT($rs['total_cost'], $vat) ); //---- กำไรขั้นต้น (รวม VAT )
			
			$row++;			
			
		}//----- end while 		
		
		$excel->getActiveSheet()->getStyle('A2:A'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
		$excel->getActiveSheet()->getStyle('G2:L'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('M2:M'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('N2:P'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('W2:Z'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
	}

	
	setToken($_GET['token']);
	$file_name = "รายงานวิเคราะห์ขายแบบละเอียด".$pred.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');	
	
}


if( isset( $_GET['sale_by_attribute'] ) && isset( $_GET['report'] ) )
{
	$code 	= $_POST['product_code'];
	$from		= fromDate($_POST['from_date']);
	$to		= toDate($_POST['to_date']);
	$option  	= $_POST['option'];
	$id_pro 	= get_id_product_by_product_code($code);
	$rs 		= sold_attribute_grid($id_pro, $from, $to, $option);
	echo $rs;
}

if( isset( $_GET['sale_by_attribute'] ) && isset( $_GET['export'] ) )
{
	$code 	= $_GET['product_code'];
	$from		= fromDate($_GET['from_date']);
	$to		= toDate($_GET['to_date']);
	$option  	= $_GET['option'];
	$id_product	= get_id_product_by_product_code($code);
	$title		= 'รายงานจำนวนขาย '.$code.' วันที่ '.thaiDate($from, "/")." ถึง ".thaiDate($to, "/");
	$excel 	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle($code);
	$excel->getActiveSheet()->setCellValue('A1', $title);
	
		
	/////////////////  ตรวจสอบ คุณลักษณะ //////////////
	$q_color = dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_color AS id, color_code AS code  FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 ");
	$q_size 	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_size AS id, size_name AS code FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0");
	$q_attribute	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_attribute AS id, attribute_name AS code FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0");
	$color 	= "SELECT tbl_product_attribute.id_color AS id FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC ";
	$size 	= "SELECT tbl_product_attribute.id_size AS id FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0 GROUP BY tbl_product_attribute.id_size  ORDER BY position ASC ";
	$attribute	= "SELECT tbl_product_attribute.id_attribute AS id FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute  ORDER BY position ASC ";
	$rc 			= dbNumRows($q_color);
	$rs			= dbNumRows($q_size);
	$ra			= dbNumRows($q_attribute);
	$c_count		= "";
	if($rc >0){ $c_count .= "1"; }else{ $c_count .= "0"; } /// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	if($rs >0){ $c_count .= "1"; }else{ $c_count .= "0"; } ///// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	if($ra >0){ $c_count .= "1"; }else{ $c_count .= "0"; } ///// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	if($c_count == "100" || $c_count == "010" || $c_count == "001") /// กรณีมี คุณลักษณะเดียว
	{
		if($rc > 0){ $data = $q_color; }else if($rs > 0 ){ $data = $q_size; }else{ $data = $q_attribute; }
		$row = 2;  /// เริ่มที่ A2;
		while($rd = dbFetchArray($data))
		{
			$qty = sum_sold_qty_by_id_product_attribute($rd['id_product_attribute'], $from, $to, $option);
			$excel->getActiveSheet()->setCellValue('A'.$row, $rd['code']);
			$excel->getActiveSheet()->setCellValue('B'.$row, $qty);
			$row++;
		}// end while
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->setCellValue('B'.$row, '=SUM(B2:B'.($row-1).')');
		$excel->getActiveSheet()->getStyle('A'.$row.':B'.$row)->getFont()->setSize(12)->setBold(true);
		$excel->getActiveSheet()->getStyle('B2:B'.($row))->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('A2:B'.($row))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}
	else if($c_count == "110" || $c_count == "101" || $c_count == "011") /// กรณีมี 2 คุณลักษณะ
	{
		if($c_count == "110"){ $colors = $color; $sizes = $size; }else if($c_count == "101"){ $colors = $color; $attributes = $attribute; }else if($c_count == "011"){ $attributes = $attribute; $sizes = $size; }
		if(isset($colors) && isset($sizes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$col = 1; /// 1 = col B
			$cc = 0;
			while($co_head = dbFetchArray($co) ) /////////   สร้างหัวตารางตามสีก่อน
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, $co_head['color_code']." ".color_name($co_head['id_color']));
				$col++; $cc++;
			}
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, "รวม");
			$si = dbQuery($sizes);
			$row = 3; $col = 0; // start at A3
			while($rd = dbFetchArray($si) )
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, get_size_name($rd['id']));
				$col++;
				$co = dbQuery($colors);
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color = ".$rc['id']." AND id_size = ".$rd['id']);
					if( dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
					}
					else
					{
						$qty = 0;
					}
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $qty);
					$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode('#,##0');
					$col++;
				}//end while
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "=SUM(B".$row.":".PHPExcel_Cell::stringFromColumnIndex($col-1).$row.")");
				$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
				$col = 0; $row++;
			}//end while
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "รวม");
			$col++;
			while($col <= $cc)
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM('.PHPExcel_Cell::stringFromColumnIndex($col).'3:'.PHPExcel_Cell::stringFromColumnIndex($col).($row-1).')');
				$col++;
			}
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM(B3:'.PHPExcel_Cell::stringFromColumnIndex($col-1).($row-1).')');
			$excel->getActiveSheet()->getStyle('A'.$row.':'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row)->getFont()->setBold(true);
			$range ='A2:'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row;
			$excel->getActiveSheet()->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$col = 1;
			$cc = 0;
			while($co_head = dbFetchArray($co) )
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, $co_head['color_code']." ".color_name($co_head['id_color']));
				$col++; $cc++;
			}			
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, "รวม");
			$si = dbQuery($attributes);
			$row = 3; $col = 0; // start at A3
			while($rd = dbFetchArray($si) )
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, get_attribute_name($rd['id']));
				$col++;
				$co = dbQuery($colors);
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color = ".$rc['id']." AND id_attribute = ".$rd['id']);
					if(dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_attribute =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
					}
					else
					{
						$qty = 0;
					}
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $qty);
					$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode('#,##0');
					$col++;
				}//end while
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "=SUM(B".$row.":".PHPExcel_Cell::stringFromColumnIndex($col-1).$row.")");
				$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
				$col = 0; $row++;
			}//end while
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "รวม");
			$col++;
			while($col <= $cc)
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM('.PHPExcel_Cell::stringFromColumnIndex($col).'3:'.PHPExcel_Cell::stringFromColumnIndex($col).($row-1).')');
				$col++;
			}
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM(B3:'.PHPExcel_Cell::stringFromColumnIndex($col-1).($row-1).')');
			$excel->getActiveSheet()->getStyle('A'.$row.':'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row)->getFont()->setBold(true);
			$range ='A2:'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row;
			$excel->getActiveSheet()->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}else if(isset($attributes) && isset($sizes) ){
			$co = dbQuery("SELECT attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
			$col = 1;
			$cc = 0;
			while($co_head = dbFetchArray($co) )
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, $co_head['attribute_name']);
				$col++; $cc++;
			}	
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 2, "รวม");		
			$si = dbQuery($sizes);
			$row = 3; $col = 0;
			while($rd = dbFetchArray($si) )
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, get_size_name($rd['id']));
				$col++;
				$co = dbQuery($attributes);
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_size = ".$rd['id']." AND id_attribute = ".$rc['id']);
					if(dbNumRows($qx) == 1)
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']."  ORDER BY position ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);	
					}
					else
					{
						$qty = 0;
					}
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $qty);
					$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode('#,##0');
					$col++;
				}//end while
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "=SUM(B".$row.":".PHPExcel_Cell::stringFromColumnIndex($col-1).$row.")");
				$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
				$col = 0; $row++;
			}//end while
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "รวม");
			$col++;
			while($col <= $cc)
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM('.PHPExcel_Cell::stringFromColumnIndex($col).'3:'.PHPExcel_Cell::stringFromColumnIndex($col).($row-1).')');
				$col++;
			}
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM(B3:'.PHPExcel_Cell::stringFromColumnIndex($col-1).($row-1).')');
			$excel->getActiveSheet()->getStyle('A'.$row.':'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row)->getFont()->setBold(true);
			$range ='A2:'.PHPExcel_Cell::stringFromColumnIndex($cc+1).$row;
			$excel->getActiveSheet()->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}
	}
	else if($c_count == "111") /// กรณีมี 3 คุณลักษณะ
	{
		$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
		$a_col = 1; 
		$cc = dbNumRows(dbQuery("SELECT id_color FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color != 0 GROUP BY id_color"));
		while($cs = dbFetchArray($co) ) ///Attribute วน สร้างที่ละช่อง     w1
		{
			$excel->getActiveSheet()->setCellValueByColumnAndRow($a_col-1, 2, $cs['attribute_name']);
			$excel->getActiveSheet()->mergeCells(PHPExcel_Cell::stringFromColumnIndex($a_col-1).'2:'.PHPExcel_Cell::stringFromColumnIndex($a_col+$cc).'2');
			$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($a_col-1).'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$qr = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			///// color header /////
			$col = $a_col;
			while($co_head = dbFetchArray($qr) ) /// w2
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 3, $co_head['color_code']." ".color_name($co_head['id_color']));
				$col++;
			} // while 2
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, 3, "รวม");
			
			$row = 4; $col = $a_col -1;
			$sizes = $size;
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) ) /// w3
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, get_size_name($rd['id']));
				$col++;
				$colors = $color;
				$qs = dbQuery($colors);
				while($rc = dbFetchArray($qs) )// w4
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND id_color = ".$rc['id']." AND id_attribute = ".$cs['id_attribute']);
					if(dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
					}
					else
					{
						$qty = 0;
					}
					$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $qty);
					$col++;
				}//end while 4
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "=SUM(".PHPExcel_Cell::stringFromColumnIndex($a_col).$row.":".PHPExcel_Cell::stringFromColumnIndex($col-1).$row.")");
				$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
				$col = $a_col-1; $row++;
			}//end while 3 
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "รวม");
			$col++;
			while($col <= ($a_col-1 +$cc))
			{
				$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM('.PHPExcel_Cell::stringFromColumnIndex($col).'4:'.PHPExcel_Cell::stringFromColumnIndex($col).($row-1).')');
				$col++;
			}
			$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, '=SUM('.PHPExcel_Cell::stringFromColumnIndex($a_col).'4:'.PHPExcel_Cell::stringFromColumnIndex($col-1).($row-1).')');
			$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($a_col).$row.':'.PHPExcel_Cell::stringFromColumnIndex($a_col+$cc).$row)->getFont()->setBold(true);
			$range = PHPExcel_Cell::stringFromColumnIndex($a_col-1).'2:'.PHPExcel_Cell::stringFromColumnIndex($a_col+$cc).$row;
			$excel->getActiveSheet()->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			//$excel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($a_col-1).'2:'.PHPExcel_Cell::stringFromColumnIndex(($a_col-1)+$cc).($row - 1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$a_col += $cc + 3;
			
		}// end while 1
		
	} /// จบ เงื่อนไขนับคุณลักษณะ
	
	/*echo "<pre>";
	print_r($excel);
	echo "</pre>";*/
	
	
	setToken($_GET['token']);
	$file_name = "attribute_analyz.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	
}

function sold_attribute_grid($id_product, $from, $to, $option)
{
	$result = "";
	/////*******************  ตรวจสอบว่าสินค้ามีกี่คุณลักษณะ *****************/////
	$q_color 	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_color AS id, color_code AS code  FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 ");
	$q_size 		= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_size AS id, size_name AS code FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0");
	$q_attribute	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_attribute AS id, attribute_name AS code FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0");
	$color 	= "SELECT tbl_product_attribute.id_color AS id FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC ";
	$size 	= "SELECT tbl_product_attribute.id_size AS id FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0 GROUP BY tbl_product_attribute.id_size  ORDER BY position ASC ";
	$attribute	= "SELECT tbl_product_attribute.id_attribute AS id FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute  ORDER BY position ASC ";
	$rc 			= dbNumRows($q_color);
	$rs			= dbNumRows($q_size);
	$ra			= dbNumRows($q_attribute);
	$c_count		= "";
	if($rc >0){ $c_count .= "1"; }else{ $c_count .= "0"; } ///// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	if($rs >0){ $c_count .= "1"; }else{ $c_count .= "0"; } ///// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	if($ra >0){ $c_count .= "1"; }else{ $c_count .= "0"; } ///// ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข /////
	
	if($c_count == "100" || $c_count == "010" || $c_count == "001") /// กรณีมี คุณลักษณะเดียว
	{
		if($rc > 0){ $data = $q_color; }else if($rs > 0 ){ $data = $q_size; }else{ $data = $q_attribute; }
		$result .= "<table class='table table-bordered' style='width:500px;'>";
		$i = 0;
		$total_qty = 0;
		while($rd = dbFetchArray($data))
		{
			$result .= "<tr>";
			$qty = sum_sold_qty_by_id_product_attribute($rd['id_product_attribute'], $from, $to, $option);
			$result .="<td style='vertical-align:middle;'>".$rd['code']."</td><td style='width:100px; padding-right:10px; vertical-align:middle; text-align:center;'>".number_format($qty)."</td>";
			$total_qty += $qty;
			$i++;
			$result .= "</tr>"; 
		}// end while
		$result .= "<tr>";
		$result .= "<td style='vertical-align:middle;'><center><strong>รวม</strong></center></td><td style='width:100px; padding-right:10px; vertical-align:middle; text-align:center;'>".number_format($total_qty)."</td>";
		$result .= "</tr>";
		$result .= "</table>";
		
	}
	else if($c_count == "110" || $c_count == "101" || $c_count == "011") /// กรณีมี 2 คุณลักษณะ
	{
		if($c_count == "110"){ $colors = $color; $sizes = $size; }else if($c_count == "101"){ $colors = $color; $attributes = $attribute; }else if($c_count == "011"){ $attributes = $attribute; $sizes = $size; }
		$result .="<table class='table table-bordered'>";
		if(isset($colors) && isset($sizes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			$total_c = array();
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
				$total_c[$co_head['id_color']] = 0;
			}
			$result .= "<td align='center' style='vertical-align:middle'><strong>รวม</td>";
			$result .= "</tr>";
			$si = dbQuery($sizes);
			
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$co = dbQuery($colors);
				$total_s = 0;
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color = ".$rc['id']." AND id_size = ".$rd['id']);
					if( dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
						$c_index = $rc['id'];
						$total_c[$c_index] += $qty;
						$total_s += $qty;
					}
					else
					{
						$qty = 0;
					}
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center>".number_format($qty)."</center></td>";
				}//end while
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_s)."</strong></center></td>";
				$result .= "</tr>";
			}//end while
			$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>รวม</strong></td>";
			$all_qty = 0;
			foreach($total_c as $total_c_qty)
			{
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_c_qty)."</strong></center></td>";
				$all_qty += $total_c_qty;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($all_qty)."</strong></center></td></tr>";
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			$total_c = array();
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
				$total_c[$co_head['id_color']] = 0;
			}
			$result .= "<td align='center' style='vertical-align:middle'><strong>รวม</strong></td>";
			$result .= "</tr>";
			$si = dbQuery($attributes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_attribute_name($rd['id'])."</strong></td>";
				$co = dbQuery($colors);
				$total_a = 0;
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color = ".$rc['id']." AND id_attribute = ".$rd['id']);
					if(dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_attribute =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
						$total_c[$rc['id']] += $qty; $total_a += $qty;
					}
					else
					{
						$qty = 0;
					}
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center>".number_format($qty)."</center></td>";
				}//end while
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_a)."</strong></center></td>";
				$result .= "</tr>";
			}//end while
			$result .= "<tr><td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>รวม</strong></center></td>";
			$all_qty = 0;
			foreach($total_c as $total_c_qty)
			{
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_c_qty)."</strong></center></td>";	
				$all_qty += $total_c_qty;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($all_qty)."</strong></center></td>";
			$result .= "</tr>";
			
		}else if(isset($attributes) && isset($sizes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			$total_a = array();
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['attribute_name']."</strong></td>";
				$total_a[$co_head['id_attribute']] = 0;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>รวม</strong></center></td>";
			$result .= "</tr>";
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$co = dbQuery($attributes);
				$total_s = 0;
				while($rc = dbFetchArray($co) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_attribute = ".$rc['id']." AND id_size = ".$rd['id']);
					if(dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']."  ORDER BY position ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);	
						$total_a[$rc['id']] += $qty;  $total_s += $qty;	
					}
					else
					{
						$qty = 0;
					}
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center>".number_format($qty)."</center></td>";
				}//end while
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_s)."</strong></center></td>";
				$result .= "</tr>";
			}//end while
			$result .= "<tr><td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>รวม</strong></center></td>";
			$all_qty = 0;
			foreach($total_a as $total_a_qty)
			{
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_a_qty)."</strong></center></td>";	
				$all_qty += $total_a_qty;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($all_qty)."</strong></center></td>";
			$result .= "</tr>";
		}
	}
	else if($c_count == "111") /// กรณีมี 3 คุณลักษณะ
	{
		$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
		$result .= "<ul class='nav nav-tabs'>";
		$n = 1;
		while($cs = dbFetchArray($co) )
		{
			$result .= "<li role='presentation' class='"; if($n == 1){ $result .= "active"; } $result .="'><a href='#".$cs['id_attribute']."' aria-controls='".$cs['id_attribute']."' role='tab' data-toggle='tab'>".$cs['attribute_name']."</a></li>";
			$n++;
		}//end while
		$result .= "</ul>";
		$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
		$result .= "<div class='tab-content'>";
		$n = 1;
		while($cs = dbFetchArray($co) )
		{
			$result .= "<div role='tabpanel' class='tab-pane "; if($n == 1){ $result .= "active"; } $result .= "' id='".$cs['id_attribute']."'>";
			$qr = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<table class='table table-bordered'>";
			$result .= "<tr><td>&nbsp;</td>";
			$total_c = array();
			while($co_head = dbFetchArray($qr) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
				$total_c[$co_head['id_color']] = 0;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>รวม</strong></center></td>";
			$result .= "</tr>";
			$sizes = $size;
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$colors = $color;
				$qs = dbQuery($colors);
				$total_s = 0;
				while($rc = dbFetchArray($qs) )
				{
					$qx = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_product." AND id_color = ".$rc['id']." AND id_size = ".$rd['id']." AND id_attribute = ".$cs['id_attribute']);
					if(dbNumRows($qx) == 1 )
					{
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
						$qty = sum_sold_qty_by_id_product_attribute($id_product_attribute, $from, $to, $option);
						$total_c[$rc['id']] += $qty;  $total_s += $qty;
					}
					else
					{
						$qty = 0;
					}
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center>".number_format($qty)."</center></td>";
				}//end while
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_s)."</strong></center></td>";
				$result .= "</tr>";
			}//end while
			$result .= "<tr><td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>รวม</strong></center></td>";
			$all_qty = 0;
			foreach($total_c as $total_c_qty)
			{
				$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($total_c_qty)."</strong></center></td>";
				$all_qty += $total_c_qty;
			}
			$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><center><strong>".number_format($all_qty)."</strong></center></td>";
			$result .= "</tr>";
			$result .= "</table>";
			
			$result .= "</div>";
			$n++;
		}// end while
		
		$result .= "</div><! ---- end tab-content ----->";
		
	} /// จบ เงื่อนไขนับคุณลักษณะ
	return $result;
}// end function



?>