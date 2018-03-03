<div class="row">
  <div class="col-sm-12 margin-bottom-20">
    <label class="total-sell-label" id="sum-amount">
      <?php echo number($total_amount,2); ?>
    </label>
  </div>
  <div class="col-sm-12 margin-bottom-20">
    <div class="btn-group width-100">
      <button type="button" class="btn btn-lg btn-primary no-radius width-50 payment" id="btn-pay-cash" onclick="payByCash()">
        <i class="fa fa-money"></i>  เงินสด
      </button>
      <button type="button" class="btn btn-lg no-radius width-50 payment" id="btn-pay-card" onclick="payByCard()">
        <i class="fa fa-credit-card"></i>  บัตรเครดิต
      </button>
    </div>
  </div>

  <div class="col-sm-12 margin-bottom-20">
    <div class="input-group">
      <input type="number" class="form-control input-lg no-radius text-center payment" id="txt-received-money" placeholder="รับเงิน (Space)" />
      <span class="input-group-btn">
        <button type="button" class="btn btn-primary btn-lg no-radius payment" onclick="justBalance()">รับพอดี</button>
      </span>
    </div>

  </div>
  <div class="col-sm-12 margin-bottom-10">
    <button type="button" class="btn btn-lg btn-success btn-block no-radius" id="btn-pay-order" onclick="payOrder()" disabled>
      ชำระเงิน (End)
    </button>
  </div>

  <div class="col-sm-12 margin-bottom-10">
    <label class="display-block font-size-16">เงินทอน</label>
    <input type="number" class="form-control input-lg no-radius text-center disabled" style="border:0px;" id="change" />
  </div>
</div>
