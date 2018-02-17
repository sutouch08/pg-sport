<?php
echo "<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>";

require_once('../invent/function/tools.php');
if(isset($_COOKIE['id_customer'])){
	$id_customer = $_COOKIE['id_customer'];
	$customer = new customer($id_customer);
}
echo "
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='breadcrumbDiv col-lg-12'>
      <ul class='breadcrumb'>
        <li><a href='index.php'>Home</a> </li>
        <li><a href='index.php?content=account'>บัญชีของฉัน</a> </li>
        <li class='active'> ข้อมูลส่วนบุลคล </li>
      </ul>
    </div>
  </div><!--/.row-->
  <div class='row'>
  
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'><span><i class='fa fa-map-marker'></i> ข้อมูลส่วนบุลคล </span></h1>
      <div class='row userInfo'>
        <div class='col-lg-12'>
          <h2 class='block-title-2'> ข้อมูลส่วนบุลคลของคุณจะถูกระบุไว้ด้านล่างคุณสามารถแก้ไขได้</h2>
        </div>";
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
	echo "<form method='post' action='controller/accountController.php?edit_user=y'>
			<input type='hidden' name='id_customer' value='$id_customer' >
				  <div class='col-xs-12 col-sm-6'>
					 <div class='form-group required'>
						<label for='InputEmail'>อีเมล์ <sup>*</sup></label>
						<input type='text' class='form-control' id='email' name='email' value='".$customer->email."' placeholder='อีเมล์' value=''>
					  </div>
					   <div class='form-group required'>
						<label for='InputCompany'>รหัสผ่าน <sup>*</sup></label>
						<input type='password' class='form-control' required id='password' name='password' placeholder='รหัสผ่าน'>
					  </div>
					  </div>
					  <div class='col-xs-12 col-sm-6'>
					   <div class='form-group required'>
						<label for='InputName'><sup>*</sup></label>&nbsp; ";getTitleRadio($customer->id_gender); echo " 
					  </div>
					  <div class='form-group required'>
						<label for='InputName'>ชื่อ <sup>*</sup> </label>
						<input required type='text' class='form-control' id='first_name' name='first_name' value='".$customer->first_name."' placeholder='ชื่อ'>
					  </div>
					  <div class='form-group required'>
						<label for='InputLastName'>นามสกุล <sup>*</sup> </label>
						<input required type='text' class='form-control' id='last_name' name='last_name' value='".$customer->last_name."' placeholder='นามสกุล'>
					  </div> 
					   <div class='form-group'>
						<label for='InputCompany'>วันเกิด </label>
						<br/>
						<div class='col-xs-12 col-sm-3' style='margin-left:-18px'><select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectDay(date('d',strtotime($customer->birthday))); echo"</select></div>
						<div class='col-xs-12 col-sm-5'><select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectMonth(date('m',strtotime($customer->birthday))); echo"</select></div>
						<div class='col-xs-12 col-sm-4'><select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectYear(date('Y',strtotime($customer->birthday))); echo"</select></div>
					  </div>
					   <div class='form-group' align='right'>
					  <button type='submit' class='btn btn-primary btn-lg' id='check_condition' width='50%' ><i class='fa fa-floppy-o'></i>&nbsp;  บันทึก</button>
					  </div>";
      echo "</div><!--/.w100-->      
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
