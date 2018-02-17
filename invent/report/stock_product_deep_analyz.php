<?php
	$pageName = 'รายงานวิเคราะห์สินค้าคงเหลือแบบละเอียด';
	$idTab = 51;  //----- รายงานผู้บริหาร
	$id_profile	= $_COOKIE['profile_id'];
	$pm	= checkAccess($id_profile, $idTab);
	$view = $pm['view'];
	accessDeny($view);
	include_once 'function/report_helper.php';
?>

<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-2 top-p"><input type="text" class="form-control input-sm text-center" id="discount" placeholder="ส่วนลดที่ให้ลูกค้า" autofocus="autofocus" /> </div>
    <div class="col-sm-4"><p class="pull-right top-p"><button class="btn btn-sm btn-success" onClick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button></p></div>
</div><!--/ row -->
<hr style="margin-bottom:35px;"/>
<div class="row">
	<div class="col-sm-12">
    <blockquote><p class="lead" style="color:#CCC;">เนื่องจากผลลัพธ์ของรายงานจะละเอียดและมีจำนวนข้อมูลในปริมาณมาก จึงไม่สามารถแสดงผลรายงานผ่านทางหน้าจอนี้ได้</p></blockquote></div>
</div>

</div><!--/ Container -->
<script>

function doExport()
{
	load_in();
	var token	= new Date().getTime();
	var disc		= $("#discount").val();
	var url 		= "report/reportController/stockReportController.php?stock_product_deep_analyz&export&discount="+disc+"&token="+token;
	get_download(token);
	window.location.href = url;
}

</script>