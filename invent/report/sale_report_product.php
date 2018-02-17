<?php 
	$page_name = "รายงานยอดขาย แยกตามสินค้า";
	?>
<div class="container">
<!-- page place holder -->
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $page_name; ?></h4>   </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-success" onClick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
            <button type="button" class="btn btn-sm btn-info" onClick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
        </p>
    </div>
</div>
<hr/>

<div class="row">
	<div class="col-sm-2" style="padding-right:5px;">
    	<label>สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" style="width:50%;"  id="btn-p-all" onClick="toggleProduct(0)">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="btn-p-range" onClick="toggleProduct(1)">เป็นช่วง</button>
        </div>
    </div>
    <div class="col-sm-3 padding-5" id="p-range" style="display:none;">
    	<label style="display:block;">เลือกสินค้า</label>
        <input type="text" class="form-control input-sm input-discount" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit" id="pdTo" name="pdTo" placeholder="สิ้นสุด" />
    </div>
    
    <div class="col-sm-2 padding-5">
    	<label>ผลลัพธ์</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" style="width:50%;"  id="btn-pd" onClick="toggleResult(0)">เป็นรุ่น</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="btn-pa" onClick="toggleResult(1)">เป็นรายการ</button>
        </div>
    </div>
    
    <div class="col-sm-3 padding-5">
    	<label>ช่องทางการขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary sale" style="width:34%;"  id="btn-sale-all" onClick="toggleSale(0)">ทั้งหมด</button>
        	<button type="button" class="btn btn-sm sale" style="width:33%;"  id="btn-sale-normal" onClick="toggleSale(1)">ปกติ</button>
            <button type="button" class="btn btn-sm sale" style="width:33%;" id="btn-sale-consign" onClick="toggleSale(5)">ฝากขาย</button>
        </div>
    </div>
    
    <div class="col-sm-2" style="padding-left:5px;">
    	<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" id="fromDate" name="fromDate" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" id="toDate" name="toDate" placeholder="สิ้นสุด" />
    </div>
    
    <input type="hidden" name="pdRange" id="pdRange" value="0" />
    <input type="hidden" name="pdResult" id="pdResult" value="0" /><!-- 0 = รวมยอดเป็นรุ่นสินค้า  , 1 = รวมยอดเป็นรายการสินค้า  -->
    <input type="hidden" name="pdSale" id="pdSale" value="0" /><!-- 0 = all , 1 = normal sale,  5 = consign sale	 -->
</div><!--/ row -->
<hr />
<div class="row">
	<div class="col-sm-12" id="rs">
      
    </div>
</div>



</div><!--/ container -->

<script id="reportTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if @first}}
<table class="table table-bordered">
    	<thead>
        	<tr style="font-size:14px; font-weight:bold;">
            	<th colspan="4" style="text-align:center; padding:15px; border:none;">
                	<span>รายงานยอดขาย แยกตามสินค้า</span>
                    <span style="margin-left:30px;"> วันที่ : {{ from }} - {{ to }}  </span>
                    <span style="margin-left:30px;">ช่วงสินค้า :  {{ pdRange }}  </span>
                    <span style="margin-left:30px;">ช่องทางการขาย : {{ pdSale }} </span>
					<span style="margin-left:30px;">แยกตาม : {{ pdResult }} </span>
                </th>
            </tr>
        	<tr style="font-size:12px;">
            	<th style="width:10%; text-align:center;">ลำดับ</th>
                <th style="width:60%; text-align:center;">สินค้า</th>
                <th style="width:15%; text-align:center;">จำนวน</th>
                <th style="width:15%; text-align:center">มูลค่า</th>
            </tr>
        </thead>
        <tbody>
	{{else}}
		{{#if @last}}
		<tr>
        	<td colspan="2" align="right" style="padding-right:35px; font-size:16px; font-weight:bold;">รวม</td>
            <td align="right" style="font-size:16px; font-weight:bold;">{{ totalQty }}</td>
            <td align="right" style="font-size:16px; font-weight:bold;">{{ totalAmount }}</td>
        </tr>
        </tbody>
    </table> 
		{{else}}	
        <tr>
        	<td align="center">{{ no }}</td>
            <td>{{ product }}</td>
            <td align="right">{{ qty }}</td>
            <td align="right">{{ amount }}</td>
        </tr>
        {{/if}}
	{{/if}}	 
{{/each}}	
</script>	
	
<script>
<?php $qs = dbQuery("SELECT product_code FROM tbl_product ORDER BY product_code ASC"); ?>
var data = [
<?php while($rs = dbFetchArray($qs) ) : ?>
<?php		echo "'".$rs['product_code']."',"; ?>
<?php endwhile; ?>
];


function getReport()
{
	var pd 		= $("#pdRange").val(); 	 //----- ช่วงสินค้า 0 = ทั้งหมด 1 = เป็นช่วง
	var pr		= $("#pdResult").val();	//----- แสดงเป็นรุ่น หรือ รายการ  0 = รุ่น 1 = รายการ
	var ps		= $("#pdSale").val();		//----- ช่องทางการขาย 0 = ทั้งหมด , 1 =  ปกติ, 5 = ฝากขาย
	var from		= $("#fromDate").val();
	var to			= $("#toDate").val();
	var pFrom	= $("#pdFrom").val();	//----- รหัสสินค้าเริ่มต้น กรณีที่เลือกสินค้าเป็นช่วง
	var pTo		= $("#pdTo").val();		//----- รหัสสินค้าสิ้นสุด กรณีที่เลือกสินค้าเป็นช่วง
		
	if( pd == 1 && (pFrom == '' || pTo == '') ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( !isDate(from) || !isDate(to) ){ swal('วันที่ไม่ถูกต้อง'); return false; }
	
	load_in();
	
	$.ajax({
		url:"report/reportController/saleReportController.php?soldByProduct",
		type:"POST", cache:"false", data:{ "pdRange" : pd, "pdResult" : pr, "pdSale" : ps, "fromDate" : from, "toDate" : to, "pdFrom" : pFrom, "pdTo" : pTo },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != "fail" && rs != '' )
			{
				var source 	= $("#reportTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#rs");
				render(source, data, output);	
			}
		}
	});	
}


function doExport()
{
	var pd 		= $("#pdRange").val(); 	 //----- ช่วงสินค้า 0 = ทั้งหมด 1 = เป็นช่วง
	var pr		= $("#pdResult").val();	//----- แสดงเป็นรุ่น หรือ รายการ  0 = รุ่น 1 = รายการ
	var ps		= $("#pdSale").val();		//----- ช่องทางการขาย 0 = ทั้งหมด , 1 =  ปกติ, 5 = ฝากขาย
	var from		= $("#fromDate").val();
	var to			= $("#toDate").val();
	var pFrom	= $("#pdFrom").val();	//----- รหัสสินค้าเริ่มต้น กรณีที่เลือกสินค้าเป็นช่วง
	var pTo		= $("#pdTo").val();		//----- รหัสสินค้าสิ้นสุด กรณีที่เลือกสินค้าเป็นช่วง
		
	if( pd == 1 && (pFrom == '' || pTo == '') ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	if( !isDate(from) || !isDate(to) ){ swal('วันที่ไม่ถูกต้อง'); return false; }
	var token = new Date().getTime();
	get_download(token);
	window.location.href = "report/reportController/saleReportController.php?exportSoldByProduct&pdRange="+pd+"&pdResult="+pr+"&pdSale="+ps+"&pdFrom="+pFrom+"&pdTo="+pTo+"&fromDate="+from+"&toDate="+to+"&token="+token;
}

$("#fromDate").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(sd){ $("#toDate").datepicker('option', 'minDate', sd); } });
$("#toDate").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(sd){ $("#fromDate").datepicker('option', 'maxDate', sd); } });


function toggleSale(p)
{
	var pd = $("#pdSale").val();
	if( p == 0 && pd != 0 )
	{
		$("#pdSale").val(0);
		$(".sale").removeClass('btn-primary');
		$("#btn-sale-all").addClass('btn-primary');
	}
	if( p == 1 && pd != 1 )
	{
		$("#pdSale").val(1);
		$(".sale").removeClass('btn-primary');
		$("#btn-sale-normal").addClass('btn-primary');	
	}
	if( p == 5 && pd != 5 )
	{
		$("#pdSale").val(5);
		$(".sale").removeClass('btn-primary');
		$("#btn-sale-consign").addClass('btn-primary');		
	}
}



function toggleResult(r)
{
	var rs = $("#pdResult").val();
	//----  เลือกเป็น รายการ
	if( rs == 0 && r == 1 )  //---- ถ้าของเก่าเป็น รุ่น และของใหม่เป็น รายการ
	{
		//-----  เปลี่ยนค่าเป็น รายการตามค่าใหม่ที่เลือกมา
		$("#pdResult").val(1);  
		$("#btn-pd").removeClass('btn-primary');
		$("#btn-pa").addClass('btn-primary');	
	}
	
	//---- เลือกเป็น รุ่นสินค้า
	if( rs == 1 && r == 0 ) //---- ถ้าของเก่าเป็น รายการ และของใหม่เป็น รุ่น
	{
		//-----  เปลี่ยนค่าเป็น รุ่น ตามค่าใหม่ที่เลือกมา
		$("#pdResult").val(0);	
		$("#btn-pa").removeClass('btn-primary');
		$("#btn-pd").addClass('btn-primary');
	}
		
}


function toggleProduct(p)
{
	var pd = $("#pdRange").val()
	if( pd == 0 && p == 1)
	{
		$("#pdRange").val(1);
		$("#btn-p-all").removeClass('btn-primary');
		$("#btn-p-range").addClass('btn-primary');
		$("#p-range").css('display', '');		
		$("#pdFrom").focus();
	}
	if( pd == 1 && p == 0 )
	{
		$("#pdRange").val(0);
		$("#btn-p-range").removeClass('btn-primary');
		$("#btn-p-all").addClass('btn-primary');
		$("#pdFrom").val('');
		$("#pdTo").val('');
		$("#p-range").css('display', 'none');	
	}
}

$(document).ready(function(e) {
   $("#pdFrom").autocomplete({
	   minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var from = $(this).val();
			var to		= $("#pdTo").val();
			if(to != "")
			{
				if(to < from)
				{ 
					$("#pdFrom").val(to);
					$("#pdTo").val(from);
				}
			}
		}
	});		
	
	$("#pdTo").autocomplete({
		minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var to 	= $(this).val();
			var from	=	$("#pdFrom").val();
			if(from !="")
			{
				if(to < from)
				{
					$("#pdTo").val(from);
					$("#pdFrom").val(to);	
				}
			}
		}
	});			
});
</script>