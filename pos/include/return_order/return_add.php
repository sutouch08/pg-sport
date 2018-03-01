<?php $cs = new return_order(); ?>
<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><i class="fa fa-download"></i> <?php echo $pageTitle; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="leave()">
        <i class="fa fa-arrow-left"></i> กลับ
      </button>
      <button type="button" class="btn btn-sm btn-success" id="btn-save" onclick="save()" disabled>
        <i class="fa fa-save"></i> บันทึก
      </button>
    </p>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-sm-1 padding-5 first">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo date('d-m-Y'); ?>" />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center" id="txt-bill" placeholder="ค้นหาบิล" autofocus />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $cs->getNewReference(); ?>" disabled />
  </div>
  <div class="col-sm-2 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm" id="txt-customer" disabled />
  </div>
  <div class="col-sm-2 padding-5">
    <label>พนักงานขาย</label>
    <input type="text" class="form-control input-sm" id="txt-emp" disabled />
  </div>
  <div class="col-sm-2 padding-5">
    <label>วิธีการชำระเงิน</label>
    <input type="text" class="form-control input-sm" id="txt-payment-method" disabled />
  </div>

  <div class="col-sm-12">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" placeholder="ไม่เกิน 100 ตัวอักษร" id="remark" />
  </div>
  <input type="hidden" id="id_order" />
  <input type="hidden" id="id_customer" />
</div>
<hr />
<div class="row">
  <div class="col-sm-12" id="result">

  </div>
</div>

<script id="bill-template" type="text/x-handlebarsTemplate">
  <table class="table table-striped border-1">
    <thead>
      <tr class="font-size-12">
        <th class="width-5 text-center">No.</th>
        <th class="width-15 text-center">บาร์โค้ด</th>
        <th class="">สินค้า</th>
        <th class="width-10 text-center">ราคา</th>
        <th class="width-8 text-center">จำนวน</th>
        <th class="width-8 text-center">จำนวนคืน</th>
        <th class="width-10 text-right">มูลค่าที่คืน</th>
      </tr>
    </thead>
    <tbody>
      {{#each this}}
        {{#if nodata}}

        {{else}}
          {{#if @last}}
          <tr>
            <td colspan="4" class="text-right">รวม</td>
            <td class="text-center">{{totalQty}}</td>
            <td class="text-center" id="sumQty">0</td>
            <td class="text-right" id="sumAmount">0</td>
          </tr>
          {{else}}
          <tr class="font-size-12">
            <td class="middle text-center">{{no}}</td>
            <td class="middle text-center">{{barcode}}</td>
            <td class="middle">{{product}}</td>
            <td class="middle text-center" id="price-{{id}}">{{price}}</td>
            <td class="middle text-center" id="qty-{{id}}">{{qty}}</td>
            <td class="middle text-center">
              <input type="number" class="form-control input-sm text-center qty" min="0" max="{{qty}}" id="cnQty-{{id}}" />
            </td>
            <td class="middle text-right" id="cnAmount-{{id}}">0</td>
          </tr>
          {{/if}}
        {{/if}}
      {{/each}}
    </tbody>
  </table>
</script>
