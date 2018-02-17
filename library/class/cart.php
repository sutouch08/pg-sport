<?php
class cart{
		public $id_cart;
		public $id_customer;
		public $valid;
		public $date_add;
		public $date_upd;
		public $sumtotal_cart;
 		public function __construct()
		{
			
		}
		public function newCart($id_cart = "")
		{
			if($id_cart != "")
			{
				$qs = dbQuery("SELECT * FROM tbl_cart WHERE id_cart = ".$id_cart);
				if(dbNumRows($qs) > 0)
				{
					$rs = dbFetchArray($qs);
					$this->id_cart = $rs['id_cart'];
					$this->id_customer = $rs['id_customer'];
					$this->id_sale = $rs['id_sale'];
					$this->date_add = $rs['date_add'];
					$this->date_upd = $rs['date_upd'];
					$this->valid = $rs['valid'];	
				}
			}
		}
		
		public function cart_detail($id_cart)
		{
			if($id_cart != "" ){
				$sql = "SELECT id_cart_product, id_cart, tbl_cart_product.id_product_attribute, qty ";
				$sql .= "FROM tbl_cart_product JOIN tbl_product_attribute ON tbl_cart_product.id_product_attribute = tbl_product_attribute.id_product_attribute ";
				$sql .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
				$sql .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
				$sql .= "WHERE id_cart = ".$id_cart." ORDER BY tbl_product_attribute.id_product ASC, tbl_product_attribute.id_color ASC, tbl_size.position ASC, tbl_attribute.position ASC";
				$sql = dbQuery($sql);
				//$sql = dbQuery("SELECT * FROM tbl_cart_product WHERE id_cart = ".$id_cart." ORDER BY date_add ASC");
				$data = array();
				if(dbNumRows($sql) > 0 )
				{
					while($rs = dbFetchArray($sql) )
					{
						$product = new product();
						$id_product_attribute = $rs['id_product_attribute'];
						$qty = $rs['qty'];
						$id_product = $product->getProductId($id_product_attribute);
						$product->product_detail($id_product, $this->id_customer);
						$product->product_attribute_detail($id_product_attribute);
						$cart_product_price = $product->product_price;
						$cart_product_sell = $product->product_sell;
						$cart_total = $qty * $cart_product_sell;
						$p_img = $product->image_attribute;
						$arr = array("id_product_attribute"=>$id_product_attribute,"reference"=>$product->reference, "price"=>$cart_product_sell, "qty"=>$qty, "color_name"=>$product->color_name, "size_name"=>$product->size_name, "total_amount"=>$cart_total, "image"=>$p_img);
						array_push($data, $arr);
					}
				}else{
					$data = false;
				}
			}else{
				$data = false;
			}
			return $data;
		}
		
		
		public function total_cart_amount($id_cart="")
		{
			$total_cart_amount = 'ว่างเปล่า';
			if($id_cart != "")
			{
				$qs = $this->cartDetail($id_cart);
				if(dbNumRows($qs) >0)
				{
					$total_cart_amount = 0;
					while($rs = dbFetchArray($qs) )
					{
						$product = new product();
						$id_product_attribute = $rs['id_product_attribute'];
						$qty = $rs['qty'];
						$id_product = $product->getProductId($id_product_attribute);
						$product->product_detail($id_product, $this->id_customer);
						$product->product_attribute_detail($id_product_attribute);
						$cart_product_price = $product->product_price;
						$cart_product_sell = $product->product_sell;
						$cart_total = $qty * $cart_product_sell;
						$total_cart_amount += $cart_total;
					}
					$total_cart_amount = number_format($total_cart_amount, 2);
				}
			}
			return $total_cart_amount;				
		}
		
		
		public function maxId(){
			list($id) = dbFetchArray(dbQuery("SELECT MAX(id_cart) FROM tbl_cart"));
			return $id;
		}
		public function addToCart($id_product_attribute, $qty)
		{
			$sql = dbQuery("INSERT INTO tbl_cart_product (id_cart, id_product_attribute, qty, date_add) VALUE (".$this->id_cart.", $id_product_attribute, $qty)");
			return $sql;			
		}
		public function cartDetail($id_cart = "")
		{
			if($id_cart == ""){ $id_cart = $this->id_cart; }
			$sql = dbQuery("SELECT * FROM tbl_cart_product WHERE id_cart = ".$id_cart." ORDER BY date_add ASC");
			return $sql;	
		}
		
		public function cartmini($id_cart,$id_customer=0){
			$product = new Product();
			if($id_cart == ""){
				 echo" 

	
         <!--- this part will be hidden for mobile version -->
      <div class='nav navbar-nav navbar-right hidden-xs'>
        <div class='dropdown  cartMenu '> 
           <div class='dropdown-menu col-lg-4 col-xs-12 col-md-4 '>
          <div class='w100 miniCartTable scroll-pane'>
		   <table id='mini_cart'>
		   <tr>
		   <td>
		   <h3></h3>
		   <h3 align='center'> ยังไม่มีรายการสินค้า  </h3>
		  <h3></h3>
		   </td>
		   </tr>
			 </table>
            </div>
            <!--/.miniCartTable-->
			<div class='miniCartFooter  text-right'>
						  <h3 class='text-right subtotal' id='sumtotalcartin'>  ราคารวม : (ว่างเปล่า) </h3>
						  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการสั่งซื้อ</a>
						 </div>
						<!--/.miniCartFooter--> 
            
            <div class='miniCartFooter' style='text-align:center;'></div> 
            <!--/.miniCartFooter--> ";
            
         echo"  </div>
          <!--/.dropdown-menu-->
		  <a href='#' class='dropdown-toggle' data-toggle='dropdown'> <i class='fa fa-shopping-cart'> </i> <span class='cartRespons' id='sumtotalcart'>ตะกร้า(ว่างเปล่า)</span> <b class='caret'> </b> </a>
        </div>
        <!--/.cartMenu-->";
			}else{
			echo"
			
         <!--- this part will be hidden for mobile version -->
      <div class='nav navbar-nav navbar-right hidden-xs'>
        <div class='dropdown  cartMenu '> 
           <div class='dropdown-menu col-lg-4 col-xs-12 col-md-4 '>
            <div class='w100 miniCartTable scroll-pane'>
             <table id='mini_cart'>";
						//$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$sql = "SELECT id_cart_product, id_cart, tbl_cart_product.id_product_attribute, qty ";
						$sql .= "FROM tbl_cart_product JOIN tbl_product_attribute ON tbl_cart_product.id_product_attribute = tbl_product_attribute.id_product_attribute ";
						$sql .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
						$sql .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
						$sql .= "WHERE id_cart = ".$id_cart." ORDER BY tbl_product_attribute.id_product ASC, tbl_product_attribute.id_color ASC, tbl_size.position ASC, tbl_attribute.position ASC";
						 $sql = dbQuery($sql);
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							$sumtotal_cart1 = "";
						}else{
						$i=0;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_price = $product->product_price;
							$cart_product_sell = $product->product_sell;
							$cart_total = $qty * $cart_product_sell;
							$sumtotal_cart = $sumtotal_cart + $cart_total;
							$past_im = $product->image_attribute;
							$sumtotal_cart1 = number_format($sumtotal_cart,2);
							$array = $product->getCategoryId($product->id_product);
							$id_cat = array();
							foreach($array as $ar){
								array_push($id_cat,$ar);
							}
							$id_category = max($id_cat);
                echo "<tr class='miniCartProduct'>
                    <td style='width:20%' class='miniCartProductThumb'><div> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'> <img src='$past_im'> </a> </div></td>
                    <td style='width:40%'><div class='miniCartDescription'>
                        <h4> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'> ".$product->reference."</a> </h4>
                        <span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span>
                        <div class='price'> <span> ".number_format($cart_product_sell,2)." </span> </div>
                      </div></td>
                    <td  style='width:10%' class='miniCartQuantity' align='left'><a > $qty </a></td>
                    <td  style='width:20%' class='miniCartSubtotal' align='right' ><span>".number_format($cart_total,2)." ฿ </span></td>
                    <td  style='width:10%' class='delete' align='center' ><a onclick='dropproductcart($id_cart,$id_product_attribute)'> <i class='fa fa-trash-o'></i> </a></td>
                  </tr>";
				  		$i++;
						}
						}
               echo "</tbody>
              </table>
            </div>
            <!--/.miniCartTable-->
            
            <div class='miniCartFooter text-right'> 
             
              <h3 class='text-right subtotal' id='sumtotalcartin'>  ราคารวม : $sumtotal_cart1 </h3> 
            <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการสั่งซื้อ</a> </div>
            <!--/.miniCartFooter--> ";
            
         echo"  </div>
          <!--/.dropdown-menu-->
		  <a href='#' class='dropdown-toggle' data-toggle='dropdown'> <i class='fa fa-shopping-cart'> </i> <span class='cartRespons' id='sumtotalcart'>";if($sumtotal_cart == ""){ echo "ตะกร้า(ว่างเปล่า)";}else{
			echo "ตะกร้า($sumtotal_cart1)";}echo "</span> <b class='caret'> </b> </a>
        </div>
        <!--/.cartMenu-->";
			}
		}
		/////************************************************************////
		public function request_cartmini($id_request_order){
			$product = new Product();
			$id_employee = $_COOKIE['user_id'];
			$employee = new employee($id_employee);
			$id_sale = $employee->get_id_sale($id_employee);
			$qs = dbQuery("SELECT id_request_order, id_customer FROM tbl_request_order WHERE id_sale = $id_sale AND status = 0");
			$rs = dbNumRows($qs);
			if($rs<1){
				setcookie("id_request_order","",time()-(3600*8*7), '/');
				 echo" 
      <!--- this part will be hidden for mobile version -->
      <div class='nav navbar-nav navbar-right hidden-xs'>
        <div class='dropdown  cartMenu '> 
           <div class='dropdown-menu col-lg-4 col-xs-12 col-md-4 '>
          <div class='w100 miniCartTable scroll-pane'>
		   <table id='mini_cart'>
		   <tr>
		   <td>
		   <h3></h3>
		   <h3 align='center'> ยังไม่มีรายการสินค้า</h3>
		  <h3></h3>
		   </td>
		   </tr>
			 </table>
            </div>
            <!--/.miniCartTable-->
			<div class='miniCartFooter  text-right'>
						  <h3 class='text-right subtotal' id='sumtotalcartin'>  ราคารวม : (ว่างเปล่า) </h3>
						  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการ</a>
						 </div>
						<!--/.miniCartFooter--> 
            
            <div class='miniCartFooter' style='text-align:center;'></div> 
            <!--/.miniCartFooter--> ";
            
         echo"  </div>
          <!--/.dropdown-menu-->
		  <a href='#' class='dropdown-toggle' data-toggle='dropdown'> <i class='fa fa-shopping-cart'> </i> <span class='cartRespons' id='sumtotalcart'>ตะกร้า (ว่างเปล่า)</span> <b class='caret'> </b> </a>
        </div>
        <!--/.cartMenu-->";
			
			}else{
				list($id_re, $id_customer) = dbFetchArray($qs);
				$id_request_order= $id_re;
				if(!isset($_COOKIE['id_request_order'])){ setcookie("id_request_order",$id_request_order,time()+(3600*8*7), '/'); }
				if(!isset($_COOKIE['id_customer'])){ setcookie("id_customer",$id_customer, time()+(3600*8*7),"/"); }
				echo"
			
         <!--- this part will be hidden for mobile version -->
      <div class='nav navbar-nav navbar-right hidden-xs'>
        <div class='dropdown  cartMenu '> 
           <div class='dropdown-menu col-lg-4 col-xs-12 col-md-4 '>
				<div class='w100 miniCartTable scroll-pane'>
				 <table id='mini_cart'>";
							$sql = dbQuery("SELECT id_request_order_detail, id_request_order, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = '$id_request_order'");
							$row = dbNumRows($sql);
							if($row == "0"){
								$sumtotal_cart = "";
							}else{
							$i=0;
							$sumtotal_cart = '';
							while($i<$row){
								list($id_request_order_detail, $id_request_order, $id_product_attribute,$qty) = dbFetchArray($sql);
								$id_product = $product->getProductId($id_product_attribute);
								$product->product_detail($id_product, $id_customer);
								$product->product_attribute_detail($id_product_attribute);
								$img = $product->image_attribute;
								$reference = $product->reference;
								$sumtotal_cart = $sumtotal_cart + $qty;
	
					echo "<tr class='miniCartProduct'>
								<td style='width:20%' class='miniCartProductThumb'><img src='$img' /></td>
								 <td style='width:40%'><div class='miniCartDescription'><h4> ".$product->reference."</h4><span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span></div></td>
								<td  style='width:10%' class='miniCartQuantity' align='left'>$qty </td>
								<td  style='width:10%' class='delete' align='center' ><a onclick='drop_request_detail($id_request_order,$id_product_attribute)'><i class='fa fa-trash-o'></i></a></td>
					  </tr>";
							$i++;
							}
						}
				   echo "
				  </table>
				</div>
					<div class='miniCartFooter text-right'> 
					  <h3 class='text-right subtotal' id='sumtotalcartin'>  ราคารวม : $sumtotal_cart </h3> 
					  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการ </a> 
					 </div>
         	</div>
		  <a href='#' class='dropdown-toggle' data-toggle='dropdown'> <i class='fa fa-shopping-cart'> </i> <span class='cartRespons' id='sumtotalcart'>";if($sumtotal_cart == ""){ echo "ตะกร้า(ว่างเปล่า)";}else{
			echo "ตะกร้า $sumtotal_cart รายการ";}echo "</span> <b class='caret'> </b> </a>
        </div>
        </div>";
			}
		}
		
		//************************************************//
		public function request_cartmini_for_mobile($id_request_order,$id_customer=0){
			$product = new Product();
			$id_employee = $_COOKIE['user_id'];
			$employee = new employee($id_employee);
			$id_sale = $employee->get_id_sale($id_employee);
			$qs = dbQuery("SELECT id_request_order, id_customer FROM tbl_request_order WHERE id_sale = $id_sale AND status = 0");
			$rs = dbNumRows($qs);
			if($rs<1){
				setcookie("id_request_order","",time()-(3600*8*7), '/');
				echo "<!-- this part is duplicate from cartMenu  keep it for mobile -->
					<div class='navbar-cart  collapse'>
					  <div class='cartMenu  col-lg-4 col-xs-12 col-md-4 '>
						<div class='w100 miniCartTable scroll-pane'>
						 <table id='mini_cart_for_mobile'>
              			</table>
						</div>					
						<div class='miniCartFooter  miniCartFooterInMobile text-right'>
						  <h3 class='text-right subtotal' id='sumtotalcartin_for_mobile'>  ราคารวม) :  </h3>
						  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการ </a>
						 </div>
					  </div>
					</div>";
			}else{
				list($id_re, $id_customer) = dbFetchArray($qs);
				$id_request_order= $id_re;
				if(!isset($_COOKIE['id_request_order'])){ setcookie("id_request_order",$id_request_order,time()+(3600*8*7), '/'); }
				if(!isset($_COOKIE['id_customer'])){ setcookie("id_customer",$id_customer, time()+(3600*8*7),"/"); }
			echo "<!-- this part is duplicate from cartMenu  keep it for mobile -->
					<div class='navbar-cart  collapse'>
					  <div class='cartMenu  col-lg-4 col-xs-12 col-md-4 '>
						<div class='w100 miniCartTable scroll-pane'>
						 <table id='mini_cart_for_mobile' >";
						$sql = dbQuery("SELECT id_request_order_detail, id_request_order, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = '$id_request_order'");
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							$sumtotal_cart1 = "";
						}else{
						$i=0;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_request_order_detail, $id_request_order, $id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$img = $product->image_attribute;
							$reference = $product->reference;
							$sumtotal_cart = $sumtotal_cart + $qty;
                echo "<tr class='miniCartProduct'>
                    <td style='width:20%' class='miniCartProductThumb'><div> <img src='$img'></div></td>
                    <td style='width:40%'><div class='miniCartDescription'>
                        <h4> ".$product->reference." </h4> <span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span></div></td>
                    <td  style='width:10%' class='miniCartQuantity' align='left'> $qty</td>
                    <td  style='width:10%' class='delete' align='center' ><a onclick='drop_request_detail($id_request_order,$id_product_attribute)'> <i class='fa fa-trash-o'></i></a></td>
                  </tr>";
				  		$i++;
						}
						}
               echo "
              </table>
						</div>
						<!--/.miniCartTable-->
						
						<div class='miniCartFooter  miniCartFooterInMobile text-right'>
						  <h3 class='text-right subtotal'  id='sumtotalcartin_for_mobile'>  ราคารวม : $sumtotal_cart  รายการ</h3>
						  <a class='btn btn-sm btn-info' href='index.php?content=cart'>ดำเนินการ </a></div>
						<!--/.miniCartFooter--> 
						
					  </div>
					  <!--/.cartMenu--> 
					</div>
					<!--/.navbar-cart-->";
					
			}
		}
		//*************************************************//
		public function request_total_for_mobile($id_request_order){
				$sql = dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail LEFT JOIN tbl_request_order ON tbl_request_order_detail.id_request_order = tbl_request_order.id_request_order WHERE tbl_request_order_detail.id_request_order = '$id_request_order' AND status=0");
				$row = dbNumRows($sql);
				echo "<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-cart'><i class='fa fa-shopping-cart colorWhite'> </i> 
				<span class='cartRespons colorWhite' id='sumtotalcart_for_mobile'>";if($row<1){ $qty = "ว่างเปล่า";}else{ list($qty) = dbFetchArray($sql); }
				echo "ตะกร้า $qty รายการ</span> </button>";
		}
		//********************************************//
		public function request_sumary($id_request_order){
			$product = new Product();
						$sql = dbQuery("SELECT id_request_order_detail, tbl_request_order_detail.id_request_order, id_product_attribute, qty FROM tbl_request_order_detail LEFT JOIN tbl_request_order ON tbl_request_order_detail.id_request_order = tbl_request_order.id_request_order WHERE tbl_request_order.id_request_order = '$id_request_order' AND status =0");
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							 echo "<div><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>";
						}else{
			echo " <div class='row'>
    <div class='col-lg-12'>
      <div class='row userInfo'>
        <div class='col-xs-12 col-sm-12'>
          <div class='cartContent w100'>
            <table class='cartTable table-responsive' style='width:100%'>
              <tbody>
                <tr class='CartProduct cartTableHeader'>
                  <td style='width:20%'  >ภาพ</td>
                  <td style='width:40%'  >รายระเอียด</td>
                  <td style='width:10%'  class='delete'>&nbsp;</td>
                  <td style='width:10%' >จำนวน</td>
                </tr>
                ";
						$i=0;
						$n=1;
						$sumtotal_cart = 0;
						while($i<$row){
							list($id_request_order_detail, $id_request_order, $id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product);
							$product->product_attribute_detail($id_product_attribute);
							$img = $product->image_attribute;
							$reference = $product->reference;
							$product_price = $product->product_price;
							$sumtotal_cart = $sumtotal_cart + $qty;
                echo "
                <tr class='CartProduct'>
                  <td><img src='$img' class='img-responsive' alt='img'></td>
                  <td><div class='CartDescription'>
                      <h4> ".$product->reference."</h4>
                      <span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span>
                      <div class='price'> <span>".number_format($product_price,2)."</span></div>
                    </div></td>
                  <td class='delete'><a title='Delete' onclick='drop_request_detail($id_request_order,$id_product_attribute)'> <i class='glyphicon glyphicon-trash fa-2x'></i></a></td>
                  <td ><a class='fa fa-plus-square-o' onclick='increase_qty($id_request_order, $id_product_attribute, $n)' style='font-size:16px; color:#009933'></a>&nbsp;<input class='quanitySniper' type='text' id='quantity".$n."' value='$qty' name='quantity".$n."' onkeyup ='update_qty($id_request_order, $id_product_attribute, $n)'>&nbsp;<a class='fa fa-minus-square-o' onclick='decrease_qty($id_request_order, $id_product_attribute, $n)' style='font-size:16px; color:#F00'></a></td>
                </tr>
                ";	
				  		$i++;
						$n++;
						}
						
               echo "
			   <tr>
					<td colspan ='4' align='right' class=' site-color' id='total'><br>รวมทั้งหมด&nbsp;&nbsp; $sumtotal_cart &nbsp;&nbsp;รายการ<br>&nbsp;</td>
                  </tr>
            </table>
          </div>
          <!--cartContent-->
          
          <div>
              <h4 ><a href='index.php?content=order'><i class='fa fa-chevron-left'></i>&nbsp;เลือกสินค้าต่อ </a></h4>     
          </div>
          <div class='col-lg-3 col-md-4 col-sm-6 col-sx-12 col-lg-offset-9 col-md-offset-8 col-sm-offset-6'> <a class='btn btn-block btn-success' href='../controller/orderController.php?confirm_request&id_request_order=$id_request_order'>ยืนยัน</a>
        </div>
      </div>
      <!--/row end-->
	   </div> 
    <!--/rightSidebar--></div> ";
	}
		}
		//*****************************************************************************************//
	
		//*****************************************************************************************//
		public function cartmini_for_mobile($id_cart,$id_customer=0){
			$product = new Product();
			if($id_cart == ""){
				echo "<!-- this part is duplicate from cartMenu  keep it for mobile -->
					<div class='navbar-cart  collapse'>
					  <div class='cartMenu  col-lg-4 col-xs-12 col-md-4 '>
						<div class='w100 miniCartTable scroll-pane'>
						 <table id='mini_cart_for_mobile'>
              </table>
						</div>
						<!--/.miniCartTable-->
						
						<div class='miniCartFooter  miniCartFooterInMobile text-right'>
						  <h3 class='text-right subtotal' id='sumtotalcartin_for_mobile'>  ราคารวม :  </h3>
						  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการสั่งซื้อ</a>
						 </div>
						<!--/.miniCartFooter--> 
						
					  </div>
					  <!--/.cartMenu--> 
					</div>
					<!--/.navbar-cart-->";
			}else{
			echo "<!-- this part is duplicate from cartMenu  keep it for mobile -->
					<div class='navbar-cart  collapse'>
					  <div class='cartMenu  col-lg-4 col-xs-12 col-md-4 '>
						<div class='w100 miniCartTable scroll-pane'>
						 <table id='mini_cart_for_mobile' style='margin-right:10px;'>";
						 $sql = "SELECT id_cart_product, id_cart, tbl_cart_product.id_product_attribute, qty ";
						$sql .= "FROM tbl_cart_product JOIN tbl_product_attribute ON tbl_cart_product.id_product_attribute = tbl_product_attribute.id_product_attribute ";
						$sql .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
						$sql .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
						$sql .= "WHERE id_cart = ".$id_cart." ORDER BY tbl_product_attribute.id_product ASC, tbl_product_attribute.id_color ASC, tbl_size.position ASC, tbl_attribute.position ASC";
						 $sql = dbQuery($sql);
						//$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							$sumtotal_cart1 = "";
						}else{
						$i=0;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_price = $product->product_price;
							//$cart_id_product = $product->id_product;
							//$cart_sell = $product->product_detail($cart_id_product,$id_customer);
							$cart_product_sell = $product->product_sell;
							$cart_total = $qty * $cart_product_sell;
							$sumtotal_cart = $sumtotal_cart + $cart_total;
							$past_im = $product->image_attribute;
							$sumtotal_cart1 = number_format($sumtotal_cart,2);
							$array = $product->getCategoryId($product->id_product);
							$id_cat = array();
							foreach($array as $ar){
								array_push($id_cat,$ar);
							}
							$id_category = max($id_cat);
                echo "<tr class='miniCartProduct'>
                    <td style='width:20%' class='miniCartProductThumb'><div> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'> <img src='$past_im'> </a> </div></td>
                    <td style='width:40%'><div class='miniCartDescription'>
                        <h4> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'> ".$product->reference."</a> </h4>
                        <span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span>
                        <div class='price'> <span> ".number_format($cart_product_sell,2)." </span> </div>
                      </div></td>
                    <td  style='width:5%' class='miniCartQuantity' align='left'><a > $qty </a></td>
                    <td  style='width:20%' class='miniCartSubtotal' align='right' ><span>".number_format($cart_total,2)." ฿ </span></td>
                    <td  style='width:5%' class='delete' align='right' ><a onclick='dropproductcart($id_cart,$id_product_attribute)'><i class='fa fa-trash-o'></i></a></td>
                  </tr>";
				  		$i++;
						}
						}
               echo "</tbody>
              </table>
						</div>
						<!--/.miniCartTable-->
						
						<div class='miniCartFooter  miniCartFooterInMobile text-right'>
						  <h3 class='text-right subtotal'  id='sumtotalcartin_for_mobile'>  ราคารวม : $sumtotal_cart1  </h3>
						  <a class='btn btn-sm btn-primary' href='index.php?content=cart'>ดำเนินการสั่งซื้อ</a></div>
						<!--/.miniCartFooter--> 
						
					  </div>
					  <!--/.cartMenu--> 
					</div>
					<!--/.navbar-cart-->";
					
			}
		}
		public function total_for_mobile($id_cart,$id_customer=0){
			$product = new Product();
			$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							$sumtotal_cart1 = "";
						}else{
						$i=0;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_price = $product->product_price;
							//$cart_id_product = $product->id_product;
							//$cart_sell = $product->product_detail($cart_id_product,$id_customer);
							$cart_product_sell = $product->product_sell;
							$cart_total = $qty * $cart_product_sell;
							$sumtotal_cart = $sumtotal_cart + $cart_total;
							$past_im = $product->image_attribute;
							$sumtotal_cart1 = number_format($sumtotal_cart,2);
							$array = $product->getCategoryId($product->id_product);
							$id_cat = array();
							foreach($array as $ar){
								array_push($id_cat,$ar);
							}
							$id_category = max($id_cat);
				  		$i++;
						}
						}
			echo "<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-cart'><i class='fa fa-shopping-cart colorWhite'> </i> <span class='cartRespons colorWhite' id='sumtotalcart_for_mobile'>";if($id_cart == "0"){ echo "ตะกร้า (ว่างเปล่า)";}else{
			echo "ตะกร้า:Cart($sumtotal_cart1)";}echo "</span> </button>";
		}
		public function cartfull($id_cart,$id_customer=0){
		
						$product = new Product();
						$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart' ORDER BY id_product_attribute ASC");
						$row = dbNumRows($sql);
						if($row == "0"){
							$sumtotal_cart = "";
							$sumtotal_cart1 = "";
							$not = "";
							 echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>";
						}else{
			echo " <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <div class='row userInfo'>
        <div class='col-xs-12 col-sm-12'>
          <div class='cartContent w100'>
            <table class='cartTable table-responsive' style='width:100%'>
              <tbody>
                <tr class='CartProduct cartTableHeader' style='font-size:12px;'>
                  <td style='width:15%'  > รูปภาพ </td>
                  <td style='width:40%'  >รายระเอียด</td>
                  <td style='width:10%'  class='delete'>&nbsp;</td>
                  <td style='width:10%' >จำนวน</td>
                  <td style='width:10%' >ราคาเต็ม</td>
                  <td style='width:15%' >ราคาหักส่วนลด</td>
                </tr>
                ";
						$i=0;
						$n=1;
						$not = "";
						$sumtotal_cart = '';
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_price = $product->product_price;
							$cart_product_sell = $product->product_sell;
							$cart_discout = $product->product_discount;
							$cart_total = $qty * $cart_product_sell;
							$cart_total_not_discount = $qty * $cart_product_price;
							$sumtotal_cart = $sumtotal_cart + $cart_total;
							$past_im = $product->image_attribute;
							$sumtotal_cart1 = number_format($sumtotal_cart,2);
							$quantity = dbFetchArray(dbQuery("select SUM(qty) AS qty from stock_qty where id_product_attribute = '$id_product_attribute'"));
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty'] + $qty_moveing;
							$sumorder_qty = $product->orderQty($id_product_attribute);
							$qty_stock = $qty_in-$sumorder_qty;
							$array = $product->getCategoryId($product->id_product);
							$id_cat = array();
							foreach($array as $ar){
								array_push($id_cat,$ar);
							}
							$id_category = max($id_cat);
							
							if($qty_stock >= "$qty"){
								$tr_color = "";
								
							}else{
								$tr_color = "#FFC0CB";
								echo "<script> alert('".$product->reference." จำนวนคงเหลือ $qty_stock ชิ้น กรุณาแก้ไขจำนวนที่สั่งซื้อ');</script>";
								$not = "1";
							}
                echo "
                <tr class='CartProduct' bgcolor='$tr_color'>
                  <td  class='CartProductThumb'><div> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'><img src='$past_im' alt='img' /></a> </div></td>
                  <td ><div class='CartDescription'>
                      <h4> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'>".$product->reference."</a> </h4>
                      <span class='size'>".$product->color_name."&nbsp;".$product->size_name." <input type='hidden' id='qty_stock' value='$qty_stock'></span>
                      <div class='size'> <span>".number_format($cart_product_price,2)." ลด $cart_discout</span></div>
                    </div></td>
                  <td class='delete'><a title='Delete' onclick='dropproductcartfull($id_cart,$id_product_attribute)'> <i class='glyphicon glyphicon-trash fa-2x'></i></a></td>
                  <td ><a class='fa fa-plus-square-o' onclick='up($id_cart,$id_product_attribute,$n,$qty_stock)' style='font-size:16px; color:#009933'></a>&nbsp;<input class='quanitySniper' type='text' id='quanity".$n."' value='$qty' name='quanity".$n."' onkeyup ='updatecart($id_cart,$id_product_attribute,$n,$qty_stock)'>&nbsp;<a class='fa fa-minus-square-o' onclick='down($id_cart,$id_product_attribute,$n,$qty_stock)' style='font-size:16px; color:#F00'></a></td>
                  <td >".number_format($cart_total_not_discount,2)." ฿</td>
                  <td class='size'>".number_format($cart_total,2)." ฿</td>
                </tr>
                ";	
				  		$i++;
						$n++;
						}
						
               echo "
            </table>
          </div>
          <!--cartContent-->
          
          <div style='margin-top:15px;'>
            <div class='box-footer'>
              <div class='pull-left'> <a href='index.php' class='btn btn-primary'> <i class='fa fa-arrow-left'></i> &nbsp; ซื้อสินค้าต่อ </a></div>
            </div>
          </div> <!--/ cartFooter --> 
          
        </div>
      </div>
      <!--/row end--> ";
	  echo "  </div> <div class='col-lg-3 col-md-3 col-sm-5 rightSidebar'>
      <div class='contentBox' >
        <div class='w100 costDetails'>
          <div class='table-block' id='order-detail-content'> 
            <div class='w100 cartMiniTable'>
              <table id='cart-summary' class='std table'>
                <tbody>
                 ";
                 /*  <tr >
                    <td>ราคาสินค้า</td>
                    <td class='price' >$sumtotal_cart1</td>
                  </tr><tr  style=''>
                    <td>ค่าจัดส่ง</td>
                    <td class='price' ><span class='success'>Free shipping!</span></td>
                  </tr>*/
                  echo "<tr >
                    <td >ราคารวมทั้งหมด</td>
                    <td class=' site-color' id='total-price'>$sumtotal_cart1</td>
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
    <!--/rightSidebar--></div> ";
	$this->sumtotal_cart = $sumtotal_cart1;
	}
	echo "<input type='hidden' id='not' value='$not' >";
		}
	public function checkproductstock($id_cart){
			$product = new Product();
						$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart' ORDER BY id_product_attribute ASC");
						$row = dbNumRows($sql);
						$i=0;
						$not = "";
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							//$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$quantity = dbFetchArray(dbQuery("select SUM(qty) AS qty from stock_qty where id_product_attribute = '$id_product_attribute'"));
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$sumorder_qty = $product->orderQty($id_product_attribute);
							$qty_stock = $qty_in-$sumorder_qty;
							$array = $product->getCategoryId($product->id_product);
							$id_cat = array();
							foreach($array as $ar){
								array_push($id_cat,$ar);
							}
							$id_category = max($id_cat);
							if($qty_stock >= "$qty"){
							}else{
								$not = "1";
							}
				  		$i++;
						}
		return $not;
		}
	public function confirm($id_cart,$id_customer="0"){
			$row = dbNumRows(dbQuery("select id_cart from tbl_cart_product where id_cart = '$id_cart' ORDER BY id_product_attribute ASC"));
			if($row == "0"){
				 //echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>";
			}else{$customer = new customer($id_customer);
			$customer->address($id_customer);
		if($id_customer != "0"){
			
			echo "";
			if($customer->id_address == ""){
			echo "
	  <div class='row'>
		<div class='col-lg-9 col-md-9 col-sm-12'>
		  <div class='row userInfo'>
			<div class='col-xs-12 col-sm-12'>
			  <div class='w100 clearfix'>
				<div class='row userInfo'>
				  <div class='col-lg-12'>
					<h2 class='block-title-2'> ที่อยู่สำหรับจัดส่งสินค้า </h2>
				  </div>            
				  <form method='post' action='controller/cartController.php?add_address=Y'>
					<div class='col-xs-12 col-sm-6'>
					  <div class='form-group required'>
						<label for='InputName'>ชื่อ <sup>*</sup> </label>
						<input required type='text' class='form-control' id='first_name' name='first_name' placeholder='ชื่อ'>
					  </div>
					  <div class='form-group required'>
						<label for='InputLastName'>นามสกุล <sup>*</sup> </label>
						<input required type='text' class='form-control' id='last_name' name='last_name' placeholder='นามสกุล'>
					  </div>
					  <div class='form-group'>
						<label for='InputEmail'>Email </label>
						<input type='text' class='form-control' id='InputEmail' placeholder='Email' value='".$customer->email."' disabled='disabled' >
						<input type='hidden' name='email' value='".$customer->email."' >
					  </div>
					  <div class='form-group'>
						<label for='InputCompany'>เลขประจำตัว </label>
						<input type='text' class='form-control' id='id_number' name='id_number' placeholder='เลขประจำตัว '>
					  </div>
					  <div class='form-group'>
						<label for='InputCompany'>บริษัท </label>
						<input type='text' class='form-control' id='company' name='company' placeholder='บริษัท'>
					  </div>
					  <div class='form-group required'>
						<label for='InputAddress'>ที่อยู่ <sup>*</sup> </label>
						<input required type='text' class='form-control' id='address1' name='address1' placeholder='ที่อยู่ '>
					  </div>
					  <div class='form-group'>
						<label for='InputAddress2'>ที่อยู่บรรทัด 2 </label>
						<input type='text' class='form-control' id='address2' name='address2' placeholder='ที่อยู่บรรทัด 2 '>
					  </div>
					  <div class='form-group required'>
						<label for='InputCity'>จังหวัด<sup>*</sup> </label>
						  <select class='form-control' required aria-required='true' id='city' name='city'> 
						 ";selectCity();echo "
						</select>
					  </div>
					  <div class='form-group required'>
						<label for='InputState'>รหัสไปรษณีย์ <sup>*</sup></label>
				  <input required type='text' class='form-control' id='postcode' name='postcode' placeholder='รหัสไปรษณีย์ '>
			
					  </div>
					</div>
					<div class='col-xs-12 col-sm-6'>
					  <div class='form-group required'>
						<label for='InputZip'>เบอร์โทรศัพท์<sup>*</sup> </label>
						<input required type='text' class='form-control' id='phone' name='phone' placeholder='เบอร์โทรศัพท์'>
					  </div>
					  <div class='form-group required'>
						<label for='InputCountry'>ชื่อสำหรับเรียกที่อยู่ <sup>*</sup> </label>
						  <input required type='text' class='form-control' id='alias' name='alias' placeholder='ชื่อสำหรับเรียกที่อยู่'>
					  </div>
					  <div class='form-group'>
						<label for='InputAdditionalInformation'>อื่นๆ</label>
						<textarea rows='3' cols='26' name='other' class='form-control' id='InputAdditionalInformation'></textarea>
					  </div>
					  <button type='submit' class='btn btn-primary btn-lg' id='check_condition' width='50%'><i class='fa fa-floppy-o'></i>&nbsp;  บันทึก</button>
					</div>
				</div>
				<!--/row end--> 
			</div>
		  </div>
		  <!--/row end--> 
		  
		</div>
	 </div>
		
	  </div> <!--/row-->";
			}else{
			echo "<form method='post' id='confrimorder' action='controller/cartController.php?confirm=Y'>
			<input type='hidden' name='id_cart' id='id_cart' value='$id_cart'>
			<input type='hidden' name='id_customer' id='id_customer' value='".$customer->id_customer."'>
	  <div class='row'>
		<div class='col-lg-9 col-md-9 col-sm-12'>
		  <div class='row userInfo'>
			<div class='col-xs-12 col-sm-12'>
			  <div class='w100 clearfix'>
				<div class='row userInfo'>
				  <div class='col-lg-12'>
					<h2 class='block-title-2'> ที่อยู่สำหรับจัดส่งสินค้า </h2>
				  </div>            
					<div class='col-xs-12 col-sm-6'>
					  <div class='form-group required'>
						<label for='InputName'>ชื่อ  </label>&nbsp;&nbsp;
						".$customer->first_name."
					  </div>
					  <div class='form-group required'>
						<label for='InputLastName'>นามสกุล  </label>&nbsp;&nbsp;
						".$customer->last_name."
					  </div>
					  <div class='form-group'>
						<label for='InputEmail'>Email </label>&nbsp;&nbsp;
						".$customer->email."
					  </div>
					  <div class='form-group'>
						<label for='InputCompany'>เลขประจำตัว </label>&nbsp;&nbsp;
						".$customer->id_number."
					  </div>
					  <div class='form-group'>
						<label for='InputCompany'>บริษัท </label>&nbsp;&nbsp;
						".$customer->company."
					  </div>
					  <div class='form-group required'>
						<label for='InputAddress'>ที่อยู่ </label>&nbsp;&nbsp;
						".$customer->address1."&nbsp;".$customer->address2."
					  </div>
					  <div class='form-group required'>
						<label for='InputState'>รหัสไปรษณีย์ </label>&nbsp;&nbsp;
						".$customer->postcode."
					  </div>
					</div>
					<div class='col-xs-12 col-sm-6'>
					  <div class='form-group required'>
						<label for='InputZip'>เบอร์โทรศัพท์</label>&nbsp;&nbsp;
						".$customer->phone."
					  </div>
					  <div class='form-group required'>
						<label for='InputCountry'>ชื่อสำหรับเรียกที่อยู่  </label>&nbsp;&nbsp;
						  ".$customer->alias."
					  </div>
					  <div class='form-group'>
						<label for='InputAdditionalInformation'>อื่นๆ</label>&nbsp;&nbsp;
						".$customer->other."
					  </div>
					  
					</div>
				</div>
				<!--/row end--> 
			</div>
		  </div>
		  <!--/row end--> 
		</div>    
		</div> 
	  </div> 
	  <input type='hidden' name='sumtotal_cart' id=''sumtotal_cart' value='".$this->sumtotal_cart."' >
	  <!--/row-->
	  ";	if($customer->active == "1"){
				$this->shipping();
				$this->payment($id_customer);
				$this->comment();
				$this->conditions();
	  		}else{
				echo "<div class='alert alert-info'>ขณะนี้เท่าได้อยู่ในระหว่างรออนุมัติจากผู้ดูแลระบบจึงไม่สามารถซื้อสินค้าได้ หรือ ติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการตรวจสอบและอนุมัติต่อไป</div>";
			}
			}
			echo "</from>";
		}else{
			
	echo "<form method='post' action='controller/cartController.php?add_user=Y' >
	<input type='hidden' name='id_cart' value='$id_cart'>
	 <div class='row'>
		<div class='col-lg-9 col-md-9 col-sm-12'>
		  <div class='row userInfo'>
			<div class='col-xs-12 col-sm-12'>
			  <div class='w100 clearfix'>
			  	<div class='row userInfo'>
				  <div class='col-lg-12'>
					<h2 class='block-title-2'> ลูกค้าใหม่ </h2>
				  </div>            
				  <form method='post' action='controller/cartController.php'>
				  <div class='col-xs-12 col-sm-6'>
					 <div class='form-group required'>
						<label for='InputEmail'>อีเมล์ <sup>*</sup></label>
						<input type='text' class='form-control' id='email' name='email' placeholder='อีเมล์' value=''>
					  </div>
					   <div class='form-group required'>
						<label for='InputCompany'>รหัสผ่าน <sup>*</sup></label>
						<input type='password' class='form-control' id='password' name='password' placeholder='รหัสผ่าน'>
					  </div>
					  </div>
					  <div class='col-xs-12 col-sm-6'>
					   <div class='form-group required'>
						<label for='InputName'><sup>*</sup></label>&nbsp; ";getTitleRadio(); echo " 
					  </div>
					  <div class='form-group required'>
						<label for='InputName'>ชื่อ <sup>*</sup> </label>
						<input required type='text' class='form-control' id='first_name' name='first_name' placeholder='ชื่อ'>
					  </div>
					  <div class='form-group required'>
						<label for='InputLastName'>นามสกุล <sup>*</sup> </label>
						<input required type='text' class='form-control' id='last_name' name='last_name' placeholder='นามสกุล'>
					  </div> 
					  <div class='form-group'>
						<label for='InputCompany'>เลขประจำตัว </label>
						<input type='text' class='form-control' id='id_number' name='id_number' placeholder='เลขประจำตัว '>
					  </div>
					   <div class='form-group'>
						<label for='InputCompany'>วันเกิด </label>
						<br/>
						<div class='col-xs-12 col-sm-3' style='margin-left:-18px'><select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectDay(); echo"</select></div>
						<div class='col-xs-12 col-sm-5'><select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectMonth(); echo"</select></div>
						<div class='col-xs-12 col-sm-4'><select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectYear(); echo"</select></div>
					  </div>
				</div>
				<!--/row end--> 
				<div class='row userInfo'>
				  <div class='col-lg-12'>
					<h2 class='block-title-2'> ที่อยู่สำหรับจัดส่งสินค้า </h2>
				  </div>            
					<div class='col-xs-12 col-sm-6'>
					 <div class='form-group'>
						<label for='InputCompany'>บริษัท </label>
						<input type='text' class='form-control' id='company' name='company' placeholder='บริษัท'>
					  </div>
					  <div class='form-group required'>
						<label for='InputAddress'>ที่อยู่ <sup>*</sup> </label>
						<input required type='text' class='form-control' id='address1' name='address1' placeholder='ที่อยู่ '>
					  </div>
					  <div class='form-group'>
						<label for='InputAddress2'>ที่อยู่บรรทัด 2 </label>
						<input type='text' class='form-control' id='address2' name='address2' placeholder='ที่อยู่บรรทัด 2 '>
					  </div>
					  <div class='form-group required'>
						<label for='InputCity'>จังหวัด<sup>*</sup> </label>
						  <select class='form-control' required aria-required='true' id='city' name='city'> 
						 ";selectCity();echo "
						</select>
					  </div>
					  <div class='form-group required'>
						<label for='InputState'>รหัสไปรษณีย์ </label>
				 		 <input required type='text' class='form-control' id='postcode' name='postcode' placeholder='รหัสไปรษณีย์ '>
					  </div><button type='submit' class='btn btn-primary btn-lg' id='postcode' name='postcode' width='50%'><i class='fa fa-floppy-o'></i>&nbsp;  บันทึก</button>
					</div>
					 
					<div class='col-xs-12 col-sm-6'>
					  <div class='form-group required'>
						<label for='InputZip'>เบอร์โทรศัพท์<sup>*</sup> </label>
						<input required type='text' class='form-control' id='phone' name='phone' placeholder='เบอร์โทรศัพท์'>
					  </div>
					  <div class='form-group required'>
						<label for='InputCountry'>ชื่อสำหรับเรียกที่อยู่ <sup>*</sup> </label>
						  <input required type='text' class='form-control' id='alias' name='alias' placeholder='ชื่อสำหรับเรียกที่อยู่'>
					  </div>
					  <div class='form-group'>
						<label for='InputAdditionalInformation'>อื่นๆ</label>
						<textarea rows='3' cols='26' name='other' class='form-control' id='InputAdditionalInformation'></textarea>
					  </div>
					 
					</div>
				</div>
				<!--/row end--> 
			</div>
		  </div>
		  <!--/row end--> 
		
	 </div>	 </div></div>
	  </div> <!--/row-->";
			
		}
						}
	}
	
	//***********************  ยืนยันคำสั่งซื้อสำหรับเซลล์สั่ง *****************************//
	public function sale_confirm($id_cart,$id_customer=0){
			$row = dbNumRows(dbQuery("select id_cart from tbl_cart_product where id_cart = '$id_cart' ORDER BY id_product_attribute ASC"));
			if($row <1){
				 //echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>";
			}else{
				$customer = new customer($id_customer);
				if($customer->active == "1"){
					echo "<form action='controller/cartController.php?confirm=Y' method='post'>";
					echo "<input type='hidden' name='id_customer' value='$id_customer' />";
					echo "<input type='hidden' name='id_cart' value='$id_cart' />";
					$this->shipping();
					$this->payment($id_customer);
					$this->comment();
					$this->conditions();
					echo "</form>";
					}else{
					echo "<div class='alert alert-info'>ยังไม่ได้เลือกลูกค้า กรุณาเลือกลูกค้าโดยคลิกปุ่มเลือกลูกค้าด้านบบขวามือ</div>";
				}
		
			}
	}
	public function shipping()
	{
		echo "<div class='w100 clearfix'>
				<div class='row userInfo'>
				  <div class='col-lg-9'>
					<h2 class='block-title-2' > เลือกตัวเลือกสำหรับจัดส่งสินค้าให้กับที่อยู่ดังกล่าว: ที่อยู่ของฉัน</h2>
				  </div>
				  <div class='col-xs-9 col-sm-9'>
					<div class='w100 row'>
					  <div class='form-group col-lg-12 col-sm-12 col-md-12 -col-xs-12'>
						<table style='width:100%'  class='table-bordered table'>
						  <tbody>
							";
						$sql = dbQuery("select id_shipping,shipping_name,shipping_price,tbl_shipping.default from tbl_shipping where active = '1' ORDER BY id_shipping ASC");
						$row = dbNumRows($sql);
						$i=0;
						$n=1;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_shipping,$shipping_name,$shipping_price,$default_shipping) = dbFetchArray($sql);
							if($default_shipping == "1"){ 
								$checked_shipping = "checked='checked'";
							}else{
								$checked_shipping = "";
							}
							echo "<tr >
							  <td width='70%'><label class='radio'>
								  <input type='radio' name='shipping' id='shipping' style='margin-left:20px; margin-right:10px;' value='$id_shipping' $checked_shipping /> $shipping_name </label></td>
							  <td width='30%' >$shipping_price ฿</td>
							</tr>";
							$i++;
						}
						 echo "</tbody>
						</table>
					  </div>
					</div></div></div></div>";
	}
	public function payment($id_customer)
	{
		$customer = new customer($id_customer);
		$credit_amount = $customer->credit_amount;
		$credit_term = $customer->credit_term;
		if($credit_amount && $credit_term == "0"){	
			$where = "AND id_payment != 3 ";
		}else{
			$where = "";
		}
		echo "<div class='w100 clearfix'>
				<div class='row userInfo'>
				  <div class='col-lg-9'>
					<h2 class='block-title-2'> กรุณาเลือกวิธีการชำระเงินของคุณ</h2>
				  </div>
				  <div class='col-xs-9 col-sm-9'>
					<div class='w100 row'>
					  <div class='form-group col-lg-12 col-sm-12 col-md-12 -col-xs-12'>
						<table style='width:100%'  class='table-bordered table'>
						  <tbody>";
						  $sql = dbQuery("select id_payment,payment_name,tbl_payment.default from tbl_payment where active = '1' $where ORDER BY id_payment ASC");
						$row = dbNumRows($sql);
						$i=0;
						$n=1;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_payment,$payment_name,$default_payment) = dbFetchArray($sql);
							if($default_payment == "1"){ 
								$checked_payment = "checked='checked'";
							}else{
								$checked_payment = "";
							}
							
								echo "<tr >
								  <td ><label class='radio'>
									  <input type='radio' name='payment' id='payment' style='margin-left:20px; margin-right:10px;' value='$id_payment' $checked_payment> $payment_name </label>
								</td>
								</tr>";
							$i++;
						
						}
						 echo "</tbody>
						</table>
					  </div>
					</div></div></div></div>";
	}
	public function comment()
	{
		echo "<div class='w100 clearfix'>
				<div class='row userInfo'>
				  <div class='col-lg-9'>
					<h2 class='block-title-2'> ข้อความของคุณ</h2>
				  </div>
				  <div class='col-xs-9 col-sm-9'>
					<div class='w100 row'>
					  <div class='form-group col-lg-12 col-sm-12 col-md-12 -col-xs-12'>
						<div class='form-group'>
                            หากคุณอยากเพิ่มความคิดเห็นเกี่ยวกับการสั่งซื้อสินค้า โปรดเขียนบันทึกไว้ในช่องด้านล่าง
                            <textarea id='comment' class='form-control' name='comment' cols='26' rows='3'></textarea>
                          </div>
					  </div>
					</div></div></div></div>";
	}
	public function conditions()
	{
		list($value) = dbFetchArray(dbQuery("select value from tbl_config where config_name = 'CONDITION'")); 
	 	echo "<div class='w100 clearfix'>
				<div class='row userInfo'>
				<div class='col-lg-9'>
					<h2 class='block-title-2'> กฎระเบียบของการบริการ</h2>
					</div>
					 <div class='col-xs-9 col-sm-9'>
					<div class='w100 row'>
					 <div class='form-group col-lg-12 col-sm-12 col-md-12 -col-xs-12'>
						<div class='form-group'>
                   
                              <input name='checkboxes' id='checkboxes' value='1' type='checkbox'   onclick='getcondition()' >&nbsp;&nbsp;
                              ฉันยอมรับกฎระเบียบของการบริการและจะปฏิบัติตามอกฎนี้ย่างไม่มีเงื่อนไข <a href='' data-toggle='modal' data-target='#myModal'> (อ่านกฎระเบียบของการบริการ) </a>
					  </div> </div> </div> <div id='checkcondition'><button type='submit' class='btn btn-primary btn-lg' id='check_condition' width='50%' disabled='disabled'><i class='fa fa-arrow-right'></i>&nbsp;   ดำเนินการสั่งซื้อ </button></div></div></div></div>
					<!-- Modal -->
					<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					  <div class='modal-dialog'>
						<div class='modal-content'>
						  <div class='modal-header'>
							<button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
							<h4 class='modal-title' id='myModalLabel'> กฎระเบียบของการบริการ</h4>
						  </div>
						  <div class='modal-body'>
							"; $search = array("\n","\s");
		  $replace = array("<br>","&nbsp;");
		  echo str_replace($search,$replace,$value)."
						  </div>
						  <div class='modal-footer' >
						  </div>
						</div>
					  </div>
					</div>
					";
	}
}



?>