<?php 
	$page_menu = "invent_stock_report";
	$page_name = $pageTitle;
	
	?>
<link rel="stylesheet" href="../library/css/morris-0.4.3.min.css"/>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><i class='fa fa-bar-chart'></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
    <ul class="nav navbar-nav navbar-right">
    <?php 
	if(!isset($_POST['att_select'])){
		echo"
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;'>
                	<button type='button' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button>
                </a>
            </li>";
	}else{
		echo"
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;' href='index.php?content=attribute_chart_report'>
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
		if(isset($_POST['att_select'])){ $attribute = $_POST['att_select']; }else{ $attribute = "color"; }
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
/************************** ถ้าไม่ได้เลือกสีไหนเลย ********************************/		
function all_color(){
	$arr = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
	$colors = array();	
	foreach( $arr as $a){
				$sql = dbQuery("SELECT id_color, color_code, color_name FROM tbl_color WHERE color_code LIKE '$a' ORDER BY color_code");
				if(dbNumRows($sql)<1){
				}else{
				$color = dbFetchArray($sql);
				$id_color = $color['id_color'];
				array_push($colors,$id_color);
			}
	}
		return $colors;
	}
	
/************************** ถ้าไม่ได้เลือกไซด์ไหนเลย ********************************/		
function all_size(){
	$sql = dbQuery("SELECT id_size, size_name FROM tbl_size ORDER BY position ASC");
	$all_size = array();
	while($r = dbFetchArray($sql)){
		$id_size = $r['id_size'];
		array_push($all_size, $id_size);
	}
	return $all_size;
}
/************************** ถ้าไม่ได้เลือก attribute ไหนเลย ********************************/		
function all_attribute(){
	$sql = dbQuery("SELECT id_attribute, attribute_name FROM tbl_attribute ORDER BY position ASC");
	$all_attribute = array();
	while($r = dbFetchArray($sql)){
		$id_attribute = $r['id_attribute'];
		array_push($all_attribute, $id_attribute);
	}
	return $all_attribute;
}
function color_grid(){
	echo"
	<div class='panel-body' id='color_grid'><table class='table table-striped'>";
	echo"<thead><th colspan='8'><label><input type='checkbox' name='all_color' id='all_color' style='margin-right:10px;'/>ทั้งหมด</label></th></thead>";
	$arr = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
	$i = 0;
	$n = 0;
	$row = count($arr);
	foreach( $arr as $a){
				$sql = dbQuery("SELECT id_color, color_code, color_name FROM tbl_color WHERE color_code LIKE '$a' ORDER BY color_code");
				if(dbNumRows($sql)<1){
				}else{
				if($i==0){ echo "<tr>"; }
				$color = dbFetchArray($sql);
				$id_color = $color['id_color'];
				$color_name = $color['color_code']." : ".$color['color_name'];
				echo"<td><label><input type='checkbox' name='color[]' value='$id_color' style='margin-right:10px;'/>$color_name</label></td>";
				$i++;
				$n++;
				if($n == $row){  if($i<8){ $m =$i%8; echo "<td colspan='$m'>&nbsp;</td></tr>"; break; }else{ echo "</tr>"; break; }}
				if($i==8){ echo "</tr>"; $i=1;}
			}
	}
	echo"</table></div>";
	}
	function size_grid(){
	echo"
	<div class='panel-body' id='size_grid'>	<table class='table table-striped'>";
	echo"<thead><thcolspan='8'><label><input type='checkbox' name='all_size' id='all_size' style='margin-right:10px;'/>ทั้งหมด</label></th></thead>";
	$sql = dbQuery("SELECT id_size, size_name FROM tbl_size ORDER BY position ASC");
	$row = dbNumRows($sql);
	$n=0;
	$i=0;
	while($r = dbFetchArray($sql)){
		$id_size = $r['id_size'];
		$size = $r['size_name'];
		if($i==0){ echo "<tr>"; }
		echo"<td><label><input type='checkbox' name='size[]' value='$id_size' style='margin-right:10px;'/>$size</label></td>";
		$i++;
		$n++;
		if($n == $row){  if($i<8){ $m =$i%8; echo "<td colspan='$m'>&nbsp;</td></tr>"; break; }else{ echo "</tr>"; break; } }
		if($i==8){ echo "</tr>"; $i=0;}
	}
		echo"</table></div>";
	}
	function attribute_grid(){
	echo"
	<div class='panel-body' id='attirbute_grid'>	<table class='table table-striped'>";
	echo"<thead><th colspan='8'><label><input type='checkbox' name='all_attribute' id='all_attribute' style='margin-right:10px;'/> ทั้งหมด</label></th></thead>";
	$sql = dbQuery("SELECT id_attribute, attribute_name FROM tbl_attribute ORDER BY position ASC");
	$row = dbNumRows($sql);
	$n=0;
	$i=0;
	while($r = dbFetchArray($sql)){
		$id_attribute = $r['id_attribute'];
		$attribute = $r['attribute_name'];
		if($i==0){ echo "<tr>"; }
		echo"<td><label><input type='checkbox' name='attribute[]' value='$id_attribute' style='margin-right:10px;'/>$attribute</label></td>";
		$i++;
		$n++;
		if($n == $row){  if($i<8){ $m =$i%8; echo "<td colspan='$m'>&nbsp;</td></tr>"; break; }else{ echo "</tr>"; break; }}
		if($i==8){ echo "</tr>"; $i=0;}
	}
		echo"</table></div>";
	}
	
?> 


<?php
if(!isset($_POST['att_select'])){
	echo"
<form id='chart_report' method='post'>
<div class='row' ><button type='submit' id='btn_submit' style='display:none'>submit</button>
	<div class='col-lg-3'><div class='input-group'>
			<span class='input-group-addon'>คุณลักษณะ</span>
			<select name='att_select' class='form-control' id='select_att'>
				<option value='color'"; if($attribute =="color"){ echo"selected='selected'"; } echo" >สี</option>
				<option value='size'"; if($attribute =="size"){ echo"selected='selected'"; } echo">ไซด์</option>
				<option value='attribute'"; if($attribute =="attribute"){ echo"selected='selected'"; } echo">คุณลักษณะอื่นๆ</option>
			</select>
			</div>
    </div>
	<div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>กลุ่มสินค้า</span><select name='category' id='category' class='form-control' >".category_list()."</select></div></div>
    <div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>การแสดงผล</span><select name='view' id='view' class='form-control' >"; get_view_list($view); echo"</select></div></div>
    <div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' name='from_date' id='from_date' class='form-control' value='$from_date'/></div></div>
    </div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	";
echo"<div class='row' id='color_row'>
		<div class='col-lg-12'>
		<div class='panel panel-primary'>
			<div class='panel-heading'> เลือกสีต่างๆเพื่อแสดงกราฟเปรียบเทียบแต่ละตัว</div>";
					 color_grid(); 
echo"</div></div></div>";
echo"<div class='row' id='size_row' style='display:none;'>
		<div class='col-lg-12'>
		<div class='panel panel-primary'>
			<div class='panel-heading'> เลือกไซด์ต่างๆเพื่อแสดงกราฟเปรียบเทียบแต่ละตัว</div>";
 size_grid(); 
echo"</div></div></div>";
echo"<div class='row' id='attribute_row' style='display:none;'>
		<div class='col-lg-12'>
		<div class='panel panel-primary'>
			<div class='panel-heading'> เลือกคุณลักษณะต่างๆเพื่อแสดงกราฟเปรียบเทียบแต่ละตัว</div>";
 attribute_grid(); 
echo"</div></div></div>";
}
?>
</form>
<?php
	if(isset($_POST['att_select'])){
		$att_select = $_POST['att_select'];
		$id_category = $_POST['category']; // ไอดี ของหมวดหมู่ที่เลือกแสดง
		if($id_category ==0){ $category = ""; $category_name = "ทั้งหมด"; }else{ $category = "id_category = $id_category AND"; $category_name = get_category_name($id_category);}
		$bar_data = "";
		$pie_data = "";
		if($att_select =="color"){
			$title = "แสดงจำนวนสีที่ขายไป";
			
			if(isset($_POST['color'])){ $colors = $_POST['color']; }else{ $colors = all_color(); }
			foreach($colors as $id_color){
				list($code, $color_name) = dbFetchArray(dbQuery("SELECT color_code, color_name FROM tbl_color WHERE id_color = $id_color"));
				$sql = dbQuery("SELECT id_color FROM tbl_color WHERE color_code LIKE '".$code."%'");
				$in_case ="";
				$i = 0;
				$row = dbNumRows($sql);
				while($rs = dbFetchArray($sql)){
					$id = $rs['id_color'];
					$in_case .= $id;
					$i++;
					if($i<$row){ $in_case .=","; }
				}
				$total_qty =0; //เก็บยอดรวมของสี
				//หายอดรวมของสีที่ขายออกจาก temp
								
				$sqo = dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold LEFT JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product WHERE $category id_role = 1 AND id_color IN($in_case) AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to')");
				list($cat_qty) = dbFetchArray($sqo);	
				//หายอดรวมของสีจาก order_consign_detail
				$qty1 = $cat_qty;
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product WHERE $category id_color IN($in_case) AND (tbl_order_consign_detail.date_upd BETWEEN '$from' AND '$to')");
				$rw = dbNumRows($sqm);
				if($rw>0){ list($qty2) = dbFetchArray($sqm); }else{ $qty2 = 0; } ///ยอดจาก ฝากขายที่ตัดยอดขาย
				$total_qty = $qty1+$qty2;
			
				$bar_data .=  "{ label: '".$color_name."', data: ".$total_qty." },";
				$pie_data .= "{ label: '".$color_name."', data: ".$total_qty." },";
				
				}//end foreach // จบ color 
		}else if($att_select =="size"){
			$title = "แสดงจำนวนไซด์ที่ขายไป";
			if(isset($_POST['size'])){ $sizes = $_POST['size']; }else{ $sizes = all_size(); } //ตัวแปรมาเป็น Array
			foreach($sizes as $size){ //วนรอบตามจำนวนสมาชิกของตัวแปร Array
				$total_qty =0; //เก็บยอดรวมของไซด์
				list($size_name) = dbFetchArray(dbQuery("SELECT size_name FROM tbl_size WHERE id_size = $size")); //ชื่อไซด์
				//หายอดรวมของไซด์ที่ขายออกจาก temp
				$sqo = dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold LEFT JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product WHERE $category id_role=1 AND id_size = $size AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to')");
				list($cat_qty) = dbFetchArray($sqo);
				//หายอดรวมของไซด์จาก order_consign_detail
				$qty1 = $cat_qty;
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product WHERE $category id_size = $size AND (tbl_order_consign_detail.date_upd BETWEEN '$from' AND '$to')");
				$rw = dbNumRows($sqm);
				if($rw>0){ list($qty2) = dbFetchArray($sqm); }else{ $qty2 = 0; } ///ยอดจาก ฝากขายที่ตัดยอดขาย
				$total_qty = $qty1+$qty2; //รวมยอดสองที่
				$bar_data .=  "{ label: '".$size_name."', data: ".$total_qty." },";
				$pie_data .=  "{ label: '".$size_name."', data: ".$total_qty." },";
				}//end foreach // จบ size 		
		}else if($att_select =="attribute"){
			$title = "แสดงจำนวนคุณลักษณะต่างๆที่ขายไป";
			if(isset($_POST['attribute'])){ $attributes = $_POST['attribute']; }else{ $attributes = all_attribute(); }
			foreach($attributes as $att ){
				$total_qty =0; //เก็บยอดรวม
				list($att_name) = dbFetchArray(dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = $att")); //ชื่อ attribute
				//หายอดรวมของ attribute ที่ขายออกจาก temp	
				$sql = dbQuery("SELECT SUM(sold_qty) FROM tbl_order_detail_sold LEFT JOIN tbl_product_attribute ON tbl_order_detail_sold.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product WHERE $category id_role = 1 AND id_attribute = $att AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to') ");
				$ro = dbNumRows($sql);
				if($ro >0){ list($qty1) = dbFetchArray($sql); }else{ $qty1 = 0; }
				//หายอดรวมของ attribute จาก order_consign_detail
				$sqm = dbQuery("SELECT SUM(qty) AS qty FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_attribute = $att AND (tbl_order_consign_detail.date_upd BETWEEN '$from' AND '$to')");
				$rw = dbNumRows($sqm);
				if($rw>0){ list($qty2) = dbFetchArray($sqm); }else{ $qty2 = 0; } ///ยอดจาก ฝากขายที่ตัดยอดขาย
				$total_qty = $qty1+$qty2; //รวมยอดสองที่
				$bar_data .=  "{ label: '".$att_name."', data: ".$total_qty." },";
				$pie_data .=  "{ label: '".$att_name."', data: ".$total_qty." },";
				}//end foreach // จบ size 	
		}//end if
echo"
<div class='row'>
          <div class='col-lg-6'>
            <div class='panel panel-primary'>
              <div class='panel-heading'><h3 class='panel-title'><i class='fa fa-line-chart'></i>&nbsp;&nbsp; $title &nbsp; $title_rang หมวดหมู่ $category_name</h3></div>
              <div class='panel-body'> <div id='morris-chart-bar' style='height:500px;'></div></div> 
          </div>    
        </div>
		<div class='col-lg-6'>
			<div class='panel panel-primary'>
              <div class='panel-heading'><h3 class='panel-title'><i class='fa fa-line-chart'></i>&nbsp;&nbsp; $title &nbsp; $title_rang หมวดหมู่ $category_name</h3></div>
              <div class='panel-body'><div id='donut_chart' style='height:500px;'></div></div> 
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
  labels: ['จำนวน(ชิ้น)'],
  parseTime: false,
  yLabelFormat: function(y){ return Math.round(y) },
  xLabelMargin:0
});
</script>
<script>
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
    return "<span style='font-size:15px; font-weight:bold; color:"+series.color+"; text-align:center;'>"+ label+ "<br/>"+ Math.round(series.percent)+"%</span>"
}
</script>

<script>
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', changeMonth:true, changeYear:true
	});
});
$(function(){
     $(".well").multiselect();
});
$("#all_color").click(function(){
      $(":checkbox[name='color[]']").prop('checked', this.checked);   
 });
	  $("#all_size").click(function(){
     		 $(":checkbox[name='size[]']").prop('checked', this.checked);   
    });
   $("#all_attribute").click(function(){
     	 $(":checkbox[name='attribute[]']").prop('checked', this.checked);   
    });
 $("#select_att").change(function(e){
	var id = $(this).val();
	if(id =="color"){
		$(":checkbox[name='all_size']").prop('checked',false);   //uncheck select all
		$(":checkbox[name='size[]']").prop('checked',false);   //uncheck all
		$(":checkbox[name='all_attribute']").prop('checked',false);   
		$(":checkbox[name='attribute[]']").prop('checked',false);   
		 $("#size_row").css("display","none");
		 $("#attribute_row").css("display","none");
		 $("#color_row").css("display","");
	}else if(id =="size"){
		$(":checkbox[name='all_color']").prop('checked',false);   //uncheck select all
		$(":checkbox[name='color[]']").prop('checked',false);   //uncheck all
		$(":checkbox[name='all_attribute']").prop('checked',false);   
		$(":checkbox[name='attribute[]']").prop('checked',false);   
		$("#color_row").css("display","none");
		$("#attribute_row").css("display","none");
		$("#size_row").css("display","");
	}else if(id =="attribute"){
		$(":checkbox[name='all_size']").prop('checked',false);   //uncheck select all
		$(":checkbox[name='size[]']").prop('checked',false);   //uncheck all
		$(":checkbox[name='all_color']").prop('checked',false);   
		$(":checkbox[name='color[]']").prop('checked',false);   
		$("#color_row").css("display","none");
		$("#size_row").css("display","none");
		$("#attribute_row").css("display","");
	}
 });
 $("#report").click(function(){
	 $("#btn_submit").click();
 });

</script>