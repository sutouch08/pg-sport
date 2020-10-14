<?php
require '../../library/config.php';
require '../../library/functions.php';
require '../../invent/function/tools.php';


//--- เปิดเอกสารใหม่
if(isset($_GET['newBill']))
{
  include 'pos/new_bill.php';
}



//----- เพิ่มรายการขายในออเดอร์
if(isset($_GET['addToOrder']))
{
  include 'pos/add_to_order.php';
}


if(isset($_GET['payOrder']))
{
  include 'pos/pay_order.php';
}



if(isset($_GET['deleteDetail']))
{
  include 'pos/delete_detail.php';
}


if(isset($_GET['pauseBill']))
{
  include 'pos/pause_bill.php';
}


if(isset($_GET['getPauseList']))
{
  include 'pos/pause_list.php';
}


if(isset($_GET['get_product_code_and_barcode']) && isset($_REQUEST['term']))
{
  $ds = array();
  $txt = trim($_REQUEST['term']);
  $qr  = "SELECT reference, barcode ";
  $qr .= "FROM tbl_product_attribute ";
  $qr .= "WHERE reference LIKE '%{$txt}%' ";
  $qr .= "OR barcode LIKE '%{$txt}%' ";
  $qr .= "ORDER BY reference ASC ";
  $qr .= "LIMIT 50";
  $qs = dbQuery($qr);
  $row = dbNumRows($qs);
  if($row > 0)
  {
    while($rs = dbFetchObject($qs))
    {
      $ds[] = $rs->reference ." | ".(empty($rs->barcode) ? $rs->reference : $rs->barcode);
    }
  }
  else
  {
    $ds[] = 'no items';
  }

  echo json_encode($ds);
}



 ?>
