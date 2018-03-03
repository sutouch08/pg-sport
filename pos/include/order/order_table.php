<?php
$qs = $order->getDetailOrder($id_order);
$total_amount = 0;
?>
<div class="row">
  <div class="col-sm-12 padding-0">
    <table class="table table-striped border-1">
      <thead>
        <tr class="font-size-12 col-border-1">
          <th class="width-5 text-center">No.</th>
          <th class="width-10 text-center">Barcode</th>
          <th class="text-center">สินค้า</th>
          <th class="width-8 text-center">ราคา</th>
          <th class="width-8 text-center">จำนวน</th>
          <th class="width-8 text-center">ส่วนลด</th>
          <th class="width-10 text-center">ยอดรวม</th>
          <th class="width-5"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
    <?php if(dbNumRows($qs) > 0) : ?>
    <?php  $no = 1; ?>
    <?php  while($rs = dbFetchObject($qs)) : ?>
    <?php  $id = $rs->id_order_pos_detail; ?>
        <tr class="font-size-12 col-border-1 item-row" id="row-<?php echo $id; ?>">
          <td class="text-center no"><?php echo $no; ?></td>
          <td class="text-center"><?php echo $rs->barcode; ?></td>
          <td class="hide-text"><?php echo $rs->product_reference; ?> : <?php echo $rs->product_name; ?></td>
          <td class="text-right" id="price-<?php echo $id; ?>"><?php echo number($rs->price,2); ?></td>
          <td class="text-right" id="qty-<?php echo $id; ?>"><?php echo number($rs->qty); ?></td>
          <td class="text-right" id="disc-<?php echo $id; ?>"><?php echo number($rs->discount_amount, 2); ?></td>
          <td class="text-right" id="amount-<?php echo $id; ?>"><?php echo number($rs->total_amount, 2); ?></td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger del-btn" onclick="deleteRow('<?php echo $id; ?>', '<?php echo $rs->product_reference; ?>')">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
        <?php $no++; ?>
        <?php $total_amount += $rs->total_amount; ?>
      <?php endwhile; ?>
    <?php endif;?>

      </tbody>
    </table>
  </div>
</div>


<script id="new-row-template" type="text/x-handlebarsTemplatte">
<tr class="font-size-12 col-border-1 item-row" id="row-{{id}}">
  <td class="text-center no"></td>
  <td class="text-center">{{barcode}}</td>
  <td class="hide-text">{{pdCode}} : {{ pdName}}</td>
  <td class="text-right">{{price}}</td>
  <td class="text-right" id="qty-{{id}}">{{qty}}</td>
  <td class="text-right" id="disc-{{id}}">{{disAmount}}</td>
  <td class="text-right" id="amount-{{id}}">{{amount}}</td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-danger del-btn" onclick="deleteRow({{id}}, '{{pdCode}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
</tr>
</script>

<script id="current-row-template" type="text/x-handlebarsTemplatte">
  <td class="text-center no"></td>
  <td class="text-center">{{barcode}}</td>
  <td class="hide-text">{{pdCode}} : {{ pdName}}</td>
  <td class="text-right">{{price}}</td>
  <td class="text-right" id="qty-{{id}}">{{qty}}</td>
  <td class="text-right" id="disc-{{id}}">{{disAmount}}</td>
  <td class="text-right" id="amount-{{id}}">{{amount}}</td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-danger del-btn" onclick="deleteRow({{id}}, '{{pdCode}}')">
      <i class="fa fa-trash"></i>
    </button>
  </td>
</script>
