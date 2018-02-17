<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<?php
require_once('../invent/function/tools.php');
$id_customer = $_COOKIE['id_customer'];
echo "
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='breadcrumbDiv col-lg-12'>
      <ul class='breadcrumb'>
        <li><a href='index.php'>Home</a> </li>
        <li><a href='index.php?content=account'>บัญชีของฉัน</a> </li>
        <li class='active'> ที่อยู่ของฉัน </li>
      </ul>
    </div>
  </div><!--/.row-->
  
  
  <div class='row'>
  
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'><span><i class='fa fa-map-marker'></i> ที่อยู่ของฉัน </span></h1>
      
      
      <div class='row userInfo'>
      
        <div class='col-lg-12'>
          <h2 class='block-title-2'> ที่อยู่ของคุณจะถูกระบุไว้ด้านล่างคุณสามารถแก้ไขได้</h2>
        ";
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}

   $sql= dbQuery("SELECT id_address,tbl_customer.id_customer, tbl_customer.first_name, tbl_customer.last_name, tbl_customer.email FROM tbl_customer LEFT JOIN tbl_address ON tbl_customer.id_customer = tbl_address.id_customer WHERE tbl_customer.id_customer = '$id_customer'");
list($id_address,$id_customer, $first_name, $last_name, $email) = dbFetchArray($sql);
if($id_address == ""){
	$id_address = 0;
	echo "<form id='address_form' action='controller/accountController.php?add_address=y' method='post'>";
	
}else{
	echo "<form id='address_form' action='controller/accountController.php?edit_address=y' method='post'>
	<input type='hidden' name='id_address' value='$id_address'/>";
}
$data = getAddressDetail($id_address);
echo"
<table width='100%' border='0'>
	<tr><td colspan='3'><input type='hidden' id='email' name='email' value='$email' />
	<input type='hidden' id='first_name' name='first_name' value='$first_name' />
	<input type='hidden' id='last_name' name='last_name' value='$last_name' /></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์ลูกค้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>$email &nbsp; ( $first_name &nbsp; $last_name )</td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เลขประจำตัว :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='id_number' id='id_number' class='form-control input-sm' value='".$data['id_number']."' />
			<span class='help-block'>เลขประจำตัวผู้เสียภาษี หรือ เลขประจำตัวประชาชน </span>	</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อสำหรับเรียกที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='alias' id='alias' class='form-control input-sm' value='".$data['alias']."'  />
			<span class='help-block'>เช่น ที่ทำงาน, บ้าน, ที่อยู่ของฉัน เป็นต้น</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='first_name' id='first_name' class='form-control input-sm' value='".$data['firstname']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' value='".$data['lastname']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='company' id='company' class='form-control input-sm' value='".$data['company']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address1' id='address1' class='form-control input-sm' value='".$data['address1']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่บรรทัด 2 :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address2' id='address2' class='form-control input-sm' value='".$data['address2']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>จังหวัด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<select name='city' id='city' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px'>"; selectCity($data['city']); echo"</td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>รหัสไปรษณีย์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='postcode' id='postcode' class='form-control input-sm' value='".$data['postcode']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เบอร์โทรศัพท์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='phone' id='phone' class='form-control input-sm' value='".$data['phone']."' />
			<span class='help-block'>ต้องมีอย่างน้อย 1 เบอร์</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อื่นๆ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><textarea class='form-control input-sm' name='other' id='other' rows='8'>".$data['other']."</textarea></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<span class='help-block'><sub style='color:red;'>*</sub> ข้อมูลที่จำเป็นต้องกรอก</span></td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' colspan='2' style='padding-bottom:10px; vertical-align:text-top;'align='right' >&nbsp;<button type='submit' class='btn btn-primary btn-lg' id='check_condition' width='50%' ><i class='fa fa-floppy-o'></i>&nbsp;  บันทึก</button></td>
	</tr>
	</table></form>
	
        </div>  
        </div><!--/.w100-->      
        <div class='col-lg-12 clearfix'>
          <ul class='pager'>
            <li class='previous pull-right'><a href='index.php'> <i class='fa fa-home'></i> หน้าหลัก </a></li>
            <li class='next pull-left'><a href='index.php?content=account'>&larr; กลับไปที่บัญชีของฉัน</a></li>
          </ul>
        </div>
        
      </div> <!--/row end--> 
    </div>
    
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
    
  </div> <!--/row-->
  
  <div style='clear:both'></div>
</div> <!-- /.main-container -->";
