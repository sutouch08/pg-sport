<div class="container width-100">
<?php if(!isset($_GET['id_order'])) : ?>
<?php  $order = new order_pos(); ?>  
<?php include 'include/order/top_menu.php'; ?>
<?php else : ?>
  <?php $id_order = isset($_GET['id_order']) ? $_GET['id_order'] : ''; ?>
  <?php $order = new order_pos($id_order); ?>
  <?php $disabled = $order->is_paid == 1 ? 'disabled' : ''; ?>
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

  <!-- pay method 1 = cash,  2 = card  -->
  <input type="hidden" id="payment-method" value="1" />
  <input type="hidden" id="sell-amount" value="<?php echo $order->total_amount; ?>" />
  <input type="hidden" id="id_order" value="<?php echo $id_order; ?>" />
  <input type="hidden" id="is_paid" value="<?php echo $order->is_paid; ?>" />
<?php endif; ?>
</div><!--/ container -->


<script src="script/order/order.js"></script>
<script src="script/order/control.js"></script>
<script src="script/order/payment.js"></script>
