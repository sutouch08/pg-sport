
<div class="container">
  <?php if($edit) : ?>
  <?php
  $rd = new receive_product($_GET['id_receive_product']);
  $po = new po();

  if(isset($_GET['id_zone']))
  {
    $zone = new zone($_GET['id_zone']);
  }

  //----ใช้ใน receive_control.php
  $zoneCode = isset($zone) ? $zone->barcode : '';
  $zoneName = isset($zone) ? $zone->zone_name : '';
  $idZone = isset($zone) ? $zone->id_zone : '';
  $inputActive = isset($zone) ? 'disabled' : '' ;
  $changeZoneActive = isset($zone) ? '' : 'disabled';
  //-----ใช้ใน receive_control.php

  $qs = $rd->getDetails($rd->id_receive_product);
  ?>
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php if($rd->status == 0) : ?>
          <button type="button" class="btn btn-sm btn-success" id="btn-save" <?php echo $changeZoneActive; ?> onclick="saveAdd()">ตรวจสอบ</button>
        <?php else : ?>
          <button type="button" class="btn btn-sm btn-danger" id="btn-unsave" onclick="unSave()">ยกเลิกการบันทึก</button>
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


    <div class="col-sm-1 padding-5 last">
      <label class="display-block not-show">แก้ไข</label>
      <button type="button" class="btn btn-sm btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
      <button type="button" class="btn btn-sm btn-success btn-block hide" id="btn-update" onclick="updateHeader()"><i class="fa fa-save"></i> Update</button>
    </div>


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
                  <span class="received-item"><?php echo $qty; ?></span>
                <?php else : ?>
                <input type="number" class="form-control input-sm text-center receive-box" id="receive-<?php echo $id_pa; ?>" value="<?php echo $qty; ?>" />
                <span class="hide" id="label-<?php echo $id_pa; ?>"><?php echo $qty; ?></span>
                <input type="hidden" id="productId-<?php echo $id_pa; ?>" value="<?php echo $id_pd; ?>" />
                <?php endif; ?>
              </td>
              <td class="middle text-center">
                <?php if(isset($rs->status) && $rs->status == 0) : ?>
                  <button type="button" class="btn btn-sm btn-danger" id="btn-remove-<?php echo $id_pa; ?>" onclick="cancleReceiveItem('<?php echo $id_pa; ?>')">
                    <i class="fa fa-trash"></i>
                  </button>
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
<script>
function getEdit(){
  $('#invoice').removeAttr('disabled');
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function updateHeader(){
  var id = $('#id_receive_product').val();
  var invoice = $('#invoice').val();
  var remark = $('#remark').val();

  load_in();
  $.ajax({
    url:'controller/receiveProductController.php?updateHeader',
    type:'POST',
    cache:false,
    data:{
      'id_receive_product' : id,
      'invoice' : invoice,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        $('#invoice').attr('disabled', 'disabled');
        $('#remark').attr('disabled', 'disabled');
        $('#btn-edit').removeClass('hide');
        $('#btn-update').addClass('hide');

        swal({
          title:'Updated',
          text:'Uddate successfull',
          type:'success',
          timer:1000
        });

      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
</script>
<script src="script/receive_product/receive_add.js?token=<?php echo date('YmdH'); ?>"></script>
<script src="script/receive_product/receive_control.js?token=<?php echo date('YmdH'); ?>"></script>
