<?php 
	$page_name = "รายงานความถี่ในการสั่งสินค้า ตามช่วงเวลา";
	include("function/report_helper.php");
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" id="btn_report" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" id="btn_export" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row' >
	<div class="col-lg-2">
    	<label style="display:block;">รูปแบบ</label>
        <select class="form-control input-sm" name="role" id="role">
        	<option value="0">ทั้งหมด</option>
            <option value="1">ขาย</option>
            <option value="5">ฝากขาย</option>
            <option value="7">อภินันท์</option>
            <option value="4">สปอนเซอร์</option>
            <option value="26">แปรสภาพ</option>
        </select>
	</div>
	<div class="col-lg-2">
		<label >ช่วงวันที่</label>
		<input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="กำหนดวันที่เริ่มต้น" style="text-align:center;" autocomplete="off"  />
	</div> 
 	<div class="col-lg-2">
		<label style="visibility:hidden">ถึง</label>
		<input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="กำหนดวันที่สิ้นสุด" style="text-align:center;" autocomplete="off"  />
	</div>   
</div>  
		  
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>     
 
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
        	<tr><th colspan="12" style="text-align:center;">ตารางแจกแจงความถี่ในการสั่งสินค้าตามช่วงเวลาต่างๆ</th></tr>
        	<tr style="font-size:12px;">
            	<th rowspan="2" style="width: 12%; text-align:center; vertical-align:middle;">วันที่</th>
				<th colspan="11" style="width: 88%; text-align:center; vertical-align:middle;">ช่วงเวลา</th>
            </tr>
			<tr style="font-size:12px;">
				<th style="width: 8%; text-align:center; vertical-align:middle;">00:00</th>
            	<th style="width: 8%; text-align:center; vertical-align:middle;">08:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">09:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">10:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">11:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">12:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">13:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">14:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">15:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">16:00</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">17:00</th>
            </tr>
            {{#each this}}
            <tr style="font-size:10px;">
            	<td align="center">{{ date }}</td>
				<td align="center">{{ rank_0 }}</td>
				<td align="center">{{ rank_1 }}</td>
				<td align="center">{{ rank_2 }}</td>
				<td align="center">{{ rank_3 }}</td>
				<td align="center">{{ rank_4 }}</td>
				<td align="center">{{ rank_5 }}</td>
				<td align="center">{{ rank_6 }}</td>
				<td align="center">{{ rank_7 }}</td>
				<td align="center">{{ rank_8 }}</td>
				<td align="center">{{ rank_9 }}</td>
				<td align="center">{{ rank_10 }}</td>	
            </tr>
            {{/each}}
        </table>
</script>
<script>
$("#from_date").datepicker({
	dateFormat: "dd-mm-yy", 
	onClose: function(selectedDate){
		$("#to_date").datepicker( "option", "minDate", selectedDate);
	}
});
$("#to_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(selectedDate){
		$("#from_date").datepicker("option", "maxDate", selectedDate);
	}
});

function report()
{
	var role 	= $("#role").val();
	var from	= $("#from_date").val();
	var to		= $("#to_date").val();
	if( !isDate(from) || !isDate(to) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"report/reportController/otherReportController.php?order_freq&report",
		type:"POST", cache: "false", data:{ "role" : role, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs !="nodata" || rs != "")
			{
				var data 	= $.parseJSON(rs);
				var source	= $("#template").html();
				var output	= $("#rs");
				render(source, data, output);
				load_out();
			}else{
				load_out();
				swal("Error!!", "เกิดความผิดพลาดระห่วงส่งข้อมูล", "error");
				return false;	
			}
		}
	});
}

function export_report()
{
	var role 	= $("#role").val();
	var from	= $("#from_date").val();
	var to		= $("#to_date").val();
	if( !isDate(from) || !isDate(to) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;
	}
	var token		= new Date().getTime();
	get_download(token);
	window.location.href = "report/reportController/otherReportController.php?order_freq&export&role="+role+"&from="+from+"&to="+to+"&token="+token;
}

</script>