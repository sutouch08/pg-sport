<?php
$id = $_GET['id_return_order'];
$cs = new return_order($id);
$emp = new employee($cs->id_employee);
$cus = new customer($cs->id_customer);
$qs = $cs->getDetails($id);
?>

<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><i class="fa fa-download"></i> <?php echo $pageTitle; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()">
        <i class="fa fa-arrow-left"></i> กลับ
      </button>
    </p>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thaiDate($cs->date_add) ?>" disabled />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $cs->order_code; ?>" disabled />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $cs->reference; ?>" disabled />
  </div>
  <div class="col-sm-2 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $cus->first_name.' '.$cus->last_name; ?>" disabled />
  </div>
  <div class="col-sm-2 padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $emp->first_name; ?>" disabled />
  </div>
  <div class="col-sm-12">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" value="<?php echo $cs->remark; ?>" disabled />
  </div>


  <input type="hidden" id="id_order" />
  <input type="hidden" id="id_customer" />
</div>
<hr />
<div class="row">
  <div class="col-sm-12" id="result">
    <table class="table table-striped border-1">
      <thead>
        <tr class="font-size-12">
          <th class="width-5 text-center">No.</th>
          <th class="">สินค้า</th>
          <th class="width-10 text-center">ราคา</th>
          <th class="width-10 text-center">จำนวนคืน</th>
          <th class="width-15 text-right">มูลค่าที่คืน</th>
        </tr>
      </thead>
      <tbody>
<?php if(dbNumRows($qs) > 0 ) : ?>
  <?php $no = 1; ?>
  <?php $totalQty = 0; ?>
  <?php $totalAmount = 0; ?>
  <?php $product = new product(); ?>
  <?php while($rs = dbFetchObject($qs)) : ?>
        <tr class="font-size-12">
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle text-center"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-center"><?php echo number($rs->qty); ?></td>
          <td class="middle text-right"><?php echo number($rs->amount, 2); ?> </td>
        </tr>
        <?php $totalQty += $rs->qty; ?>
        <?php $totalAmount += $rs->amount; ?>
        <?php $no++; ?>
      <?php endwhile; ?>
      <tr>
        <td colspan="3" class="text-right">รวม</td>
        <td class="text-center"><?php echo number($totalQty); ?></td>
        <td class="text-right"><?php echo number($totalAmount, 2); ?></td>
      </tr>
<?php endif; ?>
  </div>
</div>
