<div class="container">
<div class="row" style="height:35px;">
    <div class="col-lg-8" style="margin-top:10px;">
    	<h4 class="title">รายงานการรับสินค้าแยกตามเลขที่เอกสาร</h4>
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
		$qs = dbQuery("SELECT reference FROM tbl_receive_product ORDER BY reference");
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
		<th style="width:15%;">ใบสั่งซื้อ</th>
		<th style="width:15%;">ใบส่งสินค้า</th>
        <th style="width:10%; text-align:right">จำนวน</th>
		<th style="width:10%; text-align:right;">มูลค่า</th>
		<th style="width:20%;">พนักงาน</th>
		<th>หมายเหตุ</th>
    </thead>
{{#each this}}
	{{#if nocontent}}
		<tr><td colspan="5"><center><h4>{{ nocontent }}</h4></center></td></tr>
	{{else}}
	<tr style="font-size:12px;">
		<td>{{ date }}</td>
		<td><a href="javascript:void(0)" onclick="received_detail({{ id }})">{{ reference }}</a></td>
		<td><a href="javascript:void(0)" onclick="po_detail({{ id_po }})">{{ po }}</a></td>
		<td>{{ invoice }}</td>
		<td align="right">{{ qty }}</td>
		<td align="right">{{ amount }}</td>
		<td>{{ employee }}</td>
		<td>{{ remark }}</td>
	</tr>
	{{/if}}
{{/each}}
</table>
</script>

<div class='modal fade' id='po_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal_po' style="width:900px;;">
        <div class='modal-content'>
        	<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>										
			</div>
            <div class='modal-body' id='po_detail' style="padding-top: 20px;">
            	<center>----- No content -----</center>
            </div>
            <div class='modal-footer'>
            <input type="hidden" id="id_po" value="" />
            <button type="button" class="btn btn-info" onclick="print_po()"><i class="fa fa-print"></i> พิมพ์</button>
            <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>

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
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ reference }}</span>
    </div>
    <div class="col-lg-2">
    	<label >วันที่</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ date_add }}</span>
    </div>
    <div class="col-lg-3">
    	<label >ใบส่งสินค้า</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ invoice }}</span>
    </div>
    <div class="col-lg-2">
    	<label>ใบสั่งซื้อ</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ po_reference }}</span>
    </div>
    <div class="col-lg-3">
    	<label>ผู้ทำรายการ</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ employee }}</span>
    </div>
    <div class="col-lg-12">
    	<label>หมายเหตุ</label>
    	<span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ remark }}</span>
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
 
<!------------------  PO detail ------------------->     
<script id="po" type="text/x-handlebars-template">
{{#each this}}
	{{#if @first}}                           
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;" >{{ reference }}</span>
    </div>
    <div class="col-lg-2">
    	<label>วันที่เอกสาร</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ date_add }}</span>
    </div>
    <div class="col-lg-2">
    	<label>รหัสผู้ขาย</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ sup_code }}</span>
    </div>
    <div class="col-lg-4">
    	<label>ชื่อผู้ขาย</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ supplier }}</span>
    </div>
    <div class="col-lg-2">
    	<label>กำหนดรับสินค้า</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;">{{ due_date }}</span>
    </div>    
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-bordered" style="margin-bottom:0px;">
        	<thead style="font-size:10px;">
            	<th style="width:5%; text-align:center">ลำดับ</th>
                <th style="width:15%;">รหัสสินค้า</th>
                <th>รายละเอียด</th>
                <th style="width:10%; text-align:right;">จำนวน</th>
                <th style="width:6%; text-align:right;">ราคา/หน่วย</th>
                <th style="width:6%; text-align:center">ส่วนลด</th>
                <th style="width:5%; text-align:center;">หน่วย</th>
                <th style="width:10%; text-align:right;">จำนวนเงิน</th>
                <th style="width:10%; text-align:right;">รับแล้ว</th>
            </thead>
           
	{{else}} 
		 {{#if @last}}
					</table>
					 <table class="table table-bordered" style="margin-bottom:50px;">
						<tbody>
						<tr>
							<td style="width:60%; text-align:right;">
								ส่วนลดท้ายบิล : <span class="form-control input-sm" style="border:0px; display:inline; height:20px; text-align:right" >{{ bill_discount }}</span>
							</td>
							<td style="width:20%;"><strong>จำนวนรวม</strong></td>
							<td align="right" style="width:20%; padding-right:5px;"><span id="total_qty">{{ total_qty }}</span></td>
						</tr>
						<tr>
							<td rowspan="3" style="font-size:12px;"><strong style="font-size:14px;">หมายเหตุ : </strong>{{ remark }}</td>
							<td><strong>ราคารวม</strong></td>
							<td align="right" style="padding-right:5px;"><span id="total_price">{{ total_price }}</span></td>
						</tr>
						<tr>
							<td ><strong>ส่วนลดรวม</strong></td>
							<td align="right" style="width:20%; padding-right:5px;"><span id="total_discount">{{ total_discount }}</span></td>
						</tr>
						<tr>
							<td><strong>ยอดเงินสุทธิ</strong></td>
							<td align="right" style="padding-right:5px;"><span id="total_amount">{{ net_amount }}</span></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		{{else}}           		
			<tr style="font-size:10px;">
				<td align="center" style="border-left:solid 1px #DDD;">{{ no }}</td>
				<td style="border-left:solid 1px #DDD;">{{ product_reference }}</td>
				<td style="border-left:solid 1px #DDD;">{{ product_name }}</td>
				<td align="right" style="border-left:solid 1px #DDD;">{{ qty }}</td>
				<td align="right" style="border-left:solid 1px #DDD;">{{ price }}</td>
				<td align="center" style="border-left:solid 1px #DDD;">{{ discount }}</td>
				<td align="center" style="border-left:solid 1px #DDD;">{{ unit }}</td>
				<td align="right" style="border-left:solid 1px #DDD;">{{ total_amount }}</td>
				<td align="right" style="border-left:solid 1px #DDD;">{{ received }}</td>
			</tr>
		{{/if}}       
	{{/if}}
{{/each}}
</script>    
<script>
function received_detail(id)
{
	$.ajax({
		url:"controller/receiveProductController.php?get_received_product",
		type:"POST", cache: "false", data:{ "id_received_product" : id },
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
	window.open("controller/receiveProductController.php?print&id_receive_product="+id, "_blank");
}
function po_detail(id_po)
{
	$.ajax({
		url:"controller/poController.php?get_po_detail",
		type:"POST", cache:"false", data: { "id_po" : id_po },
		success: function(rs)
		{
			var rs 		= $.trim(rs);
			var source 	= $("#po").html();
			var data 		= $.parseJSON(rs);
			var output 	= $("#po_detail");
			render(source, data, output);
			$("#id_po").val(id_po);
			$("#po_modal").modal("show");
		}
	});
}

function print_po()
{
	var id_po = $("#id_po").val();
	window.open("controller/poController.php?print_barcode&id_po="+id_po, "_blank");
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
		url: "report/reportController/storeReportController.php?received_by_document&report",
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
	window.location.href="report/reportController/storeReportController.php?received_by_document&export&range="+range+"&from_doc="+from_doc+"&to_doc="+to_doc+"&from_date="+from+"&to_date="+to+"&token="+token;
}
</script>