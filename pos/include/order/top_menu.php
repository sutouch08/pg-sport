<div class="row" style="margin-top:-15px;">
  <div class="col-sm-6">
    <button type="button" class="btn btn-lg btn-primary" onclick="newBill()">
      <i class="fa fa-plus"></i> เพิ่มบิลขายใหม่
    </button>
    <button type="button" class="btn btn-lg btn-info" onclick="getLastBill()">
      <i class="fa fa-bolt"></i> บิลล่าสุด
    </button>
    <button type="button" class="btn btn-lg btn-warning" onclick="pauseBill()">
      <i class="fa fa-pause"></i> พักบิล
    </button>
    <button type="button" class="btn btn-lg btn-danger" onclick="cancleBill()">
      <i class="fa fa-times"></i> ยกเลิกบิล
    </button>

  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-lg btn-primary no-radius" onclick="printBill()">
        <i class="fa fa-print"></i> พิมพ์บิล
      </button>
    </p>
  </div>
</div>
