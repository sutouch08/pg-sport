<?php 
	require "../../library/config.php";
	require "../../library/functions.php";	
	if(isset($_GET['edit_address'])){
	$id_address = $_POST['id_address'];
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
	$date_upd = dbDate(date('Y-m-d'));
	if(dbQuery("UPDATE tbl_address SET alias = '$alias', company ='$company', firstname = '$first_name', lastname = '$last_name', address1 ='$address1', address2 = '$address2', city = '$city', postcode ='$postcode', phone = '$phone', id_number = '$id_number', other = '$other', date_upd= '$date_upd' WHERE id_address = $id_address")){
		$message = "แก้ไขที่อยู่เรียบร้อยแล้ว";
		header("location: ../index.php?content=my-address&message=$message");
	}else{
		$message ="แก้ไขที่อยู่ไม่สำเร็จ";
		header("location: ../index.php?content=my-address&error=$message");
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
	$other = $_POST['other'];
	$active = 1;
	$date_add = dbDate(date('Y-m-d'));
	$date_upd = dbDate(date('Y-m-d'));
	$sql= dbQuery("SELECT id_customer FROM tbl_customer WHERE email = '$email'");
	$row = dbNumRows($sql);
	list($id_customer) = dbFetchArray($sql);
	if(dbQuery("INSERT INTO tbl_address( id_customer, alias, company, firstname, lastname, address1, address2, city, postcode, phone, id_number,active, date_add, date_upd,other) VALUES ($id_customer, '$alias', '$company', '$first_name', '$last_name', '$address1', '$address2', '$city', '$postcode', '$phone', '$id_number', $active, '$date_add', '$date_upd','$other')")){
		$message = "เพิ่มที่อยู่เรียบร้อยแล้ว";
		header("location: ../index.php?content=my-address&message=$message");
	}else{
		$message = "เพิ่มที่อยู่ไม่สำเร็จ";
		header("location: ../index.php?content=my-address&error=$message");
	}
}
if(isset($_GET['edit_user'])){
	$id_customer = $_POST['id_customer'];
	$email = $_POST['email'];
	$password = md5($_POST['password']);
	$gender = $_POST['gender'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$day = $_POST['day'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$birthday = "$year-$month-$day";
	dbQuery("update tbl_customer set email = '$email' ,password = '$password' , id_gender = '$gender' , first_name = '$first_name' , last_name = '$last_name' , birthday = '$birthday' where id_customer = '$id_customer'");
	$message = "แก้ไขข้อมูลเรียบร้อยแล้ว";
	header("location: ../index.php?content=user-information&message=$message");
}
if(isset($_GET['reset_password'])&&isset($_POST['email'])){
	$email = trim($_POST['email']);
	$url = getConfig("HOME_PAGE_URL");
	$brand = $company->name;
	$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE email = '$email'");
	$row = dbNumRows($sql);
	echo $url."<br>";
	echo $brand."<br>";
	echo $email."<br>";
	if($row>0){
		list($id_customer) = dbFetchArray($sql);
		$customer = new customer($id_customer);
		$template = "<p>สวัสดี ".$customer->full_name."</p>";
		$template .= "<p>อีเมล์นี้ถูกส่งอัตโนมัติ จาก ".$url."</p>
						<p>คุณได้รับอีเมลนี้เนื่องจากการกู้คืนรหัสผ่าน สำหรับบัญชีของคุณ บนเว็บไซต์ของ $brand </p>
						<p>---------------------------------------------------------------------------------</p>
						<h2 >โปรดระวัง</h2>
						<p>---------------------------------------------------------------------------------</p>
						<p>หากคุณไม่ได้ร้องขอการเปลี่ยนแปลงรหัสผ่านนี้ อย่างดำเนินการใดๆต่อ ให้ลบอีเมล์นี้ทิ้งทันที โปรดดำเนินการต่อในกรณีที่คุณต้องการเปลี่ยนรหัสผ่านของคุณจริงๆ</p>
						<p>---------------------------------------------------------------------------------</p>
						<h2 >โปรดทำตามคำแนะนำด้านล่าง</h2>
						<p>---------------------------------------------------------------------------------</p>
						<p>&nbsp;</p>
						<p>เราต้องการให้คุณ \"ตรวจสอบ\" การกู้คืนรหัสผ่านของคุณ เพื่อแน่ใจว่าคุณเป็นคนดำเนินการกู้คืนเอง วิธีนี้จะช่วยป้องกันบัญชีของคุณจากการกลั่นแกล้งจากผู้ไม่หวังดี</p>
						<p>ง่ายๆ แค่คลิกลิงค์ด้านล่างและกรอกข้อมูลในส่วนที่เหลือ</p><p>&nbsp;</p>
						<p><a target='_blank' href='$url/shop/index.php?content=reset_password&id_customer=".$customer->id_customer."&tokenid=".$customer->password."&email=$email'>$url/shop/index.php?content=reset_password&id_customer=".$customer->id_customer."&tokenid=".$customer->password."</a></p>
						<p>(บางอีเมล์ อาจต้องคัดลอกลิงค์ไปวางในช่องที่อยู่บนเว็บเบราส์เซอร์).</p>
						<p><br>=================================================================================<br></p>";
						$template .= "<p>Hi, ".$customer->full_name."</p>
						<p>This email has been sent from ".$url."</p>
						<p>You have received this email because a password recovery for the user account \"".$email."\" was instigated by you on $brand. </p>
						<p>---------------------------------------------------------------------------------</p>
						<h2 >IMPORTANT!</h2>
						<p>---------------------------------------------------------------------------------</p>
						<p>If you did not request this password change, please IGNORE and DELETE this email immediately. Only continue if you wish your password to be reset!</p>
						<p>---------------------------------------------------------------------------------</p>
						<h2 >Activation Instructions Below</h2>
						<p>---------------------------------------------------------------------------------</p>
						<p>&nbsp;</p>
						<p>We require that you \"validate\" your password recovery to ensure thatyou instigated this action. This protects against unwanted spam and malicious abuse.</p>
						<p>Simply click on the link below and complete the rest of the form</p><p>&nbsp;</p>
						<p><a target='_blank' href='$url/shop/index.php?content=reset_password&id_customer=".$customer->id_customer."&tokenid=".$customer->password."&email=$email'>$url/shop/index.php?content=reset_password&id_customer=".$customer->id_customer."&tokenid=".$customer->password."</a></p>
						<p>(Some email client users may need to copy and paste the link into your web browser).</p>";
						$message = $template;
						echo $message;
						require LIB_ROOT.'class/PHPMailer-master/PHPMailerAutoload.php';
						$mail = new PHPMailer;
						$mail->SMTPDebug = 3;                               // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = 'admin@koolsport.co.th';                 // SMTP username
						$mail->Password = '3310101764121';                           // SMTP password
						$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 587;                                    // TCP port to connect to
						$mail->From = 'admin@koolsport.co.th';
						$mail->FromName = 'KOOL SPORT';
						$mail->addAddress($email, $customer->full_name);     // Add a recipient
						$mail->addAddress($email);               // Name is optional
						$mail->addReplyTo($company->email, 'Information');
						//$mail->addCC('itsupport@koolsport.co.th');
						//$mail->addBCC('bcc@example.com');
						$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
						//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
					//	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
						$mail->isHTML(true);                                  // Set email format to HTML	
						$mail->Subject = 'Password Recovery';
						$mail->Body    = $message;
						//$mail->AltBody = $message;
						
						if(!$mail->send()) {
							//$message = "เกิดข้อผิดพลาด : การเปลี่ยนรหัสผ่านล้มเหลว";
							//header("location: ../index.php?content=forgot_password&error_message=$message&email=$email");
							echo 'Message could not be sent.';
							echo 'Mailer Error: ' . $mail->ErrorInfo;
						} else {
							header("location: ../index.php?content=forgot_password&sent=ok");
						}
					/*	$to = $email;
						$from = $company->email;
						$topic ="Password recovery information from $brand" ;
						$message = $template;
						$header = $brand;
						$result = mail($to,@$topic,$message, $header);
						if($result){
							header("location: ../index.php?content=forgot_password&sent=ok");
						}else{
							$message = "เกิดข้อผิดพลาด : การเปลี่ยนรหัสผ่านล้มเหลว";
							header("location: ../index.php?content=forgot_password&error_message=$message&email=$email");
						}*/
	}else{
		$message = "ไม่พบอีเมล์นี้ในระบบ : Can not find this email";
		header("location: ../index.php?content=forgot_password&error_message=$message&email=$email");					
	}
}

if(isset($_GET['reset_password'])&&isset($_GET['id_customer'])){
		$id_customer = $_GET['id_customer'];
		$password = md5($_POST['new_password']);
		$sql = dbQuery("UPDATE tbl_customer SET password = '$password' WHERE id_customer = $id_customer");
		if($sql){
			header("location: ../index.php");
		}else{
			header("location: ../index.php?content=reset_password&error");
		}
}
		
	?>
	
