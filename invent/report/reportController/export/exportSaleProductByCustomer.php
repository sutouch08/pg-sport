<?php
$fromDate = $_GET['fromDate'];
$toDate 	= $_GET['toDate'];
$id_customer = $_GET['id_customer'];

$ds = array();
$sc = array(
        "fromDate" => $fromDate,
        "toDate" => $toDate
      );

$qr  = "SELECT id_customer, product_reference, product_name, SUM(sold_qty) AS qty FROM tbl_order_detail_sold ";
$qr .= "WHERE id_customer = ".$id_customer." ";
$qr .= "AND id_role = 1 ";
$qr .= "AND date_upd >= '".fromDate($fromDate)."' ";
$qr .= "AND date_upd <= '".toDate($toDate)."' ";
$qr .= "GROUP BY id_product_attribute ";
$qr .= "ORDER BY id_product DESC";

//echo $qr;
$qs = dbQuery($qr);

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$excel->getActiveSheet()->setTitle('รายงานสินค้าแยกตามลูกค้า');

//---------- ชื่อรายงาน  -------------//
$excel->getActiveSheet()->setCellValue('A1', 'รายงานสินค้าแยกตามลูกค้า วันที่ '.thaiDate($fromDate,'/').' ถึงวันที่ '.thaiDate($toDate, '/') );
$excel->getActiveSheet()->mergeCells('A1:E1');

//-----------  Table header ------//
$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
$excel->getActiveSheet()->setCellValue('B2', 'ลูกค้า');
$excel->getActiveSheet()->setCellValue('C2', 'รหัสสินค้า');
$excel->getActiveSheet()->setCellValue('D2', 'ชื่อสินค้า');
$excel->getActiveSheet()->setCellValue('E2', 'จำนวนขาย');

$excel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal('center');
$excel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setVertical('center');

$row = 3;

$no = 1;

if( dbNumRows($qs) > 0)
{

  while($rs = dbFetchObject($qs))
  {
    $excel->getActiveSheet()->setCellValue('A'.$row, $no);
    $excel->getActiveSheet()->setCellValue('B'.$row, customer_name($rs->id_customer));
    $excel->getActiveSheet()->setCellValue('C'.$row, $rs->product_reference);
    $excel->getActiveSheet()->setCellValue('D'.$row, $rs->product_name);
    $excel->getActiveSheet()->setCellValue('E'.$row, $rs->qty);

    $row++;
    $no++;
  }

  $excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
  $excel->getActiveSheet()->mergeCells('A'.$row.':D'.$row);
  $excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');

  $excel->getActiveSheet()->setCellValue('E'.$row,'=SUM(E2:E'.($row-1).')');
}

setToken($_GET['token']);
$file_name = "รายงานสินค้าแยกตามลูกค้า.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
header('Content-Disposition: attachment;filename="'.$file_name.'"');
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->save('php://output');

 ?>
