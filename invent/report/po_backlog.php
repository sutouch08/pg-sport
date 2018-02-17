<?php 
	$page_name = "รายงานสินค้าค้างรับ แยกตามใบสั่งซื้อ";
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
	<div class="col-lg-2">
    	<label style="display:block;">ผู้ขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="all_sup" onClick="all_sup()" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="select_sup" onClick="select_sup()" value="2">เลือกเฉพาะ</button>
		</div>
	</div>
    <div class="col-lg-3">
		<label>&nbsp;</label>
		<input type="text" class="form-control input-sm" name="supplier" id="supplier" style="text-align:center;" placeholder="ค้นหาชื่อผู้ขาย" disabled />
	</div> 
    <div class="col-lg-2">
    	<label style="display:block;">ช่วงเวลา</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="tmie_all" onClick="all_time()" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="time_rank" onClick="select_time()" value="2">เลือกเฉพาะ</button>
		</div>
	</div>
	<div class="col-lg-2">
		<label>จากวันที่</label>
		<input type="text" class="form-control input-sm" name="from_date" id="from_date" style="text-align:center;" disabled="disabled" />
	</div> 
 	<div class="col-lg-2">
		<label>ถึงวันที่</label>
		<input type="text" class="form-control input-sm" name="to_date" id="to_date" style="text-align:center;" disabled="disabled" />
	</div>   
    <div class="col-lg-1">
    	<input type="hidden" name="id_sup" id="id_sup" />
        <input type="hidden" name="rank" id="rank" value="1" />
        <input type="hidden" name="sup_rank" id="sup_rank" value="1"  />
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>    
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
        	<div class="modal-header"><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button></div>
            <div class="modal-body" id="po_detail"></div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-info btn-sm" onclick="export_po()"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
                <button type="button" class="btn btn-success btn-sm" onclick="print_po()"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp; ปิด</button>
                <input type="hidden" id="id_po" value="" />
            </div>
        </div>
    </div>
</div>
<script id="template" type="text/x-handlebars-template">
 <table class="table table-striped table-bordered">
    <thead>
    <th style="width:5%; text-align:center;">ลำดับ</th>
    <th style="width:10%; text-align:center;">วันที่</th>
    <th style="width:15%; text-align:center;">เลขที่เอกสาร</th>
    <th style="width:30%; text-align:center;">ผู้ขาย</th>
    <th style="width:10%; text-align:center;">กำหนดรับ</th>
    <th style="width:8%; text-align:center;">จำนวน</th>
    <th style="width:8%; text-align:center;">รับแล้ว</th>
    <th style="width:8%; text-align:center;">ค้างรับ</th>
    <th style="text-align:center;"></th>
    </thead>
    {{#each this}}
    <tr style="font-size:12px;">
    	<td align="center">{{ no }}</td>
        <td align="center">{{ date_add }}</td>
        <td align="center">{{ reference }}</td>
        <td>{{ supplier }}</td>
        <td align="center">{{ due_date }}</td>
        <td align="center">{{ qty }}</td>
        <td align="center">{{ received }}</td>
        <td align="center">{{ backlog }}</td>
        <td align="center">{{{ btn }}}</td>    
    </tr>
    {{/each}}
    </table>
</script>


<script>
function view_po(id)
{
	$("#id_po").val(id);
	$.ajax({
		url:"report/reportController/poReportController.php?get_po_detail&id_po="+id,
		type: "GET", cache: "false", 
		success: function(rs)
		{
			var rs 	= $.trim(rs);
			$("#po_detail").html(rs);
			$("#detail_modal").modal("show");
		}
	});
		
	$("#detail_modal").modal("show");
}

function print_po()
{
	var id_po = $("#id_po").val();	
	if(!isNaN(parseInt(id_po)))
	{
		window.open("report/reportController/poReportController.php?print_po&id_po="+id_po, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=100, left=200");	
	}
}

function export_po()
{
	var id_po 		= $("#id_po").val();
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/poReportController.php?export_po_detail&id_po="+id_po+"&token="+token;
}
function all_sup()
{
	$("#all_sup").addClass("btn-primary");
	$("#select_sup").removeClass("btn-primary");
	$("#sup_rank").val(1);
	$("#supplier").attr("disabled", "disabled");
}
function select_sup()
{
	$("#all_sup").removeClass("btn-primary");
	$("#select_sup").addClass("btn-primary");
	$("#sup_rank").val(2);
	$("#supplier").removeAttr("disabled");
}

function all_time()
{
	$("#rank").val(1);
	$("#time_rank").removeClass("btn-primary");
	$("#tmie_all").addClass("btn-primary");
	$("#from_date").attr("disabled", "disabled");
	$("#to_date").attr("disabled", "disabled");
}

$("#supplier").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?get_supplier", 
	autoFocus: true,
	close: function(event, ui)
	{
		var rs	= $("#supplier").val();
		var arr = rs.split(" | ");
		if( arr[0] != "ไม่พบข้อมูล" )
		{
			$("#supplier").val(arr[1]);
			$("#id_sup").val(arr[2]);
		}
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
	var sup_rank 	= $("#sup_rank").val();
	var time_rank	= $("#rank").val();
	var id_sup		= $("#id_sup").val();
	var sup_name	= $("#supplier").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( sup_rank == 2 && (sup_name == "" || id_sup == ""))
	{
		swal("กรุณาระบุชื่อผู้ขาย หรือ เลือกทั้งหมด");
		return false;
	}
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	load_in();
	$.ajax({
		url:"report/reportController/poReportController.php?po_backlog&report",
		type: "POST", cache: "false", data:{"sup_rank" : sup_rank, "time_rank" : time_rank, "id_sup" : id_sup, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs =="nodata")
			{
				load_out();
				var data = "<div class='col-lg-4 col-lg-offset-4'><div class='alert alert-info' style='margin-top:50px;'><center><strong>ไม่มีใบสั่งซื้อค้างรับ ตามเงื่อนไขที่กำหนด</strong></center></div>";
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


function export_report()
{
	var sup_rank 	= $("#sup_rank").val();
	var time_rank	= $("#rank").val();
	var id_sup		= $("#id_sup").val();
	var sup_name	= $("#supplier").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( sup_rank == 2 && (sup_name == "" || id_sup == ""))
	{
		swal("กรุณาระบุชื่อผู้ขาย หรือ เลือกทั้งหมด");
		return false;
	}
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )
	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/poReportController.php?po_backlog&export&sup_rank="+sup_rank+"&id_sup="+id_sup+"&time_rank="+time_rank+"&from="+from+"&to="+to+"&token="+token;
}
</script>