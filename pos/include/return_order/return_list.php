<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><i class="fa fa-download"></i> <?php echo $pageTitle; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    </p>
  </div>
</div>

<?php
$sCode = getFilter('sCode', 'sCode', '');
$sBill = getFilter('sBill', 'sBill', '');
$fromDate = getFilter('fromDate', 'fromDate', '');
$toDate = getFilter('toDate', 'toDate', '');
?>
<hr/>
<form id="searchForm" method="post">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center search-box" name="sCode" id="sCode" value="<?php echo $sCode; ?>" autofocus />
  </div>
  <div class="col-sm-2 padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center search-box" name="sBill" id="sBill" value="<?php echo $sBill; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block">วันที่</label>
    <input type="text" class="form-control input-sm input-discount text-center" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" />
    <input type="text" class="form-control input-sm input-unit text-center" name="toDate" id="toDate" value="<?php echo $toDate; ?>" />
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">Search</label>
    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">Reset</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()">เคลียร์ตัวกรอง</button>
  </div>
</div>
</form>
<hr/>
<?php
$where = "WHERE id != '' ";

if($sCode != '')
{
  createCookie('sCode', $sCode);
  $where .= "AND reference LIKE '%".$sCode."%' ";
}

if($sBill != '')
{
  createCookie('sBill', $sBill);
  $where .= "AND order_code LIKE '%".$sBill."%' ";
}


if($fromDate != '' && $toDate != '')
{
  createCookie('fromDate', $fromDate);
  createCookie('toDate', $toDate);
  $where .= "AND date_add >= '".fromDate($fromDate)."' ";
  $where .= "AND date_add <= '".toDate($toDate)."' ";
}


$where .= "ORDER BY reference DESC";

$paginator = new paginator();
$get_rows = get_rows();
$paginator->Per_Page('tbl_return_order', $where, $get_rows);

$qs = dbQuery("SELECT * FROM tbl_return_order ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);

 ?>
 <div class="row">
 	<div class="col-sm-8 padding-5 first">
 		<?php $paginator->display($get_rows, 'index.php?content=return_order'); ?>
 	</div>
  <div class="col-sm-4 margin-top-15">
      <p class="pull-right">
        <span class="">ว่างๆ</span><span> = ปกติ, </span>
        <span class="red">CN</span><span> = ยกเลิก </span>
    </p>
  </div>
 </div>

<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr class="font-size-12">
          <th class="width-5 text-center">No.</th>
          <th class="width-10 text-center">วันที่</th>
          <th class="width-15 text-center">เลขที่เอกสาร</th>
          <th class="width-15 text-center">เลขที่บิล</th>
          <th class="width-15 text-center">ลูกค้า</th>
          <th class="width-15 text-center">พนักงาน</th>
          <th class="width-10 text-center">จำนวน</th>
          <th class="width-5 text-center">สถานะ</th>
          <th class=""></th>
        </tr>
      </thead>
      <tbody>
<?php if(dbNumRows($qs) > 0) : ?>
  <?php $no = row_no(); ?>
  <?php $ro = new return_order(); ?>
  <?php $customer = new customer(); ?>
  <?php $emp = new employee(); ?>
  <?php while($rs = dbFetchObject($qs)) : ?>
        <tr class="font-size-12">
          <td class="middle text-center"><?php echo number($no); ?></td>
          <td class="middle text-center"><?php echo thaiDate($rs->date_add); ?></td>
          <td class="middle text-center"><?php echo $rs->reference; ?></td>
          <td class="middle text-center"><?php echo $rs->order_code; ?></td>
          <td class="middle text-center"><?php echo $customer->getName($rs->id_customer); ?></td>
          <td class="middle text-center"><?php echo $emp->getName($rs->id_employee); ?></td>
          <td class="middle text-center"><?php echo number($ro->getSumQty($rs->id)); ?></td>
          <td class="middle text-center red" id="td-<?php echo $rs->id; ?>">
            <?php echo ($rs->isCancle == 1 ? 'CN' : '' ); ?>
          </td>
          <td class="middle text-right">
            <button type="button" class="btn btn-sm btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
              <i class="fa fa-eye"></i>
            </button>
            <?php if($delete && $rs->isCancle == 0) : ?>
              <button type="button" class="btn btn-sm btn-danger" id="btn-del-<?php echo $rs->id; ?>" onclick="cancleReturn('<?php echo $rs->id; ?>', '<?php echo $rs->reference; ?>')">
                <i class="fa fa-times"></i>
              </buton>
            <?php endif; ?>
          </td>
        </tr>
    <?php $no++; ?>
  <?php endwhile; ?>
<?php else : ?>
        <tr>
          <td colspan="8" class="text-center">
            <h4>ไม่พบรายการ</h4>
          </td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
