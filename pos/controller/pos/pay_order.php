<?php
$sc = TRUE;
$id_order = $_POST['id_order'];

//--- วิธีการชำระเงิน
//--- 1 = cash, 2 = credit card
$id_payment = $_POST['paymentMethod'];

//---- ยอดเงินที่รับมา
$payAmount = $_POST['payAmount'];

$order = new order_pos();

//--- ปรับปรุงยอดให้เป็นปัจจุบันก่อน
$order->updateOrderAmount($id_order);

//--- ดึงข้อมูลทั้งหมด
$order->getData($id_order);

$orderAmount = $order->order_amount;

if($payAmount < $orderAmount)
{
  $sc = FALSE;
  $message = 'รับเงินไม่ครบ';
}
else
{
  startTransection();

  $change = $payAmount - $orderAmount;
  $arr = array(
    'id_payment' => $id_payment,
    'received_amount' => $payAmount,
    'change_amount' => $change,
    'status' => 1,
    'is_paid' => 1
  );

  if($order->update($id_order, $arr) !== TRUE)
  {
    $sc = FALSE;
    $message = 'ชำระเงินไม่สำเร็จ';
  }
  else
  {
    //--- เตรียมข้อมูลตัดสต็อก
    $qs = $order->getDetails($id_order);

    if(dbNumRows($qs) > 0)
    {
      $id_zone = getConfig('POS_ZONE_ID');
      $stock = new stock();
      $zone = new zone($id_zone);
      $mv = new movement();
      $pd = new product();

      while($rs = dbFetchObject($qs))
      {
        //----- update stock
        if($stock->updateStockZone($id_zone, $rs->id_product_attribute, (-1 * $rs->qty)) !== TRUE)
        {
          $sc = FALSE;
          $message = 'ตัดสต็อกไม่สำเร็จ';
        }

        $arr = array(
          'id_zone' => $id_zone
        );

        if($order->updateDetail($rs->id_order_pos_detail, $arr) !== TRUE)
        {
          $sc = FALSE;
          $message = 'ปรับปรุง id_zone ในรายการขายไม่สำเร็จ';
        }

        //----- บันทึกขาย
        $cost = $pd->getCost($rs->id_product_attribute);
        $arr = array(
          'id_order' => 0,
          'reference' => $order->reference,
          'id_role' => 11,
          'id_customer' => $order->id_customer,
          'id_employee' => $order->id_employee,
          'id_sale' => 0,
          'id_product' => $rs->id_product,
          'id_product_attribute' => $rs->id_product_attribute,
          'product_name' => $rs->product_name,
          'product_reference' => $rs->product_reference,
          'barcode' => $rs->barcode,
          'product_price' => $rs->price,
          'order_qty' => $rs->qty,
          'sold_qty' => $rs->qty,
          'reduction_percent' => $rs->pdisc,
          'reduction_amount' => $rs->adisc,
          'discount_amount' => $rs->discount_amount,
          'final_price' => $rs->final_price,
          'total_amount' => $rs->total_amount,
          'cost' => $cost,
          'total_cost' => $rs->qty * $cost,
          'id_payment' => $id_payment
        );

        if($order->sold($arr) !== TRUE)
        {
          $sc = FALSE;
          $message = 'บันทึกขายไม่สำเร็จ';
        }

        //---- บันทึก $movement
        $movement = $mv->move_out($order->reference, $zone->id_warehouse, $id_zone, $rs->id_product_attribute, $rs->qty, $order->date_add);
        if($movement !== TRUE)
        {
          $sc = FALSE;
          $message = 'บันทึก movement ไม่สำเร็จ';
        }

      } //--- end while

    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบรายการขาย';
    }


  } //--- end if $order->update

  if($sc === TRUE)
  {
    commitTransection();
  }
  else
  {
    dbRollback();
  }

  endTransection();

  echo $sc === TRUE ? 'success' : $message;
}



 ?>
