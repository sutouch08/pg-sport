<?php
	$id_tab 	= 43;
    $pm 		= checkAccess($id_profile, $id_tab);
	$view 	= $pm['view'];
	$add 		= $pm['add'];
	$edit 		= $pm['edit'];
	$delete 	= $pm['delete'];
	accessDeny($view);
	include 'function/transfer_helper.php';
	include 'function/product_helper.php';
?>	
<input type="hidden" id="canAdd" value="<?php echo $add; ?>" />
<input type="hidden" id="canEdit" value="<?php echo $edit; ?>" />
<input type="hidden" id="canDelete" value="<?php echo $delete; ?>" />
<div class="container">
    <div class="row top-row">
    	<div class="col-sm-6 top-col">
        	<h4 class="title"><?php echo $pageTitle; ?></h4>
        </div>
        <div class="col-sm-6">
        	<p class="pull-right top-p">
	<?php if( $add && ! isset( $_GET['add'] ) && ! isset( $_GET['edit'] ) && ! isset( $_GET['view_detail'] ) ) : ?>
    			<button type="button" class="btn btn-sm btn-success" onclick="getNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    <?php endif; ?>       
    
    <?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) OR isset( $_GET['view_detail'] ) ) : ?>
    			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php endif; ?>    
            </p>
        </div>
    </div><!--/ row -->
    <hr />

<?php 
	if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) )
	{
		if( isset( $_GET['barcode'] ) )
		{
			include 'include/transfer_add_barcode.php';
		}
		else
		{
			include 'include/transfer_add.php';
		}
	}
	else if( isset( $_GET['view_detail'] ) )
	{
		include 'include/transfer_view.php';
	}
	else
	{
		include 'include/transfer_list.php';
	}
?>
</div><!--/ container -->

<script src="script/transfer.js"></script>
<script src="../library/js/beep.js"></script>