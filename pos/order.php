<div class="container width-100">
<?php $id_order = isset($_GET['id_order']) ? $_GET['id_order'] : ''; ?>
<?php $order = new order($id_order); ?>
<?php include 'include/order/top_menu.php'; ?>
  <hr />
  <!-- sell row -->
  <div class="row sell-row">
    <!-- left panel -->
    <div class="col-sm-9">
      <?php include 'include/order/sell_control.php'; ?>
      <?php include 'include/order/order_table.php'; ?>
    </div>
    <!--/ left panel -->

    <!-- right panel -->
    <div class="col-sm-3 padding-right-0">
      <?php include 'include/order/right_panel.php'; ?>
    </div>
    <!--/ right panel -->

  </div>
  <!--/ sell row -->

<!-- pay method 0 = cash,  1 = card  2 = credit -->
<input type="hidden" id="payment-method" value="0" />
<input type="hidden" id="sell-amount" value="<?php echo $total_order; ?>" />
<input type="hidden" id="id_order" value="<?php echo $id_order; ?>" />
</div><!--/ container -->
<script src="script/order/order.js"></script>
