<?php
$billCode = $_POST['billCode'];
$date_add = dbDate($_POST['date_add']);
$id_customer = $_POST['id_customer'];
$id_employee = getCookie('user_id');
$remark = $_POST['remark'];
$qtys = $_POST['qty'];
$sc = TRUE;

$order = new order_pos();
$cs = new return_order();
$stock = new stock();
$mv = new movement();
$zone = new zone();
$pd = new product();

$id_order = $order->getId($billCode);
$order->getData($id_order);

if($id_order == FALSE)
{
  $sc = FALSE;
  $message = 'เลขที่บิลไม่ถูกต้อง';
}
else
{
  if(empty($qtys))
  {
    $sc = FALSE;
    $message = 'ไม่มีรายการรับคืน';
  }
  else
  {
    startTransection();
    $reference = $cs->getNewReference($date_add);
    $arr = array(
      'reference' => $reference,
      'order_code' => $billCode,
      'id_customer' => $id_customer,
      'id_employee' => getCookie('user_id'),
      'date_add' => $date_add,
      'remark' => $remark
    );

    //--- เพิ่มเอกสารรับคืน
    $id = $cs->add($arr);

    if($id == FALSE)
    {
      $sc = FALSE;
      $message = 'เพิ่มเอกสารรับคืนไม่สำเร็จ '.$cs->error;
    }
    else
    {
      foreach($qtys as $id_od => $qty)
      {
        if($sc == FALSE)
        {
          break;
        }

        $rs = $order->getDetail($id_od); //--- ได้ object หรือ false;
        if($rs == FALSE)
        {
          $sc = FALSE;
          $message = 'ไม่พบรายการซื้อ';
        }
        else if($rs->is_cancle == 1)
        {
          $sc = FALSE;
          $message = 'ไม่สามารถรับคืนรายการที่มีการยกเลิกแล้วได้ : '.$rs->product_reference;
        }
        else if($qty > ($rs->qty - $rs->cn_qty))
        {
          $sc = FALSE;
          $message = 'จำนวนที่คืนมากกว่าจำนวนที่ซื้อ : '.$rs->product_reference;
        }
        else
        {

          $arr = array(
            'id_return_order' => $id,
            'id_product_attribute' => $rs->id_product_attribute,
            'product_code' => $rs->product_reference,
            'qty' => $qty,
            'price' => $rs->final_price,
            'amount' => $rs->final_price * $qty,
            'id_zone' => $rs->id_zone
          );

          //--- เพิ่มรายการรับคืนในเอกสาร
          if($cs->addDetail($arr) !== TRUE)
          {
            $sc = FALSE;
            $message = 'เพิ่มรายการไม่สำเร็จ';
          }

          //--- เพิ่มสต็อก
          if($stock->updateStockZone($rs->id_zone, $rs->id_product_attribute, $qty) !== TRUE)
          {
            $sc = FALSE;
            $message = 'ปรับปรุงสต็อกไม่สำเร็จ';
          }

          //--- บันทึก movement
          $movement = $mv->move_in($reference, $zone->getWarehouseId($rs->id_zone), $rs->id_zone, $rs->id_product_attribute, $qty, $date_add);
          if($movement !== TRUE)
          {
            $sc = FALSE;
            $message = 'บันทึก movement ไม่สำเร็จ';
          }

          //----- บันทึกขาย
          $cost = $pd->getCost($rs->id_product_attribute);
          $arr = array(
            'id_order' => 0,
            'reference' => $reference,
            'id_role' => 12,
            'id_customer' => $id_customer,
            'id_employee' => $id_employee,
            'id_sale' => 0,
            'id_product' => $rs->id_product,
            'id_product_attribute' => $rs->id_product_attribute,
            'product_name' => $rs->product_name,
            'product_reference' => $rs->product_reference,
            'barcode' => $rs->barcode,
            'product_price' => $rs->final_price,
            'order_qty' => (-1 * $qty),
            'sold_qty' => (-1 * $qty),
            'reduction_percent' => 0.00,
            'reduction_amount' => 0.00,
            'discount_amount' => 0.00,
            'final_price' => $rs->final_price,
            'total_amount' => ($rs->final_price * $qty) * (-1),
            'cost' => (-1 * $cost),
            'total_cost' => (-1) * ($qty * $cost),
            'id_payment' => $order->id_payment
          );

          if($order->sold($arr) !== TRUE)
          {
            $sc = FALSE;
            $message = 'บันทึกขายไม่สำเร็จ';
          }

          //--- ปรับปรุงยอดคืนสินค้าใน tbl_order_pos_detail
          $arr = array(
            'cn_qty' => ($qty + $rs->cn_qty)
          );

          if($order->updateDetail($rs->id_order_pos_detail, $arr) !== TRUE)
          {
            $sc = FALSE;
            $message = 'ปรับปรุงยอดคืนในรายการขายไม่สำเร็จ';
          }

        } //--- end if
      } //--- end foreach
    } //--- end if id === FALSE;

    if($sc === TRUE)
    {
      commitTransection();
    }
    else
    {
      dbRollback();
    }

    endTransection();

  } //--- end if empty qty;
} //--- end if id_order

echo $sc === TRUE ? $id : $message;

 ?>
