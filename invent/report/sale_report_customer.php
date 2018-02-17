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
			<label class="display-block">พนักงานขาย</label>
			<div class="btn-group width-100">
				<button type="button" class="btn btn-sm btn-primary width-50" id="btn-all" onclick="toggleOption(0)">ทั้งหมด</button>
				<button type="button" class="btn btn-sm width-50" id="btn-some" onclick="toggleOption(1)">บางคน</button>
			</div>
		</div>

		<input type="hidden" id="option" value="0" />

	</div><!--/row-->




	<div class="modal fade" id="rangeModal" tabindex="-1" role="dialog" aria-labelledby="rangeModal" aria-hidden="true">
		<div class="modal-dialog" style="width:800px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">เลือกพนักงานขาย</h4>
				</div>
				<div class="modal-body">
					<from id="saleForm">
						<table class="table table-bordered">
						<?php
						$qs  = dbQuery("SELECT s.id_sale, e.first_name, e.last_name FROM tbl_sale AS s JOIN tbl_employee AS e USING(id_employee) ORDER BY first_name ASC");
						$row = dbNumRows($qs);
						if( $row > 0 )
						{
							$i = 1;
							$sc = '';
							while($rs = dbFetchObject($qs))
							{
								$sc .= $i == 1 ? '<tr>' : '';
								$sc .= '<td class="width-10 text-center">';
								$sc .= '<input type="checkbox" class="sale-check" id="'.$rs->id_sale.'" name="sale['.$rs->id_sale.']" value="'.$rs->id_sale.'" />';
								$sc .= '</td>';
								$sc .= '<td class="width-40">';
								$sc .= '<label for="'.$rs->id_sale.'">'.$rs->first_name.' '.$rs->last_name.'</label>';
								$sc .= '</td>';
								$sc .= $i == 2 ? '</tr>' : '';

								$i++;
								$i = $i > 2 ? 1 : $i;
							}

							if($row%2 > 0 )
							{
								$sc .= '<td colspan="2"></td>';
							}

							echo $sc;
						}
						?>
						</table>
					</form>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">ตกลง</button>
				</div>
			</div>

		</div>
	</div>

</div> <!---/ container --->
<script src="script/report/sale_report_customer.js"></script>
