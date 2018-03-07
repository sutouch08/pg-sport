<?php
$from = fromDate($_GET['fromDate']);
$to = toDate($_GET['toDate']);

$qr  = "SELECT SUM(total_amount) AS amount ";
$qr .= "FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11, 12) ";
$qr .= "AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
$qs = dbQuery($qr);
list($amount) = dbFetchArray($qs);
$totalAmount = is_null($amount) ? 0 : $amount;

$qr  = "SELECT SUM(total_amount) AS amount ";
$qr .= "FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11, 12) ";
$qr .= "AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
$qr .= "AND id_payment = 1 ";
$qs = dbQuery($qr);
list($amount) = dbFetchArray($qs);
$cashAmount = is_null($amount) ? 0 : $amount;

$qr  = "SELECT SUM(total_amount) AS amount ";
$qr .= "FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11, 12) ";
$qr .= "AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
$qr .= "AND id_payment = 2 ";
$qs = dbQuery($qr);
list($amount) = dbFetchArray($qs);
$cardAmount = is_null($amount) ? 0 : $amount;

$qr  = "SELECT SUM(sold_qty) AS amount ";
$qr .= "FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11, 12) ";
$qr .= "AND date_upd >= '".$from."' AND date_upd <= '".$to."' ";
$qs = dbQuery($qr);
list($amount) = dbFetchArray($qs);
$qty = is_null($amount) ? 0 : $amount;

$sc = array(
  'fromDate' => thaiDate($from, '/'),
  'toDate' => thaiDate($to, '/'),
  'qty' => number($qty),
  'soldAmount' => number($totalAmount, 2),
  'cashAmount' => number($cashAmount, 2),
  'cardAmount' => number($cardAmount, 2)
);

echo json_encode($sc);

 ?>
