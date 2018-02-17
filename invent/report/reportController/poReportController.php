<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";
require "../../function/report_helper.php";
require "../../function/po_helper.php";

//-------------------------------------( รายงานสินค้าค้างรับ แยกตามผู้ขาย แสดงใบสั่งซื้อ ) ----------------------------------//
//------------ Get Report product backlog 
if( isset( $_GET['productBacklog'] ) && isset( $_GET['report'] ) )
{
	if( $_POST['viewOption'] == 1 )
	{
		include 'report/reportProductBacklogByItems.php';	
	}
	else
	{
		include 'report/reportProductBacklogByProducts.php';	
	}
}


if( isset( $_GET['productBacklog'] ) && isset( $_GET['export'] ) )
{
	if( $_GET['viewOption'] == 1 )
	{
		include 'export/exportProductBacklogByItems.php';	
	}
	else
	{
		include 'export/exportProductBacklogByProducts.php';	
	}	
}


//----------- Get Report product backlog by supplier -----------------------//
if( isset( $_GET['productBacklogBySupplier'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportProductBacklogBySupplier.php';
}

//----------- Export Report to Excel file (รายงานสินค้าค้างส่งแยกตามผู้ขาย )
if( isset( $_GET['productBacklogBySupplier'] ) && isset( $_GET['export'] ) )
{
	include 'export/exportProductBacklogBySupplier.php';	
}


//---------------------------------------------( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )------------------------------------//
//----- Get Report product backlog by product ( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )
if( isset( $_GET['product_backlog_by_product'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportProductBacklogByProduct.php';	
}

//-----  Export Report to Excel file ( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )
if( isset( $_GET['product_backlog_by_product'] ) && isset( $_GET['export'] ) )
{
	include 'export/exportProductBacklogByProduct.php';
}

//------ Get Backlog details ( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )
if( isset( $_GET['product_backlog_by_product'] ) && isset( $_GET['detail'] ) )
{
	include 'detail/detailProductBacklogByProduct.php';	
}

//------ Print report result ( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )
if( isset( $_GET['product_backlog_by_product'] ) && isset( $_GET['printReport'] ) )
{
	include 'print/printProductBacklogByProduct.php';
}

//------- Print Detail Report  ( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )
if( isset( $_GET['product_backlog_by_product'] ) && isset( $_GET['printDetail'] ) )
{
	include 'print/printDetailProductBacklogByProduct.php';	
}
//---------------------------------------------( รายงานสินค้าค้างรับแยกตามรุ่นสินค้า )------------------------------------//



//--------------------------------------------( รายงานสินค้าค้างรับแยกตามรายการสินค้า )---------------------------------//
//--------Get product backlog by item report ( รายงานสินค้าค้างรับแยกตามรายการสินค้า )
if( isset( $_GET['product_backlog_by_item'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportProductBacklogByItem.php';
}

//--------Export product backlog by item report ( รายงานสินค้าค้างรับแยกตามรายการสินค้า )
if( isset( $_GET['product_backlog_by_item'] ) && isset( $_GET['export'] ) )
{
	include 'export/exportProductBacklogByItem.php';
}

//------ Get Backlog Detail ( รายงานสินค้าค้างรับแยกตามรายการสินค้า )
if( isset( $_GET['product_backlog_by_item'] ) && isset( $_GET['detail'] ) )
{
	include 'detail/detailProductBacklogByItem.php';	
}

//------ Print Report Backlog ( รายงานสินค้าค้างรับแยกตามรายการสินค้า )
if( isset( $_GET['product_backlog_by_item'] ) && isset( $_GET['printReport'] ) )
{
	include 'print/printProductBacklogByItem.php';	
}

//------ Print Detail Backlog ( รายงานสินค้าค้างรับแยกตามรายการสินค้า )
if( isset( $_GET['product_backlog_by_item'] ) && isset( $_GET['printDetail'] ) )
{
	include 'print/printDetailProductBacklogByItem.php';	
}
//--------------------------------------------( รายงานสินค้าค้างรับแยกตามรายการสินค้า )---------------------------------//



if( isset( $_GET['get_po_list'] ) && isset( $_POST['id_product_attribute'] ) && isset( $_POST['id_sup'] ) )
{
	$id_sup	= $_POST['id_sup'];
	$id 		= $_POST['id_product_attribute'];
	$from		= fromDate($_POST['from']);
	$to 		= toDate($_POST['to']);
	$data 	= array();
	if( $id_sup != 0 )
	{
		$qs = dbQuery("SELECT tbl_po_detail.id_po, reference, tbl_po.date_add, id_supplier, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND id_supplier = ".$id_sup." AND (tbl_po.date_add BETWEEN '".$from."' AND '".$to."') GROUP BY tbl_po.id_po ORDER BY tbl_po.date_add DESC");	
		
	}
	else
	{
		$qs = dbQuery("SELECT tbl_po_detail.id_po, reference, tbl_po.date_add, id_supplier, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND (tbl_po.date_add BETWEEN '".$from."' AND '".$to."') GROUP BY tbl_po.id_po ORDER BY tbl_po.date_add DESC");	
	}
	if( dbNumRows($qs) > 0 )
	{
		$total_qty 			= 0;
		$total_received		= 0;
		$total_backlog		= 0;
		while($rs = dbFetchArray($qs) )
		{
			$backlog		= get_item_po_backlog($rs['id_po'], $id);
			$arr = array(
						"reference" 	=> $rs['reference'],
						"date_add"	=> thaiDate($rs['date_add']),
						"sup_name"	=> supplier_name($rs['id_supplier']),
						"qty"			=> number_format($rs['qty']),
						"received"	=> number_format($rs['received']),
						"backlog"		=>	 number_format($backlog)
						);
			array_push($data, $arr);
			$total_qty 			+= $rs['qty'];
			$total_received 	+= $rs['received'];
			$total_backlog		+= $backlog;
		}
		$arr = array(
					"total_qty"		=> number_format($total_qty),
					"total_received"	=> number_format($total_received),
					"total_backlog"	=> number_format($total_backlog)
					);
		array_push($data, $arr);
	}
	else
	{
		$arr = array(
						"reference" 	=> "-",
						"date_add"	=> "-",
						"sup_name"	=> "-",
						"qty"			=> 0,
						"received"	=> 0,
						"backlog"		=>0
						);
			array_push($data, $arr);
		$arr = array(
					"total_qty"		=> 0,
					"total_received"	=> 0,
					"total_backlog"	=> 0
					);
		array_push($data, $arr);
	}
	echo json_encode($data);		
}



if( isset( $_GET['po_by_product'] ) && isset( $_GET['report'] ) )
{
	$id_product 	= $_POST['id_product'];
	$sup_rank		= $_POST['sup_rank'];
	$id_sup			= $_POST['id_sup'];
	$from				= fromDate($_POST['from']);
	$to				= toDate($_POST['to']);	
	$data 			= array();
	$qr 	= "SELECT id_product_attribute, reference FROM tbl_product_attribute ";
	$qr 	.= "LEFT JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qr	.= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr 	.= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qr 	.= "WHERE id_product = ".$id_product." ORDER BY tbl_color.color_code, tbl_size.position, tbl_attribute.position ASC";
	$qs = dbQuery($qr);
	//$qs = dbQuery("SELECT id_product_attribute, reference FROM tbl_product_attribute LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." ORDER BY id_color ASC, position ASC");
	if( dbNumRows($qs) > 0 )/// if 1
	{
		$no = 1;
		while( $rs = dbFetchArray($qs) )
		{
			$id = $rs['id_product_attribute'];
			if( $sup_rank == 2 )
			{
				$qr = dbQuery("SELECT SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND id_supplier = ".$id_sup." AND ( tbl_po.date_add BETWEEN '".$from."' AND '".$to."')");	
			}
			else
			{
				$qr = dbQuery("SELECT SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND ( tbl_po.date_add BETWEEN '".$from."' AND '".$to."')");
			}
			
			$rd = dbFetchArray($qr);
			$arr = array(
								"no"							=> $no,
								"id_product_attribute" 	=> $id, 
								"product"						=>	$rs['reference'], 
								"qty" 							=> number_format($rd['qty']), 
								"received" 					=> number_format($rd['received']), 
								"backlog"						=> number_format(sum_backlog($id, $id_sup, $from, $to)),
								"id_sup"						=> $sup_rank == 2 ? $id_sup : 0,
								"content"						=> $rd['qty'] > 0 ? "content" : ""
								);
			array_push($data, $arr);
			$no++;
		}/// end while
			
	}/// end if 1
	
	echo json_encode($data);	
}

if( isset( $_GET['check_product_code'] ) && isset( $_POST['product_code'] ) )
{
	$id_product = 0;
	$product_code = trim($_POST['product_code']);
	$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code = '".$product_code."'");
	if( dbNumRows($qs) == 1 )
	{
		list($id_product) = dbFetchArray($qs);
	}
	echo $id_product;
}

//********************** รายงานใบสั่งซื้อค้างรับ แยกตามใบสั่งซื้อ  ************************//
if( isset( $_GET['po_backlog'] ) && isset( $_GET['report'] ) )
{
	/// POST DATA = data:{"sup_rank" : sup_rank, "time_rank" : time_rank, "id_sup" : id_sup, "from" : from, "to" : to }
	$sup_rank 	= $_POST['sup_rank'];
	$rank			= $_POST['time_rank'];
	$data			= array();
	if($sup_rank == 1 && $rank == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 ");		
	}
	else if( $sup_rank == 1 && $rank == 2)
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND ( date_add BETWEEN '".fromDate($_POST['from'], true)."' AND '".toDate($_POST['to'], true)."')");
	}
	else if( $sup_rank == 2 && $rank == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND id_supplier = ".$_POST['id_sup']);
	}
	else if( $sup_rank == 2 && $rank == 2 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND id_supplier = ".$_POST['id_sup']." AND ( date_add BETWEEN '".fromDate($_POST['from'], true)."' AND '".toDate($_POST['to'], true)."')");
	}
	
	if(dbNumRows($qs) > 0 ) :
		$n = 1;
		$po = new po();
		$total_qty = 0; $total_received = 0;
		while($rs = dbFetchArray($qs) ) :
			$qty 			= $po->total_qty($rs['id_po']);
			$received 	= $po->po_received_qty($rs['id_po']);
			$arr 			= array(
									"no" 			=> $n,
									"id"	 			=> $rs['id_po'],
									"date_add" 	=> thaiDate($rs['date_add']),
									"reference" 	=> $rs['reference'],
									"supplier" 	=> supplier_name($rs['id_supplier']),
									"due_date" 	=> thaiDate($rs['due_date']),
									"received" 	=> number_format($received),
									"qty"			=> number_format($qty),
									"backlog" 	=> number_format($qty - $received),
									"btn"			=>"<button type='button' class='btn btn-info btn-xs btn-block' onclick='view_po(".$rs['id_po'].")'><i class='fa fa-eye'></i></button>"
									);
			array_push($data, $arr);
			$n++;		$total_qty += $qty; $total_received += $received;					
		endwhile;
		$arr = array(
						"no" 			=> "",
						"id"	 			=> "",
						"date_add" 	=> "",
						"reference" 	=> "",
						"supplier" 	=> "",
						"due_date" 	=> "รวม",
						"received" 	=> number_format($total_received),
						"qty"			=> number_format($total_qty),
						"backlog" 	=> number_format($total_qty - $total_received),
						"btn"			=>""
						);
		array_push($data, $arr);
		echo json_encode($data);
	else :
		echo "nodata";
	endif;
}
//********************** จบรายงานใบสั่งซื้อค้างรับ แยกตามใบสั่งซื้อ  ************************//

//********************** รายงานใบสั่งซื้อค้างรับ แยกตามใบสั่งซื้อ EXport to Excel ************************//
if( isset( $_GET['po_backlog'] ) && isset( $_GET['export'] ) )
{
	/// get DATA = data:{"sup_rank" : sup_rank, "time_rank" : time_rank, "id_sup" : id_sup, "from" : from, "to" : to }
	$sup_rank 	= $_GET['sup_rank'];
	$rank			= $_GET['time_rank'];
	$data			= array();
	$po			= new po();
	if($sup_rank == 1 && $rank == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 ");		
	}
	else if( $sup_rank == 1 && $rank == 2)
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND ( date_add BETWEEN '".fromDate($_GET['from'], true)."' AND '".toDate($_GET['to'], true)."')");
	}
	else if( $sup_rank == 2 && $rank == 1 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND id_supplier = ".$_GET['id_sup']);
	}
	else if( $sup_rank == 2 && $rank == 2 )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE valid = 0 AND status != 0 AND id_supplier = ".$_GET['id_sup']." AND ( date_add BETWEEN '".fromDate($_GET['from'], true)."' AND '".toDate($_GET['to'], true)."')");
	}
	$sup = $sup_rank == 1 ? "ทั้งหมด" : $po->supplier_code($_GET['id_sup'])." : ".$po->supplier_name($_GET['id_sup']);
	$time = $rank == 1 ? "ทั้งหมด" : "วันที่ ".$_GET['from']." ถึง ".$_GET['to'];
	
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("ใบสั่งซื้อค้างรับ");
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth("8");
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth("15");
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth("15");
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth("40");
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth("15");
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth("10");
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth("10");
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth("10");
	$excel->getActiveSheet()->getColumnDimension("I")->setWidth("10");
	$excel->getActiveSheet()->getColumnDimension("J")->setWidth("10");
	$excel->getActiveSheet()->getColumnDimension("K")->setWidth("10");
	$excel->getActiveSheet()->setCellValue("A1", "รายงานใบสั่งซื้อค้างรับ แยกตามใบสั่งซื้อ ณ วันที่ ".date("d/m/Y"));
	$excel->getActiveSheet()->mergeCells("A1:K1");
	$excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal("center")->setVertical("center");
	$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
	$excel->getActiveSheet()->getRowDimension(4)->setRowHeight(25);
	$excel->getActiveSheet()->setCellValue("A2", "ผู้ขาย")->setCellValue("B2", $sup)->setCellValue("G2", "ช่วงวันที่")->setCellValue("H2", $time);
	$excel->getActiveSheet()->mergeCells("B2:E2")->mergeCells("H2:K2");
	$excel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal("right")->setVertical("center");
	$excel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal("center")->setVertical("center");
	$excel->getActiveSheet()->getStyle("G2")->getAlignment()->setHorizontal("right")->setVertical("center");
	$excel->getActiveSheet()->getStyle("H2")->getAlignment()->setHorizontal("center")->setVertical("center");
	$excel->getActiveSheet()->getStyle("A2:K2")->getBorders()->getBottom()->setBorderStyle("double");
	
	$excel->getActiveSheet()->setCellValue("A4", "ลำดับ")
									->setCellValue("B4", "วันที่")
									->setCellValue("C4", "เลขที่เอกสาร")
									->setCellValue("D4", "ผู้ขาย")
									->setCellValue("E4", "กำหนดรับ")
									->setCellValue("F4", "จำนวน")
									->setCellValue("G4", "มูลค่าสั่งซื้อ")
									->setCellValue("H4", "รับแล้ว")
									->setCellValue("I4", "มูลค่ารับ")
									->setCellValue("J4", "ค้างรับ")
									->setCellValue("K4", "มูลค่าค้างรับ");
	$excel->getActiveSheet()->getStyle("A4:K4")->getAlignment()->setHorizontal("center");		
							
	if(dbNumRows($qs) > 0 )
	{
		$row = 5;
		$n = 1;
		while($rs = dbFetchArray($qs) ) 
		{
			$ds 	= get_po_backlog_data($rs['id_po']);
			$excel->getActiveSheet()->setCellValue("A".$row, $n)
											->setCellValue("B".$row, thaiDate($rs['date_add'], "/"))
											->setCellValue("C".$row, $rs['reference'])
											->setCellValue("D".$row, supplier_name($rs['id_supplier']))
											->setCellValue("E".$row, thaiDate($rs['due_date'], "/"))
											->setCellValue("F".$row, $ds['po_qty'])
											->setCellValue("G".$row, $ds['po_amount'])
											->setCellValue("H".$row, $ds['received_qty'])
											->setCellValue("I".$row, $ds['received_amount'])
											->setCellValue("J".$row, $ds['backlog_qty'])
											->setCellValue("K".$row, $ds['backlog_amount']);
			
			$n++;	 $row++;					
		}
		$rx = $row -1;
		$excel->getActiveSheet()->setCellValue("A".$row, "รวม");
		$excel->getActiveSheet()->mergeCells("A".$row.":E".$row);
		$excel->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal("right");
		$excel->getActiveSheet()->setCellValue("F".$row, "=SUM(F5:F".$rx.")");
		$excel->getActiveSheet()->setCellValue("G".$row, "=SUM(G5:G".$rx.")");
		$excel->getActiveSheet()->setCellValue("H".$row, "=SUM(H5:H".$rx.")");
		$excel->getActiveSheet()->setCellValue("I".$row, "=SUM(I5:I".$rx.")");
		$excel->getActiveSheet()->setCellValue("J".$row, "=SUM(J5:J".$rx.")");
		$excel->getActiveSheet()->setCellValue("K".$row, "=SUM(K5:K".$rx.")");
		
		$excel->getActiveSheet()->getStyle("F5:K".$row)->getNumberFormat()->setFormatCode("#,##0.00");
		$excel->getActiveSheet()->getStyle("A".$row.":K".$row)->getBorders()->getBottom()->setBorderStyle("double");
	}
	//echo "<pre>"; print_r($excel); echo "</pre>";
	
	setToken($_GET['token']);
	
	$file_name = "PO_Backlog.xlsx";
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");	
}
//********************** จบรายงานใบสั่งซื้อค้างรับ แยกตามใบสั่งซื้อ EXport to Excel ************************//

//*********************  รายละเอียดในใบสั่งซื้อ แสดงรายการ ยอดสั่ง รับแล้ว ค้างรับ *******//
if( isset( $_GET['get_po_detail'] ) && isset( $_GET['id_po'] ) )
{
	$id_po 	= $_GET['id_po'];
	$po 		= new po($id_po);
	$data		= "";
	$qs		= $po->get_detail($id_po);
	if( dbNumRows($qs) > 0 ) :
		$data		.= "<div class='row'>";
		$data 	.= "<div class='col-lg-2'><label>เลขที่เอกสาร</label><span class='form-control input-sm' style='border: 0px; padding-left: 0px;'>".$po->reference."</span></div>";
		$data 	.= "<div class='col-lg-2'><label>วันที่เอกสาร</label><span class='form-control input-sm' style='border: 0px; padding-left: 0px;'>".thaiDate($po->date_add)."</span></div>";
		$data 	.= "<div class='col-lg-2'><label>รหัสผู้ขาย</label><span class='form-control input-sm' style='border: 0px; padding-left: 0px;'>".$po->supplier_code($po->id_supplier)."</span></div>";
		$data 	.= "<div class='col-lg-4'><label>ชื่อผู้ขาย</label><span class='form-control input-sm' style='border: 0px; padding-left: 0px;'>".$po->supplier_name($po->id_supplier)."</span></div>";
		$data 	.= "<div class='col-lg-2'><label>กำหนดรับสินค้า</label><span class='form-control input-sm' style='border: 0px; padding-left: 0px;'>".thaiDate($po->due_date)."</span></div>";
		$data 	.= "</div>"; // row
		$data 	.= "<hr class='hr' />";
		$data		.= "<table class='table table-striped table-bordered' style='margin-bottom:0px;'>";
		$data 	.= "<thead>";
		$data 	.= "<th style='width: 5%; text-align:center;'>ลำดับ</th>";
		$data 	.= "<th style='width: 15%; text-align:center;'>รหัสสินค้า</th>";
		$data 	.= "<th style='text-align:center;'>รายละเอียด</th>";
		$data 	.= "<th style='width: 10%; text-align:center;'>จำนวน</th>";
		$data 	.= "<th style='width: 10%; text-align:center;'>รับแล้ว</th>";
		$data 	.= "<th style='width: 10%; text-align:center;'>ค้างรับ</th>";
		$data 	.= "</thead>";
		$n 	= 1;
		$total_qty = 0; $total_received = 0;
		while($rs = dbFetchArray($qs) ) :
			$dis    = $po->getDiscount($rs['discount_percent'], $rs['discount_amount']);
			$backlog = $rs['received'] >= $rs['qty'] ? 0 : $rs['qty'] - $rs['received'];
			$data .= "<tr style='font-size=12px'>";
			$data .= "<td align='center'>".$n."</td>";
			$data .= "<td>".get_product_reference($rs['id_product_attribute'])."</td>";
			$data .= "<td>".get_product_name($rs['id_product'])."</td>";
			$data .= "<td align='right'>".ac_format($rs['qty'])."</td>";
			$data .= "<td align='center'>".ac_format($rs['received'])."</td>";
			$data .= "<td align='center'>". ac_format($backlog)."</td>";		
			$data .= "</tr>";	
			$n++; $total_qty += $rs['qty']; $total_received += $rs['received'];
		endwhile;
		$data .= "<tr style='font-size=12px'>";
		$data .= "<td colspan='3' align='right' style='padding-right: 10px;'>รวมทั้งหมด</td>";
		$data .= "<td align='right'>".ac_format($total_qty)."</td>";
		$data .= "<td align='center'>".ac_format($total_received)."</td>";
		$data .= "<td align='center'>".ac_format($total_qty - $total_received)."</td>";		
		$data .= "</tr>";	
		$data .= "</table>";
	else : 
		$data = "<div class='col-lg-12' style='text-align:center'><center><h4>ไม่พบข้อมูล</h4></center></div>";
	endif;
		echo $data;	
		
}

//*********************  รายละเอียดในใบสั่งซื้อ แสดงรายการ ยอดสั่ง รับแล้ว ค้างรับ *******//

//*********************  รายละเอียดในใบสั่งซื้อ แสดงรายการ ยอดสั่ง รับแล้ว ค้างรับ  Export to Excel *******//
if( isset( $_GET['export_po_detail'] ) && isset( $_GET['id_po'] ) )
{
	$id_po 	= $_GET['id_po'];
	$po 		= new po($id_po);
	$data		= array();
	$qs		= $po->get_detail($id_po);
	if( dbNumRows($qs) > 0 ) :
		$arr	= array("******************* รายงานใบสั่งซื้อค้างรับ *******************");
		array_push($data, $arr);
		$arr 	= array("", "เลขที่เอกสาร", $po->reference , "ผู้ขาย", $po->supplier_code($po->id_supplier), $po->supplier_name($po->id_supplier));
		array_push($data, $arr);
		$arr 	= array("", "วันที่เอกสาร",thaiDate($po->date_add), "กำหนดรับสินค้า", thaiDate($po->due_date));
		array_push($data, $arr);
		$arr 	= array("------------------------------------------------------------------------------------------------------------------");
		array_push($data, $arr);
		$arr 	= array("ลำดับ", "รหัสสินค้า", "รายละเอียด", "จำนวน", "รับแล้ว", "ค้างรับ");
		array_push($data, $arr);
		$n 		= 1;
		$total_qty = 0; $total_received = 0;
		while($rs = dbFetchArray($qs) ) :
			$backlog = $rs['received'] >= $rs['qty'] ? 0 : $rs['qty'] - $rs['received'];
			$arr = array($n, get_product_reference($rs['id_product_attribute']), get_product_name($rs['id_product']), $rs['qty'], $rs['received'], $backlog);
			array_push($data, $arr);	
			$n++; $total_qty += $rs['qty']; $total_received += $backlog;
		endwhile;
		$arr 	= array("", "", "รวมทั้งหมด", $total_qty, $total_received, $total_qty - $total_received);
		array_push($data, $arr);
	else : 
		$arr = array("-------------- ไม่มีข้อมูล -----------------");
		array_push($data, $arr);
	endif;
	
	$excel = new Excel_XML("UTF-8", false, "PO_detail_backlog");
	$excel->addArray($data);
	$excel->generateXML("PO_detail_backlog");		
	setToken($_GET['token']);
}

//*********************  รายละเอียดในใบสั่งซื้อ แสดงรายการ ยอดสั่ง รับแล้ว ค้างรับ Export to Excel  *******//

//****************************  พิมพ์  ****************************//
if( isset( $_GET['print_po']) && isset( $_GET['id_po'] ) )
{
	$id_po			= $_GET['id_po'];
	$po 				= new po($id_po);
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("รายงานใบสั่งซื้อค้างรับ");
	$header			= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "รหัสผู้ขาย"=>supplier_code($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date), "ชื่อผู้ขาย"=>supplier_name($po->id_supplier));
	$print->add_header($header);
	$detail			= $po->get_detail($id_po);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>1, "header_rows"=>3, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	$total_qty 		= 0;
	$total_received	= 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("รหัส", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("รายละเอียด", "width:50%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("รับแล้ว", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ค้างรับ", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้จัดทำ", "","วันที่............................."), 
						array("ผู้ตรวจสอบ", "","วันที่............................."),
						array("ผู้อนุมัติ", "","วันที่.............................")
						);						
	$print->set_footer($footer);		
	
	$n = 1;
	while($total_page > 0 )
	{
		echo $print->page_start();
			echo $print->top_page();
			echo $print->content_start();
				echo $print->table_start();
				$i = 0;
				$product = new product();
				while($i<$row) : 
					$rs = dbFetchArray($detail);
					if(count($rs) != 0) :
						$product->product_attribute_detail($rs['id_product_attribute']);
						$id_product 		= $product->id_product; //$product->getProductId($rs['id_product_attribute']);
						$product_code 	= $product->product_reference($rs['id_product_attribute']);
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$product->product_name($id_product)."' />";
						$data 				= array($n, $product_code, $product_name, ac_format($rs['qty']), ac_format($rs['received']), ac_format($rs['qty'] - $rs['received']) );
						$total_qty 			+= $rs['qty'];
						$total_received		+= $rs['received'];
					else :
						$data = array("", "", "<input type='text' style='border:0px; width:100%' />", "","", "");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty = number_format($total_qty);
					$received = number_format($total_received);
					$backlog = number_format($total_qty - $total_received);
				}else{ 
					$qty = ""; 
					$received = "";
					$backlog = "";
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; border-bottom-left-radius:10px; width:70%; text-align:right;'>**** รวม****</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; text-align:center;'>".$qty."</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; text-align:center;'>".$received."</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:center;'>".$backlog."</td>")
						);
			echo $print->print_sub_total($sub_total);				
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}
?>