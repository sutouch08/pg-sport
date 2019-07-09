<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-tasks"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <?php if($add) : ?>
          <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <hr/>

<?php
$sCode    = getFilter('sCode', 'sCode' ,'');
$sInvoice = getFilter('sInvoice', 'sInvoice' ,'');
$sPo      = getFilter('sPo', 'sPo', '');
$sSup     = getFilter('sSup', 'sSup', '');
$fromDate = getFilter('fromDate', 'fromDate', '');
$toDate   = getFilter('toDate', 'toDate', '');
?>
<form id="searchForm" method="post">
  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label>เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" name="sCode" id="sCode" value="<?php echo $sCode; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ใบส่งสินค้า</label>
      <input type="text" class="form-control input-sm text-center" name="sInvoice" id="sInvoice" value="<?php echo $sInvoice; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ใบสั่งซื้อ</label>
      <input type="text" class="form-control input-sm text-center" name="sPo" id="sPo" value="<?php echo $sPo; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ผู้ขาย</label>
      <input type="text" class="form-control input-sm text-center" name="sSup" id="sSup" value="<?php echo $sSup; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label class="display-block">วันที่</label>
      <input type="text" class="form-control input-sm input-discount text-center" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" />
      <input type="text" class="form-control input-sm input-unit text-center" name="toDate" id="toDate" value="<?php echo $toDate; ?>" />
    </div>
    <div class="col-sm-1 padding-5">
      <label class="display-block not-show">apply</label>
      <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-sm-1 padding-5 last">
      <label class="display-block not-show">reset</label>
      <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> เคลียร์</button>
    </div>
  </div>
</form>

<?php
$where = "WHERE id_receive_product != 0 ";

createCookie('sCode', $sCode);
createCookie('sInvoice', $sInvoice);
createCookie('sPo', $sPo);
createCookie('sSup', $sSup);
createCookie('fromDate', $fromDate);
createCookie('toDate', $toDate);

if($sCode != "")
{
  $where .= "AND reference LIKE '%".$sCode."%' ";
}

if($sInvoice != "")
{
  $where .= "AND invoice LIKE '%".$sInvoice."%' ";
}

if($sPo != "")
{
  $where .= "AND po_reference LIKE '%".$sPo."%' ";
}

if($sSup != "")
{
  $supIn = getSuplierIn($sSup);
  $poIn = getPoInBySupplierIn($supIn);
  $where .= "AND id_po IN(".$poIn.") ";
}

if($fromDate !== "" && $toDate !== "")
{
  $where .= "AND date_add >= '".fromDate($fromDate)."' ";
  $where .= "AND date_add <= '".toDate($toDate)."' ";
}

$where .= "ORDER BY date_add DESC";


$paginator = new paginator();
$get_rows = get_rows();
$paginator->Per_Page("tbl_receive_product", $where, $get_rows);
$paginator->display($get_rows,"index.php?content=receive_product");
$Page_Start = $paginator->Page_Start;
$Per_Page = $paginator->Per_Page;
$qr  = "SELECT * FROM tbl_receive_product ".$where." LIMIT ".$Page_Start.", ".$Per_Page;
$qs = dbQuery($qr);
?>

<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 middle text-center">No.</th>
          <th class="width-10 middle">วันที่</th>
          <th class="width-15 middle">เลขที่เอกสาร</th>
          <th class="width-10 middle">ใบส่งสินค้า</th>
          <th class="width-10 middle">ใบสั่งซื้อ</th>
          <th class="width-15 middle">ผู้ขาย</th>
          <th class="width-10 middle text-right">มูลค่า</th>
          <th class="width-10 middle text-center">สถานะ</th>
          <th class="middle text-right"></th>
        </tr>
      </thead>
      <tbody>
    <?php if(dbNumRows($qs) > 0) : ?>
    <?php   $no = row_no(); ?>
    <?php   $cs = new receive_product(); ?>
    <?php   while($rs = dbFetchObject($qs)) : ?>
      <tr class="font-size-12" id="row-<?php echo $rs->id_receive_product; ?>">
        <td class="middle text-center"><?php echo $no; ?></td>
        <td class="middle"><?php echo thaiDate($rs->date_add); ?></td>
        <td class="middle"><?php echo $rs->reference; ?></td>
        <td class="middle"><?php echo $rs->invoice; ?></td>
        <td class="middle"><?php echo $rs->po_reference; ?></td>
        <td class="middle"><?php echo getSupplierNameByPoId($rs->id_po); ?></td>
        <td class="middle text-right"><?php echo $cs->total_amount($rs->id_receive_product); ?></td>
        <td class="middle text-center">
          <?php if($rs->status != 1) : ?>
            <span class="red">ยังไม่บันทึก</span>
          <?php endif; ?>
        </td>
        <td class="middle text-right">
        <?php if($rs->status == 1) : ?>
          <button type="button" class="btn btn-xs btn-primary" onclick="printReceived(<?php echo $rs->id_receive_product; ?>)"><i class="fa fa-print"></i></button>
        <?php endif; ?>
          <button type="button" class="btn btn-xs btn-info" onclick="viewDetail(<?php echo $rs->id_receive_product; ?>)"><i class="fa fa-eye"></i></button>
        <?php if($edit) : ?>
          <button type="button" class="btn btn-xs btn-warning" onclick="goEdit(<?php echo $rs->id_receive_product; ?>)"><i class="fa fa-pencil"></i></button>
        <?php endif; ?>
        <?php if($delete) : ?>
          <button type="button" class="btn btn-xs btn-danger" onclick="getDelete('<?php echo $rs->id_receive_product; ?>','<?php echo $rs->reference; ?>')">
            <i class="fa fa-trash"></i>
          </button>
        <?php endif; ?>
        </td>
      </tr>
      <?php $no++; ?>
    <?php endwhile; ?>
    <?php else : ?>
      <tr>
        <td colspan="8" class="middle text-center">---- No content ----</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


</div><!--/ container -->
<script>
function clearFilter()
{
	$.ajax({
		url:"controller/receiveProductController.php?clear_filter",
		type:"GET",
    cache: "false",
    success: function(rs){
			window.location.href="index.php?content=receive_product";
		}
	});
}
</script>
