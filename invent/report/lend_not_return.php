<?php 
	$page_name = "รายงานใบยืมสินค้า ยังไม่คืน แสดงรายการสินค้า /เลขที่เอกสาร /ผู้ยืม";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<form name='report_form' id='report_form' action='' method='post'>
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-primary btn-sm" onClick="get_report()"><i class="fa fa-file-text"></i>&nbsp;รายงาน</button>
            <button type="button" class="btn btn-success btn-sm" onClick="do_export()"><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button>
        </p>
     </div>
</div>
<hr />
<!-- End page place holder -->
<div class="row">
	<div class="col-lg-2" style="padding-right:5px; padding-left:15px;">
    	<label style="display:block;">ผู้ยืม</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" id="btn_lender_all" onClick="lender_all()" style="width:50%;">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" id="btn_lender_once" onClick="lender_once()" style="width:50%;">เฉพาะ</button>
        </div>
    </div>
    <div class="col-lg-2" style="padding-right:5px; padding-left:5px;">
    	<label style="visibility:hidden">lender</label>
        <input type="text" class="form-control input-sm" name="lender" id="lender" placeholder="ค้นหาชื่อผู้ยิม" disabled />
    </div>
    <div class="col-lg-3" style="padding-right:5px; padding-left:5px;">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" id="btn_item_all" onClick="item_all()" style="width:33%;">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" id="btn_product" onClick="item_product()" style="width:33%;">เฉพาะรุ่น</button>
            <button type="button" class="btn btn-sm" id="btn_item_once" onClick="item_once()" style="width:34%;">เฉพาะรายการ</button>
        </div>
    </div>
    <div class="col-lg-2" style="padding-right:5px; padding-left:5px;">
    	<label style="visibility:hidden">items</label>
        <input type="text" class="form-control input-sm" name="item" id="item" placeholder="ค้นหาสินค้า" disabled />
        <input type="text" class="form-control input-sm" name="product" id="product" placeholder="ค้นหาสินค้า" style="display:none;" disabled />
    </div>
    <div class="col-lg-1" style="padding-right:5px; padding-left:5px; width:12.5%">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="เริ่มต้น" />
    </div>
    <div class="col-lg-1" style="padding-right:15px; padding-left:5px; width:12.5%">
    	<label style="visibility:hidden">วันที่</label>
        <input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="สิ้นสุด" />
    </div>
    <input type="hidden" name="lender_range" id="lender_range" value="0" />
    <input type="hidden" name="item_range" id="item_range" value="0" />
    <input type="hidden" name="id_employee" id="id_employee" value="" />
    <input type="hidden" name="id_item" id="id_item" value="" />
    <input type="hidden" name="id_product" id="id_product" value="" />
</div>
</form>
<hr />
<div class="row"><div class="col-lg-12" id="rs"></div></div>

<script id="report_template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead style="font-size:12px;">
    	<th style="width:5%; text-align:center;">ลำดับ</th>
        <th style="width:10%;">วันที่</th>
        <th style="width:15%;">เลขที่เอกสาร</th>
        <th style="width:20%;">พนักงาน</th>
        <th style="width:35%;">สินค้า</th>
        <th style="width:5%; text-align:center;">จำนวน</th>
        <th style="width:10%; text-align:right;">มูลค่า</th>
    </thead>
    {{#each this}}
    	{{#if nocontent}}
        	<tr>
            	<td align="center" colspan="7"><h4>-----  ไม่พบข้อมูล  -----</h4></td>
            </tr>
        {{else}}
        	{{#if last}}
            	<tr>
                	<td colspan="5" align="right"><strong>รวม</strong></td>
                    <td align="center">{{ total_qty }}</td>
                    <td align="right">{{ total_amount }}</td>
                </tr>
            {{else}}
            	<tr style="font-size:12px;">
                	<td align="center">{{ no }}</td>
                    <td>{{ date }}</td>
                    <td>{{ reference }}</td>
                    <td>{{ employee }}</td>
                    <td>{{ item }}</td>
                    <td align="center">{{ qty }}</td>
                    <td align="right">{{ amount }}</td>
                </tr>
            {{/if}}
        {{/if}}    
    {{/each}}
</table>
</script>
</div><!--- container -->
<script>
function get_report()
{
	var lender_range	= $("#lender_range").val();
	var item_range		= $("#item_range").val();
	var lender			= $("#lender").val();
	var item				= $("#item").val();
	var product			= $("#product").val();
	var id_emp			= $("#id_employee").val();
	var id_item			= 	$("#id_item").val();
	var id_product		= $("#id_product").val();
	var from_date		= $("#from_date").val();
	var to_date			= $("#to_date").val();
	
	if( lender_range == 1 && (id_emp =='' || lender == '')){ swal("กรุณาระบุชื่อพนักงาน"); return false; }
	if( item_range == 1 && (id_item =='' || item == "")){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( item_range == 2 && (id_product == '' || product == '')){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( !isDate(from_date) || !isDate(to_date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	
	load_in();
	$.ajax({
		url:"report/reportController/lendReportController.php?lend_not_return&report",
		type:"POST", cache:"false", data:{ "emp_range" : lender_range, "item_range" : item_range, "id_employee" : id_emp, "id_product_attribute" : id_item, "id_product" : id_product, "from_date" : from_date, "to_date" : to_date },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			var source 	= $("#report_template").html();
			var data		= $.parseJSON(rs);
			var output	= $("#rs");
			render(source, data, output);
		}
	});
}

function do_export()
{
	var lender_range	= $("#lender_range").val();
	var item_range		= $("#item_range").val();
	var lender			= $("#lender").val();
	var item				= $("#item").val();
	var product			= $("#product").val();
	var id_emp			= $("#id_employee").val();
	var id_item			= 	$("#id_item").val();
	var id_product		= $("#id_product").val();
	var from_date		= $("#from_date").val();
	var to_date			= $("#to_date").val();
	var token			= new Date().getTime();
	if( lender_range == 1 && (id_emp =='' || lender == '')){ swal("กรุณาระบุชื่อพนักงาน"); return false; }
	if( item_range == 1 && (id_item =='' || item == "")){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( item_range == 2 && (id_product == '' || product == '')){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( !isDate(from_date) || !isDate(to_date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }	
	
	var target = "report/reportController/lendReportController.php?lend_not_return&export&token="+token;
	$("#report_form").attr("action", target);
	get_download(token);
	$("#report_form").submit();
}

function lender_all()
{
	$("#lender_range").val(0);
	$("#lender").val("");
	$("#id_employee").val('');
	$("#lender").attr("disabled", "disabled");
	$("#btn_lender_once").removeClass("btn-primary");
	$("#btn_lender_all").addClass("btn-primary");	
}
function lender_once()
{
	$("#lender_range").val(1);
	$("#lender").removeAttr("disabled");
	$("#btn_lender_all").removeClass("btn-primary");
	$("#btn_lender_once").addClass("btn-primary");	
	$("#lender").focus();
}
function item_all()
{
	$("#item_range").val(0);
	$("#item").val('');
	$("#id_item").val('');
	$("#product").val('');
	$("#id_product").val('');
	$("#item").attr("disabled", "disabled");
	$("#product").attr("disabled", "disabled");
	$("#btn_item_once").removeClass("btn-primary");
	$("#btn_product").removeClass("btn-primary");
	$("#btn_item_all").addClass("btn-primary");	
}

function item_product()
{
	$("#item_range").val(2);
	$("#id_tem").val('');
	$("#item").val('');
	$("#item").attr("disabled", "disabled");
	$("#item").css("display", "none");
	$("#product").removeAttr("disabled");
	$("#product").css("display","");
	$("#btn_item_all").removeClass("btn-primary");
	$("#btn_item_once").removeClass("btn-primary");
	$("#btn_product").addClass("btn-primary");
	$("#product").focus();
}

function item_once()
{
	$("#item_range").val(1);
	$("#id_product").val('');
	$("#product").val('');
	$("#product").attr("disabled", "disabled");
	$("#product").css("display", "none");
	$("#item").removeAttr("disabled");
	$("#item").css("display", "");
	$("#btn_item_all").removeClass("btn-primary");
	$("#btn_product").removeClass("btn-primary");
	$("#btn_item_once").addClass("btn-primary");
	$("#item").focus();	
}

$("#lender").autocomplete({
	source: "controller/autoComplete.php?get_employee",
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var ar = rs.split(" | ");
		var id = ar[1];
		$(this).val(ar[0]);
		$("#id_employee").val(id);
	}
});

$("#item").autocomplete({
	source: "controller/autoComplete.php?get_product_attribute",
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var ar = rs.split(" | ");
		var id = ar[1];
		$(this).val(ar[0]);
		$("#id_item").val(id);
	}
});

$("#product").autocomplete({
	source: "controller/autoComplete.php?get_product_id",
	sutoFocus: true,
	close: function(){
		var rs = $(this).val();
		var ar = rs.split(" : ");
		var id = ar[2];
		var code = ar[0];
		$("#id_product").val(id);
		$(this).val(code);
	}
});

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
</script>