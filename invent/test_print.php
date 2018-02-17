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

<body style='padding-top:0px; margin-top:-15px;'>
<div style='width:180mm; margin-right:auto; margin-left:auto; padding-left:10px; padding-right:10px; padding-top:0px;'>
<table width="100%" border="0px;">
<tr>
<?php 
$i = 0;
while($i<1) :
	$data = array(	
					array("barcode" => "pack001", "item" => "Item Pack"),
					array("barcode" => "pack001", "item" => "Item Pack"),	
					array("barcode" => "pack001", "item" => "Item Pack"),	
					array("barcode" => "pack001", "item" => "Item Pack"),				
				);
?>


<?php	foreach($data as $rs) : ?>	
<td style="width: 25%; padding: 15px;">		
<div style="width: 100%; margin-bottom:50px;">
<center><?php echo $rs['item']; ?></center>
<?php echo "<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$rs['barcode']."' width='100%' />"; ?>
</div>
</td>  
<?php 	endforeach; ?>

<?php $i++; ?>
<?php endwhile; ?>  
</tr></table>  
</div>   
</body>

</html>
