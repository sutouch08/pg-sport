<?php
$rd = new receive_product($_GET['id_receive_product']);
$id_zone = $_GET['id_zone'];
$product = new product();
$zone = new zone();
$qs = $rd->getDetails($rd->id_receive_product);
$po = new po();
?>
<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goEdit(<?php echo $rd->id_receive_product; ?>, <?php echo $id_zone; ?>)"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php if($add) : ?>
          <button type="button" class="btn btn-sm btn-success" onclick="saveReceived()"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <hr/>

  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label class="display-block">เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $rd->reference; ?>" disabled />
    </div>
    <div class="col-sm-1 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thaiDate($rd->date_add); ?>" disabled />
    </div>
    <div class="col-sm-2 padding-5">
      <label class="display-block">ใบสั่งซื้อ</label>
      <input type="text" class="form-control input-sm text-center" id="po" value="<?php echo $rd->po_reference; ?>" disabled />
    </div>
    <div class="col-sm-2 padding-5">
      <label class="display-block">ใบส่งสินค้า</label>
      <input type="text" class="form-control input-sm text-center" id="invoice" value="<?php echo $rd->invoice; ?>" disabled />
    </div>
    <div class="col-sm-5 padding-5 last">
      <label class="display-block">หมายเหตุ</label>
      <input type="text" class="form-control input-sm text-center" id="remark" value="<?php echo $rd->remark; ?>" disabled />
    </div>
    <input type="hidden" id="id_receive_product" value="<?php echo $rd->id_receive_product; ?>" />
    <input type="hidden" id="id_po" value="<?php echo $rd->id_po; ?>" />
  </div><!--/row-->

  <hr class="margin-top-15" />
  <div class="row">
    <div class="col-sm-12">
      <h4 class="text-center">** กรุณาตรวจสอบความถูกต้องก่อนบันทึก**</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped table-bordered">
        <thead>
          <th class="width-5 text-center">No.</th>
          <th class="width-15 text-center">รหัส</th>
          <th class="text-center">สินค้า</th>
          <th class="width-10 text-center">สั่งซื้อ</th>
          <th class="width-10 text-center">รับแล้ว</th>
          <th class="width-10 text-center">ครั้งนี้</th>
          <th class="width-10 text-center">รวมครั้งนี้</th>
          <th class="width-10 text-center">ขาด</th>
          <th class="width-10 text-center">เกิน</th>
        </thead>
        <tbody>
          <?php  if(dbNumRows($qs) > 0) : ?>
            <?php
                $no = 1;
                $totalPO = 0;
                $totalReceived = 0;
                $totalQty = 0;
                $totalSumReceived = 0;
                $totalOver = 0;
                $totalBacklog = 0;
            ?>
            <?php while($rs = dbFetchObject($qs)) : ?>
              <?php $pd = $product->getDetail($rs->id_product_attribute); ?>
              <?php $poItem = $po->getDetailByItem($rd->id_po, $rs->id_product_attribute); ?>
              <?php $sumReceive = $poItem->received + $rs->qty; ?>
              <?php $over = ($poItem->qty - $sumReceive) < 0 ? 0 : $poItem->qty - $sumReceive; ?>
              <?php $backlog = ($poItem->qty - $sumReceive) > 0 ? 0 : ($poItem->qty - $sumReceive)*-1; ?>
            <tr class="font-size-12">
              <td class="text-center middle"><?php echo $no; ?></td>
              <td class="middle"><?php echo $pd->reference; ?></td>
              <td class="middle"><?php echo $product->product_name($pd->id_product); ?></td>
              <td class="middle text-center"><?php echo number($poItem->qty);  ?></td>
              <td class="middle text-center"><?php echo number($poItem->received);  ?></td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
              <td class="middle text-center"><?php echo number($sumReceive);  ?></td>
              <td class="middle text-center"><?php echo number($over); ?></td>
              <td class="middle text-center"><?php echo number($backlog); ?></td>
            </tr>
            <?php
                $no++;
                $totalPO += $poItem->qty;
                $totalReceived += $poItem->received;
                $totalQty += $rs->qty;
                $totalSumReceived += $sumReceive;
                $totalOver += $over;
                $totalBacklog += $backlog;
            ?>
            <?php endwhile; ?>
            <tr>
              <td colspan="3" class="text-right">รวม</td>
              <td class="middle text-center"><strong><?php echo number($totalPO); ?></strong></td>
              <td class="middle text-center"><strong><?php echo number($totalReceived); ?></strong></td>
              <td class="middle text-center"><strong><?php echo number($totalQty); ?></strong></td>
              <td class="middle text-center"><strong><?php echo number($totalSumReceived); ?></strong></td>
              <td class="middle text-center"><strong><?php echo number($totalOver); ?></strong></td>
              <td class="middle text-center"><strong><?php echo number($totalBacklog); ?></strong></td>
            </tr>
          <?php else : ?>
            <tr id="pre_label"><td align='center' colspan='9'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div><!--/ container -->
<script src="script/receive_product/receive_review.js?token=<?php echo date('YmdH'); ?>"></script>
