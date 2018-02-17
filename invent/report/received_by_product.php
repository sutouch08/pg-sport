<?php 
	$page_name = $pageTitle;
?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-sm-2">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="btn-all-pd" onclick="all_product()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="btn-range-pd" onclick="range_product()">เลือกช่วง</button>
        </div>
        <input type="hidden" id="pRange" value="0" /> <!-- 0 = ทั้งหมด 1 = เป็นช่วง -->
	</div>
    <div class="col-sm-2 padding-5">
    	<label style="display:block; visibility:hidden;">สินค้า</label>
        <input type="text" class="form-control input-sm text-center pd" id="pFrom" placeholder="เริมต้น" disabled />
        <input type="hidden" id="id_pFrom" />
	</div>
    <div class="col-sm-2 padding-5">
    	<label style="display:block; visibility:hidden;">สินค้า</label>
        <input type="text" class="form-control input-sm text-center pd" id="pTo" placeholder="สิ้นสุด" disabled />
        <input type="hidden" id="id_pTo" />
	</div>
    <div class="col-sm-2">
    	<label style="display:block;">ช่วงเวลา</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="tmie_all" onClick="all_time()" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="time_rank" onClick="select_time()" value="2">เลือกเฉพาะ</button>
		</div>
	</div>
	<div class="col-sm-2">
		<label>จากวันที่</label>
		<input type="text" class="form-control input-sm" name="from_date" id="from_date" style="text-align:center;" disabled="disabled" />
	</div> 
 	<div class="col-sm-2">
		<label>ถึงวันที่</label>
		<input type="text" class="form-control input-sm" name="to_date" id="to_date" style="text-align:center;" disabled="disabled" />
	</div>   
    <div class="col-sm-1">
        <input type="hidden" name="rank" id="rank" value="1" />
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>    

<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead>
    	<tr>
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:35%;">รหัสสินค้า</th>
			<th style="width:35%;">ชื่อสินค้า</th>
            <th style="width:25%; text-align:right;">จำนวน</th>
        </tr>
    </thead>
    <tbody>
    {{#each this}}
    	{{#if @last}}
        <tr style="font-size:14px;">
        	<td colspan="3" align="right">รวม</td>
            <td align="right">{{ totalQty }}</td>
            <td></td>
        </tr>
        {{else}}
    	<tr style="font-size:12px;">
        	<td align="center">{{ no }}</td>
            <td>{{ pCode }}</td>
			<td>{{ pName }}</td>
            <td align="right">{{ received }}</td>
        </tr>
        {{/if}}
    {{/each}}
    </tbody>
</table>
</script>


<script>
function all_product()
{
	$("#btn-range-pd").removeClass('btn-primary');
	$("#btn-all-pd").addClass('btn-primary');
	$("#pRange").val(0);
	$(".pd").attr("disabled", "disabled");
}

function range_product()
{
	$("#btn-all-pd").removeClass('btn-primary');
	$("#btn-range-pd").addClass('btn-primary');
	$("#pRange").val(1);
	$(".pd").removeAttr("disabled");
	$("#pFrom").focus();
}

function all_time()
{
	$("#rank").val(1);
	$("#time_rank").removeClass("btn-primary");
	$("#tmie_all").addClass("btn-primary");
	$("#from_date").attr("disabled", "disabled");
	$("#to_date").attr("disabled", "disabled");
}

$("#pFrom").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?getProductCode",
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		var arr = rs.split(" | ");
		if( arr[0] != "nodata" ){
			$(this).val(arr[0]);
			$("#id_pFrom").val(arr[2]);
			var pTo = $("#pTo").val();
			var pFrom = $(this).val();
			if( pTo != "" && pFrom > pTo ){
				var idFrom	= $("#id_pFrom").val();
				var idTo		= $("#id_pTo").val();
				$("#pTo").val(	pFrom);
				$("#id_pTo").val(idFrom);
				$("#pFrom").val(pTo);
				$("#id_pFrom").val(idTo);
			}
		}
	}
});

$("#pTo").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?getProductCode",
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		var arr = rs.split(" | ");
		if( arr[0] != "nodata" ){
			$(this).val(arr[0]);
			$("#id_pTo").val(arr[2]);
			var pTo = $(this).val();
			var pFrom = $("#pFrom").val();
			if( pFrom != "" && pFrom > pTo){
				var idFrom	= $("#id_pFrom").val();
				var idTo		= $("#id_pTo").val();
				$("#pTo").val(	pFrom);
				$("#id_pTo").val(idFrom);
				$("#pFrom").val(pTo);
				$("#id_pFrom").val(idTo);
			}
		}
	}
});

$("#pFrom").focusout(function(e) {
    if($(this).val() == ""){
		$("#id_pFrom").val("");
	}else{
		$(this).removeClass('has-error');
	}
});

$("#pTo").focusout(function(e) {
    if($(this).val() == ""){
		$("#id_pTo").val("");
	}else{
		$(this).removeClass('has-error');
	}
});

function select_time()
{
	$("#rank").val(2);
	$("#time_rank").addClass("btn-primary");
	$("#tmie_all").removeClass("btn-primary");
	$("#from_date").removeAttr("disabled");
	$("#to_date").removeAttr("disabled");
	$("#from_date").focus();
}
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


function report()
{
	var pRange		= $("#pRange").val();
	var pFrom		= $("#pFrom").val();
	var pTo			= $("#pTo").val();
	var id_pFrom	= $("#id_pFrom").val();
	var id_pTo		= $("#id_pTo").val();
	var tRange		= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( pRange == 1 && (pFrom == "" || pTo == "" || id_pFrom == "" || id_pTo == "") )
	{
		swal({ type:"warning", title: "สินค้าไม่ถูกต้อง"}, function(){ productError(pFrom, id_pFrom, pTo, id_pTo) });
		return false;
	}
	if(tRange == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	load_in();
	$.ajax({
		url:"report/reportController/receiveReportController.php?receivedByProduct&report",
		type: "GET", cache: "false", data:{"pRange" : pRange, "pFrom" : pFrom, "pTo" : pTo, "tRange" : tRange, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs =="nodata")
			{
				load_out();
				var data = "<div class='col-sm-4 col-sm-offset-4'><div class='alert alert-info' style='margin-top:50px;'><center><strong>ไม่มีการรับสินค้าตามเงื่อนไขที่กำหนด</strong></center></div>";
				$("#rs").html(data);
			}else{
				var data 	= $.parseJSON(rs);
				var source 	= $("#template").html();
				var output 	= $("#rs");
				render(source, data, output);
				load_out();
			}
		}
	});	
}

function productError(pFrom, id_pFrom, pTo, id_pTo)
{
	setTimeout(function(){
		if(pFrom == "" || id_pFrom == ""){
			$("#pFrom").addClass("has-error");	
		}
		if(pTo == "" || id_pTo == ""){
			$("#pTo").addClass("has-error");
		}
		if(pFrom == ""){
			$("#pFrom").focus();
		}else if( pTo == "" ){
			$("#pTo").focus();
		}
	}, 200);
}

function export_report()
{
	var pRange		= $("#pRange").val();
	var pFrom		= $("#pFrom").val();
	var pTo			= $("#pTo").val();
	var id_pFrom	= $("#id_pFrom").val();
	var id_pTo		= $("#id_pTo").val();
	var tRange		= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( pRange == 1 && (pFrom == "" || pTo == "" || id_pFrom == "" || id_pTo == "") )
	{
		swal({ type:"warning", title: "สินค้าไม่ถูกต้อง"}, function(){ productError(pFrom, id_pFrom, pTo, id_pTo) });
		return false;
	}
	if(tRange == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/receiveReportController.php?receivedByProduct&export&pRange="+pRange+"&tRange="+tRange+"&pFrom="+pFrom+"&pTo="+pTo+"&from="+from+"&to="+to+"&token="+token;
}
</script>