<?php
	$page_name = "รายงานยอดขาย แยกตามพนักงานและเอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='index.php?content=sale_amount_document&report=y' method='post'>
<div class="row">
	<div class="col-sm-8"><h3 class="title"><i class="fa fa-file-text-o"></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-4">
       <p class="pull-right">
        <button type="button" class="btn btn-success" id="report"><i class="fa fa-file-text-o"></i>&nbsp; รายงาน</button>
        <button type="button" class="btn btn-success" id="gogo"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
     	</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<div class='row'>
	<div class='col-lg-6'>
        <table width='100%' style='border-right:0px'>
            <tr>
                <td colspan='4' style='text-align:center; border-bottom:1px solid #CCC; padding:10px;'><h4 style='margin:0px;'>พนักงานขาย</h4></td>
            </tr>
            <tr>
                <td style='width:20%; padding-top:10px;'><input type='radio' name='sale' id='sale1' value='0' checked='checked' /><label for='sale1' style='padding-left:15px;'>ทั้งหมด</label></td>
                <td colspan='3' style='padding-right:10px;'></td>
            </tr>
            <tr>
                <td style='width:20%; padding-top:10px;'><input type='radio' name='sale' id='sale2' value='1'/><label for='sale2' style='padding-left:15px; margin-right:10px;'>ตั้งแต่ :</label></td>
                <td style='width:35%; padding-top:10px;'><select id='sale_from' name='sale_from' class='form-control' disabled><?php echo saleList(); ?></select></td>
                <td style='width:10%; padding-left:10px; padding-top:10px;'>ถึง :</td>
                <td style='width:35%; padding-right:10px; padding-top:10px;'><select id='sale_to' name='sale_to' class='form-control' disabled><?php echo saleList(); ?></select></td>
            </tr>
            <tr>
                <td style='width:20%; padding-top:10px;'><input type='radio' name='sale' id='sale3' value='2'/><label for='sale3' style='padding-left:15px;'>เฉพาะ :</label></td>
                <td colspan ='3' style='width:30%; padding-right:10px; padding-top:10px;'><select id='sale_selected' name='sale_selected' class='form-control' disabled ><?php echo saleList(); ?></select></td>
            </tr>
        </table>
	</div>
    <div class='col-lg-6'>
        <table width='100%' style='border-right:0px'>
            <tr>
            	<td colspan='4' style='text-align:center; border-bottom:1px solid #CCC; padding:10px;'><h4 style='margin:0px;'>การแสดงผล</h4></td>
            </tr>
            <tr>
            	<td colspan='4' style='width:100%; padding-top:10px;'><input type='radio' name='view' id='view_all' value='0'/><label for='view_all' style='padding-left:15px;'>ทั้งหมด</label></td>
            </tr>
            <tr>
            	<td style='width:20%; padding-top:10px;'><input type='radio' name='view' id='view_in' value='1' checked='checked' /><label for='view_in' style='padding-left:15px;'>แสดงเป็น :</label></td>
                <td style='width:30%; padding-left:10px; padding-top:10px;'><select name='view_selected' id='view_selected' class='form-control' ><?php echo get_view_list($view); ?></select></td>
                <td colspan='2'></td>	
            </tr>
            <tr>
            	<td style='width:20%; padding-top:10px;'><input type='radio' name='view' id='view_rank' value='2' /><label for='view_rank' style='padding-left:15px;'>ระหว่าง :</label></td>
                <td style='width:35%; padding-left:10px; padding-top:10px;'><input type='text' name='from_date' id='from_date' class='form-control' disabled /></td>	
                <td style='width:10%; padding-top:10px;'><label for='view_rank' style='padding-left:15px;'>ถึง :</label></td>
                <td style='width:35%; padding-left:10px; padding-top:10px;'><input type='text' name='to_date' id='to_date' class='form-control' disabled /></td>
            </tr>
        </table>
    </div>
	 </form>
</div>   
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' /><input type='hidden' id='view' value='1' /><input type='hidden' id='sale' value='0' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     
<script>

$(document).ready(function() {
   	    $("#sale1").change(function(){
		$("#sale").val(0);
		$("#sale_from").attr("disabled","disabled");
		$("#sale_to").attr("disabled","disabled");
		$("#sale_selected").attr("disabled","disabled");
	});
});

$(document).ready(function() {
   	    $("#sale2").change(function(){
		$("#sale").val(1);
		$("#sale_from").removeAttr("disabled");
		$("#sale_to").removeAttr("disabled");
		$("#sale_selected").attr("disabled","disabled");
		
	});
});

$(document).ready(function() {
   	    $("#sale3").change(function(){
		$("#sale").val(2);
		$("#sale_from").attr("disabled","disabled");
		$("#sale_to").attr("disabled","disabled");
		$("#sale_selected").removeAttr("disabled");
	});
});
$(document).ready(function() {
   	    $("#view_all").change(function(){
		$("#view_selected").attr("disabled","disabled");
		$("#from_date").attr("disabled","disabled");
		$("#to_date").attr("disabled","disabled");
		$("#view").val(0);
	});
});
$(document).ready(function() {
   	    $("#view_in").change(function(){
		$("#view_selected").removeAttr("disabled");
		$("#from_date").attr("disabled","disabled");
		$("#to_date").attr("disabled","disabled");
		$("#view").val(1);
	});
});
$('input').on('ifChecked', function(event){
 $(this).change();
});
$(document).ready(function() {
   	    $("#view_rank").change(function(){
		$("#from_date").removeAttr("disabled");
		$("#to_date").removeAttr("disabled");
		$("#view_selected").attr("disabled","disabled");
		$("#view").val(2);
	});
});
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });

$(document).ready(function(e) {
    $("#gogo").click(function(){	
	var sale = $("#sale").val();
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'view_all';
	}else if(view ==1){
		var view_select = $("#view_selected").val();
		var view_report = "view_selected="+view_select;
	}else if(view ==2){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	}else{
		var view_report = 'view_all';
	}
	if(sale ==0){
		var sale_rank = 'sale_all';
	}else if(sale ==1){
		var sale_from = $("#sale_from").val();
		var sale_to = $("#sale_to").val();
		var sale_rank = "sale_from="+sale_from+"&sale_to="+sale_to;
	}else if(sale==2){
		var sale_selected = $("#sale_selected").val();
		var sale_rank = "sale_selected="+sale_selected
	}else{
		var sale_rank = "sale_all";
	}
	$("#report_form").attr("action","controller/reportController.php?export_sale_amount_document&view="+view+"&"+view_report+"&sale="+sale+"&"+sale_rank );
	$(this).attr("type", "submit");
});
});

$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	var sale = $("#sale").val();
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'view_all';
	}else if(view ==1){
		var view_select = $("#view_selected").val();
		var view_report = "view_selected="+view_select;
	}else if(view ==2){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	}else{
		var view_report = 'view_all';
	}
	if(sale ==0){
		var sale_rank = 'sale_all';
	}else if(sale ==1){
		var sale_from = $("#sale_from").val();
		var sale_to = $("#sale_to").val();
		var sale_rank = "sale_from="+sale_from+"&sale_to="+sale_to;
	}else if(sale==2){
		var sale_selected = $("#sale_selected").val();
		var sale_rank = "sale_selected="+sale_selected
	}else{
		var sale_rank = "sale_all";
	}
	$.ajax({
		url:"controller/reportController.php?sale_amount_document&view="+view+"&"+view_report+"&sale="+sale+"&"+sale_rank , type:"GET",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}
</script>