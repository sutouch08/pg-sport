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
    <div class='col-lg-1'><button type='submit' id='btn_ok' class='btn btn-default' >ตกลง</button></div>
	<div class='col-lg-2'><button type='button' class='btn btn-default' id='switch' style='display:inline-block; float:right;'>ดูยอดเงิน</button></div>
    </div>
</form>";
?>
<script>
	$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', changeMonth:true, changeYear:true
	});
    });
$("#btn_ok").click(function(e){
	$("#chart_report").submit();
});
</script>