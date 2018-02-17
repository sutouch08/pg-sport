<?php 
	$page_name = $pageTitle;
?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="report()"><i class="fa fa-bar-chart"></i>&nbsp; แสดงรายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออกเป็น Excel</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="printReport()"><i class="fa fa-print"></i>&nbsp; พิมพ์รายงาน</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-sm-2">
    	<label class="display-block">สินค้า</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pdAll" onclick="pdAll()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="btn-pdRange" onclick="pdRange()">เลือกช่วง</button>
        </div>
        <input type="hidden" id="pdRange" value="0" />
    </div>
	<div class="col-sm-2">
    	<label class="not-show">สินค้า</label>
        <input type="text" class="form-control input-sm text-center pd" id="pFrom" placeholder="เริมต้น" disabled />
        <input type="hidden" id="id_pFrom" />
	</div>
    <div class="col-sm-2">
    	<label class="not-show">สินค้า</label>
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
<input type="hidden" id="id_pd" />
<div class='modal fade' id='detailGrid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal' style="width:600px;">
        <div class='modal-content'>
            <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            <center><h4 class='modal-title' id='modal_title'>&nbsp;</h4></center>
            </div>
            <div class='modal-body' id='modal_body'>
            	<table class="table table-striped table-bordered">
                	<thead>
                    	<th style="width:5%; text-align:center;">ลำดับ</th>
                        <th style="width:30%; text-align:center;">สินค้า</th>
                        <th style="width:20%; text-align:center;">ใบสั่งซื้อ</th>
                        <th style="width:15%; text-align:center;">จำนวนสั่ง</th>
                        <th style="width:15%; text-align:center;">รับแล้ว</th>
                        <th style="width:15%; text-align:center;">ค้างรับ</th>
                    </thead>
                    <tbody id="detailTable">
                    </tbody>
                </table>            
            </div>
            <div class='modal-footer'>
            <button type="button" class="btn btn-sm btn-primary" onclick="printDetail()"><i class="fa fa-print"></i> พิมพ์</button>
            <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>
<script id="detail-template" type="text/x-handlebars-template">
{{#each this}}    
	{{#if @last}}
	<tr>
		<td colspan="3" align="right">รวม</td>
		<td align="center">{{ totalPO }}</td>
		<td align="center">{{ totalReceived }}</td>
		<td align="center">{{ totalBalance }}</td>
	</tr>
	{{else}}
    <tr>
        <td align="center">{{ no }}</td>
        <td>{{ pCode }}</td>
        <td align="center">{{ PO }}</td>
        <td align="center">{{ pQty }}</td>
        <td align="center">{{ rQty }}</td>
        <td align="center">{{ bQty }}</td>
    </tr>
	{{/if}}
{{/each}}    
</script>


<script id="template" type="text/x-handlebars-template">
<table class="table table-striped table-bordered">
	<thead>
    	<tr>
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:25%; text-align:center;">รหัสสินค้า</th>
			<th style="width:25%; text-align:center;">ชื่อสินค้า</th>
            <th style="width:10%; text-align:center;">สั่งซื้อ</th>
            <th style="width:10%; text-align:center;">รับแล้ว</th>
            <th style="width:10%; text-align:center;">ค้างรับ</th>
            <th style="width:15%; text-align:center;">รายละเอียด</th>
        </tr>
    </thead>
    <tbody>
    {{#each this}}
    	{{#if @last}}
        <tr style="font-size:14px;">
        	<td colspan="3" align="right">รวม</td>
            <td align="right">{{ totalPoQty }}</td>
            <td align="right">{{ totalReceivedQty }}</td>
            <td align="right">{{ totalBalanceQty }}</td>
            <td></td>
        </tr>
        {{else}}
    	<tr style="font-size:12px;">
        	<td align="center">{{ no }}</td>
            <td>{{ pCode }}</td>
			<td>{{ pName }}
            <td align="right">{{ poQty }}</td>
            <td align="right">{{ receivedQty }}</td>
            <td align="right">{{ balanceQty }}</td>
            <td align="center"><button type="button" class="btn btn-xs btn-info" onclick="getDetail({{ id_product }})">รายละเอียด</button></td>
        </tr>
        {{/if}}
    {{/each}}
    </tbody>
</table>
</script>


<script>
function getDetail(id_pd)
{
	var time_rank	= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	$("#id_pd").val(id_pd);
	load_in();
	$.ajax({
		url:"report/reportController/poReportController.php?product_backlog_by_product&detail"	,
		type:"GET", cache:false, data:{"id_product" : id_pd, "tRange" : time_rank, "from" : from, "to" : to },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) )
			{
				var source 	= $("#detail-template").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailTable");
				render(source, data, output);	
			}
			else
			{
				var html = '<tr><td colspan="6" align="center">ไม่พบข้อมูล</td></tr>';
				$("#detailTable").html(html);
			}
			
			$("#detailGrid").modal('show');
		}
	});
}

function pdAll()
{
	$("#btn-pdRange").removeClass('btn-primary');
	$("#btn-pdAll").addClass('btn-primary');
	$("#pdRange").val(0);
	$(".pd").attr("disabled", "disabled");
}

function pdRange()
{
	$("#btn-pdAll").removeClass('btn-primary');
	$("#btn-pdRange").addClass('btn-primary');
	$("#pdRange").val(1);
	$(".pd").removeAttr('disabled');	
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
	var pdRange	= $("#pdRange").val();
	var pFrom		= $("#pFrom").val();
	var pTo			= $("#pTo").val();
	var id_pFrom	= $("#id_pFrom").val();
	var id_pTo		= $("#id_pTo").val();
	var time_rank	= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( pdRange == 1 && (pFrom == "" || pTo == "" || id_pFrom == "" || id_pTo == "") )
	{
		swal({ type:"warning", title: "สินค้าไม่ถูกต้อง"}, function(){ productError(pFrom, id_pFrom, pTo, id_pTo) });
		return false;
	}
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	load_in();
	$.ajax({
		url:"report/reportController/poReportController.php?product_backlog_by_product&report",
		type: "GET", cache: "false", data:{"pdRange" : pdRange, "pFrom" : pFrom, "pTo" : pTo, "time_rank" : time_rank, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs =="nodata")
			{
				load_out();
				var data = "<div class='col-sm-4 col-sm-offset-4'><div class='alert alert-info' style='margin-top:50px;'><center><strong>ไม่มีใบสั่งซื้อค้างรับ ตามเงื่อนไขที่กำหนด</strong></center></div>";
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
	var pdRange	= $("#pdRange").val();
	var pFrom		= $("#pFrom").val();
	var pTo			= $("#pTo").val();
	var id_pFrom	= $("#id_pFrom").val();
	var id_pTo		= $("#id_pTo").val();
	var tRange		= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( pdRange == 1 && (pFrom == "" || pTo == "" || id_pFrom == "" || id_pTo == "") )
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
	var url = "report/reportController/poReportController.php?product_backlog_by_product&export&pdRange="+pdRange+"&pFrom="+pFrom+"&pTo="+pTo+"&tRange="+tRange+"&from="+from+"&to="+to+"&token="+token;
	window.location.href = url;
}

function printReport()
{
	var pdRange	= $("#pdRange").val();
	var pFrom		= $("#pFrom").val();
	var pTo			= $("#pTo").val();
	var id_pFrom	= $("#id_pFrom").val();
	var id_pTo		= $("#id_pTo").val();
	var tRange		= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( pdRange == 1 && (pFrom == "" || pTo == "" || id_pFrom == "" || id_pTo == "") )
	{
		swal({ type:"warning", title: "สินค้าไม่ถูกต้อง"}, function(){ productError(pFrom, id_pFrom, pTo, id_pTo) });
		return false;
	}
	if(tRange == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	var url = "report/reportController/poReportController.php?product_backlog_by_product&printReport&pdRange="+pdRange+"&pFrom="+pFrom+"&pTo="+pTo+"&tRange="+tRange+"&from="+from+"&to="+to;
	printOut(url);
}

function printDetail()
{
	var id_pd		= $("#id_pd").val();
	var tRange		= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( id_pd == "" )
	{
		swal("สินค้าไม่ถูกต้อง");
		return false;	
	}
	
	if(tRange == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	
	var url = "report/reportController/poReportController.php?product_backlog_by_product&printDetail&id_product="+id_pd+"&tRange="+tRange+"&from="+from+"&to="+to;
	printOut(url);
}
</script>