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



 ?>
