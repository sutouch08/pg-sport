<?php $pageName = 'รายงานค่าขนส่งสินค้า(ออนไลน์)'; ?>

<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-success" onClick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
            <button type="button" class="btn btn-sm btn-info" onClick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>            
    </div>
</div><!--/ top row -->
<hr/>
<div class="row">
	<div class="col-sm-2">
    	<label>วันที่</label>
    	<input type="text" class="form-control input-sm text-center" name="fromDate" id="fromDate" placeholder="เริ่มต้น" />
    </div>
    <div class="col-sm-2">
    	<label style="visibility:hidden;">วันที่</label>
    	<input type="text" class="form-control input-sm text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" />
    </div>
</div><!--/ row -->
<hr/>
<div class="row">
	<div class="col-sm-12" id="rs"></div>
</div>

<script id="reportTemplate" type="text/x-handlebars-template">
<table class="table table-striped">
<thead>
	<tr>
    	<th style="width:10%; text-align:center;">ลำดับ</th>
        <th style="width:15%;">ออเดอร์</th>
        <th style="width:20%;">ลูกค้า</th>
        <th style="width:20%;">พนักงาน</th>
        <th style="width:10%; text-align:center;">จำนวน</th>
        <th style="width:10%; text-align:center;">ค่าขนส่ง</th>
    </tr>
</thead>
<tbody>
{{#each this}}
	{{#if @last}}
    <tr>
    	<td colspan="4" align="right"><strong>รวม</strong></td>
        <td align="center"><strong>{{ qty }}</strong></td>
        <td align="center"><strong>{{ amount }}</strong></td>
    </tr>
    {{else}}
	<tr>
    	<td align="center">{{ no }}</td>
        <td>{{ reference }}</td>
        <td>{{ customer }}</td>
        <td>{{ emp }}</td>
        <td align="center">{{ qty }}</td>
        <td align="center">{{ amount }}</td>
    </tr>
    {{/if}}
{{/each}}    
</tbody>
</table>
</script>
</div><!--/ Container -->
<script>
function doExport()
{
	var from 	= $("#fromDate").val();
	var to		= $("#toDate").val();
	if( ! isDate(from) || ! isDate(to) ){ 
		swal('วันที่ไม่ถูกต้อง'); 
		return false;	
	}
	
	var token = new Date().getTime();
	var url 	= "report/reportController/otherReportController.php?exportDeliveryFeeReport&from_date="+from+"&to_date="+to+"&token="+token;
	get_download(token);
	window.location.href = url;
}

function getReport()
{
	var from	= $("#fromDate").val();
	var to		= $("#toDate").val();
	if( !isDate(from) || !isDate(to) ){
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}
	load_in();
	$.ajax({
		url:"report/reportController/otherReportController.php?getDeliveryFeeReport",
		type:"POST", cache:"false", data: { "from_date" : from, "to_date" : to },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var source = $("#reportTemplate").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#rs");
				render(source, data, output);	
			}
		}
	});
}


$("#fromDate").datepicker({
	dateFormat : 'dd-mm-yy',
	onClose: function(se){
		$("#toDate").datepicker('option', 'minDate', se);
	}
});
$("#toDate").datepicker({
	dateFormat : 'dd-mm-yy',
	onClose: function(se){
		$("#fromDate").datepicker('option', 'maxDate', se);
	}
});

</script>