<?php 
$id_customer = $_COOKIE['id_customer']; 
require_once('../invent/function/tools.php');
	require LIB_ROOT."class/Order.php";
	if(isset($_GET['id_order'])){
		$id_order = $_GET['id_order'];
	}else{
		$id_order = "";
	}
	$order = new order($id_order);
 ?>

<!-- styles needed by footable  -->
<link href="assets/css/footable-0.1.css" rel="stylesheet" type="text/css" />
<link href="assets/css/footable.sortable-0.1.css" rel="stylesheet" type="text/css" />

<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='breadcrumbDiv col-lg-12'>
      <ul class='breadcrumb'>
        <li><a href='index.php'>Home</a> </li>
        <li><a href='index.php?content=account'> บัญชีของฉัน </a> </li>
        <li class='active'><?php if(isset($_GET['detail'])){ echo "<a href='index.php?content=order'> รายการสั่งซื้อสินค้า </a>"; }else{ echo "รายการสั่งซื้อสินค้า";}?></li>
      </ul>
    </div>
  </div>
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12'>
      <h1 class='section-title-inner'><span><i class='fa fa-list-alt'></i> รายการสั่งซื้อสินค้า </span></h1>
      <div class='row userInfo'>
        <div class='col-lg-12'>
          <h2 class='block-title-2'> รายการสั่งซื้อสินค้าของคุณ </h2>
        </div>
        <?php if(isset($_GET['detail'])){
	$state = $order->orderState();
	echo"";
	echo"		
        <div class='row'>
        	<div class='col-lg-12'><h4>".$order->reference." - ".$_COOKIE['customer_name']."</h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'><form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>
		<div class='col-lg-6'>
		</div></form>
		<div class='col-lg-6'>
		
		</div><!--col --></div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	";
		
		
		
		
			$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		echo"
		<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px; solid #ccc;'><thead><th style='width:10%'>รูปภาพ</th><th style='width:50%'>สินค้า</th><th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th><th style='width:20%; text-align:center;'>จำนวนเงิน</th></thead>";
		if($row>0){
			$discount ="";
			$amount = "";
			$total_amount = "";
			while($i = dbFetchArray($sql)){
				$product = new product();
				$total = $i['product_price']*$i['product_qty'];
				echo"<tr>
				<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']." : ".$i['barcode'].$order->valid."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_price'],2)."</td>
				<td style='text-align:center; vertical-align:middle;'><p id='qty".$i['id_order'].$i['id_product_attribute']."'>".number_format($i['product_qty'])."</p><input type='text' id='edit_qty".$i['id_order'].$i['id_product_attribute']."' style='display:none;' /></td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($total,2)." </td>
				</tr>";
					$discount += $i['discount_amount'];
					$total_amount += $total;
					$amount += $i['total_amount'];
			}
			echo" 
			<tr><input type='hidden' name='new_qty' id='new_qty' /><input type='hidden' name='id_order' id='id_order' />
			<td rowspan='3' colspan='3'></td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td><td align='right'><b>".number_format($total_amount,2)." ฿</b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td><td align='right'><b>".number_format($discount,2)." ฿</b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><h4>สุทธิ </h4></td><td align='right'><h4>".number_format($amount,2)." ฿</h4></td></tr></table>";
			
		}else{
			echo" <tr><td colspan='6' align='center'><h4>ไม่มีรายการสินค้า</h4></td></tr></table>";
		}
		}else{
        echo "<ul class='nav nav-tabs'>
          <li class='active'><a href='#Tab1' data-toggle='tab'>รายการทั้งหมด</a></li>
          <li><a href='#Tab2' data-toggle='tab'>ยังไม่ได้ชำระเงิน</a></li>
          <li><a href='#Tab3' data-toggle='tab'>ชำระเงินแล้ว</a></li>
        </ul>
        <div class='tab-content'>
		<div class='tab-pane active' id='Tab1'>
       		";$order->order_customer($id_customer,"0,1");echo "
		</div>
        <div class='tab-pane' id='Tab2'>
            ";$order->order_customer($id_customer,"0"); echo "
		</div>
		<div class='tab-pane' id='Tab3'>
           ";$order->order_customer($id_customer,"1");echo "
		</div>
        </div>";
		}
        echo "</div>
        <div class='col-lg-12 clearfix'>
          <ul class='pager'>
			<li class='previous pull-right'><a href='index.php'> <i class='fa fa-home'></i> หน้าหลัก </a></li>
            <li class='next pull-left'><a href='index.php?content=account'>&larr; กลับไปที่บัญชีของฉัน</a></li>
          </ul>
        </div>
      </div>
      <!--/row end--> 
      
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
  </div>
  <!--/row-->
  
  <div style='clear:both'></div>
</div>
<!-- /main-container -->";
?>
<!-- include footable plugin --> 
<script src="assets/js/footable.js" type="text/javascript"></script> 
<script src="assets/js/footable.sortable.js" type="text/javascript"></script> 
<script type="text/javascript">
    $(function() {
      $('.footable').footable();
    });
  </script> 