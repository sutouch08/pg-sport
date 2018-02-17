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
			$id_customer = "";
		}
		$id_cart = $_POST['id_cart'];
		$number = $_POST['number'];
		$qtystock = $_POST['qty'];
		$id_product_attribute = $_POST['id_product_attribute'];
		list($qty,$id_cart_product) = dbFetchArray(dbQuery("SELECT qty,id_cart_product FROM tbl_cart_product where id_product_attribute = '$id_product_attribute' and id_cart = '$id_cart'"));
		if($id_cart_product == ""){
			dbQuery("insert into tbl_cart_product(id_cart,id_product_attribute,qty,date_add)values('$id_cart','$id_product_attribute','$number',NOW())");
			$y = 1;
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
								$product->product_attribute_detail($id_product_attribute);
								$cart_product_price = $product->product_price;
								$cart_id_product = $product->id_product;
								$product->product_detail($cart_id_product,$id_customer);
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
                    <td  style='width:5%' class='delete' align='right' ><a onclick='dropproductcart($id_cart,$id_product_attribute)'> x </a></td>
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
			$id_customer = "";
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
							$product->product_attribute_detail($id_product_attribute);
							$cart_product_price = $product->product_price;
							$cart_id_product = $product->id_product;
							$cart_sell = $product->product_detail($cart_id_product,$id_customer);
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
                    <td  style='width:5%' class='delete' align='right' ><a onclick='dropproductcart($id_cart,$id_product_attribute)'> x </a></td>
                  </tr>";
				  		$i++;
						}
						}
	} 
	if(isset($_GET['checksumtotal'])){
		if(isset($_COOKIE['id_customer'])){
			$id_customer = $_COOKIE['id_customer'];
		}else{
			$id_customer = "";
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
							$product->product_attribute_detail($id_product_attribute);
							$cart_id_product = $product->id_product;
							$cart_sell = $product->product_detail($cart_id_product,$id_customer);
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
			$id_customer = "";
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
			$id_customer = "";
		}
		$product = new product();
		$id_cart = $_GET['id_cart'];
		$id_product_attribute = $_GET['id_product_attribute'];
		$qty = $_GET['qty'];
		list($qty_stock) = dbFetchArray(dbQuery("select SUM(qty) AS qty from stock_qty where id_product_attribute = '$id_product_attribute'"));
		list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
		$qtyorder = $product->orderQty($id_product_attribute);
		$qtystock = ($qty_stock + $qty_moveing) - $qtyorder;
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
		$id_customer = $_GET['id_customer'];
		$customer = new customer($id_customer);
		$id_payment = $_GET['payment'];
		if($id_payment == "3"){
			$to_date = date('Y-m-d');
			$date_order_first = $customer->date_order_first;
			$credit_term = $customer->credit_term;
			$credit_balance = $customer->credit_balance + $_GET['sumtotal_cart'];
			$date_dew = date ("Y-m-d", strtotime("$credit_term day", strtotime($date_order_first)));
			if($to_date > "$date_dew"){
				$true = "";
				$message = "not!คุณมีสินค้าที่เกินกำหนดชำระ กรุณาติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการชำระเงิน";
				echo $message;//header("location: ../index.php?content=cart&error=$message");
			}else if($credit_balance < "0"){
				$true = "";
				$message = "not!คุณมีวงเงินเครดิตของคุณไม่พอสำหรับสั่งออร์เดอร์นี้ กรุณาติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการชำระเงิน";
				echo $message;//header("location: ../index.php?content=cart&error=$message");
			}else{
				$true = "Y";
			}
		}else if($id_payment == "4"){
			$to_date = date('Y-m-d');
			$date_order_first = $customer->date_order_first;
			$credit_term = $customer->credit_term;
			$credit_balance = $customer->credit_balance + $_GET['sumtotal_cart'];
			$date_dew = date ("Y-m-d", strtotime("$credit_term day", strtotime($date_order_first)));
			if($credit_balance < "0"){
				$true = "";
				$message = "not!คุณมีวงเงินเครดิตของคุณไม่พอสำหรับสั่งออร์เดอร์นี้ กรุณาติดต่อเจ้าหน้าที่ดูแลท่านอยู่เพื่อดำเนินการชำระเงิน";
				echo $message;//header("location: ../index.php?content=cart&error=$message");
			}else{
				$true = "Y";
			}
		}else{
			$true = "Y";
		}
		if($true == "Y"){
			$id_cart = $_GET['id_cart'];
			$id_delivery = $_GET['shipping'];
			$comment = $_GET['comment'];
			//ถ้าเพิ่มค่าส่งมาแก้ตรงนี้
			$shipping_number = 0;
			$invoice_number = 0;
			$delivery_number = 0;
			list($payment) = dbFetchArray(dbQuery("select payment_name from tbl_payment where id_payment = '$id_payment'"));
			$valid = 0;
			$reference = get_max_role_reference("PREFIX_ORDER","1");
			list($id_sale) = dbFetchArray(dbQuery("select id_sale from tbl_customer where id_customer = '$id_customer'"));
			dbQuery("insert into tbl_order (reference,id_customer,id_cart,id_address_delivery,current_state,payment,shipping_number,invoice_number,delivery_number,delivery_date,comment,valid,date_add) values ('$reference','$id_customer','$id_cart','$id_delivery','1','$payment','$shipping_number','$invoice_number','$delivery_number','','$comment','$valid',NOW())");
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
						$product->product_detail($id_product,$id_customer);
						$product->product_attribute_detail($id_product_attribute);
						$product_name = $product->product_name;
						$product_referenec = $product->reference;
						$barcode = $product->barcode;
						$product_price = $product->product_price;
						if($product->discount_type == "percentage"){
							$reduction_parcent = $product->product_discount1;
							$reduction_amount = 0;
						}else if($product->discount_type == "amount"){
							$reduction_parcent = 0;
							$reduction_amount = $product->product_discount1;
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
						//--------------------------------email สำหรับลูกค้า------------------------------//
						$order = new order($id_order);
						$customer->address($id_customer);
						$url = getConfig("HOME_PAGE_URL");
						$template = "<p>สวัสดี ".$customer->full_name." เลขที่คำสั่งซื้อของท่าน คือ ".$order->reference."</p>";
						$template .= "<p>อีเมล์นี้ถูกส่งอัตโนมัติ จาก ".$url."</p>
						<p>------------------------------------------------------------------------------------------------</p>
						<p>ข้อมูลผู้สั่งซื้อ</p>
						<table>
						<tr><td>ชื่อผู้ซื้อ : </td><td>".$customer->full_name."</td></tr>
						<tr><td>เบอร์โทร : </td><td>".$customer->phone."</td></tr>
						<tr><td>อีเมล : </td><td>".$customer->email."</td></tr>
						<tr><td>ที่อยู่ในการจัดส่ง : </td><td>".$customer->address1." ".$customer->address2." ".$customer->city." ".$customer->postcode."</td></tr>
						</table>
						<p>------------------------------------------------------------------------------------------------</p>";
		$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$template .= "<table id='product_table' border='0'  class='table' style='width:900px; padding:10px; border: 1px; solid #ccc;'><tr height='50px' bgcolor='#CCCCCC'><td style='width:10%'>รูปภาพ</td><td style='width:50%'>สินค้า</td><td style='width:10%; text-align:center;'>ราคา</td><td style='width:10%; text-align:center;'>จำนวน</td><td style='width:20%; text-align:center;'>จำนวนเงิน</td></tr>";
			$discount ="";
			$amount = "";
			$total_amount = "";
			while($i = dbFetchArray($sql)){
				$product = new product();
				$total = $i['product_price']*$i['product_qty'];
				$template .="<tr height='50px' bgcolor='#E8E8E8'>
				<td style='text-align:center; vertical-align:middle;'><img src='$url".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_price'],2)."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_qty'])."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['total_amount'],2)."</td>
				</tr>";
					$discount += $i['discount_amount'];
					$total_amount += $total;
					$amount += $i['total_amount'];
			}
			$template .="<tr height='30px' bgcolor='#E8E8E8'>
			<td rowspan='3' colspan='2'></td>
			<td colspan='2' align='right'><b>สินค้า</b></td><td align='right' bgcolor='#E8E8E8'><b>".number_format($total_amount,2)." ฿</b></td></tr>
			<tr height='30px' bgcolor='#E8E8E8'><td colspan='2' align='right'><b>ส่วนลด</b></td><td align='right'><b>".number_format($discount,2)." ฿</b></td></tr>
			<tr height='30px' bgcolor='#E8E8E8'><td colspan='2' align='right'><b>สุทธิ </b></td><td align='right'><b>".number_format($amount,2)." ฿</b></td></tr></table>
			<p>------------------------------------------------------------------------------------------------</p>";
		list($value) = dbFetchArray(dbQuery("select value from tbl_config where id_config = 13"));
		  $search = array("\n","\s");
		  $replace = array("<br>","&nbsp;");
		  $template .="วิธีการชำระเงิน". str_replace($search,$replace,$value)."
		  <p>---------------------------------------------------------------------------------------------------</p>";
		  $template .="แจ้งการชำระเงินได้ที่";
		  $template .="<p><a target='_blank' href='$url/shop/index.php?content=payment&id_customer=".$customer->id_customer."'>$url/shop/index.php?content=payment&id_customer=".$customer->id_customer."</a></p>
		  <p>---------------------------------------------------------------------------------------------------</p>";
		  
						$message = $template;
						//echo $message;
						require LIB_ROOT.'class/PHPMailer-master/PHPMailerAutoload.php';
						$mail = new PHPMailer;
						$mail->SMTPDebug = 3;                               // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = 'itsupport@koolsport.co.th';                 // SMTP username
						$mail->Password = 'koolsport';               // SMTP password
						$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 587;                                    // TCP port to connect to
						$mail->From = 'itsupport@koolsport.co.th';
						$mail->FromName = $company->name;
						$mail->addAddress("maojunghi@gmail.com", $customer->full_name);     // Add a recipient
						$mail->addAddress("maojunghi@gmail.com");               // Name is optional
						$mail->addReplyTo("maojunghi@gmail.com", 'Information');
						//$mail->addCC('itsupport@koolsport.co.th');
						//$mail->addBCC('bcc@example.com');
						$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
						//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
						//	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
						$mail->isHTML(true);                                  // Set email format to HTML	
						$mail->Subject = 'Order';
						$mail->Body = $message;
						//$mail->AltBody = $message;
						$mail->send();
						
						
					//------------------------------------------------สำหรับพนักงาน-------------------------------------------//
						$template_employee = "<p>NEW ORDER</p>";
						$template_employee .= "<p>อีเมล์นี้ถูกส่งอัตโนมัติ จาก ".$url."</p>
						<p>------------------------------------------------------------------------------------------------</p>
						<p>ข้อมูลผู้สั่งซื้อ</p>
						<table>
						<tr><td>ชื่อผู้ซื้อ : </td><td>".$customer->full_name."</td></tr>
						<tr><td>เบอร์โทร : </td><td>".$customer->phone."</td></tr>
						<tr><td>อีเมล : </td><td>".$customer->email."</td></tr>
						<tr><td>ที่อยู่ในการจัดส่ง : </td><td>".$customer->address1." ".$customer->address2." ".$customer->city." ".$customer->postcode."</td></tr>
						</table>
						<p>------------------------------------------------------------------------------------------------</p>";
		$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$template_employee .= "<table id='product_table' border='0'  class='table' style='width:900px; padding:10px; border: 1px; solid #ccc;'><tr height='50px' bgcolor='#CCCCCC'><td style='width:10%'>รูปภาพ</td><td style='width:50%'>สินค้า</td><td style='width:10%; text-align:center;'>ราคา</td><td style='width:10%; text-align:center;'>จำนวน</td><td style='width:20%; text-align:center;'>จำนวนเงิน</td></tr>";
			$discount ="";
			$amount = "";
			$total_amount = "";
			while($i = dbFetchArray($sql)){
				$product = new product();
				$total = $i['product_price']*$i['product_qty'];
				$template_employee .="<tr height='50px' bgcolor='#E8E8E8'>
				<td style='text-align:center; vertical-align:middle;'><img src='$url".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_price'],2)."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_qty'])."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['total_amount'],2)."</td>
				</tr>";
					$discount += $i['discount_amount'];
					$total_amount += $total;
					$amount += $i['total_amount'];
			}
			$template_employee .="<tr height='30px' bgcolor='#E8E8E8'>
			<td rowspan='3' colspan='2'></td>
			<td colspan='2' align='right'><b>สินค้า</b></td><td align='right' bgcolor='#E8E8E8'><b>".number_format($total_amount,2)." ฿</b></td></tr>
			<tr height='30px' bgcolor='#E8E8E8'><td colspan='2' align='right'><b>ส่วนลด</b></td><td align='right'><b>".number_format($discount,2)." ฿</b></td></tr>
			<tr height='30px' bgcolor='#E8E8E8'><td colspan='2' align='right'><b>สุทธิ </b></td><td align='right'><b>".number_format($amount,2)." ฿</b></td></tr></table>
			<p>------------------------------------------------------------------------------------------------</p>";
		//list($value) = dbFetchArray(dbQuery("select value from tbl_config where id_config = 13"));
		 // $search = array("\n","\s");
		 // $replace = array("<br>","&nbsp;");
		 //$template .="วิธีการชำระเงิน". str_replace($search,$replace,$value)."
		 // <p>---------------------------------------------------------------------------------------------------</p>";
		  $template_employee .="ไปที่รายการสั่งซื้อ<a target='_blank' href='$url/invent/index.php?content=order&edit=y&id_order=".$id_order."&view_detail=y'>คลิก ".$order->reference."</a>
		  <p>---------------------------------------------------------------------------------------------------</p>";
		  
						$message_employee = $template_employee;
						//echo $message;
					//.	require LIB_ROOT.'class/PHPMailer-master/PHPMailerAutoload.php';
						//$mail = new PHPMailer;
						$mail->SMTPDebug = 3;                               // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = 'itsupport@koolsport.co.th';                 // SMTP username
						$mail->Password = 'koolsport';               // SMTP password
						$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 587;                                    // TCP port to connect to
						$mail->From = 'itsupport@koolsport.co.th';
						$mail->FromName = $company->name;
						$email_to_arr = $company->email_to_neworder;
						$arr = explode(",", $email_to_arr);
						foreach($arr as $email_to){
							$mail->addAddress($email_to);               // Name is optional
							$mail->addReplyTo($email_to, 'Information');
						}
						//$mail->addAddress("maojunghi@gmail.com", "TEST");     // Add a recipient
						
						//$mail->addCC('itsupport@koolsport.co.th');
						//$mail->addBCC('bcc@example.com');
						$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
						//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
						//	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
						$mail->isHTML(true);                                  // Set email format to HTML	
						$mail->Subject = 'NEW ORDER';
						$mail->Body = $message_employee;
						//$mail->AltBody = $message;
						$mail->send();
					// echo "<div id='result'><h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table></div>";
							
				}
		}
	}
	if(isset($_GET['checkproductstock'])){
		$id_cart = $_COOKIE['id_cart'];
		echo $cart->checkproductstock($id_cart);
	}
	
	
if(isset($_GET['add_to_cart'])){
	$id_cart = $_POST['id_cart'];
	$id_customer = $_POST['id_customer'];
	$order_qty = $_POST['qty'];
	if($id_cart == ""){
		$sql = dbQuery("INSERT INTO tbl_cart (id_customer, date_add) VALUES ($id_customer, NOW())");
		$id = dbInsertId($sql);
		$id_cart = $id;
		setcookie("id_cart", $id_cart, time() + 36000000,"/");
	}
	$msg = " | รายการต่อไปนี้ไม่ถูกเพิ่มลงตะกร้า เนื่องจากมียอดคงเหลือไม่พอ | ";
	foreach($order_qty as $id_product_attribute => $qty)
	{
		if($qty != "")
		{
			$sql = dbQuery("SELECT id_cart_product, qty FROM tbl_cart_product WHERE id_cart = ".$id_cart." AND id_product_attribute = ".$id_product_attribute);
			$row = dbNumRows($sql);
			if($row >0){
				list($id_cart_product, $old_qty) = dbFetchArray($sql);
				$new_qty = $old_qty + $qty;
				$product = new product();
				$a_qty = $product->available_order_qty($id_product_attribute);
				$filter = $product->maxShowstock();
				if($filter == 0 || $filter ==""){ $filter = 100000000000; } /// กำหนดสต็อกฟิลว์เตอร์
				if($a_qty >$filter){ $a_qty = $filter; }
				if($new_qty > $a_qty){
					$reference = $product->get_product_reference($id_product_attribute);
					$msg .= " ".$reference;
				}else{					
					dbQuery("UPDATE tbl_cart_product SET qty = ".$new_qty." WHERE id_cart_product = ".$id_cart_product);
				}
				
			}else{
				dbQuery("INSERT INTO tbl_cart_product (id_cart, id_product_attribute, qty, date_add) VALUES ( $id_cart, $id_product_attribute, $qty, NOW())");
			}
		}
	}
	echo "success".$msg;
	//header("location: ../index.php?content=order");
}
	?>
	
