<?php 
	$page_menu = "invent_stock_report";
	$page_name = $pageTitle;
	
	?>
<link rel="stylesheet" href="../library/css/morris-0.4.3.min.css"/>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-10"><h3><i class='fa fa-bar-chart'></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-2">
    <ul class="nav navbar-nav navbar-right">
    <?php 
	if(!isset($_POST['view'])){
		echo"
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;'>
                	<button type='button' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button>
                </a>
            </li>";
	}else{
		echo"
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;' href='index.php?content=sale_chart_zone'>
                	<button type='button' class='btn btn-link'><span class='fa fa-undo' style='color:#5cb85c; font-size:35px;'></span><br />กลับ</button>
                </a>
            </li>";
	}
	?>
        </ul>
        </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />

<!-- End page place holder -->
<?php 
		if(isset($_POST['view'])){ $view = $_POST['view']; }else{ $view = "year"; }
		if(isset($_POST['from_date']) &&$_POST['from_date'] != "เลือกวัน"){ 
		$from_date = date('d-m-Y',strtotime($_POST['from_date'])); 
		$from_dated = date('d-m-Y',strtotime($_POST['from_date']));
		} else { 
		$from_date = "เลือกวัน";
		$from_dated = date('d-m-Y');
		} 
		switch($view){
			case "week":
			$rang = getWeek($from_dated);
			$from = $rang['from'];
			$to = $rang['to'];
			$title_rang = "วันที่ &nbsp;".thaiDate($from)."&nbsp;ถึง &nbsp;".thaiDate($to);
			break;
			case "month":
			$rang = getMonth($from_dated);
			$from = $rang['from'];
			$to = $rang['to'];
			$title_rang = "เดือน &nbsp;".getThaiMonthName($from_dated)."&nbsp;".date("Y", strtotime($from_dated));
			break;
			case "year":
			$rang = getYear($from_dated);
			$from = $rang['from'];
			$to = $rang['to'];
			$title_rang = "ปี &nbsp;".date("Y", strtotime($from_dated));
			break;
		}
if(!isset($_POST['view'])){
	echo"
<form id='chart_report' method='post'>
<div class='row' ><button type='submit' id='btn_submit' style='display:none'>submit</button>
    <div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>การแสดงผล</span><select name='view' id='view' class='form-control' >"; get_view_list($view); echo"</select></div></div>
    <div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' name='from_date' id='from_date' class='form-control' value='$from_date'/></div></div>
    </div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	";
}

	if(isset($_POST['view'])){
		$sale = new sale();
	$bar_data = "";
		$pie_data = "";
		$title = "แสดงยอดขายเปรียบเทียบพื้นที่การขาย";
		$result = $sale->groupLeaderBoard($from, $to);
		$n = 1;
		$total_amount = 0;
		foreach($result as $data){
			$zone_name = $data['zone_name'];
			$amount = $data['sale_amount'];
			$bar_data .=  "{ label: '".$zone_name."', data: ".$amount." },";
			$pie_data .= "{ label: '".$zone_name."', data: ".$amount." },";
			$total_amount = $total_amount+$amount;
			$n++;
		}
				

echo"
<div class='row'>
          <div class='col-lg-12'>
            <div class='panel panel-primary'>
              <div class='panel-heading'><h3 class='panel-title'><i class='fa fa-line-chart'></i>&nbsp;&nbsp; $title &nbsp; $title_rang</h3></div>
              <div class='panel-body'> <div id='morris-chart-bar' style='height:500px;'></div></div> 
			  <div class='panel-footer'><h3 class='panel-title' id='footer'>ยอดขายรวม : ".number_format($total_amount,2)." บาท</h3></div>
          </div>    
        </div>
		<div class='col-lg-12'>
			<div class='panel panel-primary'>
              <div class='panel-heading'><h3 class='panel-title'><i class='fa fa-line-chart'></i>&nbsp;&nbsp; $title &nbsp; $title_rang</h3></div>
              <div class='panel-body'><div id='donut_chart' style='height:500px;'></div></div> 
			   <div class='panel-footer'><h3 class='panel-title' id='footer'>ยอดขายรวม : ".number_format($total_amount,2)." บาท</h3></div>
            </div>
          </div>    
        </div>
 </div>";
}
 ?>
 </div>
 <style>/*
.multiselect {
    width:100%;
    min-height:200px;
	padding-bottom:20px;
    border:solid 1px #c0c0c0;
    overflow:auto;
}
 
.multiselect label {
    display:block;
	margin-left:10px;
}
 
.multiselect-on {
    color:#ffffff;
    background-color:#000099;
}*/
</style>
<script src="../library/js/raphael-min.js"></script>
<script src="../library/js/morris-0.4.3.min.js"></script> 
<script src="../library/js/plugins/flot/jquery.flot.js"></script>
<script src="../library/js/plugins/flot/jquery.flot.pie.js"></script>

<script>
var line = new Morris.Bar({
  element: 'morris-chart-bar',
  data: [	
	<?php if(isset($bar_data)){ echo $bar_data; } ?>
  ],
  xkey: 'label' ,
  ykeys:['data'],
  labels: ['จำนวน(บาท)'],
  parseTime: false,
  yLabelFormat: function(y){ return Math.round(y) },
  xLabelMargin:0
});
</script>
<script>
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
// pie chart -> flot chart
var data = [ <?php if(isset($pie_data)){ echo $pie_data; } ?> ];
$.plot('#donut_chart', data, { 
		series: {	
				 pie: { 	
						show: true,
						label:{
							show:true,
							formatter: labelFormatter, //กำหนดรูปแบบของตัวหนังสือ ด้วย function labelFormatter ข้างล่าง
						},
						combine: { // รวมรายการที่มี เปอร์เซ็นไม่ถึงที่กำหนด ให้เป็นอันใหญ่อยู่ใน other 
							color: '#999',
							threshold: 0.05 //ถ้าน้อยกว่า 5% (0.05) ให้รวมไว้ที่ other
						}
					 }
				},	
		legend: {	
				show: false	
				}
    });
	function labelFormatter(label, series) { /// กำหนดรูปแบบ Label
  //return "" + label + "<br/>" +series.percent + "%";
    return "<span style='font-size:15px; font-weight:bold; color:"+series.color+"; text-align:center;'>"+ label+ "<br/>"+ Math.round(series.percent)+"%<br> "+numberWithCommas(series.data[0][1])+" บาท</span>"
}
</script>

<script>
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', changeMonth:true, changeYear:true
	});
});
 $("#report").click(function(){
	 $("#btn_submit").click();
 });
</script>