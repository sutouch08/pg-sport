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
  </div>
  <hr/>
  <div class="row">
    <div class="col-sm-12" id="result">

    </div>
  </div>
</div><!--/ container -->

<script id="template" type="text/x-handlebarsTemplate">
<table class="table table-bordered">
  <tr>
    <td colspan="4" class="middle text-center font-size-18">สรุปยอดขาย วันที่ {{fromDate}} - {{toDate}}</td>
  </tr>
  <tr>
    <td class="text-center width-25">
      <span class="font-size-24 display-block green">จำนวน</span>
      <span class="font-size-24 display-block green">{{qty}}</span>
    </td>
    <td class="text-center width-25">
      <span class="font-size-24 display-block">ยอดขายรวม</span>
      <span class="font-size-24 display-block">{{soldAmount}}</span>
    </td>
    <td class="text-center width-25">
      <span class="font-size-24 display-block blue">เงินสด</span>
      <span class="font-size-24 display-block blue">{{cashAmount}}</span>
    </td>
    <td class="text-center width-25">
      <span class="font-size-24 display-block red">บัตรเครดิต</span>
      <span class="font-size-24 display-block red">{{cardAmount}}</span>
    </td>
  </tr>
</table>
</script>
<script src="script/report/sale_summary.js"></script>
