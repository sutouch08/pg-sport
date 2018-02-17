
<?php if( !isset($id_customer ) ) : ?>
		<form action ='controller/orderController.php?new=y&id_sale=<?php echo $id_sale; // id_sale ประกาศไว้ที่ top menu ?>' method='post'>
        <div class='col-lg-4 col-md-4 col-sm-6 col-xs-6 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-3'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'><h3>เลือกลูกค้า </h3> </div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><select name='id_customer' id='id_customer' class='form-control input-sm input-sx'><?php customerList(getSaleId($_COOKIE['user_id'])); ?></select> </div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
		 <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><button class='form-control btn-success' type='submit' >ตกลง</button></div>
		</div>
		</form>
<?php else : ?>
<!--------------------  Modal SELECT CUSTOMER  ----------------------->
	<div class='modal fade' id='customer_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog'>
			<div class='modal-content'>
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='myModalLabel'>--- เลือกลูกค้า ---</h4>
				</div>
				<div class='modal-body'>
				<form action ='controller/orderController.php?new=y&id_sale=<?php echo $id_sale; ?>&content=cart' method='post'>
					<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><br />
                    	<select name='id_customer' id='id_customer' class='form-control input-sm input-sx'> 
						<?php customerList(getSaleId($_COOKIE['user_id'])); ?>
                    	</select> 
                    </div>
					<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
				 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='submit' class='btn btn-primary'>ตกลง</button></form>
				</div>
                </form>
			</div>
		</div>
	</div>
    <!-------------------- End  Modal SELECT CUSTOMER  ----------------------->
	<div class='row'>
    	<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>
    		<p class='pull-right'>
	<?php if( $id_customer != 0 ) : ?>            
				<input type='hidden' id='customer_id' value='<?php $id_customer; ?>' />
				คุณกำลังสั่งสินค้าให้กับ &nbsp;<strong><?php echo customer_name($id_customer); ?></strong>&nbsp;&nbsp;
				<a href='#' data-toggle='modal' data-target='#customer_change'>	
					<button type='button' class='btn btn-warning btn-xs'><i class="fa fa-pencil"></i>แก้ไข</button> 
                </a>
					หรือ 
               <button type='button' class='btn btn-danger btn-xs' onclick="confirm_delete('โปรดตรวจสอบ','คุณแน่ใจว่าต้องการยกเลิกรายการในตะกร้าสินค้านี้ทั้งหมด','controller/orderController.php?cancle=true&id_cart=<?php echo $id_cart; ?>')"><i class="fa fa-trash"></i>&nbsp;ยกเลิก</button>
	<?php  else : ?>
				<input type='hidden' id='customer_id' value='<?php echo $id_customer; ?>' /> 
                คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา 
                <a href='#' data-toggle='modal' data-target='#customer_change'>
                	<button type='button' class='btn btn-success'><i class='fa fa-user'></i>เลือกลูกค้า</button> 
               </a>
	<?php endif; ?>
			</p>
		 </div>
      </div>
	<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>
<?php endif; ?>  
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-6 col-xs-6'>
      <h3 class='section-title-inner'><span>ตะกร้าสินค้า </span></h3>
    </div>
    <div class='col-lg-3 col-md-3 col-sm-6 col-xs-6 rightSidebar'>
      <a href='index.php' class="pull-right"><button type="button" class="btn btn-info"><i class='fa fa-chevron-left'></i>&nbsp;ซื้อสินค้าต่อ </button></a>
    </div>
  </div><!--/.row-->
 <?php if( $id_cart != "" ) : ?>

<div class='row'>
 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <table class='cartTable table-responsive' style='width:100%'>
              <tbody>
                <tr class='CartProduct cartTableHeader' style="font-size:0.8em;">
                  <td colspan="2" style='width:55%'>รายระเอียด</td>
                  <td style='width:10%'>จำนวน</td>
                  <td style='width:15%'>ราคา</td>
                  <td style='width:20%' >มูลค่า</td>
                </tr>
<?php $qs = $cart->cart_detail($id_cart); ?>
<?php $sumtotal_cart = 0; $sum_qty = 0; ?>
<?php	foreach($qs as $rs) : ?>
<?php 		$product = new product();
				$id_product_attribute = $rs['id_product_attribute'];
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product, $id_customer);
				$product->product_attribute_detail($id_product_attribute);			
				$qty = $rs['qty'];		
				$cart_product_price = $product->product_price;
				$cart_product_sell = $product->product_sell;
				$cart_discout = $product->product_discount;			
				$cart_total = $qty * $cart_product_sell;			
				$cart_total_not_discount = $qty * $cart_product_price;			
				$sumtotal_cart += $cart_total;		
				$sum_qty += $qty;	
				$can_order = $product->available_order_qty($id_product_attribute);
				$p_img = $rs['image'];
?>
				<tr class='CartProduct <?php echo $id_product_attribute; ?>'>
                  <td style="width:15%"  class='CartProductThumb'><img class="img-responsive" src='<?php echo $p_img; ?>' alt='img' style="width:100%; max-width:125px;" /></td>
                  <td style="width:40%" >
                  	<div class='CartDescription'>
                      <span class="size" style="display:block"><?php echo $rs['reference']; ?></span>
                      <span class='size' style="display:block"><?php echo $rs['color_name']; ?>&nbsp; : &nbsp; <?php echo $rs['size_name']; ?></span>
                      <div class='size' style="display:block">ส่วนลด <?php echo $cart_discout; ?></span>
                    </div>
                  </td>
                  <td ><?php echo $rs['qty']; ?></td>
                  <td ><?php echo number_format($cart_product_price,2); ?></td>
                  <td class='size'><?php echo number_format($cart_total,2); ?></td>
                </tr>		
<?php endforeach; ?>                		
				<tr><td colspan="5" style="line-height:50px;"><center><strong>รวมทั้งหมด &nbsp; <?php echo number_format($sum_qty); ?>&nbsp; รายการ มูลค่ารวม <?php echo number_format($sumtotal_cart,2); ?></strong></center></td>
</tbody>
</table>
	</div>
</div>
<form action="controller/orderController.php?confirm_order" method="post">
<div class="row">
	<div class='col-lg-12 col-md-12 col-xs-12 col-sm-12'><h2 class='block-title-2'> ข้อความของคุณ</h2></div>
	<div class='col-lg-12 col-md-12 col-xs-12 col-sm-12'>
     	<textarea id='comment' class='form-control' name='comment' cols='26' rows='3'></textarea>         
	</div>
    <div class='col-lg-12 col-md-12 col-xs-12 col-sm-12'>&nbsp;
    	<input type="hidden" name="id_cart" value="<?php echo $id_cart; ?>"  />
        <input type="hidden" name="id_customer" value="<?php echo $id_customer; ?>"  />
   </div>
<?php if($id_customer !=0) : ?>   
    <div class='col-lg-12 col-md-12 col-xs-12 col-sm-12'>
    	<label for="condition">
     	<input type="checkbox" name="condition" id="condition" />&nbsp; ฉันยอมรับเงื่อนไขการสั่งซื้อ</label>
	</div>
     <div class='col-lg-12 col-md-12 col-xs-12 col-sm-12'>&nbsp;<!---------  Divider  --------------></div>
      <div class='col-lg-2 col-md-3 col-xs-12 col-sm-12'><button id="btn_confirm" type="submit" type="button" class="btn btn-success btn-block" disabled="disabled" onclick="confirm_order(<?php echo $id_cart; ?>)">ยืนยันการสั่งซื้อ</button></div>
<?php else : ?>
<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>
<div class="alert alert-warning"><h4>ยังไม่ได้เลือกลูกค้า</h4></div>
<?php endif; ?>      
    <h3>&nbsp;</h3>
    </form>
</div>
<?php elseif( $id_cart == "" ) : ?>     
<div class="row"   >
<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>
<div class="alert alert-success"><h4>ไม่มีสินค้าในตะกร้าของคุณ</h4></div>
</div>
</div>
<?php endif; ?>
<script>
$("#condition").click(function(){
	if($(this).is(":checked")){
		$("#btn_confirm").removeAttr("disabled");
	}else{
		$("#btn_confirm").attr("disabled","disabled");
	}
});

function confirm_order(id_cart)
{
	
}
</script>

