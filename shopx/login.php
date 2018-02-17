<!-- Modal Login start -->
<div class="modal signUpContent fade" id="ModalLogin" tabindex="-1" role="dialog" >
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title-site text-center" > เข้าระบบ </h3>
      </div><form  id="Login_form" method="post"  >
      <div class="modal-body">
        <div class="form-group login-username">
          <div >
            <input name="user_email" id="login-user" class="form-control input"  size="20" placeholder="Enter Email" type="email" required="required">
          </div>
        </div>
        <div class="form-group login-password">
          <div >
            <input name="user_password" id="login-password" class="form-control input"  size="20" placeholder="Password" type="password" required="required">
          </div>
        </div>
        <div class="form-group">
          
            <div class="checkbox login-remember">
                <input name="rememberme" id="rememberme" value="forever" checked="checked" type="checkbox" style="margin-left:0px; margin-right:5px;">
                <label for="rememberme">จำฉัน ไว้ในระบบ </label>
            </div>
          
        </div>
        <div >
          <div >
            <input id="login" class="btn  btn-block btn-lg btn-primary" value="เข้าระบบ" type="button" onclick="check_login()">
          </div>
        </div></form>
        <!--userForm--> 
        
      </div>
        <p style="text-align:center; color:red;" id="message"></p>
      <div class="modal-footer">
        <p class="text-center"><a href="index.php?content=forgot_password" > ลืมรหัสผ่าน? </a> </p>
      </div>
    </div>
    <!-- /.modal-content --> 
    
  </div>
  <!-- /.modal-dialog --> 
  
</div>
<!-- /.Modal Login --> 
<!-- Modal Signup start -->
<div class="modal signUpContent fade" id="ModalSignup" tabindex="-1" role="dialog" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title-site text-center" > สมัครสมาชิก </h3>
      </div>
      <form method="post" id="new_user" action="controller/loginController.php?new_user=y" >
      <div class="modal-body">
        <div class="form-group required">
          <div >
          <label for='InputName'>Email <sup>*</sup> </label>
            <input name="EMAIL" id='EMAIL' class="form-control input"  size="20" placeholder="Enter Email" type="text" required >
          </div>
        </div>
        <div class="form-group required">
          <div >
          <label for='InputName'>Password <sup>*</sup> </label>
            <input name="PASSWORD" id="PASSWORD"  class="form-control input"  size="20" placeholder="Password" type="password" required >
          </div>
        </div>
         <div class='form-group required'>
						<label for='InputName'><sup>*</sup></label><?php getTitleRadio();?>
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
						<div class='col-xs-12 col-sm-3' style='margin-left:-18px'><select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectDay(); ?></select></div>
						<div class='col-xs-12 col-sm-5'><select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php  selectMonth(); ?></select></div>
						<div class='col-xs-12 col-sm-4'><select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php  selectYear();?></select></div>
					  </div>
        <div class="form-group">
          <div >
            <div class="checkbox login-remember">
            </div>
          </div>
        </div>
        <div >
          <div >
            <input  class="btn  btn-block btn-lg btn-primary" value="สมัครสามชิก" type="submit" onclick="JavaScript:return check_email();">
          </div>
        </div>
        <!--userForm--> 
        </form>
      </div>
      <div class="modal-footer">
        <p class="text-center">  </p>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.ModalSignup End --> 
<script>
function check_login(){
var email = $("#login-user").val();
var pass = $("#login-password").val();
$.ajax({
	type:"GET", url:"controller/loginController.php", cache:false, data:"email="+email+"&password="+pass, success: function(valid){
		if(valid == "false"){
			$("#message").html("อีเมล์หรือรหัสผ่านไม่ถูกต้อง");
		}else if(valid == "true"){
			$("#Login_form").submit();
		}
	}
	});
}
function check_email(){
	
	
	var email = $("#EMAIL").val();
	$.ajax({
	type:"GET", url:'controller/loginController.php', cache:false, data:'EMAIL='+email, success: function(value){
		if(value == "false"){
			alert("อีเมล์นี้ถูกใช้แล้วกรุณาใส่รหัสผ่านที่ถูกต้อง หรือ ใช้อีเมล์อื่น");
		}else if(value == "true"){
			if($("#EMAIL").val() == ""){
				alert("กรุณาใส่อีเมล");
				$("#EMAIL").focus();
				return false;
			}else if($("#PASSWORD").val() == ""){
				alert("กรุณาใส่รหัส");
				$("#PASSWORD").focus();
				return false;
			}else if($("#gender").val() == ""){
				alert("กรุณาใส่คำนำหน้า");
				$("#gender").focus();
				return false;
			}
			else if($("#first_name").val() == ""){
				alert("กรุณาใส่อีเมลชื่อ");
				$("#first_name").focus();
				return false;
			}else if($("#last_name").val() == ""){
				alert("กรุณาใส่อีเมลนามสกุล");
				$("#last_name").focus();
				return false;
			}else if($("#id_number").val() == ""){
				alert("กรุณาใส่เลขบัตรประจำตัว");
				$("#id_number").focus();
				return false;
			}else{
			$("#new_user").submit();
			}
		}
	}
	});
}

</script>
