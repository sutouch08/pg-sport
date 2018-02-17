<?php

require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/sponsor_helper.php";
require "../../function/report_helper.php";

if( isset($_GET['export_sponsor_summary']) && isset( $_GET['rank'] ) ){
	$cus_rank 		= $_GET['customer_rank'];
	$id_customer 	= $_GET['customer_id'];
	$year 			= $_GET['year'];
	$rank 			= $_GET['rank'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	$start 			= date("$year-01-01 00:00:00");
	$end 				= date("$year-12-31 23:59:59");
	if($cus_rank == 1 )
	{ 
		$qs = dbQuery("SELECT * FROM tbl_sponsor WHERE id_customer = ".$id_customer." AND year = ".$year);
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_sponsor WHERE year = ".$year);	
	}
	if($rank == 1)
	{
		$from = fromDate($from_date);
		$to 	= toDate($to_date);
	}else{
		$from = $start;
		$to 	= $end;
	}
	$title = "รายงานสรุปยอดสปอนเซอร์ แยกตามผู้รับ ".thaiDate($from, "/"). " - ".thaiDate($to, "/")."  งบประมาณปี ".$year;
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("Sponsor Summary");
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth(7);
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
	$excel->getActiveSheet()->getRowDimension()->setRowHeight(30);
	
	$excel->getActiveSheet()->mergeCells("A1:H1");
	$excel->getActiveSheet()->setCellValue("A1", $title);
	$excel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal("center");
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setVertical("center");
	
	//////  define table header
	
	$excel->getActiveSheet()->setCellValue("A2", "ลำดับ");
	$excel->getActiveSheet()->setCellValue("B2", "ผู้รับ");
	$excel->getActiveSheet()->setCellValue("C2", "งบประมาณ");
	$excel->getActiveSheet()->setCellValue("D2", "ใช้ไป");
	$excel->getActiveSheet()->setCellValue("E2", "ทุน (ใช้ไป)");
	$excel->getActiveSheet()->setCellValue("F2", "ใช้ไปทั้งหมด");
	$excel->getActiveSheet()->setCellValue("G2", "ทุน (ใช้ไปทั้งหมด)");
	$excel->getActiveSheet()->setCellValue("H2", "งบประมาณคงเหลือ");
	$excel->getActiveSheet()->getStyle("A2:H2")->getAlignment()->setHorizontal("center");
	$excel->getActiveSheet()->getStyle("A2:H2")->getAlignment()->setVertical("center"); 
	
	

	
	
	if(dbNumRows($qs) > 0)
	{
		$n = 1;
		$row = 3;
		while($rs = dbFetchArray($qs))
		{
			$id_customer 	= $rs['id_customer'];
			$id_sponsor 	= $rs['id_sponsor'];
			$id_budget 		= get_id_budget_by_id_sponsor($id_sponsor);
			$budget 			= get_sponsor_budget($id_budget);
			$used 			= budget_used($id_customer, $from, $to);
			$used_cost 	= budget_used_cost($id_customer, $from, $to);
			$all_used			= budget_used($id_customer, $start, $end);
			$all_used_cost = budget_used_cost($id_customer, $start, $end);
			$returnAmount 	= getReturnSponsorAmount($id_customer, $from, $to);
			$returnCostAmount		= getReturnSponsorCostAmount($id_customer, $from, $to);
			$return_amount_all		= getReturnSponsorAmount($id_customer, $start, $end);
			$return_cost_all		= getReturnSponsorCostAmount($id_customer, $start, $end);
			
			$used				= $used - $returnAmount;
			$all_used			= $all_used - $return_amount_all;
			$used_cost		= $used_cost - $returnCostAmount;
			$all_used_cost	= $all_used_cost - $return_cost_all;
			
			$balance 		= $budget - $all_used;
		
			$excel->getActiveSheet()->setCellValue("A".$row, $n);											
			$excel->getActiveSheet()->setCellValue("B".$row, customer_name($id_customer));		
			$excel->getActiveSheet()->setCellValue("C".$row, $budget);									
			$excel->getActiveSheet()->setCellValue("D".$row, $used);									
			$excel->getActiveSheet()->setCellValue("E".$row, $used_cost);								
			$excel->getActiveSheet()->setCellValue("F".$row, $all_used);									
			$excel->getActiveSheet()->setCellValue("G".$row, $all_used_cost);							
			$excel->getActiveSheet()->setCellValue("H".$row, $balance);	
								
			$n++; $row++;
		}
		//// Summary row
		
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->setCellValue("C".$row, "=SUM(C3:C".($row-1).")");
		$excel->getActiveSheet()->setCellValue("D".$row, "=SUM(D3:D".($row-1).")");
		$excel->getActiveSheet()->setCellValue("E".$row, "=SUM(E3:E".($row-1).")");
		$excel->getActiveSheet()->setCellValue("F".$row, "=SUM(F3:F".($row-1).")");
		$excel->getActiveSheet()->setCellValue("G".$row, "=SUM(G3:G".($row-1).")");
		$excel->getActiveSheet()->setCellValue("H".$row, "=SUM(H3:H".($row-1).")");
		$excel->getActiveSheet()->mergeCells("A".$row.":B".$row);
		$excel->getActiveSheet()->getStyle("A3:A".$row)->getAlignment()->setHorizontal("center");
		$excel->getActiveSheet()->getStyle("C3:H".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("A2:H".$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$excel->getActiveSheet()->getStyle("D2:E".$row)->getFont()->getColor()->setRGB("0000FF");
		$excel->getActiveSheet()->getStyle("F2:G".$row)->getFont()->getColor()->setRGB("FF0000");
		$excel->getActiveSheet()->getStyle("H2:H".$row)->getFont()->getColor()->setRGB("008000");					
			
	}

	setToken($_GET['token']);
	$file_name = "Sponsor_summary.xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");	
	
}

//******************************** รายงานสรุปยอดสปอนเซอร์สโมสร ตามช่วงเวลา  ***********************//
if( isset($_GET['sponsor_summary']) && isset( $_GET['rank'] ) ){
	$cus_rank 		= $_GET['customer_rank'];
	$id_customer 	= $_GET['customer_id'];
	$year 			= $_GET['year'];
	$rank 			= $_GET['rank'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	$start 			= date("$year-01-01 00:00:00");
	$end 				= date("$year-12-31 23:59:59");
	if($cus_rank == 1 )
	{ 
		$qs = dbQuery("SELECT * FROM tbl_sponsor WHERE id_customer = ".$id_customer." AND year = ".$year);
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_sponsor WHERE year = ".$year);	
	}
	if($rank == 1)
	{
		$from = fromDate($from_date);
		$to 	= toDate($to_date);
	}else{
		$from = $start;
		$to 	= $end;
	}
	
	$body = array();		
	$row = dbNumRows($qs);
	if($row >0){
		$total_budget 			= 0;
		$total_used 				= 0;
		$total_used_cost 		= 0;
		$total_balance 			= 0;
		$total_all_used 			= 0;
		$total_all_used_cost	= 0; 	
		$total_budget			= 0;
		$total_balance			= 0;
		$n = 1;
		while($rs = dbFetchArray($qs)){
			$id_customer 	= $rs['id_customer'];
			$id_sponsor 	= $rs['id_sponsor'];
			$id_budget 		= get_id_budget_by_id_sponsor($id_sponsor);
			$budget 			= get_sponsor_budget($id_budget);
			$used 			= budget_used($id_customer, $from, $to);
			$used_cost 	= budget_used_cost($id_customer, $from, $to);
			$all_used			= budget_used($id_customer, $start, $end);
			$all_used_cost = budget_used_cost($id_customer, $start, $end);
			$returnAmount 			= getReturnSponsorAmount($id_customer, $from, $to);
			$returnCostAmount		= getReturnSponsorCostAmount($id_customer, $from, $to);
			$return_amount_all		= getReturnSponsorAmount($id_customer, $start, $end);
			$return_cost_all		= getReturnSponsorCostAmount($id_customer, $start, $end);
			$used				= $used - $returnAmount;
			$all_used			= $all_used - $return_amount_all;
			$used_cost		= $used_cost - $returnCostAmount;
			$all_used_cost	= $all_used_cost - $return_cost_all;
			
			$balance 		= $budget - $all_used;
			$arr = array(
								"n"					=> $n, 
								"customer"		=> customer_name($id_customer), 
								"budget"			=> number_format($budget,2), 
								"used"				=> number_format($used, 2), 
								"used_cost"		=> number_format($used_cost, 2),
								"all_used"			=> number_format($all_used, 2), 
								"all_used_cost"	=> number_format($all_used_cost, 2),
								"balance"			=>	 number_format($balance,2), 
								"detail_btn"		=> $id_customer
				);
			array_push($body, $arr);
			$total_used				+= $used;
			$total_used_cost		+= $used_cost;
			$total_all_used			+= $all_used;
			$total_all_used_cost	+= $all_used_cost;
			$total_budget			+= $budget;
			$total_balance			+= $balance;
			$n++;
		}
			$arr = array(
							"total_budget"			=>	number_format($total_budget,2), 
							"total_used"				=>	number_format($total_used,2), 
							"total_used_cost"		=> number_format($total_used_cost, 2),
							"total_all_used"			=>	number_format($total_all_used,2), 
							"total_all_used_cost"	=> number_format($total_all_used_cost, 2),
							"total_balance"			=>	number_format($total_balance,2),
				);
			array_push($body, $arr);
	}else{
		$arr = array("nocontent"=>"----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
		array_push($body, $arr);
	}
	
	echo json_encode($body);	
}


//******************************** รายงานสรุปยอดสปอนเซอร์ ตามช่วงเวลา  Export to excel ***********************//



//******************************** Sponsor Detail  **************************//

if( isset($_GET['sponsor_detail']) && isset( $_GET['id_customer'] ) ){
	$id_customer 	= $_GET['id_customer'];
	$rank 			= $_GET['rank'];
	$year 			= $_GET['year'];
	$from_date 		= $_GET['from_date'];
	$to_date 		= $_GET['to_date'];
	if($rank == 1){
		$from 	= date("Y-m-d", strtotime($from_date))." 00:00:00";
		$to 		= date("Y-m-d", strtotime($to_date))." 23:59:59";
	}else{
		$from 	= date("$year-01-01 00:00:00");
		$to 		= date("$year-12-31 23:59:59");
	}
	$customer 	= new customer($id_customer);
	$name 		= $customer->full_name;
	$data 		= array();
			
	$sql = dbQuery("SELECT id_order, reference, id_customer, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY reference");
	$row = dbNumRows($sql);
	$total_qty = 0;
	$total_amount = 0;
	if($row >0){	
		$i = 0;
		while($i<$row){
			$rs 			= dbFetchArray($sql);
			$id_order 	= $rs['id_order'];
			$reference 	= $rs['reference'];
			$qty 			= $rs['qty'];
			$amount 		= $rs['amount'];
			$date_upd 	= $rs['date_upd'];
			$arr 			= array(
									"date"			=>thaiDate($date_upd), 
									"reference"	=>$reference, 
									"qty"			=>number_format($qty), 
									"amount"		=>number_format($amount,2), 
									"remark"		=>get_order_remark($id_order)
									);
			array_push($data, $arr);
			$total_qty 	+= $qty;
			$total_amount += $amount;
			$i++;
		}
		$qs = getReturnSponsorByCustomer($id_customer, $from, $to);
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchArray($qs) )
			{
				$id				= $rs['id_return_sponsor'];	
				$reference	= $rs['reference'];
				$remark		= $rs['remark'];
				$res			= getReturnSponsorQtyAndAmount($id);
				$qty 			= $res['qty'] * -1;
				$amount		= $res['amount'] * -1;
				$arr			= array(
										'date' 			=> thaiDate($rs['date_add']), 
										'reference' 	=> $reference, 
										'qty' 			=> number_format($qty), 
										'amount' 		=> number_format($amount, 2), 
										'remark' 		=> $remark
										);
				array_push($data, $arr);
				$total_qty += $qty;
				$total_amount += $amount;										
			}
		}
			$arr = array("date"=>"", "reference"=>"", "qty"=>number_format($total_qty), "amount"=>number_format($total_amount,2), "remark"=>"");
			array_push($data, $arr);
	}else{
			$arr = array("nocontent" => "----------------------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------------------");
			array_push($data, $arr);
	}
	echo json_encode($data);
}

if(isset($_GET['get_sponsor_detail']) && isset($_GET['id_order']) )
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
			$qs = dbQuery("SELECT * FROM tbl_sponsor_log WHERE action_type = '".$action_type."' AND ( date_upd BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59') AND (action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%') ORDER BY date_upd DESC");
		}else{
			$qs = dbQuery("SELECT * FROM tbl_sponsor_log WHERE ( date_upd BETWEEN '".$from_date." 00:00:00' AND '".$to_date." 23:59:59') AND action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%' ORDER BY date_upd DESC");	
		}
	}else{
		if($action_type != 'all' )
		{
			$qs = dbQuery("SELECT * FROM tbl_sponsor_log WHERE action_type = '".$action_type."' AND (action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%') ORDER BY date_upd DESC");
		}else{
			$qs = dbQuery("SELECT * FROM tbl_sponsor_log WHERE action LIKE '%".$search_text."%' OR from_value LIKE '%".$search_text."%' OR to_value LIKE '%".$search_text."%' ORDER BY date_upd DESC");	
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
if( isset($_GET['sponsor_by_customer']) && isset( $_GET['rank'] ) ){
	$view = $_GET['view'];
	$rank	= $_GET['rank'];
	$cus_rank = $_GET['customer_rank'];
	if($cus_rank == 1 ){ $id_customer = $_GET['customer_id']; $qr = "id_customer = ".$id_customer; $cus_title = customer_name($id_customer); }else{ $id_customer = ""; $qr = "id_customer != ''";  $cus_title = "ทั้งหมด"; }
	if($rank == 1 ){ $from_date = dbDate($_GET['from_date'])." 00:00:00"; $to_date = dbDate($_GET['to_date'])." 23:59:59"; }else{ $from_date = date("Y-01-01 00:00:00"); $to_date = date("Y-12-31 23:59:59"); }
	$data = array();
	if($view == 0 ) /// แยกตามเลขที่เอกสาร
	{
		$sql = dbQuery("SELECT id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 4 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference");
		
		if( dbNumRows($sql) >0 )
		{	        
			$total_qty = 0;
			$total_amount = 0;         
			while($rs = dbFetchArray($sql) )
			{
				$arr = array(
					"id_order"=>$rs['id_order'],
					"date_upd"=>thaiDate($rs['date_upd']), 
					"customer"=>customer_name($rs['id_customer']), 
					"employee"=>employee_name($rs['id_employee']),
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
		$sql = dbQuery("SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 4 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC");
		$data = array();
			if(dbNumRows($sql) > 0 )
			{
				$total_qty 		= 0;
				$total_amount 	= 0;
				while($rs = dbFetchArray($sql) )
				{
					$product = new product();
					$arr = array(
						"img"=>$product->get_product_attribute_image($rs['id_product_attribute'], 1), 
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

//********************************  รายงานยอดสปอนเซอร์ ตามช่วงเวลา Export to excel  *********************//

if( isset($_GET['export_sponsor_by_customer']) && isset( $_GET['rank'] ) ){
	$view = $_GET['view'];
	$rank	= $_GET['rank'];
	$cus_rank = $_GET['customer_rank'];
	$id_customer = $cus_rank == 1 ? $_GET['customer_id'] : 0;
	$from_date = $rank == 1 ? fromDate($_GET['from_date']) : fromDate(date('Y-01-01'));
	$to_date 	= $rank == 1 ? toDate($_GET['to_date']) : toDate(date('Y-12-31'));
	
	if($cus_rank == 1 && $view == 0 )
	{ 
		$qs = "SELECT id_order, id_customer, id_employee, reference, SUM( sold_qty ) AS qty, SUM( total_amount ) AS amount, date_upd ";
		$qs .= "FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference";
	}
	else if( $cus_rank == 1 && $view == 1 )
	{ 
		$qs = "SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount ";
		$qs .= "FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC";
	 }
	 else if( $cus_rank == 0 && $view == 0 )
	 {
		$qs = "SELECT id_order, id_customer, id_employee, reference, SUM( sold_qty ) AS qty, SUM( total_amount ) AS amount, date_upd ";
		$qs .= "FROM tbl_order_detail_sold WHERE id_role = 4 AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference";
	 }
	 else if( $cus_rank == 0 && $view == 1 )
	 {
		$qs = "SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount ";
		$qs .= "FROM tbl_order_detail_sold WHERE id_role = 4 AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC";	 
	 }
	
	$excel 	= new PHPExcel();
	
	// ---------------------------
	//			แยกตามเลขที่เอกสาร
	//----------------------------
	if( $view == 0 ) 
	{
		$qs = dbQuery($qs);
		$title = 'รายงานยอดสนับสนุนสโมสร (บุคคลภายนอก) แยกตามเลขที่เอกสาร';
		$club	= $cus_rank == 1 ? customer_name($id_customer) : 'ทั้งหมด' ;
		$date = date('d-m-Y H:i:s');
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Sponsor by document');
		$excel->getActiveSheet()->mergeCells('A1:F1');
		$excel->getActiveSheet()->setCellValue('A1', $title);
		$excel->getActiveSheet()->setCellValue('A2', 'สโมสร');
		$excel->getActiveSheet()->setCellValue('B2', $club);
		$excel->getActiveSheet()->setCellValue('C2', 'ตั้งแต่ '.thaiDate($from_date, '/').'  -  '.thaiDate($to_date, '/'));
		$excel->getActiveSheet()->mergeCells('C2:D2');
		$excel->getActiveSheet()->setCellValue('E2', 'วันที่ออกรายงาน : '.$date);
		$excel->getActiveSheet()->mergeCells('E2:F2');
		
		$excel->getActiveSheet()->setCellValue('A4', 'วันที่')
										->setCellValue('B4', 'สโมสร(ผู้รับ)')
										->setCellValue('C4', 'ผู้เบิก(พนักงาน)')
										->setCellValue('D4', 'เลขที่เอกสาร')
										->setCellValue('E4', 'จำนวน')
										->setCellValue('F4', 'มูลค่า')
										->setCellValue('G4', 'หมายเหตุ');
		$row = 5; 								
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchArray($qs) )
			{
				$excel->getActiveSheet()->setCellValue('A'.$row, thaiDate($rs['date_upd'], '/'));
				$excel->getActiveSheet()->setCellValue('B'.$row, customer_name($rs['id_customer']));
				$excel->getActiveSheet()->setCellValue('C'.$row, employee_name($rs['id_employee']));
				$excel->getActiveSheet()->setCellValue('D'.$row, $rs['reference']);
				$excel->getActiveSheet()->setCellValue('E'.$row, $rs['qty']);
				$excel->getActiveSheet()->setCellValue('F'.$row, $rs['amount']);
				$excel->getActiveSheet()->setCellValue('G'.$row, get_remark($rs['id_order']));
				$row++;
			}
			$rx = $row-1;
			$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
			$excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
			$excel->getActiveSheet()->setCellValue('E'.$row, 'SUM(E5:E'.$rx.')');
			$excel->getActiveSheet()->setCellValue('F'.$row, 'SUM(F5:F'.$rx.')');
			$excel->getActiveSheet()->getStyle('E5:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		}
		
		
		$file_name = "Sponsor_by_document.xlsx";
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename='".$file_name."'");
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$writer->save("php://output");	
	}
	else
	{
		$qs = dbQuery($qs);
		$title = 'รายงานยอดสนับสนุนสโมสร (บุคคลภายนอก) แยกตามรายการสินค้า';
		$club	= $cus_rank == 1 ? customer_name($id_customer) : 'ทั้งหมด' ;
		$date = date('d-m-Y H:i:s');
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Sponsor by items');
		$excel->getActiveSheet()->mergeCells('A1:F1');
		$excel->getActiveSheet()->setCellValue('A1', $title);
		$excel->getActiveSheet()->setCellValue('A2', 'สโมสร');
		$excel->getActiveSheet()->setCellValue('B2', $club);
		$excel->getActiveSheet()->setCellValue('C2', 'ตั้งแต่ '.thaiDate($from_date, '/').'  -  '.thaiDate($to_date, '/'));
		$excel->getActiveSheet()->mergeCells('C2:D2');
		$excel->getActiveSheet()->setCellValue('E2', 'วันที่ออกรายงาน : '.$date);
		$excel->getActiveSheet()->mergeCells('E2:F2');
		
		$excel->getActiveSheet()->setCellValue('A4', 'ลำดับ')
										->setCellValue('B4', 'รหัส')
										->setCellValue('C4', 'สินค้า')
										->setCellValue('D4', 'จำนวน')
										->setCellValue('E4', 'มูลค่า');
		$row = 5; 								
		if( dbNumRows($qs) > 0 )
		{
			$n = 1;
			while( $rs = dbFetchArray($qs) )
			{
				$excel->getActiveSheet()->setCellValue('A'.$row, $n);
				$excel->getActiveSheet()->setCellValue('B'.$row, $rs['product_reference']);
				$excel->getActiveSheet()->setCellValue('C'.$row, $rs['product_name']);
				$excel->getActiveSheet()->setCellValue('D'.$row, $rs['qty']);
				$excel->getActiveSheet()->setCellValue('E'.$row, $rs['amount']);
				$row++;
			}
			$rx = $row-1;
			$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
			$excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
			$excel->getActiveSheet()->setCellValue('D'.$row, 'SUM(D5:D'.$rx.')');
			$excel->getActiveSheet()->setCellValue('E'.$row, 'SUM(E5:E'.$rx.')');
			$excel->getActiveSheet()->getStyle('D5:E'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		}
		
		
		$file_name = "Sponsor_by_items.xlsx";
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename='".$file_name."'");
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$writer->save("php://output");	
	}
	
}

?>