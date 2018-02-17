<div class="container">
<div class="row" style="height:35px;">
    <div class="col-lg-8" style="margin-top:10px;">
    	<h4 class="title">รายงานการรับสินค้าจากการแปรสภาพ แยกตามเลขที่เอกสาร</h4>
    </div>
    <div class="col-lg-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-primary btn-sm" id="report"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-success btn-sm" id="gogo"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
        </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-2">
    	<label style="display:block;">เลขที่เอกสาร</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="btn_all" onClick="select_all()">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="btn_range" onClick="select_range()">เลือกเป็นช่วง</button>
		</div>
	</div>
    <div class="col-lg-2">
    	<label>เริ่มต้น</label>
        <input type="text" class="form-control input-sm" id="from_doc" name="from_doc" placeholder="ระบุเลขที่เอกสารเริ่มต้น" disabled />
    </div>
     <div class="col-lg-2">
    	<label>สิ้นสุด</label>
        <input type="text" class="form-control input-sm" id="to_doc" name="to_doc" placeholder="ระบุเลขที่เอกสารสุดท้าย" disabled />
    </div>
	<div class="col-lg-2">
    	<label>จากวันที่</label>
        <input type="text" name="from_date" id="from_date" class="form-control input-sm" style="text-align:center" placeholder="ระบุวันที่เริ่มต้น" autocomplete="off" />
    </div>
    <div class="col-lg-2">
    	<label>ถึงวันที่</label>
        <input type="text" name="to_date" id="to_date" class="form-control input-sm" style="text-align:center" placeholder="ระบุวันที่สิ้นสุด" autocomplete="off" />
    </div>
</div>
<input type="hidden" name="range" id="range" value="0" />

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="result"><!---  แสดงผลลัพธ์ตามเงื่อนไขที่กำหนด  ---></div>
</div>
</div><!--- Container --->
<?php 
		$data = "";
		$qs = dbQuery("SELECT reference FROM tbl_receive_tranform ORDER BY reference");
		while($rs = dbFetchArray($qs) )
		{
			$data .= "'".$rs['reference']."', ";
		}
?>		
<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead>
    	<th style="width:10%;">วันที่</th>
        <th style="width:15%;">เลขที่เอกสาร</th>
		<th style="width:15%;">เลขที่อ้างอิง</th>
        <th style="width:10%; text-align:right">จำนวน</th>
		<th style="width:20%;">พนักงาน</th>
		<th>หมายเหตุ</th>
    </thead>
{{#each this}}
	{{#if nocontent}}
		<tr><td colspan="5"><center><h4>{{ nocontent }}</h4></center></td></tr>
	{{else}}
	<tr>
		<td>{{ date }}</td>
		<td><a href="javascript:void(0)" onclick="received_detail({{ id }})">{{ reference }}</a></td>
		<td>{{ order_reference }}</td>
		<td align="right">{{ qty }}</td>
		<td>{{ employee }}</td>
		<td>{{ remark }}</td>
	</tr>
	{{/if}}
{{/each}}
</table>
</script>

<div class='modal fade' id='receive_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal' style="width:900px;">
        <div class='modal-content'>
        	<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>										
			</div>
            <div class='modal-body' id='receive_detail'>
            	<center>----- No content -----</center>
            </div>
            <div class='modal-footer'>
            <input type="hidden" id="id_received" value="" />
            <button type="button" class="btn btn-info" onclick="print_received()"><i class="fa fa-print"></i> พิมพ์</button>
            <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-----------------  Received detail --------------->
<script id="received" type="text/x-handleblars-template">
{{#each this}}
{{#if @first}}
<div class="row">
	<div class="col-lg-3">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">เลขที่เอกสาร</label> <span>{{ reference }}</span></span>
    </div>
    <div class="col-lg-2">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">วันที่</label><span>{{ date }}</span></span>
    </div>
    <div class="col-lg-3">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">อ้างอิงใบเบิกสินค้า</label><span>{{ order_reference }}</span></span>
    </div>
    <div class="col-lg-4">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">ผู้ทำรายการ</label><span>{{ employee }}</span></span>
    </div>
    <div class="col-lg-12">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">หมายเหตุ</label><span>{{ remark }}</span></span>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />

<div class="row">
	<div class="col-lg-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = &nbsp; บันทึกแล้ว &nbsp;&nbsp; <i class="fa fa-remove" style="color:red;"></i>&nbsp; = &nbsp; ยังไม่บันทึก </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    	<table class='table table-striped'>
            <thead>
                <th style='width:5%; text-align:center;'>ลำดับ</th>
                <th style='width:15%;'>รหัส</th>
                <th>สินค้า</th>
                <th style="text-align:center; width: 10%;">โซน</th>
                <th style='width:10%; text-align:center;'>จำนวน</th>
                <th style="width:5%; text-align:center;">สถานะ</th>
            </thead>
{{else}}
	{{#if @last}}
            <tr>
                <td colspan="4" align="right"><h4>รวม</h4></td>
                <td align="center"><h4>{{ total_qty }}</h4></td>
                <td align="right"><h4>ชิ้น</h4></td>
             </tr>
        </table>
    	</div>
    </div>
    {{else}}
    	<tr style="font-size:10px;">
          	<td align="center">{{ no }}</td>
            <td>{{ product_reference }}</td>
            <td>{{ product_name }}</td>
            <td align="center">{{ zone }}</td>
            <td align="center">{{ qty }}</td>
            <td align="center">{{{ status }}}</td>
        </tr>
    {{/if}}
{{/if}}
{{/each}}             
 </script>
 
<script>
function received_detail(id)
{
	$.ajax({
		url:"report/reportController/storeReportController.php?get_received_tranform_detail",
		type:"POST", cache: "false", data:{ "id_received_tranform" : id },
		success: function(rs)
		{
			var rs = $.trim(rs);
			var source 	= $("#received").html();
			var data		= $.parseJSON(rs);
			var output	= $("#receive_detail");
			render(source, data, output);
			$("#id_received").val(id);
			$("#receive_modal").modal("show");
		}
	});
}

function print_received()
{
	var id = $("#id_received").val();
	window.open("controller/receiveTranformController.php?print&id_receive_tranform="+id, "_blank");
}

var docList = [<?php echo $data; ?>];

function select_all()
{
	$("#range").val(0);
	$("#btn_range").removeClass("btn-primary");
	$("#btn_all").addClass("btn-primary");
	$("#from_doc").attr("disabled", "disabled");
	$("#to_doc").attr("disabled", "disabled");
}

function select_range()
{
	$("#range").val(1);
	$("#btn_all").removeClass("btn-primary");
	$("#btn_range").addClass("btn-primary");
	$("#from_doc").removeAttr("disabled");
	$("#to_doc").removeAttr("disabled");
	$("#from_doc").focus();
}

$("#from_doc").autocomplete({
	source : docList,
	autoFocus : true,
	close : function()
	{
		var from = $(this).val();
		var to 	= $("#to_doc").val();
		if(to != "")
		{
			if(to < from)
			{
				$("#to_doc").val(from);
				$(this).val(to);
			}
		}
		$("#to_doc").focus();
	}
});

$("#to_doc").autocomplete({
	source : docList,
	autoFocus : true,
	close : function()
	{
		var to 	= $(this).val();
		var from	= $("#from_doc").val();	
		if( from != "")
		{
			if( to < from)
			{
				$("#from_doc").val(to);
				$(this).val(from);
			}
		}
	}
});

$("#from_date").datepicker({
	dateFormat: 'dd-mm-yy', onClose: function(selectedDate){ $("#to_date").datepicker("option", "minDate", selectedDate); }
});
$("#to_date").datepicker({
	dateFormat: 'dd-mm-yy', onClose: function(selectedDate){ $("#from_date").datepicker("option", "maxDate", selectedDate); }
});
	
$("#report").click(function(e) {
	var from 			= $("#from_date").val();
	var to				= $("#to_date").val();
	var range 		= $("#range").val();
	var from_doc 	= $("#from_doc").val();
	var to_doc 		= $("#to_doc").val();
	if( range == 1 && (from_doc == "" || to_doc == "") )
	{ 
		swal("กรุณาระบุเลขที่เอกสารทั้ง 2 ช่อง");
		return false;
	}
	if(!isDate(from) || !isDate(to))
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;	
	}
	get_report(range, from_doc, to_doc, from, to);	
});
	
$("#gogo").click(function(e) {
    var from 			= $("#from_date").val();
	var to				= $("#to_date").val();
	var range 		= $("#range").val();
	var from_doc 	= $("#from_doc").val();
	var to_doc 		= $("#to_doc").val();
	if( range == 1 && (from_doc == "" || to_doc == "") )
	{ 
		swal("กรุณาระบุเลขที่เอกสารทั้ง 2 ช่อง");
		return false;
	}
	if(!isDate(from) || !isDate(to))
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;	
	}
	go_export(range, from_doc, to_doc, from, to)
});
	
function get_report(range, from_doc, to_doc, from, to)
{
	load_in();
	$.ajax({
		url: "report/reportController/storeReportController.php?transform_by_document&report",
		type:"POST", cache:false,
		data:{"range" : range, "from_doc" : from_doc, "to_doc" : to_doc, "from_date" : from, "to_date" : to},
		success: function(data)
		{
			var source = $("#template").html();
			var data = $.parseJSON(data);
			var output = $("#result");
			render(source, data, output);
			load_out();	
		}
	});
}

function go_export(range, from_doc, to_doc, from, to)
{
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/storeReportController.php?transform_by_document&export&range="+range+"&from_doc="+from_doc+"&to_doc="+to_doc+"&from_date="+from+"&to_date="+to+"&token="+token;
}
</script>