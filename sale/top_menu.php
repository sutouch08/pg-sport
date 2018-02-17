<!-- Fixed navbar start -->
 <?php 
$id_sale	= $_COOKIE['sale_id'];
$rs	= check_cart($id_sale);
if($rs != false){	
	$id_cart	= $rs['id_cart'];
	$id_customer	= $rs['id_customer'];
}else{
	$id_cart	= "";
	$id_customer = 0;
}
$cart = new cart($id_cart);
$total_cart_amount = $cart->total_cart_amount($id_cart);
?>
<!-- Fixed navbar start -->
<div class="navbar navbar-tshop navbar-fixed-top megamenu" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only"> Toggle navigation </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> </button>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-cart"> <i class="fa fa-shopping-cart colorWhite"> </i> <span class="cartRespons colorWhite total_cart"><?php echo $total_cart_amount;  ?></span> </button>
         <!--------------------- แสดงเมนู ------------------->    
  <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav" >
        <li> <a href="index.php?content=order"> Home </a> </li>
        <li class="dropdown megamenu-fullwidth"> <a data-toggle="dropdown" class="dropdown-toggle" href="#"> Products <b class="caret"> </b> </a>
          <ul class="dropdown-menu">
            <li class="megamenu-content "> 
				<?php 	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND id_category !=0 ORDER BY position ASC");	?>
                <?php	while($rs = dbFetchArray($sql) ) : ?>
								<ul class="col-lg-3 col-sm-3 col-md-3 unstyled noMagrinLeft newCollectionUI">
								<li><a href="index.php?content=order&id_category=<?php echo $rs['id_category']; ?>" style="display:block;"><?php echo $rs['category_name']; ?></a></li>
					<?php	$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = ".$rs['id_category']." ORDER BY position ASC"); ?>
					<?php	while($rd = dbFetchArray($sqr) ) : 			?>
                        			<li><a href="index.php?ocntent=order&id_category=<?php echo $rd['id_category']; ?>" style="display:block;"><?php echo $rd['category_name']; ?></a></li>
					<?php	endwhile; ?>
                    			</ul>			
				<?php	endwhile; 	?>
            </li>
          </ul>
        </li>
        <li> <a href="index.php?content=dashboard"> Dash Board </a> </li>
        <li> <a href="index.php?content=tracking"> ติดตามออเดอร์ </a> </li>
        <!-- <li> <a href="request/index.php"> request product </a> </li> -->
      </ul>
      
        <ul class="nav navbar-nav">
   		 <li class='dropdown'>
                    <a class='dropdown-toggle' style='color:#FFF; background-color:transparent;' data-toggle='dropdown' href='#'>
                       <?php echo employee_name($_COOKIE['user_id']); ?> <i class='fa fa-caret-down'></i>
                    </a>
                    <ul class='dropdown-menu dropdown-user'>
                        <li><a href="index.php?content=Employee&reset_password=y&id_employee=<?php echo $_COOKIE['user_id']; ?> "><i class='fa fa-key'></i> Reset Password</a>
                        </li>
                       
                        <li class='divider'></li>
                        <li><a href='index.php?logout'><i class='fa fa-sign-out fa-fw'></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul> 
      </div>
    </div>
  
<?php $data = $cart->cart_detail($id_cart); ?>    
    <!-- this part is duplicate from cartMenu  keep it for mobile -->
    <div class="navbar-cart  collapse">
      <div class="cartMenu  col-lg-4 col-xs-12 col-md-4 ">
        <div class="w100 miniCartTable scroll-pane">
          <table  >
            <tbody>
<?php 	$total_amount = 0; ?>            
<?php if($data != false) : ?>
<?php 	$total_amount = 0; ?>
<?php 	$qs = $data; ?>
<?php 	foreach($qs as $rs)  : ?> 
              <tr class="miniCartProduct <?php echo $rs['id_product_attribute']; ?>">
                <td style="width:20%" class="miniCartProductThumb"><div> <img src="<?php echo $rs['image']; ?>" alt="img"></div></td>
                <td style="width:40%"><div class="miniCartDescription">
                    <h4><?php echo $rs['reference']; ?></h4>
                    <span class="size"><?php echo $rs['color_name']; ?> &nbsp; : &nbsp; <?php echo $rs['size_name']; ?></span>
                    <div class="price"> <span><?php echo number_format($rs['price'],2); ?></span> </div>
                  </div></td>
                <td  style="width:10%" class="miniCartQuantity"><?php echo $rs['qty']; ?></td>
                <td  style="width:15%" class="miniCartSubtotal ">
                	<span><?php echo number_format($rs['total_amount'],2); ?></span> 
                </td>
                <td  style="width:5%" class="delete"><a href="javascript:void(0)" onclick="delete_cart_product(<?php echo $id_cart.", ". $rs['id_product_attribute']; ?>)"><i class="fa fa-trash-o"></i></a></td>
              </tr>
<?php 		$total_amount += $rs['total_amount']; ?>              
<?php 	endforeach; ?>              
<?php endif; ?>
            </tbody>
          </table>
        </div>
        <!--/.miniCartTable-->
        
        <div class="miniCartFooter  miniCartFooterInMobile text-right">
          <h3 class="text-right subtotal"> Subtotal: <span class="total_cart"><?php echo number_format($total_amount,2); ?></span></h3>
          <a class="btn btn-sm btn-primary" href="index.php?content=cart">ดำเนินการสั่งซื้อ(CHECKOUT) </a> </div>
        <!--/.miniCartFooter--> 
        
      </div>
      <!--/.cartMenu--> 
    </div>
    <!--/.navbar-cart-->
  
   
      
      <!--- this part will be hidden for mobile version -->
      <div class="nav navbar-nav navbar-right hidden-xs">
        <div class="dropdown  cartMenu "> 
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-shopping-cart"> </i> <span class="cartRespons total_cart"><?php echo $total_cart_amount; ?></span> <b class="caret"> </b> </a>
          <div class="dropdown-menu col-lg-4 col-xs-12 col-md-4 ">
            <div class="w100 miniCartTable scroll-pane">
              <table>
                <tbody>
<?php 	$total_amount = 0; ?>                
<?php if($data != false) : ?>
<?php 	$total_amount = 0; ?>
<?php 	$qs = $data; ?>
<?php 	foreach($qs as $rs)  : ?> 
              <tr class="miniCartProduct <?php echo $rs['id_product_attribute']; ?>">
                <td style="width:20%" class="miniCartProductThumb"><div> <img src="<?php echo $rs['image']; ?>" alt="img"></div></td>
                <td style="width:40%"><div class="miniCartDescription">
                    <h4><?php echo $rs['reference']; ?></h4>
                    <span class="size"><?php echo $rs['color_name']; ?> &nbsp; : &nbsp; <?php echo $rs['size_name']; ?></span>
                    <div class="price"> <span><?php echo number_format($rs['price'],2); ?></span> </div>
                  </div></td>
                <td  style="width:10%" class="miniCartQuantity"><?php echo $rs['qty']; ?></td>
                <td  style="width:15%" class="miniCartSubtotal ">
                	<span><?php echo number_format($rs['total_amount'],2); ?></span> 
                </td>
                <td  style="width:5%" class="delete"><a href="javascript:void(0)" onclick="delete_cart_product(<?php echo $id_cart.", ". $rs['id_product_attribute']; ?>)"><i class="fa fa-trash-o"></i></a></td>
              </tr>
<?php 		$total_amount += $rs['total_amount']; ?>              
<?php 	endforeach; ?>              
<?php endif; ?>
                </tbody>
              </table>
            </div>
            <!--/.miniCartTable-->
            
            <div class="miniCartFooter text-right">
              <h3 class="text-right subtotal"> Subtotal: <span class="total_cart"><?php echo number_format($total_amount,2); ?></span></h3>
               <a class="btn btn-sm btn-primary" href="index.php?content=cart">ดำเนินการสั่งซื้อ(CHECKOUT) </a></div>
            <!--/.miniCartFooter--> 
            
          </div>
          <!--/.dropdown-menu--> 
        </div>
        <!--/.cartMenu-->
        
      </div>
      <!--/.navbar-nav hidden-xs--> 
    </div>
    <!--/.nav-collapse --> 
    
  </div>
  <!--/.container -->
  
 
  
</div>
<!-- /.Fixed navbar  -->
<script>
function delete_cart_product(id_cart, id_product_attribute)
{
	var row = $("."+id_product_attribute);
	$.ajax({
		url: "controller/orderController.php?delete_cart_product&id_cart="+id_cart+"&id_product_attribute="+id_product_attribute,
		type:"GET",cache:false,
		success: function(rs){
			var rs = $.trim(rs);
			if(rs =="fail"){
				alert("ลบรายการไม่สำเร็จ");
			}else{
				row.remove();
				$(".total_cart").html(rs);				
			}
		}
	});
}
</script>
