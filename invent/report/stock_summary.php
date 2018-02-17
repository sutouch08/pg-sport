<?php 
	$page_name = "รายงานสรุปสินค้าคงเหลือ แยกตามรุ่นสินค้า";
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
<div class='row'>
	<div class="col-lg-3">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="product_1" onClick="select_product($(this), 2)" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="product_2" onClick="select_product($(this), 1)" value="2">เลือกเป็นช่วง</button>
		</div>
	</div>
	<div class="col-lg-3">
		<label>เลือกจาก</label>
		<input type="text" class="form-control input-sm" name="p_from" id="p_from" disabled />
	</div> 
 	<div class="col-lg-3">
		<label>ถึง</label>
		<input type="text" class="form-control input-sm" name="p_to" id="p_to" disabled />
	</div>   
 	<div class="col-lg-3">
    	<label>เลือกคลัง</label>
        <select class="form-control input-sm" id="wh" name="wh">
        	<?php echo select_warehouse(); ?>
        </select>
		<input type="hidden" name="product" id="product" value="1" />
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
        	<tr><th colspan="7" style="text-align:center;">========== รายงานสรุปสินค้าคงเหลือแยกตามรุ่นสินค้า =============</th></tr>
        	<tr style="font-size:12px;">
            	<th style="width:10%; text-align:center; vertical-align:middle;">ลำดับ</th>
                <th style="width: 20%; vertical-align:middle;">รหัส</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">ทุน</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">ราคา</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">คงเหลือ</th>
				<th style="width: 20%; text-align:right; vertical-align:middle;">มูลค่าทุน</th>
				<th style="width: 20%; text-align:right; vertical-align:middle;">มูลค่าราคา</th>
            </tr>
            {{#each this}}
            <tr style="font-size:10px;">
            	<td align="center">{{no}}</td>
				<td>{{ code }}</td>
            	<td align="right" style="color:blue;">{{ cost }}</td>
				<td align="right" style="color:#060;">{{ price }}</td>
				<td align="right" style="color:red;">{{ qty }}</td>
                <td align="right" style="color:blue;">{{ cost_amount }}</td>
				<td align="right" style="color:#060;">{{ price_amount }}</td>
            </tr>
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

function select_product(el, id)
{
	var i = el.val();
	$("#product").val(i);
	$("#product_"+id).removeClass("btn-primary");
	el.addClass("btn-primary");
	if(i == 2 )
	{
		$("#p_from").removeAttr("disabled");
		$("#p_to").removeAttr("disabled");
	}else if(i == 1 ){
		$("#p_from").attr("disabled", "disabled");
		$("#p_to").attr("disabled", "disabled");
	}
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
function report()
{
	var product 	= $("#product").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var wh			= $("#wh").val();
	if(product == 2)
	{
		if( from == "" || to == "")
		{ 
			swal("เลือกช่วงสินค้า");
			return false;
		}
	}
	load_in();
	$.ajax({
		url:"report/reportController/stockReportController.php?stock_summary&report",
		type:"POST", cache: "false", data:{ "rank" : product, "from" : from, "to" : to, "wh" : wh },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs !="fail" || rs != "")
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
	var product 	= $("#product").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var wh			= $("#wh").val();
	var token		= new Date().getTime();
	if(product == 2)
	{
		if( from == "" || to == "")
		{ 
			swal("เลือกช่วงสินค้า");
			return false;
		}
	}
	get_download(token);
	window.location.href = "report/reportController/stockReportController.php?stock_summary&export&rank="+product+"&from="+from+"&to="+to+"&wh="+wh+"&token="+token;
}
</script>