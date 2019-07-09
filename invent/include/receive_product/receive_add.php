
<div class="container">
  <?php if($add) : ?>
  <?php
  $rd = new receive_product($_GET['id_receive_product']);
  $po = new po();
  $details = $rd->getDetails($rd->id_receive_product);
  $qs = dbNumRows($details) > 0 ? $details : $po->getPoBacklog($rd->id_po);

  //----ใช้ใน receive_control.php
  $zoneCode =  '';
  $zoneName =  '';
  $idZone =  '';
  $inputActive = '' ;
  $changeZoneActive = 'disabled';
  //-----ใช้ใน receive_control.php
  ?>
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php if($rd->status == 0) : ?>
          <button type="button" class="btn btn-sm btn-success" id="btn-save" disabled onclick="saveAdd()"><i class="fa fa-save"></i> บันทึก</button>
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
    <div class="col-sm-4 padding-5">
      <label class="display-block">หมายเหตุ</label>
      <input type="text" class="form-control input-sm text-center" id="remark" value="<?php echo $rd->remark; ?>" disabled />
    </div>

    <?php if($rd->status == 0) : ?>
    <div class="col-sm-1 padding-5 last">
      <label class="display-block not-show">แก้ไข</label>
      <button type="button" class="btn btn-sm btn-warning btn-block" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
    </div>
    <?php endif; ?>

    <input type="hidden" id="id_receive_product" value="<?php echo $rd->id_receive_product; ?>" />
    <input type="hidden" id="id_po" value="<?php echo $rd->id_po; ?>" />
  </div><!--/row-->

  <?php
  if($rd->status == 0)
  {
    include('include/receive_product/receive_control.php');
  }
  ?>

  <hr class="margin-top-15" />
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped table-bordered">
        <thead>
          <th class="width-5 text-center">No.</th>
          <th class="width-15 text-center">รหัส</th>
          <th class="text-center">สินค้า</th>
          <th class="width-10 text-center">รับ</th>
          <th class="width-5 text-center">Actions</th>
        </thead>
        <tbody>
          <?php  if(dbNumRows($qs) > 0) : ?>
            <?php $no = 1; ?>
            <?php while($rs = dbFetchObject($qs)) : ?>
              <?php $id_pa = $rs->id_product_attribute; ?>
              <?php $id_pd = $rs->id_product; ?>
              <?php $qty = isset($rs->received) ? (($rs->qty > $rs->received) ? $rs->qty - $rs->received : 0) : $rs->qty; ?>
              <?php $status = isset($rs->status) ? $rs->status : 0; ?>
            <tr id="row-<?php echo $id_pa; ?>">
              <td class="text-center middle no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->reference; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
              <td class="middle text-center">
                <?php if(isset($rs->status)) : ?>
                  <span><?php echo $qty; ?></span>
                <?php else : ?>
                <input type="number" class="form-control input-sm text-center receive-box" id="receive-<?php echo $id_pa; ?>" value="<?php echo $qty; ?>" />
                <span class="hide" id="label-<?php echo $id_pa; ?>"><?php echo $qty; ?></span>
                <input type="hidden" id="productId-<?php echo $id_pa; ?>" value="<?php echo $id_pd; ?>" />
                <?php endif; ?>
              </td>
              <td class="middle text-center">
                <?php if(!isset($rs->status)) : ?>
                  <button type="button" class="btn btn-sm btn-danger" id="btn-remove-<?php echo $id_pa; ?>" onclick="deleteRow('<?php echo $id_pa; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
            <?php endwhile; ?>
          <?php else : ?>
            <tr id="pre_label"><td align='center' colspan='7'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else : ?>
    <?php include('include/page_error.php'); ?>
  <?php endif; ?>
</div><!--/ container -->

<script src="script/receive_product/receive_add.js?token=<?php echo date('YmdH'); ?>"></script>
<script src="script/receive_product/receive_control.js?token=<?php echo date('YmdH'); ?>"></script>
