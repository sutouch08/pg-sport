<?php
//////////////    แสดงผลรายงาน ตามเงื่อนไขที่เลือกไป /////////////////////
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
if($from_date !=="เลือกวัน" || $to_date !=="เลือกวัน"){
  $from = dbDate($from_date);
  $to = dbDate($to_date);
}else{
  $rang = getMonth();
  $to = $rang['to']." 23:59:59";
  $from = $rang['from']." 00:00:00";
}
$title = "รายงานยอดขายแยกตามพนักงานขาย วันที่ ".thaiTextDate($from)." ถึง ".thaiTextDate($to)." : ".COMPANY;
  $header = array(1=>array($title));
  $body = array(1=>array("ลำดับ","พนักงานขาย","พี้นที่การขาย","ยอดขาย"));
  $line = array(1=>array("====================================================================="));
  $sale = new sale();
  $result = $sale->saleLeaderBoard($from, $to);
  $n = 1;
  $total_amount = 0;
  foreach($result as $data){
    $salex = new sale($data['id']);
    $sale_name = $salex->full_name;
    $zone_name = $salex->group_name;
    $amount = $data['sale_amount'];
    $arr = array($n,$sale_name, $zone_name, number_format($amount,2));
    array_push($body, $arr);
    $total_amount = $total_amount+$amount;
    $n++;
  }
  $arr = array(" "," "," รวม ",number_format($total_amount,2));
  array_push($body, $arr);
  $sheet_name = "Sale_Report_BY_Employee";
  $xls = new Excel_XML('UTF-8', false, $sheet_name);
  $xls->addArray($header);
  $xls->addArray($line);
  $xls->addArray ( $body );
  $xls->generateXML("Sale_Report_BY_Employee");

 ?>
