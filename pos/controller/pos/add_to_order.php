<?php
$sc = TRUE;
$id_order = $_POST['id_order'];
$barcode = trim($_POST['barcode']);
$qty = $_POST['qty'];
$pdisc = $_POST['pdisc'];
$adisc = $_POST['adisc'];
$price = $_POST['price'];

$order = new order_pos();
$product  = new product();
$pd = $product->getProductAttributeByBarcode($barcode);

$price = $price != '' ? $price : $pd->price;

if($pd === FALSE)
{
  $sc = FALSE;
  $message = 'บาร์โค้ดไม่ถูกต้อง';
}
else
{
  //--- ถ้ามีอยู่แล้วจะได้ row เป็น object กลับมา หากไม่มี จะได้ FALSE;
  $ds = $order->getExistsDetail($id_order, $pd->id_product_attribute, $price, $pdisc, $adisc);
  if($ds == FALSE)
  {
    //---- ไม่มีรายการอยู่
    $final_price = $price;

    //--- เอาส่วนลดเป็นจำนวนเงินมาลบออกก่อน
    if($adisc > 0 && $final_price > 0)
    {
      $final_price = ($price - $adisc) < 0 ? 0 : $price - $adisc;
    }

    //----หลังจากนั้นสามารถเป็น % ได้หลังจากหักส่วนลดเป็นจำนวนเงินแล้ว
    if($pdisc > 0 && $final_price > 0)
    {
      $disc = $final_price * ($pdisc * 0.01);
      $final_price -= $disc;
    }

    $final_price = $final_price < 0 ? 0 : $final_price;

    $arr = array(
      'id_order_pos' => $id_order,
      'id_product' => $pd->id_product,
      'id_product_attribute' => $pd->id_product_attribute,
      'product_reference' => $pd->reference,
      'product_name' => $product->getName($pd->id_product),
      'barcode' => $pd->barcode,
      'qty' => $qty,
      'price' => $price,
      'pdisc' => $pdisc,
      'adisc' => $adisc,
      'discount_amount' => ($price - $final_price) * $qty,
      'final_price' => $final_price,
      'total_amount' => $qty * $final_price
    );

    $id = $order->addDetail($arr);
    if($id === FALSE)
    {
      $sc =  FALSE;
      $message = 'เพิ่มรายการขายไม่สำเร็จ';
    }
    else
    {
      $data = $order->getDetail($id);
      $result = array(
        'result' => 'add',
        'data' => array(
          'id' => $data->id_order_pos_detail,
          'barcode' => $data->barcode,
          'pdCode' => $data->product_reference,
          'pdName' => $data->product_name,
          'price' => number($data->price, 2),
          'qty' => number($data->qty),
          'disAmount' => number($data->discount_amount, 2),
          'amount' => number($data->total_amount, 2)
        )
      );
    } //--- end if id
  }
  else  //---- isExists update
  {
    $qty = $ds->qty + $qty;
    $disc_amount = ($ds->price - $ds->final_price) * $qty;
    $total_amount = $qty * $ds->final_price;
    $arr = array(
      'qty' => $qty,
      'discount_amount' => $disc_amount,
      'total_amount' => $total_amount
    );

    if($order->updateDetail($ds->id_order_pos_detail, $arr) === FALSE)
    {
      $sc = FALSE;
      $message = 'ปรับปรุงรายการไม่สำเร็จ ' . $order->error;
    }
    else
    {
      $result = array(
        'result' => 'update',
        'data' => array(
          'id' => $ds->id_order_pos_detail,
          'barcode' => $ds->barcode,
          'pdCode' => $ds->product_reference,
          'pdName' => $ds->product_name,
          'price' => number($ds->price, 2),
          'qty' => number($qty),
          'disAmount' => number($disc_amount, 2),
          'amount' => number($total_amount, 2)
        )
      );
    } //--- end if update
  } //--- end if ds exists detail
} //--- end if barcode

if($sc === TRUE)
{
  $order->updateOrderAmount($id_order);
}

echo $sc === TRUE ? json_encode($result) : $message;
 ?>
