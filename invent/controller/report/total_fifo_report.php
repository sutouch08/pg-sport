<?php
$warehouse_rank = $_GET['warehouse'];
$product_rank = $_GET['product'];
$view_rank = $_GET['view'];
$today = date('Y-m-d');
$from = "";
$to = "";
if(isset($_GET['product_from'])&&isset($_GET['product_to'])){ // *** เรียงลำดับ id_product จากน้อยไปมาก
  $p_from  = getProductAttributeID($_GET['product_from']);
  $p_to = getProductAttributeID($_GET['product_to']);
    if($p_to < $p_from){
      $product_from = $p_to;
      $product_to = $p_from;
    }else{
      $product_from = $p_from;
      $product_to = $p_to;
    }
}else{
  $product_from =""; $product_to = "";
}
if(isset($_GET['product_selected'])){ $product_selected = $_GET['product_selected'];}else{ $product_selected="";}
if($product_rank==0){  //// product
  $product ="id_product_attribute !=''";
  }else if($product_rank==1){
    $product ="(id_product_attribute BETWEEN '$product_from' AND '$product_to' )";
  }else if($product_rank ==2){
    $product_selected = getProductAttributeID($product_selected);
    $product ="id_product_attribute = '$product_selected'";
  }
if(isset($_GET['warehouse_selected'])){ $warehouse_selected = trim($_GET['warehouse_selected']);}else{ $warehouse_selected="";}
if($warehouse_rank==0){  //// customer
  $warehouse ="id_warehouse !='-1'";
  $id_warehouse = "";
  }else if($warehouse_rank ==1){
      $warehouse ="id_warehouse = '$warehouse_selected'";
      $id_warehouse = $warehouse_selected;
  }
if(isset($_GET['view_selected'])){ $view_selected = $_GET['view_selected'];}else{ $view_selected = "";}
 if($view_rank==1){
      switch($view_selected){
        case "week" :
          $rang = getWeek($today);
          break;
        case "month" :
          $rang = getMonth();
          break;
        case "year" :
          $rang = getYear();
          break ;
        default :
          $rang = getMonth();
          break;
        }
        $from = $rang['from']." 00:00:00";
        $to = $rang['to']." 23:59:59";
        $view = "AND (date_upd BETWEEN '$from' AND '$to') ";
  }else if($view_rank ==2){
        $from = dbDate($_GET['from_date'])." 00:00:00";
        $to = dbDate($_GET['to_date'])." 23:59:59";
        if($from =="1970-01-01" || $to =="1970-01-01"){ $from = date('Y-m-d')."00:00:00"; $to = date('Y-m-d')."23:59:59"; }
        $view = "AND (date_upd BETWEEN '$from' AND '$to') ";
  }
  $before_date = date('Y-m-d', strtotime("-1day $from"));
/////////////////////////////////////////////////////////////////////
if($warehouse_selected !=""){ $wh="  คลัง : ".getWarehouseName($warehouse_selected);}else{$wh =" รวมทุกคลัง";}
$report_title = "รายงานยอดรวมสินค้า เข้า-ออก วันที่ ".thaiTextDate($from)."  ถึง ".thaiTextDate($to);
$html = "<h4>$report_title &nbsp;&nbsp; $wh</h4><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' /><table class='table table-striped table-hover'>";
$html .= "<table class='table table-striped'><thead><tr><th style='width:5%; text-align: center;'>ลำดับ</th><th style='width:10%;'>บาร์โค้ด</th><th style='width:15%;'>รหัส</th><th style='width:30%;'>สินค้า</th>
      <th style='width:10%;'>คลัง</th><th style='width:10%; text-align: right;'>ต้นทุน</th><th style='width:10%; text-align: right;'>เข้า</th><th style='width:10%; text-align: right;'>ออก</th></tr></thead>";

  $qr = dbQuery("SELECT id_product_attribute, id_warehouse, SUM(move_in), SUM(move_out) FROM tbl_stock_movement WHERE id_reason != 9 AND $product  AND $warehouse $view GROUP BY id_product_attribute, id_warehouse ORDER BY id_product_attribute ASC");
  $row = dbNumRows($qr);
  $i = 0;
  $n = 1;
  $total_in = 0;
  $total_out = 0;
  if($row>0){
    while($i<$row){
      list($id_product_attribute, $wh_id, $move_in, $move_out) = dbFetchArray($qr);
      $product = new product();
      $id_product = $product->getProductId($id_product_attribute);
      $product->product_detail($id_product);
      $product->product_attribute_detail($id_product_attribute);
      $html .= "<tr style='font-size: 12px;'><td align='center'>$n</td><td>".$product->barcode."</td><td>".$product->reference."</td><td>".$product->product_name."</td><td>".get_warehouse_name_by_id($wh_id)."</td>";
      $html .= "<td align='right'>".number_format($product->product_cost)."</td><td align='right'>".number_format($move_in)."</td><td align='right'>".number_format($move_out)."</td></tr>";
      $total_in += $move_in;
      $total_out += $move_out;
      $i++; $n++;
    }
    $html .= "<tr style='font-size: 12px;'><td colspan='6' align='right'>รวม</td><td align='right'>".number_format($total_in)."</td><td align='right'>".number_format($total_out)."</td></tr>";
  }else{
    $html .= "<tr><td colspan='9'><h4 align='center'>ไม่มีรายการตามเงื่อนไขที่เลือก</h4></td></tr>";
  }
  $html .= "</table>";
echo $html;

?>
