<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">

      </p>
    </div>
  </div>
  <hr/>
  <div class="row">
    <div class="col-sm-1 padding-5 first">
      <label class="display-block not-show">toDay</label>
      <button type="button" class="btn btn-sm btn-info btn-block" onclick="toDay('<?php echo date('d-m-Y'); ?>')">วันนี้</button>
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>เริ่มต้น</label>
      <input type="text" class="form-control input-sm text-center" id="fromDate" placeholder="เริ่มต้น" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>สิ้นสุด</label>
      <input type="text" class="form-control input-sm text-center" id="toDate" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-1 padding-5">
      <label class="display-block not-show">report</label>
      <button type="button" class="btn btn-sm btn-success btn-block" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
    </div>
    <div class="col-sm-1 padding-5">
      <label class="display-block not-show">export</label>
      <button type="button" class="btn btn-sm btn-info btn-block" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
    </div>
  </div>
  <hr/>
  <div class="row">
    <div class="col-sm-12" id="result">

    </div>
  </div>
</div><!--/ container -->

<script id="template" type="text/x-handlebarsTemplate">
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th colspan="9" class="middle text-center font-size-18">
        รายงานการขาย วันที่ {{fromDate}} - {{toDate}}
      </th>
    </tr>
    <tr class="font-size-12">
      <th class="width-5 text-center">ลำดับ</th>
      <th class="width-10 text-center">วันที่</th>
      <th class="width-10 text-center">เลขที่</th>
      <th class="width-10 text-center">ชำระโดย</th>
      <th class="width-25">รหัสสินค้า</th>
      <th class="width-10 text-center">ราคา</th>
      <th class="width-10 text-center">จำนวน</th>
      <th class="width-10 text-center">ส่วนลด</th>
      <th class="width-10 text-center">มูลค่า</th>
    </tr>
  </thead>
  <tbody>
  {{#each detail}}
    {{#if nodata}}
      <tr>
        <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
    {{else}}
      {{#if @last}}
        <tr>
          <td colspan="6" class="text-right">รวม</td>
          <td class="text-center">{{totalQty}}</td>
          <td class="text-center">{{totalDisc}}</td>
          <td class="text-center">{{totalAmount}}</td>
        </tr>
      {{else}}
        <tr class="font-size-12">
          <td class="text-center">{{no}}</td>
          <td class="text-center">{{date}}</td>
          <td class="text-center">{{reference}}</td>
          <td class="text-center">{{payment}}</td>
          <td>{{pdCode}}</td>
          <td class="text-center">{{price}}</td>
          <td class="text-center">{{qty}}</td>
          <td class="text-center">{{disc}}</td>
          <td class="text-center">{{amount}}</td>
        </tr>
      {{/if}}
    {{/if}}
  {{/each}}
  </tbody>
</table>
</script>
<script src="script/report/sale_by_item.js"></script>
