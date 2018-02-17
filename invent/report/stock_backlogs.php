<?php 
	$pageName = "รายงานสินค้า ค้างส่ง";
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-success" onClick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
            <button type="button" class="btn btn-sm btn-info" onClick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
        </p>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-2 padding-right-5">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="pAll" onClick="pdOption(0)">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50"id="pRange" onClick="pdOption(1)">เลือกช่วง</button>
        </div>
    </div>
    <div class="col-sm-3 padding-left-5">
    	<label style="display:block; visibility:hidden">ช่วง</label>
        <input type="text" class="form-control input-sm input-discount text-center p-range" name="pdFrom" id="pdFrom" placeholder="เริ่มต้น" disabled />
        <input type="text" class="form-control input-sm input-unit text-center p-range" name="pdTo" id="pdTo" placeholder="สิ้นสุด" disabled />
    </div>

	 <div class="col-sm-2">
    	<label style="display:block;">วันที่</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="dAll" onClick="dOption(0)">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="dRange" onClick="dOption(1)">เลือกช่วง</button>
        </div>
    </div>
    <div class="col-sm-2">
    	<label style="display:block; visibility:hidden;" >วันที่</label>
        <input type="text" class="form-control input-sm text-center d-range" name="dFrom" id="dFrom" placeholder="เริ่มต้น" disabled />
    </div>
    <div class="col-sm-2">
    	<label style="display:block; visibility:hidden;" >วันที่</label>
        <input type="text" class="form-control input-sm text-center d-range" name="dTo" id="dTo" placeholder="สิ้นสุด" disabled  />
    </div>
    
    <input type="hidden" name="p-options" id="p-options" value="0" />
    <input type="hidden" name="d-options" id="d-options" value="0" />
</div>
<hr style="margin-top:10px;"/>
<div class="row">
	<div class="col-sm-12" id="rs"></div>
</div><!--/ row -->
</div><!--/ Container -->

<script id="itemTemplate" type="text/x-handlebars-template">
<table class="table table-striped">
<thead>
	<tr>
    	<th style="width:5%; text-align:center;">ลำดับ</th>
        <th style="width:15%;">สินค้า</th>
        <th style="width:15%;">ออเดอร์</th>
        <th style="width:30%;">ลูกค้า</th>
		<th style="width:15%;">เงื่อนไข</th>
        <th style="width:10%; text-align:center;">จำนวน</th>
        <th style="width:10%; text-align:center;">สถานะ</th>	
    </tr>
    <tbody>
    {{#each this}}
    	{{#if @last}}
            <tr>
                <td colspan="5" align="right"><h4>รวม</h4></td>
                <td colspan="2" align="center"><h4>{{ totalQty }}</h4></td>
            </tr>
		{{else}}           
            <tr style="font-size:12px;">
                <td align="center">{{ no }}</td>
                <td>{{ reference }}</td>
				<td>{{ order }}</td>
                <td>{{ customer }}</td>
                <td>{{ payment }}</td>
                <td align="center">{{ qty }}</td>
                <td align="center">{{ status }}</td>
            </tr>
		{{/if}}
	{{/each}}            	
    </tbody>
</thead>
</table>
</script>


<script>
function doExport()
{
	var pOption = $("#p-options").val();
	var dOption	= $("#d-options").val()
	var pdFrom	= $("#pdFrom").val();
	var pdTo		= $("#pdTo").val();
	var from		= $("#dFrom").val();
	var to			= $("#dTo").val();
	if( dOption == 1 && (! isDate(from) || ! isDate(to) ) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( pOption == "1" && (pdFrom == "" || pdTo == "" ) ){ swal("สินค้าไม่ถูกต้อง"); return false; }
	
	var token = new Date().getTime();
	var url = "report/reportController/orderReportController.php?exportItemBacklogs&pOption="+pOption+"&pdFrom="+pdFrom+"&pdTo="+pdTo+"&dOption="+dOption+"&from="+from+"&to="+to+"&token="+token;
	get_download(token);
	window.location.href = url;
}

function getReport()
{
	var pOption = $("#p-options").val();
	var dOption	= $("#d-options").val()
	var pdFrom	= $("#pdFrom").val();
	var pdTo		= $("#pdTo").val();
	var from		= $("#dFrom").val();
	var to			= $("#dTo").val();
	if( dOption == 1 && (! isDate(from) || ! isDate(to) ) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( pOption == "1" && (pdFrom == "" || pdTo == "" ) ){ swal("สินค้าไม่ถูกต้อง"); return false; }
	
	load_in();
	$.ajax({
		url:"report/reportController/orderReportController.php?getItemBacklogs",
		type:"POST", cache:"false", data:{ "pOption" : pOption, "pdFrom" : pdFrom, "pdTo" : pdTo, "dOption" : dOption, "from" : from, "to" : to },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var source 	= $("#itemTemplate").html();
			var data		= $.parseJSON(rs);
			var output	= $("#rs");
			render(source, data, output);
		}
	});			
}

$("#pdFrom").autocomplete({
	source: 'controller/autoComplete.php?product_code',
	minLength: 1,
	autoFocus: true,
	select : function(event, ui) {
        $(this).attr('rel',ui.item.label);
    },
    open : function() {
        $(this).attr('rel', 0);
    },
    close : function() {                    
        if($(this).attr('rel') =='0'){
            $(this).val('');
			$(this).focus();
		}else{
			$("#pdTo").focus();
		}
		reOrderProductCode();
    }
});

$("#pdTo").autocomplete({
	source: 'controller/autoComplete.php?product_code',
	minLength: 1,
	autoFocus: true,
	select : function(event, ui) {
        $(this).attr('rel',ui.item.label);
    },
    open : function() {
        $(this).attr('rel', 0);
    },
    close : function() {                    
        if($(this).attr('rel') =='0'){
            $(this).val('');
			$(this).focus();
		}
		reOrderProductCode();
    }
});

function reOrderProductCode()
{
	var from = $("#pdFrom").val();
	var to		= $("#pdTo").val();
	if( from != "" && to != "" )
	{
		if( from > to ){
			$("#pdFrom").val(to);
			$("#pdTo").val(from);
		}
	}
}


$("#dFrom").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#dTo").datepicker('option', 'minDate', sd);
	}
});
$("#dTo").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#dFrom").datepicker('option', 'maxDate', sd);
	}
});

function dOption(id)
{
	if( id == 0 )
	{
		$(".d-range").attr('disabled', 'disabled');
		$("#dRange").removeClass('btn-primary');
		$("#dAll").addClass('btn-primary');
	}else if( id == 1 ){
		$(".d-range").removeAttr('disabled');
		$("#dAll").removeClass('btn-primary');
		$("#dRange").addClass('btn-primary');
		$("#dFrom").focus();
	}
	$("#d-options").val(id);	
}

function pdOption(id)
{
	if( id == 0 )
	{
		$(".p-range").attr('disabled', 'disabled');
		$("#pRange").removeClass('btn-primary');
		$("#pAll").addClass('btn-primary');	
	}else if( id == 1 ){
		$(".p-range").removeAttr('disabled');
		$("#pAll").removeClass('btn-primary');
		$("#pRange").addClass('btn-primary');
		$("#pdFrom").focus();
	}
	$("#p-options").val(id);	
}
</script>