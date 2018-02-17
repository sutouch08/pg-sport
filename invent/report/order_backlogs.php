<?php 
	$pageName = "รายงานออเดอร์ ค้างส่ง";
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
    <div class="col-sm-2">
    	<label style="display:block;">ลูกค้า</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="cAll" onClick="cOption(0)">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="cRange" onClick="cOption(1)">เลือกช่วง</button>
        </div>
    </div>
    <div class="col-sm-2">
    	<label style="display:block; visibility:hidden">ช่วง</label>
        <input type="text" class="form-control input-sm text-center c-range" name="cFrom" id="cFrom" placeholder="เริ่มต้น" disabled />
       
    </div>
     <div class="col-sm-2">
    	<label style="display:block; visibility:hidden">ช่วง</label>
        <input type="text" class="form-control input-sm text-center c-range" name="cTo" id="cTo" placeholder="สิ้นสุด" disabled />
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
    
    <input type="hidden" name="d-options" id="d-options" value="0" />
    <input type="hidden" name="c-options" id="c-options" value="0" />
    <input type="hidden" name="cid-From" id="cid-From" />
    <input type="hidden" name="cid-To" id="cid-To" />
</div>
<hr style="margin-top:10px;"/>
<div class="row">
	<div class="col-sm-12" id="rs"></div>
</div><!--/ row -->
</div><!--/ Container -->

<script id="orderTemplate" type="text/x-handlebars-template">
<table class="table table-striped">
<thead>
	<tr>
    	<th style="width:5%; text-align:center;">ลำดับ</th>
        <th style="width:15%;">ออเดอร์</th>
        <th style="width:30%;">ลูกค้า</th>
        <th style="width:15%;">เงื่อนไข</th>
        <th style="width:10%; text-align:center;">ยอดเงิน</th>
        <th style="width:10%; text-align:center;">สถานะ</th>
        <th style="width:15%; text-align:center;">วันที่</th>        
    </tr>
    <tbody>
    {{#each this}}
    	{{#if @last}}
            <tr>
                <td colspan="5" align="right"><h4>รวม</h4></td>
                <td colspan="2" align="center"><h4>{{ totalAmount }}</h4></td>
            </tr>
		{{else}}           
            <tr style="font-size:12px;">
                <td align="center">{{ no }}</td>
                <td>{{ reference }}</td>
                <td>{{ customer }}</td>
                <td>{{ payment }}</td>
                <td align="center">{{ amount }}</td>
                <td align="center">{{ status }}</td>
                <td align="center">{{ date_add }}</td>
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
	var dOption = $("#d-options").val();
	var cOption	= $("#c-options").val();
	var cFrom	= $("#cid-From").val();
	var cTo		= $("#cid-To").val();
	var from		= $("#dFrom").val();
	var to			= $("#dTo").val();
	if( dOption == 1 && (! isDate( from ) || ! isDate(to) ) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( cOption == "1" && ( cFrom == "" || cTo == "" ) ){ swal("ลูกค้าไม่ถูกต้อง"); return false; }
	
	var token	= new Date().getTime();
	var url		= 'report/reportController/orderReportController.php?exportOrderBacklogs&dOption='+dOption+'&cOption='+cOption+'&cFrom='+cFrom+'&cTo='+cTo+'&from='+from+'&to='+to+'&token='+token;
	get_download(token);
	window.location.href = url;
}


function getReport()
{
	var dOption = $("#d-options").val();
	var cOption	= $("#c-options").val();
	var cFrom	= $("#cid-From").val();
	var cTo		= $("#cid-To").val();
	var from		= $("#dFrom").val();
	var to			= $("#dTo").val();
	if( dOption == 1 && (! isDate( from ) || ! isDate(to) ) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( cOption == "1" && ( cFrom == "" || cTo == "" ) ){ swal("ลูกค้าไม่ถูกต้อง"); return false; }
	load_in();
	$.ajax({
		url:"report/reportController/orderReportController.php?getOrderBacklogs",
		type:"POST", cache:"false", data:{ "dOption" : dOption, "cOption" : cOption, "cFrom" : cFrom, "cTo" : cTo, "from" : from, "to" : to },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var source = $("#orderTemplate").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#rs");
			render(source, data, output);
		}
	});
			
}

$("#cFrom").autocomplete({
	source: 'controller/autoComplete.php?get_customer',
	minLength: 1,
	autoFocus: true,
	select: function(event, ui){
		$(this).attr('rel', ui.item.label);
	},
	open: function(){
		$(this).attr('rel', 0);
	},
	close: function(){
		if( $(this).attr('rel') == '0'){
			$(this).val('');
			$(this).focus();
		}else{
			var ds = $(this).val();
			var rs = ds.split(' | ');
			var name = rs[1];
			var id		= rs[2];
			$(this).val(name);
			$("#cid-From").val(id);
			$("#cTo").focus();
		}
		reorderCustomer();
	}
});

$("#cTo").autocomplete({
		source: 'controller/autoComplete.php?get_customer',
		minLength: 1,
		autoFocus: true,
		select: function(event, ui){
			$(this).attr('rel', ui.item.label);
		},
		open: function(){
			$(this).attr('rel', 0);
		},
		close: function(){
			if( $(this).attr('rel') == '0'){
				$(this).val('');
				$(this).focus();
			}else{
				var ds = $(this).val();
				var rs = ds.split(' | ');
				var name = rs[1];
				var id = rs[2];
				$(this).val(name);
				$("#cid-To").val(id);
			}
			reorderCustomer();
		}
});

function reorderCustomer()
{
	var from 		= $("#cid-From").val();
	var to			= $("#cid-To").val();
	var cFrom 	= $("#cFrom").val();
	var cTo		= $("#cTo").val();
	if( from != '' && to != ''){
		if( from > to ){
			$("#cid-From").val(to);
			$("#cid-To").val(from);
			$("#cFrom").val(cTo);
			$("#cTo").val(cFrom);
		}
	}
}



$("#dFrom").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#dTo").datepicker('option', 'minDate', sd);
		if($(this).val() != ''){ $("#dTo").focus(); }
	}
});
$("#dTo").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#dFrom").datepicker('option', 'maxDate', sd);
	}
});

function cOption(id)
{
	if( id == 0 )
	{
		$(".c-range").attr('disabled', 'disabled');
		$("#cRange").removeClass('btn-primary');
		$("#cAll").addClass('btn-primary');
	}else if( id == 1 ){
		$(".c-range").removeAttr('disabled');
		$("#cAll").removeClass('btn-primary');
		$("#cRange").addClass('btn-primary');
		$("#cFrom").focus();
	}
	$("#c-options").val(id);	
}

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
</script>