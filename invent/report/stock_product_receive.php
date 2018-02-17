<?php 
	$page_name = "รายงานการรับสินค้าจากการซื้อ";
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
<div class='row' style="margin-left:-2.5px; margin-right:-2.5px;">
	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
    	<label style="display:block;">ใบสั่งซื้อ</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="po_1" onClick="all_po()" >ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="po_2" onClick="select_po()" >เฉพาะ</button>
		</div>
	</div>
	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label>ค้นหา</label>
		<input type="text" class="form-control input-sm" name="reference" id="reference" style="text-align:center;" placeholder="PO หรือ ผู้ขาย" disabled />
	</div> 
    <div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
    	<label style="display:block;">ผู้ขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="sup_1" onClick="all_sup()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="sup_2" onClick="select_sup()">เฉพาะ</button>
		</div>
	</div>
	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label>ค้นหา</label>
		<input type="text" class="form-control input-sm" name="sup_name" id="sup_name" style="text-align:center;" placeholder="รหัส หรือ ชื่อผู้ขาย" disabled />
	</div> 
 	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label style="display:block;">วันที่เอกสาร</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="rank_1" onClick="all_rank()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="rank_2" onClick="select_rank()">ช่วง</button>
       </div>
	</div>   
    <div class="col-lg-1" style="padding-left:2.5px; padding-right:2.5px;">
		<label>จากวันที่</label>
		<input type="text" class="form-control input-sm" name="from_date" id="from_date" style="text-align:center;" placeholder="เลือกวันที่" disabled />
	</div>
    <div class="col-lg-1" style="padding-left:2.5px; padding-right:2.5px;">
		<label>ถึงวันที่</label>
		<input type="text" class="form-control input-sm" name="to_date" id="to_date" style="text-align:center;" placeholder="เลือกวันที่" disabled />
	</div>
    <input type="hidden" name="po" id="po" value="1" />
    <input type="hidden" name="sup" id="sup" value="1"  />
    <input type="hidden" name="id_sup" id="id_sup" value=""  />
    <input type="hidden" name="rank" id="rank" value="1"  />
		
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>     
 
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
        	<tr>
				<th colspan="10" style="text-align:center;">
					รายงานการรับสินค้าเข้าจากการซื้อแสดงรายการสินค้า 
					<span class='pull-right'><button type='button' class='btn btn-info btn-xs' onclick="print_report()"><i class='fa fa-print'></i>&nbsp; พิมพ์</button></span>
				</th>
			</tr>
        	<tr style="font-size:12px;">
            	<th style="width: 5%; text-align:center; vertical-align:middle;">ลำดับ</th>
				<th style="width: 10%; text-align:center; vertical-align:middle;">วันที่</th>
                <th style="width: 10%; vertical-align:middle;">รหัสสินค้า</th>
				<th style="width: 10%; text-align:center; vertical-align:middle;">เลขที่เอกสาร</th>
				<th style="width: 10%; text-align:center; vertical-align:middle;">เลขที่ใบสั่งซื้อ</th>
				<th style="width: 15%; text-align:center; vertical-align:middle;">ผู้ขาย</th>
				<th style="width: 12%; text-align:center; vertical-align:middle;">เลขที่ใบส่งสินค้า</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">ราคา</th>
				<th style="width: 8%; text-align:center; vertical-align:middle;">จำนวน</th>
				<th style="width: 10%; text-align:center; vertical-align:middle;">มูลค่า</th>
            </tr>
            {{#each this}}
				{{#if @last}}
					<tr style="font-size:10px;">
						<td colspan="8" align="right"><strong>รวม</strong></td>
						<td align="center" >{{ qty }}</td>
						<td align="right" >{{ amount }}</td>
					</tr>
				{{else}}
					<tr style="font-size:10px;">
						<td align="center">{{no}}</td>
						<td align="center">{{ date_add }}</td>
						<td>{{ product_code }}</td>
						<td align="center" >{{ reference }}</td>
						<td align="center" >{{ po_reference }}</td>
						<td align="center" >{{ supplier }}</td>
						<td align="center" >{{ invoice }}</td>
						<td align="center" >{{ cost }}</td>
						<td align="center" >{{ qty }}</td>
						<td align="right" >{{ amount }}</td>
					</tr>
				{{/if}}
            {{/each}}
        </table>
</script>
<script>
function all_po()
{
	$("#po").val(1);
	$("#po_2").removeClass("btn-primary");
	$("#po_1").addClass("btn-primary");
	$("#reference").attr("disabled", "disabled");
}

function select_po()
{
	$("#po").val(2);
	$("#po_1").removeClass("btn-primary");
	$("#po_2").addClass("btn-primary");
	$("#reference").removeAttr("disabled");	
	$("#reference").focus();
}

function all_sup()
{
	$("#sup").val(1);
	$("#sup_2").removeClass("btn-primary");
	$("#sup_1").addClass("btn-primary");
	$("#sup_name").attr("disabled", "disabled");	
}

function select_sup()
{
	$("#sup").val(2);
	$("#sup_1").removeClass("btn-primary");
	$("#sup_2").addClass("btn-primary");
	$("#sup_name").removeAttr("disabled");
}

function all_rank()
{
	$("#rank").val(1);
	$("#rank_2").removeClass("btn-primary");
	$("#rank_1").addClass("btn-primary");
	$("#from_date").attr("disabled", "disabled");
	$("#to_date").attr("disabled", "disabled");
}

function select_rank()
{
	$("#rank").val(2);
	$("#rank_1").removeClass("btn-primary");
	$("#rank_2").addClass("btn-primary");
	$("#from_date").removeAttr("disabled");
	$("#to_date").removeAttr("disabled");
	$("#from_date").focus();
}


$("#reference").autocomplete({
   minLength: 1,
	source: "controller/autoComplete.php?get_po",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr 	= data.split(" | ");
		$(this).val(arr[0]);
	}
});		

$("#sup_name").autocomplete({
	minLength : 1,
	source : "controller/autoComplete.php?get_supplier",
	autoFocus: true,
	close: function(event, ui)
	{
		var data = $(this).val();
		var arr 	= data.split(" | ");
		$("#sup_name").val(arr[1]);
		$("#id_sup").val(arr[2]);	
	}
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
  
  
function report()
{
	var po 		= $("#po").val();
	var ref		= $("#reference").val();
	var supplier 	= $("#sup").val();
	var sup_name = $("#sup_name").val();
	var id_sup	= $("#id_sup").val();
	var rank 		= $("#rank").val();
	var from		= $("#from_date").val();
	var to			= $("#to_date").val();
	
	if( po == 2 && $("#reference").val().length < 5 )
	{
		swal("เลขที่ใบสั่งซื้อไม่ถูกต้อง");
		return false;	
	}
	if( supplier == 2 && (sup_name == "" || id_sup == "") )
	{
		swal("กรุณาระบุชื่อผู้ขาย");
		return false;	
	}
	if( rank == 2 && (!isDate(from) || !isDate(to)) )
	{	
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"report/reportController/storeReportController.php?receive_product&report",
		type:"POST", cache: "false", data:{ "po" : po, "rank" : rank, "reference" : ref, "sup" : supplier, "id_sup" : id_sup, "from_date" : from, "to_date" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs !="fail" && rs != "")
			{
				var data 	= $.parseJSON(rs);
				var source	= $("#template").html();
				var output	= $("#rs");
				render(source, data, output);
				load_out();
			}else{
				$("#rs").html('');
				load_out();
				swal("Opps!!", "ไม่มีรายการตามเงื่อนไขที่เลือก", "warning");
				return false;	
			}
		}
	});
}

function export_report()
{
	var po 		= $("#po").val();
	var ref		= $("#reference").val();
	var supplier 	= $("#sup").val();
	var sup_name = $("#sup_name").val();
	var id_sup	= $("#id_sup").val();
	var rank 		= $("#rank").val();
	var from		= $("#from_date").val();
	var to			= $("#to_date").val();
	
	if( po == 2 && $("#reference").val().length < 5 )
	{
		swal("เลขที่ใบสั่งซื้อไม่ถูกต้อง");
		return false;	
	}
	if( supplier == 2 && (sup_name == "" || id_sup == "") )
	{
		swal("กรุณาระบุชื่อผู้ขาย");
		return false;	
	}
	if( rank == 2 && (!isDate(from) || !isDate(to)) )
	{	
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	var token = new Date().getTime();
	var page = "report/reportController/storeReportController.php?receive_product&export&po="+po+"&rank="+rank+"&reference="+ref+"&sup="+supplier+"&id_sup="+id_sup+"&from_date="+from+"&to_date="+to+"&token="+token;
	get_download(token);
	window.location.href = page;
}

function print_report()
{
	var po 			= $("#po").val();
	var ref			= $("#reference").val();
	var supplier 	= $("#sup").val();
	var sup_name 	= $("#sup_name").val();
	var id_sup		= $("#id_sup").val();
	var rank 			= $("#rank").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	
	if( po == 2 && $("#reference").val().length < 5 )
	{
		swal("เลขที่ใบสั่งซื้อไม่ถูกต้อง");
		return false;	
	}
	if( supplier == 2 && (sup_name == "" || id_sup == "") )
	{
		swal("กรุณาระบุชื่อผู้ขาย");
		return false;	
	}
	if( rank == 2 && (!isDate(from) || !isDate(to)) )
	{	
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	
	var page = "report/reportController/storeReportController.php?receive_product&print_report&po="+po+"&rank="+rank+"&reference="+ref+"&sup="+supplier+"&id_sup="+id_sup+"&from_date="+from+"&to_date="+to;
	window.open(page, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=100, left=200");	
}
</script>