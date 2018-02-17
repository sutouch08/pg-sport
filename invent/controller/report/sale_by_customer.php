<?php
$customer_rank = $_GET['customer'];
$view_rank = $_GET['view'];
$today = date('Y-m-d');
$from = "";
$to = "";
if(isset($_GET['customer_from'])&&isset($_GET['customer_to'])){ // *** เรียงลำดับ id_customer จากน้อยไปมาก
  $c = reorder(trim($_GET['customer_from']), trim($_GET['customer_to']));
  $customer_from = $c['first'];
  $customer_to 	= $c['last'];
}else{
  $customer_from =""; $customer_to = "";
}
if(isset($_GET['customer_selected'])){ $customer_selected = trim($_GET['customer_selected']);}else{ $customer_selected="";}
if(isset($_GET['view_selected'])){ $view_selected = $_GET['view_selected'];}else{ $view_selected = "";}
if($customer_rank==0){  //// customer
  $customer ="customer_code !='-1'";
  if($view_rank == 0){
      $view = "";
      }else if($view_rank==1){
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
        $view = " AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }else if($view_rank ==2){
        $from = dbDate($_GET['from_date'])." 00:00:00";
        $to = dbDate($_GET['to_date'])." 23:59:59";
        if($from =="1970-01-01" || $to =="1970-01-01"){ $from = date('Y-m-d')."00:00:00"; $to = date('Y-m-d')."23:59:59"; }
        $view = "AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }
  }else if($customer_rank==1){
    $customer ="(customer_code BETWEEN '$customer_from' AND '$customer_to' )";
    if($view_rank == 0){
      $view = "";
      }else if($view_rank==1){
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
        $view = "AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }else if($view_rank ==2){
        $from = dbDate($_GET['from_date'])." 00:00:00";
        $to = dbDate($_GET['to_date'])." 23:59:59";
        if($from =="1970-01-01" || $to =="1970-01-01"){ $from = date('Y-m-d')."00:00:00"; $to = date('Y-m-d')."23:59:59"; }
        $view = "AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }
    }else if($customer_rank ==2){
      //$customer ="customer_code = '$customer_selected'";
      $customer = "tbl_customer.id_customer = '$customer_selected'";
      if($view_rank == 0){
      $view = "";
      }else if($view_rank==1){
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
        $view = "AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }else if($view_rank ==2){
        $from = dbDate($_GET['from_date'])." 00:00:00";
        $to = dbDate($_GET['to_date'])." 23:59:59";
        if($from =="1970-01-01" || $to =="1970-01-01"){ $from = date('Y-m-d')."00:00:00"; $to = date('Y-m-d')."23:59:59"; }
        $view = "AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ";
      }
    }

/////////////////////////////////////////////////////////////////////
if($view_rank ==0){ $rank = " ทั้งหมด"; }else{ $rank = thaiDate($from)." ถึง ".thaiDate($to); }
$report_title = "รายงานยอดขาย แยกตามลูกค้า วันที่ ".$rank." : ".COMPANY;
$html = " 	<h4 align='center'>$report_title</h4> <hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<table class='table table-striped table-hover'>
<thead><th style='width:5%; text-align: center;'>ลำดับ</th><th style='width:10%; '>รหัสลูกค้า</th><th style='width:25%; '>ชื่อลูกค้า</th><th style='width:25%;'>ชื่อร้าน/บริษัท</th>
<th style='width:10%;'>กลุ่มหลัก</th><th style='width:10%; text-align:'>พนักงานขาย</th><th style='width:15%; text-align: right;'>ยอดขาย</th></thead>";

  $qr = dbQuery("SELECT tbl_order_detail_sold.id_customer FROM tbl_order_detail_sold LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE $customer  $view AND id_role IN(1,5) GROUP BY tbl_order_detail_sold.id_customer");
  $row = dbNumRows($qr);
  $i = 0;
  $n = 1;
  $total_movement = 0;
  $dataset = array();
  if($row>0){
  while($i<$row){
    list($id_customer) = dbFetchArray($qr);
    $customer = new customer($id_customer);
    $customer_name = $customer->full_name;
    $company = $customer->company;
    $customer_code = $customer->customer_code;
    $customer_group = customer_group($customer->id_default_group);
    $sale = new sale($customer->id_sale);
    $sale_name = $sale->first_name;
    $bill_discount	= $customer->total_bill_discount($id_customer, $from, $to);
    $sql = dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE id_customer = $id_customer $view AND id_role IN(1,5) GROUP BY id_customer");
    list($total_amount) = dbFetchArray($sql);
    $total_amount -= $bill_discount;
    $arr = array("customer_code"=>$customer_code, "customer_name"=>$customer_name, "company"=>$company, "customer_group"=>$customer_group, "sale_name"=>$sale_name, "total_amount"=>$total_amount);
    array_push($dataset, $arr);
    $total_movement += $total_amount;
    $i++;
  }
  function customer_amount_desc($item1,$item2){
    if ($item1['total_amount'] == $item2['total_amount']) return 0;
    return ($item1['total_amount'] < $item2['total_amount']) ? 1 : -1;
  }
  uasort($dataset, 'customer_amount_desc');
  foreach($dataset as $data){
    $customer_name = $data['customer_name'];
    $company = $data['company'];
    $customer_code = $data['customer_code'];
    $customer_group = $data['customer_group'];
    $sale_name = $data['sale_name'];
    $total_amount = $data['total_amount'];
    $html .="<tr><td align='center'>$n</td><td>$customer_code</td><td>$customer_name</td><td>$company</td><td>$customer_group</td><td>$sale_name</td><td align='right'>".number_format($total_amount,2)."</td></tr>";
    $n++;
  }
      $html .="<tr><td colspan='6' style='text-align: right;'><h4>รวม</h4></td><td style='text-align: right;'><h4>".number_format($total_movement,2)."</h4></td></tr>
      <tr><td colspan='7'><h4>&nbsp;</h4></td></tr>";
  }else{
    $html .="<tr><td colspan='7'><h4 align='center'>ไม่มีรายการตามเงื่อนไขที่เลือก</h4></td></tr>";
  }
  $html ."</table>";
  echo $html;

 ?>
