<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";
require "../../function/order_helper.php";
include "../../function/address_helper.php";

if( isset( $_GET['getItemBacklogs'] ) )
{
	$sc = 'fail';
	$pOption	= $_POST['pOption'];
	$dOption	= $_POST['dOption'];
	$pFrom	= $_POST['pdFrom'];
	$pTo		= $_POST['pdTo'];
	$from		= $dOption == 0 ? '' : fromDate($_POST['from']);
	$to		= $dOption == 0 ? '' : toDate($_POST['to']);
	
	$pdIn		= $pOption == 0 ? FALSE : product_in_code($pFrom, $pTo);
	$dRange 	= $dOption == 0 ? "" : " AND tbl_order.date_add >= '".$from."' AND tbl_order.date_add <= '".$to."'";
	$pRange	= $pOption == 0 ? "" : ( $pdIn === FALSE ? "" : " AND id_product IN(".$pdIn.")");
	$qs	= dbQuery("SELECT * FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE current_state NOT IN(6,8,9) AND valid != 2 AND order_status = 1".$pRange . $dRange . " ORDER BY id_product_attribute ASC");
	
	$ds 		= array();
	$totalQty	= 0;
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array(
								'no'			=> $n,
								'reference'	=> $rs['product_reference'],
								'order'		=> $rs['reference'],
								'customer'	=> customer_name($rs['id_customer']),
								'payment'		=> $rs['payment'],
								'qty'			=> number_format($rs['product_qty']),
								'status'		=> stateLabel($rs['current_state'])
							);	
			array_push($ds, $arr);
			$totalQty += $rs['product_qty'];
			$n++;					
		}
	}
	$arr = array('totalQty' => number_format($totalQty));
	array_push($ds, $arr);
	$sc = json_encode($ds);	
	echo $sc;
}

if( isset( $_GET['exportItemBacklogs'] ) )
{
	$pOption	= $_GET['pOption'];
	$dOption	= $_GET['dOption'];
	$pFrom	= $_GET['pdFrom'];
	$pTo		= $_GET['pdTo'];
	$from		= $dOption == 0 ? '' : fromDate($_GET['form']);
	$to		= $dOption == 0 ? '' : toDate($_GET['to']);
	$pdIn		= $pOption == 0 ? FALSE : product_in_code($pFrom, $pTo);
	$dRange 	= $dOption == 0 ? "" : " AND tbl_order.date_add >= '".$from."' AND tbl_order.date_add <= '".$to."'";
	$pRange	= $pOption == 0 ? "" : ( $pdIn === FALSE ? "" : " AND id_product IN(".$pdIn.")");
	$qs	= dbQuery("SELECT * FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE current_state NOT IN(6,8,9) AND valid != 2 AND order_status = 1".$pRange . $dRange . " ORDER BY id_product_attribute ASC");
	
	$reportRange	= $dOption == 0 ? 'ทั้งหมด' : 'วันที่ '.thaiDate($from, '/').' ถึงวันที่ '.thaiDate($to, '/');
	
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานสินค้าค้างส่ง');
	//------ Report Title ----//
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าค้างส่ง '.$reportRange);
	$excel->getActiveSheet()->mergeCells('A1:G1');
	
	//---- Table header ---//
	$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B2', 'สินค้า');
	$excel->getActiveSheet()->setCellValue('C2', 'ออเดอร์');
	$excel->getActiveSheet()->setCellValue('D2', 'ลูกค้า');
	$excel->getActiveSheet()->setCellValue('E2', 'เงื่อนไข');
	$excel->getActiveSheet()->setCellValue('F2', 'จำนวน');
	$excel->getActiveSheet()->setCellValue('G2', 'สถานะ');
	$excel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
	
	$row = 3; //-- Start width row 3
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $n);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['product_reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('D'.$row, customer_name($rs['id_customer']) );
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs['payment']);
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs['product_qty']);
			$excel->getActiveSheet()->setCellValue('G'.$row, stateLabel($rs['current_state']));
			$n++;	
			$row++;
		}
		$rn = $row - 1;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F3:F'.$rn.')');
		$excel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
		$excel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->getAlignment()->setHorizontal('right');
	}
	setToken($_GET['token']);
	$fileName = 'รายงานสินค้าค้างส่ง.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fileName.'"');
	$writer = PHPExcel_IOFactory::CreateWriter($excel, 'Excel2007');
	$writer->save('php://output');	
		
}

if( isset( $_GET['exportOrderBacklogs'] ) )
{
	$cOption		= $_GET['cOption']; 	//--- เลือกลูกค้า
	$cFrom		= $_GET['cFrom'];		//--- เลือกลูกค้าเริ่มจาก ( id_customer )
	$cTo			= $_GET['cTo'];		//--- เลือกลูกค้าสิ้นสุดที่ ( id_customer )
	$dOption		= $_GET['dOption']; 	//--- เลือกวันที่
	$from			= fromDate( $_GET['from'] );
	$to			= toDate( $_GET['to'] );
	$range		= $dOption == 0 ? "" : " AND date_add >= '".$from."' AND date_add <= '".$to."'";
	$customer	= $cOption == 0 ? '' : ' AND id_customer >= '.$cFrom.' AND id_customer <= '.$cTo.' ';
	$qs = dbQuery("SELECT * FROM tbl_order WHERE current_state NOT IN(6,8,9) AND valid != 2 AND order_status = 1 ".$range . $customer);	
	$reportRange	= $dOption == 0 ? 'ทั้งหมด' : 'วันที่ '.thaiDate($from, '/').' ถึงวันที่ '.thaiDate($to, '/');
	
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานออเดอร์ค้างส่ง');
	
	//------ Report Title ----//
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานออเดอร์ค้างส่ง '.$reportRange);
	$excel->getActiveSheet()->mergeCells('A1:G1');
	
	//---- Table header ---//
	$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B2', 'ออเดอร์');
	$excel->getActiveSheet()->setCellValue('C2', 'ลูกค้า');
	$excel->getActiveSheet()->setCellValue('D2', 'เงื่อนไข');
	$excel->getActiveSheet()->setCellValue('E2', 'ยอดเงิน');
	$excel->getActiveSheet()->setCellValue('F2', 'สถานะ');
	$excel->getActiveSheet()->setCellValue('G2', 'วันที่');
	$excel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
	
	$row = 3; //-- Start width row 3
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$id			= $rs['id_order'];
			$excel->getActiveSheet()->setCellValue('A'.$row, $n);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs['payment'] == 'ออนไลน์' ? onlineCustomerName($id) : customer_name($rs['id_customer']));
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs['payment']);
			$excel->getActiveSheet()->setCellValue('E'.$row, orderAmount($id));
			$excel->getActiveSheet()->setCellValue('F'.$row, stateLabel($rs['current_state']));
			$excel->getActiveSheet()->setCellValue('G'.$row, thaiDate($rs['date_add'], '/'));
			$n++;	
			$row++;
		}
		$rn = $row - 1;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(E3:E'.$rn.')');
		$excel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
		$excel->getActiveSheet()->getStyle('F'.$row.':G'.$row)->getAlignment()->setHorizontal('right');
	}
	setToken($_GET['token']);
	$fileName = 'รายงานออเดอร์ค้างส่ง.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fileName.'"');
	$writer = PHPExcel_IOFactory::CreateWriter($excel, 'Excel2007');
	$writer->save('php://output');	
	
}

if( isset( $_GET['getOrderBacklogs'] ) )
{
	$sc 			= 'fail';
	$cOption		= $_POST['cOption']; 	//--- เลือกลูกค้า
	$cFrom		= $_POST['cFrom'];		//--- เลือกลูกค้าเริ่มจาก ( id_customer )
	$cTo			= $_POST['cTo'];		//--- เลือกลูกค้าสิ้นสุดที่ ( id_customer )
	$dOption		= $_POST['dOption']; 	//--- เลือกวันที่
	$from			= fromDate( $_POST['from'] );
	$to			= toDate( $_POST['to'] );
	
	$range		= $dOption == 0 ? "" : " AND date_add >= '".$from."' AND date_add <= '".$to."'";
	$customer	= $cOption == 0 ? '' : ' AND id_customer >= '.$cFrom.' AND id_customer <= '.$cTo.' ';
	
	$qs = dbQuery("SELECT * FROM tbl_order WHERE current_state NOT IN(6,8,9) AND valid != 2 AND order_status = 1 ".$range . $customer);
		
	$ds	= array();
	$totalAmount = 0;
	if( dbNumRows($qs) > 0 )
	{
		$n = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$id			= $rs['id_order'];
			$amount 	= orderAmount($id);
			$arr 		= array(
								'no'		=> $n,
								'reference'	=> $rs['reference'],
								'customer'	=> $rs['payment'] == 'ออนไลน์' ? onlineCustomerName($id) : customer_name($rs['id_customer']),
								'payment'		=> $rs['payment'],
								'amount'		=> number_format($amount, 2),
								'status'		=> stateLabel($rs['current_state']),
								'date_add'	=> thaiDate($rs['date_add'], '/')
								);
			array_push($ds, $arr);								
			$totalAmount += $amount;
			$n++;	
		}
	}
	$arr = array('totalAmount' => number_format($totalAmount, 2));
	array_push($ds, $arr);
	$sc = json_encode($ds);		
	echo $sc;
}

if( isset( $_GET['orderMoniter'] ) && isset( $_GET['no_group'] ) )
{
	$sc = array();
	//print_r($_POST);
	$s_ref	= $_POST['s_ref'];
	$s_cus	= $_POST['s_cus'] == "" ? "" : customer_in($_POST['s_cus']);
	$s_emp	= $_POST['s_emp'] == "" ? "" : employee_in($_POST['s_emp']);
	$fromDate = $_POST['from_date'] == "" ? "" : dbDate($_POST['from_date']);
	$toDate	= $_POST['to_date'] == "" ? "" : dbDate($_POST['to_date']);
	$fhour	= $_POST['fhour'];
	$thour	= $_POST['thour'];
	$timeState = $_POST['timeState'];

	//----- Payment
	$payment	= "";
	$payment	.= $_POST['online'] == "" ? "" : "'ออนไลน์',";
	$payment 	.= $_POST['credit'] == "" ? "" : "'เครดิต',";
	$payment	.= $_POST['cash'] == "" ? "" : "'เงินสด',";
	$payment	.= $_POST['consign'] == "" ? "" : "'ฝากขาย',";
	$payment	.= $_POST['support'] == "" ? "" : "'เบิกอภินันท์',";
	$payment 	.= $_POST['sponsor'] == "" ? "" : "'สปอนเซอร์สโมสร',";
	$payment	.= $_POST['transform'] == "" ? "" : "''";
	$payment	= trim($payment, ",");
	
	//----- State
	$state 	= "";
	$state 	.= $_POST['state_1'] == "" ? "" : '1,';
	$state 	.= $_POST['state_3'] == "" ? "" : '3,';
	$state 	.= $_POST['state_4'] == "" ? "" : '4,';
	$state 	.= $_POST['state_5'] == "" ? "" : '5,';
	$state 	.= $_POST['state_11'] == "" ? "" : "11,";
	$state 	.= $_POST['state_10'] == "" ? "" : '10,';
	$state	= trim($state, ",");
	
	$where = "WHERE current_state NOT IN(9,8) AND order_status = 1 AND valid != 2 ";
	$where .= $payment == "" ? "" : "AND payment IN(".$payment.") ";
	$where .= $state == "" ? "" : "AND current_state IN(".$state.") ";
	$where .= $s_ref == "" ? "" : "AND reference LIKE '%".$s_ref."%' ";
	$where .= ($s_cus == "" OR $s_cus === FALSE )? "" : "AND id_customer IN(".$s_cus.") ";
	$where .= ($s_emp == "" OR $s_emp === FALSE ) ? "" : "AND id_employee IN(".$s_emp.") ";
	
	$no = 1;
	
	while( $toDate >= $fromDate )
	{
		if( $timeState != "" )
		{
			$qr = "SELECT tbl_order.* FROM tbl_order JOIN tbl_order_state_change ON tbl_order.id_order = tbl_order_state_change.id_order ";
			$q_state = "AND tbl_order_state_change.id_order_state = ".$timeState." ";
			$q_time = $fromDate != "" && $toDate != "" ? "AND tbl_order_state_change.date_add >= '".$fromDate." ".$fhour.":00' AND tbl_order_state_change.date_add <= '".$fromDate." ".$thour.":00' " : "";
		}
		else
		{
			$qr = "SELECT * FROM tbl_order ";	
			$q_state = "";
			$q_time = $fromDate != "" && $toDate != "" ? "AND date_add >= '".fromDate($fromDate)."' AND date_add <= '".toDate($fromDate)."' " : "";
		}
		
		$qs = dbQuery($qr . $where . $q_state . $q_time );
		if( dbNumRows($qs) > 0 )
		{	
			while( $rs = dbFetchObject($qs) )
			{
				$arr = array(
									"no" => $no,
									"reference"	=> $rs->reference,
									"customer"	=> customer_name($rs->id_customer),
									"province"	=> customerProvince($rs->id_customer),
									"employee"	=> employee_name($rs->id_employee),
									"amount"		=> number_format(orderAmount($rs->id_order), 2),
									"payment"	=> $rs->payment,
									"state"		=> stateLabel($rs->current_state),
									"date_add"	=> thaiDate($rs->date_add, "/"),
									"date_upd"	=> thaiDate($rs->date_upd, "/")
									);
				array_push($sc, $arr);
				$no++;									
			}
		}	
		$fromDate = date("Y-m-d", strtotime("+1 day", strtotime($fromDate)));	
	}
	echo count($sc) > 0 ? json_encode($sc) : json_encode(array("no" => 0));
}

if( isset( $_GET['orderMoniter'] ) && isset( $_GET['group_by_customer'] ) )
{
	$sc = array();
	//print_r($_POST);
	$s_ref	= $_POST['s_ref'];
	$s_cus	= $_POST['s_cus'] == "" ? "" : customer_in($_POST['s_cus']);
	$s_emp	= $_POST['s_emp'] == "" ? "" : employee_in($_POST['s_emp']);
	$fromDate = $_POST['from_date'] == "" ? "" : dbDate($_POST['from_date']);
	$toDate	= $_POST['to_date'] == "" ? "" : dbDate($_POST['to_date']);
	$fhour	= $_POST['fhour'];
	$thour	= $_POST['thour'];
	$timeState = $_POST['timeState'];
	$customer = "";

	//----- Payment
	$payment	= "";
	$payment	.= $_POST['online'] == "" ? "" : "'ออนไลน์',";
	$payment 	.= $_POST['credit'] == "" ? "" : "'เครดิต',";
	$payment	.= $_POST['cash'] == "" ? "" : "'เงินสด',";
	$payment	.= $_POST['consign'] == "" ? "" : "'ฝากขาย',";
	$payment	.= $_POST['support'] == "" ? "" : "'เบิกอภินันท์',";
	$payment 	.= $_POST['sponsor'] == "" ? "" : "'สปอนเซอร์สโมสร',";
	$payment	.= $_POST['transform'] == "" ? "" : "''";
	$payment	= trim($payment, ",");
	
	//----- State
	$state 	= "";
	$state 	.= $_POST['state_1'] == "" ? "" : '1,';
	$state 	.= $_POST['state_3'] == "" ? "" : '3,';
	$state 	.= $_POST['state_4'] == "" ? "" : '4,';
	$state 	.= $_POST['state_5'] == "" ? "" : '5,';
	$state 	.= $_POST['state_11'] == "" ? "" : "11,";
	$state 	.= $_POST['state_10'] == "" ? "" : '10,';
	$state	= trim($state, ",");
	
	$where = "WHERE current_state NOT IN(9,8) AND order_status = 1 AND valid != 2 ";
	$where .= $payment == "" ? "" : "AND payment IN(".$payment.") ";
	$where .= $state == "" ? "" : "AND current_state IN(".$state.") ";
	$where .= $s_ref == "" ? "" : "AND reference LIKE '%".$s_ref."%' ";
	$where .= ($s_cus == "" OR $s_cus === FALSE )? "" : "AND id_customer IN(".$s_cus.") ";
	$where .= ($s_emp == "" OR $s_emp === FALSE ) ? "" : "AND id_employee IN(".$s_emp.") ";
		
	if( $fromDate != "" && $toDate != "" )
	{
		$qc = dbQuery("SELECT id_customer FROM tbl_order ".$where."AND date_add >= '".fromDate($fromDate)."' AND date_add <= '".toDate($toDate)."' GROUP BY id_customer");
	}
	else
	{
		$qc = dbQuery("SELECT id_customer FROM tbl_order ".$where."GROUP BY id_customer");
	}
	
	if( dbNumRows($qc) > 0 )
	{
		while( $rd = dbFetchObject( $qc ) )
		{
			$from = $fromDate;
			$to 	= $toDate;
			while($to >= $from)
			{
						if( $timeState != "" )
						{
							$qr = "SELECT tbl_order.* FROM tbl_order JOIN tbl_order_state_change ON tbl_order.id_order = tbl_order_state_change.id_order ";
							$q_state = "AND tbl_order_state_change.id_order_state = ".$timeState." ";
							$q_cus	= "AND id_customer = ".$rd->id_customer." ";
							$q_time = ($from != "" && $to != "") ? "AND tbl_order_state_change.date_add >= '".$from." ".$fhour.":00' AND tbl_order_state_change.date_add <= '".$from." ".$thour.":00' " : "";
						}
						else
						{
							$qr = "SELECT * FROM tbl_order ";	
							$q_state = "";
							$q_cus	= "AND id_customer = ".$rd->id_customer." ";
							$q_time = ($from != "" && $to != "") ? "AND date_add >= '".fromDate($from)."' AND date_add <= '".toDate($from)."' " : "";
						}
				
						$qs = dbQuery($qr . $where . $q_state . $q_cus . $q_time );
						$rows = dbNumRows($qs);
						if( $rows > 0 )
						{	
							$no = 1;
							$ds = array();
										while( $rs = dbFetchObject($qs) )
										{
											$arr = array(
																"no" => $no,
																"reference"	=> $rs->reference,
																"customer"	=> customer_name($rs->id_customer),
																"employee"	=> employee_name($rs->id_employee),
																"amount"		=> number_format(orderAmount($rs->id_order), 2),
																"payment"	=> $rs->payment,
																"state"		=> stateLabel($rs->current_state),
																"date_add"	=> thaiDate($rs->date_add, "/"),
																"date_upd"	=> thaiDate($rs->date_upd, "/")
																);
											array_push($ds, $arr);
											$no++;						
										}//--- endwhile
							$arr = array(
												"customer_id"		=> $rd->id_customer,
												"customer_name"	=> customer_name($rd->id_customer),
												"province"			=> customerProvince($rd->id_customer),
												"total_order"			=> $rows,
												"order"				=> $ds
												);
							array_push($sc, $arr);					
						}//-- endif
						$from = date("Y-m-d", strtotime("+1 day", strtotime($from)));		
			}//-- endwhile
		}//-- endif
	}//-- endif	
	echo json_encode($sc);
}

?>