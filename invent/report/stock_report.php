<?php  
	$pageName = 'รายงานสินค้าคงเหลือ';  
	include_once 'function/report_helper.php';
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-list"></i> รายงาน</button>
            <button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="printReport()"><i class="fa fa-print"></i> พิมพ์</button>
        </p>
    </div>
</div><!--/ row -->
<hr/>
<form id="conditionForm">
<div class="row">
	<div class="col-sm-2">
    	<label style="display:block;">สินค้า</label>
    	<div class="btn-group width-100">
        	<button type="button" id="btn-pdAll" class="btn btn-sm btn-primary width-50" onclick="toggleProduct(0)">ทั้งหมด</button>
            <button type="button" id="btn-pdRange" class="btn btn-sm width-50" onclick="toggleProduct(1)">เป็นช่วง</button>
        </div>
    </div>
    <div class="col-sm-2">
    	<label class="not-show">From</label>
        <input type="text" class="form-control input-sm" name="pdFrom" id="pdFrom" placeholder="จาก : เลือกสินค้า" disabled />
    </div>
    <div class="col-sm-2">
    	<label class="not-show">TO</label>
        <input type="text" class="form-control input-sm" name="pdTo" id="pdTo" placeholder="ถึง : เลือกสินค้า" disabled />
    </div>
    <div class="col-sm-2">
    	<label style="display:block;">คลัง</label>
        <div class="btn-group width-100">
        	<button type="button" id="btn-whAll" class="btn btn-sm btn-primary width-50" onclick="toggleWh(0)">ทั้งหมด</button>
            <button type="button" id="btn-whList" class="btn btn-sm width-50" onclick="toggleWh(1)">บางรายการ</button>
        </div>
    </div>
    <div class="col-sm-2">
    	<label style="display:block;">วันที่</label>
        <div class="btn-group width-100">
        	<button type="button" id="btn-current" class="btn btn-sm btn-primary width-50" onclick="toggleDate(0)">ปัจจุบัน</button>
            <button type="button" id="btn-onDate" class="btn btn-sm width-50" onclick="toggleDate(1)">ณ วันที่</button>
        </div>
    </div>
    <div class="col-sm-1 col-1-harf">
    	<label class="not-show">date</label>
        <input type="text" name="date" id="date" class="form-control input-sm" placeholder="ระบุวันที่" disabled />
    </div>
    
    <input type="hidden" name="idFrom" id="idFrom" value="0" />
    <input type="hidden" name="idTo" id="idTo" value="0" />
    <input type="hidden" name="whOption" id="whOption" value="0" />
    <input type="hidden" name="pdOption" id="pdOption" value="0" />
    <input type="hidden" name="dateOption" id="dateOption" value="0" />
</div><!--/ row condition -->
<hr/>
<div class="row">
	<div class="col-sm-12" id="result">
    	<!-- result will be here -->
    </div>
</div><!--/ row result -->
<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='modal_title'>เลือกคลัง</h4>                
            </div>
            <div class='modal-body' id='modal_body'>
         	
		<?php $qs = dbQuery("SELECT * FROM tbl_warehouse"); ?>
        <?php if( dbNumRows($qs) > 0 ) : ?>
        <?php	while( $rs = dbFetchObject($qs) ) : ?>
        		<div class="col-sm-12">
                	<label><input type="checkbox" class="chk" name="wh[<?php echo $rs->id_warehouse; ?>]" value="<?php echo $rs->id_warehouse; ?>" style="margin-right:10px;" /><?php echo $rs->warehouse_name; ?></label>
                </div>
		<?php 	endwhile; ?>                
        <?php endif; ?>    
        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
</form>
</div><!--/ container -->

<script id="reportTemplate" type="text/x-handlebarsTemplate">
<table class="table table-striped">
	<thead>
    	<tr>
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:15%;">บาร์โค้ด</th>
            <th style="width:20%;">รหัสสินค้า</th>
            <th style="width:30%;">ชื่อสินค้า</th>
            <th style="width:10%; text-align:right;">ทุน</th>
            <th style="width:10%; text-align:right;">คงเหลือ</th>
            <th style="width:10%; text-align:right;">มูลค่า</th>
        </tr>
    </thead>
    <tbody>
    {{#each this}}
    	{{#if @last}}
        	<tr style="font-size:12px;">
            	<td colspan="5" align="right"><strong>รวม</strong></td>
                <td align="right"><strong>{{ total_qty }}</strong></td>
                <td align="right"><strong>{{ total_amount }}</strong></td>
            </tr>
        {{else}}
        	<tr style="font-size:12px;">
            	<td align="center">{{ no }}</td>
                <td>{{ barcode }}</td>
                <td>{{ reference }}</td>
                <td>{{ product_name }}</td>
                <td align="right">{{ cost }}</td>
                <td align="right">{{ qty }}</td>
                <td align="right">{{ amount }}</td>
            </tr>
        {{/if}}
    {{/each}}
    </tbody>
</table>
</script>
<script>
function getReport()
{
	var whOption = $("#whOption").val();  //--- เลือกคลังแบบไหน
	var pdOption = $("#pdOption").val();  //---- เลือกสินค้าแบบไหน
	var dateOption = $("#dateOption").val();  //----- เลือกวันที่แบบไหน
	var pdFrom = $("#pdFrom").val();  //--- สินค้าเริ่มต้น
	var pdTo = $("#pdTo").val();  //---- สินค้าสุดท้าย
	var idFrom = $("#idFrom").val();  //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var idTo = $("#idTo").val(); //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var wh 	= $(".chk:checked").length;
	var date = $("#date").val();
	//----- ตรวจสอบเงื่อนไขสินค้า
	if( pdOption == 1 && ( pdFrom == '' || pdTo == '' || idFrom == 0 || idTo == 0 ) ){
		swal("สินค้าไม่ถูกต้อง"); 
		return false;	
	}
	
	//----- ตรวจสอบเงื่อนไขคลังสินค้า
	if( whOption == 1 && wh == 0 ){
		swal("กรุณาระบุคลังสินค้า");
		return false;
	}
	
	//---- ตรวจสอบวันที่
	if( dateOption == 1 && ! isDate(date) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	
	var data = $("#conditionForm").serialize();
	
	load_in();
	$.ajax({
		url:"report/reportController/stockReportController.php?reportStockBalance&report",
		type:"POST", cache:"false", data: data,
		success: function(rs){
			load_out();
			var source 	= $("#reportTemplate").html();
			var data		= $.parseJSON(rs);
			var output 	= $("#result");
			render(source, data, output);
		}
	});
		
}

function doExport()
{
	var whOption = $("#whOption").val();  //--- เลือกคลังแบบไหน
	var pdOption = $("#pdOption").val();  //---- เลือกสินค้าแบบไหน
	var dateOption = $("#dateOption").val();  //----- เลือกวันที่แบบไหน
	var pdFrom = $("#pdFrom").val();  //--- สินค้าเริ่มต้น
	var pdTo = $("#pdTo").val();  //---- สินค้าสุดท้าย
	var idFrom = $("#idFrom").val();  //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var idTo = $("#idTo").val(); //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var wh 	= $(".chk:checked").length;
	var date = $("#date").val();
	//----- ตรวจสอบเงื่อนไขสินค้า
	if( pdOption == 1 && ( pdFrom == '' || pdTo == '' || idFrom == 0 || idTo == 0 ) ){
		swal("สินค้าไม่ถูกต้อง"); 
		return false;	
	}
	
	//----- ตรวจสอบเงื่อนไขคลังสินค้า
	if( whOption == 1 && wh == 0 ){
		swal("กรุณาระบุคลังสินค้า");
		return false;
	}
	
	//---- ตรวจสอบวันที่
	if( dateOption == 1 && ! isDate(date) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	
	var data = $("#conditionForm").serialize();
	
	load_in();
	var token	= new Date().getTime();
	var url 		= "report/reportController/stockReportController.php?reportStockBalance&export&"+data+"&token="+token;
	get_download(token);
	window.location.href = url;
}

function printReport()
{
	var whOption = $("#whOption").val();  //--- เลือกคลังแบบไหน
	var pdOption = $("#pdOption").val();  //---- เลือกสินค้าแบบไหน
	var dateOption = $("#dateOption").val();  //----- เลือกวันที่แบบไหน
	var pdFrom = $("#pdFrom").val();  //--- สินค้าเริ่มต้น
	var pdTo = $("#pdTo").val();  //---- สินค้าสุดท้าย
	var idFrom = $("#idFrom").val();  //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var idTo = $("#idTo").val(); //---- ไว้ตรวจสอบว่าสินค้าที่เลือกถูกต้อง
	var wh 	= $(".chk:checked").length;
	var date = $("#date").val();
	//----- ตรวจสอบเงื่อนไขสินค้า
	if( pdOption == 1 && ( pdFrom == '' || pdTo == '' || idFrom == 0 || idTo == 0 ) ){
		swal("สินค้าไม่ถูกต้อง"); 
		return false;	
	}
	
	//----- ตรวจสอบเงื่อนไขคลังสินค้า
	if( whOption == 1 && wh == 0 ){
		swal("กรุณาระบุคลังสินค้า");
		return false;
	}
	
	//---- ตรวจสอบวันที่
	if( dateOption == 1 && ! isDate(date) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	
	var data = $("#conditionForm").serialize();
	var url 	= "report/reportController/stockReportController.php?reportStockBalance&print&"+data;
	var center = ($(document).width() - 800)/2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

$("#pdFrom").autocomplete({
	source: 'controller/autoComplete.php?getProductCode',
	autoFocus: true,
	close: function(event,ui){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		var code = arr[0];
		var idpd 	= arr[2];
		if( isNaN(idpd) ){
			idpd = 0;
		}
		$(this).val(code);
		$("#idFrom").val(idpd);
		var pdTo = $("#pdTo").val();
		var pdFrom = $("#pdFrom").val();
		var idFrom = $("#idFrom").val();
		var idTo = $("#idTo").val();
		if( pdTo != '' && pdFrom > pdTo){
			$("#pdFrom").val(pdTo);
			$("#pdTo").val(pdFrom);
			$("#idFrom").val(idTo);
			$("#idTo").val(idFrom);	
		}
		if( $("#pdFrom").val() != '' ){
			$("#pdTo").focus();
		}
	}		
});

$("#pdTo").autocomplete({
	source: 'controller/autoComplete.php?getProductCode',
	autoFocus: true,
	close: function(event,ui){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		var code = arr[0];
		var idpd 	= arr[2];
		if( isNaN(idpd) ){
			idpd = 0;
		}
		$(this).val(code);
		$("#idTo").val(idpd);
		var pdTo = $("#pdTo").val();
		var pdFrom = $("#pdFrom").val();
		var idFrom = $("#idFrom").val();
		var idTo = $("#idTo").val();
		if( pdFrom != '' && pdFrom > pdTo){
			$("#pdFrom").val(pdTo);
			$("#pdTo").val(pdFrom);	
			$("#idFrom").val(idTo);
			$("#idTo").val(idFrom);	
		}
	}		
});


$("#date").datepicker({ dateFormat: 'dd-mm-yy' });
function toggleDate(id)
{
	if( id == 0 ){
		$("#dateOption").val(0);
		$("#btn-onDate").removeClass('btn-primary');
		$("#btn-current").addClass('btn-primary');
		$("#date").attr("disabled", "disabled");
	}
	if( id == 1 ){
		$("#dateOption").val(1);
		$("#btn-current").removeClass('btn-primary');
		$("#btn-onDate").addClass('btn-primary');
		$("#date").removeAttr('disabled');
	}
}

function toggleProduct(id)
{
	if( id == 0 ){
		$("#pdOption").val(0);
		$("#pdFrom").attr("disabled", "disabled");
		$("#pdTo").attr("disabled", "disabled");
		$("#btn-pdRange").removeClass('btn-primary');
		$("#btn-pdAll").addClass('btn-primary');
	}
	if( id == 1 ){
		$("#pdOption").val(1);
		$("#pdFrom").removeAttr("disabled");
		$("#pdTo").removeAttr("disabled");
		$("#btn-pdAll").removeClass('btn-primary');
		$("#btn-pdRange").addClass('btn-primary');
	}
}
function toggleWh(id)
{
	if( id == 0 ){
		$("#whOption").val(0);
		$("#btn-whList").removeClass('btn-primary');
		$("#btn-whAll").addClass('btn-primary');	
	}
	if( id == 1 ){
		$("#whOption").val(1);
		$("#btn-whAll").removeClass('btn-primary');
		$("#btn-whList").addClass('btn-primary');
		$("#wh-modal").modal('show');
	}
}
</script>