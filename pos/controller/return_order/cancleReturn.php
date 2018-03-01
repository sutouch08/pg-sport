<?php
$id = $_POST['id_return_order'];
$sc = TRUE;
$cs = new return_order($id);
$order = new order_pos();
$stock = new stock();
$zone = new zone();
$mv = new movement();

$qs = $cs->getDetails($id);
if(dbNumRows($qs) > 0)
{
  startTransection();
  while($rs = dbFetchObject($qs))
  {
    //--- ถ้าพบอะไรไม่ถูกต้องออกจาก loop ทันที
    if($sc !== TRUE)
    {
      break;
    }

    //--- ยกเลิกรายการรับคืน
    if($cs->cancleDetail($rs->id) !== TRUE)
    {
      $sc = FALSE;
      $message = 'ยกเลิกรายการไม่สำเร็จ';
    }

    //--- ลดยอดรับคืนแล้วใน tbl_order_pos_detail
    if($)

  } //--- end while

  if($sc === TRUE)
  {
    commitTransection();
  }
  else
  {
    dbRollback();
  }

  endTransection();
}
else
{
  $sc = FALSE;
  $message = 'ไม่พบรายการรับคืน';
}

 ?>
