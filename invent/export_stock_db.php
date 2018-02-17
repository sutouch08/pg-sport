
<div class="container">
	<div class="row top-row">
		<div class="col-sm-6 top-col">
			<h4 class="title"><i class="fa fa-cloud-download"></i> <?php echo $pageTitle; ?></h4>
		</div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-info" onclick="exportStockZone()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
			</p>
		</div>
	</div>

<hr />

</div>   <!-- End container -->
<script>

function exportStockZone()
{
	var token = new Date().getTime();
	get_download(token);
	window.location.href = "controller/exportController.php?exportStockZone&token="+token;
}
</script>
