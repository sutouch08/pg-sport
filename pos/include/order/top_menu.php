<?php $pl = $order->getPauseList(); ?>
<?php $countPaused = dbNumRows($pl); ?>
<div class="row" style="margin-top:-15px;">
  <div class="col-sm-6">
    <button type="button" class="btn btn-lg btn-primary" onclick="newBill()">
      <i class="fa fa-plus"></i> เพิ่มบิลขายใหม่
    </button>
    <?php if($order->id_order_pos != '') : ?>
    <button type="button" class="btn btn-lg btn-warning" id="btn-pause-bill" onclick="pauseBill()">
      <i class="fa fa-pause"></i> พักบิล
    </button>
    <?php endif; ?>
    <?php if($countPaused > 0) : ?>
    <button type="button" class="btn btn-lg btn-info" onclick="showPauseBill()">
      <i class="fa fa-file-text-o"></i> บิลที่พักไว้ <span class="badge"><?php echo $countPaused; ?></span>
    </button>
    <?php endif; ?>
    <!--
    <button type="button" class="btn btn-lg btn-danger" onclick="cancleBill()">
      <i class="fa fa-times"></i> ยกเลิกบิล
    </button>
  -->

  </div>
  <?php if($order->id_order_pos != '') : ?>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-lg btn-primary no-radius" disabled id="btn-print-bill" onclick="printBill()">
        <i class="fa fa-print"></i> พิมพ์บิล
      </button>
    </p>
  </div>
  <?php endif; ?>
</div>


<?php include 'include/order/pause_list.php'; ?>
