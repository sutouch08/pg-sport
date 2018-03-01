<?php

$code = $_GET['reference'];
$cs = new order_pos();
$cs->getDataByReference($code);
$cus = new customer();
$emp = new employee();
$sc = array(
  'customerName' => $cus->getName($cs->id_customer),
  'id_customer' => $cs->id_customer,
  'empName' => $emp->getName($cs->id_employee),
  'payment' => ($cs->id_payment == 2 ? 'บัตรเครดิต' : 'เงินสด'),
  'data' => ''
);

$ds = array();
$qr  = "SELECT opd.* FROM tbl_order_pos_detail AS opd ";
$qr .= "LEFT JOIN tbl_order_pos AS op ON opd.id_order_pos = op.id_order_pos ";
$qr .= "WHERE op.reference = '".$code."' AND opd.qty > opd.cn_qty ";
//echo $qr;
$qs = dbQuery($qr);

if(dbNumRows($qs) > 0)
{
  $no = 1;
  $totalQty = 0;
  while($rs = dbFetchObject($qs))
  {
    $arr = array(
      'no' => $no,
      'id' => $rs->id_order_pos_detail,
      'barcode' => $rs->barcode,
      'product' => $rs->product_reference.' : '.$rs->product_name,
      'price' => $rs->final_price,
      'qty' => ($rs->qty - $rs->cn_qty)
    );

    array_push($ds, $arr);
    $totalQty += ($rs->qty - $rs->cn_qty);
    $no++;
  }

  array_push($ds, array('totalQty' => $totalQty));
}
else
{
  array($ds, array('nodata' => 'nodata'));
}

$sc['data'] = $ds;
echo json_encode($sc);

 ?>
