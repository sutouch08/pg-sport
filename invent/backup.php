<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";
?>

<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>ทดสอบระบบ</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
     
    
    
    <!-- SB Admin CSS - Include with every page 
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">-->
</head>

<body>
<div class="container">
<button type="button" class="btn btn-success" onclick="go_backup()">Backup now</button>
<div class="row" id="rs">
</div>

<script>  

function go_backup()
{

	$.ajax({
		url:"../backup/backupController.php?backup",
		type:"GET", cache:false, success: function(rs)
		{

			$("#rs").append("<div class='col-lg-12'><i class='fa fa-check' style='color:green'></i>&nbsp; backup at "+rs+"</div>");
		}
	});
}
			var interv = setInterval(function () { 
										var date = new Date();
										var time = date.getTime();
										go_backup();										
									}, 600000); 
</script>
</div>

</body>

</html>


