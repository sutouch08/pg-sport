<?php
	$page_name 	= "รับสินค้าเข้า จากการซื้อ";
	$id_tab 			= 47;
	$id_profile 		= $_COOKIE['profile_id'];
  $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);

	if(isset($_GET['add']))
	{
		if(isset($_GET['id_receive_product']))
		{
			include('include/receive_product/receive_add.php');
		}
		else
		{
			include('include/receive_product/receive_new_add.php');
		}

	}
	elseif(isset($_GET['review']) && isset($_GET['id_receive_product']))
	{
		include('include/receive_product/receive_review.php');
	}
	elseif(isset($_GET['edit']) && isset($_GET['id_receive_product']))
	{
		include('include/receive_product/receive_edit.php');
	}
	elseif(isset($_GET['view_detail']) && isset($_GET['id_receive_product']))
	{
		include('include/receive_product/receive_detail.php');
	}
	else
	{
		include('function/supplier_helper.php');
		include('include/receive_product/receive_list.php');
	}

?>

<script src="script/receive_product/receive_product.js?token=<?php echo date('YmdH'); ?>"></script>
<?php if(!isset($_GET['id_receive_product'])) : ?>
<script src="script/receive_product/receive_list.js?token=<?php echo date('YmdH'); ?>"></script>
<?php endif; ?>
