<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-tasks"></i>  <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </div>
  </div>
  <hr/>
  <div class="row">
    <div class="col-sm-2 padding-5 first">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm" disabled />
    </div>
    <div class="col-sm-1 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo date('d-m-Y'); ?>" />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ใบสั่งซื้อ</label>
      <input type="text" class="form-control input-sm text-center" id="po" value="" autofocus />
    </div>
    <div class="col-sm-2 padding-5">
      <label>ใบส่งสินค้า</label>
      <input type="text" class="form-control input-sm text-center" id="invoice" value="" />
    </div>
    <div class="col-sm-4 padding-5">
      <label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" id="remark" value="" />
    </div>
    <div class="col-sm-1 padding-5 last">
      <label class="display-block not-show">add</label>
      <button type="button" class="btn btn-sm btn-success btn-block" onclick="addNew()">เพิ่มเอกสาร</buttton>
    </div>
  </div>

  <input type="hidden" id="id_po" />

</div><!--/ container -->
<script src="script/receive_product/receive_new_add.js?token=<?php echo date('YmdH'); ?>"></script>
