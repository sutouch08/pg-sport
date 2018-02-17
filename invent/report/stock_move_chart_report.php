<?php 
	$page_menu = "invent_stock_report";
	$page_name = "กราฟรายงานภาพรวมสินค้าเปรียบเทียบยอด เข้า / ออก";
	
	?>
<link rel="stylesheet" href="../library/css/morris-0.4.3.min.css"/>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-11"><h3><i class='fa fa-bar-chart'></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-1"></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />

<!-- End page place holder -->
<?php	
		if(isset($_POST['id_product'])){ $id_product = $_POST['id_product']; }else{ $id_product = 0; }
		if(isset($_POST['view'])){ $view = $_POST['view']; }else{ $view = "0"; }
		if(isset($_POST['from_date']) &&$_POST['from_date'] != "เลือกวัน" && $view == 0 ){ $from_date = date('d-m-Y',strtotime($_POST['from_date']));} else { $from_date = "เลือกวัน";} 
		 if(isset($_POST['to_date']) &&$_POST['to_date'] != "เลือกวัน" && $view == 0 ){ $to_date = date('d-m-Y',strtotime($_POST['to_date']));} else { $to_date = "เลือกวัน";} 
echo"
<form id='chart_report' method='post'>
<div class='row' >
	<div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>เลือกสินค้า</span><select name='id_product' class='form-control'>"; get_product_list($id_product); echo"</select></div></div>
    <div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>การแสดงผล</span><select name='view' id='view' class='form-control' required='required' >";  get_view_list($view); echo "</select></div></div>
    <div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' name='from_date' id='from_date' class='form-control' value='$from_date'/></div></div>
    <div class='col-lg-3'><button type='submit' id='btn_ok' class='btn btn-default' >ตกลง</button><button type='button' class='btn btn-default' id='switch' style='display:inline-block; float:right;'>รายระเอียดสินค้าออก</button></div>
    </div>
</form>";
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
		  $today = date('Y-m-d');
if(isset($view)&&$view !="0" ){
		$movement_in = 0;
		$movement_out =0;
		$movement_sale = 0;
		$movement_sponsor = 0;
		$movement_consignment = 0;
		if(isset($id_product)&&$id_product !=0){ 
			$id_product = $_POST['id_product'];
			if($view =="month"){
				$month = date("m",strtotime($from_date)); 
				$year= date("Y",strtotime($from_date));
				$date = date_in_month($month, $year);
				foreach( $date as $d){
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
				$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end')");
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail JOIN tbl_product_attribute ON tbl_recieved_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product AND (date BETWEEN '$d_start' AND '$d_end')");
				$move_in =0;
				$move_out = 0;
				list($sold_qty) = dbFetchArray($sql);	
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); 
				$move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;	
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
					$chart .= "{ d: '$day', move_in: '$move_in', move_out: '$move_out', sale: '$sale', sponsor: '$sponsor', consignment: '$consignment' },";				
				}
			}else if($view =="year"){
			 $year= date("Y",strtotime($today));
			 $m = 13;
			 $i = 1;
				 while($i<$m){
				 $month = shortMonthName($i);
				 $month_name = MonthName($i);
				  $d_start = date('Y-m-01', strtotime($year.$month))." 00:00:00.000000";
				  $d_end = date('Y-m-t', strtotime($year.$month))." 23:59:59.000000";
				$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end')");
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail JOIN tbl_product_attribute ON tbl_recieved_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product AND (date BETWEEN '$d_start' AND '$d_end')");
				$move_in =0;
				$move_out = 0;
				$total_amount = 0;
				list($sold_qty) = dbFetchArray($sql);
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); 
				$move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;	
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
					$chart .= "{ d: '$month_name', move_in: '$move_in', move_out: '$move_out', sale: '$sale', sponsor: '$sponsor', consignment: '$consignment' },";				
				 $i++;
				}
			}else if($view =="week"){
				$rang = getWeek($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
				 while($from<=$to){
					 $day = date("d/m",strtotime("$from"));
					 $d_start = date('Y-m-d', strtotime("$from"))." 00:00:00.000000";	
					 $d_end = date('Y-m-d', strtotime("$from"))." 23:59:59.000000";
					$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end')");
					$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail JOIN tbl_product_attribute ON tbl_recieved_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product AND (date BETWEEN '$d_start' AND '$d_end')");
				$move_in =0;
				$move_out = 0;
			
				list($sold_qty) = dbFetchArray($sql);
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); $move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;	
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE id_product = $id_product AND (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
					$chart .= "{ d: '$day', move_in: '$move_in', move_out: '$move_out', sale: '$sale',sponsor: $sponsor, consignment: $consignment },";				
					 $from = date('Y-m-d',strtotime("+1day $from"));
				}
			}		
	}else{
		if($view =="month"){
				$month = date("m",strtotime($from_date)); 
				$year= date("Y",strtotime($from_date));
				$date = date_in_month($month, $year);
				foreach( $date as $d){
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
					$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE  (date_upd BETWEEN '$d_start' AND '$d_end')");
					$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail  WHERE  (date BETWEEN '$d_start' AND '$d_end') AND status=1");		
				$move_in =0;
				$move_out = 0;
				
				list($sold_qty) = dbFetchArray($sql);
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); $move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;		
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE  (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
					$chart .= "{ d: '$day', move_in: '$move_in', move_out: '$move_out', sale: '$sale', sponsor: '$sponsor', consignment: '$consignment' },";
				}
			}else if($view =="year"){
			 $year= date("Y",strtotime($today));
			 $m = 13;
			 $i = 1;
				 while($i<$m){
				 $month_name = MonthName($i);
				  $d_start = date('Y-m-01', strtotime($year.$month))." 00:00:00.000000";
				 $d_end = date('Y-m-t', strtotime($year.$month))." 23:59:59.000000";
				$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE (date_upd BETWEEN '$d_start' AND '$d_end')");
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail e WHERE (date BETWEEN '$d_start' AND '$d_end') AND status=1");
				$move_in =0;
				$move_out = 0;
				list($sold_qty) = dbFetchArray($sql);
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); $move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
				$chart .= "{ d: '$month_name', move_in: '$move_in', move_out: '$move_out', sale: '$sale',sponsor: '$sponsor', consignment: '$consignment' },";
				 $i++;
				}
			}else if($view =="week"){
				$rang = getWeek($from_date);
				$from = $rang['from'];
				$to = $rang['to'];
				 while($from<=$to){
					 $day = date("d/m",strtotime("$from"));
					 $d_start = date('Y-m-d', strtotime("$from"))." 00:00:00.000000";
					 $d_end = date('Y-m-d', strtotime("$from"))." 23:59:59.000000";
					$sql = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold  WHERE (date_upd BETWEEN '$d_start' AND '$d_end')");
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_recieved_detail e WHERE (date BETWEEN '$d_start' AND '$d_end') AND status=1");
				$move_in =0;
				$move_out = 0;
				list($sold_qty) = dbFetchArray($sql);
				$move_out += $sold_qty;
				$move_out += $sold_qty;
				list($recieved_qty) = dbFetchArray($sqm); $move_in += $recieved_qty;
				$movement_in = $movement_in + $move_in;  
				$movement_out = $movement_out+$move_out;
				list($sale) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 1"));
				list($sponsor) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 4"));
				list($consignment) = dbFetchArray(dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$d_start' AND '$d_end') AND id_role = 5"));
				$sale += 0;
				$sponsor += 0;
				$consignment += 0;
				$movement_sale = $movement_sale + $sale;
				$movement_sponsor = $movement_sponsor + $sponsor;
				$movement_consignment = $movement_consignment + $consignment;
					$chart .= "{ d: '$day', move_in: '$move_in', move_out: '$move_out', sale: '$sale', sponsor: '$sponsor', consignment: '$consignment' },";
					 $from = date('Y-m-d',strtotime("+1day $from"));
				}
			}	
	}
}//isset $view

echo "<input type='hidden' id='movement_in' value='".number_format($movement_in)."' /><input type='hidden' id='move_amount' value='".number_format($movement_out)."' /><input type='hidden' id='i' value='1' />
<input type='hidden' id='movement_sale' value='".number_format($movement_sale)."' />
<input type='hidden' id='movement_sponsor' value='".number_format($movement_sponsor)."' />
<input type='hidden' id='movement_consignment' value='".number_format($movement_consignment)."' />
"; ?>
 
 <input type='hidden' id='data' value="<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>" />
<script src="../library/js/raphael-min.js"></script>
<script src="../library/js/morris-0.4.3.min.js"></script> 
<script>
var line = new Morris.Line({
  element: 'morris-chart-line',
  data: [	
	<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>
  ],
  xkey: 'd' ,
  ykeys:['move_in','move_out'],
  labels: ['สินค้าเข้า(ชิ้น)','สินค้าออก(ชิ้น)'],
  smooth: false, 
  parseTime: false,
  yLabelFormat: function(y){ return y = Math.round(y); },
  xLabelMargin:0
  
});
</script>
<script>
$(document).ready(function(e) {
    if($("#movement_in").length){
		 var amount2 = $("#move_amount").val();
		var amount = $("#movement_in").val();
		$("#footer").html("สินค้าเข้า : "+amount+"  ชิ้น  สินค้าออก : "+amount2+" ชิ้น");
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
 		 ykeys:['sale','sponsor','consignment'],
  		 labels: ['ขาย(ชิ้น)','สปอนเซอร์(ชิ้น)','ฝากขาย(ชิ้น)'],
		  smooth: false, 
		  parseTime: false,
		  yLabelFormat: function(y){ return y = Math.round(y); },
		  xLabelMargin:0
		});
		$("#i").val(2);
		$("#switch").html("สินค้า เข้า/ออก");
		if($("#move_amount").length){
		var amount2 = $("#move_amount").val();
		var movement_sale = $("#movement_sale").val();
		var movement_sponsor = $("#movement_sponsor").val();
		var movement_consignment = $("#movement_consignment").val();
		$("#footer").html("สินค้าออกรวม : "+amount2+" ชิ้น ขาย : "+movement_sale+" ชิ้น สปอนเซอร์ : "+movement_sponsor+" ชิ้น ฝากขาย : "+movement_consignment+" ชิ้น");
		}
	}else if(i==2){
		$("#morris-chart-line").empty();
		line =  new Morris.Line({
 		element: 'morris-chart-line',
  		 data: [<?php if(isset($chart)&&$chart !=""){ echo $chart; }else{ echo $default; } ?>],
 		 xkey: 'd' ,
 		 ykeys:['move_in','move_out'],
  		 labels: ['สินค้าเข้า(ชิ้น)','สินค้าออก(ชิ้น)'],
		  smooth: false, 
		  parseTime: false,
		  yLabelFormat: function(y){ return y = Math.round(y); },
		  xLabelMargin:0
		});
		$("#i").val(1);
		$("#switch").html("รายระเอียดสินค้าออก");
		 if($("#movement_in").length){
			 var amount2 = $("#move_amount").val();
		var amount = $("#movement_in").val();
		$("#footer").html("สินค้าเข้า : "+amount+"  ชิ้น  สินค้าออก : "+amount2+" ชิ้น");
	}
	}
}
function switch_graph_detail(){

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
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', changeMonth:true, changeYear:true
	});
    });
$("#btn_ok").click(function(e){
	$("#chart_report").submit();
});
</script>