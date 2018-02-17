<div class="container main-container headerOffset" id="body">
    <div class="row">
        <div class="col-lg-9 col-md-9 col-sm-7 col-xs-6 col-xxs-12 text-center-xs">
            <h1 class="section-title-inner"><span><i class="fa fa-shopping-basket"></i> ตะกร้าสินค้า </span></h1>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-5 rightSidebar col-xs-6 col-xxs-12 text-center-xs">
            <h4 class="caps"><a href="javascript:void(0)" onClick="goToHome()"><i class="fa fa-chevron-left"></i> เลือกซื้อสินค้าต่อ.. </a></h4>
        </div>
    </div>
    <!--/.row-->
<?php $total_discount = 0; $total_price = 0; $total_amount = 0; ?>
    <div class="row">
        <div class="col-lg-9 col-md-9 col-sm-7">
            <div class="row userInfo">
                <div class="col-xs-12 col-sm-12">
<?php if( isset( $cart_items ) && $cart_items !== FALSE ) : ?>	  
<?php 	$items = $cart_items; ?>              
                    <div class="cartContent w100 hidden-xs">
                        <table class="cartTable table-responsive" id="cart-table" style="width:100%">
                            <tbody>
                            <tr class="CartProduct cartTableHeader">
                                <td style="width:15%">สินค้า</td>
                                <td style="width:45%"></td>
                                <td style="width:15%">จำนวน</td>
                                <td style="width:10%">ส่วนลด</td>
                                <td style="width:10%">รวม</td>
                                <td style="width:5%" class="delete">&nbsp;</td>
                            </tr>
	                            
	<?php foreach( $cart_items as $item ) : ?>	
    <?php 
					$id_pa = $item->id_product_attribute; 
					$id_pd = getIdProduct($id_pa);
					$price = itemPrice($id_pa);
					$discount = discountAmount($id_pa, $item->qty, $this->id_customer);
					$total_sell = ($price * $item->qty) - $discount;
					$dis 		= get_discount($this->id_customer, $id_pd); 
					$total_price += $price * $item->qty;
					$total_amount += $total_sell;
					$total_discount += $discount;
					$available_qty = apply_stock_filter($this->product_model->getAvailableQty($id_pa)); 
	?>									
                            <tr class="CartProduct" id="row_<?php echo $id_pa; ?>" style="font-size:12px;">
                                <td class="CartProductThumb">
                                    <div><img src="<?php echo get_image_path( get_id_image($id_pa), 2); ?>" alt="img"></div>
                                    <input type="hidden" class="id_pa" value="<?php echo $id_pa; ?>"/>
                                </td>
                                <td>
                                    <div class="CartDescription">
                                        <h4><?php echo productReference($id_pa); ?> </h4>
                                        <span class="size"><?php echo itemName($id_pa); ?></span>
                                        <span class="size" style="display:block;"><?php echo attrType($id_pa); ?> : <?php echo attrLabel($id_pa); ?></span>
                        				<span id="price_<?php echo $id_pa; ?>" class="price-standard <?php if( $dis['discount'] == 0 ) : ?>hide<?php endif; ?>"><?php echo $price; ?></span>
                                        <div class="price" id="sell-price_<?php echo $id_pa; ?>"><?php echo number_format(sell_price($price, $dis['discount'], $dis['type']),2); ?></div>
                                    </div>
                                </td>
                                
                                <td>
									<div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-xs decrease-btn" type="button"  onClick="decreaseQty(<?php echo $id_pa; ?>, 1)"><i class="fa fa-minus"></i></button>
                                      </span>
                                      <span id="Qty_<?php echo $id_pa; ?>" class="form-control" style="text-align:center; height:36px;"><?php echo $item->qty; ?></span>
                                      <span class="input-group-btn">
                                        <button class="btn btn-xs increase-btn" type="button" onClick="increaseQty(<?php echo $id_pa; ?>, <?php echo $available_qty; ?>)"><i class="fa fa-plus"></i></button>
                                      </span>
                                    </div><!-- /input-group -->
                                    <span class="stock-label">คงเหลือ <?php echo number_format($available_qty); ?> ในสต็อก</span>
								</td>
                                <td id="discount_<?php echo $id_pa; ?>"><?php echo number_format($discount, 2); ?></td>
                                <td id="total_sell_<?php echo $id_pa; ?>"><?php echo number_format($total_sell, 2); ?></td>
                                <td><a title="Delete" onClick="deleteCartRow(<?php echo $id_pa; ?>)"> <i class="fa fa-times fa-lg"></i></a></td>
                            </tr>
	<?php endforeach; ?>
  							</tbody>
                        </table>
			 </div>          
             <!--cartContent-->
             <!-- For mobile -->
             <div class="cartContent w100 hide visible-xs" >
              <table class="table" style="width:100%">
                 <tr>
                 <td colspan="2" style="width:80%">สินค้า</td>
                 <td style="width:20%; text-align:right;">จำนวน</td>
                 </tr>
	<?php  foreach( $items as $item ) :   
   					$id_pa = $item->id_product_attribute; 
					$id_pd = getIdProduct($id_pa);
					$price = itemPrice($id_pa);
					$discount = discountAmount($id_pa, $item->qty, $this->id_customer);
					$total_sell = ($price * $item->qty) - $discount;
					$dis 		= get_discount($this->id_customer, $id_pd); 
					$available_qty = apply_stock_filter($this->product_model->getAvailableQty($id_pa));  
					?>
              <tr id="m-row_<?php echo $id_pa; ?>" style="font-size:12px; border-bottom:solid 1px #ccc;">
              	<td style="width: 20%; text-align:center; vertical-align:middle;">
                     <div><img src="<?php echo get_image_path( get_id_image($id_pa), 1); ?>" alt="img"></div>
                </td>
                <td>
                	<div class="CartDescription">
                    <span style="font-size:16px; font-weight:bold; display:block;"><?php echo productReference($id_pa); ?> </span>
                    <span class="size"><?php echo itemName($id_pa); ?></span>
                    <span class="size" style="display:block;"><?php echo attrType($id_pa); ?> : <?php echo attrLabel($id_pa); ?></span>
                    <span id="m-price_<?php echo $id_pa; ?>" class="price-standard <?php if( $dis['discount'] == 0 ) : ?>hide<?php endif; ?>"><?php echo $price; ?></span>
                    <div class="price" id="m-sell-price_<?php echo $id_pa; ?>"><?php echo number_format(sell_price($price, $dis['discount'], $dis['type']),2); ?></div>
                    <span class="stock-label" style="top:0px;">คงเหลือ <?php echo number_format($available_qty); ?> ในสต็อก</span>
                    <span style="display:block; font-size:14px;"><a title="Delete" onClick="deleteCartRow(<?php echo $id_pa; ?>)">ลบ</a></span>
                    
                     </div>
                </td>
                <td align="center">
                	<button class="btn btn-xs increase-btn" type="button" onClick="increaseQty(<?php echo $id_pa; ?>, <?php echo $available_qty; ?>)"><i class="fa fa-plus"></i></button>
                     <span id="mQty_<?php echo $id_pa; ?>" class="form-control input-xs" style="text-align:center; margin-top:5px; margin-bottom:5px; border-radius:0px;"><?php echo $item->qty; ?></span>
                     <button class="btn btn-xs decrease-btn" type="button" onClick="decreaseQty(<?php echo $id_pa; ?>, 1)"><i class="fa fa-minus"></i></button>  
                </td>
              </tr>
                    					
	<?php  endforeach; ?>		
    			</table>			         	
             </div>
             <!-- For mobile -->
                    <div class="cartFooter w100" style="background:none; border-top:0px;">
                        <div class="box-footer">
                            <div class="pull-left"><a href="javascript:void(0)" onClick="goToHome()" class="btn btn-info"> <i class="fa fa-arrow-left"></i> &nbsp; เลือกซื้อสินค้าต่อ </a></div>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-warning" onClick="reloadCart()"><i class="fa fa-undo"></i> &nbsp; โหลดตะกร้าใหม่</button>
                            </div>
                        </div>
                    </div>
                    <!--/ cartFooter -->              
<?php else : ?>
		     <div class="col-lg-12" style="padding-top: 50px; padding-bottom: 50px;">
             	<h4 class="style2 text-center"><span style="font-size:22px;">ไม่มีสินค้าในตะกร้า</span></h4>
                <center style="margin-top: 20px;">
                <button class="btn btn-primary btn-lg" onClick="goToHome()" style="width: 200px;">เลือกซื้อสินค้าต่อ</button>
                </center>
             </div>
<?php endif; ?>		                    
                          
                   
                    

                </div>
            </div>
            <!--/row end-->

        </div>
        <?php $shipping_cost = delivery_cost($this->cart_qty); ?>
        <div class="col-lg-3 col-md-3 col-sm-5 rightSidebar">
            <div class="contentBox">
                <div class="w100 costDetails">
                    <div class="table-block" id="order-detail-content">
                    <?php if( isset( $cart_items) && $cart_items != FALSE ) : ?>
                    	<a class="btn btn-primary btn-lg btn-block " id="checkout-btn-top" title="checkout" href="checkout-0.html" style="margin-bottom:20px"> ดำเนินการต่อ &nbsp; <i class="fa fa-arrow-right"></i> </a>
					<?php endif; ?>
                        <div class="w100 cartMiniTable">
                            <table id="cart-summary" class="std table">
                                <tbody>
                                <tr>
                                    <td>สินค้ารวม</td>
                                    <td class="price" id="total-price"><?php echo number_format($total_price, 2); ?></td>
                                </tr>
                                <tr style="">
                                    <td>ค่าจัดส่ง</td>
                                    <td class="price" id="shipping-fee"><?php echo number_format($shipping_cost, 2); ?></td>
                                </tr>
                                <tr class="cart-total-price ">
                                    <td>ส่วนลดรวม</td>
                                    <td class="price" id="total-discount"><?php echo number_format($total_discount, 2); ?></td>
                                </tr>
                                <tr>
                                    <td>รวมทั้งสิ้น</td>
                                    <td class=" site-color" id="total-amount" style="font-size:22px; font-weight:bold;"><?php echo number_format( (($total_price - $total_discount) + $shipping_cost), 2); ?> </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                    <?php if( isset( $cart_items) && $cart_items != FALSE ) : ?>
                                        <button type="button" class="btn btn-primary btn-block" id="checkout-btn-bottom" onClick="checkOut()"> ดำเนินการต่อ</button>
                                   <?php endif; ?>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End popular -->

        </div>
        <!--/rightSidebar-->

    </div>
    <!--/row-->

    <div style="clear:both"></div>
</div>
<!-- /.main-container -->
<script>
function deleteCartRow(id_pa)
{
	var id_cart = $('#id_cart').val();
	$.ajax({
		url:"<?php echo base_url(); ?>shop/cart/deleteCartProduct"	,
		type:"POST", cache: "false", data:{ "id_cart" : id_cart, "id_pa" : id_pa },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				$("#row_"+id_pa).animate({opacity : 0}, 1000, function(){ $('#row_'+id_pa).remove(); recalCart(); });
				$("#m-row_"+id_pa).animate({opacity : 0}, 1000, function(){ $('#m-row_'+id_pa).remove(); recalCart(); });
				
			}
		}
	});
}

function reloadCart()
{
	load_in();
	$('#body').animate({opacity: 0.1}, 1000, function(){ window.location.reload(); });	
}
function decreaseQty(id_pa, min_qty)
{
	var qty = parseInt(removeCommas($('#Qty_'+id_pa).text()));
	var min_qty = parseInt(min_qty);
	if( qty > min_qty)
	{
		qty -= 1;
		$('#Qty_'+id_pa).text(qty);
		$("#mQty_"+id_pa).text(qty);
		updateCart(id_pa, qty);
	}		
}

function increaseQty(id_pa, max_qty)
{
	var qty = parseInt(removeCommas($('#Qty_'+id_pa).text()));
	var max_qty = parseInt(max_qty);
	if( qty < max_qty )
	{
		qty += 1 ;
		$('#Qty_'+id_pa).text(qty);
		$("#mQty_"+id_pa).text(qty);
		updateCart(id_pa, qty);
	}		
}

function updateCart(id_pa, qty)
{
	var id_cart = $('#id_cart').val();
	$.ajax({
		url:"<?php echo base_url(); ?>shop/cart/updateCart",
		type:'POST', cache:'false', data:{ "id_cart" : id_cart, "id_pa" : id_pa, "qty" : qty },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '' && rs != 'fail' )
			{
				recalCart();
			}
		}
	});
}

function recalCart()
{
	var total_price 		= 0;
	var total_discount 	= 0;
	var shipping			= 50;
	var cartLabel		= 0;
	$('.id_pa').each(function(index, element) {
        var id = $(this).val();
		var qty = parseInt(removeCommas($('#Qty_'+id).text()));
		var price = parseFloat(removeCommas($('#price_'+id).text()));
		var sell_price = parseFloat(removeCommas($('#sell-price_'+id).text()));
		var discount = price - sell_price;
		var total_dis = discount * qty;
		var total_amount = sell_price * qty;
		$('#discount_'+id).text(addCommas(total_dis.toFixed(2)));
		$('#total_sell_'+id).text(addCommas(total_amount.toFixed(2)));
		total_price += price * qty;
		total_discount += total_dis;
		shipping += qty * 10;
		cartLabel += qty;
		$('#total-price').text(addCommas(total_price.toFixed(2)));
		$('#shipping-fee').text(addCommas(shipping.toFixed(2)));
		$('#total-discount').text(addCommas(total_discount.toFixed(2)));
		var total_amount = total_price - total_discount + shipping;
		$('#total-amount').text(addCommas(total_amount.toFixed(2)));
		$("#cartLabel").text(addCommas(cartLabel));
		$("#cartMobileLabel").text(addCommas(cartLabel));
    });
	if( total_price == 0 && cartLabel == 0 )
	{
		var html = '<tr><td colspan="6">' + 
						'<div class="col-lg-12" style="padding-top: 50px; padding-bottom: 50px;">'+
								'<h4 class="style2 text-center"><span style="font-size:22px;">ไม่มีสินค้าในตะกร้า</span></h4>'+
								'<center style="margin-top: 20px;">'+
								'<button class="btn btn-primary btn-lg" onClick="goToHome()" style="width: 200px;">เลือกซื้อสินค้าต่อ</button>'+
								'</center>'+
						 '</div></td></tr>';
		$('#cart-table').append(html);
		$('#cartLabel').css('visibility', 'hidden');	
		$("#total-price").text(0.00);
		$("#total-discount").text(0.00);
		$("#shipping-fee").text(0.00);
		$("#total-amount").text(0.00);		
		$("#checkout-btn-top").css("display", "none");
		$("#checkout-btn-bottom").css("display", "none");			 
	}
	
}
</script>