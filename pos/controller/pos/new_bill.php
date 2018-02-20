<?php
$sc = TRUE;
$order = new order_pos();
$id = $order->getNotSaveId();

if($id === FALSE)
{
  $arr = array(
    'id_customer' => 0,
    'id_employee' => getCookie('user_id'),
    'reference' => $order->getNewReference(),
    'pos_id' => getConfig('POS_ID')
  );

  $id = $order->add($arr);
  if($id === FALSE)
  {
    $sc = FALSE;
    $message = $order->dbError();
  }
}

echo $sc === TRUE ? $id : $message;
 ?>
