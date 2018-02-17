<?php
  $warehouse_rank = $_GET['warehouse'];
  $product_rank = $_GET['product'];
  $view_rank = $_GET['view'];
  $today = date('Y-m-d H:i:s');
  $from = "";
  $to = "";
  if($product_rank==0)
  {  //// product
    $product ="tbl_product_attribute.id_product !=''";
    $p_title = "ทุกรายการ";
  }
  else if($product_rank ==1)
  {
      $pro_from = $_GET['product_from'];
      $pro_to = $_GET['product_to'];
      if($pro_from > $pro_to){ 	$product_from = $pro_to; $product_to = $pro_from; }else{ $product_from = $pro_from; $product_to = $pro_to; }
      $qa = dbQuery("SELECT id_product FROM tbl_product WHERE product_code BETWEEN '".$product_from."' AND '".$product_to."' ORDER BY product_code ASC");
      $rw = dbNumRows($qa);
      $a = 1;
      $in = "";
      while($rx = dbFetchArray($qa))
      {
        $id_product = $rx['id_product'];
        $in .= $id_product;
        if($a<$rw){ $in .= ", "; }
        $a++;
      }
      $product ="tbl_product_attribute.id_product IN(".$in.")";
      $p_title = $product_from." ถึง ".$product_to;
  }
  else if($product_rank == 2)
  {
    $product_selected = $_GET['product_selected'];
    $product = "tbl_product_attribute.id_product = $product_selected";
    $p_title = "เฉพาะ ".$_GET['product_code_selected'];
  }


  if($warehouse_rank == 0)
  {
        $warehouse = "id_warehouse !=''";
        $id_warehouse = "";
  }
  else if($warehouse_rank==1)
  {
          $warehouse_selected = $_GET['warehouse_selected'];
          $warehouse = "id_warehouse = '$warehouse_selected'";
          $id_warehouse = "id_warehouse = $warehouse_selected";
  }

  if(isset($_GET['view_selected'])){ $date = dbDate($_GET['view_selected']); $date_selected= date('Y-m-d',strtotime("+1day $date"));}else{ $date = date('Y-m-d'); $date_selected = $date; }
  /////////////////////////////////////////////////////////////////////
  $report_title = "รายงานสินค้าคงเหลือ ณ วันที่ ".thaiTextDate($date); if($id_warehouse ==""){ $report_title .=" รวมคลังทุกคลัง";}else{ $report_title .="  คลัง : ".getWarehouseName($warehouse_selected);}
  $report_title .= "  สินค้า : ".$p_title;

  $html = "<h4 align='center'>$report_title</h4><table class='table table-striped'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
  <thead style='font-size: 12px;'>
    <th style='width:5%; text-align:center;'>ลำดับ</th>
    <th style='width:10%;'>บาร์โค้ด</th>
    <th style='width:10%;;'>รหัส</th>
    <th style='width:20%;'>ชื่อสินค้า</th>
    <th style='width:10%; text-align:center;'>สี</th>
    <th style='width:5%; text-align:center'>ไซด์</th>
    <th style='width:10%; text-align:center'>คุณลักษณะ</th>
    <th style='width:10%; text-align: right;'>ราคาทุน</th>
    <th style='width:10%; text-align: right;'>คงเหลือ</th>
    <th style='width:10%; text-align: right;'>มูลค่า</th>
  </thead>";

    $qr = dbQuery("SELECT id_product_attribute, reference, barcode, product_name, cost, id_color, id_size, id_attribute FROM tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE $product ORDER BY product_code ASC");
    $row = dbNumRows($qr);
    $total_qty = 0;
    $total_amount = 0;
    $i = 0;
    $n = 1;
    while($i<$row){
      $rs = dbFetchArray($qr);
      $id_product_attribute = $rs['id_product_attribute'];
      $reference = $rs['reference'];
      $barcode = $rs['barcode'];
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
        $sql .=" WHERE id_product_attribute = $id_product_attribute AND $warehouse AND (date_upd BETWEEN '$date_selected 00:00:00' AND '$today 23:59:59') GROUP BY id_product_attribute";
        list($stock_in, $stock_out) = dbFetchArray(dbQuery($sql));
          $stock = $qty + ($stock_out - $stock_in);
        }else	if($view_rank==0){
          $stock = $qty;
        }
      if($stock ==0){
      $amount = 0;
       }else{
      $amount = $stock * $cost;
      $html .= "<tr style='font-size: 12px;'><td align='center'>".$n."</td><td>".$barcode."</td><td>".$reference."</td><td>".$product_name."</td>
      <td align='center'>".$color."</td><td align='center'>".$size."</td><td align='center'>".$attribute."</td>
      <td align='right'>".number_format($cost,2)."</td><td align='right'>".number_format($stock)."</td><td align='right'>".number_format($amount,2)."</td></tr>";
      $n++;
      }
      $total_qty = $total_qty + $stock;
      $total_amount = $total_amount + $amount;
      $i++;
    }
    $html .="<tr><td colspan='8' style='text-align:right; padding-right:10px;'><h4>รวม</h4></td>
          <td style='text-align:right; padding-right:10px;'><h4>".number_format($total_qty)."</h4></td><td style='text-align:right; padding-right:10px;'><h4>".number_format($total_amount,2)."</h4></td></tr></table>";
    echo $html;
 ?>
