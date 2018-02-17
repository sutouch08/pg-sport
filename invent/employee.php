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
	function active_btn($val1, $val2, $class = "btn-success")
	{
		$val = "";
		if($val1 == $val2)
		{
			$val = $class;
		}
		return $val;			
	}
	$btn = "";
	if( isset($_GET['reset_password']) )
	{
		$btn .= "<button class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	 }
	 else if( isset($_GET['add']) )
	 {
		 $btn .= "<button class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
		 if($add ) :
		   	$btn .= "<button type='button' class='btn btn-success btn-sm' onclick='submit_add()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
		 endif;
	 }
	 else if( isset($_GET['edit']) )
	 {
		   $btn .= "<button class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
			if($edit) :
				$btn .= "<button type='button' class='btn btn-success btn-sm' onclick='submit_edit()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
			endif;	
	   }
	   else
	   {
		   if( $add ) :
		   		$btn .= "<button type='button' class='btn btn-success btn-sm' onclick='add_new()'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>";
		   endif;
	   }
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-lg-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-user"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-lg-4">
     <p class="pull-right" style="margin-bottom:0px;">
       <?php 
	   	echo $btn;
	   ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->

<?php  if(isset($_GET['add'])) :  ?>
<form id="add_form" method="post">
<div class="row">
	<div class="col-lg-4"><span class="form-control label-left">ชื่อ : </span></div>
    <div class="col-lg-4"><input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="ชื่อพนักงาน (จำเป็น)" autofocus="autofocus" autocomplete="off" /></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12"><input type="hidden" name="id_employee" id="id_employee" value="0" />&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">นามสกุล : </span></div>
    <div class="col-lg-4"><input type='text' name='last_name' id='last_name' class='form-control input-sm' placeholder="นามสกุลพนักงาน" autocomplete='off' /></div>
    <div class="col-lg-4">&nbsp;</div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">Email / User name : </span></div>
    <div class="col-lg-4"><input type='text' name='email' id='email' class='form-control input-sm' autocomplete='off' placeholder="อีเมล์ หรือ ชื่อสำหรับเข้าระบบ (จำเป็น)"/></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">รหัสผ่าน : </span></div>
    <div class="col-lg-4"><input type='password' name='password' id='password' class='form-control input-sm' autocomplete="off" placeholder="รหัสผ่านสำหรับเข้าระบบ (จำเป็น)"/></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">ยืนยันรหัสผ่าน : </span></div>
    <div class="col-lg-4"><input type='password' name='cf_password' id='cf_password' class='form-control input-sm' autocomplete="off" placeholder="ป้อนรหัสผ่านให้ตรงกับช่องรหัสผ่านด้านบน (จำเป็น)"/></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">โปรไฟล์ : </span></div>
    <div class="col-lg-4"><select name='id_profile' id='id_profile' class="form-control input-sm" style="color:#999;"><?php echo selectEmployeeGroup('',"----------  เลือก  ----------"); ?></select></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">รหัสลับ : </span></div>
    <div class="col-lg-4"><input type='password' name='s_key' id='s_key' class='form-control input-sm' autocomplete="off" placeholder="รหัสลับสำหรับใช้อนุมัติ" /></div>
    <div class="col-lg-4"></div>
    <div class="col-lg-12">&nbsp;</div>
    
     <div class="col-lg-4"><span class="form-control label-left">สถานะ : </span></div>
    <div class="col-lg-3">
        <div class="btn-group">
        	<button type="button" id="btn_enable" class="btn btn-success btn-sm" style="width: 50%;" onclick="enable()"><i class="fa fa-check"></i>&nbsp; เปิดใช้งาน</button>
            <button type="button" id="btn_disable" class="btn btn-sm" style="width: 50%;" onclick="disable()"><i class="fa fa-ban"></i>&nbsp; ปิดใช้งาน</button>
        </div>
    </div>
    <div class="col-lg-5"><input type="hidden" name="active" id="active" value="1" /></div>
    <div class="col-lg-12">&nbsp;</div>
</div>
</form>
<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_employee'] ) ) :  ?>
<?php $employee = new employee($_GET['id_employee']); ?>
<div class="row">
	<div class="col-lg-4"><span class="form-control label-left">ชื่อ : </span></div>
    <div class="col-lg-4"><input type="text" name="first_name" id="first_name" class="form-control input-sm" value="<?php echo $employee->first_name; ?>" placeholder="ชื่อพนักงาน (จำเป็น)" autofocus="autofocus" autocomplete="off" /></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12"><input type="hidden" name="id_employee" id="id_employee" value="<?php echo $_GET['id_employee']; ?>" />&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">นามสกุล : </span></div>
    <div class="col-lg-4"><input type='text' name='last_name' id='last_name' class='form-control input-sm' value="<?php echo $employee->last_name; ?>" placeholder="นามสกุลพนักงาน" autocomplete='off' /></div>
    <div class="col-lg-4">&nbsp;</div>
    <div class="col-lg-12">&nbsp;</div>
    
    <div class="col-lg-4"><span class="form-control label-left">Email / User name : </span></div>
    <div class="col-lg-4"><input type='text' name='email' id='email' class='form-control input-sm' value="<?php echo $employee->email; ?>" autocomplete='off' placeholder="อีเมล์ หรือ ชื่อสำหรับเข้าระบบ (จำเป็น)"/></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
        
    <div class="col-lg-4"><span class="form-control label-left">โปรไฟล์ : </span></div>
    <div class="col-lg-4"><select name='id_profile' id='id_profile' class="form-control input-sm" style="color:#999;"><?php echo selectEmployeeGroup($employee->id_profile,"----------  เลือก  ----------"); ?></select></div>
    <div class="col-lg-4"><span class="form-control label-right" style="color:red;">*</span></div>
    <div class="col-lg-12">&nbsp;</div>
        
     <div class="col-lg-4"><span class="form-control label-left">สถานะ : </span></div>
    <div class="col-lg-3">
        <div class="btn-group">
        	<button type="button" id="btn_enable" class="btn btn-sm <?php echo active_btn($employee->active, 1, "btn-success"); ?>" style="width: 50%;" onclick="enable()"><i class="fa fa-check"></i>&nbsp; เปิดใช้งาน</button>
            <button type="button" id="btn_disable" class="btn btn-sm <?php echo active_btn($employee->active, 0, "btn-danger"); ?>" style="width: 50%;" onclick="disable()"><i class="fa fa-ban"></i>&nbsp; ปิดใช้งาน</button>
        </div>
    </div>
    <div class="col-lg-5"><input type="hidden" name="active" id="active" value="<?php echo $employee->active; ?>" /></div>
    <div class="col-lg-12">&nbsp;</div>
</div>

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
	$where = "WHERE id_employee != 0 ";
	if($search_text !="") : 
		$where .= "AND (first_name LIKE '%".$search_text."%' OR last_name LIKE '%".$search_text."%' OR email LIKE '%".$search_text."%')";
	endif;
	$where .= " ORDER BY id_employee DESC";
?>

<?php
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$paginator->Per_Page("tbl_employee",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=Employee");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
?>	
<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td 
	{
		vertical-align:middle;
	}
</style>	
<div class='row'>
	<div class='col-sm-12'>
<?php 	$qs = dbQuery("SELECT * FROM tbl_employee ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>		
<?php    $row = dbNumRows($qs); ?>
<?php	if($row >0) : ?>
		<table class='table table-striped table-hover'>
			<thead>
				<th style='width:5%; text-align:center; border-top:solid 1px #ccc;'>ลำดับ</th>
                <th style='width:30%; border-top:solid 1px #ccc;'>ชื่อ</th>
				<th style='width:20%; border-top:solid 1px #ccc;'>User Name</th>
                <th style='width:20%; border-top:solid 1px #ccc;'>โปรไฟล์</th>
                <th style='width:10%; border-top:solid 1px #ccc; text-align:center;'>สถานะ</th>
                <th style='width:15%; border-top:solid 1px #ccc; text-align:right;'>การกระทำ</th>
			</thead>
<?php 		while($rs = dbFetchArray($qs)) : ?>
			<tr style="font-size:12px;">
				<td align='center' ><?php echo $rs['id_employee']; ?></td>
				<td ><?php echo employee_name($rs['id_employee']); ?></td>
				<td ><?php echo $rs['email']; ?></td>
				<td ><?php echo profile_name($rs['id_profile']); ?></td>
				<td align='center' style='vertical-align:middle;'><?php echo isActived($rs['active']); ?></td>
				<td align='right'>
                <?php if($rs['id_profile'] >2) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-xs'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-xs' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>	
                <?php elseif($rs['id_profile'] == 1 && $id_profile == 1) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-xs'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-xs' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>
                <?php elseif($rs['id_profile'] == 2 && ($id_profile == 1 || $id_profile == 2) ) : ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&reset_password&id_employee=".$rs['id_employee']."'><button type='button' class='btn btn-default btn-xs'><i class='fa fa-key'></i></button></a>"); ?>
                <?php echo can_do($edit, "<a href='index.php?content=Employee&edit=y&id_employee=".$rs['id_employee']."'><button class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></button></a>"); ?>
                <?php $link = "controller/employeeController.php?drop=y&id_employee=".$rs['id_employee']; ?>
                <?php echo can_do($delete, "<button class='btn btn-danger btn-xs' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบ ".employee_name($rs['id_employee'])." ?', 'โปรดจำไว้ เมื่อลบแล้วไม่สามารถกู้คืนได้','".$link."')\"><i class='fa fa-trash'></i></button>"); ?>
                <?php endif; ?>				
				</td>
			</tr>
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
function check_name(role)
{
	var id 			= $("#id_employee").val();
	var name 		= $("#first_name").val();
	var last_name 	= $("#last_name").val();	
	$.ajax({
		url:"controller/employeeController.php?check_name",
		type: "POST", data:{ "id_employee" : id, "first_name" : name, "last_name" : last_name },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "0")
			{
				check_email(role);
			}else{
				load_out();
				swal("ผิดพลาด !!", "มีพนักงานคนนี้อยู่ในระบบแล้ว", "error");
			}
		}
	});
}

function check_email(role)
{
	var email 	= $("#email").val();
	var id 		= $("#id_employee").val();
	if(role == "add")
	{
		var s_key	= $("#s_key").val();
	}else if(role == "edit"){
		var s_key	= "";
	}		
	$.ajax({
		url:"controller/employeeController.php?check_email",
		type: "POST", cache: "false", data: { "id_employee" : id, "email" : email },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if( rs == "0")
			{
				if( s_key != "" )
				{
					check_s_key(role);
					
				}else{
					if(role == "add")
					{
						add();
					}else if( role == "edit"){
						edit();
					}
				}
			}else{
				load_out();
				swal("ผิดพลาด !!", "อีเมล์ หรือ User name นี้มีคนใช้แล้ว กรุณาระบุอีเมล์ หรือ User name อื่น", "error");
			}
		}
	});
}

function check_s_key(role)
{
	var id 	= $("#id_employee").val();
	var s_key 	= $("#s_key").val();
	$.ajax({
		url:"controller/employeeController.php?check_s_key",
		type: "POST", cache: "false", data: { "s_key" : s_key },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "0")
			{
				if(role == "add")
				{
					add();
				}else if( role == "edit" ){
					edit();
				}
			}else{
				load_out();
				swal("ผิดพลาด !!", "ไม่สามารถใช้รหัสลับนี้ได้ กรุณากำหนดรหัสลับอื่น", "error");	
			}
		}
	});	
}

function enable()
{
	$("#active").val(1);
	$("#btn_disable").removeClass("btn-danger");
	$("#btn_enable").addClass("btn-success");
}

function disable()
{
	$("#active").val(0);
	$("#btn_enable").removeClass("btn-success");
	$("#btn_disable").addClass("btn-danger");
}

function add_new()
{
	window.location.href = "index.php?content=Employee&add=y";	
}

function go_back()
{
	window.location.href = "index.php?content=Employee";	
}

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
	var first_name 	= $("#first_name").val();
	var email 		= $("#email").val();
	var password 	= $("#password").val();
	var cf_pass 	= $("#cf_password").val();
	var id_profile 	= $("#id_profile").val();
	var s_key		= $("#s_key").val();
	
	if( first_name == ""){ swal("ข้อมูลไม่ครบ","กรุณาระบุชื่อพนักงาน", "error"); return false; }
	if( email == ""){ swal("ข้อมูลไม่ครบ", "กรุณากำหนดอีเมล์ หรือ User name สำหรับใช้เข้าระบบ", "error"); return false; }
	if( password == ""){ swal("ข้อมูลไม่ครบ","กรุณากำหนดรหัสผ่านสำหรับเข้าระบบ", "error"); return false; }
	if( password != "" && cf_pass != password ){ swal("รหัสผ่านไม่ตรงกัน", "รหัสผ่านไม่ตรงกัน โปรดป้อนรหัสผ่านทั้ง 2 ช่อง", "error"); return false; }
	if( id_profile == ""){ swal("ข้อมูลไม่ครบ", "กรุณาเลือกโปรไฟล์", "error"); return false; }
	if( s_key != "" && s_key.length < 4 ){ swal("รหัสลับไม่ถูกต้อง", "รหัสลับต้องมีความยาวอย่างน้อย 4 ตัวอักษร", "error"); return false; }
	
	load_in();
	check_name("add");
}

function submit_edit()
{
	var first_name 	= $("#first_name").val();
	var email 		= $("#email").val();
	var id_profile 	= $("#id_profile").val();
	
	if( first_name == ""){ swal("ข้อมูลไม่ครบ","กรุณาระบุชื่อพนักงาน", "error"); return false; }
	if( email == ""){ swal("ข้อมูลไม่ครบ", "กรุณากำหนดอีเมล์ หรือ User name สำหรับใช้เข้าระบบ", "error"); return false; }
	if( id_profile == ""){ swal("ข้อมูลไม่ครบ", "กรุณาเลือกโปรไฟล์", "error"); return false; }
	
	load_in();
	check_name("edit");
}

function add()
{
	$.ajax({
		url:"controller/employeeController.php?add",
		type: "POST", cache: "false", data: $("#add_form").serialize(),
		success: function(rs)
		{
			var rs = $.trim(rs);
			var rs = parseInt(rs);
			if( !isNaN(rs) )
			{
				load_out();
				window.location.href="index.php?content=Employee&edit=y&id_employee="+rs+"&message=เพิ่มพนักงานเรียบร้อยแล้ว";
			}else{
				load_out();
				swal("ผิดพลาด !!", "เพิ่มพนักงานไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});	
}

function edit()
{
	var id	 			= $("#id_employee").val();
	var first_name 	= $("#first_name").val();
	var last_name	= $("#last_name").val();
	var email 		= $("#email").val();
	var id_profile 	= $("#id_profile").val();
	var active 		= $("#active").val();
	$.ajax({
		url:"controller/employeeController.php?edit",
		type: "POST", cache: "false", data: {"id_employee" : id, "first_name" : first_name, "last_name" : last_name, "email" : email, "id_profile" : id_profile, "active" : active },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if( rs == "success" )
			{
				load_out();
				swal({ title:"เรียบร้อย", text:"แก้ไขข้อมูลพนักงานเรียบร้อยแล้ว", timer: 1000, type: "success"});
			}else{
				load_out();
				swal("ผิดพลาด !!", "แก้ไขข้อมูลพนักงานไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});		
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