<?php
$customer_rank = $_GET['customer'];
$view_rank = $_GET['view'];
$today = date('Y-m-d');
$from = "";
$to = "";
if(isset($_GET['customer_from'])&&isset($_GET['customer_to'])){ // *** เรียงลำดับ id_customer จากน้อยไปมาก
  $p_from  = trim($_GET['customer_from']);
  $p_to = trim($_GET['customer_to']);
    if($p_to < $p_from){
      $customer_from = $p_to;
      $customer_to = $p_from;
    }else{
      $customer_from = $p_from;
      $customer_to = $p_to;
    }
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
      $customer ="customer_code = '$customer_selected'";
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
$title = array(1=>array($report_title));
$sub_header = array("ลำดับ","รหัสลูกค้า","ชื่อลูกค้า","ชื่อร้าน/บริษัท","กลุ่มหลัก","พนักงานขาย","ยอดขาย");
$body = array();
$line = array(1=>array("==========================================================="));
  $qr = dbQuery("SELECT tbl_order_detail_sold.id_customer FROM tbl_order_detail_sold LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE $customer  $view AND id_role IN(1,5) GROUP BY tbl_order_detail_sold.id_customer");
  $row = dbNumRows($qr);
  $i = 0;
  $n = 1;
  $total_movement = 0;
  array_push($body, $sub_header);
  if($row>0){
  while($i<$row){
    list($id_customer) = dbFetchArray($qr);
    $customer = new customer($id_customer);
    $bill_discount = $customer->total_bill_discount($id_customer, $from, $to);/// ส่วนลดท้ายบิล
    $customer_name = $customer->full_name;
    $company = $customer->company;
    $customer_code = $customer->customer_code;
    $customer_group = customer_group($customer->id_default_group);
    $sale = new sale($customer->id_sale);
    $sale_name = $sale->first_name;
    $sql = dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE id_customer = $id_customer $view AND id_role IN(1,5) GROUP BY id_customer");
    list($total_amount) = dbFetchArray($sql);
    $total_amount -= $bill_discount;
      $arr = array($n, $customer_code, $customer_name, $company, $customer_group, $sale_name, $total_amount);
      array_push($body, $arr);
      $total_movement += $total_amount;
      $i++; $n++;
      }
      $arr = array("","","","","","รวม", number_format($total_movement,2));
      array_push($body, $arr);
  }else{
    $arr = array("=========================  ไม่มีรายการตามเงื่อนไขที่เลือก ===============================");
  }
  $sheet_name = "Sale_amount_by_customer";
  $xls = new Excel_XML('UTF-8', false, $sheet_name);
  $xls->addArray($title);
  $xls->addArray($line);
  $xls->addArray ($body);
  $xls->generateXML("Sale_amount_by_customer");

 ?>
