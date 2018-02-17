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

$no = 1;
$total_qty = 0;

if( dbNumRows($qs) > 0)
{

  while($rs = dbFetchObject($qs))
  {
    $customerName = customer_name($rs->id_customer);
    $arr = array(
          "no" => $no,
          "customerName" => $customerName,
          "product" => $rs->product_reference.' : '.$rs->product_name,
          "qty" => number_format($rs->qty)
    );

    array_push($ds, $arr);

    $total_qty += $rs->qty;
    $no++;
  }
  array_push($ds, array("total_qty" => number_format($total_qty)));
}
else
{
  array_push($ds, array("total_qty" => number_format($total_qty)));
}

$sc['data'] = $ds;

echo json_encode($sc);
 ?>
