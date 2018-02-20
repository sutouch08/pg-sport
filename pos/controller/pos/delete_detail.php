<?php
$sc = TRUE;
$id = $_POST['id_order_pos_detail'];
$id_order = $_POST['id_order_pos'];
$order = new order_pos();

if($order->deleteDetail($id) === TRUE)
{
  $order->updateOrderAmount($id_order);
}
else
{
  $sc = FALSE;
  $message = 'ลบรายการไม่สำเร็จ';
}

echo $sc === TRUE ? 'success' : $message;
 ?>
