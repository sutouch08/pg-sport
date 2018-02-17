<?php
$id_product_attribute = $_GET['id_product_attribute'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$before_date = $_GET['before_date'];
$id_warehouse = $_GET['id_warehouse'];
$title = $_GET['title'];
if($id_warehouse ==""){
  $warehouse = "id_warehouse != -1";
}else{
  $warehouse = "id_warehouse = $id_warehouse";
}
echo "<!DOCTYPE html>
      <html>
      <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='icon' href='../favicon.ico' type='image/x-icon' />
        <title>ออเดอร์</title>
        <!-- Core CSS - Include with every page -->
        <link href='../../library/css/bootstrap.css' rel='stylesheet'>
        <link href='../../library/css/font-awesome.css' rel='stylesheet'>
        <link href='../../library/css/bootflat.min.css' rel='stylesheet'>
         <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
         <script src='../../library/js/jquery.min.js'></script>
        <script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
        <script src='../../library/js/bootstrap.min.js'></script>
        <!-- SB Admin CSS - Include with every page -->
        <link href='../../library/css/sb-admin.css' rel='stylesheet'>
        <link href='../../library/css/template.css' rel='stylesheet'>
      </head>
      <body style='padding-top:10px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
      <div class=\"hidden-print\">	<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button></div>
      <h5 style='float:left'>$title</h5>
      <table class='table table-striped table-hover'>";

    $header_row = "
    <tr style='font-size:12px;'>
    <td width='10%'>วันที่ </td><td width='15%'>เลขที่เอกสาร</td><td width='10%' align='right'>เข้า</td><td width='10%' align='right'>ออก</td>
    <td width='10%' align='right'>ต้นทุน</td><td width='10%' align='right'>ต้นทุนรวม</td><td width='10%' align='right'>คงเหลือ</td><td width='15%' align='right'>มูลค่า</td>
    </tr>";
    $product = new product();
    $id_product = $product->getProductId($id_product_attribute);
    $product->product_detail($id_product);
    $product->product_attribute_detail($id_product_attribute);
    $barcode = $product->barcode;
    $reference = $product->reference;
    $product_name = $product->product_name;
    $product_cost = $product->product_cost;
      $sql = dbQuery("SELECT date_upd, reference, sum(move_in), sum(move_out) FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND $warehouse AND (date_upd BETWEEN '$from_date' AND '$to_date') GROUP BY reference, id_warehouse ORDER BY date_upd ASC");
      $rows = dbNumRows($sql);
      $total_in = 0;
      $total_out = 0;
      $v = 0;
      list($stock_in, $stock_out) = dbFetchArray(dbQuery("SELECT SUM(move_in) AS stock_in, SUM(move_out) AS stock_out FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND $warehouse AND date_upd < '$from_date'"));
      $bf_balance = $stock_in-$stock_out;
      echo"<tr style='font-size:12px;'><td colspan='8'>$reference  :  $product_name  :  $barcode </td></tr>";
      echo $header_row;
      echo"<tr style='font-size:12px;'><td>".thaiDate($before_date)."</td><td colspan='5'>ยอดยกมา</td><td align='right'>".number_format($bf_balance)."</td><td align='right'>".number_format($bf_balance*$product_cost,2)."</td></tr>";
      $balance = $bf_balance;
      while($v<$rows){
      list($date_upd, $document, $move_in, $move_out) = dbFetchArray($sql);
      $balance = $balance+$move_in-$move_out;
      echo" <tr style='font-size:12px;'><td>".thaiDate($date_upd)."</td><td>$document</td><td align='right'>$move_in</td><td align='right'>$move_out</td><td align='right'>$product_cost</td>
      <td align='center'>"; if($move_in !=0){ echo number_format($move_in*$product_cost,2);} else { echo number_format($move_out*$product_cost,2);} echo"</td>
      <td align='right'>$balance</td><td align='right'>".number_format($balance*$product_cost,2)."</td><tr>";
      $total_in += $move_in;
      $total_out += $move_out;
      $v++;
      }
      $movement = $total_in - $total_out;
      echo 	"<tr style='font-size:12px;'><td colspan='4' style='vertical-align:middle;'>&nbsp;</td><td align='right'>รวมเข้า</td><td align='right'>รวมออก</td><td align='right'>เคลื่อนไหว</td><td align='right'>มูลค่า</td></tr>
          <tr style='font-size:12px;'><td colspan='4' align='right'>&nbsp;</td><td align='right'>$total_in</td><td align='right'>$total_out</td><td align='right'>$movement</td>
          <td align='right'>".number_format($movement*$product_cost,2)."</td></tr>
          <tr style='font-size:12px;'><td colspan='8'><h4>&nbsp;</h4></td></tr>";
      echo "</table>";
 ?>
