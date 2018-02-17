<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";
require "function/lend_helper.php";
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
     
    
    <?php 
	function is_exists($id_order)
	{
		return dbNumRows(dbQuery("SELECT id_order FROM tbl_lend WHERE id_order = ".$id_order));			
	}
	function return_qty($id_order, $id_pa)
	{
		$qty = 0;
		list($qs) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND status = 5"));
		if( !is_null($qs) )
		{
			$qty = $qs;	
		}
		
		return $qty;
	}
	
	
	?>
    <!-- SB Admin CSS - Include with every page 
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">-->
</head>

<body>
<div class="container">
<?php if( isset( $_GET['add_lend'] ) && isset( $_GET['id_order'] ) ) : ?>
<?php
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$lend 	= new lend();
	$ss 	= true;
	if( $order->current_state != 8 )
	{
		startTransection();	
		$qs = dbQuery("INSERT INTO tbl_lend (id_order, id_employee, date_add, id_user) VALUES (".$id_order.", ".$order->id_employee.", '".$order->date_add."', ".$order->id_employee.")");
		if( $qs )
		{
			$id_lend = $lend->get_id_lend_by_order($id_order);
			$qr = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_order = ".$id_order);
			if(dbNumRows($qr) > 0)
			{
				while($rs = dbFetchArray($qr))
				{
					$return_qty = return_qty($id_order, $rs['id_product_attribute']);
					$qa 	= dbQuery("INSERT INTO tbl_lend_detail (id_lend, id_product_attribute, qty, return_qty) VALUES (".$id_lend.", ".$rs['id_product_attribute'].", ".$rs['sold_qty'].", ".$return_qty.")");
					if( !$qa ){ $ss = false; }
				}
			}
		}
		else
		{
			$ss = false;
		}
		
		if( $ss )
		{
			commitTransection();
		}
		else
		{
			dbRollback();	
		}
		header("location: test.php");
	}


?>
<?php elseif( isset($_GET['update_return']) ) : ?>
<?php
	$i = 0;
	$o = 0;
	$qs = dbQuery("SELECT id_lend, id_order FROM tbl_lend");
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$qr = dbQuery("SELECT id_lend_detail, id_product_attribute FROM tbl_lend_detail WHERE id_lend = ".$rs['id_lend']);
			while($rd = dbFetchArray($qr))
			{
				$return_qty = return_qty($rs['id_order'], $rd['id_product_attribute']);
				$qd = dbQuery("UPDATE tbl_lend_detail SET return_qty = ".$return_qty." WHERE id_lend_detail = ".$rd['id_lend_detail']);
				$i++;
			}
			$o++;
		}
	}

?>
<h4>UPDATE  : <?php echo $o; ?>  Documents  With   <?php echo $i; ?>  items</h4><br/>
<?php elseif( isset( $_GET['update_lend_status']) ) : ?>
<?php
	$lend = new lend();
	$qs = dbQuery("SELECT * FROM tbl_lend");
	$o = 0;
	$i  = 0;
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$qr = dbQuery("SELECT id_lend_detail FROM tbl_lend_detail WHERE id_lend =".$rs['id_lend']);
			if( dbNumRows($qr) > 0 )
			{
				while($rd = dbFetchArray($qr))
				{
					$valid = $lend->isValid($rd['id_lend_detail']);
					if($valid)
					{
						$qa = dbQuery("UPDATE tbl_lend_detail SET valid = 1 WHERE id_lend_detail = ".$rd['id_lend_detail']);
						$i++;	
					}
				}
				$lend->change_lend_status($rs['id_lend'], 2);
				$o++;
			}
		}
	}

?>
<h4>Check :  <?php echo $o; ?>  Documents  AND Update  <?php echo $i; ?> Items </h4><br>

<?php else : ?>
<table class="table">
<tr><td></td>ID<td>reference</td><td>action</td></tr>
<?php
	$qs = dbQuery("SELECT * FROM tbl_order WHERE role = 3");
	while($rs = dbFetchArray($qs)) : 
	if( $rs['current_state'] != 8 ) :
?>
<tr>
	<td><?php echo $rs['id_order']; ?></td>
    <td><?php echo $rs['reference']; ?></td>
    <td>
		<?php if(!is_exists($rs['id_order'])) : ?>
        <a href="<?php echo WEB_ROOT; ?>invent/test.php?add_lend&id_order=<?php echo $rs['id_order']; ?>"><button type="button" class="btn btn-primary">action</button></a>
        <?php endif; ?>
	</td>
</tr>

<?php	
		endif;
	endwhile;
	
?>
</table>

<?php endif; ?>
</div>

</body>

</html>
