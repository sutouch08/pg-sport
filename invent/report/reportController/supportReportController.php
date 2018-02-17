<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";
require "../../function/support_helper.php";
require "../../function/report_helper.php";

function get_remark($id_order){
	$remark = "";
	$sql = dbQuery("SELECT comment FROM tbl_order WHERE id_order = ".$id_order);
	$row = dbNumRows($sql);
	if($row == 1){
		$rs = dbFetchArray($sql);
		$remark .= $rs['comment'];
	}
	return $remark;
}
if(isset($_GET['support_by_employee']) && isset($_GET['view']) )
{
	$view  		= $_GET['view'];
	$rank	 		= $_GET['rank'];
	$em_rank 	= $_GET['employee_rank'];
	if($em_rank == 1 ){ $id_employee = $_GET['employee_id']; $qr = "id_employee = ".$id_employee; $em_title = employee_name($id_employee); }else{ $id_employee = ""; $qr = "id_employee != ''";  $em_title = "ทั้งหมด"; }
	if($rank == 1 ){ $from_date = dbDate($_GET['from_date'])." 00:00:00"; $to_date = dbDate($_GET['to_date'])." 23:59:59"; }else{ $from_date = date("Y-01-01 00:00:00"); $to_date = date("Y-12-31 23:59:59"); }
	$data = array();
	if($view == 0 ) /// แยกตามเลขที่เอกสาร
	{
		$sql = dbQuery("SELECT id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference");
		
		if( dbNumRows($sql) >0 )
		{	        
			$total_qty = 0;
			$total_amount = 0;         
			while($rs = dbFetchArray($sql) )
			{
				$arr = array(
					"id_order"=>$rs['id_order'],
					"date_upd"=>thaiDate($rs['date_upd']),
					"employee"=>employee_name($rs['id_employee']),
					"customer"=>customer_name($rs['id_customer']),
					"reference"=>$rs['reference'],
					"qty"=>number_format($rs['qty']),
					"amount"=>number_format($rs['amount'], 2),
					"remark"=>get_remark($rs['id_order'])
					);
				array_push($data, $arr);
				$total_qty 		+= $rs['qty'];
				$total_amount 	+= $rs['amount'];
			}
			$arr = array("total_qty"=>number_format($total_qty), "total_amount"=>number_format($total_amount, 2));
			array_push($data, $arr);			
		}else{
			$arr = array("nocontent"=>"------- ไม่มีรายการตามเงื่อนไขที่กำหนด  -------");
			array_push($data, $arr);
		}
				
	}else{  //// แยกตามรายการสินค้า id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd
		$sql = dbQuery("SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC");		
			if(dbNumRows($sql) > 0 )
			{
				$total_qty 		= 0;
				$total_amount 	= 0;
				while($rs = dbFetchArray($sql) )
				{
					$product = new product();
					$img = $product->get_product_attribute_image($rs['id_product_attribute'], 1);
					$arr = array(
						"img"=>$img,
						"reference"=>$rs['product_reference'],
						"product_name"=>$rs['product_name'],
						"qty"=>number_format($rs['qty']),
						"amount"=>number_format($rs['amount'], 2)
					);
					array_push($data, $arr);
					$total_qty 		+= $rs['qty'];
					$total_amount	+= $rs['amount'];
				}
				$arr = array("total_qty"=>number_format($total_qty), "total_amount"=>number_format($total_amount, 2));
				array_push($data, $arr);
			}else{
				$arr = array("nocontent"=>"----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------"); 
				array_push($data, $arr);
			}				
	}	
	echo json_encode($data);
}

if(isset($_GET['export_support_by_employee']) && isset($_GET['view']) )
{
	$view  		= $_GET['view'];
	$rank	 		= $_GET['rank'];
	$em_rank 	= $_GET['employee_rank'];
	if($em_rank == 1 ){ $id_employee = $_GET['employee_id']; $qr = "id_employee = ".$id_employee; $em_title = employee_name($id_employee); }else{ $id_employee = ""; $qr = "id_employee != ''";  $em_title = "ทั้งหมด"; }
	if($rank == 1 ){ $from_date = dbDate($_GET['from_date'])." 00:00:00"; $to_date = dbDate($_GET['to_date'])." 23:59:59"; }else{ $from_date = date("Y-01-01 00:00:00"); $to_date = date("Y-12-31 23:59:59"); }
	$excel 	= array();
	if($view == 0 ) /// แยกตามเลขที่เอกสาร
	{
		$sql = dbQuery("SELECT id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference");
		
		$title 		= array("รายงานยอดเบิกอภินันท์ ของพนักงาน : $em_title แยกตามเลขที่เอกสาร วันที่ ".thaiDate($from_date)." ถึง ".thaiDate($to_date));		
		$header 	= array("วันที่", "ผู้เบิก(พนักงาน)", "ผู้รับ", "เลขที่เอกสาร", "จำนวน", "มูลค่า", "หมายเหตุ");
		array_push($excel, $title);
		array_push($excel, $header);
		if( dbNumRows($sql) >0 )
		{	 
			$total_qty 		= 0;
			$total_amount 	= 0;
			while($rs = dbFetchArray($sql) )
			{
				$arr = array(thaiDate($rs['date_upd']), employee_name($rs['id_employee']), customer_name($rs['id_customer']), $rs['reference'], $rs['qty'], $rs['amount'], get_remark($rs['id_order']));
				array_push($excel, $arr);
				$total_qty		+= $rs['qty'];
				$total_amount	+= $rs['amount'];
			}
			$arr = array("", "", "", "รวม", $total_qty, $total_amount, "");
			array_push($excel, $arr);			
		}else{
			$arr = array("----------- ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
			array_push($excel, $arr);	
		}
		
	}else{  //// แยกตามรายการสินค้า id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd
		$sql = dbQuery("SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC");
		$title 		= array("รายการสินค้าอภินันท์ ของพนักงาน : ".$em_title."  ตั้งแต่  ".thaiDate($from_date)." ถึง  ".thaiDate($to_date)." แยกตามรายการสินค้า");
		$header 	= array("รหัส", "สินค้า", "จำนวน", "มูลค่า");
		array_push($excel, $title);
		array_push($excel, $header);
			if(dbNumRows($sql) > 0 )
			{
				$total_qty 		= 0;
				$total_amount 	= 0;
				while($rs = dbFetchArray($sql) )
				{
					$arr = array($rs['product_reference'], $rs['product_name'], $rs['qty'], $rs['amount']);
					array_push($excel, $arr);
					$total_qty 		+= $rs['qty'];
					$total_amount	+= $rs['amount'];
				}
				$arr = array("", "รวม", $total_qty, $total_amount);
				array_push($excel, $arr);
				
			}else{
				$arr = array("----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
				array_push($excel, $arr);
			}				
	}	
	$Excel = new Excel_XML();
	$Excel->setEncoding("UTF-8");
	$Excel->setWorksheetTitle('Support by Employee');
	$Excel->addArray($excel);
	$Excel->generateXML('Support_by_Employee');
}

if(isset($_GET['get_support_detail']) && isset($_GET['id_order']) )
{
	$id_order = $_GET['id_order'];
	$data = array();
	$qs = dbQuery("SELECT id_product_attribute, product_reference, product_name, sold_qty, final_price, total_amount FROM tbl_order_detail_sold WHERE id_order = ".$id_order );
	if(dbNumRows($qs) > 0 )
	{		
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$img = get_product_attribute_image($rs['id_product_attribute'], 1);
			$arr = array(
				"img"=>$img, 
				"reference"=>$rs['product_reference'], 
				"product_name"=>$rs['product_name'], 
				"price"=>number_format($rs['final_price']), 
				"qty"=>number_format($rs['sold_qty']), 
				"amount"=>number_format($rs['total_amount'],2)
				);
			array_push($data, $arr);
			$total_qty += $rs['sold_qty'];
			$total_amount += $rs['total_amount'];
		}
		$arr = array("total_qty"=>number_format($total_qty), "total_amount"=>number_format($total_amount, 2));
		array_push($data, $arr);
	}else{
		$arr = array("nocontent"=>"----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
		array_push($data, $arr);
	}
	echo json_encode($data);
}



function action_to_thai($actions)
{
	$action = '';
	switch($actions)
	{
		case 'add' :
		$action = 'เพิ่ม';
		break;
		case 'edit' :
		$action = 'แก้ไข';
		break;
		case 'delete' :
		$action = 'ลบ';
		break;
	}
	return $action;
}

function value($action)
{
	if($action > 0 )
	{
		$action = number_format($action, 2);
	}
	return $action;
}

if( isset($_GET['get_log']) )
{
	$search_text = $_GET['search_text'];
	$action_type = $_GET['action_type'];
	$from_date = $_GET['from_date'] != '' ? dbDate($_GET['from_date']) : '';
	$to_date = $_GET['to_date'] != '' ? dbDate($_GET['to_date']) : '';
	$action = array();
	if($from_date != '' && $to_date != '')
	{
		if($action_type != 'all' )
		{
			$qs = dbQuery("SELECT * FROM tbl_support_log WHERE action_type = '".$action_type."' AND ( date_upd BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59') AND (action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%') ORDER BY date_upd DESC");
		}else{
			$qs = dbQuery("SELECT * FROM tbl_support_log WHERE ( date_upd BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59') AND action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%' ORDER BY date_upd DESC");	
		}
	}else{
		if($action_type != 'all' )
		{
			$qs = dbQuery("SELECT * FROM tbl_support_log WHERE action_type = '".$action_type."' AND (action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%') ORDER BY date_upd DESC");
		}else{
			$qs = dbQuery("SELECT * FROM tbl_support_log WHERE action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%' ORDER BY date_upd DESC");	
		}
	}
	
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		while($rs = dbFetchArray($qs) )
		{
			$arr = array("n"=>$n, "employee_name"=>$rs['employee_name'], "action_type"=>action_to_thai($rs['action_type']), "action"=>$rs['action'], "from_value"=>value($rs['from_value']), "to_value"=>value($rs['to_value']), "date_upd"=>thaiDateTime($rs['date_upd']));
			array_push($action, $arr);
			$n++;			
		}		
	}else{
		$mes = "-------  ไม่มีรายการตามเงื่อนไขที่เลือก  ------";
		$arr = array("nocontent"=>$mes);
		array_push($action, $arr);
	}
	echo json_encode($action);	
}


if( isset($_GET['export_support_summary']) ){
	$emp_rank 		= $_GET['employee_rank'];
	$id_employee 	= $_GET['employee_id'];
	$year 			= $_GET['year'];
	$rank 			= $_GET['rank'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	$start				= date("$year-01-01 00:00:00");
	$end				= date("$year-12-31 23:59:59");
	$from				= $rank == 1 ? date("$year-m-d", strtotime($from_date))." 00:00:00" : $start;
	$to				= $rank == 1 ? date("$year-m-d", strtotime($to_date))." 23:59:59" : $end;
	$qr1				= "SELECT * FROM tbl_support WHERE id_employee = ".$id_employee." AND year = '".$year."'";
	$qr2				= "SELECT * FROM tbl_support WHERE year = '".$year."'";
	$qr				= $emp_rank == 1 ? $qr1 : $qr2;
	$body 			= array();	
	$qs 				=  dbQuery($qr);
	
	$title	= 'รายงานสรุปยอดอภินันท์ แยกตามพนักงาน วันที่ '.thaiDate($from, '/').' - '.thaiDate($to, '/').' งบประมาณปี '. $year;
	
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("สรุปยอดเบิกอภินันท์");
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth(7);
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
	$excel->getActiveSheet()->getRowDimension()->setRowHeight(30);
	
	$excel->getActiveSheet()->mergeCells("A1:F1");
	$excel->getActiveSheet()->setCellValue("A1", $title);
	$excel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal("center");
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setVertical("center");
	
	//////  define table header
	
	$excel->getActiveSheet()->setCellValue("A2", "ลำดับ");
	$excel->getActiveSheet()->setCellValue("B2", "ผู้รับ");
	$excel->getActiveSheet()->setCellValue("C2", "งบประมาณ");
	$excel->getActiveSheet()->setCellValue("D2", "ใช้ไป");
	$excel->getActiveSheet()->setCellValue("E2", "ใช้ไปทั้งหมด");
	$excel->getActiveSheet()->setCellValue("F2", "งบประมาณคงเหลือ");
	$excel->getActiveSheet()->getStyle("A2:F2")->getAlignment()->setHorizontal("center");
	$excel->getActiveSheet()->getStyle("A2:F2")->getAlignment()->setVertical("center"); 
	
	
	if( dbNumRows($qs) >0)
	{
		$n = 1;
		$row = 3;
		while( $rs = dbFetchArray($qs) ){
			$id_employee 		= $rs['id_employee'];
			$id_support 		= get_id_support_by_employee($id_employee);
			$id_budget 			= get_id_budget_by_id_support($id_support);
			$budget 				= get_support_budget($id_budget);			
			$employee_name 	= employee_name($id_employee);	
			$used 				= getSupportUsed($id_employee, $from, $to);
			$allUsed				= getSupportUsed($id_employee, $start, $end);
			$return				= getReturnSupportAmount($id_employee, $from, $to);
			$allReturn 			= getReturnSupportAmount($id_employee, $start, $end);
			$used 				= $used - $return;
			$allUsed				= $allUsed - $allReturn;
			
			$excel->getActiveSheet()->setCellValue("A".$row, $n);											
			$excel->getActiveSheet()->setCellValue("B".$row, $employee_name);		
			$excel->getActiveSheet()->setCellValue("C".$row, $budget);									
			$excel->getActiveSheet()->setCellValue("D".$row, $used);									
			$excel->getActiveSheet()->setCellValue("E".$row, $allUsed);								
			$excel->getActiveSheet()->setCellValue("F".$row, '=C'.$row.' - E'.$row);									
			$n++; $row++;
		}
		//// Summary row
		
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->setCellValue("C".$row, "=SUM(C3:C".($row-1).")");
		$excel->getActiveSheet()->setCellValue("D".$row, "=SUM(D3:D".($row-1).")");
		$excel->getActiveSheet()->setCellValue("E".$row, "=SUM(E3:E".($row-1).")");
		$excel->getActiveSheet()->setCellValue("F".$row, "=SUM(F3:F".($row-1).")");
		$excel->getActiveSheet()->mergeCells("A".$row.":B".$row);
		$excel->getActiveSheet()->getStyle("A3:A".$row)->getAlignment()->setHorizontal("center");
		$excel->getActiveSheet()->getStyle("C3:F".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("A2:F".$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$excel->getActiveSheet()->getStyle("D2:D".$row)->getFont()->getColor()->setRGB("0000FF");
		$excel->getActiveSheet()->getStyle("E2:E".$row)->getFont()->getColor()->setRGB("FF0000");
		$excel->getActiveSheet()->getStyle("F2:F".$row)->getFont()->getColor()->setRGB("008000");			
	}
	setToken($_GET['token']);
	$file_name = "Support_summary.".date('d-m-Y').".xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");	
	
}


if( isset($_GET['support_summary']) && isset( $_GET['rank'] ) ){
	$emp_rank 		= $_GET['employee_rank'];
	$id_employee 	= $_GET['employee_id'];
	$year 			= $_GET['year'];
	$rank 			= $_GET['rank'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	$start				= date("$year-01-01 00:00:00");
	$end				= date("$year-12-31 23:59:59");
	$from				= $rank == 1 ? date("$year-m-d", strtotime($from_date))." 00:00:00" : $start;
	$to				= $rank == 1 ? date("$year-m-d", strtotime($to_date))." 23:59:59" : $end;
	$qr1				= "SELECT * FROM tbl_support WHERE id_employee = ".$id_employee." AND year = '".$year."'";
	$qr2				= "SELECT * FROM tbl_support WHERE year = '".$year."'";
	$qr				= $emp_rank == 1 ? $qr1 : $qr2;
	$body 			= array();	
	$qs 				=  dbQuery($qr);
	if( dbNumRows($qs) >0)
	{
		$total_budget 		= 0;
		$total_amount 		= 0;
		$total_balance 		= 0;
		$total_all_amount 	= 0;
		$n = 1;
		while( $rs = dbFetchArray($qs) ){
			$id_employee 		= $rs['id_employee'];
			$id_support 		= get_id_support_by_employee($id_employee);
			$id_budget 			= get_id_budget_by_id_support($id_support);
			$budget 				= get_support_budget($id_budget);			
			$employee_name 	= employee_name($id_employee);
			
			$used 				= getSupportUsed($id_employee, $from, $to);
			$allUsed				= getSupportUsed($id_employee, $start, $end);
			$return				= getReturnSupportAmount($id_employee, $from, $to);
			$allReturn 			= getReturnSupportAmount($id_employee, $start, $end);
			
			$used 				= $used - $return;
			$allUsed				= $allUsed - $allReturn;
			
			$balance 			= $budget - $allUsed;
			$arr = array(
								"n"				=>$n, 
								"employee"	=>$employee_name, 
								"budget"		=>number_format($budget,2), 
								"used"			=>number_format($used,2), 
								"all_used"		=>number_format($allUsed,2), 
								"balance"		=>number_format($balance,2), 
								"detail_btn"	=>$id_employee
				);
			array_push($body, $arr);
			$total_amount += $used;
			$total_budget += $budget;
			$total_balance += $balance;
			$total_all_amount += $allUsed;
			$n++;
		}
			$arr = array(
							"n"				=>" ",
							"employee"	=>"รวม", 
							"budget"		=>number_format($total_budget,2), 
							"used"			=>number_format($total_amount,2), 
							"all_used"		=>number_format($total_all_amount,2), 
							"balance"		=>number_format($total_balance,2), 
							"detail_btn"	=>" "
				);
			array_push($body, $arr);
	}else{
		$arr = array("nocontent"=>"----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
		array_push($body, $arr);
	}
	
	echo json_encode($body);	
}


if( isset($_GET['get_support_detail']) && isset($_GET['id_employee']) )
{
	$id_employee 	= $_GET['id_employee'];
	$rank 			= $_GET['rank'];
	$year 			= $_GET['year'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	$start				= date("$year-01-01 00:00:00");
	$end				= date("$year-12-31 23:59:59");
	$from				= $rank == 1 ? date("Y-m-d", strtotime($from_date))." 00:00:00" : $start;
	$to				= $rank == 1 ? date("Y-m-d", strtotime($to_date))." 23:59:59" : $end;

	$data = array();		
	
	$sql = dbQuery("SELECT id_order, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 7 AND id_employee = ".$id_employee." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference");
	$total_qty = 0;
	$total_amount = 0;
	if(dbNumRows($sql) > 0){
		while( $rs = dbFetchArray($sql) ){
			$id_order 	= $rs['id_order'];
			$reference 	= $rs['reference'];
			$qty 			= $rs['qty'];
			$amount 		= $rs['amount'];
			$date_upd 	= $rs['date_upd'];
			$arr 			= array(
									"date"			=> thaiDate($date_upd), 
									"reference"	=> $reference, 
									"qty"			=> number_format($qty), 
									"amount"		=> number_format($amount,2), 
									"remark"		=> get_remark($id_order)
								);
			array_push($data, $arr);
			$total_qty 		+= $qty;
			$total_amount 	+= $amount;
		}
		
		$qs 	= getReturnSupportByEmployee($id_employee, $from, $to);
		if( dbNumRows( $qs ) > 0 )
		{
			while( $rs = dbFetchArray($qs) )
			{
				$id 		= $rs['id_return_support'];
				$res 		= getReturnSupportQtyAndAmount($id);
				$qty		= $res['qty'] * -1;
				$amount	= $res['amount'] * -1;	
				$arr 		= array(
									"date"			=> thaiDate($rs['date_add']), 
									"reference"	=> $rs['reference'], 
									"qty"			=> number_format($qty), 
									"amount"		=> number_format($amount,2), 
									"remark"		=> $rs['remark']
								);
				array_push($data, $arr);
				$total_qty += $qty;
				$total_amount += $amount;								
			}
		}
			$arr = array("date"=>"", "reference"=>"", "qty"=>number_format($total_qty), "amount"=>number_format($total_amount,2), "remark"=>"");
			array_push($data, $arr);
	}else{
			$arr = array("nocontent"=>"----------------------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------------------");
			array_push($data, $arr);
	}
	echo json_encode($data);
}

if( isset($_GET['attribute_analyz']) && isset($_POST['product']) )
{
	$p_rank = $_POST['product'];
	$d_rank = $_POST['date'];
	$p_selected = $_POST['id_product'];
	$p_checked = isset($_POST['p_checked']) ? $_POST['p_checked'] : "";
	
	switch( $p_rank ) {
		case 0 :
			$p_q = "id_product != 0";
		break;
		case 1 :
			$in_cuase = "";
			$i = 1;
			$c = count($p_checked);
			foreach($p_checked as $id =>$value) {
				if($value != "") {
					$in_cuase .= $id;
					if($i<$c) { $in_cuase .= ", "; }
				}
				$i++;
			}
			$p_q = "id_product IN(".$in_cuase.")";
		break;
		case 2 :
			$p_q = "id_product = ".$p_selected;
		break;
		}
	switch( $d_rank ) {
		case 0 :
			$from = date("Y-01-01 00:00:00");
			$to 	= date("Y-12-31 23:59:59");
			break;
		case 1 :
			$from = date("Y-m-d 00:00:00", strtotime($_POST['from_date']));
			$to 	= date("Y-m-d 23:59:59", strtotime($_POST['to_date']));
			break;
	}
	$result = array();
	$qr = dbQuery("SELECT id_product_attribute, id_product, reference, id_color, id_size, id_attribute FROM tbl_product_attribute WHERE ".$p_q." ORDER BY id_product ASC");
	$n = 1;
	while($rm = dbFetchArray($qr) )
	{
		$id_product_attribute = $rm['id_product_attribute'];
		$id_product = $rm['id_product'];
		$product_code = get_product_code($id_product);
		$id_color 		= $rm['id_color'];
		$id_size 			= $rm['id_size'];
		$id_attribute 	= $rm['id_attribute'];
		$reference		= $rm['reference'];
		$qs 	= dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
		$rs 	= dbFetchArray($qs); 
		$color 	= get_color_code($id_color);
		$size 	= get_size_name($id_size);
		$attr 		= get_attribute_name($id_attribute);
		$qty 		= $rs['qty'] > 0 ? $rs['qty'] : 0 ;
		$arr = array("n"=>$n, "product_code"=>$product_code, "reference"=>$reference,"color"=>$color, "size"=>$size, "attribute"=>$attr, "qty"=>$qty);
		array_push($result, $arr);		
		$n++;
		}
		echo json_encode($result);
	
}

if( isset($_GET['export_attribute_analyz']) && isset($_POST['product']) )
{
	$p_rank = $_POST['product'];
	$d_rank = $_POST['date'];
	$p_selected = $_POST['id_product'];
	$p_checked = isset($_POST['p_checked']) ? $_POST['p_checked'] : "";
	
	switch( $p_rank ) {
		case 0 :
			$p_q = "id_product != 0";
		break;
		case 1 :
			$in_cuase = "";
			$i = 1;
			$c = count($p_checked);
			foreach($p_checked as $id =>$value) {
				if($value != "") {
					$in_cuase .= $id;
					if($i<$c) { $in_cuase .= ", "; }
				}
				$i++;
			}
			$p_q = "id_product IN(".$in_cuase.")";
		break;
		case 2 :
			$p_q = "id_product = ".$p_selected;
		break;
		}
	switch( $d_rank ) {
		case 0 :
			$from = date("Y-01-01 00:00:00");
			$to 	= date("Y-12-31 23:59:59");
			break;
		case 1 :
			$from = date("Y-m-d 00:00:00", strtotime($_POST['from_date']));
			$to 	= date("Y-m-d 23:59:59", strtotime($_POST['to_date']));
			break;
	}
	$result = array();
	$title = array("รายงานสรุปยอดจำนวนขาย แสดงคุณลักษณะสินค้า   วันที่  ".thaiDate($from,"/")."  ถึง   ".thaiDate($to, "/"));
	$header = array("ลำดับ","รุ่นสินค้า","รายการสินค้า","สี","ไซด์","อื่นๆ","จำนวนขาย");
	array_push($result, $title);
	array_push($result, $header);
	$qr = dbQuery("SELECT id_product_attribute, id_product, reference, id_color, id_size, id_attribute FROM tbl_product_attribute WHERE ".$p_q." ORDER BY id_product ASC");
	$n = 1;
	while($rm = dbFetchArray($qr) )
	{
		$id_product_attribute = $rm['id_product_attribute'];
		$id_product = $rm['id_product'];
		$product_code = get_product_code($id_product);
		$id_color 		= $rm['id_color'];
		$id_size 			= $rm['id_size'];
		$id_attribute 	= $rm['id_attribute'];
		$reference		= $rm['reference'];
		$qs 	= dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product_attribute = ".$id_product_attribute." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
		$rs 	= dbFetchArray($qs); 
		$color 	= get_color_code($id_color);
		$size 	= get_size_name($id_size);
		$attr 		= get_attribute_name($id_attribute);
		$qty 		= $rs['qty'] > 0 ? $rs['qty'] : 0 ;
		$arr = array("n"=>$n, "product_code"=>$product_code, "reference"=>$reference,"color"=>$color, "size"=>$size, "attribute"=>$attr, "qty"=>$qty);
		array_push($result, $arr);		
		$n++;
		}
	$xml = new Excel_XML();
	$xml->setEncoding("UTF-8");
	$xml->setWorksheetTitle("Sale_attribute_analyz");
	$xml->addArray($result);
	$xml->generateXML("Sale_attribute_analyz");	
	
}

?>