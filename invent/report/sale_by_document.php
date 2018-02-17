<?php
	$page_name = "รายงานยอดขายแยกตามเลขที่เอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>

<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onClick="getReport()"><i class="fa fa-file-text-o"></i>&nbsp; รายงาน</button>
        <button type="button" class="btn btn-sm btn-info" onClick="doExport()"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
     	</p>
     </div>
</div>
<hr />
<!-- End page place holder -->

<div class='row'>
	<div class="col-sm-2 col-sm-offset-4">
   		<label>วันทึ่</label>
        <input type="text" name="fromDate" id="fromDate" class="form-control input-sm text-center" placeholder="เริ่มต้น" />
    </div>
    <div class="col-sm-2">
    	<label style="visibility:hidden">วันที่</label>
        <input type="text" name="toDate" id="toDate" class="form-control input-sm text-center" placeholder="สิ้นสุด" />
    </div>
</div>
<hr />
<div class='row'>
	<div class='col-lg-12' id='rs'></div>
</div>  
<script id="template" type="text/x-handlebars-template" >
	<table class="table table-bordered table-striped">
    <thead>
    	<tr>
        	<th style="width:15%; text-align:center;">วันที่</th>
            <th style="width:15%; text-align:center;">เอกสาร</th>
            <th style="width:40%; text-align:center;">ลูกค้า</th>
            <th style="width:10%; text-align:center;">จำนวนเงิน</th>
            <th style="width:10%; text-align:center;">ส่วนลด</th>
            <th style="width:10%; text-align:center;">สุทธิ</th>
        </tr>
    </thead>
    <tbody>
    {{#each this}}
    	{{# if @last}}
    <tr>
    	<td colspan="3" align="right"><strong>รวม</strong></td>
        <td align="right"><strong>{{ totalAmount }}</strong></td>
        <td align="right"><strong>{{ totalDiscount }}</strong></td>
        <td align="right"><strong>{{ totalNetAmount }}</strong></td>
    </tr>
    	{{else}}
    
    <tr>
    	<td>{{ date }}</td>
        <td align="center">{{ reference }}</td>
        <td>{{ customer }}</td>
        <td align="right">{{ amount }}</td>
        <td align="right">{{ discount }}</td>
        <td align="right">{{ netAmount }}</td>        
    </tr>
    	{{/if}}
	{{/each}}        
    </tbody>    
    </table>
</script>

</div><!--/ container -->  
<script>
function getReport()
{
	var from = $("#fromDate").val();
	var to		= $("#toDate").val();
	if( ! isDate(from) || ! isDate(to) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?getSaleReportDocument",
		type:"POST", cache:"false", data:{ "from" : from, "to" : to },
		success: function(rs){
			load_out();
			var rs 		= $.trim(rs);
			if( rs != 'nodata' )
			{
				var source 	= $("#template").html();
				var data		= $.parseJSON(rs);
				var output	= $("#rs");
				render(source, data, output);
			}
			else
			{
				$("#rs").html('<center><h4 style="margin-top:50px; color:#CCC;"><i class="fa fa-bar-chart"></i> ไม่มีรายการขายในช่วงเวลาที่กำหนด</h4></center>');
			}
		}
	});
}

function doExport()
{
	var from		= $("#fromDate").val();
	var to			= $("#toDate").val();
	if( ! isDate(from) || ! isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	var token = new Date().getTime();
	get_download(token);
	window.location.href = "report/reportController/saleReportController.php?exportSaleReportDocument&from="+from+"&to="+to+"&token="+token;		
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#toDate").datepicker('option', 'minDate', sd);
		if( $(this).val() !=''){
			$("#toDate").focus();
		}
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#fromDate").datepicker('option', 'maxDate', sd);	
	}
});


</script>