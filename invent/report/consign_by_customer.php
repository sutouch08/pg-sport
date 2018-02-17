
<?php 
	$page_name = "รายงานเอกสารตัดยอดสินค้าฝากขาย แยกตามลูกค้า เรียงตามเลขที่เอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<form id="report_form" method="post">
<div class="row">
	<div class="col-sm-8"><h4 style="margin-bottom:0px; margin-top:0px;"><i class="fa fa-list"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-4"><p class="pull-right">
       <a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-info' id='report'><i class="fa fa-file-text-o"></i>&nbsp;รายงาน</button></a>
		<a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-success' id='gogo'><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button></a></p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<div class='row'>
<!-- ++++++++++++++++++++++++++++  ลูกค้า +++++++++++++++++++++++++++++-->    
<div class="col-lg-7" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding:0px; height:100px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ลูกค้า</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-4" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='customer' id='customer1' value='0' checked="checked"/>&nbsp;
                </span>
                <label for="customer1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>
            
            <div class="col-lg-8">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='customer' id='customer2' value='1'  />
                        <label for="customer2" style="padding-left:10px; margin-bottom:0px;">เฉพาะ</label>
                        </span>
                        <input type="hidden" name="customer_selected" id="customer_selected"  />
                		<input type="text" name="txt_customer" id="txt_customer" class="form-control" disabled="disabled" />
                	</div>
             </div>
             
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end ลูกค้า  +++++++++++++++++++++++++++++--> 

<!-- ++++++++++++++++++++++++++++  วันที่ +++++++++++++++++++++++++++++-->    
<div class="col-lg-5" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding:0px; height:100px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">วันที่</legend>
        <div class="col-lg-12">&nbsp;</div>
        <div class="col-lg-6" style="margin-bottom:10px;">
        	<div class="input-group input-group-sm">
            <span class="input-group-addon">จาก</span>
        	<input type='text' name='from_date' id='from_date' class='form-control' />
            </div>
        </div>
        <div class="col-lg-6" style="margin-bottom:10px;">
        	<div class="input-group input-group-sm">
            <span class="input-group-addon">ถึง&nbsp;&nbsp;</span>
        	<input type='text' name='to_date' id='to_date' class='form-control' />
            </div>
        </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end วันที่  +++++++++++++++++++++++++++++--> 
</form>
 </div>
    

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' /><input type='hidden' id='customer' value='0' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     
<script>

$(document).ready(function(e) {
   $("#txt_customer").autocomplete({
		source:"controller/customerController.php?get_customer",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_customer").val();
			var arr = data.split(':');
			var id = arr[1];
			var name = arr[0];
			$("#customer_selected").val(id);
			$(this).val(name);
		}
	});			
});

$(document).ready(function() {
   	    $("#customer1").change(function(){
		$("#txt_customer").attr("disabled","disabled");
		$("#customer").val(0);
	});
});
$(document).ready(function() {
   	    $("#customer2").change(function(){
		$("#txt_customer").removeAttr("disabled");
		$("#customer").val(1);
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
	var customer = $("#customer").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
			return;
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
			return;
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	if(customer ==0){
		var customer_report = 'customer_all';
	}else if(customer ==1){
		var customer_select = $("#customer_selected").val();
		if(customer_select ==""){ alert("กรุณาเลือกลูกค้า"); $("#txt_customer").focus(); return; }
		var customer_report = "customer_selected="+customer_select;	
	}else{
		var customer_report = 'customer_all';
	}
	$("#report_form").attr("action","controller/reportController.php?export_consign_by_customer&"+view_report+"&customer="+customer+"&"+customer_report );
	$(this).attr("type", "submit");
});
});

$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	var customer = $("#customer").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
			return;
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
			return;
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	if(customer ==0){
		var customer_report = 'customer_all';
	}else if(customer ==1){
		var customer_select = $("#customer_selected").val();
		if(customer_select ==""){ alert("กรุณาเลือกลูกค้า"); $("#txt_customer").focus(); return; }
		var customer_report = "customer_selected="+customer_select;	
	}else{
		var customer_report = 'customer_all';
	}
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	$.ajax({
		url:"controller/reportController.php?consign_by_customer&"+view_report+"&customer="+customer+"&"+customer_report , type:"POST",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}
</script>