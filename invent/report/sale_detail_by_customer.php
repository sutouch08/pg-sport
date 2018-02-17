<div class="container">
	<div class="row top-row">
		<div class="col-sm-6 top-col">
			<h4 class="title"><i class="fa fa-bar-chart"></i> <?php echo $pageTitle; ?></h4>
		</div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
			</p>
		</div>
	</div>
	<hr />

	<div class="row">
		<div class="col-sm-3">
			<label class="display-block">วันที่</label>
			<input type="text" class="form-control input-sm input-discount text-center" id="fromDate" placeholder="เริ่มต้น" />
			<input type="text" class="form-control input-sm input-unit text-center" id="toDate" placeholder="สิ้นสุด" />
		</div>

		<div class="col-sm-2">
			<label class="display-block">ลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" id="customer" />
		</div>

		<input type="hidden" id="option" value="0" />
		<input type="hidden" id="id_customer" value="" />

	</div><!--/row-->
	<hr/>
	<div class="row">
		<div class="col-sm-12" id="result">
			<!--- พื้นที่แสดงผลรายงาน --->


		</div>
	</div>





<script id="report-template" type="text/x-handlebars-template">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th colspan="4" class="text-center">รายงานสินค้าแยกตามลูกค้า วันที่ {{ fromDate}} ถึง {{ toDate }} </th>
			</tr>

			<tr>
				<th class="width-10 text-center">ลำดับ</th>
				<th class="width-35 text-center">ลูกค้า</th>
				<th class="width-35 text-center">สินค้า</th>
				<th class="width-15 text-center">จำนวน</th>
			</tr>
		</thead>
		<tbody>
			{{#each data}}
				{{#if @last}}
					<tr>
						<td class="text-right" colspan="3">รวม</td>
						<td class="text-center">{{total_qty}}</td>
					</tr>
				{{else}}
					<tr>
						<td class="text-center">{{no}}</td>
						<td>{{customerName}}</td>
						<td>{{product}}</td>
						<td class="text-center">{{qty}}</td>
					</tr>
				{{/if}}
			{{/each}}
		</tbody>
	</table>
</script>


</div> <!---/ container --->
<script src="script/report/sale_detail_by_customer.js"></script>
