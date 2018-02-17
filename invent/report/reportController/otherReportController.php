<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";
require "../../../library/class/php-excel.class.php";
require "../../function/order_helper.php";


if( isset( $_GET['getDeliveryFeeReport'] ) )
{
	$from = fromDate($_POST['from_date']);
	$to	= toDate($_POST['to_date']);
	$sc 	= 'fail';
	$ds	= array();
	$qr	= "SELECT tbl_order.id_order, reference, id_customer, id_employee, amount FROM tbl_delivery_fee JOIN tbl_order ON tbl_delivery_fee.id_order = tbl_order.id_order ";
	$qr	.= "WHERE payment = 'ออนไลน์' AND valid = 1 AND date_upd >= '".$from."' AND date_upd <= '".$to."'";
	
	$qs = dbQuery($qr);
	
	if( dbNumRows($qs) > 0 )
	{
		$totalQty = 0;
		$totalAmount = 0;
		$n	= 1;
		while( 	$rs = dbFetchArray($qs) )
		{
			$qty = qcQty($rs['id_order']);
			$qty = $qty == 0 ? orderQty($rs['id_order']) : $qty;
			$arr = array(
						'no'			=> $n,
						'reference'	=> $rs['reference'],
						'customer'	=> onlineCustomerName($rs['id_order']),
						'emp'			=> employee_name($rs['id_employee']),
						'qty'			=> number_format($qty),
						'amount'		=> number_format($rs['amount'])					
							);
			array_push($ds, $arr);
			$totalQty 			+= $qty;
			$totalAmount 	+= $rs['amount'];
			$n++;							
		}
		$arr = array("qty" => number_format($totalQty), "amount" => number_format($totalAmount));
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}
	echo $sc;
}

if( isset( $_GET['exportDeliveryFeeReport'] ) )
{
	$from		= fromDate( $_GET['from_date'] );
	$to		= toDate( $_GET['to_date'] );	
	$qr		= "SELECT tbl_order.id_order, reference, id_customer, id_employee, amount FROM tbl_delivery_fee JOIN tbl_order ON tbl_delivery_fee.id_order = tbl_order.id_order ";
	$qr		.= "WHERE payment = 'ออนไลน์' AND valid = 1 AND date_upd >= '".$from."' AND date_upd <= '".$to."'";
	
	$qs 		= dbQuery($qr);
	
	$excel	= new PHPExcel();
	
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานค่าขนส่ง');  //---- ชื่อ Sheet

	//------ ชื่อรายงาน -----//
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานค่าขนส่ง วันที่ '.thaiDate($from, '/').' - '.thaiDate($to, '/'));
	$excel->getActiveSheet()->mergeCells('A1:F1');
	
	//------- Table Header  ----//
	$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B2', 'ออเดอร์');
	$excel->getActiveSheet()->setCellValue('C2', 'ลูกค้า');
	$excel->getActiveSheet()->setCellValue('D2', 'พนักงาน');
	$excel->getActiveSheet()->setCellValue('E2', 'จำนวน');
	$excel->getActiveSheet()->setCellValue('F2', 'ค่าขนส่ง');
	$excel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setVertical('center');
	
	$row = 3;
	if( dbNumRows($qs) > 0 )
	{
		$n	= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$qty 	= qcQty($rs['id_order']);
			$qty 	= $qty == 0 ? orderQty($rs['id_order']) : $qty;
			$excel->getActiveSheet()->setCellValue('A'.$row, $n);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, onlineCustomerName($rs['id_order']) );
			$excel->getActiveSheet()->setCellValue('D'.$row, employee_name($rs['id_employee']) );
			$excel->getActiveSheet()->setCellValue('E'.$row, $qty);
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs['amount'] );			
			$n++;	
			$row++;
		}
		$r = $row - 1;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E3:E'.$r.')');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F3:F'.$r.')');
		$excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
		
		$excel->getActiveSheet()->getStyle('E3:E'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('F3:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}
	setToken($_GET['token']);
	$fileName = 'รายงานค่าขนส่ง.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fileName.'"');
	$writer = PHPExcel_IOFactory::CreateWriter($excel, 'Excel2007');
	$writer->save('php://output');	
}

if( isset($_GET['order_freq']) && isset($_GET['report']) )
{
	$from		= dbDate($_POST['from']);
	$to		= dbDate($_POST['to']);
	$date		= $from;
	$role 		= $_POST['role'];
	$rank		= array("00:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "23:59:59");
	$data 	= array();
	if($role == 0 )
	{
		$q = "role IN(1,2,4,5,6,7)";
	}
	else if( $role == 26 )
	{
		$q = "role IN(2,6)";
	}
	else
	{
		$q = "role = ".$role;
	}
	$total_qty = array(0,0,0,0,0,0,0,0,0,0,0);
	while($date <= $to)
	{
		$ra = $rank;
		$qty = array();
		foreach($ra as $key => $rd)
		{
			if( $key < 11 )
			{
				$d_from = $date." ".$rank[$key];
				$d_to 	= $date." ".$rank[($key+1)];
				$qs = dbQuery("SELECT id_order FROM tbl_order WHERE ".$q." AND order_status = 1 AND date_add >= '".$d_from."' AND date_add < '".$d_to."'");
				$qty[$key] = dbNumRows($qs);
				$total_qty[$key] += $qty[$key];
			}
		}
		$arr = array(
							"date"=> thaiDate($date,"/"), 
							"rank_0"	=>	ac_format($qty[0]), 
							"rank_1"	=>	ac_format($qty[1]), 
							"rank_2"	=>	ac_format($qty[2]), 
							"rank_3"	=>	ac_format($qty[3]), 
							"rank_4"	=>	ac_format($qty[4]), 
							"rank_5"	=>	ac_format($qty[5]), 
							"rank_6"	=>	ac_format($qty[6]), 
							"rank_7"	=>	ac_format($qty[7]), 
							"rank_8"	=>	ac_format($qty[8]), 
							"rank_9"	=>	ac_format($qty[9]), 
							"rank_10"	=>	ac_format($qty[10])
						);
		array_push($data, $arr);
		$date 	= date("Y-m-d", strtotime("+1 day $date"));
	}
	$arr = array(
						"date"		=> "รวม", 
						"rank_0"	=>	ac_format($total_qty[0]), 
						"rank_1"	=>	ac_format($total_qty[1]), 
						"rank_2"	=>	ac_format($total_qty[2]), 
						"rank_3"	=>	ac_format($total_qty[3]), 
						"rank_4"	=>	ac_format($total_qty[4]), 
						"rank_5"	=>	ac_format($total_qty[5]), 
						"rank_6"	=>	ac_format($total_qty[6]), 
						"rank_7"	=>	ac_format($total_qty[7]), 
						"rank_8"	=>	ac_format($total_qty[8]), 
						"rank_9"	=>	ac_format($total_qty[9]), 
						"rank_10"	=>	ac_format($total_qty[10])
					);
	array_push($data, $arr);
	echo json_encode($data);
}

if( isset($_GET['order_freq']) && isset($_GET['export']) )
{
	$from		= dbDate($_GET['from']);
	$to		= dbDate($_GET['to']);
	$date		= $from;
	$role 		= $_GET['role'];
	$rank		= array("00:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "23:59:59");
	$data 	= array();
	if($role == 0 )
	{
		$q = "role IN(1,2,4,5,6,7)";
		$title = "ทั้งหมด";
	}
	else if( $role == 26 )
	{
		$q = "role IN(2,6)";
		$title = "เฉพาะ แปรสภาพ";
	}
	else
	{
		$q = "role = ".$role;
	}
	if( $role == 1 ){ $title = "เฉพาะ ขายสินค้า"; }else if( $role == 4 ){ $title = "เฉพาะ สปอนเซอร์"; }else if( $role == 5 ){  $title = "เฉพาะ ฝากขาย"; }else if( $role == 7 ){ $title = "เฉพาะ อภินันท์"; }
	$arr = array("ตารางแจกแจงความถี่ในการสั่งสินค้าแยกตามช่วงเวลาต่างๆ ช่วงวันที่ ".thaiDate($from, "/")." - ".thaiDate($to, "/")."  ".$title);
	array_push($data, $arr);
	
	$arr = array("", "                                                                          ช่วงเวลา                                                                          ");
	array_push($data, $arr);
	
	$arr = array(" วันที่  ", "00:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
	array_push($data, $arr);
	
	
	$total_qty = array(0,0,0,0,0,0,0,0,0,0,0);
	while($date <= $to)
	{
		$ra = $rank;
		$qty = array();
		foreach($ra as $key => $rd)
		{
			if( $key < 11 )
			{
				$d_from = $date." ".$rank[$key];
				$d_to 	= $date." ".$rank[($key+1)];
				$qs = dbQuery("SELECT id_order FROM tbl_order WHERE ".$q." AND order_status = 1 AND date_add >= '".$d_from."' AND date_add < '".$d_to."'");
				$qty[$key] = dbNumRows($qs);
				$total_qty[$key] += $qty[$key];
			}
		}
		$arr = array( thaiDate($date,"/"), $qty[0], $qty[1], $qty[2], $qty[3], $qty[4], $qty[5], $qty[6], $qty[7], $qty[8], $qty[9], $qty[10]	);
		array_push($data, $arr);
		$date 	= date("Y-m-d", strtotime("+1 day $date"));
	}
	$arr = array("รวม", $total_qty[0] , $total_qty[1] , $total_qty[2] , $total_qty[3] , $total_qty[4] , $total_qty[5] , $total_qty[6] , $total_qty[7] , $total_qty[8] , $total_qty[9] , $total_qty[10]);
	array_push($data, $arr);
	$excel = new Excel_XML("UTF-8", "false", "order_freq");
	$excel->addArray($data);
	$excel->generateXML("order_freq");
	setToken($_GET['token']);
}

?>