<div class="container">
<?php
if(isset($_GET['add']))
{
  include 'include/return_order/return_add.php';
}
else if(isset($_GET['view_detail']))
{
  include 'include/return_order/return_detail.php';
}
else
{
  include 'include/return_order/return_list.php';
}

?>
</div><!--/ container -->
<script src="script/return_order/return_order.js"></script>
<script src="script/return_order/return_add.js"></script>
<script src="script/return_order/return_list.js"></script>
