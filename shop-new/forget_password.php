<?php
if(isset($_GET['sent'])){
	echo"
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'> <span> <i class='fa fa-unlock-alt'> </i>Reset password link has been sent. </span></h1>
      <div class='row userInfo'>
        <div class='col-xs-12 col-sm-12'>
          <p> Reset password link has been sent to your email please find out your inbox to process next step.</p>
		  <p> ลิงค์รีเซ็ตรหัสผ่านได้ถูกส่งไปยังอีเมลของคุณโปรดดูที่กล่องจดหมายของคุณที่จะดำเนินการขั้นตอนต่อไป</p>
          <div class='clear clearfix'>
            <ul class='pager'>
              <li class='previous pull-right'> <a href='#'  data-toggle='modal' data-target='#ModalLogin'> &larr; Back to Login </a></li>
            </ul>
          </div>
        </div>
      </div>
      <!--/row end-->
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
  </div>
  <!--/row-->
  <div style='clear:both'> </div>
</div>";
}else if(!isset($_GET['error_message'])){
	echo"
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'> <span> <i class='fa fa-unlock-alt'> </i> ลืมรหัสผ่านใช่มั้ย ? </span></h1>
      <div class='row userInfo'>
        <div class='col-xs-12 col-sm-12'>
          <p> กรอกอีเมล์ที่ลงทะเบียนไว้ เราจะส่ง ลิงค์สำหรับเปลี่ยนรหัสผ่าน ไปที่อีเมล์ของคุณ  </p>
          <form action='controller/accountController.php?reset_password=y' method='post'>
            <div class='form-group'>
              <label for='exampleInputEmail1'> Email address </label>
              <input  type='email' class='form-control' name='email' id='email' placeholder='Enter email' style='height:35px;' required='required'/>
            </div>
            <button type='submit' class='btn   btn-primary'> <i class='fa fa-unlock'> </i> ส่งลิงค์ให้ฉัน </button>
          </form>
          <div class='clear clearfix'>
            <ul class='pager'>
              <li class='previous pull-right'> <a href='#'  data-toggle='modal' data-target='#ModalLogin'> &larr; เข้าระบบ </a></li>
            </ul>
          </div>
        </div>
      </div>
      <!--/row end-->
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
  </div>
  <!--/row-->
  <div style='clear:both'> </div>
</div>";
}else{
	$email = $_GET['email'];
	echo"
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'> <span> <i class='fa fa-unlock-alt'> </i> ลืมรหัสผ่านใช่มั้ย ? </span></h1>
      <div class='row userInfo'>
        <div class='col-xs-12 col-sm-12'>
          <p> กรอกอีเมล์ที่ลงทะเบียนไว้ เราจะส่ง ลิงค์สำหรับเปลี่ยนรหัสผ่าน ไปที่อีเมล์ของคุณ </p>
          <form action='controller/accountController.php?reset_password=y' method='post'>
            <div class='form-group'>
              <label for='exampleInputEmail1'> Email address </label>
              <input  type='email' class='form-control' name='email' id='email' placeholder='Enter email' style='height:35px;' value='$email' required='required' autofocus />
			  <label for='exampleInputEmail1'><span style='color:red;'>ไม่พบอีเมล์นี้ในระบบ กรุณาเปลี่ยนอีเมล์แล้วลองใหม่อีกครั้ง</label>
            </div>
            <button type='submit' class='btn   btn-primary'> <i class='fa fa-unlock'> </i> ส่งลิงค์ให้ฉัน </button>
          </form>
          <div class='clear clearfix'>
            <ul class='pager'>
              <li class='previous pull-right'> <a href='#'  data-toggle='modal' data-target='#ModalLogin'> &larr; เข้าระบบ </a></li>
            </ul>
          </div>
        </div>
      </div>
      <!--/row end-->
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
  </div>
  <!--/row-->
  <div style='clear:both'> </div>
</div>";
}
	

	
?>