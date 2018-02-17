<?php

	/* Dash Board รายงานยอดขาย */

	$page_name = "สรุปยอดขาย แยกตามพื้นที่";
	$id_profile = $_COOKIE['profile_id'];
	$id_user= $_COOKIE['user_id'];
	$today = date('Y-m-d');
	$this_month = date("m",strtotime("this month"));
	$last_month = date("m",strtotime("-1 month"));
	$last_year = date("Y",strtotime("-1 year"))+543;
	$rang = getMonth();
	$from = $rang['from'];
	$to = $rang['to'];
	$employee = new employee($id_user);
	$sale = new sale();

	include 'function/vat_helper.php';

	function posColor($n){
		$i = 10-$n;
		switch($i){
			case 9 :
			$class = "#4FC1E9";
			break;
			case 8 :
			$class = "#48CFAD";
			break;
			case 7 :
			$class = "#A0D468";
			break;
			case 6 :
			$class = "#FFCE54";
			break;
			case 5 :
			$class = "#FC6E51";
			break;
			default :
			$class = "#DA4453";
			break;
		}
		return $class;
	}

	?>

<div class="container">
	<div class="row top-row">
		<div class="col-sm-12">
			<h4 class="title">สรุปยอดขาย แยกตามพื้นที่</h4>
		</div>
	</div>
<hr />
<!-- End page place holder -->
<div class="row">
	<div class="col-sm-3 col-xs-12 padding-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="white margin-top-5 margin-bottom-0 text-center">วันนี้</h4>
			</div>
			<div class="panel-body">
				<div class="row" style="margin-top:-15px; margin-bottom:-15px;">
					<table class="table" style="margin-bottom:0px;">
<?php $ds = $sale->LeaderGroup('today'); ?>
<?php $n = 1; ?>
<?php foreach ($ds as $key => $value) : ?>
					<tr style="background-color: <?php echo posColor($n); ?>; color:#FFF; font-size:12px;">
						<td class="width-15 text-center" style="border-top:0px;"><?php echo $n; ?></td>
						<td class="width-50" style="border-top:0px;"><?php echo $value['group_name']; ?></td>
						<td class="width-35 text-right" style="border-top:0px;"><?php echo number_format(removeVAT($value['amount']), 2); ?></td>
					</tr>
<?php 	$n++; ?>
<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-3 col-xs-12 padding-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="white margin-top-5 margin-bottom-0 text-center">สับดาห์นี้</h4>
			</div>
			<div class="panel-body">
				<div class="row" style="margin-top:-15px; margin-bottom:-15px;">
					<table class="table" style="margin-bottom:0px;">
<?php $ds = $sale->LeaderGroup('this_week'); ?>
<?php $n = 1; ?>
<?php foreach ($ds as $key => $value) : ?>
					<tr style="background-color: <?php echo posColor($n); ?>; color:#FFF; font-size:12px;">
						<td class="width-15 text-center" style="border-top:0px;"><?php echo $n; ?></td>
						<td class="width-50" style="border-top:0px;"><?php echo $value['group_name']; ?></td>
						<td class="width-35 text-right" style="border-top:0px;"><?php echo number_format(removeVAT($value['amount']), 2); ?></td>
					</tr>
<?php 	$n++; ?>
<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-3 col-xs-12 padding-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="white margin-top-5 margin-bottom-0 text-center">เดือนนี้</h4>
			</div>
			<div class="panel-body">
				<div class="row" style="margin-top:-15px; margin-bottom:-15px;">
					<table class="table" style="margin-bottom:0px;">
<?php $ds = $sale->LeaderGroup('this_month'); ?>
<?php $n = 1; ?>
<?php foreach ($ds as $key => $value) : ?>
					<tr style="background-color: <?php echo posColor($n); ?>; color:#FFF; font-size:12px;">
						<td class="width-15 text-center" style="border-top:0px;"><?php echo $n; ?></td>
						<td class="width-50" style="border-top:0px;"><?php echo $value['group_name']; ?></td>
						<td class="width-35 text-right" style="border-top:0px;"><?php echo number_format(removeVAT($value['amount']), 2); ?></td>
					</tr>
<?php 	$n++; ?>
<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-3 col-xs-12 padding-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="white margin-top-5 margin-bottom-0 text-center">ปีนี้</h4>
			</div>
			<div class="panel-body">
				<div class="row" style="margin-top:-15px; margin-bottom:-15px;">
					<table class="table" style="margin-bottom:0px;">
<?php $ds = $sale->LeaderGroup('this_year'); ?>
<?php $n = 1; ?>
<?php foreach ($ds as $key => $value) : ?>
					<tr style="background-color: <?php echo posColor($n); ?>; color:#FFF; font-size:12px;">
						<td class="width-15 text-center" style="border-top:0px;"><?php echo $n; ?></td>
						<td class="width-50" style="border-top:0px;"><?php echo $value['group_name']; ?></td>
						<td class="width-35 text-right" style="border-top:0px;"><?php echo number_format(removeVAT($value['amount']), 2); ?></td>
					</tr>
<?php 	$n++; ?>
<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

</div><!-- container -->
