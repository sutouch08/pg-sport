<?php 
	$page_menu = "invent_sale_report_zone";
	$page_name = "รายงานยอดขาย แยกตามพนักงานขาย";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-sm btn-success" onClick="get_report()"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-sm btn-info" onClick="do_export()"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
        </p>
     </div>
</div>
<hr/>
<!-- End page place holder -->
 
<div class="row">
	<div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="เริ่มต้น" style="text-align:center;" />
    </div>
    <div class="col-lg-2">
    	<label style="visibility:hidden;">วันที่</label>
        <input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="สิ้นสุด" style="text-align:center;" />
    </div>
    <div class="col-lg-3">
    	<label style="display:block;">ช่องทางขาย</label>
    	<div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" style="width:33%;" id="btn_all" onClick="sale_all()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" id="btn_sale" style="width:33%;" onClick="sale()">ขายปกติ</button>
            <button type="button" class="btn btn-sm" id="btn_cons" style="width:33%;" onClick="consign()">ฝากขาย</button>
        </div>
    </div>
    <div class="col-lg-2">
    	<label style="display:block;">พนักงานขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" style="width:50%;" id="btn_em_all" onClick="em_all()">ทั้งหมด</button>            
            <button type="button" class="btn btn-sm" style="width:50%;" id="btn_em_range" onClick="em_range()">บางคน</button>
        </div>
    </div>
    <div class="col-lg-2">
    	<label style="display:block; visibility:hidden;">เลือกรายการ</label>
        <button type="button" class="btn btn-sm btn-default" id="btn_select_range" onClick="show_list()" style="display:none;"><i class="fa fa-check-square-o"></i> เลือกพนักงานขาย</button>
    </div>
</div>
<input type="hidden" id="role" value="0"><!---  0 = ทั้งหมด  1 = ขายอย่างเดียว  5 = ฝากขาย  -->
<input type="hidden" id="emp_range" value="0"><!---  0 = ทั้งหมด  1 = เลือกบางรายการ  -->

<div class='modal fade' id='range_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:800px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title'>เลือกพนังานขาย</h4>
				 </div>
				 <div class='modal-body' >
                 <form id="select_form" method="post">
                 	<table class="table table-bordered">
                    <?php $qs = dbQuery("SELECT * FROM tbl_sale"); ?>
                    <?php $i = 1; ?>
                    <?php $row = dbNumRows($qs); ?>
                    <?php while( $rs = dbFetchArray($qs)) : ?>
						<?php if( $i == 1 ) : ?><tr><?php endif; ?>
                        <td style="width:15%; text-align:center"><input type="checkbox" class="group_check" id="<?php echo $rs['id_sale']; ?>" name="sale[<?php echo $rs['id_sale']; ?>]" value="<?php echo $rs['id_employee']; ?>" /></td>
                        <td style="width:35%"><label for="<?php echo $rs['id_sale']; ?>"><?php echo employee_name($rs['id_employee']); ?></label></td>                        
                        <?php if( $i == 2 ) : ?></tr><?php endif; ?>
                        <?php $i++; if( $i > 2 ){ $i = 1; } ?>
                   <?php endwhile; ?>
                   <?php if( $row%2 > 0 ): ?>
                   		<td style="width:15%; text-align:center"></td>
                        <td style="width:35%"></td>
                        </tr>
                    <?php endif; ?>
                    </table>
                 </form>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onClick="check_input()">เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
    
    <div class='modal fade' id='detail_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:800px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title'>รายละเอียดยอดขาย</h4>
				 </div>
				 <div class='modal-body' id="detail_body">
                 
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				 </div>
			</div>
		</div>
	</div>
    
<hr/>
<div class="row"><div class="col-sm-12" id="rs"></div></div>

<script id="result_template" type="text/x-handlebars-template">
    <table class="table table-striped table-bordered">
    {{#each this}}
        {{#if @first}}
        <thead>
        	<tr><th colspan="6" style="text-align:center;">รายงานยอดขาย แยกตามพนักงานขาย  วันที่ {{ from }}  ถึงวันที่  {{ to }}</th></tr>
            <tr>
            	<th style="text-align:center; width:5%;">ลำดับ</th>
                <th style="text-align:center;">พนักงานขาย</th>
                <th style="width:15%; text-align:center;">ยอดขาย</th>
                <th style="width:15%; text-align:center;">ลดหนี้</th>
                <th style="width:15%; text-align:center">มูลค่าขาย</th>
                <th style="width:15%; text-align:center;">รายละเอียด</th>
            </tr>
        </thead>
        {{else}}
        	{{#if last}}
            	<tr>
                    <td colspan="2" align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{ total_amount }}</strong></td>
                    <td align="right"><strong>{{ total_return }}</strong></td>
                    <td align="right"><strong>{{ total_sale }}</strong></td>
                    <td align="center"></td>
                </tr>
            {{else}}
            	<tr style="font-size:12px;">
                	<td align="center">{{ no }}</td>
                    <td>{{ emp }}</td>
                    <td align="right">{{ amount }}</td>
                    <td align="right">{{ return_amount }}</td>
                    <td align="right">{{ sale_amount }}</td>
                    <td align="center"><a href="javascript:void(0)" onClick="get_sale_detail({{ id_sale }}, '{{ from }}', '{{ to }}', {{role}})"> รายละเอียด</a></td>
                </tr>
            {{/if}}        
        {{/if}}
    {{/each}}
    </table>
</script>
<script id="detail_template" type="text/x-handlebars-template" >
    <table class="table table-bordered table-striped">
    {{#each this}}
    {{#if @first}}
        <tr><td colspan="2" align="center">{{ emp }}</td><td colspan="2" align="center">{{ range }}</td></tr>
        <tr><td align="center">วันที่</td><td align="center">ยอดขาย</td><td align="center">ยอดคืน</td><td align="center">มูลค่าขาย</td></tr>    
    {{else}}
    	{{#if total}}
        	<tr><td align="right"><strong>รวม</strong></td><td align="right"><strong>{{ total_amount }}</strong></td><td align="right"><strong>{{ total_return }}</strong></td><td align="right"><strong>{{ total_sale }}</strong></td></tr>
        {{else}}
    	<tr><td align="center">{{ date }}</td><td align="right">{{ amount }}</td><td align="right">{{ return_amount }}</td><td align="right">{{ sale_amount }}</td></tr>
        {{/if}}
    {{/if}}
    {{/each}}
    </table>
</script>	

</div>  <!---   container --->   
<script>
function get_sale_detail(id, from, to, role)
{
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?get_sale_detail_by_employee",
		type:"POST", cache: "false", data:{ "id_sale" : id, "from_date" : from, "to_date" : to, "role" : role },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var data = $.parseJSON(rs);
			var source = $("#detail_template").html();
			var output = $("#detail_body");
			render(source, data, output);
			$("#detail_modal").modal("show");
		}
	});
}
function get_report()
{
	var from = $("#from_date").val();
	var to		= $("#to_date").val();
	var role	= $("#role").val();
	var emp	= $("#emp_range").val();
	var se	= check_items();
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( role != 0 && role != 1 && role != 5 ){ swal("กรุณาเลือกช่องทางการขายใหม่"); return false; }
	if( emp != 0 && emp != 1 ){ swal("กรุณาเลือกพนักงานขายใหม่"); return false; }
	if( emp == 1 && se == 0 ){ swal("คุณยังไม่ได้เลือกพนักงานขาย"); return false; }
	if( emp == 0){ var datax = { "all" : "all"}; }
	if( emp == 1){ var datax = $("#select_form").serialize(); }
	
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?sale_report_employee&report&from_date="+from+"&to_date="+to+"&role="+role+"&range="+emp,
		type:"POST", cache:"false", data: datax,
		success: function(rs)
		{
			load_out();
			var source	= $("#result_template").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#rs");
			render(source, data, output);
		}
	});		
}

function do_export()
{
	var from = $("#from_date").val();
	var to		= $("#to_date").val();
	var role	= $("#role").val();
	var emp	= $("#emp_range").val();
	var se	= check_items();
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( role != 0 && role != 1 && role != 5 ){ swal("กรุณาเลือกช่องทางการขายใหม่"); return false; }
	if( emp != 0 && emp != 1 ){ swal("กรุณาเลือกพนักงานขายใหม่"); return false; }
	if( emp == 1 && se == 0 ){ swal("คุณยังไม่ได้เลือกพนักงานขาย"); return false; }
	var token = new Date().getTime();
	var target = "report/reportController/saleReportController.php?sale_report_employee&export&from_date="+from+"&to_date="+to+"&role="+role+"&range="+emp+"&token="+token;
	get_download(token);
	$("#select_form").attr("action", target);
	$("#select_form").submit();
}

function check_input()
{
	var i = 0;
	$(".group_check").each(function(index, element) {
		if($(this).is(":checked")){ i++; }
    });
	if(i == 0){ swal("คุณไม่ได้เลือกรายการใดๆเลย"); return false; }
	$("#range_modal").modal("hide");
	return i;
}

function check_items()
{
	var i = 0;
	$(".group_check").each(function(index, element) {
		if($(this).is(":checked")){ i++; }
    });
	return i;
}

function show_list()
{
	$("#range_modal").modal("show");	
}
function em_all()
{
	$("#emp_range").val(0);
	$("#btn_em_range").removeClass("btn-primary");
	$("#btn_select_range").css("display", "none");
	$("#btn_em_all").addClass("btn-primary");
	//console.log($("#emp_range").val());	
}

function em_range()
{
	$("#emp_range").val(1);
	$("#btn_em_all").removeClass("btn-primary");
	$("#btn_select_range").css("display", "");
	$("#btn_em_range").addClass("btn-primary");
	//console.log($("#emp_range").val());	
}

function sale_all()
{
	$("#role").val(0);
	$("#btn_sale").removeClass("btn-primary");
	$("#btn_cons").removeClass("btn-primary");
	$("#btn_all").addClass("btn-primary");
}

function sale()
{
	$("#role").val(1);
	$("#btn_all").removeClass("btn-primary");
	$("#btn_cons").removeClass("btn-primary");
	$("#btn_sale").addClass("btn-primary");	
}

function consign()
{
	$("#role").val(5);
	$("#btn_all").removeClass("btn-primary");
	$("#btn_sale").removeClass("btn-primary");
	$("#btn_cons").addClass("btn-primary");	
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
$(document).ready(function(e) {
    $("#gogo").click(function(){
		var action = $("#export").val();
		$("#report_form").attr("action", action );
		$(this).attr("type", "submit");
	});
});


</script>