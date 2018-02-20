<?php
include '../../library/config.php';
include '../../library/functions.php';
include '../../invent/function/tools.php';

$id = $_GET['id_order'];
$order = new order_pos($id);
$details = $order->getDetails($id);
 ?>


<!DOCTYPE HTML>
<html>
<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />

		<title>ใบเสร็จ</title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>library/css/template.css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>library/css/pos.css" />
    <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
  	<script src="<?php echo WEB_ROOT; ?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
	  <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.js"></script>

	</head>

<body style='padding-top:0px; background-color:white;'>
<?php $paper_width = getConfig("PAPER_SIZE"); ?>
<div style="width:<?php echo $paper_width; ?>mm; padding:2px; margin-left:auto; margin-right:auto;">
    <div class="hidden-print" style="margin-bottom:25px; display:none;">
    		<button type="button" class="btn btn-primary btn-xs" id="btn_print" onClick="print_bill()" style="width:50%; float:left; margin-bottom:5px;"><i class="fa fa-print"></i>&nbsp; พิมพ์ ( space )</button>
    		<button type="button" class="btn btn-success btn-xs" id="btn_cancle" onClick="go_back()" style="width:50%; float:left; margin-bottom:5px;"><i class="fa fa-arrow-left"></i>&nbsp; กลับ ( esc )</button>
    </div>
    <center>
      <span class="font-size-10 margin-bottom-10">
        <?php echo getConfig('COMPANY_FULL_NAME'); ?>
      </span>
    </center>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr class="font-size-10">
        <td class="width-100 text-center">TAX#<?php echo getConfig('COMPANY_TAX_ID'); ?>  (VAT Included)</td>
      </tr>
      <tr class="font-size-10">
        <td class="width-100 text-center">POS#<?php echo getConfig('POS_ID'); ?></td>
      </tr>
      <tr class="font-size-10">
        <td class="width-100 text-center">ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ</td>
      </tr>
    </table>
    <?php
		$total_qty = 0;
		$total_price = 0;
		$total_amount = 0;
		$discount = 0;
	   ?>
    <table class="table pos-bill" border="0" align="center" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th class="width-10" ></th>
            <th></th>
          <?php if($paper_width > 50) : ?>
            <th class="width-15 text-right"></th>
          <?php endif; ?>
            <th class="width-25 text-right"></th>
          </tr>
        </thead>

    <?php if(dbNumRows($details) > 0) : ?>
        <?php while($ro = dbFetchObject($details)) : ?>
        <tr>
            <td><?php echo number($ro->qty); ?></td>
            <td><?php echo $ro->product_reference; ?></td>
          <?php if($paper_width > 50) : ?>
            <td class="text-right">@<?php echo number($ro->price,2); ?></td>
          <?php endif; ?>
            <td class="text-right"><?php echo number($ro->price * $ro->qty,2); ?></td>
        </tr>
        <?php
			$total_qty += $ro->qty;
			$total_price += $ro->qty * $ro->price;
			$total_amount += $ro->total_amount;
			$discount += $ro->discount_amount;
		?>
    <?php endwhile; ?>
        <tr>
            <td></td>
            <td class="text-right">ยอดรวม</td>
            <?php if($paper_width > 50) : ?>
            <td></td>
            <?php endif; ?>
            <td class="text-right"><?php echo number($total_price,2); ?></td>
        </tr>
        <?php if( $discount > 0 ) : ?>
        <tr>
          <td></td>
          <td class="text-right">ส่วนลดรวม</td>
          <?php if($paper_width > 50) : ?>
          <td></td>
          <?php endif; ?>
            <td class="text-right"><?php echo number($discount, 2); ?></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right">ยอดสุทธิ</td>
          <?php if($paper_width > 50) : ?>
          <td></td>
          <?php endif; ?>
            <td class="text-right"><?php echo number($total_amount, 2); ?></td>
        </tr>
        <?php endif; ?>

        <tr>
          <td></td>
          <td class="text-right">รับเงิน</td>
          <?php if($paper_width > 50) : ?>
          <td></td>
          <?php endif; ?>
            <td class="text-right"><?php echo number($order->received_amount, 2); ?></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right">เงินทอน</td>
          <?php if($paper_width > 50) : ?>
          <td></td>
          <?php endif; ?>
            <td class="text-right"><?php echo number($order->change_amount, 2); ?></td>
        </tr>

        <tr>
          <td></td>
          <td class="text-right">ชำระโดย</td>
          <?php if($paper_width > 50) : ?>
          <td></td>
          <?php endif; ?>
            <td class="text-right"><?php echo $order->id_payment == 2 ? 'บัตรเครดิต' : 'เงินสด'; ?></td>
        </tr>
        <tr >
          <td colspan="4">
            เลขที่: <?php echo $order->reference; ?>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            วันที่: <?php echo thaiDateTime($order->date_upd); ?>
          </td>
        </tr>
        <tr>
          <td colspan="4" >
            พนักงาน: <?php echo employee_name($order->id_employee); ?>
          </td>
        </tr>
      <?php endif; ?>
    </table>

    <center style="margin-bottom:10px;">-- THANK YOU --</center>


	</div>

<script>
	function print_bill()
	{
		window.print();
	}

	$(document).keyup(function(e){
		if(e.keyCode == 27)
		{
			window.close();
		}
		if(e.keyCode == 13)
		{
			print_bill();
		}
	});

$(document).ready(function(){
  print_bill();
});

</script>
</body>
</html>
