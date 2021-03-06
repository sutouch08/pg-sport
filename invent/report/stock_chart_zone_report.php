<?php 
	$page_menu = "invent_stock_report";
	$page_name = "กราฟรายงานการเคลื่อนไหวสินค้า แยกตามพื้นที่การขาย";
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	?>
<link rel="stylesheet" href="../library/css/morris-0.4.3.min.css"/>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sx-10"><h3><i class='fa fa-bar-chart'></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sx-2"></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />

<!-- End page place holder -->
<?php	
include "report/product_filter.php";
if(isset($_POST['id_product'])&&$_POST['id_product'] !="0"){
	$id_pro = $_POST['id_product'];
	$product = new product();
	$product->product_detail($id_pro,0);
	$chart_title = $product->product_code." : ".$product->product_name." : ";
	
}else{
	$chart_title = "ทั้งหมด ";
}
if(isset($_POST['view'])&&isset($_POST['from_date'])){
	if($_POST['from_date'] !="เลือกวัน"){ $from_dated = $_POST['from_date']; }else{ $from_dated = date("Y-m-d"); }
	if($_POST['view'] !="0"){ $viewer = $_POST['view']; }else{ $viewer = "month";}
	switch($viewer){
		case "week" :
		$ra = getWeek($from_dated);
		$from_d = $ra['from'];
		$to_d = $ra['to'];
		$chart_title .= "&nbsp;&nbsp;จากวันที่ ".thaiDate($from_d)." ถึง ".thaiDate($to_d);
		break;
		case "month":
		$month = getThaiMonthName($from_dated);
		$year = date("Y", strtotime($from_dated));
		$chart_title .= "&nbsp;&nbsp;เดือน ".$month." ".$year;	
		break;
		case "year":
		$year = date("Y",strtotime($from_dated));
		$chart_title .= "&nbsp;&nbsp;ปี ".$year;
		break;
		default :
		$month = getThaiMonthName(date('Y-m-d'));
		$year = date("Y", strtotime(date('Y-m-d')));
		$chart_title .= "&nbsp;&nbsp;เดือน ".$month." ".$year;	
		break;
	}
}else{
	$chart_title .= "เดือน ".getThaiMonthName(date('Y-m-d'))." ".date('Y',strtotime(date('Y-m-d')));
}
	
?> 

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary" id='qty' style="position:relative;">
              <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-line-chart"></i>&nbsp;&nbsp;<?php echo $chart_title; ?></h3></div>
              <div class="panel-body"> <div id="morris-chart-line"></div></div> 
              <div class="panel-footer"><h3 class="panel-title" id='footer'> </h3></div>
            </div>
        </div>
 </div>
 
          
            
 <?php
 	
 		if(isset($_POST['id_product'])){ $id_product = $_POST['id_product']; }else{ $id_product = 0; }
		if(isset($_POST['view'])&&$_POST['view'] !="0"){ $view = $_POST['view']; }else{ $view = "month"; }
		if(isset($_POST['from_date']) &&$_POST['from_date'] != "เลือกวัน" && $view == 0 ){ $from_date = date('d-m-Y',strtotime($_POST['from_date']));} else { $from_date = date("Y-m-d");} 
		  $chart = "";
		  $group = "";
		  $group_data ="";
		  $group_names = "";
		  $y_key_qty = "";
		  $y_key_amount = "";
		  $today = date('Y-m-d');
		  $arrgroup = array();
		  $arrgroup_name = array();
		  $sql = dbQuery("SELECT id_group,group_name FROM tbl_group  ORDER BY id_group ASC");
			$row = dbNumRows($sql);
				$i =0;
				$n = 1;
				while($i<$row){
					list($id_group, $group_name) = dbFetchArray($sql);
					$arrgroup[] = $id_group;
					$arrgroup_name[$id_group] = $group_name;
				$group_names .= "'$group_name',";
				$y_key_qty .="'group_qty$id_group',";
				$y_key_amount .="'group_amount$id_group',";
				$i++;
				$n++;
				}
if(isset($view)&&$view !="0" ){
		$movement = 0;
		$move_amount =0;
		if(isset($id_product)&&$id_product !=0){ 
			$id_product = $_POST['id_product'];
			if($view =="month"){
				$rang = getMonth($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
				$month = date("m",strtotime($from_date)); 
				$year= date("Y",strtotime($from_date));
				$date = date_in_month($month, $year);
				foreach( $date as $d){
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
				foreach( $arrgroup as $id_group){
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_product = '$id_product' AND id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
				$movement = $movement + $qty;  
				$move_amount = $move_amount+$total_amount;		
				if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
				$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$day', $group_data },";
				}
			}else if($view =="year"){
				$rang = getYear($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
			 $year= date("Y",strtotime($today));
			 $m = 13;
			 $i = 1;
				 while($i<$m){
				 $month_name = MonthName($i);
				 $d_start = date($year.'-'.sprintf("%02d",$i).'-01')." 00:00:00.000000";
				 $d_end = date($year.'-'.sprintf("%02d",$i).'-'.date('t',strtotime($year.'-'.sprintf('%02d',$i))))." 23:59:59:.000000";
				foreach( $arrgroup as $id_group){
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_product = '$id_product' AND id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
				$movement = $movement + $qty;
				$move_amount = $move_amount+$total_amount;
				if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
				$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$month_name', $group_data },";
				 $i++;
				}
			}else if($view =="week"){
				$rang = getWeek($from_date);
				$from = $rang['from'];
				$from1 = $rang['from'];
				$to = $rang['to'];
				 while($from1<=$to){
					 $day = date("d/m",strtotime("$from1"));
					 $d_start = date('Y-m-d', strtotime("$from1"))." 00:00:00.000000";	
					 $d_end = date('Y-m-d', strtotime("$from1"))." 23:59:59:.000000";
					foreach( $arrgroup as $id_group){
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_product = '$id_product' AND id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
				$movement = $movement + $qty;  
				$move_amount = $move_amount+$total_amount;		
				if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
				$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$day', $group_data },";
					 $from1 = date('Y-m-d',strtotime("+1day $from1"));
				}
			}		
	}else{
		if($view =="month"){
			$rang = getMonth($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
				$month = date("m",strtotime($from_date)); 
				$year= date("Y",strtotime($from_date));
				$date = date_in_month($month, $year);
				foreach( $date as $d){
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));$group_data = "";
				foreach( $arrgroup as $id_group){
					$name_group = $arrgroup_name[$id_group];
					list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
					$movement = $movement + $qty;  
					$move_amount = $move_amount+$total_amount;		
					if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
					$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$day', $group_data },";
					//echo "$chart<br>";
				}//echo $group;
			}else if($view =="year"){
				$rang = getYear($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
			 $year= date("Y",strtotime($today));
			 $m = 13;
			 $i = 1;
				 while($i<$m){
				 $month_name = MonthName($i);
				 $d_start = date($year.'-'.sprintf("%02d",$i).'-01')." 00:00:00.000000";
				 $d_end = date($year.'-'.sprintf("%02d",$i).'-'.date('t',strtotime($year.'-'.sprintf('%02d',$i))))." 23:59:59:.000000";
					foreach( $arrgroup as $id_group){
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
				$movement = $movement + $qty;  
				$move_amount = $move_amount+$total_amount;		
				if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
				$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$month_name', $group_data },";
				 $i++;
				}
			}else if($view =="week"){
				$rang = getWeek($from_date);
				$from = $rang['from'];
				$from1 = $rang['from'];
				$to = $rang['to'];
				 while($from1<=$to){
					 $day = date("d/m",strtotime("$from1"));
					 $d_start = date('Y-m-d', strtotime("$from1"))." 00:00:00.000000";
					 $d_end = date('Y-m-d', strtotime("$from1"))." 23:59:59:.000000";
					foreach( $arrgroup as $id_group){
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$d_start' AND '$d_end' AND id_default_group = '$id_group'"));
				$movement = $movement + $qty;  
				$move_amount = $move_amount+$total_amount;		
				if($qty == ""){$qty = 0;}if($total_amount == ""){$total_amount = 0;}
				$group_data .= "group_qty$id_group: $qty, group_amount$id_group: $total_amount,";
				}
					$chart .= "{ d: '$day', $group_data },";
					 $from1 = date('Y-m-d',strtotime("+1day $from1"));
				}
			}	
	}
}//isset $view
		 
/*$default = "";
if(!isset($_POST['view'])&&!isset($_POST['id_product'])){
				$movement = 0;
				$move_amount =0;
				$today = date('Y-m-d');
				$month = date("m",strtotime($today)); 
				$year= date("Y",strtotime($today));
				$date = date_in_month($month, $year);
				foreach( $date as $d){
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
				$sql = dbQuery("select tbl_product.product_code.tbl_temp.qty.tbl_order_detail.final_price from `tbl_temp` left join `tbl_order_detail` on`tbl_temp`.`id_product_attribute` = `tbl_order_detail`.`id_product_attribute` and `tbl_temp`.`id_order` = `tbl_order_detail`.`id_order`  left join `tbl_product` on `tbl_order_detail`.`id_product` = `tbl_product`.`id_product` where `tbl_temp`.`status` = 4 and `tbl_order`.`role` = 1 and date_upd BETWEEN '$d_start' AND '$d_end'");
				$qty =0;
				$total_amount = 0;
				while($r = dbFetchArray($sql)){
					$qty = $qty+$r['qty'];
					$amount = $r['qty']*$r['final_price'];
					$total_amount = $amount+$total_amount;
				}
				$movement = $movement + $qty;  
				$move_amount = $move_amount+$total_amount;			
				if($qty>0){
					$default .= "{ d: '$day', qty: $qty, amount: $total_amount },";
				}else{
					$default .= "{ d: '$day', qty: 0, amount: 0 },";
				}
				}		
}*/
$amount_total = "";
$qty_total = "";
if(isset($id_product)&&$id_product !=0){ 
foreach( $arrgroup as $id_group){
				$name_group = $arrgroup_name[$id_group];
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_product = '$id_product' and id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$from 00:00:00.000000' AND '$to 23:59:59:.000000' AND id_default_group = '$id_group'"));
					$amount_total .= "&nbsp;&nbsp;&nbsp;  $name_group ".number_format($total_amount,2)." บาท";
					$qty_total .= "&nbsp;&nbsp;&nbsp;  $name_group ".number_format($qty)." ชิ้น";
					
				}
}else{
	foreach( $arrgroup as $id_group){
				$name_group = $arrgroup_name[$id_group];
				list($total_amount,$qty) = dbFetchArray(dbQuery("select SUM(total_amount),SUM(sold_qty) from tbl_order_detail_sold  LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer where id_role IN (1,5) and tbl_order_detail_sold.date_upd BETWEEN '$from 00:00:00.000000' AND '$to 23:59:59:.000000' AND id_default_group = '$id_group'"));
					$amount_total .= "&nbsp;&nbsp;&nbsp;  $name_group ".number_format($total_amount,2)." บาท";
					$qty_total .= "&nbsp;&nbsp;&nbsp;  $name_group ".number_format($qty)." ชิ้น";
				}
}
echo "<input type='hidden' id='movement' value='".number_format($movement)."' /><input type='hidden' id='move_amount' value='".number_format($move_amount,2)."' /><input type='hidden' id='i' value='1' />";
 ?>
 <input type='hidden' id='data' value="<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?> " />
<script src="../library/js/raphael-min.js"></script>
<script src="../library/js/morris-0.4.3.min.js"></script> 
<script>
var line = new Morris.Line({
  element: 'morris-chart-line',
  data: [	
	<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>
  ],
  xkey: 'd' ,
 ykeys:[<?php echo $y_key_qty;?> ],
  		 labels: [<?php echo $group_names;?>],
  smooth: false, 
  parseTime: false,
  yLabelFormat: function(y){ return y = Math.round(y); },
  xLabelMargin:0
  
});
</script>
<script>
$(document).ready(function(e) {
    if($("#movement").length){
		var amount = $("#movement").val();
		$("#footer").html("เคลื่อนไหว : "+amount+"  ชิ้น <?php echo $qty_total;?>");
	}
});
function switch_graph(){
	var i = $("#i").val();
	if(i ==1){
		$("#morris-chart-line").empty();
		line =  new Morris.Line({
 		 element: 'morris-chart-line',
  		 data: [<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>],
 		 xkey: 'd' ,
 		 ykeys:[<?php echo $y_key_amount;?>],
  		 labels: [<?php echo $group_names;?>],
		  smooth: false, 
		  parseTime: false,
		  yLabelFormat: function(y){ return y = Math.round(y); },
		  xLabelMargin:0
		});
		$("#i").val(2);
		$("#switch").html("ดูจำนวน");
		if($("#move_amount").length){
		var amount2 = $("#move_amount").val();
		$("#footer").html("เคลื่อนไหว : "+amount2+" บาท <?php echo $amount_total;?>");
		}
	}else if(i==2){
		$("#morris-chart-line").empty();
		line =  new Morris.Line({
 		element: 'morris-chart-line',
  		 data: [<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>],
 		 xkey: 'd' ,
 		 ykeys:[<?php echo $y_key_qty;?>],
  		 labels: [<?php echo $group_names;?>],
		  smooth: false, 
		  parseTime: false,
		  yLabelFormat: function(y){ return y = Math.round(y); },
		  xLabelMargin:0
		});
		$("#i").val(1);
		$("#switch").html("ดูยอดเงิน");
		 if($("#movement").length){
		var amount = $("#movement").val();
		$("#footer").html("เคลื่อนไหว : "+amount+"  ชิ้น <?php echo $qty_total;?>");
	}
	}
}
$("#switch").click(function(e) {
    switch_graph();
});
$(document).ready(function() {
   	    $("#product1").change(function(){
		$("#product_from").attr("disabled","disabled");
		$("#product_to").attr("disabled","disabled");
		$("#product_selected").attr("disabled","disabled");
	});
});

$(document).ready(function() {
   	    $("#product2").change(function(){
		$("#product_from").removeAttr("disabled");
		$("#product_to").removeAttr("disabled");
		$("#product_from").focus();
		$("#product_selected").attr("disabled","disabled");
	});
});

$(document).ready(function() {
   	    $("#product3").change(function(){
		$("#product_from").attr("disabled","disabled");
		$("#product_to").attr("disabled","disabled");
		$("#product_selected").removeAttr("disabled");
		$("#product_selected").focus();
	});
});

$(document).ready(function() {
	$("#product_from" ).autocomplete(
	{
		 source: 'controller/reportController.php'
	});
});
$(document).ready(function() {
	$("#product_to" ).autocomplete(
	{
		 source: 'controller/reportController.php'
	});
});
$(document).ready(function() {
	$("#product_selected" ).autocomplete(
	{
		 source: 'controller/reportController.php'
	});
});
$(document).ready(function() {
   	    $("#warehouse1").change(function(){
		$("#warehouse_selected").attr("disabled","disabled");
	});
});
$(document).ready(function() {
   	    $("#warehouse2").change(function(){
		$("#warehouse_selected").removeAttr("disabled");
	});
});
$(document).ready(function() {
   	    $("#date2").click(function(){
		$("#date_selected").removeAttr("disabled");
	});
});
$(document).ready(function() {
   	    $("#date1").click(function(){
		$("#date_selected").attr("disabled","disabled");
	});
});
$(function() {
    $("#date_selected").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
$(document).ready(function(e) {
    $("#gogo").click(function(){
		$("#report_form").attr("action", "controller/reportController.php?stock_report=y");
		$(this).attr("type", "submit");
	});
});
$(document).ready(function(e) {
    $("#report").click(function(e) {
        $("#report_form").attr("action", "index.php?content=stock_report&stock_report=y");
    });
});

</script>