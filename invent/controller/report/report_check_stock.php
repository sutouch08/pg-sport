<?php
$id_check = $_GET['id_check'];
$check_stock = new checkstock();
$check_stock->detail($id_check);
$name_check = $check_stock->name_check;
$report_title = "รายงาน$name_check";
$qr = dbQuery("SELECT SUM(qty_before),SUM(qty_after),Product FROM stock_check WHERE id_check = $id_check GROUP BY id_product_attribute ORDER BY Product ASC");
$row = dbNumRows($qr);
$i = 0;
$n = 1;
$header = array(1=>array($report_title));
 $myarray = array(1=>array("ลำดับ","ชื่อสินค้า", "จำนวนสต็อก", "จำนวนที่เช็คได้", "ยอดต่าง"));
while($i<$row){
  list($sumqty_before,$sumqty_after,$product) = dbFetchArray($qr);
  $diff = $sumqty_after - $sumqty_before;
  $data = array($n,$product,$sumqty_before, $sumqty_after, $diff);
  array_push($myarray, $data);
  $i++;
  $n++;
}
$sheet_name = "Check_stock_report";
$xls = new Excel_XML('UTF-8', false, $sheet_name);
$xls->addArray($header);
$xls->addArray ( $myarray );
$xls->generateXML( "check_stock_report" );
 ?>
