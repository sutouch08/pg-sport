<?php 
	require "../../library/config.php";
	require "../../library/functions.php";
	require_once('../../invent/function/tools.php');
	include SRV_ROOT."library/class/category.php";
	include SRV_ROOT."library/class/cart.php";
	//$id_customer = $_COOKIE['id_customer'];
	$cart = new cart();
if(isset($_GET['newcart'])){
		$cart->newCart();
		$id = $cart->id_cart;
		echo $id;
}


if(isset($_GET['addtocart'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = 0;
		}
		$id_cart = $_POST['id_cart'];
		$number = $_POST['number'];
		$qtystock = $_POST['qty'];
		$id_product_attribute = $_POST['id_product_attribute'];
		list($qty,$id_cart_product) = dbFetchArray(dbQuery("SELECT qty,id_cart_product FROM tbl_cart_product where id_product_attribute = '$id_product_attribute' and id_cart = '$id_cart'"));
		if($id_cart_product == ""){
			if($id_cart >0 || $id_product_attribute >0){
			dbQuery("insert into tbl_cart_product(id_cart,id_product_attribute,qty,date_add)values('$id_cart','$id_product_attribute','$number',NOW())");
			$y = 1;
			}
		}else{
			$sum_qty = $qty + $number;
			if($sum_qty <= "$qtystock"){
				dbQuery("UPDATE tbl_cart_product set qty = '$sum_qty' where id_product_attribute = '$id_product_attribute' and id_cart = '$id_cart'");
				$y = 1;
			}else{
				$y = 0;
			}
		}
		if($y == "1"){
		$product = new product();		
		$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$row = dbNumRows($sql);
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
                echo "<tr class='miniCartProduct'>
                    <td style='width:20%' class='miniCartProductThumb'><div> <a href='product-details.html'> <img src='$past_im'> </a> </div></td>
                    <td style='width:40%'><div class='miniCartDescription'>
                        <h4> <a href='product-details.html'> ".$product->reference."</a> </h4>
                        <span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span>
                        <div class='price'> <span> ".number_format($cart_product_sell,2)." </span> </div>
                      </div></td>
                    <td  style='width:5%' class='miniCartQuantity' align='left'><a > $qty </a></td>
                    <td  style='width:20%' class='miniCartSubtotal' align='right' ><span>".number_format($cart_total,2)." ฿ </span></td>
                    <td  style='width:5%' class='delete' align='right' ><a onclick='dropproductcart($id_cart,$id_product_attribute)'><i class='fa fa-trash-o'></i></a></td>
                  </tr>";
				  		$i++;
						}
		}else{
			echo "1";
		}
}


if(isset($_GET['dropproductcart'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = 0;
		}
		$id_cart = $_GET['id_cart'];
		$id_product_attribute = $_GET['id_product_attribute'];
		dbQuery("DELETE FROM tbl_cart_product where id_cart = '$id_cart' and id_product_attribute = '$id_product_attribute'");
		$product = new product();		
		$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$row = dbNumRows($sql);
						if($row == "0"){
							
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
                echo "<tr class='miniCartProduct'>
                    <td style='width:20%' class='miniCartProductThumb'><div> <a href='product-details.html'> <img src='$past_im'> </a> </div></td>
                    <td style='width:40%'><div class='miniCartDescription'>
                        <h4> <a href='product-details.html'> ".$product->reference."</a> </h4>
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
} 


if(isset($_GET['checksumtotal'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = 0;
		}
		$id_cart = $_GET['id_cart'];
		$product = new product();
		$sql = dbQuery("select id_cart_product,id_cart,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart' ");
						$row = dbNumRows($sql);
						if($row == "0"){
							echo "ว่างเปล่า";
						}else{
						$i=0;
						$sumtotal_cart = '';
						while($i<$row){
							list($id_cart_product,$id_cart,$id_product_attribute,$qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_sell = $product->product_sell;
							$cart_total = $qty * $cart_product_sell;
							$sumtotal_cart = $sumtotal_cart + $cart_total;
							$sumtotal_cart1 = number_format($sumtotal_cart,2);
							$i++;
						}
						echo $sumtotal_cart1;
						}
}


if(isset($_GET['dropproductcartfull'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = 0;
		}
		$id_cart = $_GET['id_cart'];
		$id_product_attribute = $_GET['id_product_attribute'];
		dbQuery("DELETE FROM tbl_cart_product where id_cart = '$id_cart' and id_product_attribute = '$id_product_attribute'");
		echo $cart->cartfull($id_cart,$id_customer);
}


if(isset($_GET['updateproductcartfull'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = 0;
		}
		$product = new product();
		$id_cart = $_GET['id_cart'];
		$id_product_attribute = $_GET['id_product_attribute'];
		$qty = $_GET['qty'];
		list($qty_stock) = dbFetchArray(dbQuery("select SUM(qty) AS qty from stock_qty where id_product_attribute = '$id_product_attribute'"));
		$qtyorder = $product->orderQty($id_product_attribute);
		$qtystock = $qty_stock - $qtyorder;
		if($qtystock >= "$qty"){ 
			dbQuery("UPDATE tbl_cart_product SET qty = '$qty' WHERE id_cart = '$id_cart' and id_product_attribute = '$id_product_attribute'");
			echo $cart->cartfull($id_cart,$id_customer);
		}else{
			dbQuery("UPDATE tbl_cart_product SET qty = '$qtystock' WHERE id_cart = '$id_cart' and id_product_attribute = '$id_product_attribute'");
			echo $cart->cartfull($id_cart,$id_customer);
		}
}


if(isset($_GET['add_address'])){
		$email = $_POST['email'];
		if(isset($_POST['company'])){ $company = $_POST['company'];}else{ $company = "";}
		if(isset($_POST['id_number'])){ $id_number = $_POST['id_number'];}else{ $id_number = "";}
		if(isset($_POST['address2'])){ $address2 = $_POST['address2'];}else{ $address2 = "";}
		if(isset($_POST['postcode'])){ $postcode = $_POST['postcode'];}else{ $postcode = "";}
		if(isset($_POST['other'])){ $other = $_POST['other'];}else{ $other = "";}
		$alias = $_POST['alias'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$address1 = $_POST['address1'];
		$city = $_POST['city'];
		$phone = $_POST['phone'];
		$active = 1;
		$date_add = dbDate(date('Y-m-d'));
		$date_upd = dbDate(date('Y-m-d'));
		$sql= dbQuery("SELECT id_customer FROM tbl_customer WHERE email = '$email'");
		$row = dbNumRows($sql);
		list($id_customer) = dbFetchArray($sql);
		dbQuery("INSERT INTO tbl_address( id_customer, alias, company, firstname, lastname, address1, address2, city, postcode, phone, id_number,active, date_add, date_upd,other) VALUES ($id_customer, '$alias', '$company', '$first_name', '$last_name', '$address1', '$address2', '$city', '$postcode', '$phone', '$id_number', $active, '$date_add', '$date_upd', '$other')");
		header("location: ../index.php?content=cart");
	}
	if(isset($_GET['add_user'])){
		
		$id_cart = $_POST['id_cart'];
			if(isset($_POST['gender'])){ $gender = $_POST['gender'];}else{ $gender = 0; }
		$id_default_group = "1";
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$password = md5($_POST['password']);
		$birthday = dbDate($_POST['day']."-".$_POST['month']."-".$_POST['year']);
		$active = $company->default_active;
		$date_add = date('Y-m-d');
		$date_upd = date("Y-m-d");
		$group_checked = 1;
		if(isset($_POST['company'])){ $company = $_POST['company'];}else{ $company = "";}
		if(isset($_POST['id_number'])){ $id_number = $_POST['id_number'];}else{ $id_number = "";}
			if(isset($_POST['address2'])){ $address2 = $_POST['address2'];}else{ $address2 = "";}
			if(isset($_POST['postcode'])){ $postcode = $_POST['postcode'];}else{ $postcode = "";}
			if(isset($_POST['other'])){ $other = $_POST['other'];}else{ $other = "";}
			$alias = $_POST['alias'];
			$address1 = $_POST['address1'];
			$city = $_POST['city'];
			$phone = $_POST['phone'];
		$checked = dbNumRows(dbQuery("SELECT email FROM tbl_customer WHERE email = '$email'"));
		if($checked >0){
			$message = "อีเมล์ซ้ำ มีอีเมล์นี้ในระบบแล้ว";
			header("location: ../index.php?content=cart&error=$message");
		}else if($checked <1){
			if(dbQuery("INSERT INTO tbl_customer(id_default_group, id_sale, id_gender, first_name, last_name, email, password, birthday, credit_amount, credit_term, active, date_add, date_upd) VALUES ($id_default_group, 0, $gender, '$first_name', '$last_name', '$email', '$password', '$birthday', '0', '0', '$active', '$date_add', '$date_upd')")){
				$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name = '$first_name' AND last_name = '$last_name' AND email = '$email'");
				list($id_customer) = dbFetchArray($sql); 
				setcookie("id_customer",$id_customer,time()+(3600*24*30),'/');
				setcookie("customer_name",$first_name."&nbps".$last_name,time()+(3600*24*30),'/');
				dbQuery("INSERT INTO tbl_customer_group(id_customer, id_group) VALUES ($id_customer, 1)");
				dbQuery("INSERT INTO tbl_address( id_customer, alias, company, firstname, lastname, address1, address2, city, postcode, phone, id_number,active, date_add, date_upd,other) VALUES ('$id_customer', '$alias', '$company', '$first_name', '$last_name', '$address1', '$address2', '$city', '$postcode', '$phone', '$id_number', '$active', '$date_add', '$date_upd', '$other')");
				dbQuery("update tbl_cart set id_customer = '$id_customer' where id_cart = '$id_cart'");
					header("location: ../index.php?content=cart");
				}else{
					$message = "เพิ่มลูกค้าไม่สำเร็จ";
					header("location: ../index.php?content=cart&error=$message");
				}
		}
	}
	if(isset($_GET['confirm'])){
		$product = new product();
		$id_customer = $_POST['id_customer'];
		$id_payment = $_POST['payment'];
		if($id_payment == "3"){
			$customer = new customer($id_customer);
			$to_date = date('Y-m-d');
			$date_order_first = $customer->date_order_first;
			$credit_term = $customer->credit_term;
			if($customer->credit_amount >0){ 			
				$credit_balance = $customer->credit_balance + $_POST['sumtotal_cart'];
			/*	$date_dew = date ("Y-m-d", strtotime("$credit_term day", strtotime($date_order_first)));
			if($to_date > "$date_dew"){
				$message = "คุณมีสินค้าาที่เกินกำหนดชำระ กรุณาติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการชำระเงิน <a href='index.php?content=order'>คลิกรายการสั่งซื้อสินค้า</a>";
				header("location: ../index.php?content=cart&error=$message");
			}else */if($credit_balance < "0"){
				$message = "คุณมีวงเงินเครดิตของคุณไม่พอสำหรับสั่งออร์เดอร์นี้ กรุณาติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการชำระเงิน <a href='index.php?content=order'>คลิกรายการสั่งซื้อสินค้า</a>";
				header("location: ../index.php?content=cart&error=$message");
			}else{
				$true = "Y";
			}
			}else{ 
			$true = "Y";
			}
		
		}else{
			$true = "Y";
		}
		if($true == "Y"){
		$id_cart = $_POST['id_cart'];
		$id_delivery = $_POST['shipping'];
		$comment = $_POST['comment'];
		//ถ้าเพิ่มค่าส่งมาแก้ตรงนี้
		$shipping_number = 0;
		$invoice_number = 0;
		$delivery_number = 0;
		
		list($payment) = dbFetchArray(dbQuery("select payment_name from tbl_payment where id_payment = '$id_payment'"));
		$valid = 0;
		$reference = get_max_role_reference("PREFIX_ORDER","1");
		list($id_sale) = dbFetchArray(dbQuery("select id_sale from tbl_customer where id_customer = '$id_customer'"));
		list($id_employee) = dbFetchArray(dbQuery("select id_employee from tbl_sale where id_sale = '$id_sale'"));
		dbQuery("insert into tbl_order (reference,id_customer,id_employee,id_cart,id_address_delivery,current_state,payment,shipping_number,invoice_number,delivery_number,delivery_date,comment,valid,date_add) values ('$reference','$id_customer','$id_employee','$id_cart','$id_delivery','1','$payment','$shipping_number','$invoice_number','$delivery_number','','$comment','$valid',NOW())");
		list($id_order) = dbFetchArray(dbQuery("select id_order from tbl_order where reference = '$reference' and id_customer = '$id_customer'"));
		dbQuery("INSERT INTO tbl_order_state_change (id_order, id_order_state, id_employee, date_add) VALUES ($id_order,1,0,NOW())");
		$sql = dbQuery("select id_cart_product,id_product_attribute,qty from tbl_cart_product where id_cart = '$id_cart'");
						$row = dbNumRows($sql);
						if($row == "0"){
						}else{
						$i=0;
						while($i<$row){
							list($id_cart_product,$id_product_attribute,$product_qty) = dbFetchArray($sql);
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product, $id_customer);
							$product->product_attribute_detail($id_product_attribute);
							$product_name = $product->product_name;
							$product_referenec = $product->reference;
							$barcode = $product->barcode;
							$product_price = $product->product_price;
							if($product->discount_type == "percentage"){
								$reduction_parcent = $product->product_discount;
								$reduction_amount = 0;
							}else if($product->discount_type == "amount"){
								$reduction_parcent = 0;
								$reduction_amount = $product->product_discount;
							}else if($product->discount_type == "cus_percentage"){
								$reduction_parcent = $product->get_max_discount($id_product,$id_customer);
								$reduction_amount = 0;
							}else if($product->discount_type == ""){
								$reduction_parcent = 0;
								$reduction_amount = 0;
							}
							$final_price = $product->product_sell;
							$total_amount = $product_qty * $final_price;
							$discount_amount = ($product_price *$product_qty)-$total_amount;
							dbQuery("insert into tbl_order_detail (id_order,id_product,id_product_attribute,product_name,product_qty,product_reference,barcode,product_price,reduction_percent,reduction_amount,discount_amount,final_price,total_amount,valid_detail) values ('$id_order','$id_product','$id_product_attribute','$product_name','$product_qty','$product_referenec','$barcode','$product_price','$reduction_parcent','$reduction_amount','$discount_amount','$final_price','$total_amount','0')");
							$i++;
						}
						setcookie("id_customer","",time()-3600,"/");
						setcookie("id_cart","",time()-3600,"/");
						?>
                        <script >
						function createCookie(name,value,days) {
							if (days) {
								var date = new Date();
								date.setTime(date.getTime()+(days*24*60*60*1000));
								var expires = "; expires="+date.toGMTString();
							}
							else var expires = "";
							document.cookie = name+"="+value+expires+"; path=/";
							window.location="../index.php?content=cart&finish=Y";
						}
						createCookie("id_cart","",-1);
						</script>
                        <?php 
						}
		}
	}
	if(isset($_GET['checkproductstock'])){
		$id_cart = $_COOKIE['id_cart'];
		echo $cart->checkproductstock($id_cart);
	}
	?>
	
