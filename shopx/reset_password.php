<?php
	if(isset($_GET['id_customer'])&&isset($_GET['tokenid'])){
		$id_customer = $_GET['id_customer'];
		$password = $_GET['tokenid'];
		$email = $_GET['email'];
		if($id_customer !="" && $password != ""){
			$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE id_customer = $id_customer AND email ='$email' AND password = '$password' AND id_customer !=0");
			$row = dbNumRows($sql);
			if($row==1){
		echo"
		<div class='container'>
			<div class='row' style='margin-top:100px;'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><h3>&nbsp;</h3></div></div>
			<div class='row'><form id='reset_password_form' action='controller/accountController.php?reset_password=y&id_customer=$id_customer' method ='post'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 '><h4 style='text-align:center;'>เปลี่ยนรหัสผ่าน</h4></div>
			</div>
			<div class='row'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
					<div class='col-xs-4'><p style='text-align:right;'>รหัสผ่านใหม่</p></div>
					<div class='col-xs-8'><input type='password' class='form-control' name='new_password' id='new1' placeholder='New password' required /></div>
				</div>
			</div>
			<div class='row'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
					<div class='col-xs-4'><p style='text-align:right;'>ยืนยันรหัสผ่านใหม่</p></div>
					<div class='col-xs-8'><input type='password' class='form-control' id='new2' placeholder='Confirm password' required /></div>
				</div>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
				<div class='col-xs-12' style='text-align:left;'><p id='miss_match' style='text-align:left; color:red;'></p></div>
				</div>
			</div>
			<div class='row'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 '><div class='col-xs-4'>&nbsp;</div>
					<div class='col-xs-8'><p style='text-align:left;'><button type='button' class='btn btn-lg btn-primary' onclick='check_password()'>เปลี่ยนรหัสผ่าน</button></p></div>
				</div>
			</div></form>
		</div>";
		}else{
			echo "<div class='container'>
			<div class='row' style='margin-top:100px;'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><h3>&nbsp;</h3></div></div>
			<div class='row'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 '>
					<p><h4>มีบางอย่างผิดพลาด !!!</h4>ไม่สามารถดำเนินการต่อได้ กรุณาติดต่อผู้ดูแลระบบ</p>
					<p><h4>Something went wrong !!!</h4>please contact webmaster</p>
				</div>
			</div>
		</div>";
		}
		}
	}
	if(isset($_GET['error'])){
		echo "<div class='container'>
			<div class='row' style='margin-top:100px;'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><h3>&nbsp;</h3></div></div>
			<div class='row'>
				<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 '>
					<p><h4>มีบางอย่างผิดพลาด !!!</h4>ไม่สามารถดำเนินการต่อได้ กรุณาติดต่อผู้ดูแลระบบ</p>
					<p><h4>Something went wrong !!!</h4>please contact webmaster</p>
				</div>
			</div>
		</div>";
	}
?>
<script>
	function check_password(){
		pass_field1 = $("#new1").val();
		pass_field2 = $("#new2").val();
		if(pass_field1 == pass_field2){
			$("#reset_password_form").submit();
		}else{
			$("#miss_match").html("รหัสผ่านไม่ตรงกัน : password miss match");
		}
	}		
</script>