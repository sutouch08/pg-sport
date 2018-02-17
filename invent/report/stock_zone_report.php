<?php 
	$page_name = "รายงานสินค้าคงเหลือ แยกตามโซน";
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
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="all_product" onClick="all_product()">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="select_product" onClick="select_product()">เลือกเป็นช่วง</button>
		</div>
	</div>
	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label style="visibility:hidden">เลือกจาก</label>
		<input type="text" class="form-control input-sm" name="p_from" id="p_from" placeholder="จาก : เลือกสินค้า" autocomplete="off" disabled />
	</div> 
 	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label style="visibility:hidden">ถึง</label>
		<input type="text" class="form-control input-sm" name="p_to" id="p_to" placeholder="ถึง : เลือกสินค้า" autocomplete="off" disabled />
	</div>   
 	<div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
    	<label>เลือกคลัง</label>
        <select class="form-control input-sm" id="wh" name="wh" onchange="update_id()">
        	<?php echo select_warehouse(); ?>
        </select>
	</div>
    <div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
    	<label style="display:block;">โซน</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="all_zone" onClick="all_zone()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="select_zone" onClick="select_zone()">เฉพาะ</button>
		</div>
	</div>
    <div class="col-lg-2" style="padding-left:2.5px; padding-right:2.5px;">
		<label style="visibility:hidden">เลือกจาก</label>
		<input type="text" class="form-control input-sm" name="zone_name" id="zone_name" placeholder="เลือกโซน" autocomplete="off" disabled />
	</div> 
    <input type="hidden" id="product_rank" value="1"  />
    <input type="hidden" id="zone_rank" value="1" />
</div>  
		  
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>     
 
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
        	<tr><th colspan="7" style="text-align:center;">========== รายงานสินค้าคงเหลือแยกตามโซน =============</th></tr>
        	<tr style="font-size:12px;">
            	<th style="width:5%; text-align:center; vertical-align:middle;">ลำดับ</th>
				<th style="width: 20%; vertical-align:middle;">โซน</th>
				<th style="width: 10%; vertical-align:middle;">บาร์โค้ด</th>
                <th style="width: 20%; vertical-align:middle;">รหัส</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">ทุน</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">คงเหลือ</th>
				<th style="width: 20%; text-align:right; vertical-align:middle;">มูลค่า</th>
            </tr>
            {{#each this}}
				{{#if nodata}}
				<tr>
					<td colspan="7" align="center"><h4>-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</h4></td>				
				</tr>
				{{else}}
				<tr style="font-size:10px;">
					<td align="center">{{no}}</td>
					<td>{{ zone }}</td>
					<td>{{ barcode }}</td>
					<td>{{ reference }}</td>
					<td align="right">{{ cost }}</td>
					<td align="right">{{ qty }}</td>
					<td align="right" >{{ amount }}</td>
				</tr>
				{{/if}}
            {{/each}}
        </table>
</script>
<script>
<?php $qs = dbQuery("SELECT product_code FROM tbl_product ORDER BY product_code ASC"); ?>
var data = [
<?php while($rs = dbFetchArray($qs) ) : ?>
<?php		echo "'".$rs['product_code']."',"; ?>
<?php endwhile; ?>
];

var id_wh = 0;

function update_id()
{
	id_wh = $("#wh").val();	
	console.log(id_wh);
	$("#zone_name").autocomplete({
	minLength : 1,
	source : "controller/autoComplete.php?get_zone&id_warehouse="+id_wh,
	autoFocus : true,
	close: function(event, ui)
	{
		if($(this).val() == "ไม่มีข้อมูล")
		{
			$(this).val("");
		}
	}
});
}
function all_product()
{
	$("#product_rank").val(1);
	$("#select_product").removeClass("btn-primary");
	$("#all_product").addClass("btn-primary");
	$("#p_from").attr("disabled", "disabled");
	$("#p_to").attr("disabled", "disabled");	
}

function select_product()
{
	$("#product_rank").val(2);
	$("#all_product").removeClass("btn-primary");
	$("#select_product").addClass("btn-primary");
	$("#p_from").removeAttr("disabled");
	$("#p_to").removeAttr("disabled");
	$("#p_from").focus();	
}

function all_zone()
{
	$("#zone_rank").val(1);
	$("#select_zone").removeClass("btn-primary");
	$("#all_zone").addClass("btn-primary");
	$("#zone_name").attr("disabled", "disabled");	
}

function select_zone()
{
	$("#zone_rank").val(2);
	$("#all_zone").removeClass("btn-primary");
	$("#select_zone").addClass("btn-primary");
	$("#zone_name").removeAttr("disabled");
	$("#zone_name").focus();	
}

$(document).ready(function(e) {
   $("#p_from").autocomplete({
	   minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var from = $(this).val();
			var to		= $("#p_to").val();
			if(to != "")
			{
				if(to < from)
				{ 
					$("#p_from").val(to);
					$("#p_to").val(from);
				}
			}
		}
	});		
	
	$("#p_to").autocomplete({
		minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var to 	= $(this).val();
			var from	=	$("#p_from").val();
			if(from !="")
			{
				if(to < from)
				{
					$("#p_to").val(from);
					$("#p_from").val(to);	
				}
			}
		}
	});			
});

$("#zone_name").autocomplete({
	minLength : 1,
	source : "controller/autoComplete.php?get_zone&id_warehouse="+id_wh,
	autoFocus : true,
	close: function(event, ui)
	{
		if($(this).val() == "ไม่มีข้อมูล")
		{
			$(this).val("");
		}
	}
});

function report()
{
	var p_rank	 	= $("#product_rank").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var wh			= $("#wh").val();
	var zone_rank	= $("#zone_rank").val();
	var zone_name	= $.trim($("#zone_name").val());
	if(p_rank == 2 && (from =="" || to =="") )
	{
		swal("กรุณาเลือกช่วงสินค้า");
		return false;
	}
	if(zone_rank == 2 && zone_name == "")
	{
		swal("กรุณาระบุโซน");
		return false;	
	}
	load_in();
	$.ajax({
		url:"report/reportController/stockReportController.php?stock_by_zone&report",
		type:"POST", cache: "false", data:{ "product_rank" : p_rank, "from" : from, "to" : to, "wh" : wh, "zone_rank" : zone_rank, "zone_name" : zone_name },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs !="nodata" || rs != "")
			{
				var data 		= $.parseJSON(rs);
				var source	= $("#template").html();
				var output	= $("#rs");
				render(source, data, output);
			}else{
				swal("Error!!", "เกิดความผิดพลาดระห่วงส่งข้อมูล", "error");
				return false;	
			}
		}
	});
}

function export_report()
{
	var p_rank	 	= $("#product_rank").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var wh			= $("#wh").val();
	var zone_rank	= $("#zone_rank").val();
	var zone_name	= $.trim($("#zone_name").val());
	if(p_rank == 2 && (from =="" || to =="") )
	{
		swal("กรุณาเลือกช่วงสินค้า");
		return false;
	}
	if(zone_rank == 2 && zone_name == "")
	{
		swal("กรุณาระบุโซน");
		return false;	
	}
	var token		= new Date().getTime();
	get_download(token);
	window.location.href = "report/reportController/stockReportController.php?stock_by_zone&export&product_rank="+p_rank+"&from="+from+"&to="+to+"&wh="+wh+"&zone_rank="+zone_rank+"&zone_name="+zone_name+"&token="+token;
}
function export_csv()
{
	var p_rank	 	= $("#product_rank").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var wh			= $("#wh").val();
	var zone_rank	= $("#zone_rank").val();
	var zone_name	= $.trim($("#zone_name").val());
	if(p_rank == 2 && (from =="" || to =="") )
	{
		swal("กรุณาเลือกช่วงสินค้า");
		return false;
	}
	if(zone_rank == 2 && zone_name == "")
	{
		swal("กรุณาระบุโซน");
		return false;	
	}
	var token		= new Date().getTime();
	get_download(token);
	csv_link = "report/reportController/stockReportController.php?stock_by_zone&export_csv&product_rank="+p_rank+"&from="+from+"&to="+to+"&wh="+wh+"&zone_rank="+zone_rank+"&zone_name="+zone_name+"&token="+token;
	window.open(csv_link, "_blank");
}
</script>