<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";

if( isset( $_GET['pdbcd'] ) && isset( $_GET['report'] ) )
{
	$cus_range 	= $_POST['cus_range'];
	$item_range		= $_POST['item_range'];
	$id_cus			= $_POST['id_customer'];
	$id_pa			= $_POST['id_product_attribute'];
	$id_product		= $_POST['id_product'];
	$from				= fromDate($_POST['from_date']);
	$to				= toDate($_POST['to_date']);
	$role 				= $_POST['role'] == 0 ? "1,4,5" : $_POST['role'];
	$in					= $item_range == 2 ? id_product_attribute_in_product($id_product) : "";
	$data				= array();	
	if( $cus_range == 0 && $item_range == 0 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";
	}
	else if( $cus_range == 0 && $item_range == 1 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_product_attribute = ".$id_pa." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";
	}
	else if( $cus_range == 0 && $item_range == 2 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_product_attribute IN(".$in.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 0 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 1 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND id_product_attribute = ".$id_pa." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 2 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND id_product_attribute IN(".$in.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	$qs = dbQuery($sql);
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		$total_qty = 0;
		$total_amount = 0;
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array(
						"no"			=> $no,
						"date"			=> thaiDate($rs['date_upd']),
						"customer"	=> customer_name($rs['id_customer']),
						"item"			=> get_product_reference( $rs['id_product_attribute'] )." : ".get_product_name( $rs['id_product'] ),
						"qty"			=> number_format($rs['sold_qty']),
						"amount"		=> number_format($rs['total_amount'], 2),
						"reference"	=> $rs['reference']
					);
			array_push($data, $arr);
			$no++;	
			$total_qty += $rs['sold_qty'];
			$total_amount += $rs['total_amount'];
		}	
		$arr = array("total_qty" 	=> number_format($total_qty), "total_amount" => number_format($total_amount, 2) );
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent" => "nocontent");
		array_push($data, $arr);	
	}
	echo json_encode($data);
}

if( isset( $_GET['pdbcd'] ) && isset( $_GET['export'] ) )
{
	$cus_range 	= $_POST['cus_range'];
	$item_range		= $_POST['item_range'];
	$id_cus			= $_POST['id_customer'];
	$id_pa			= $_POST['id_item'];
	$id_product		= $_POST['id_product'];
	$from				= fromDate($_POST['from_date']);
	$to				= toDate($_POST['to_date']);
	$role 				= $_POST['role'] == 0 ? "1,4,5" : $_POST['role'];
	$in					= $item_range == 2 ? id_product_attribute_in_product($id_product) : "";
	
	if( $cus_range == 0 && $item_range == 0 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";
	}
	else if( $cus_range == 0 && $item_range == 1 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_product_attribute = ".$id_pa." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";
	}
	else if( $cus_range == 0 && $item_range == 2 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_product_attribute IN(".$in.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 0 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 1 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND id_product_attribute = ".$id_pa." AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	else if( $cus_range == 1 && $item_range == 2 )
	{
		$sql = "SELECT * FROM tbl_order_detail_sold WHERE id_role IN(".$role.") AND id_customer = ".$id_cus." AND id_product_attribute IN(".$in.") AND (date_upd BETWEEN '".$from."' AND '".$to."') ORDER BY id_customer ASC , id_product ASC";	
	}
	
	$cus_title 	= $cus_range == 0 ? 'ลูกค้า : ทั้งหมด' : 'ลูกค้า : '.customer_name($id_cus);
	if( $item_range == 0 ){ $item_title = 'สินค้า : ทั้งหมด'; }else if( $item_range == 1 ){ $item_title = 'สินค้า : '.get_product_reference($id_pa); }else if( $item_range == 2 ){ $item_title = 'สินค้า : '.get_product_code($id_product); }
	$date_title	= 'วันที่ : '.thaiDate($from, '/').' - '.thaiDate($to, '/');
	
		
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("PDBCD");
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth(8);
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth(25);
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	
	$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
	$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้า แยกตามลูกค้า แสดงเลขที่เอกสาร'); 
	$excel->getActiveSheet()->mergeCells('A1:H1');
	$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
	
	$excel->getActiveSheet()->setCellValue('A2', $cus_title);
	$excel->getActiveSheet()->mergeCells('A2:C2');
	$excel->getActiveSheet()->setCellValue('D2', $item_title);
	$excel->getActiveSheet()->mergeCells('D2:E2');
	$excel->getActiveSheet()->setCellValue('F2', $date_title);
	$excel->getActiveSheet()->mergeCells('F2:H2');
	
	$excel->getActiveSheet()->setCellValue('A4', 'ลำดับ')
									->setCellValue('B4', 'วันที่')
									->setCellValue('C4', 'ลูกค้า')
									->setCellValue('D4', 'รหัสสินค้า')
									->setCellValue('E4', 'ชื่อสินค้า')
									->setCellValue('F4', 'จำนวน')
									->setCellValue('G4', 'มูลค่า')
									->setCellValue('H4', 'เลขที่เอกสาร');
										
	
	$qs = dbQuery($sql);

	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		$row = 5;
		while( $rs = dbFetchArray($qs) )
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $no)
											->setCellValue('B'.$row, thaiDate($rs['date_upd'], '/'))
											->setCellValue('C'.$row, customer_name($rs['id_customer']))
											->setCellValue('D'.$row, get_product_reference($rs['id_product_attribute']))
											->setCellValue('E'.$row, get_product_name( $rs['id_product'] ) )
											->setCellValue('F'.$row, $rs['sold_qty'])
											->setCellValue('G'.$row, $rs['total_amount'])
											->setCellValue('H'.$row, $rs['reference']);
			$row++;
			$no++;	
		}	
		$rx = $row -1;
		$excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
		$excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
		$excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal("right");
		$excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$rx.')')->setCellValue('G'.$row, '=SUM(G5:G'.$rx.')');
		
		$excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
		$excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		$excel->getActiveSheet()->getStyle('A4:H'.$row)->getBorders()->getAllBorders()->setBorderStyle('thin');
	}
	setToken($_GET['token']);
	$file_name = 'Product_by_customer_with_document.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header("Content-Disposition: attachment; filename='".$file_name."'");
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save("php://output");	
}


?>