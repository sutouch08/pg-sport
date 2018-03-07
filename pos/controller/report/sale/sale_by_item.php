<?php
$from = fromDate($_GET['fromDate']);
$to = toDate($_GET['toDate']);
$ds = array();

$qr  = "SELECT * FROM tbl_order_detail_sold ";
$qr .= "WHERE id_role IN(11,12) ";
$qr .= "AND date_upd >= '".$from."' ";
$qr .= "AND date_upd <= '".$to."' ";

$qs = dbQuery($qr);
if(dbNumRows($qs) > 0)
{
  $no = 1;
  $totalQty = 0;
  $totalDisc = 0;
  $totalAmount = 0;
  while($rs = dbFetchObject($qs))
  {
    $arr = array(
      'no' => $no,
      'date' => thaiDate($rs->date_upd, '/'),
      'reference' => $rs->reference,
      'payment' => ($rs->id_payment == 2 ? 'บัตรเครดิต' : 'เงินสด'),
      'pdCode' => $rs->product_reference,
      'price' => number($rs->product_price, 2),
      'qty' => number($rs->sold_qty),
      'disc' => number($rs->discount_amount, 2),
      'amount' => number($rs->total_amount, 2)
    );

    array_push($ds, $arr);
    $no++;
    $totalQty += $rs->sold_qty;
    $totalDisc += $rs->discount_amount;
    $totalAmount += $rs->total_amount;
  }

  $arr = array(
    'totalQty' => number($totalQty),
    'totalDisc' => number($totalDisc, 2),
    'totalAmount' => number($totalAmount, 2)
  );

  array_push($ds, $arr);
}
else
{
  array_push($ds, array('nodata' => 'nodata'));
}

$sc = array(
  'fromDate' => thaiDate($from, '/'),
  'toDate' => thaiDate($to, '/'),
  'detail' => $ds
);

echo json_encode($sc);

 ?>
