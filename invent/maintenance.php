<?php
require_once '../library/config.php';
require_once '../library/functions.php';
require_once "function/tools.php";
$id_emp		= getCookie('user_id') ? getCookie('user_id') : FALSE;
$cando		= 0;
if( $id_emp !== FALSE )
{
	$id_profile	= getCookie('profile_id');
	$tab			= 61; 	//---- สามารถปิด/เปิดระบบได้หรือไม่ --//
	$pm			= checkAccess($id_profile, $tab);
	$cando 		= $pm['view'] + $pm['add'] + $pm['edit'] + $pm['delete'];
}
?>
<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>Maintenance</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/template.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/jquery.md5.js"></script>
     
    
    <?php 
	
	?>
    <!-- SB Admin CSS - Include with every page 
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">-->
</head>

<body>
<div class="container">
<div class="row">
	<div class="col-sm-12"><?php echo getConfig('MAINTENANCE_MESSAGE'); ?></div>
</div>
<!--
<div style="width:100%; margin-top:100px; font-size:18px; text-align:center;">..... Maintenance Mode .....</div>
<div style="width:100%; margin-top: 25px;"><?php echo getConfig('MAINTENANCE_MESSAGE'); ?></div>
-->
<div style="position:fixed; top:15px; right:15px;">
	<?php if( $id_emp === FALSE ) : ?>
    	<a href="javascript:void(0)" onClick="goLogin()">เข้าสู่ระบบ</a>    
    <?php else : ?>
    	<?php if( $cando > 0 ) : ?>
		<a href="javascript:void(0)" onClick="setConfig(<?php echo $id_emp; ?>)">เปิดระบบ</a>
    	<?php endif; ?>
	<?php endif; ?>        
</div>
</div>

<div class='modal fade' id='loginModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' >เข้าสู่ระบบ</h4>
			</div>
			<div class='modal-body'>
				<div class="col-sm-12">
                	<label>User Name</label>
                    <input type="text" class="form-control input-sm" name="userName" id="userName" placeholder="ชื่อผู้ใช้งาน" />
                </div>
                <div class="col-sm-12">
                	<label>Password</label>
                    <input type="password" class="form-control input-sm" name="password" id="password" placeholder="รหัสผ่าน" />
                </div>
                <div style="width:100%; height:20px; margin-top:10px; margin-bottom:10px; float:left;">
                	<p style="text-align:center; color:red; visibility:hidden;" id="errorMessage">ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง</p>
                </div>
                <div class="col-sm-12">
                	<button type="button" class="btn btn-sm btn-primary btn-block" onClick="doLogin()">ลงชื่อเข้าใช้</button>
                </div>
			</div>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>

</body>
<script>
	var intv = setInterval(function(){
					$.ajax({
						url:"<?php echo WEB_ROOT; ?>library/functions.php?isClosed",
						type:"GET", cache:'false', success: function(rs){
							var rs = $.trim(rs);
							if( rs == 0 ){
								window.location.reload();
							}
						}
					});
	}, 3000);
function goLogin()
{
	$("#loginModal").modal('show');
}

function doLogin()
{
	var userName 	= $("#userName").val();
	var password	= $("#password").val();
	if( userName == ''){ 
		$("#userName").css("border-color", "red");
		$("#password").css("border-color", '');
		$("#errorMessage").text('กรุณาใส่ชื่อผู้ใช้งาน');
		$("#errorMessage").css('visibility', 'visible');
		$("#userName").focus();
		return false;
	}
	if( password == ""){
		$("#password").css("border-color", "red");
		$("#userName").css("border-color", '');
		$("#errorMessage").text('กรุณาใส่รหัสผ่าน');
		$("#errorMessage").css('visibility', 'visible');
		$("#password").focus();
		return false;
	}
	if( userName != '' && password != '')
	{
		$("#userName").css("border-color", '');
		$("#password").css("border-color", '');
		var password = MD5(password);
		$.ajax({
			url:"controller/employeeController.php?empLogin",
			type:"POST", cache:"false", data:{ "userName" : userName, "password" : password },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' )
				{
					$("#loginModal").modal('hide');
					window.location.reload();
				}else{
					$("#errorMessage").text('ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง');
					$("#errorMessage").css('visibility', 'visible');
				}
			}
		});
	}
	
}

function setConfig(id_emp)
{
	$.ajax({
		url:"controller/settingController.php?activeSystem",
		type:"POST", cache:"false", data:{ "id_employee" : id_emp },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				window.location.reload();;
			}
		}
	});
}
</script>
</html>
