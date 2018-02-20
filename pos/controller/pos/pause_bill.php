<?php
//---- Pause Bill
$sc = TRUE;
$id_order = $_POST['id_order'];
$cs = new order_pos($id_order);

//--- ถ้าบิลถูกยกเลิกไปแล้ว
if($cs->status == 3)
{
  $sc = FALSE;
  $message = 'บิลถูกยกเลิกไปแล้ว';
}
else
{
  if($cs->pauseBill($id_order) !== TRUE)
  {
    $sc = FALSE;
    $message = 'พักบิลไม่สำเร็จ';
  }
}


echo $sc === TRUE ? 'success' : $message;

 ?>
