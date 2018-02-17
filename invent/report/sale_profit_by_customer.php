<?php 
	$page_name = "รายงานยอดขาย แยกตามรายการสินค้า แสดงกำไรขั้นต้น";
	$id_tab = 51;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	accessDeny($view);
?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height::30px;">
	<div class="col-sm-8" style="margin-top: 10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" id="btn_report" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" id="btn_export" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<form id="export_form" method="post">
<div class='row'>
	<div class="col-lg-2">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" name="customer" id="customer" style="text-align:center;" placeholder="เลือกลูกค้า"  />
        <input type="hidden" id="id_customer" name="id_customer" value="" />
    </div>
	<div class="col-lg-2">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%" id="product_1" onClick="all_product()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%" id="product_2" onClick="select_product()" >เป็นช่วง</button>
		</div>
	</div>
 	<div class="col-lg-2">
		<label>จาก</label>
        <input type="text" class="form-control input-sm" id="p_from" name="p_from" placeholder="กำหนดสินค้า" style="text-align:center;" disabled  />
	</div>
    <div class="col-lg-2">
    	<label>ถึง</label>
        <input type="text" class="form-control input-sm" id="p_to" name="p_to" placeholder="กำหนดสินค้า" style="text-align:center;" disabled  />
    </div>
    <div class="col-lg-2">
    	<label>วันที่เริ่มต้น</label>
        <input type="text" class="form-control input-sm" id="from_date" name="from_date" placeholder="วันที่เริ่มต้น" style="text-align:center;"  />
    </div>
    <div class="col-lg-2">
    	<label>วันที่สิ้นสุด</label>
        <input type="text" class="form-control input-sm" id="to_date" name="to_date" placeholder="วันที่สิ้นสุด" style="text-align:center;"  />
    </div>
    <input type="hidden" name="product" id="product" value="1" />
    <input type="hidden" name="title" id="title" />
</div>    
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="result">  </div>
</div>
</div><!------- container ------>  
<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead>
    	<tr><th colspan="8" style="text-align:center;">รายงานยอดขาย แยกตามรายการสินค้า แสดงกำไรขั้นต้น : <span id="name"></span></th></tr>
        <tr>
        	<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width:15%; text-align:left;">รหัส</th>
            <th style="width:20%; text-align:left;">รายละเอียด</th>
			<th style="width:10%; text-align:right;">ราคา</th>
            <th style="width:10%; text-align:right;">จำนวนขาย</th>
            <th style="width:10%; text-align:right;">ต้นทุนขาย</th>
            <th style="width:10%; text-align:right;">มูลค่าขาย</th>
            <th style="width:10%; text-align:right;">กำไรขั้นต้น</th>
			<th style="width:10%; text-align:right;">% (กำไร)</th>
			
        </tr>
    </thead>
    <tbody>
    {{#each this}}
    	<tr style="font-size:12px;">
        	<td align="center">{{ no }}</td>
            <td>{{ product_code }}</td>
            <td><input type="text" style="width:100%; border:0px; padding-left:0px; padding-right:0px; background-color: transparent;" value="{{ product_name }}" /></td>
			<td align="right">{{ price }}</td>
            <td align="right">{{ qty }}</td>
            <td align="right" style="color: #33F;">{{ cost }}</td>
            <td align="right" style="color: #F0F;">{{ amount }}</td>
            <td align="right" >{{{ profit }}}</td>
			<td align="right">{{{ percent }}}</td>
        </tr>
    {{/each}}
    </tbody>
</table>
</script>
<script>

$("#from_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(selectedDate){ 
		$("#to_date").datepicker("option", "minDate", selectedDate);
	}
});

$("#to_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(selectedDate){
		$("#from_date").datepicker("option", "maxDate", selectedDate);
	}
});

$("#p_from").autocomplete({
	source: "controller/autoComplete.php?get_product_code",
	autoFocus: true,
	close: function()
	{
		var data = $.trim($(this).val());
		var arr	= data.split(" : ");
		$(this).val( arr[0] );
		reorder();
	}
});

$("#p_to").autocomplete({
	source: "controller/autoComplete.php?get_product_code",
	autoFocus: true,
	close: function()
	{
		var data = $.trim($(this).val());
		var arr	= data.split(" : ");
		$(this).val( arr[0] );
		reorder();
	}
});

$("#customer").autocomplete({
	source: "controller/autoComplete.php?get_customer_id",
	autoFocus: true,
	close: function()
	{
		var data = $.trim($(this).val());
		var arr	= data.split(" : ");
		var name = arr[1];
		$(this).val(name);
		$("#title").val(name);
		$("#id_customer").val(arr[0]);
	}
});

function reorder()
{
	var p_from 	= $("#p_from").val();
	var p_to		= $("#p_to").val()	
	if( p_from != "" && p_to != "")
	{
		if( p_from > p_to )
		{
			$("#p_from").val(p_to);
			$("#p_to").val(p_from);
		}
	}
}
function all_product()
{
	$("#product").val(1);
	$("#product_2").removeClass("btn-primary");
	$("#p_from").attr("disabled", "disabled");
	$("#p_to").attr("disabled", "disabled");
	$("#product_1").addClass("btn-primary");	
}

function select_product()
{
	$("#product").val(2);
	$("#product_1").removeClass("btn-primary");
	$("#p_from").removeAttr("disabled");
	$("#p_to").removeAttr("disabled");
	$("#product_2").addClass("btn-primary");
}
function report()
{
	var id_cus		= $("#id_customer").val();
	var p_rank		= $("#product").val();
	var p_from		= $("#p_from").val();
	var p_to			= $("#p_to").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	var title			= $("#title").val();
	
	if( id_cus == "")
	{
		swal("กรุณาระบุลูกค้า");
		return false;	
	}
	if( !isDate(from) || !isDate(to) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?sale_profit_customer&report",
		type:"POST", cache: "false", data: { "id_customer" : id_cus, "p_rank" : p_rank, "p_from" : p_from, "p_to" : p_to, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs !="fail" || rs != "")
			{
				var data 	= $.parseJSON(rs);
				var source	= $("#template").html();
				var output	= $("#result");
				render(source, data, output);
				$("#name").text(title);
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
	var id_cus		= $("#id_customer").val();
	var cus_name	= $("#customer").val();
	var p_rank		= $("#product").val();
	var p_from		= $("#p_from").val();
	var p_to			= $("#p_to").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( !isDate(from) || !isDate(to) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	var token		= new Date().getTime();
	var url = "report/reportController/saleReportController.php?sale_profit_customer&export&id_customer="+id_cus+"&token="+token;
	get_download(token);
	$("#export_form").attr("action", url);
	$("#export_form").submit();
}
</script>