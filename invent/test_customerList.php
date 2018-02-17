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
<?php 
function get_discount($id_cus, $id_cat)
{
	$qs = dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = ".$id_cus." AND id_category = ".$id_cat);
	if(dbNumRows($qs) > 0 )
	{
		list($dis) = dbFetchArray($qs);
	}else{
		$dis		= 0;
	}
	return $dis;
}
?>
<div style="width:100%; padding:10px;">
<?php if( !isset($_GET['load']) ) :?>
	<div class="col-lg-2 col-lg-offset-5" style="text-align:center"><button class="btn btn-default" onClick="window.location.href='test.php?load=y'">show data</button></div>
<?php else: ?>.
<?php $cate = array(); ?>
<table class="table table-bordered">
<thead>
	<th>ลำดับ</th><th>รหัส</th><th>ชื่อ</th>
    <?php $qa = dbQuery("SELECT * FROM tbl_category"); ?>
    <?php while($rc = dbFetchArray($qa) ) : ?>
    <th><?php echo $rc['category_name']; ?></th>
    <?php $cate[] = $rc['id_category']; ?>
    <?php endwhile; ?>
</thead>
<?php $qs = dbQuery("SELECT * FROM tbl_customer"); ?>
<?php $n =1; ?>
<?php while($rs = dbFetchArray($qs) ) : ?>
	<tr>
    	<td><?php echo $n; ?></td>
        <td><?php echo $rs['customer_code']; ?></td>
        <td><?php echo $rs['first_name']." ".$rs['last_name']; ?></td>
        <?php $ca = $cate; ?>
        <?php foreach( $ca as $id) : ?>
        <td><?php echo get_discount($rs['id_customer'], $id); ?></td>
        <?php endforeach; ?>
        </tr>
        <?php $n++; ?>
<?php endwhile; ?>
</table>    
<?php endif; ?>
</div>
</body>

</html>
