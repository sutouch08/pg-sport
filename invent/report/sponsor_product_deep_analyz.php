<?php
	$pageName = 'รายงานวิเคราะห์สปอนเซอร์แบบละเอียด';
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
    <div class="col-sm-3 col-sm-offset-4">
    	<label style="display:block;">ช่วงเวลา</label>
    	<input type="text" class="form-control input-sm input-discount text-center" name="fromDate" id="fromDate" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" />
    </div>
    
    
    
    <input type="hidden" name="saleType" id="saleType" value="0" />
</div>
<hr style="margin-top:15px; margin-bottom:35px;"/>
<div class="row">
	<div class="col-sm-12">
    <blockquote><p class="lead" style="color:#CCC;">เนื่องจากผลลัพธ์ของรายงานจะละเอียดและมีจำนวนข้อมูลในปริมาณมาก จึงไม่สามารถแสดงผลรายงานผ่านทางหน้าจอนี้ได้</p></blockquote></div>
</div>

</div><!--/ Container -->
<script>
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

function doExport()
{
	var role 	= $("#saleType").val();
	var from 	= $("#fromDate").val();
	var to		= $("#toDate").val();
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง");  return false; }
	var token	= new Date().getTime();
	var url 		= "report/reportController/sponsorAnalyzController.php?sponsorProductDeepAnalyz&from="+from+"&to="+to+"&token="+token;
	get_download(token);
	window.location.href = url;
}

</script>