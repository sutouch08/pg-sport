<?php
	$id_tab = 66;
  $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
  accessDeny($view);
	include 'function/warehouse_helper.php';
	?>

<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-tasks"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">

      </p>
    </div>
  </div>
  <hr/>

<?php
$reference = getFilter('reference', 'reference', '');
$pdCode = getFilter('pdCode', 'pdCode', '');
$whCode = getFilter('whCode', 'whCode', '');
$zoneCode = getFilter('zoneCode', 'zoneCode', '');
$fromDate = getFilter('fromDate', 'fromDate', '');
$toDate = getFilter('toDate', 'toDate', '');
 ?>
<form id="searchForm" method="post">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search-box" name="reference" value="<?php echo $reference; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm search-box" name="pdCode" value="<?php echo $pdCode; ?>" />
  </div>
	<div class="col-sm-2 padding-5">
    <label>คลัง</label>
    <input type="text" class="form-control input-sm search-box" name="whCode" value="<?php echo $whCode; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search-box" name="zoneCode" value="<?php echo $zoneCode; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block">วันที่</label>
    <input type="text" class="form-control input-sm text-center input-discount" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" />
    <input type="text" class="form-control input-sm text-center input-unit" name="toDate" id="toDate" value="<?php echo $toDate; ?>" />
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">reset</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()">เคลียร์ตัวกรอง</button>
  </div>
</div>
</form>
<hr class="margin-top-15 margin-bottom-15" />
<?php

$length = 0;
$where = "WHERE s.reference != '' ";

if( $reference != '')
{
  createCookie('reference', $reference);
  $where .= "AND s.reference LIKE '%".$reference."%' ";
	$length++;
}

if($pdCode != '')
{
  createCookie('pdCode', $pdCode);
  $where .= "AND p.reference LIKE '%".$pdCode."%' ";
	$length++;
}

if($whCode != '')
{
	createCookie('whCode', $whCode);
	$where .= "AND z.id_warehouse IN(".getWarehouseIn($whCode).") ";
}


if($zoneCode != '')
{
  createCookie('zoneCode', $zoneCode);
  $where .= "AND (z.barcode_zone LIKE '%".$zoneCode."%' OR z.zone_name LIKE '%".$zoneCode."%') ";
	$length++;
}

if($fromDate != '' && $toDate != '')
{
  createCookie('fromDate', $fromDate);
  createCookie('toDate', $toDate);
  $where .= "AND s.date_upd >= '".fromDate($fromDate)."' ";
  $where .= "AND s.date_upd <= '".toDate($toDate)."' ";
	$length++;
}

$where .= "ORDER BY s.date_upd DESC, s.reference ASC, p.reference ASC, move_in ASC";

if($length == 0)
{
	$where = "WHERE s.reference = ''";
}



$table = "tbl_stock_movement AS s JOIN tbl_zone AS z ON s.id_zone = z.id_zone ";
$table .= "JOIN tbl_product_attribute AS p ON s.id_product_attribute = p.id_product_attribute ";
$qr  = "SELECT z.zone_name, p.reference AS pdCode, s.reference, s.move_in, s.move_out, s.date_upd FROM ";
$qr .= $table;


$paginator	= new paginator();
$get_rows	= get_rows();
$paginator->Per_Page($table, $where, $get_rows);
$paginator->display($get_rows, 'index.php?content=stock_movement');
$qs = dbQuery($qr. $where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);

$move_in = 0;
$move_out = 0;

$qa = dbQuery("SELECT SUM(move_in), SUM(move_out) FROM ".$table . $where);
if(dbNumRows($qa) == 1)
{
	list($move_in, $move_out) = dbFetchArray($qa);
}
?>

<table class="table table-striped table-bordered">
  <tr>
    <th class="width-5 text-center">ลำดับ</th>
    <th class="width-15 text-center">วันที่</th>
    <th class="width-10 text-center">เลขที่เอกสาร</th>
    <th class="width-30 text-center">สินค้า</th>
    <th class="width-8 text-center">เข้า</th>
    <th class="width-8 text-center">ออก</th>
    <th class="text-center">โซน</th>
  </tr>
  <tbody>
<?php if( dbNumRows($qs) > 0) : ?>
<?php  $no = row_no(); ?>
<?php  while($rs = dbFetchObject($qs)) : ?>
  <tr class="font-size-12">
    <td class="text-center"><?php echo $no; ?></td>
    <td class="text-center"><?php echo thaiDateTime($rs->date_upd); ?></td>
    <td class="text-center"><?php echo $rs->reference; ?></td>
    <td><?php echo $rs->pdCode; ?></td>
    <td class="text-center"><?php echo ac_format($rs->move_in); ?></td>
    <td class="text-center"><?php echo ac_format($rs->move_out); ?></td>
    <td class=""><?php echo $rs->zone_name; ?></td>
  </tr>
<?php  $no++; ?>
<?php endwhile; ?>
	<tr class="font-size-12">
		<td colspan="4" class="text-right">รวม</td>
		<td class="text-center"><?php echo ac_format($move_in); ?></td>
    <td class="text-center"><?php echo ac_format($move_out); ?></td>
    <td class="">ยอดต่าง(เข้า - ออก) :  <?php echo  ac_format($move_in - $move_out); ?></td>
	</tr>
<?php else : ?>
  <tr>
    <td colspan="7" class="text-center">--- ไม่พบข้อมูล ---</td>
  </tr>
<?php endif; ?>
  </tbody>
</table>

</div><!--- container --->

<script src="script/stock_movement.js"></script>
