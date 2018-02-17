<?php 
	$page_name = "รายงานจำนวนขาย แยกตามคุณลักษณะสินค้า";
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
            <button type="button" class="btn btn-info btn-sm" onclick="do_export()"><i class="fa fa-file-excel-o"></i>  ส่งออก</button>
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->

<div class="row">
	<div class="col-sm-3">
    	<label>สินค้า</label>
        <input type="text" class="form-control input-sm" id="product_code" name="product_code" autocomplete="off" placeholder="ค้นหารหัสสินค้า" />
    </div>
	<div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="เริ่มต้น" />
    </div>
    <div class="col-sm-2">
    	<label style="visibility:hidden">วันที่</label>
        <input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-2">
    	<label>ลดหนี้</label>
        <select class="form-control input-sm" name="return_option" id="return_option">
        	<option value="1">หักลดหนี้</option>
            <option value="0">ไม่หักลดหนี้</option>
        </select>
    </div>
</div>

<hr style="margin-top: 10px;"/>
<div class="row">
	<div class="col-sm-12" id="rs">
    
    </div>
</div>



</div>

	

<script>
$("#product_code").autocomplete({
	source: "controller/autoComplete.php?product_code",
	autoFocus: true
});

function get_report()
{
	var from_date	= $("#from_date").val();
	var to_date 		= $("#to_date").val();
	var product		= $("#product_code").val();
	var option	 	= $("#return_option").val();
	if( product == ""){ swal("กรุณาระบุสินค้า"); return false; }
	if( !isDate(from_date) || !isDate(to_date) ){	swal("วันที่ไม่ถูกต้อง"); return false;	}
	load_in();
	$.ajax({
		url:"report/reportController/saleAnalyzController.php?sale_by_attribute&report",
		type:"POST", cache:"false", data:{ "from_date" : from_date, "to_date" : to_date, "product_code" : product, "option" : option },
		success: function(rs)
		{
			load_out();
			$("#rs").html(rs);
		}
	});	
}

function do_export()
{
	var from_date	= $("#from_date").val();
	var to_date 		= $("#to_date").val();
	var product		= $("#product_code").val();
	var option	 	= $("#return_option").val();
	if( product == ""){ swal("กรุณาระบุสินค้า"); return false; }
	if( !isDate(from_date) || !isDate(to_date) ){	swal("วันที่ไม่ถูกต้อง"); return false;	}
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/saleAnalyzController.php?sale_by_attribute&export&product_code="+product+"&from_date="+from_date+"&to_date="+to_date+"&option="+option+"&token="+token;
}


$(document).ready(function(e) {
    $("#from_date").datepicker({	dateFormat: "dd-mm-yy", onClose: function(sd){ $("#to_date").datepicker("option", "minDate", sd); }});
	$("#to_date").datepicker({ dateFormat: "dd-mm-yy", onClose: function(sd){ $("#from_date").datepicker("option", "maxDate", sd); }});
});
</script>
<!-----  container  ----->