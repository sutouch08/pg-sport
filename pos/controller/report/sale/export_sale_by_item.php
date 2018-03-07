<?php
include '../helper/report_helper.php';

$from = fromDate($_GET['fromDate']);
$to = toDate($_GET['toDate']);
$title = 'รายงานการขาย วันที่ '.thaiDate($from, '/').' - '.thaiDate($to, '/');

$qr  = "SELECT * FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11,12) ";
$qr .= "AND date_upd >= '".$from."' ";
$qr .= "AND date_upd <= '".$to."' ";

$qs = dbQuery($qr);

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->getActiveSheet()->setTitle('รายงานการขาย');

$excel->getActiveSheet()->setCellValue('A1', $title);
$excel->getActiveSheet()->mergeCells('A1:I1');

$excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
$excel->getActiveSheet()->setCellValue('B2', 'วันที่');
$excel->getActiveSheet()->setCellValue('C2', 'เลขที่');
$excel->getActiveSheet()->setCellValue('D2', 'การชำระเงิน');
$excel->getActiveSheet()->setCellValue('E2', 'สินค้า');
$excel->getActiveSheet()->setCellValue('F2', 'ราคา');
$excel->getActiveSheet()->setCellValue('G2', 'จำนวน');
$excel->getActiveSheet()->setCellValue('H2', 'ส่วนลด');
$excel->getActiveSheet()->setCellValue('I2', 'มูลค่า');

$row = 3;

if(dbNumRows($qs) > 0)
{
  $no = 1;
  while($rs = dbFetchObject($qs))
  {
    $excel->getActiveSheet()->setCellValue('A'.$row, $no);
    $excel->getActiveSheet()->setCellValue('B'.$row, thaiDate($rs->date_upd, '/'));
    $excel->getActiveSheet()->setCellValue('C'.$row, $rs->reference);
    $excel->getActiveSheet()->setCellValue('D'.$row, ($rs->id_payment == 2 ? 'บัตรเครดิต' : 'เงินสด'));
    $excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_reference);
    $excel->getActiveSheet()->setCellValue('F'.$row, $rs->product_price);
    $excel->getActiveSheet()->setCellValue('G'.$row, $rs->sold_qty);
    $excel->getActiveSheet()->setCellValue('H'.$row, $rs->discount_amount);
    $excel->getActiveSheet()->setCellValue('I'.$row, $rs->total_amount);
    $no++;
    $row++;

  }

  $rx = $row -1;

  $excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
  $excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
  $excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G3:G'.$rx.')');
  $excel->getActiveSheet()->setCellValue('H'.$row, '=SUM(H3:H'.$rx.')');
  $excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I3:I'.$rx.')');
}

setToken($_GET['token']);
$file_name = "รายงานการขาย.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
header('Content-Disposition: attachment;filename="'.$file_name.'"');
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->save('php://output');

 ?>
