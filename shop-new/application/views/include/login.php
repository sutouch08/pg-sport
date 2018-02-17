<script src="<?php echo base_url(); ?>library/js/jquery.md5.js"></script>

<!-- Modal Login start -->
<div class="modal signUpContent fade" id="ModalLogin" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:300px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
                <h3 class="modal-title-site text-center"> Login to TSHOP </h3>
            </div>
            <div class="modal-body">
            <div id="err" style="text-align:center; display:none;"><span style="color:red; text-align:center;">Invalid Username or Password</span></div>
            <div id="err_active" style="text-align:center; display:none;"><span style="color:red; text-align:center;">บัญชีของคุณยังไม่ได้รับการยืนยัน</span></div>
            <div id="err_user" style="text-align:center; display:none;"><span style="color:red; text-align:center;">Invalid Username</span></div>
    
                <div class="form-group login-username">
                    <div>
                        <input name="log" id="login-user" class="form-control input" placeholder="Enter Username" type="text">
                    </div>
                </div>
                <div id="err_pass" style="text-align:center; display:none;"><span style="color:red; text-align:center;">Invalid Password</span></div>
                <div class="form-group login-password">
                    <div>
                        <input name="Password" id="login-password" class="form-control input" placeholder="Password" type="password">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <div class="checkbox login-remember">
                            <label><input name="rememberme" id="rememberme" value="forever" checked="checked" type="checkbox"> Remember Me </label>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                    	<button type="button" class="btn btn-block btn-lg btn-primary" onClick="login()">LOGIN</button>
                    </div>
                </div>
                <!--userForm-->

            </div>
            <div class="modal-footer">
                <p class="text-center"> <a href="forgot-password.html">ลืมรหัสผ่าน ? </a></p>
            </div>
        </div>
        <!-- /.modal-content -->

    </div>
    <!-- /.modal-dialog -->

</div>
<!-- /.Modal Login -->

<!-- Modal Signup start -->
<div class="modal signUpContent fade" id="ModalSignup" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
                <h3 class="modal-title-site text-center"> REGISTER </h3>
            </div>
            <div class="modal-body">
                <div class="control-group"><a class="fb_button btn  btn-block btn-lg " href="#"> SIGNUP WITH
                    FACEBOOK </a></div>
                <h5 style="padding:10px 0 10px 0;" class="text-center"> OR </h5>

                <div class="form-group reg-username">
                    <div>
                        <input name="login" class="form-control input" size="20" placeholder="Enter Username"
                               type="text">
                    </div>
                </div>
                <div class="form-group reg-email">
                    <div>
                        <input name="reg" class="form-control input" size="20" placeholder="Enter Email" type="text">
                    </div>
                </div>
                <div class="form-group reg-password">
                    <div>
                        <input name="password" class="form-control input" size="20" placeholder="Password"
                               type="password">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <div class="checkbox login-remember">
                            <label>
                                <input name="rememberme" id="rememberme" value="forever" checked="checked"
                                       type="checkbox">
                                Remember Me </label>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <input name="submit" class="btn  btn-block btn-lg btn-primary" value="REGISTER" type="submit">
                    </div>
                </div>
                <!--userForm-->

            </div>
            <div class="modal-footer">
                <p class="text-center"> Already member? <a data-toggle="modal" data-dismiss="modal" href="#ModalLogin">
                    Sign in </a></p>
            </div>
        </div>
        <!-- /.modal-content -->

    </div>
    <!-- /.modal-dialog -->

</div>
<script>
function login()
{
	$("#err_user").css("display", "none");
	$("#err_pass").css("display", "none");
	var user = $("#login-user").val();
	var pass = $("#login-password").val();
	var id_customer = $("#id_customer").val();
	if( $("#rememberme").is(":checked") )
	{
		var rmbm = 1;
	}
	else
	{
		var rmbm = 0;
	}
	if( pass != ''){ pass = MD5(pass); }
	//console.log(rmbm);
	if( user == ""){ $("#err_user").css("display", ""); $("#login-user").focus(); return false;}	
	if( pass == ""){ $("#err_pass").css("display", ""); $("#login-password").focus(); return false; }
	load_in();
	$.ajax({
		url:"<?php echo base_url(); ?>shop/login",
		type:"POST", cache: "false", data:{ "user_name" : user, "password" : pass, "rememberme" : rmbm, "id_customer" : id_customer },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == "fail")
			{
				login_fail();	
			}
			else if( rs == "not_active" )
			{
				login_disactive();
			}
			else if(rs == "success" )
			{
				window.location.reload();
			}
		}
	});
}

function login_fail()
{
	$("#err_active").css("display", "none");
	$("#err_user").css("display", "none");
	$("#err_pass").css("display", "none");
	$("#err").css("display", "");	
}

function login_disactive()
{	
	$("#err_user").css("display", "none");
	$("#err_pass").css("display", "none");
	$("#err").css("display", "none");	
	$("#err_active").css("display", "");	
}
		
</script>