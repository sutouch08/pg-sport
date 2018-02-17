<?php 
	$page_menu = "invent_sale";
	$page_name = "พนักงาน";
	$id_tab = 26;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$btn = "";
	if(isset($_GET['reset_password'])){
		   $btn .= "<a href='index.php?content=Employee'><button class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	   }else if(isset($_GET['edit']) || isset($_GET['add'])){
		   $btn .= "<a href='index.php?content=Employee'><button class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		   if($add || $edit) :
		   		$btn .= "<button type='button' class='btn btn-success' onclick='submit_add()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
			endif;
	   }else{
		   $btn .= can_do($add, "<a href='index.php?content=Employee&add=y'><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	   }
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3 class="title"><i class="fa fa-user"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
     <p class="pull-right">
       <?php 
	   	echo $btn;
	   ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php

if(isset($_GET['add'])){
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?add=y' >";
	$id_profile = "";
}else if(isset($_GET['edit'])){
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?edit=y'>";
	$id_employee = $_GET['id_employee'];
	$employee = new employee($id_employee);
	$id_profile = $employee->id_profile;
	echo "<input type='hidden' name='id_employee' id='id_employee' value='".$id_employee."'/>";
}else if(isset($_GET['reset_password'])){
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?reset_password=y'>";
}
?>
<?php  if(isset($_GET['add'])) :  ?>

	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='first_name' id='first_name' class='form-control input-sm' required='required' autofocus />
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' required='required'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์/User name :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' class='form-control input-sm' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password' class='form-control input-sm' required='required'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>* </td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>โปรไฟล์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_profile' id='id_profile' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php echo selectEmployeeGroup(); ?></select>
			<span class='help-block'></span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสลับ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='s_key' id='s_key' class='form-control input-sm' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'> </td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<div class='row'>
				<input type='radio' name='active' id='yes' value='1' checked='checked' style='margin-left:15px;' />
				<label for='yes' style='padding-left10px;'><i class='fa fa-check fa-2x' style='color: #5cb85c;'></i></label>
				<input type='radio' name='active' id='no' value='0' style='margin-left:15px;' /><label for='no' style='padding-left:10px;'><i class='fa fa-remove fa-2x' style='color:red;'></i></label></div>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	</table></form>
<?php elseif(isset($_GET['edit'])) :  ?>

	<div class='col-lg-4 label-left'>ชื่อ :</div>
	<div class='col-lg-4'><input type='text' name='first_name' id='first_name' value='<?php echo $employee->first_name; ?>' class='form-control input-sm' required='required' autofocus /></div>
	<div class='col-lg-4 label-right'><span style='color:red;'>*</span></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>นามสกุล :</div>
	<div class='col-lg-4'><input type='text' name='last_name' id='last_name' value='<?php echo $employee->last_name; ?>' class='form-control input-sm' required='required'/></div>
	<div class='col-lg-4 label-right'><span style='color:red;'>*</span></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>อีเมล์/User name :</div>
	<div class='col-lg-4'><input type='text' name='email' id='email' value='<?php echo $employee->email; ?>' class='form-control input-sm' autocomplete='off'/></div>
	<div class='col-lg-4 label-right'></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>โปรไฟล์ :</div>
	<div class='col-lg-4'><select name='id_profile' id='id_profile' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php echo selectEmployeeGroup($id_profile); ?></select></div>
	<div class='col-lg-4 label-right'></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>สถานะ :</div>
	<div class='col-lg-4'>
				<label for='yes' style='padding-left10px;'><input type='radio' name='active' id='yes' value='1' <?php echo isChecked($employee->active, 1); ?> style='margin-left:15px;' />
				<i class='fa fa-check fa-2x' style='color: #5cb85c;'></i></label>
				<label for='no' style='padding-left:10px;'><input type='radio' name='active' id='no' value='0' <?php echo isChecked($employee->active, 0); ?> style='margin-left:15px;' />
				<i class='fa fa-remove fa-2x' style='color:red;'></i></label>
	</div>
	<div class='col-lg-4 label-right'></div>
	<div class='col-lg-12'>&nbsp;</div>
	</form>

<?php elseif(isset($_GET['reset_password'])&&isset($_GET['id_employee'])) :   
	$employee = new employee($_GET['id_employee']);
	$id_employee = $employee->id_employee;
	$first_name = $employee->first_name;
	$last_name = $employee->last_name;		?>
   </form>
    <div class="row">
	<input type='hidden' name='id_employee' id='id_employee' value='<?php echo $id_employee; ?>' />
	<div class='col-lg-4 label-left'>ชื่อ :</div>
	<div class='col-lg-4'><span class="form-control input-sm"><?php echo $first_name; ?></span></div>
	<div class='col-lg-4 label-right'></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>นามสกุล :</div>
	<div class='col-lg-4'><span class="form-control input-sm"><?php echo $last_name; ?></span></div>
	<div class='col-lg-4 label-right'></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>รหัสผ่านใหม่ :</div>
	<div class='col-lg-4'><input type='password' name='password' id='password' class='form-control input-sm' /></div>
	<div class='col-lg-4 label-right'><span style='color:red'>*</span></div>
	<div class='col-lg-12'>&nbsp;</div>
	
	<div class='col-lg-4 label-left'>ยืนยันรหัสผ่าน :</div>
	<div class='col-lg-4'><input type='password' name='cfm_password' id='cfm_password' class='form-control input-sm' /></div>
	<div class='col-lg-4 label-right'><span style='color:red'>*</span></div>
	<div class='col-lg-12'>&nbsp;</div>
	<div class='col-lg-2 col-lg-offset-6'><button type='button' class='btn btn-warning btn-block' id='reset'>เปลี่ยนรหัสผ่าน</button></div>
    </div>
   
	<?php if($_GET['id_employee'] == $_COOKIE['user_id']) : ?>
    <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
    <div class="row">
    <div class="col-lg-4 label-left">รหัสลับใหม่</div>
    <div class="col-lg-4"><input type="password" name="s_key" id="s_key" class="form-control input-sm"  /></div>
    <div class="col-lg-4">&nbsp;</div>
    <div class='col-lg-12'>&nbsp;</div>
    <div class="col-lg-4 label-left">ยืนยันรหัสลับ</div>
    <div class="col-lg-4"><input type="password" name="cmf_s_key" id="cmf_s_key" class="form-control input-sm"  /></div>
    <div class="col-lg-4">&nbsp;</div>  
    <div class='col-lg-12'>&nbsp;</div>
    <div class="col-lg-2 col-lg-offset-6"><button type="button" class="btn btn-info btn-block" onclick="reset_s_key()"><i class="fa fa-key"></i>&nbsp; เปลี่ยนรหัสลับ</button></div>
    </div>
    <?php endif; ?>    
	
	
<?php else :  ?>
<?php 	
		if( isset($_POST['search_text']) ) : 
			$search_text = $_POST['search_text']; 
			setcookie("employee_search_text", $search_text, time() + 3600, "/");
		elseif( isset($_COOKIE['employee_search_text'])) :
			$search_text = $_COOKIE['employee_search_text'];
		else :
			$search_text = '';
		endif;
?>		
<form id="search_form" method="post" action="index.php?content=Employee">
<div class="row">
	<div class="col-lg-4 col-lg-offset-3">
    	<input type="text" style="display:none;" />
    	<input type="text" class="form-control" id="search_text" name="search_text" value="<?php echo $search_text; ?>" placeholder="ค้นหาพนักงาน พิมพ์ชื่อแล้วกด Eenter หรือ คลิกค้นหา" autofocus="autofocus" style="text-align:center" />
    </div>
    <div class="col-lg-2"><button type="button" id="btn_search" class="btn btn-primary btn-block"><i class="fa fa-search"></i>&nbsp; ค้นหา</button></div>
    <div class="col-lg-2"><a href="controller/employeeController.php?clear_filter"><button type="button" class="btn btn-warning"><i class="fa fa-refresh"></i>&nbsp; เคลียร์ฟิลเตอร์</button></a></div>
</div>
</form>

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<?php
	if($search_text !="") : 
		$result = dbQuery("SELECT id_employee, id_profile, email, active FROM tbl_employee WHERE first_name LIKE '%".$search_text."%' OR last_name LIKE '%".$search_text."%' OR email LIKE '%".$search_text."%'");
 	else :
		$result = dbQuery("SELECT id_employee, id_profile, email, active FROM tbl_employee");
	endif;
	?>
<div class='row'>
	<div class='col-sm-12'>
		
<?php    $row = dbNumRows($result); ?>
<?php	if($row >0) : ?>
		<table class='table table-striped table-hover'>
			<thead style='background-color:#48CFAD;'>
				<th style='width:5%; text-align:center;'>ลำดับ</th>
                <th style='width:30%;'>ชื่อ</th>
				<th style='width:20%; ;'>User Name</th>
                <th style='width:20%;'>โปรไฟล์</th>
                <th style='width:10%;'>สถานะ</th>
                <th style='width:15%;'>การกระทำ</th>
			</thead>
<?php		$n = 1; ?>
<?php 		while($rs = dbFetchArray($result)) : ?>
			<tr>
				<td align='center' ><?php echo $n; ?></td>
				<td ><?php echo employee_name($rs['id_employee']); ?></td>
				<td ><?php echo $rs['email']; ?></td>
				<td ><?php echo profile_name($rs['id_profile']); ?></td>
				<td align='center' style='vertical-align:middle;'><?php echo isActived($rs['active']); ?></td>
				<td align='right'>
                <?php if($rs['id_profile'] >2) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-sx'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-sx'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-sx' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>	
                <?php elseif($rs['id_profile'] == 1 && $id_profile == 1) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-sx'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-sx'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-sx' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>
                <?php elseif($rs['id_profile'] == 2 && ($id_profile == 1 || $id_profile == 2) ) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-sx'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-sx'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-sx' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>
                <?php endif; ?>				
				</td>
			</tr>
<?php			$n++; ?>
<?php		endwhile;  ?>
		</table>
<?php else : ?>
	<center><h4>----------  ไม่มีพนักงานตามชื่อที่ค้นหา  ----------</h4></center>
<?php endif; ?>   
	</div> 
</div>
<?php endif; ?>
</div>
<script>
$("#search_text").keyup(function(e) {
    if(e.keyCode == 13)
	{
		search_employee();
	}
});
$("#btn_search").click(function(e) {
    search_employee();
});

function search_employee()
{
	load_in();
	var text = $("#search_text").val();
	if(text =='')
	{
		load_out();
		swal("ยังไม่ได้ระบุคำคน");
		return false;
	}else{
		$("#search_form").submit();
	}
}
function submit_add(){
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var email = $("#email").val();
	var password = $("#password").val();
	var id_profile = $("#id_profile").val();
	if(first_name == ""){
		alert("ยังไม่ได้ใส่ชื่อ");
		$("#first_name").focus();
	}else if(last_name == ""){
		alert("ยังไม่ได้ใส่นามสกุล");
		$("#last_name").focus();
	}else if(email == ""){
		alert("ยังไม่ได้ใส่อีเมล์");
		$("#email").focus();
	}else if(id_profile == ""){
		alert("ยังไม่ได้เลือกโปรไฟร์");
		$("#id_profile").focus();
	}else{
		document.add_employee.submit();
	}
}
$("#reset").click(function(){
	var password = $("#password").val();
	var cfm_pass = $("#cfm_password").val();
	var id_employee = $("#id_employee").val();
	if(password ==""){
		swal("กรุณาใส่รหัสผ่านใหม่", "รหัสผ่านใหม่ไม่สามารถเป็นค่าว่างได้", "error");
	}else if(cfm_pass == ""){
		swal("กรุณาใส่ยืนยันรหัสผ่าน", "คุณจำเป็นต้องยืนยันรหัสผ่านให้ตรงกับรหัสผ่านใหม่กำหนดไว้", "error");
	}else if(password != cfm_pass){
		$("#warning_label").html("รหัสผ่านสองช่องไม่ตรงกัน กรุณาใส่รหัสผ่านและยืนยันรหัสผ่านให้ตรงกัน");
	}else{
		load_in();
		$.ajax({
			url:"controller/employeeController.php?reset_password",
			type: "POST", cache: "false", data: { "password" : password, "id_employee" : id_employee },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs == "success")
				{
					load_out();
					swal({ title: "เรียบร้อย", text: "เปลี่ยนรหัสผ่านเรียบร้อยแล้ว", timer: 1000, type: "success" });
					window.location.reload();
				}else{
					load_out();
					swal({ title: "Error!!", text: "เปลี่ยนรหัสผ่านไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", timer: 1000, type: "success" });
				}
			}
		});
	}
	
});

function reset_s_key()
{
	var id_employee = $("#id_employee").val();
	var s_key = $("#s_key").val();
	var cmf		= $("#cmf_s_key").val();
	if(s_key == "" || cmf == "")
	{
		swal("Error!!", "รหัสลับไม่สามารถเป็นค่าว่างได้","error");	
		return false;
	}else if(s_key != cmf){
		swal("Error!!", "รหัสลับ 2 ช่องไม่ตรงกัน", "error");
		return false;
	}else{
		load_in();
		$.ajax({
			url:"controller/employeeController.php?reset_s_key",
			type: "POST", cache: "false", data: { "s_key" : s_key, "id_employee" : id_employee },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs == "success")
				{
					load_out();
					swal({ title: "เรียบร้อย", text: "เปลี่ยนรหัสลับเรียบร้อยแล้ว", timer: 1000, type: "success" });
					window.location.reload();
				}else{
					load_out();
					swal({ title: "เปลี่ยนรหัสลับไม่สำเร็จ", text: "ไมาสามารถใช้รหัสนี้ได้ ลองใหม่อีกครั้งด้วยรหัสผ่านอื่น", type: "error" });
				}
			}
		});
	}
}
function reset_password(){
	var id = $("#id_employee").val();
	if(id==""){
		alert("ไม่พบตัวแปร id_employee กรุณาติดต่อผู้ดูแลระบบ");
	}else{
		$("#reset").click();
	}
}
</script>