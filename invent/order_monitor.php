
<?php 
	$page_name	= "ติดตามออเดอร์";
	$id_tab 			= 14;
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/order_helper.php';
?>    
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-success btn-sm" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
		</p>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-2 padding-5" style="padding-left:15px;">
 		<label>เอกสาร</label>
        <input type="text" class="form-control input-sm" id="s_ref" name="s_ref" value="" placeholder="พิมพ์เลขที่เอกสาร" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="s_cus" name="s_cus" value="" placeholder="พิมพ์ชื่อลูกค้า" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>พนักงาน</label>
        <input type="text" class="form-control input-sm" id="s_emp" name="s_emp" value="" placeholder="พิมพ์ชื่อพนักงาน" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" id="from_date" name="from_date" value="" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" id="to_date" name="to_date" value="" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label style="display:block;">เวลา</label>
        <select class="form-control input-sm input-discount width-50" name="fhour" id="fhour">
        	<?php echo selectTime(); ?>
        </select>
        <select class="form-control input-sm input-unit width-50" name="thour" id="thour">
        	<?php echo selectTime(); ?>
        </select>
    </div>
     <div class="col-sm-2 padding-5 last">
    	<label style="display:block; visibility:hidden;">&nbsp;</label>
    	<select class="form-control input-sm" id="selectState" onchange="stateChange()">
        	<?php echo selectStateTime(); ?>
        </select>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-sm-5 padding-5 first">
 		<label style="display:block;">ประเภท</label>
        <div class="btn-group width-100">
            <button type="button" id="btn-online" class="btn btn-sm"  onClick="show('online')" >ออนไลน์</button>
            <button type="button" id="btn-credit" class="btn btn-sm" onClick="show('credit')">เครดิต</button>
            <button type="button" id="btn-cash" class="btn btn-sm" onClick="show('cash')">เงินสด</button>
            <button type="button" id="btn-consign" class="btn btn-sm" onClick="show('consign')">ฝากขาย</button>
            <button type="button" id="btn-support" class="btn btn-sm" onClick="show('support')">อภินันท์</button>
            <button type="button" id="btn-sponsor" class="btn btn-sm" onClick="show('sponsor')">สปอนเซอร์</button>
            <button type="button" id="btn-transform" class="btn btn-sm" onClick="show('transform')">แปรสภาพ</button>            
        </div>        
    </div> 
    <div class="col-sm-5 padding-5">
    	<label style="display:block;">สถานะ</label>
    	<div class="btn-group width-100">
            <button type="button" id="btn-state-1" class="btn btn-sm" onclick="toggleState(1)">รอชำระเงิน</button>
            <button type="button" id="btn-state-3" class="btn btn-sm" onclick="toggleState(3)">รอจัดสินค้า</button>
            <button type="button" id="btn-state-4" class="btn btn-sm" onclick="toggleState(4)">กำลังจัดสินค้า</button>
            <button type="button" id="btn-state-5" class="btn btn-sm" onclick="toggleState(5)">รอตรวจ</button>
            <button type="button" id="btn-state-11" class="btn btn-sm" onclick="toggleState(11)">กำลังตรวจ</button>
            <button type="button" id="btn-state-10" class="btn btn-sm" onclick="toggleState(10)">รอเปิดบิล</button>
 
        </div>   
	</div>
     <div class="col-sm-2">
    	<label style="display:block;">Group By</label>
        <div class="btn-group width-100">
        <button type="button" id="group-customer" class="btn btn-sm group-by width-50" onclick="groupBy('customer')">ลูกค้า</button>
        <button type="button" id="group-transport" class="btn btn-sm group-by width-50" onclick="groupBy('transport')">ขนส่ง</button>
        </div>
    </div>
	
</div>

<input type="hidden" id="online" value=""/>
<input type="hidden" id="credit" value=""/>
<input type="hidden" id="cash" value=""/>
<input type="hidden" id="consign" value=""/>
<input type="hidden" id="support" value=""/>
<input type="hidden" id="sponsor" value=""/>
<input type="hidden" id="transform" value=""/>

<input type="hidden" id="state-1" value=""/>
<input type="hidden" id="state-3" value=""/>
<input type="hidden" id="state-4" value=""/>
<input type="hidden" id="state-5" value=""/>
<input type="hidden" id="state-11" value=""/>
<input type="hidden" id="state-10" value=""/>
      
<input type="hidden" id="group_by" value="" />
<hr />
<div class="row">
	<div class="col-sm-12" id="rs">
    
    </div>
</div>

<script id="no-group-template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead>
    	<tr style="font-size:12px;">
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:10%;">เลขที่อ้างอิง</th>
            <th style="width:10%;">ลูกค้า</th>
            <th style="width:10%;">จังหวัด</th>
            <th style="width:10%;">พนักงาน</th>
            <th style="width:10%; text-align:center;">ยอดเงิน</th>
            <th style="width:10%; text-align:center;">เงื่อนไข</th>
            <th style="width:10%; text-align:center;">สถานะ</th>
            <th style="width:10%; text-align:center;">เพิ่ม</th>
            <th style="width:10%; text-align:center;">ปรับปรุง</th>
        </tr>
    </thead>
    <tbody>
    {{#each this}}
		{{#if no}}
    	<tr style="font-size:10px;">
        	<td align="center">{{ no }}</td>
            <td>{{ reference }}</td>
            <td>{{ customer }}</td>
            <td>{{ province }}</td>
            <td>{{ employee }}</td>
            <td align="center">{{ amount }}</td>
            <td align="center">{{ payment }}</td>
            <td align="center">{{ state }}</td>
            <td align="center">{{ date_add }}</td>
            <td align="center">{{ date_upd }}</td>
        </tr>
		{{else}}
		<tr>
        	<td colspan="10" align="center"><h4>ไม่พบรายการตามเงื่อนไขที่กำหนด</h4></td>
        </tr>
		{{/if}}
	{{/each}}       
    </tbody>
</table>
</script>

<script id="customer-group-template" type="text/x-handlebars-template">
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	{{#each this}}
  			<div class="panel panel-default" style="border:solid 1px #ccc; margin-top:2px;">
    			<div class="panel-heading" role="tab" id="heading_{{customer_id}}">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{customer_id}}" aria-expanded="true" aria-controls="collapse_{{customer_id}}">
							<span style="padding-left:10px;">{{ customer_name }}</span>
							<span class="pull-right" style="padding-right:50px;">{{ total_order }}</span>				
						</a>
					</h4>
    			</div>
    			<div id="collapse_{{customer_id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_{{customer_id}}">
				  <div class="panel-body" style="padding-top:0px; padding-bottom:0px;">
						<table class="table table-striped" style="margin-bottom:0px;">
							<tr style="font-size:12px;">
								<th style="width:5%; text-align:center;">ลำดับ</th>
								<th style="width:10%;">เลขที่อ้างอิง</th>
								<th style="width:10%;">พนักงาน</th>
								<th style="width:10%; text-align:center;">ยอดเงิน</th>
								<th style="width:10%; text-align:center;">เงื่อนไข</th>
								<th style="width:10%; text-align:center;">สถานะ</th>
								<th style="width:10%; text-align:center;">เพิ่ม</th>
								<th style="width:10%; text-align:center;">ปรับปรุง</th>
							</tr>
							{{#each order}}
							<tr style="font-size:10px;">
								<td align="center">{{ no }}</td>
								<td>{{ reference }}</td>
								<td>{{ employee }}</td>
								<td align="center">{{ amount }}</td>
								<td align="center">{{ payment }}</td>
								<td align="center">{{ state }}</td>
								<td align="center">{{ date_add }}</td>
								<td align="center">{{ date_upd }}</td>
							</tr>
							{{/each}}
						</table>
				  </div>
    		</div>
  		</div>
  {{/each}}
  </div>
</script>
</div><!--/ container -->

<script>
function getSearch()
{
	var group = $("#group_by").val();
	var dataset = { 
							"s_ref" : $("#s_ref").val(), 
							"s_cus" : $("#s_cus").val(), 
							"s_emp" : $("#s_emp").val(), 
							"from_date" : $("#from_date").val(), 
							"to_date" : $("#to_date").val(), 
							"fhour" : $("#fhour").val(), 
							"thour" : $("#thour").val(), 
							"timeState" : $("#selectState").val(), 
							"online" : $("#online").val(), 
							"credit" : $("#credit").val(), 
							"cash" : $("#cash").val(), 
							"consign" : $("#consign").val(),
							"support" : $("#support").val(),
							"sponsor" : $("#sponsor").val(),
							"transform" : $("#transform").val(),
							"state_1" : $("#state-1").val(),
							"state_3" : $("#state-3").val(),
							"state_4" : $("#state-4").val(),
							"state_5" : $("#state-5").val(),
							"state_11" : $("#state-11").val(),
							"state_10" : $("#state-10").val(),
							"group_by" : $("#group_by").val()
						};
	
		
 	if( group == "customer"){
		getReportCustomer(dataset);
	}else if( group == "transport" ){
		getReportTransport(dataset);
	}else{
		getReportNogroup(dataset);
	}
}

function getReportCustomer(dataset)
{
	load_in();						
	$.ajax({
		url:"report/reportController/orderReportController.php?orderMoniter&group_by_customer",
		type:"POST", cache:"false", data: dataset,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != ""){
				var source	= $("#customer-group-template").html();
				var data = $.parseJSON(rs);
				var output = $("#rs");
				render(source, data, output);
			}
		}
	});	
}

function getReportNogroup(dataset)
{
	load_in();						
	$.ajax({
		url:"report/reportController/orderReportController.php?orderMoniter&no_group",
		type:"POST", cache:"false", data: dataset,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != ""){
				var source	= $("#no-group-template").html();
				var data = $.parseJSON(rs);
				var output = $("#rs");
				render(source, data, output);
			}
		}
	});	
}


	function groupBy(name)
	{
		var btn = $("#group-"+name);
		var input = $("#group_by");
		if( 	btn.hasClass("btn-info") && input.val() == name ){
			input.val("");
			btn.removeClass("btn-info");	
		}else{
			$(".group-by").removeClass("btn-info");
			input.val(name);
			btn.addClass("btn-info");	
		}
	}
	
	function show(name)
	{
		var btn = $("#btn-"+name);
		var input = $("#"+name);
		if( btn.hasClass("btn-info") && input.val() != ""){
			input.val("");
			btn.removeClass("btn-info");
		}else{
			input.val(name);
			btn.addClass("btn-info");
		}			
	}
	
	
	function toggleState(id)
	{
		var btn = $("#btn-state-"+id);
		if( btn.hasClass("btn-info") ){
			$("#state-"+id).val("");
			btn.removeClass("btn-info");
		}else{ 
			$("#state-"+id).val(id);
			btn.addClass("btn-info");
		}
	}
	
	function stateChange(){
		var state = $("#selectState").val();	
		if(state != "" ){
			$("#state-1").val("");
			$("#btn-state-1").removeClass("btn-info");
			$("#btn-state-1").attr("disabled", "disabled");	
		}else{
			$("#btn-state-1").removeAttr("disabled");	
		}
	}
	
	$("#from_date").datepicker({
    	dateFormat: 'dd-mm-yy',
    	onClose: function(selectedDate) {
        	$("#to_date").datepicker("option", "minDate", selectedDate);
        	if ($(this).val() != '' && $("#to_date").val() == '') { $("#to_date").focus(); }
    	}
	});

	$("#to_date").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(selectedDate) {
			$("#from_date").datepicker("option", "maxDate", selectedDate);
		}
	});
</script>