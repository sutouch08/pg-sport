<?php
	$pageName = 'รายงานวิเคราะห์ขายแบบละเอียด';
	$idTab = 51;  //----- รายงานผู้บริหาร
	$id_profile	= $_COOKIE['profile_id'];
	$pm	= checkAccess($id_profile, $idTab);
	$view = $pm['view'];
	accessDeny($view);
?>

<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6"><p class="pull-right top-p"><button class="btn btn-sm btn-success" onClick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button></p></div>
</div><!--/ row -->
<hr/>
<div class="row">
	<div class="col-sm-3">
    	<label style="display:block;">ช่องทางการขาย</label>
    	<div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" id="btn-all"  onClick="toggleSale(0)" style="width:33%;">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" id="btn-sale" onClick="toggleSale(1)" style="width:33%;">ปกติ</button>
            <button type="button" class="btn btn-sm" id="btn-consign" onClick="toggleSale(5)" style="width:34%;">ฝากขาย</button>
        </div>
    </div>
    
    <div class="col-sm-3">
    	<label style="display:block;">ช่วงเวลา</label>
    	<input type="text" class="form-control input-sm input-discount text-center" name="fromDate" id="fromDate" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" />
    </div>
    
    
    
    <input type="hidden" name="saleType" id="saleType" value="0" />
</div>
<hr style="margin-bottom:35px;"/>
<div class="row">
	<div class="col-sm-12">
    <blockquote><p class="lead" style="color:#CCC;">เนื่องจากผลลัพธ์ของรายงานจะละเอียดและมีจำนวนข้อมูลในปริมาณมาก จึงไม่สามารถแสดงผลรายงานผ่านทางหน้าจอนี้ได้</p></blockquote></div>
</div>

</div><!--/ Container -->
<script>
function toggleSale(id)
{
	$("#saleType").val(id);
	if( id == 0 ){
		$("#btn-sale").removeClass('btn-primary');
		$("#btn-consign").removeClass('btn-primary');
		$("#btn-all").addClass('btn-primary');
	}else if( id == 1 ){
		$("#btn-all").removeClass('btn-primary');
		$("#btn-consign").removeClass('btn-primary');
		$("#btn-sale").addClass('btn-primary');
	}else if( id == 5 ){
		$("#btn-all").removeClass('btn-primary');
		$("#btn-sale").removeClass('btn-primary');
		$("#btn-consign").addClass('btn-primary');
	}
}

$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy', onClose: function(sd){
		$("#toDate").datepicker('option', 'minDate', sd);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy', onClose: function(sd){
		$("#fromDate").datepicker('option', 'maxDate', sd);		
	}
});

function isLeapYear(year)
{
	var isLeap = new Date(year,2,1,-1).getDate()==29;
	return isLeap;
}

function getMaxDate(days, d, m, y)
{
	var leap = isLeapYear(y);
	if( m == '02' && leap == true ){ mD = 29; }else if( m == '02' && leap == false ){ mD = 28; }
	if( m == '01' || m == '03' || m == '05' || m == '07' || m == '08' || m == '10' || m == '12' ){ mD = 31; }
	if( m == '04' || m == '06' || m == '09' || m == '11' ){ mD = 30; }
	var date = parseInt(d);
	var days = parseInt(days);
	while( days > 0 )
	{
		if( date == mD )
		{
			date = 1;
			m++;
		}else{
			date++;
		}
		days--;
	}
	return date+'-'+m+'-'+y;
}

function getMinDate(days, d, m, y)
{
	var leap = isLeapYear(y);
	if( m == '02' && leap == true ){ mD = 29; }else if( m == '02' && leap == false ){ mD = 28; }
	if( m == '01' || m == '03' || m == '05' || m == '07' || m == '08' || m == '10' || m == '12' ){ mD = 31; }
	if( m == '04' || m == '06' || m == '09' || m == '11' ){ mD = 30; }
	var date = parseInt(d);
	var days = parseInt(days);
	while( days > 0 )
	{
		if( date == 1 )
		{
			m--;
			date = mD;			
		}else{
			date--;
		}
		days--;
	}
	return date+'-'+m+'-'+y;
}

function doExport()
{
	var role 	= $("#saleType").val();
	var from 	= $("#fromDate").val();
	var to		= $("#toDate").val();
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง");  return false; }
	var token	= new Date().getTime();
	var url 		= "report/reportController/saleAnalyzController.php?soldProductDeepAnalyz&role="+role+"&from="+from+"&to="+to+"&token="+token;
	get_download(token);
	window.location.href = url;
}

</script>