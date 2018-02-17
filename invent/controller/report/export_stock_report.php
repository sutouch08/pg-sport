<?php
$warehouse_rank = $_GET['warehouse'];
$product_rank = $_GET['product'];
$view_rank = $_GET['view'];
$today = date('Y-m-d');
$from = "";
$to = "";
if($product_rank==0){  //// product
  $product ="tbl_product_attribute.id_product !=''";
  $p_title = "ทุกรายการ";
}else if($product_rank ==1){
  $pro_from = $_GET['product_from'];
    $pro_to = $_GET['product_to'];
    if($pro_from > $pro_to){ 	$product_from = $pro_to; $product_to = $pro_from; }else{ $product_from = $pro_from; $product_to = $pro_to; }
    $qa = dbQuery("SELECT id_product FROM tbl_product WHERE product_code BETWEEN '".$product_from."' AND '".$product_to."' ORDER BY product_code ASC");
    $rw = dbNumRows($qa);
    $a = 1;
    $in = "";
    while($rx = dbFetchArray($qa)){
      $id_product = $rx['id_product'];
      $in .= $id_product;
      if($a<$rw){ $in .= ", "; }
      $a++;
    }
    $product ="tbl_product_attribute.id_product IN(".$in.")";
    $p_title = $product_from." ถึง ".$product_to;
}else if($product_rank == 2){
  $product_selected = $_GET['product_selected'];
  $product = "tbl_product_attribute.id_product = $product_selected";
  $p_title = "เฉพาะ ".$_GET['product_code_selected'];
}
if($warehouse_rank == 0){
      $warehouse = "id_warehouse !=''";
      $id_warehouse = "";
}else if($warehouse_rank==1){
        $warehouse_selected = $_GET['warehouse_selected'];
        $warehouse = "id_warehouse = '$warehouse_selected'";
        $id_warehouse = "id_warehouse = $warehouse_selected";
}

if(isset($_GET['view_selected'])){ $date = dbDate($_GET['view_selected']); $date_selected = date('Y-m-d',strtotime("+1day $date"));}else{ $date = date('Y-m-d'); $date_selected = $date; }
/////////////////////////////////////////////////////////////////////
$report_title = "รายงานสินค้าคงเหลือ ณ วันที่ ".thaiTextDate($date); if($id_warehouse ==""){ $report_title .=" รวมคลังทุกคลัง";}else{ $report_title .="  คลัง : ".getWarehouseName($warehouse_selected);}
$report_title .= " : ".COMPANY;
$title = array(1=>array($report_title));
$report_title2 = "สินค้า : ".$p_title;
$title2 = array(1=>array($report_title2));
$sub_header = array("ลำดับ","รุ่น","บาร์โค้ด","รหัส","ขื่อสินค้า","สี", "ไซด์", "คุณลักษณะ","ราคาทุน","คงเหลือ","มูลค่า");
$line = array(1=>array("======================================================================================="));
$body = array();
$qr = dbQuery("SELECT id_product_attribute, product_code, reference, barcode, product_name, cost, id_color, id_size, id_attribute FROM tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE $product ORDER BY product_code");
  $row = dbNumRows($qr);
  $total_qty = 0;
  $total_amount = 0;
  $i = 0;
  $n = 1;
  array_push($body, $sub_header);
  while($i<$row){
    $rs = dbFetchArray($qr);
    $id_product_attribute = $rs['id_product_attribute'];
    $reference = $rs['reference'];
    $barcode = $rs['barcode'];
    $product_code = $rs['product_code'];
    $product_name = $rs['product_name'];
    $cost = $rs['cost'];
    $color = $rs['id_color'] != 0 ? get_color_code($rs['id_color'])." : ".color_name($rs['id_color']) : "";
    $size  = $rs['id_size'] != 0 ? get_size_name($rs['id_size']) : "";
    $attribute = $rs['id_attribute'] != 0 ? get_attribute_name($rs['id_attribute']) : "";
    $product = new product();
    if($warehouse_rank == 0){
      $qty = $product->all_available_qty($id_product_attribute);
    }else if($warehouse_rank == 1){
      $stock_qty = $product->stock_qty_by_warehouse($id_product_attribute, $warehouse_selected);
      $move_qty = $product->move_qty_by_warehouse($id_product_attribute, $warehouse_selected);
      $cancle_qty = $product->cancle_qty_by_warehouse($id_product_attribute, $warehouse_selected);
      $buffer_qty = $product->buffer_qty_by_warehouse($id_product_attribute, $warehouse_selected);
      $qty = $stock_qty + $move_qty + $cancle_qty + $buffer_qty;
    }

    if($view_rank==1){
      $sql = "SELECT SUM(move_in) AS stock_in, SUM(move_out) AS stock_out FROM tbl_stock_movement";
      $sql .=" WHERE id_product_attribute = ".$id_product_attribute." AND ".$warehouse." AND (date_upd BETWEEN '$date_selected 00:00:00' AND '$today 23:59:59') GROUP BY id_product_attribute";
      list($stock_in, $stock_out) = dbFetchArray(dbQuery($sql));
      $stock = $qty + ($stock_out - $stock_in);
    }else	if($view_rank==0){
      $stock = $qty;
    }
    if($stock ==0){
      $amount = 0;
    }else{
    $amount = $stock * $cost;
    $arr = array($n, $product_code, $barcode, $reference, $product_name, $color, $size, $attribute, $cost, $stock, $amount);
    array_push($body, $arr);
    $total_qty = $total_qty + $stock;
    $total_amount = $total_amount + $amount;
    $n++;
    }
    $i++;
  }
  $arr = array(" ", " ", ""," ", " ", "", "", "","รวม", $total_qty, $total_amount);
  array_push($body, $arr);
  $sheet_name = "Stock_report";
  $xls = new Excel_XML('UTF-8', false, $sheet_name);
  $xls->addArray($title);
  $xls->addArray($title2);
  $xls->addArray($line);
  $xls->addArray ($body);
  $xls->generateXML("Stock_report");
 ?>
