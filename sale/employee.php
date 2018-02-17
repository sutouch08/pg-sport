<?php 
	
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;</h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['reset_password'])){
		   echo"<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='reset_password();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
 if(isset($_GET['reset_password'])){
	echo "<form method='post' name='add_employee' action='controller/saleController.php?reset_password=y'>";
}
 if(isset($_GET['reset_password'])&&isset($_GET['id_employee'])){
	$employee = new employee($_GET['id_employee']);
	$id_employee = $employee->id_employee;
	$first_name = $employee->first_name;
	$last_name = $employee->last_name;
	$email = $employee->email;
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp; <input type='hidden' name='id_employee' id='id_employee' value='$id_employee' /></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' value='$first_name' class='form-control input-sm' disabled /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' value='$last_name' class='form-control input-sm' disabled /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์/User name :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' value='$email' class='form-control input-sm' required='required' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password'class='form-control input-sm' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>* </td>
	</tr>
	<tr><td colspan='3'><button type='submit' id='reset' style='display:none;'>reset</button>
	</table></form>";
	
}
	?>
</div>
<script>


function reset_password(){
	var id = $("#id_employee").val();
	if(id==""){
		alert("ไม่พบตัวแปร id_employee กรุณาติดต่อผู้ดูแลระบบ");
	}else{
		$("#reset").click();
	}
}
</script>