<?php 
	$page_menu = "invent_sale_report_zone";
	$page_name = "รายงานยอดขาย แยกตามพื้นที่การขาย";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-lg-8 col-sm-12 col-xs-12" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-4 col-sm-12 col-xs-12">
       <p class="pull-right" style="margin-bottom:0px;">
       		<button type="button" class="btn btn-success btn-sm" onclick="get_report()"><i class="fa fa-list"></i>  รายงาน</button>
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->

<div class="row">
	<div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="เริ่มต้น" />
    </div>
    <div class="col-sm-2">
    	<label style="visibility:hidden">วันที่</label>
        <input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="สิ้นสุด" />
    </div>
	<div class="col-sm-3">
    	<label style="display:block;">พื้นที่การขาย</label>
    	<div class="btn-group" style="width:100%;">
        	<button class="btn btn-sm btn-primary" id="btn_all" onClick="select_all()" style="width:33%;">ทั้งหมด</button>
            <button class="btn btn-sm" id="btn_once" onClick="select_once()" style="width:33%;">เฉพาะ</button>
            <button class="btn btn-sm" id="btn_range" onClick="select_range()" style="width:33%;">บางรายการ</button>
        </div>
    </div>
    <div class="col-sm-3">
    	<label style="display:block; visibility:hidden">area</label>
    	<button type="button" class="btn btn-sm btn-default" onClick="show_items()" id="btn_show" style="display:none;"><i class="fa fa-check-square"></i>&nbsp; เลือกรายการ</button>
        <select class="form-control input-sm" name="select_group" id="select_group" style="display:none;" >
        	<option value="0">เลือกพื้นที่การขาย</option>
            <?php selectCustomerGroup(); ?>
        </select>
    </div>
    <input type="hidden" name="range" id="range" value="0" />  <!--- 0 = all , 1 = once , 2 = some --->
</div>

<hr style="margin-top: 10px;"/>
<div class="row">
	<div class="col-sm-12" id="rs">
    
    </div>
</div>



</div>
<div class='modal fade' id='range_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:800px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title'>เลือกพื้นที่</h4>
				 </div>
				 <div class='modal-body' >
                 <form id="select_form">
                 	<table class="table table-bordered">
                    <?php $qs = dbQuery("SELECT * FROM tbl_group"); ?>
                    <?php $i = 1; ?>
                    <?php $row = dbNumRows($qs); ?>
                    <?php while( $rs = dbFetchArray($qs)) : ?>
						<?php if( $i == 1 ) : ?><tr><?php endif; ?>
                        <td style="width:15%; text-align:center"><input type="checkbox" class="group_check" id="<?php echo $rs['id_group']; ?>" name="group[<?php echo $rs['id_group']; ?>]" value="<?php echo $rs['id_group']; ?>" /></td>
                        <td style="width:35%"><label for="<?php echo $rs['id_group']; ?>"><?php echo $rs['group_name']; ?></label></td>                        
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
					<h4 class='modal-title'>รายละเอียดยอดขายแยกตามพื้นที่</h4>
				 </div>
				 <div class='modal-body' id="detail_body">
                 
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				 </div>
			</div>
		</div>
	</div>

<script id="detail_template" type="text/x-handlebars-template" >
    <table class="table table-bordered table-striped">
    {{#each this}}
    {{#if @first}}
        <tr><td colspan="2" align="center">{{ zone }}</td><td colspan="2" align="center">{{ range }}</td></tr>
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
 
<script id="result_template" type="text/x-handlebars-template">
    <table class="table table-striped table-bordered">
    {{#each this}}
        {{#if @first}}
        <thead>
        	<tr><th colspan="6" style="text-align:center;">รายงานยอดขาย แยกตามพื้นที่การขาย  วันที่ {{ from }}  ถึงวันที่  {{ to }}      แสดง {{ range }}</th></tr>
            <tr>
            	<th style="text-align:center; width:5%;">ลำดับ</th>
                <th style="text-align:center;">พื้นที่การขาย</th>
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
                    <td>{{ zone }}</td>
                    <td align="right">{{ amount }}</td>
                    <td align="right">{{ return_amount }}</td>
                    <td align="right">{{ sale_amount }}</td>
                    <td align="center"><a href="javascript:void(0)" onClick="get_sale_detail({{ id_group }}, '{{ from }}', '{{ to }}')"> รายละเอียด</a></td>
                </tr>
            {{/if}}        
        {{/if}}
    {{/each}}
    </table>
</script>	

<script>
function get_sale_detail(id, from, to)
{
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?get_sale_detail_by_zone",
		type:"POST", cache: "false", data:{ "id_group" : id, "from_date" : from, "to_date" : to },
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
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if( !isDate(from_date) || !isDate(to_date) ){	swal("วันที่ไม่ถูกต้อง"); return false;	}
	var range = $("#range").val();
	var once = $("#select_group").val();
	var some = check_items();
	if(range == 1 && once == 0){  swal("ยังไม่ได้เลือกรายการ"); return false; }
	if(range == 2 && some == 0 ){ swal("ยังไม่ได้เลือกรายการ"); return false; }
	
	if(range == 0){ var datax = { "all" : "all"}; }
	if(range == 1){ var datax = {"select" : once }; }
	if(range == 2){ var datax = $("#select_form").serialize(); }
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?sale_report_zone&report&from_date="+from_date+"&to_date="+to_date+"&range="+range,
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



function check_items()
{
	var i = 0;
	$(".group_check").each(function(index, element) {
		if($(this).is(":checked")){ i++; }
    });
	return i;
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

function show_items()
{
	$("#range_modal").modal("show");	
}

function select_all()
{
	$("#btn_once").removeClass("btn-primary");
	$("#btn_range").removeClass("btn-primary");
	$("#btn_show").css("display", "none");
	$("#select_group").css("display", "none");
	$("#range").val(0);
	$("#btn_all").addClass("btn-primary");
}

function select_once()
{
	$("#btn_all").removeClass("btn-primary");
	$("#btn_range").removeClass("btn-primary");
	$("#btn_show").css("display", "none");
	$("#select_group").css("display", "");
	$("#range").val(1);
	$("#btn_once").addClass("btn-primary");
}

function select_range()
{
	$("#btn_all").removeClass("btn-primary");
	$("#btn_once").removeClass("btn-primary");
	$("#select_group").css("display", "none");
	$("#btn_show").css("display", "");
	$("#range").val(2)
	$("#btn_range").addClass("btn-primary");
}

$(document).ready(function(e) {
    $("#from_date").datepicker({	dateFormat: "dd-mm-yy", onClose: function(sd){ $("#to_date").datepicker("option", "minDate", sd); }});
	$("#to_date").datepicker({ dateFormat: "dd-mm-yy", onClose: function(sd){ $("#from_date").datepicker("option", "maxDate", sd); }});
});
</script>
<!-----  container  ----->