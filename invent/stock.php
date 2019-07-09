<?php
	$id_tab = 67;
  $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
  accessDeny($view);
	?>

<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-tasks"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<?php if($delete) : ?>
					<!--<button type="button" class="btn btn-sm btn-success" onclick="addStock()">เพิ่มสต็อก</button> -->
				<?php endif; ?>
      </p>
    </div>
  </div>
  <hr/>

<?php
$pdCode = getFilter('pdCode', 'pdCode', '');
$zoneCode = getFilter('zoneCode', 'zoneCode', '');
 ?>
<form id="searchForm" method="post">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm search-box" name="pdCode" value="<?php echo $pdCode; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search-box" name="zoneCode" value="<?php echo $zoneCode; ?>" />
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
$where = "WHERE s.id_product_attribute != '' ";

if($pdCode != '')
{
  createCookie('pdCode', $pdCode);
  $where .= "AND p.reference LIKE '%".$pdCode."%' ";
	$length++;
}

if($zoneCode != '')
{
  createCookie('zoneCode', $zoneCode);
  $where .= "AND (z.barcode_zone LIKE '%".$zoneCode."%' OR z.zone_name LIKE '%".$zoneCode."%') ";
	$length++;
}

$where .= "ORDER BY p.date_upd DESC";

if($length == 0)
{
	$where = "WHERE s.id_product_attribute = ''";
}



$table = "tbl_stock AS s JOIN tbl_zone AS z ON s.id_zone = z.id_zone ";
$table .= "JOIN tbl_product_attribute AS p ON s.id_product_attribute = p.id_product_attribute ";
$qr  = "SELECT s.id_stock, z.zone_name, p.reference, s.qty, s.date_upd FROM ";
$qr .= $table;

$paginator	= new paginator();
$get_rows	= get_rows();
$paginator->Per_Page($table, $where, $get_rows);
$paginator->display($get_rows, 'index.php?content=stock');
$qs = dbQuery($qr. $where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);

?>

<table class="table table-striped table-bordered">
  <tr>
    <th class="width-5 text-center">ลำดับ</th>
    <th class="width-20 text-center">สินค้า</th>
		<th class="width-40">โซน</th>
    <th class="width-8 text-center">คงเหลือ</th>
    <th class="width-15 text-center">ปรับปรุง</th>
		<?php if($delete) : ?>
		<th class="width-10 text-center"></th>
		<?php endif; ?>
  </tr>
  <tbody>
<?php if( dbNumRows($qs) > 0) : ?>
<?php  $no = row_no(); ?>
<?php  while($rs = dbFetchObject($qs)) : ?>
  <tr class="font-size-12" id="row-<?php echo $rs->id_stock; ?>">
    <td class="text-center"><?php echo $no; ?></td>
    <td><?php echo $rs->reference; ?></td>
		<td class=""><?php echo $rs->zone_name; ?></td>
    <td class="text-center">
			<?php if($delete) : ?>
			<input type="number" class="form-control input-xs text-center edit-qty" id="qty-<?php echo $rs->id_stock; ?>" value="<?php echo $rs->qty; ?>" disabled />
			<?php else : ?>
				<?php echo number($rs->qty); ?>
			<?php endif; ?>

		</td>
		<td class="text-center"><?php echo thaiDateTime($rs->date_upd); ?></td>
		<?php if($delete) : ?>
		<td class="text-center">
			<button type="button" class="btn btn-xs btn-warning" id="btn-edit-<?php echo $rs->id_stock; ?>" onclick="editStock(<?php echo $rs->id_stock; ?>)"><i class="fa fa-pencil"></i></button>
			<button type="button" class="btn btn-xs btn-success hide" id="btn-update-<?php echo $rs->id_stock; ?>" onclick="updateStock(<?php echo $rs->id_stock; ?>)"><i class="fa fa-save"></i></button>
			<button type="button" class="btn btn-xs btn-danger" onclick="deleteStock(<?php echo $rs->id_stock; ?>)"><i class="fa fa-trash"></i></button>
		</td>
		<?php endif; ?>
  </tr>
<?php  $no++; ?>
<?php endwhile; ?>
<?php else : ?>
  <tr>
    <td colspan="6" class="text-center">--- ไม่พบข้อมูล ---</td>
  </tr>
<?php endif; ?>
  </tbody>
</table>


<?php if($delete) : ?>

	<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" id="modal" style="width:300px;">
			<div class="modal-content">
	  			<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal_title">เพิ่มสต็อกสินค้า</h4>
	         <input type="hidden" id="id_product" value="" />
					 <input type="hidden" id="id_zone" value="" />
				 </div>
				 <div class="modal-body" id="modal_body">
					 <div class="row">
					 	<div class="col-sm-12">
					 		<label>รหัสสินค้า</label>
							<input type="text" class="form-control input-sm text-center" id="pd-code" />
							<span class="help-block red not-show" id="pd-error">สินค้าไม่ถูกต้อง</span>
					 	</div>
						<div class="col-sm-12">
							<label>โซน</label>
							<input type="text" class="form-control input-sm text-center" id="zone-code" />
							<span class="help-block red not-show" id="zone-error">โซนไม่ถูกต้อง</span>
						</div>
						<div class="col-sm-12">
							<label>จำนวน</label>
							<input type="number" class="form-control input-sm text-center" id="add-qty" />
							<span class="help-block red not-show" id="qty-error">จำนวนไม่ถูกต้อง</span>
						</div>
					 </div>
				 </div>
				 <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
					<button type="button" class="btn btn-primary" id="add-btn" onClick="addToStock()" >เพิ่มในโซน</button>
				 </div>
			</div>
		</div>
	</div>
<?php endif; ?>
</div><!--- container --->

<script src="script/stock.js?token=<?php echo date('Ymd'); ?>"></script>
