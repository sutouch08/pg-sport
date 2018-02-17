<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $pageTitle; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-sm btn-default" onclick="updateReceived()"><i class="fa fa-retweet"></i> &nbsp; คำนวณยอดรับสินค้าใหม่</button>
        	<button type="button" class="btn btn-success btn-sm" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-sm-2 padding-5 first">
    	<label class="display-block">สินค้า</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pdAll" onclick="allProduct()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="btn-pdRange" onclick="rangeProduct()">เป็นช่วง</button>
        </div>
    </div>
    <div class="col-sm-2 padding-5">
    	<label class="display-block not-show">From</label>
        <input type="text" class="form-control input-sm text-center" id="pdFrom" placeholder="เริ่มต้น" disabled />
    </div>
     <div class="col-sm-2 padding-5 last">
    	<label class="display-block not-show">To</label>
        <input type="text" class="form-control input-sm text-center" id="pdTo" placeholder="สิ้นสุด" disabled />
    </div>

	<div class="col-sm-2 padding-5 first">
    	<label class="display-block">ผู้ขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm width-50" id="all_sup" onClick="all_sup()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="select_sup" onClick="select_sup()">เฉพาะ</button>
		</div>
	</div>
    <div class="col-sm-4 padding-5 last">
		<label>&nbsp;</label>
		<input type="text" class="form-control input-sm" id="supplier" style="text-align:center;" placeholder="ค้นหาชื่อผู้ขาย" disabled />
	</div> 
    
    <div class="divider-hidden margin-top-10 margin-bottom-10"></div>
    <div class="col-sm-2 padding-5 first">
    	<label style="display:block;">วันที่</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm width-50" id="tmie_all" onClick="all_time()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm width-50" id="time_rank" onClick="select_time()">ระหว่าง</button>
		</div>
	</div>
	<div class="col-sm-2 padding-5">
		<label class="not-show">จากวันที่</label>
		<input type="text" class="form-control input-sm text-center" id="from_date" placeholder="จากวันที่" disabled="disabled" />
	</div> 
 	<div class="col-sm-2 padding-5 last">
		<label class="not-show">ถึงวันที่</label>
		<input type="text" class="form-control input-sm text-center" name="to_date" id="to_date" placeholder="ถึงวันที่" disabled="disabled" />
	</div>   
    <div class="col-sm-3">
    	<label class="display-block">ใบสั่งซื้อ</label>
    	<div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-33" id="btn-openPO" onclick="openPO()">ยังไม่ปิด</button>
            <button type="button" class="btn btn-sm width-33" id="btn-closePO" onclick="closePO()">ปิดแล้ว</button>
        	<button type="button" class="btn btn-sm width-33" id="btn-allPO" onclick="allPO()">ทั้งหมด</button>
        </div>
    </div>
    <div class="col-sm-3">
    	<label class="display-block">รูปแบบสินค้า</label>
    	<div class="btn-group width-100">
        	<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd" onclick="toggleView(0)">แสดงรุ่นสินค้า</button>
            <button type="button" class="btn btn-sm width-50" id="btn-pa" onclick="toggleView(1)">แสดงรายการสินค้า</button>
        </div>
    </div>
    	<input type="hidden" id="id_sup" />
        <input type="hidden" id="rank" value="1" />
        <input type="hidden" id="sup_rank" value="1" />
        <input type="hidden" id="pdOption" value="0" />
        <input type="hidden" id="poOption" value="0" /> <!-- 0 = ยังไม่ปิด  1= ปิดแล้ว  2= ทั้งหมด -->
        <input type="hidden" id="viewOption" value="0" /><!-- 0 = แสดงรุ่นสินค้า  1 = แสดงรายการสินค้า -->
        
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-12" id="rs">
    	
    </div>
</div>
</div><!------- container ------>    

<script id="template" type="text/x-handlebars-template">
 <table class="table table-striped table-bordered">
    <thead>
    <th class="width-5 text-center">ลำดับ</th>
    <th class="width-10 text-center">วันที่</th>
	<th class="width-15 text-center">สินค้า</th>
    <th class="width-10 text-center">เลขที่เอกสาร</th>
    <th class="width-20 text-center">ผู้ขาย</th>
    <th class="width-10 text-center">กำหนดรับ</th>
    <th style="width:8%; text-align:center;">จำนวน</th>
    <th style="width:8%; text-align:center;">รับแล้ว</th>
    <th style="width:8%; text-align:center;">ค้างรับ</th>
	<th style="width:5%; text-align:center;">หมายเหตุ</th>
    </thead>
    {{#each this}}
		{{#if @last}}
		<tr>
			<td colspan="6" align="right">รวม</td>
			<td align="center">{{ totalPoQty }}</td>
			<td align="center">{{ totalReceivedQty }}</td>
			<td align="center">{{ totalBalanceQty }}</td>
			<td align="center"></td>
		</tr>
		{{else}}
		<tr style="font-size:12px;">
			<td align="center">{{ no }}</td>
			<td align="center">{{ date_add }}</td>
			<td>{{ product }}</td>
			<td align="center">{{ poReference }}</td>
			<td>{{ supplier }}</td>
			<td align="center">{{ due_date }}</td>
			<td align="center">{{ qty }}</td>
			<td align="center">{{ received }}</td>
			<td align="center">{{ backlog }}</td>
			<td align="center">{{ closed }}</td>
		</tr>
		{{/if}}
    {{/each}}
    </table>
</script>


<script>
function toggleView(id){
	if(id == 0 ){
		//---- แสดงรุ่นสินค้า
		$("#btn-pd").addClass('btn-primary');
		$("#btn-pa").removeClass('btn-primary');
	}else{
		//--- แสดงรายการสินค้า
		$("#btn-pd").removeClass('btn-primary');
		$("#btn-pa").addClass('btn-primary');
	}
	$("#viewOption").val(id);
}


function closePO(){
	$("#poOption").val(1);
	$("#btn-allPO").removeClass('btn-primary');
	$("#btn-openPO").removeClass('btn-primary');
	$("#btn-closePO").addClass('btn-primary');
}

function openPO(){
	$("#poOption").val(0);
	$("#btn-allPO").removeClass('btn-primary');
	$("#btn-closePO").removeClass('btn-primary');
	$("#btn-openPO").addClass('btn-primary');	
}

function allPO(){
	$("#poOption").val(2);
	$("#btn-openPO").removeClass('btn-primary');
	$("#btn-closePO").removeClass('btn-primary');
	$("#btn-allPO").addClass('btn-primary');	
}

function allProduct(){
	$("#pdOption").val(0);
	$("#btn-pdRange").removeClass('btn-primary');
	$("#btn-pdAll").addClass('btn-primary');
	$("#pdFrom").attr('disabled', 'disabled');
	$("#pdTo").attr('disabled', 'disabled');
}


function rangeProduct(){
	$("#pdOption").val(1);
	$("#btn-pdAll").removeClass('btn-primary');
	$("#btn-pdRange").addClass('btn-primary');
	$("#pdFrom").removeAttr('disabled');
	$("#pdTo").removeAttr('disabled');	
	$("#pdFrom").focus();
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


$("#pdFrom").autocomplete({
	minLength: 1,
	source: 'controller/autoComplete.php?product_code',
	autoFocus: true,
	close: function(event, ui)	{
		var pdFrom = $(this).val();
		var pdTo		= $("#pdTo").val();
		if( pdTo != "" && pdTo < pdFrom ){
			$(this).val(pdTo);
			$("#pdTo").val(pdFrom);
		}
		if( pdFrom != '' ) {
			$("#pdTo").focus();
		}
	}			
});

$("#pdTo").autocomplete({
	minLength: 1,
	source: 'controller/autoComplete.php?product_code',
	autoFocus: true,
	close: function(event, ui) {
		var pdTo = $(this).val();
		var pdFrom	= $("#pdFrom").val();
		if( pdFrom != '' && pdFrom > pdTo ) {
			$(this).val(pdFrom);
			$("#pdFrom").val(pdTo);
		}
	}
});


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


function report() {
	var pdOption	= $("#pdOption").val();
	var pdFrom		= $("#pdFrom").val();
	var pdTo			= $("#pdTo").val();
	var sup_rank 	= $("#sup_rank").val();
	var time_rank	= $("#rank").val();
	var id_sup		= $("#id_sup").val();
	var sup_name	= $("#supplier").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	var poOption	= $("#poOption").val();
	var viewOption	= $("#viewOption").val();
	
	if( pdOption == 1 && (pdFrom == "" || pdTo == "") ){
		swal("สินค้าไม่ถูกต้อง");
		return false;
	}
	
	if( sup_rank == 2 && (sup_name == "" || id_sup == "")) {
		swal("กรุณาระบุชื่อผู้ขาย หรือ เลือกทั้งหมด");
		return false;
	}
	
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	
	load_in();
	$.ajax({
		url:"report/reportController/poReportController.php?productBacklog&report",
		type: "POST", 
		cache: "false", 
		data:{ 
				"pdRange" : pdOption, 
				"pdFrom" : pdFrom, 
				"pdTo" : pdTo, 
				"supRange" : sup_rank, 
				"timeRange" : time_rank, 
				"id_sup" : id_sup, 
				"from" : from, 
				"to" : to,
				"poOption" : poOption,
				"viewOption" : viewOption
				},
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs =="nodata")
			{
				load_out();
				var data = "<div class='col-sm-4 col-sm-offset-4'><div class='alert alert-info' style='margin-top:50px;'><center><strong>ไม่มีใบสั่งซื้อค้างรับ ตามเงื่อนไขที่กำหนด</strong></center></div>";
				$("#rs").html(data);
			}else{
				var data 		= $.parseJSON(rs);
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
	var pdOption	= $("#pdOption").val();
	var pdFrom		= $("#pdFrom").val();
	var pdTo			= $("#pdTo").val();
	var sup_rank 	= $("#sup_rank").val();
	var time_rank	= $("#rank").val();
	var id_sup		= $("#id_sup").val();
	var sup_name	= $("#supplier").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	var poOption	= $("#poOption").val();
	var viewOption	= $("#viewOption").val();
	
	if( pdOption == 1 && (pdFrom == "" || pdTo == "") ){
		swal("สินค้าไม่ถูกต้อง");
		return false;
	}
	
	if( sup_rank == 2 && (sup_name == "" || id_sup == "")) {
		swal("กรุณาระบุชื่อผู้ขาย หรือ เลือกทั้งหมด");
		return false;
	}
	
	if(time_rank == 2 && ( !isDate(from) || !isDate(to) ) )	{
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;	
	}
	
	var token = new Date().getTime();
	var url = 'report/reportController/poReportController.php?productBacklog&export';
	url 	+= '&poOption='+poOption+'&pdOption='+pdOption+'&pdFrom='+pdFrom+'&pdTo='+pdTo;
	url		+= '&supRange='+sup_rank+'&id_sup='+id_sup+'&viewOption='+viewOption;
	url		+= '&timeRange='+time_rank+'&from='+from+'&to='+to+'&token='+token;
	get_download(token);
	window.location.href = url;
}

function updateReceived(){
	load_in();
	$.ajax({
		url:"controller/poController.php?updateReceivedQty",
		type:"GET", cache:"false",
		success: function(rs){
			load_out();
			swal({ title: 'Updated', text: 'ปรับปรุงยอดรับสินค้าเรียบร้อยแล้ว', type: 'success', timer: 1000 });
		}
	});
}

</script>