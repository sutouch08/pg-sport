<?php
$rd = new receive_product($_GET['id_receive_product']);
$product = new product();
$zone = new zone();
$qs = $rd->getDetails($rd->id_receive_product);
?>
<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <button type="button" class="btn btn-sm btn-info" onclick="printReceived(<?php echo $rd->id_receive_product; ?>)"><i class="fa fa-print"></i> พิมพ์</button>
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
      <table class="table table-striped table-bordered">
        <thead>
          <th class="width-5 text-center">No.</th>
          <th class="width-15 text-center">รหัส</th>
          <th class="text-center">สินค้า</th>
          <th class="width-20 text-center">โซน</th>
          <th class="width-10 text-center">จำนวน</th>
          <th class="width-5 text-center">สถานะ</th>
        </thead>
        <tbody>
          <?php  if(dbNumRows($qs) > 0) : ?>
            <?php $no = 1; ?>
            <?php $total = 0; ?>
            <?php while($rs = dbFetchObject($qs)) : ?>
              <?php $pd = $product->getDetail($rs->id_product_attribute); ?>
            <tr>
              <td class="text-center middle"><?php echo $no; ?></td>
              <td class="middle"><?php echo $pd->reference; ?></td>
              <td class="middle"><?php echo $product->product_name($pd->id_product); ?></td>
              <td class="middle text-center"><?php echo $zone->getName($rs->id_zone);  ?></td>
              <td class="middle text-center"><?php echo number($rs->qty); ?></td>
              <td class="middle text-center"><?php echo $rs->status == 1 ? '<i class="fa fa-check green"></i>' : ''; ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total += $rs->qty; ?>
            <?php endwhile; ?>
            <tr>
              <td colspan="4" class="text-right">รวม</td>
              <td class="middle text-center"><strong><?php echo number($total); ?></strong></td>
              <td></td>
            </tr>
          <?php else : ?>
            <tr id="pre_label"><td align='center' colspan='7'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div><!--/ container -->
