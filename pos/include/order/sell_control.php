<div class="row sell-table" style="background-color:#EEE">
    <div class="col-sm-1 col-1-harf padding-5 first">
      <label>ส่วนลด [฿]</label>
      <input type="number" min="0" class="form-control input-sm text-center control" id="a-disc" value="" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>ส่วนลด [%]</label>
      <input type="number" min="0" max="100" class="form-control input-sm text-center control" id="p-disc" value="" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label>ราคา</label>
      <input type="number" class="form-control input-sm text-center" min="0" id="price" value="" />
    </div>
    <div class="col-sm-1 padding-5">
      <label>จำนวน</label>
      <input type="number" min="0" class="form-control input-sm text-center control" id="qty" value="1" />
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
      <label class="display-block not-show">btn</label>
      <div class="btn-group width-100">
        <button type="button" class="btn btn-sm btn-success width-50 control" id="btn-qty-up" onclick="increaseQty()">
          <i class="fa fa-plus"></i>
        </button>
        <button type="button" class="btn btn-sm btn-info width-50 control" id="btn-qty-down" onclick="decreaseQty()">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>

    <div class="col-sm-3 col-3-harf padding-5">
      <label>บาร์โค้ด</label>
      <input type="text" class="form-control input-sm control" id="barcode-item" autofocus />
    </div>
    <div class="col-sm-1 col-1-harf padding-5 last">
      <label class="display-block not-show">Btn</label>
      <button type="button" class="btn btn-sm btn-primary btn-block control" onclick="addToOrder()">
        <i class="fa fa-bolt"></i> เพิ่มรายการ
      </button>
    </div>
    <div class="col-sm-12 text-center" style="padding-top:15px;">
      <span style="font-size:16px">
      <span id="sum-items"><?php echo $order->total_product; ?></span> รายการ /  จำนวนสินค้า
      <span id="sum-qty"><?php echo $order->total_qty; ?></span> รายการ
      </span>
      <span class="pull-right" style="font-size:16px">#<?php echo $order->reference; ?></span>
    </div>
  </div><!--/ row -->
