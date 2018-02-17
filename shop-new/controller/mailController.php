<?php 
	require "../../library/config.php";
	require "../../library/functions.php";
	require_once('../../invent/function/tools.php');
	include SRV_ROOT."library/class/category.php";
	include SRV_ROOT."library/class/cart.php";
	if(isset($_GET['confirm_order'])){
		$id_customer = $_GET['id_customer'];
		$id_order = $_GET['id_order'];
		$customer = new customer($id_customer);
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
						$mail->addAddress($customer->email, $customer->full_name);     // Add a recipient
						$mail->addAddress($customer->email);               // Name is optional
						$mail->addReplyTo($company->email, 'Information');
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
						$mail->addAddress("maojunghi@gmail.com", "TEST");     // Add a recipient
						$mail->addAddress("maojunghi@gmail.com");               // Name is optional
						$mail->addReplyTo("maojunghi@gmail.com", 'Information');
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
	}
	?>
	
