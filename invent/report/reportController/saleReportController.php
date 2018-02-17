<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";
require "../../../library/class/php-excel.class.php";


//----------------- รายงานสินค้า แยกตามลูกค้า  ---------------------//
if( isset( $_GET['sale_detail_by_customer']) && isset( $_GET['report']))
{
	include 'report/reportSaleProductByCustomer.php';
}


//--------------- export รายงานสินค้า แยกตามลูกค้า -----------------//
if( isset( $_GET['sale_detail_by_customer']) && isset( $_GET['export']))
{
	include 'export/exportSaleProductByCustomer.php';
}



//----------------- รายงานยอดขาย แยกตามเลขที่เอกสาร ( sale_by_document.php ) -------------//
if( isset( $_GET['getSaleReportDocument'] ) )
{
	$sc		= 'nodata';
	$from		= fromDate($_POST['from'], TRUE);
	$to		= toDate($_POST['to'], TRUE);
	$field		= "id_order, reference, id_customer, SUM(discount_amount) AS discount, SUM(total_amount) AS amount, date_upd";
	$qs		= dbQuery("SELECT ".$field." FROM tbl_order_detail_sold WHERE id_role = 1 AND date_upd > '".$from."' AND date_upd < '".$to."' GROUP BY id_order ORDER BY date_upd ASC");
	$totalAmount = 0;
	$totalDiscount = 0;
	$totalNetAmount = 0;
	if( dbNumRows($qs) > 0 )
	{
		$ds = array();
		while( $rs = dbFetchArray($qs) )
		{
			$dis 		= $rs['discount']; //------- discount_amount
			$total		= $rs['amount']; //-------- total_discount
			$bill_dis 	= bill_discount($rs['id_order']);
			$amount 	= $total + $dis + $bill_dis;
			$arr = array(
						'date'		=> thaiDate($rs['date_upd'], '/'),
						'reference'	=> $rs['reference'],
						'customer'	=> customer_name($rs['id_customer']),
						'amount'		=> number_format($amount,2),
						'discount'		=> number_format( ($dis + $bill_dis), 2),
						"netAmount"	=> number_format( $total, 2 )
						);
			array_push($ds, $arr);
			$totalAmount += $amount;
			$totalDiscount += $dis + $bill_dis;
			$totalNetAmount += $total;
		}
		$arr = array('totalAmount' => number_format($totalAmount, 2), 'totalDiscount' => number_format($totalDiscount, 2), 'totalNetAmount' => number_format( $totalNetAmount, 2));
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}

	echo $sc;
}


//----------------- รายงานยอดขาย แยกตามเลขที่เอกสาร ( sale_by_document.php ) -------------//
if( isset( $_GET['exportSaleReportDocument'] ) )
{
	$from		= fromDate($_GET['from'], TRUE);
	$to		= toDate($_GET['to'], TRUE);
	$field		= "id_order, reference, id_customer, SUM(discount_amount) AS discount, SUM(total_amount) AS amount, date_upd";
	$qs		= dbQuery("SELECT ".$field." FROM tbl_order_detail_sold WHERE id_role = 1 AND date_upd > '".$from."' AND date_upd < '".$to."' GROUP BY id_order ORDER BY date_upd ASC");

	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานยอดขายแยกตามเลขที่เอกสาร');

	//---------- ชื่อรายงาน  -------------//
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานยอดขายแยกตามเลขที่เอกสาร วันที่ '.thaiDate($_GET['from'],'/').' ถึงวันที่ '.thaiDate($_GET['to'], '/') );
	$excel->getActiveSheet()->mergeCells('A1:F1');

	//-----------  Table header ------//
	$excel->getActiveSheet()->setCellValue('A2', 'วันที่');
	$excel->getActiveSheet()->setCellValue('B2', 'เอกสาร');
	$excel->getActiveSheet()->setCellValue('C2', 'ลูกค้า');
	$excel->getActiveSheet()->setCellValue('D2', 'จำนวนเงิน');
	$excel->getActiveSheet()->setCellValue('E2', 'ส่วนลด');
	$excel->getActiveSheet()->setCellValue('F2', 'สุทธิ');
	$excel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setVertical('center');
	$row = 3;
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$dis 		= $rs['discount']; //------- discount_amount
			$total		= $rs['amount']; //-------- total_discount
			$bill_dis 	= bill_discount($rs['id_order']);
			$amount 	= $total + $dis + $bill_dis;
			$y		= date('Y', strtotime($rs['date_upd']) );
			$m		= date('m', strtotime($rs['date_upd']) );
			$d		= date('d', strtotime($rs['date_upd']) );
			$date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);
			$excel->getActiveSheet()->setCellValue('A'.$row, $date);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, customer_name($rs['id_customer']));
			$excel->getActiveSheet()->setCellValue('D'.$row, $amount);
			$excel->getActiveSheet()->setCellValue('E'.$row, $dis+$bill_dis);
			$excel->getActiveSheet()->setCellValue('F'.$row, $total);

			$row++;
		}
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
		$excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D3:D'.( $row - 1 ).')');
		$excel->getActiveSheet()->setCellValue('E'.$row, '=SUM(E3:E'.( $row - 1 ).')');
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F3:F'.( $row - 1 ).')');

		$excel->getActiveSheet()->getStyle('A1:F'.$row)->getBorders()->getAllBorders()->setBorderStyle('thin');
		$excel->getActiveSheet()->getStyle('D3:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$excel->getActiveSheet()->getStyle('A3:A'.( $row -1 ))->getNumberFormat()->setFormatCode('dd/mm/yyyy');

	}

	setToken($_GET['token']);
	$file_name = "รายงานยอดขายแยกตามเลขที่เอกสาร.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');

}

//------------------  รายงานยอดขาย แยกตามสินค้า ( sale_report_product.php) -----------//
if( isset( $_GET['soldByProduct'] ) )
{
	$sc 			= 'fail';
	$ds			= array();
	$pdRange	= $_POST['pdRange'];	//----- ช่วงสินค้า 0 = ทั้งหมด 1 = เป็นช่วง
	$pdResult	= $_POST['pdResult'];	//----- แสดงเป็นรุ่น หรือ รายการ  0 = รุ่น 1 = รายการ
	$pdSale		= $_POST['pdSale'];		//----- ช่องทางการขาย 0 = ทั้งหมด , 1 =  ปกติ, 5 = ฝากขาย
	$pdFrom		= $_POST['pdFrom'];	//----- รหัสสินค้าเริ่มต้น กรณีที่เลือกสินค้าเป็นช่วง
	$pdTo		= $_POST['pdTo'];		//----- รหัสสินค้าสิ้นสุด กรณีที่เลือกสินค้าเป็นช่วง
	$from			= fromDate($_POST['fromDate']);
	$to			= toDate($_POST['toDate']);

	//-----  Inititial Head Title of report ----//
	$pRange		= $pdRange == 0 ? "ทั้งหมด" : $pdFrom." - ".$pdTo;
	$pSale		= $pdSale == 1 ? "ขายปกติ" : ( $pdSale == 5 ? "ฝากขาย" : "ทั้งหมด" );
	$pResult		= $pdResult == 0 ? "รุ่นสินค้า" : "รายการสินค้า";

	$arr 			= array(
								"from" 		=> thaiDate($_POST['fromDate'], '/'),
								"to" 			=> thaiDate($_POST['toDate'], '/') ,
								"pdRange"	=> $pRange,
								"pdSale"		=> $pSale,
								"pdResult"	=> $pResult
								);

	array_push($ds, $arr);

	//-------- End of Head Title -----//

	$qr		= "SELECT tbl_order_detail_sold.product_reference, product_code, tbl_order_detail_sold.product_name, SUM( sold_qty) AS qty, SUM( total_amount ) AS amount ";
	$qr		.= "FROM tbl_order_detail_sold JOIN tbl_product ON tbl_order_detail_sold.id_product = tbl_product.id_product ";
	$qr		.= "WHERE id_order_detail_sold != 0 ";
	$qr		.= $pdRange == 0 ? '' : "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' ";
	$qr		.= $pdSale == 0 ? "AND id_role IN( 1, 5 ) " : ( $pdSale == 1 ? "AND id_role = 1 " : "AND id_role = 5 ");
	$qr		.= "AND tbl_order_detail_sold.date_upd >= '".$from."' AND tbl_order_detail_sold.date_upd <= '".$to."' ";
	$qr		.= $pdResult == 0 ? "GROUP BY tbl_order_detail_sold.id_product " : "GROUP BY tbl_order_detail_sold.id_product_attribute ";

	$qs 		= dbQuery($qr);

	if( dbNumRows($qs) > 0 )
	{
		$no				= 1;
		$totalQty			= 0;
		$totalAmount		= 0;
		while( $rs = dbFetchArray($qs) )
		{
			$arr 	= array(
								"no"		=> $no,
								"product"	=> $pdResult == 0 ? $rs['product_code'] : $rs['product_reference'],
								"qty"		=> number_format($rs['qty']),
								"amount"	=> number_format($rs['amount'], 2)
							);
			array_push($ds, $arr);
			$no ++;
			$totalQty 			+= $rs['qty'];
			$totalAmount		+= $rs['amount'];
		}
		$arr = array( "totalQty" => number_format($totalQty), "totalAmount" => number_format($totalAmount, 2) );
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}
	else
	{
		$arr 	= array("no"	=> "", "product"	=> "", "qty" => "", "amount"	=> "");
		array_push($ds, $arr);

		$arr = array( "totalQty" => '0', "totalAmount" => '0.00' );
		array_push($ds, $arr);

		$sc = json_encode($ds);
	}

	echo $sc;
}

//------------------  Export รายงานยอดขาย แยกตามสินค้า ( sale_report_product.php) -----------//
if( isset( $_GET['exportSoldByProduct'] ) )
{
	$sc 			= 'fail';
	$ds			= array();
	$pdRange	= $_GET['pdRange'];	//----- ช่วงสินค้า 0 = ทั้งหมด 1 = เป็นช่วง
	$pdResult	= $_GET['pdResult'];	//----- แสดงเป็นรุ่น หรือ รายการ  0 = รุ่น 1 = รายการ
	$pdSale		= $_GET['pdSale'];		//----- ช่องทางการขาย 0 = ทั้งหมด , 1 =  ปกติ, 5 = ฝากขาย
	$pdFrom		= $_GET['pdFrom'];	//----- รหัสสินค้าเริ่มต้น กรณีที่เลือกสินค้าเป็นช่วง
	$pdTo		= $_GET['pdTo'];		//----- รหัสสินค้าสิ้นสุด กรณีที่เลือกสินค้าเป็นช่วง
	$from			= fromDate($_GET['fromDate']);
	$to			= toDate($_GET['toDate']);

	//-----  Inititial Head Title of report ----//
	$pRange		= $pdRange == 0 ? "ทั้งหมด" : $pdFrom." - ".$pdTo;
	$pSale		= $pdSale == 1 ? "ขายปกติ" : ( $pdSale == 5 ? "ฝากขาย" : "ทั้งหมด" );
	$pResult		= $pdResult == 0 ? "รุ่นสินค้า" : "รายการสินค้า";
	$excel 	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('รายงานยอดขายแยกตามสินค้า');

	//-------- ชื่อรายงาน
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานยอดขาย แยกตามสินค้า');
	$excel->getActiveSheet()->mergeCells('A1:D1');
	$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);

	//---------  เงื่อนไขรายงาน
	$excel->getActiveSheet()->setCellValue('A2', 'แยกตาม');
	$excel->getActiveSheet()->setCellValue('B2', 'ช่วงสินค้า');
	$excel->getActiveSheet()->setCellValue('C2', 'ช่องทางการขาย');
	$excel->getActiveSheet()->setCellValue('D2', 'วันที่');
	$excel->getActiveSheet()->setCellValue('A3', $pResult);
	$excel->getActiveSheet()->setCellValue('B3', $pRange);
	$excel->getActiveSheet()->setCellValue('C3', $pSale);
	$excel->getActiveSheet()->setCellValue('D3', thaiDate($_GET['fromDate'], '/') .' - '.thaiDate($_GET['toDate'], '/'));

	//---------- header table
	$excel->getActiveSheet()->setCellValue('A4', 'ลำดับ')
									->setCellValue('B4', 'สินค้า')
									->setCellValue('C4', 'จำนวน')
									->setCellValue('D4', 'มูลค่า');

	//---------- Column and Row Dimension
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
	$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
	$excel->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
	$excel->getActiveSheet()->getRowDimension(4)->setRowHeight(25);

	//----------  Set alignment
	$excel->getActiveSheet()->getStyle('A1:D4')->getAlignment()->setHorizontal('center');
	$excel->getActiveSheet()->getStyle('A1:D4')->getAlignment()->setVertical('center');

	//-----------  End of Header  -------//

	//------------------- Start of Data Content  ----------------//

	$qr		= "SELECT tbl_order_detail_sold.product_reference, product_code, tbl_order_detail_sold.product_name, SUM( sold_qty) AS qty, SUM( total_amount ) AS amount ";
	$qr		.= "FROM tbl_order_detail_sold JOIN tbl_product ON tbl_order_detail_sold.id_product = tbl_product.id_product ";
	$qr		.= "WHERE id_order_detail_sold != 0 ";
	$qr		.= $pdRange == 0 ? '' : "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' ";
	$qr		.= $pdSale == 0 ? "AND id_role IN( 1, 5 ) " : ( $pdSale == 1 ? "AND id_role = 1 " : "AND id_role = 5 ");
	$qr		.= "AND tbl_order_detail_sold.date_upd >= '".$from."' AND tbl_order_detail_sold.date_upd <= '".$to."' ";
	$qr		.= $pdResult == 0 ? "GROUP BY tbl_order_detail_sold.id_product " : "GROUP BY tbl_order_detail_sold.id_product_attribute ";

	$qs 		= dbQuery($qr);

	if( dbNumRows($qs) > 0 )
	{
		$row	= 5;
		$no	= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no)
											->setCellValue('B'.$row, $pdResult == 0 ? $rs['product_code'] : $rs['product_reference'])
											->setCellValue('C'.$row, $rs['qty'])
											->setCellVAlue('D'.$row, $rs['amount']);
			$no++;
			$row++;
		}
		$excel->getActiveSheet()->getStyle('C5:C'.($row-1))->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('D5:D'.($row-1))->getNumberFormat()->setFormatCode('#,##0.00');

		//--------  Sum Qty and Amount row
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':B'.$row);
		$excel->getActiveSheet()->setCellValue('C'.$row, '=SUM(C5:C'.($row-1).')');
		$excel->getActiveSheet()->setCellValue('D'.$row, '=SUM(D5:D'.($row-1).')');

		$excel->getActiveSheet()->getStyle('A'.$row.':D'.$row)->getFont()->setSize(16);
		$excel->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

		$excel->getActiveSheet()->getStyle('A1:D'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}
	setToken($_GET['token']);
	$fileName = 'รายงานยอดขายแยกตามสินค้า.xlsx'; //'Sold_amount_by_product.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachement; filename= "'.$fileName.'"');
	$writer	= PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');

}


if( isset( $_GET['get_sale_detail_by_employee'] ) && isset( $_POST['id_sale'] ) )
{
	$id_sale	 	= $_POST['id_sale'];
	$from 		= fromDate($_POST['from_date']);
	$to 			= toDate($_POST['to_date']);
	$rolex		= $_POST['role'] == 1 ? "ขายปกติ" : $_POST['role'] == 5 ? "ฝากขาย" : "ทั้งหมด";
	$role			= $_POST['role'] == 0 ? "1, 5" : $_POST['role'];
	$st			= $from;
	$data			= array();
	$arr 			= array("emp" => sale_name($id_sale), "range" => thaiDate($from, "/")." - ".thaiDate($to, "/")." :  ".$rolex  );
	array_push($data, $arr);
	$ro 			= new return_order();
	$total_amount 	= 0;
	$total_return   	= 0;
	$total_sale 		= 0;
	while( $st <= $to )
	{
		$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_sale = ".$id_sale." AND (date_upd BETWEEN '".fromDate($st)."' AND '".toDate($st)."')");
		list($amount) = dbFetchArray($qs);
		if($role == 5 ){ $return_amount = 0; }else{ $return_amount = $ro->get_return_amount_by_sale($id_sale, fromDate($st), toDate($st));}
		$sale_amount	= $amount - $return_amount;
		$arr = array( "date" => thaiDate($st), "amount" => number_format($amount, 2), "return_amount" => number_format($return_amount, 2), "sale_amount"	=> number_format($sale_amount, 2));
		array_push($data, $arr);
		$st = date("Y-m-d", strtotime("+1 day $st"));
		$total_amount 	+= $amount;
		$total_return 	+= $return_amount;
		$total_sale 		+= $sale_amount;
	}
	$arr = array("total" => "total", "total_amount" => number_format($total_amount, 2), "total_return" => number_format($total_return, 2), "total_sale" => number_format($total_sale, 2));
	array_push($data, $arr);
	echo json_encode($data);

}

if( isset( $_GET['sale_report_employee'] ) && isset( $_GET['report'] ) )
{
	$data 	= array();
	$from		= fromDate($_GET['from_date']);
	$to		= toDate($_GET['to_date']);
	$role		= $_GET['role'] == 0 ? "1, 5" : $_GET['role'];
	$range	= $_GET['range'];
	$arr = array("from" => thaiDate($from, "/"), "to" => thaiDate($to, "/"));
	array_push($data, $arr);
	$ro 		= new return_order();
	if( $range )
	{
		$in = get_in($_POST['sale']);
		$qr = dbQuery(	"SELECT * FROM tbl_sale WHERE id_sale IN(".$in.") ");
	}
	else
	{
		$qr = dbQuery("SELECT * FROM tbl_sale");
	}
	if( dbNumRows($qr) > 0 )
	{
		$no	= 1;
		$total_amount 	= 0;
		$total_return	= 0;
		$total_sale		= 0;
		while( $rs = dbFetchArray($qr) )
		{
		 	$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_sale = ".$rs['id_sale']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
			list($amount) = dbFetchArray($qs);
			if( is_null($amount) ){ $amount = 0; }
			if( $role == 5 ){ $return = 0; }else{	$return = $ro->get_return_amount_by_sale($rs['id_sale'], $from, $to); }
			$sale_amount = $amount - $return;
			$arr = array(
						"no"				=> $no,
						"emp"				=> employee_name($rs['id_employee']),
						"amount"			=> number_format($amount,2),
						"return_amount"	=> number_format($return, 2),
						"sale_amount"	=> number_format($sale_amount, 2),
						"id_sale"			=> $rs['id_sale'],
						"from"				=> $from,
						"to"				=> $to,
						"role"				=> $_GET['role']
						);
			array_push($data, $arr);
			$total_amount 	+= $amount;
			$total_return	+= $return;
			$total_sale		+= $sale_amount;
			$no++;
		}
		$arr = array("last" => "last", "total_amount" => number_format($total_amount, 2), "total_return" => number_format($total_return, 2), "total_sale" => number_format($total_sale, 2));
		array_push($data, $arr);
	}
	echo json_encode($data);
}

if( isset( $_GET['sale_report_employee'] ) && isset( $_GET['export'] ) )
{
	$excel 	= new PHPExcel();
	$from		= fromDate($_GET['from_date']);
	$to		= toDate($_GET['to_date']);
	$rolex	= $_GET['role'];
	$role		= $_GET['role'] == 0 ? "1, 5" : $_GET['role'];
	$range	= $_GET['range'];
	$ro 		= new return_order();
	if( $range )
	{
		$in = get_in($_POST['sale']);
		$qr = dbQuery(	"SELECT * FROM tbl_sale WHERE id_sale IN(".$in.") ");
	}
	else
	{
		$qr = dbQuery("SELECT * FROM tbl_sale");
	}
	if( $rolex == 0){ $sale_type = "ทั้งหมด"; }else if( $rolex == 1 ){ $sale_type = "ขายปกติ"; }else if( $rolex == 5 ){ $sale_type = "ฝากขาย"; }else{ $sale_type = "ทั้งหมด"; }
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('sold amount by employee');
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานยอดขาย แยกตามพักงานขาย  วันที่  '.thaiDate($from, '/').' ถึงวันที่  '.thaiDate($to, '/').'  ช่องทางการขาย : '.$sale_type);
	$excel->getActiveSheet()->mergeCells('A1:E1');
	$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
	$excel->getActiveSheet()->getStyle('A1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B2', 'พนักงานขาย');
	$excel->getActiveSheet()->setCellValue('C2', 'ยอดขาย');
	$excel->getActiveSheet()->setCellValue('D2', 'ลดหนี้');
	$excel->getActiveSheet()->setCellValue("E2", "มูลค่าขาย");
	$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$excel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal("center");
	$excel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setVertical("center");
	$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);


	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(45);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(21);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(21);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(21);

	if( dbNumRows($qr) > 0 )
	{
		$no	= 1;
		$row = 3;
		while( $rs = dbFetchArray($qr) )
		{
		 	$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_sale = ".$rs['id_sale']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
			list($amount) = dbFetchArray($qs);
			if( is_null($amount) ){ $amount = 0; }
			if( $role == 5 ){ $return = 0; }else{	$return = $ro->get_return_amount_by_sale($rs['id_sale'], $from, $to); }
			$sale_amount = $amount - $return;

			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue("B".$row, employee_name($rs['id_employee']));
			$excel->getActiveSheet()->setCellValue("C".$row, $amount);
			$excel->getActiveSheet()->setCellValue("D".$row, $return);
			$excel->getActiveSheet()->setCellValue("E".$row, $sale_amount);
			$no++;	$row++;
		}
		$excel->getActiveSheet()->getStyle("C3:E".($row-1))->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->mergeCells("A".$row.":B".$row);
		$excel->getActiveSheet()->setCellValue("C".$row, '=SUM(C3:C'.($row-1).')');
		$excel->getActiveSheet()->setCellValue("D".$row, '=SUM(D3:D'.($row-1).')');
		$excel->getActiveSheet()->setCellValue("E".$row, '=SUM(E3:E'.($row-1).')');

		$excel->getActiveSheet()->getStyle('C'.$row.':E'.$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle('A3:A'.$row)->getAlignment()->setHorizontal("center");
		$excel->getActiveSheet()->getStyle('A2:E'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}
	setToken($_GET['token']);
	$file_name = "sold_amount_by_sale.xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");
}

function get_in($data)
{
	$rs = "";
	$c 	= count($data);
	$i 		= 0;
	foreach($data as $id => $val)
	{
		$rs .= $id;
		$i++;
		if($i < $c){ $rs .= ", "; }
	}
	if( $rs == ""){ $rs = 0; }
	return $rs;
}


if( isset( $_GET['get_sale_detail_by_zone'] ) && isset( $_POST['id_group'] ) )
{
	$id_group 	= $_POST['id_group'];
	$from 		= fromDate($_POST['from_date']);
	$to 			= toDate($_POST['to_date']);
	$st			= $from;
	$data			= array();
	$arr 			= array("zone" => customer_group($id_group), "range" => "วันที่ ". thaiDate($from)." ถึงวันที่ ".thaiDate($to));
	array_push($data, $arr);
	$ro 			= new return_order();
	$total_amount 	= 0;
	$total_return   	= 0;
	$total_sale 		= 0;
	while( $st <= $to )
	{
		$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE id_default_group = ".$id_group." AND id_role IN(1,5) AND (tbl_order_detail_sold.date_upd BETWEEN '".fromDate($st)."' AND '".toDate($st)."')");
		list($amount) = dbFetchArray($qs);
		$return_amount = $ro->get_return_amount_by_customer_group($id_group, fromDate($st), toDate($st));
		$sale_amount	= $amount - $return_amount;
		$arr = array( "date" => thaiDate($st), "amount" => number_format($amount, 2), "return_amount" => number_format($return_amount, 2), "sale_amount"	=> number_format($sale_amount, 2));
		array_push($data, $arr);
		$st = date("Y-m-d", strtotime("+1 day $st"));
		$total_amount 	+= $amount;
		$total_return 	+= $return_amount;
		$total_sale 		+= $sale_amount;
	}
	$arr = array("total" => "total", "total_amount" => number_format($total_amount, 2), "total_return" => number_format($total_return, 2), "total_sale" => number_format($total_sale, 2));
	array_push($data, $arr);

	echo json_encode($data);

}


if( isset( $_GET['sale_report_zone'] ) && isset( $_GET['report'] ) )
{
	$data = array();
	$from = fromDate($_GET['from_date']);
	$to	= toDate($_GET['to_date']);
	$range = $_GET['range'];
	$id_group = isset($_POST['select']) ? $_POST['select'] : 0 ;
	if( $range == 0 ){ $qs = dbQuery("SELECT * FROM tbl_group");	}
	if( $range == 1 ){ $qs = dbQuery("SELECT * FROM tbl_group WHERE id_group = ".$_POST['select']); }
	if( $range == 2 ){ $in = get_in($_POST['group']); $qs = dbQuery("SELECT * FROM tbl_group WHERE id_group IN(".$in.")"); }
	$row = dbNumRows($qs);
	$rank 	= $range == 0 ? "ทุกพื้นที่การขาย" : $range == 1 ? "เฉพาะ ".customer_group($id_group) : "บางพื้นที่";
	$arr = array("range" => $rank, "from" => $_GET['from_date'], "to" => $_GET['to_date']);
	array_push($data, $arr);
	if($row > 0 )
	{
		$no = 1;
		$ro = new return_order();
		$total_amount 	= 0;
		$total_return   	= 0;
		$total_sale 		= 0;
		while($rs = dbFetchArray($qs) )
		{
			$qr = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE id_default_group = ".$rs['id_group']." AND id_role IN(1,5) AND (tbl_order_detail_sold.date_upd BETWEEN '".$from."' AND '".$to."')");
			list($amount) = dbFetchArray($qr);
			$return_amount = $ro->get_return_amount_by_customer_group($rs['id_group'], $from, $to);
			$sale_amount	= $amount - $return_amount;
			$arr = array(
							"no" => $no,
							"id_group" 			=> $rs['id_group'],
							"zone" => $rs['group_name'],
							"amount" => number_format($amount, 2),
							"return_amount" => number_format($return_amount, 2),
							"sale_amount"		=> number_format($sale_amount, 2),
							"from"					=> $from,
							"to"					=> $to
							);
			array_push($data, $arr);
			$total_amount 	+= $amount;
			$total_return 	+= $return_amount;
			$total_sale 		+= $sale_amount;
			$no++;
		}
		$arr = array("last" => "last", "total_amount" => number_format($total_amount, 2), "total_return" => number_format($total_return, 2), "total_sale" => number_format($total_sale, 2));
		array_push($data, $arr);
	}

	echo json_encode($data);
}

if( isset($_GET['sale_profit_customer'] ) && isset($_GET['report']) && isset($_POST['id_customer'] ) )
{
	if( $_POST['p_rank'] == 2 )
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND id_customer = ".$_POST['id_customer']." AND product_reference BETWEEN '".product_from($_POST['p_from'])."' AND '".product_to($_POST['p_to'])."' AND (tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from'])."' AND '".toDate($_POST['to'])."') ";
		$sql .= "GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
	}
	else
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND id_customer = ".$_POST['id_customer']." AND tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from'])."' AND '".toDate($_POST['to'])."' GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
	}

	$data = array();
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_cost = 0;
		$total_amount = 0;
		$total_profit = 0;
		while($rs = dbFetchArray($qs) )
		{
			$cost 	= $rs['qty'] * $rs['cost'];
			$profit 	= $rs['amount'] - $cost;
			$percent	= $rs['amount'] == 0? 0 : ($profit/$rs['amount'])*100;
			$arr = array(
						"no" 					=> $n,
						"product_code"		=> $rs['product_reference'],
						"product_name"		=> $rs['product_name'],
						"price"				=> $rs['price'],
						"qty" 					=> number_format($rs['qty']),
						"cost" 				=> number_format($cost,2),
						"amount" 				=> number_format($rs['amount'], 2),
						"profit" 				=> $profit < 0 ? "<span style='color:red;'>".number_format($profit, 2)."</span>" : "<span style='color: green;'>".number_format($profit, 2)."</span>",
						"percent"				=> $percent < 0 ? "<span style='color:red'>".number_format($percent,2)." % </span>" : "<span style='color: green;'>".number_format($percent,2)." % </span>"
						);
			array_push($data, $arr);
			$total_qty 		+= $rs['qty'];
			$total_cost 		+= $cost;
			$total_amount 	+= $rs['amount'];
			$total_profit 	+= $profit;
			$n++;
		}
		$total_percent = $total_amount == 0 ? 0 : ($total_profit / $total_amount)*100;
		$arr = array(
						"no" 					=> "",
						"product_code"		=> "",
						"product_name"		=> "",
						"price"				=> "รวม",
						"qty" 					=> number_format($total_qty),
						"cost" 				=> number_format($total_cost,2),
						"amount" 				=> number_format($total_amount, 2),
						"profit" 				=> $total_profit < 0 ? "<span style='color: red'>".number_format($total_profit, 2)."</span>" : "<span style='color: green;'>".number_format($total_profit, 2)."</span>",
						"percent"				=> $total_percent < 0 ? "<span style='color: red'>". number_format($total_percent, 2)." % </span>" : "<span style='color: green;'>".number_format($total_percent, 2)." %</span>"
						);
		array_push($data, $arr);
	}
	else
	{
		$arr = array(
						"no" 					=> "-",
						"product_code"		=> "-",
						"product_name"		=> "-",
						"price"				=> "-",
						"qty" 					=> "-",
						"cost" 				=> "-",
						"amount" 				=> "-",
						"profit" 				=> "-"
						);
		array_push($data, $arr);
	}
	echo json_encode($data);
}

if( isset($_GET['sale_profit_customer'] ) && isset($_GET['export']) && isset($_GET['id_customer'] ) )
{
	if( $_POST['product'] == 2 )
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND id_customer = ".$_GET['id_customer']." AND product_reference BETWEEN '".product_from($_POST['p_from'])."' AND '".product_to($_POST['p_to'])."' AND (tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from_date'])."' AND '".toDate($_POST['to_date'])."') ";
		$sql .= "GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
		$p_title = $_POST['p_from']." ถึง ".$_GET['p_to'];
	}
	else
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND id_customer = ".$_GET['id_customer']." AND tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from_date'])."' AND '".toDate($_POST['to_date'])."' GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
		$p_title = "ทั้งหมด";
	}

	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("sale profit by customer");

	$excel->getActiveSheet()->setCellValue("A1", "รายงายยอดขาย แยกตามรายการสินค้า แสดงกำไรขั้นต้น");
	$excel->getActiveSheet()->setCellValue("A2", "ลูกค้า");
	$excel->getActiveSheet()->setCellValue("B2", $_POST['customer']);
	$excel->getActiveSheet()->setCellValue("C1", "วันที่");
	$excel->getActiveSheet()->setCellValue("C2", thaiDate($_POST['from_date'], "/")." - ".thaiDate($_POST['to_date'], "/"));
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(40);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("I")->setWidth(10);
	$excel->getActiveSheet()->setCellValue("A3", "ลำดับ")
									->setCellValue("B3", "รหัส")
									->setCellValue("C3", "รายละเอียด")
									->setCellValue("D3", "ราคา")
									->setCellValue("E3", "จำนวนขาย")
									->setCellValue("F3", "ต้นทุนขาย")
									->setCellValue("G3", "มูลค่าขาย")
									->setCellValue("H3", "กำไรขั้นต้น")
									->setCellValue("I3", "% (กำไร)");

	if(dbNumRows($qs) > 0 )
	{
		$row = 4;
		$n = 1;
		while($rs = dbFetchArray($qs) )
		{
			$cost 	= $rs['qty'] * $rs['cost'];
			$profit 	= $rs['amount'] - $cost;
			$percent	= $rs['amount'] == 0 ? 0 : number_format(($profit/$rs['amount']), 4);
			$excel->getActiveSheet()->setCellValue("A".$row, $n)
										    ->setCellValue("B".$row, $rs['product_reference'])
											->setCellValue("C".$row, $rs['product_name'])
											->setCellValue("D".$row, $rs['price'])
											->setCellValue("E".$row, $rs['qty'])
											->setCellValue("F".$row, $cost)
											->setCellValue("G".$row, $rs['amount'])
											->setCellValue("H".$row, $profit)
											->setCellValue("I".$row, "=H".$row."/G".$row);
			$n++; $row++;
		}
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->mergeCells("A".$row.":D".$row);
		$excel->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal("right");
		$rx = $row - 1;
		$excel->getActiveSheet()->setCellValue("E".$row, "=SUM(E4:E".$rx.")")
										->setCellValue("F".$row, "=SUM(F4:F".$rx.")")
										->setCellValue("G".$row, "=SUM(G4:G".$rx.")")
										->setCellValue("H".$row, "=SUM(H4:H".$rx.")")
										->setCellValue("I".$row, "=H".$row."/G".$row);
		$excel->getActiveSheet()->getStyle("D4:D".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("E4:E".$row)->getNumberFormat()->setFormatCode("#,##0");
		$excel->getActiveSheet()->getStyle("F4:H".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("I4:I".$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
	}
	setToken($_GET['token']);
	$file_name = "sale_profit_by_customer.xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");
}


if( isset($_GET['sale_profit_item'] ) && isset($_GET['report']) && isset($_POST['p_rank'] ) )
{
	if( $_POST['p_rank'] == 2 )
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND product_reference BETWEEN '".product_from($_POST['p_from'])."' AND '".product_to($_POST['p_to'])."' AND (tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from'])."' AND '".toDate($_POST['to'])."') ";
		$sql .= "GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
	}
	else
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_POST['from'])."' AND '".toDate($_POST['to'])."' GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
	}

	$data = array();
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_cost = 0;
		$total_amount = 0;
		$total_profit = 0;
		while($rs = dbFetchArray($qs) )
		{
			$cost 	= $rs['qty'] * $rs['cost'];
			$profit 	= $rs['amount'] - $cost;
			$percent	= $rs['amount'] == 0? 0 : ($profit/$rs['amount'])*100;
			$arr = array(
						"no" 					=> $n,
						"product_code"		=> $rs['product_reference'],
						"product_name"		=> $rs['product_name'],
						"price"				=> $rs['price'],
						"qty" 					=> number_format($rs['qty']),
						"cost" 				=> number_format($cost,2),
						"amount" 				=> number_format($rs['amount'], 2),
						"profit" 				=> $profit < 0 ? "<span style='color:red;'>".number_format($profit, 2)."</span>" : "<span style='color: green;'>".number_format($profit, 2)."</span>",
						"percent"				=> $percent < 0 ? "<span style='color:red'>".number_format($percent,2)." % </span>" : "<span style='color: green;'>".number_format($percent,2)." % </span>"
						);
			array_push($data, $arr);
			$total_qty 		+= $rs['qty'];
			$total_cost 		+= $cost;
			$total_amount 	+= $rs['amount'];
			$total_profit 	+= $profit;
			$n++;
		}
		$total_percent = $total_amount == 0 ? 0 : ($total_profit / $total_amount)*100;
		$arr = array(
						"no" 					=> "",
						"product_code"		=> "",
						"product_name"		=> "",
						"price"				=> "รวม",
						"qty" 					=> number_format($total_qty),
						"cost" 				=> number_format($total_cost,2),
						"amount" 				=> number_format($total_amount, 2),
						"profit" 				=> $total_profit < 0 ? "<span style='color: red'>".number_format($total_profit, 2)."</span>" : "<span style='color: green;'>".number_format($total_profit, 2)."</span>",
						"percent"				=> $total_percent < 0 ? "<span style='color: red'>". number_format($total_percent, 2)." % </span>" : "<span style='color: green;'>".number_format($total_percent, 2)." %</span>"
						);
		array_push($data, $arr);
	}
	else
	{
		$arr = array(
						"no" 					=> "-",
						"product_code"		=> "-",
						"product_name"		=> "-",
						"price"				=> "-",
						"qty" 					=> "-",
						"cost" 				=> "-",
						"amount" 				=> "-",
						"profit" 				=> "-"
						);
		array_push($data, $arr);
	}
	echo json_encode($data);
}


if( isset($_GET['sale_profit_item'] ) && isset($_GET['export']) && isset($_GET['p_rank'] ) )
{
	if( $_GET['p_rank'] == 2 )
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND product_reference BETWEEN '".product_from($_GET['p_from'])."' AND '".product_to($_GET['p_to'])."' AND (tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_GET['from'])."' AND '".toDate($_GET['to'])."') ";
		$sql .= "GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
		$p_title = $_GET['p_from']." ถึง ".$_GET['p_to'];
	}
	else
	{
		$sql = "SELECT product_reference, product_name, price, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, tbl_order_detail_sold.cost FROM tbl_order_detail_sold JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$sql .= "WHERE id_role IN(1,5) AND tbl_order_detail_sold.date_upd BETWEEN '".fromDate($_GET['from'])."' AND '".toDate($_GET['to'])."' GROUP BY tbl_order_detail_sold.id_product_attribute ORDER BY tbl_order_detail_sold.product_reference ASC";
		$qs = dbQuery($sql);
		$p_title = "ทั้งหมด";
	}

	$data = array();

	$arr 	= array("รายงานยอดขาย แยกตามรายการสินค้า แสดงกำไรขั้นต้น");
	array_push($data, $arr);
	$arr 	= array("รายการสินค้า : ". $p_title . "  วันที่  ". $_GET['from']. " ถึง ". $_GET['to']);
	array_push($data, $arr);
	$arr 	= array("ลำดับ", "รหัส", "รายละเอียด", "ราคา", "จำนวนขาย", "ต้นทุนขาย", "มูลค่าขาย", "กำไรขั้นต้น", "% (กำไร)");
	array_push($data, $arr);

	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_cost = 0;
		$total_amount = 0;
		$total_profit = 0;
		while($rs = dbFetchArray($qs) )
		{
			$cost 	= $rs['qty'] * $rs['cost'];
			$profit 	= $rs['amount'] - $cost;
			$percent	= $rs['amount'] == 0 ? 0 : number_format(($profit/$rs['amount']), 4);
			$arr = array($n, $rs['product_reference'], $rs['product_name'], $rs['price'], $rs['qty'], $cost, $rs['amount'], $profit, $percent);
			array_push($data, $arr);
			$total_qty 		+= $rs['qty'];
			$total_cost 		+= $cost;
			$total_amount 	+= $rs['amount'];
			$total_profit 	+= $profit;
			$n++;
		}
		$arr = array("", "", "", "รวม", $total_qty, $total_cost, $total_amount, $total_profit, $total_amount == 0 ? 0 : number_format(($total_profit / $total_amount), 4));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("-", "-", "-", "-", "-", "-", "-", "-");
		array_push($data, $arr);
	}
	$excel 	= new Excel_XML("UTF-8", false, "sale_profit_by_items");
	$excel->addArray($data);
	$excel->generateXML("sale_profit_by_items");
	setToken($_GET['token']);
}

if( isset($_GET['sale_summary_by_category']) && isset($_GET['report']) )
{
	$rank		= $_GET['rank'];
	$year		= $_GET['year'];

	$data 	= array(); /// ไว้เก็บผลลัพธ์เพื่อส่งกลับ
	if($rank == 2)
	{
		$p_selected = $_POST['p_selected'];
		$in	 = "";
		$c  = count($p_selected);
		$i 	= 1;
		foreach($p_selected as $id)
		{
			$in .= $id;
			if($i < $c){ $in .=", "; }
			$i++;
		}

		$qm = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category IN($in)");
	}else{
		$qm = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category != 0");
	}
	if(dbNumRows($qm) > 0 ):
	while($rs = dbFetchArray($qm) ) :
		$arr = array("no"=>"", "product"=>$rs['category_name'],
								"qty_1"=>"", "qty_2"=>"", "qty_3"=>"", "qty_4"=>"", "qty_5"=>"", "qty_6"=>"",
								"qty_7"=>"", "qty_8"=>"", "qty_9"=>"", "qty_10"=>"", "qty_11"=>"", "qty_12"=>"", "total_qty"=>"",
								"amount_1"=>"","amount_2"=>"","amount_3"=>"","amount_4"=>"", "amount_5"=>"","amount_6"=>"",
								"amount_7"=>"", "amount_8"=>"", "amount_9"=>"", "amount_10"=>"", "amount_11"=>"", "amount_12"=>"", "total_amount"=>""
							);
		array_push($data, $arr);
		$qs = dbQuery("SELECT id_product, product_code FROM tbl_product WHERE default_category_id =".$rs['id_category']." ORDER BY product_code ASC");
			$n = 1;
			$sum_qty = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0);
			$sum_amount = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0);
			$all_qty = 0;
			$all_amount = 0;
			while($rd = dbFetchArray($qs)) :
				$month = array("01","02","03","04","05", "06", "07","08", "09", "10", "11", "12");
				$total_qty = 0;
				$total_amount = 0;
				$code = $rd['product_code'];
				$qty = array();
				$amount = array();
				$i = 1;
				foreach($month as $m)
				{
					$from	= date("$year-$m-01 00:00:00");
					$to	= date("$year-$m-t 23:59:59");
					$qr = dbQuery("SELECT SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product = ".$rd['id_product']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
					if(dbNumRows($qr) == 1 )
					{
						list($qtyx, $amountx)	= dbFetchArray($qr);
						$qty[$m] 				= number_format($qtyx);
						$amount[$m] 		= number_format($amountx);
						$total_qty += $qtyx;
						$total_amount += $amountx;
						$sum_qty[$i] += $qtyx;
						$sum_amount[$i] += $amountx;
					}else{
						$qty[$m] 			= 0;
						$amount[$m] 	= 0;
						$sum_qty[$i] += 0;
						$sum_amount[$i] += 0;
					}
					$i++;
				}
				$arr = array("no"=>$n, "product"=>$code,
								"qty_1"=>$qty['01'], "qty_2"=>$qty['02'], "qty_3"=>$qty['03'], "qty_4"=>$qty['04'], "qty_5"=>$qty['05'], "qty_6"=>$qty['06'],
								"qty_7"=>$qty['07'], "qty_8"=>$qty['08'], "qty_9"=>$qty['09'], "qty_10"=>$qty['10'], "qty_11"=>$qty['11'], "qty_12"=>$qty['12'], "total_qty"=>number_format($total_qty),
								"amount_1"=>$amount['01'],"amount_2"=>$amount['02'],"amount_3"=>$amount['03'],"amount_4"=>$amount['04'], "amount_5"=>$amount['05'],"amount_6"=>$amount['06'],
								"amount_7"=>$amount['07'], "amount_8"=>$amount['08'], "amount_9"=>$amount['09'], "amount_10"=>$amount['10'], "amount_11"=>$amount['11'], "amount_12"=>$amount['12'], "total_amount"=>number_format($total_amount)
							);
				$all_qty += $total_qty;
				$all_amount += $total_amount;
				array_push($data, $arr);

				$n++;
			endwhile;
			$arr = array("no"=>"", "product"=>"Grand Total", "qty_1"=>number_format($sum_qty['1']), "qty_2"=>number_format($sum_qty['2']), "qty_3"=>number_format($sum_qty['3']), "qty_4"=>number_format($sum_qty['4']),
							"qty_5"=>number_format($sum_qty['5']), "qty_6"=>number_format($sum_qty['6']), "qty_7"=>number_format($sum_qty['7']), "qty_8"=>number_format($sum_qty['8']),
							"qty_9"=>number_format($sum_qty['9']), "qty_10"=>number_format($sum_qty['10']), "qty_11"=>number_format($sum_qty['11']), "qty_12"=>number_format($sum_qty['12']), "total_qty"=>number_format($all_qty),
							"amount_1"=>number_format($sum_amount['1']),"amount_2"=>number_format($sum_amount['2']),"amount_3"=>number_format($sum_amount['3']),"amount_4"=>number_format($sum_amount['4']),
							"amount_5"=>number_format($sum_amount['5']),"amount_6"=>number_format($sum_amount['6']), "amount_7"=>number_format($sum_amount['7']), "amount_8"=>number_format($sum_amount['8']),
							"amount_9"=>number_format($sum_amount['9']), "amount_10"=>number_format($sum_amount['10']), "amount_11"=>number_format($sum_amount['11']), "amount_12"=>number_format($sum_amount['12']),
							"total_amount"=>number_format($all_amount)
							);
			array_push($data, $arr);
			endwhile;
			echo json_encode($data);
		else:
			echo "fail";
		endif;

}


if( isset($_GET['sale_summary_by_category']) && isset($_GET['export']) )
{
	$rank		= $_GET['rank'];
	$year		= $_GET['year'];
	$data 	= array(); /// ไว้เก็บผลลัพธ์เพื่อส่งกลับ
	$arr 		= array("========================================= รายงานยอดขายแยกตามสินค้าเรียงตามหมวดหมู่สินค้าเปรียบเทียบแต่ละเดือน ===========================================");
	array_push($data, $arr);
	if($rank == 1 )
	{
		$qm = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category != 0");
		$title = array("หมวดหมู่สินค้า : ทั้งหมด   ปี : $year");
	}else if($rank == 2 ){
		$p_selected = $_POST['p_selected'];
		$in	 = "";
		$c  = count($p_selected);
		$i 	= 1;
		foreach($p_selected as $id)
		{
			$in .= $id;
			if($i < $c){ $in .=", "; }
			$i++;
		}

		$qm = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category IN($in)");
		$title	= array("หมวดหมู่สินค้า : บางรายการ   ปี : $year");
	}
	if(dbNumRows($qm) > 0 ):
		array_push($data, $title);
		$arr = array("ลำดับ", "รหัสสินค้า", "*****", "*****", "*****","*****", "*****", "*****", "จำนวนตัว", "*****", "*****", "*****", "*****", "*****", "*****",  "*****", "*****", "*****","*****", "*****", "*****", "จำนวนเงิน", "*****", "*****", "*****", "*****", "*****", "*****");
		array_push($data, $arr);
		$arr = array("", "", "Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Total",  "Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Total");
		array_push($data, $arr);

		while($rs = dbFetchArray($qm) ) :
			$arr = array($rs['category_name'] );
			array_push($data, $arr);
			$qs = dbQuery("SELECT id_product, product_code FROM tbl_product WHERE default_category_id =".$rs['id_category']." ORDER BY product_code ASC");
			$n = 1;
			$sum_qty = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "total"=>0);
			$sum_amount = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "total"=>0);
			$all_qty = 0;
			$all_amount = 0;
			while($rd = dbFetchArray($qs)) :
				$month = array("01","02","03","04","05", "06", "07","08", "09", "10", "11", "12");
				$total_qty = 0;
				$total_amount = 0;
				$code = $rd['product_code'];
				$qty = array();
				$amount = array();
				$i = 1;
				foreach($month as $m)
				{
					$from	= date("$year-$m-01 00:00:00");
					$to	= date("$year-$m-t 23:59:59");
					$qr = dbQuery("SELECT SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product = ".$rd['id_product']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
					list($qtyx, $amountx)	= dbFetchArray($qr);
					$qty[$m] 				= $qtyx == NULL? 0 : $qtyx;
					$amount[$m] 		= $amountx == NULL? 0 : $amountx;
					$total_qty 			+= $qtyx;
					$total_amount 		+= $amountx;
					$sum_qty[$i] 		+= $qtyx;
					$sum_amount[$i] 	+= $amountx;
					$i++;
				}
				$arr = array($n, $code, $qty['01'], $qty['02'], $qty['03'], $qty['04'], $qty['05'], $qty['06'], $qty['07'], $qty['08'], $qty['09'], $qty['10'], $qty['11'], $qty['12'], $total_qty,
							$amount['01'], $amount['02'], $amount['03'], $amount['04'],  $amount['05'], $amount['06'], $amount['07'], $amount['08'], $amount['09'], $amount['10'], $amount['11'], $amount['12'], $total_amount
							);
				$all_qty += $total_qty;
				$all_amount += $total_amount;
				array_push($data, $arr);

				$n++;
			endwhile;
			$arr = array("", "Grand Total", $sum_qty['1'], $sum_qty['2'], $sum_qty['3'], $sum_qty['4'], $sum_qty['5'], $sum_qty['6'], $sum_qty['7'], $sum_qty['8'], $sum_qty['9'], $sum_qty['10'], $sum_qty['11'], $sum_qty['12'], $all_qty,
					$sum_amount['1'], $sum_amount['2'], $sum_amount['3'], $sum_amount['4'], $sum_amount['5'], $sum_amount['6'], $sum_amount['7'], $sum_amount['8'], $sum_amount['9'], $sum_amount['10'], $sum_amount['11'], $sum_amount['12'],	$all_amount
							);
			array_push($data, $arr);
		endwhile;
	else :
		$arr 		= array("========================================= Error!! ===========================================");
		array_push($data, $arr);
	endif;

	$sheet_name = "Sale_summary_by_category";
	$xls = new Excel_XML('UTF-8', false, $sheet_name);
	$xls->addArray ($data);
	$xls->generateXML( $sheet_name );
	setToken($_GET['token']);
}



if( isset($_GET['sale_summary']) && isset($_GET['report']) )
{
	$rank		= $_POST['rank'];
	$p_from	= $_POST['from'];
	$p_to		= $_POST['to'];
	$year		= $_POST['year'];

	$data 	= array(); /// ไว้เก็บผลลัพธ์เพื่อส่งกลับ
	if($rank == 1 )
	{
		$qs = dbQuery("SELECT id_product, product_code FROM tbl_product ORDER BY product_code ASC");
	}else if($rank == 2 ){
		$qs = dbQuery("SELECT id_product, product_code FROM tbl_product WHERE product_code BETWEEN '".$p_from."' AND '".$p_to."' ORDER BY product_code ASC");
	}

	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$sum_qty = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0);
		$sum_amount = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0);
		$all_qty = 0;
		$all_amount = 0;
		while($rd = dbFetchArray($qs)) :
			$month = array("01","02","03","04","05", "06", "07","08", "09", "10", "11", "12");
			$total_qty = 0;
			$total_amount = 0;
			$code = $rd['product_code'];
			$qty = array();
			$amount = array();
			$i = 1;
			foreach($month as $m)
			{
				$from	= date("$year-$m-01 00:00:00");
				$to	= date("$year-$m-t 23:59:59");
				$qr = dbQuery("SELECT SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product = ".$rd['id_product']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
				if(dbNumRows($qr) == 1 )
				{
					list($qtyx, $amountx)	= dbFetchArray($qr);
					$qty[$m] 				= number_format($qtyx);
					$amount[$m] 		= number_format($amountx);
					$total_qty += $qtyx;
					$total_amount += $amountx;
					$sum_qty[$i] += $qtyx;
					$sum_amount[$i] += $amountx;
				}else{
					$qty[$m] 			= 0;
					$amount[$m] 	= 0;
					$sum_qty[$i] += 0;
					$sum_amount[$i] += 0;
				}
				$i++;
			}
			$arr = array("no"=>$n, "product"=>$code,
							"qty_1"=>$qty['01'], "qty_2"=>$qty['02'], "qty_3"=>$qty['03'], "qty_4"=>$qty['04'], "qty_5"=>$qty['05'], "qty_6"=>$qty['06'],
							"qty_7"=>$qty['07'], "qty_8"=>$qty['08'], "qty_9"=>$qty['09'], "qty_10"=>$qty['10'], "qty_11"=>$qty['11'], "qty_12"=>$qty['12'], "total_qty"=>number_format($total_qty),
							"amount_1"=>$amount['01'],"amount_2"=>$amount['02'],"amount_3"=>$amount['03'],"amount_4"=>$amount['04'], "amount_5"=>$amount['05'],"amount_6"=>$amount['06'],
							"amount_7"=>$amount['07'], "amount_8"=>$amount['08'], "amount_9"=>$amount['09'], "amount_10"=>$amount['10'], "amount_11"=>$amount['11'], "amount_12"=>$amount['12'], "total_amount"=>number_format($total_amount)
						);
			$all_qty += $total_qty;
			$all_amount += $total_amount;
			array_push($data, $arr);

			$n++;
		endwhile;
		$arr = array("no"=>"", "product"=>"Grand Total", "qty_1"=>number_format($sum_qty['1']), "qty_2"=>number_format($sum_qty['2']), "qty_3"=>number_format($sum_qty['3']), "qty_4"=>number_format($sum_qty['4']),
						"qty_5"=>number_format($sum_qty['5']), "qty_6"=>number_format($sum_qty['6']), "qty_7"=>number_format($sum_qty['7']), "qty_8"=>number_format($sum_qty['8']),
						"qty_9"=>number_format($sum_qty['9']), "qty_10"=>number_format($sum_qty['10']), "qty_11"=>number_format($sum_qty['11']), "qty_12"=>number_format($sum_qty['12']), "total_qty"=>number_format($all_qty),
						"amount_1"=>number_format($sum_amount['1']),"amount_2"=>number_format($sum_amount['2']),"amount_3"=>number_format($sum_amount['3']),"amount_4"=>number_format($sum_amount['4']),
						"amount_5"=>number_format($sum_amount['5']),"amount_6"=>number_format($sum_amount['6']), "amount_7"=>number_format($sum_amount['7']), "amount_8"=>number_format($sum_amount['8']),
						"amount_9"=>number_format($sum_amount['9']), "amount_10"=>number_format($sum_amount['10']), "amount_11"=>number_format($sum_amount['11']), "amount_12"=>number_format($sum_amount['12']),
						"total_amount"=>number_format($all_amount)
						);
		array_push($data, $arr);
		echo json_encode($data);
	}else{
		echo "fail";
	}

}


if( isset($_GET['sale_summary']) && isset($_GET['export']) )
{
	$rank		= $_GET['rank'];
	$p_from	= $_GET['from'];
	$p_to		= $_GET['to'];
	$year		= $_GET['year'];

	$data 	= array(); /// ไว้เก็บผลลัพธ์เพื่อส่งกลับ
	$arr 		= array("========================================= รายงานยอดขายแยกตามสินค้าเปรียบเทียบแต่ละเดือน ===========================================");
	array_push($data, $arr);
	if($rank == 1 )
	{
		$qs = dbQuery("SELECT id_product, product_code FROM tbl_product ORDER BY product_code ASC");
		$title = array("สินค้า : ทั้งหมด   ปี : $year ");
	}else if($rank == 2 ){
		$qs = dbQuery("SELECT id_product, product_code FROM tbl_product WHERE product_code BETWEEN '".$p_from."' AND '".$p_to."' ORDER BY product_code ASC");
		$title	= array("สินค้า : $p_from ถึง $p_to   ปี : $year");
	}
	array_push($data, $title);
	$arr = array("ลำดับ", "รหัสสินค้า", "*****", "*****", "*****","*****", "*****", "*****", "จำนวนตัว", "*****", "*****", "*****", "*****", "*****", "*****",  "*****", "*****", "*****","*****", "*****", "*****", "จำนวนเงิน", "*****", "*****", "*****", "*****", "*****", "*****");
	array_push($data, $arr);
	$arr = array("", "", "Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Total",  "Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Total");
	array_push($data, $arr);
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$sum_qty = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "total"=>0);
		$sum_amount = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "total"=>0);
		$all_qty = 0;
		$all_amount = 0;
		while($rd = dbFetchArray($qs)) :
			$month = array("01","02","03","04","05", "06", "07","08", "09", "10", "11", "12");
			$total_qty = 0;
			$total_amount = 0;
			$code = $rd['product_code'];
			$qty = array();
			$amount = array();
			$i = 1;
			foreach($month as $m)
			{
				$from	= date("$year-$m-01 00:00:00");
				$to	= date("$year-$m-t 23:59:59");
				$qr = dbQuery("SELECT SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_product = ".$rd['id_product']." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
				list($qtyx, $amountx)	= dbFetchArray($qr);
				$qty[$m] 				= $qtyx == NULL? 0 : $qtyx;
				$amount[$m] 		= $amountx == NULL? 0 : $amountx;
				$total_qty 			+= $qtyx;
				$total_amount 		+= $amountx;
				$sum_qty[$i] 		+= $qtyx;
				$sum_amount[$i] 	+= $amountx;
				$i++;
			}
			$arr = array($n, $code, $qty['01'], $qty['02'], $qty['03'], $qty['04'], $qty['05'], $qty['06'], $qty['07'], $qty['08'], $qty['09'], $qty['10'], $qty['11'], $qty['12'], $total_qty,
						$amount['01'], $amount['02'], $amount['03'], $amount['04'],  $amount['05'], $amount['06'], $amount['07'], $amount['08'], $amount['09'], $amount['10'], $amount['11'], $amount['12'], $total_amount
						);
			$all_qty += $total_qty;
			$all_amount += $total_amount;
			array_push($data, $arr);

			$n++;
		endwhile;
		$arr = array("", "Grand Total", $sum_qty['1'], $sum_qty['2'], $sum_qty['3'], $sum_qty['4'], $sum_qty['5'], $sum_qty['6'], $sum_qty['7'], $sum_qty['8'], $sum_qty['9'], $sum_qty['10'], $sum_qty['11'], $sum_qty['12'], $all_qty,
				$sum_amount['1'], $sum_amount['2'], $sum_amount['3'], $sum_amount['4'], $sum_amount['5'], $sum_amount['6'], $sum_amount['7'], $sum_amount['8'], $sum_amount['9'], $sum_amount['10'], $sum_amount['11'], $sum_amount['12'],	$all_amount
						);
		array_push($data, $arr);
	}else{
		$arr 		= array("========================================= Error!! ===========================================");
		array_push($data, $arr);
	}

	$sheet_name = "Sale_summary";
	$xls = new Excel_XML('UTF-8', false, $sheet_name);
	$xls->addArray ($data);
	$xls->generateXML($sheet_name);
	setToken($_GET['token']);
}
?>
