<?php 
	$id_tab 		= 7;
	$id_profile 	= getCookie('profile_id');
    $pm 			= checkAccess($id_profile, $id_tab);
	$view 		= $pm['view'];
	$add 			= $pm['add'];
	$edit 			= $pm['edit'];
	$delete 		= $pm['delete'];
	accessDeny($view);
	include 'function/transform_helper.php';
	?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col">
  		<h4 class="title">
        	<i class="fa fa-retweet"></i>  <?php echo $pageTitle; ?></h4>
        </h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <?php if( ! isset( $_GET['add'] ) && ! isset( $_GET['edit'] ) && ! isset( $_GET['view_detail'] ) && $add ) : ?>
        	<button type="button" class="btn btn-sm btn-success" onClick="newTransform()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php else : ?>
        	<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php endif; ?>
        
        </p>
    </div>
</div><!--/ row -->
<hr/>
<?php if( isset( $_GET['add'] ) ) : ?>
<?php
	$order		= isset( $_GET['id_order'] ) ? new order($_GET['id_order']) : FALSE;
	$id_order	= $order === FALSE ? '' : $order->id_order;
	$reference	= $order === FALSE ? '' : $order->reference;
	$date			= $order === FALSE ? date('d-m-Y') : $order->date_add;
	$emp			= $order === FALSE ? '' : employee_name($order->id_employee);
	$customer	= $order === FALSE ? '' : customer_name($order->id_customer);
	$role			= $order === FALSE ? '' : $order->role;
	$remark		= $order === FALSE ? '' : $order->comment;
	$active		= $order === FALSE ? '' : 'disabled';
?>
<div class="row"><!-- row101 -->
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
		<span class="form-control input-sm" <?php echo $active; ?>><?php echo $reference; ?></span>
	</div>
    <div class="col-sm-1 col-1-harf">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm text-center" name="dateAdd" id="dateAdd" value="<?php echo $date; ?>" <?php echo $active; ?> />
    </div>
    <div class="col-sm-3">
    	<label>ผู้เบิก</label>
        <input type="text" class="form-control input-sm" name="emp" id="emp" value="<?php echo $emp; ?>" <?php echo $active; ?> />
    </div>
    <div class="col-sm-2">
    	<label>วัตถุประสงค์</label>
        <select class="form-control input-sm" name="role" id="role">
        	<?php echo selectTransformRole($role); ?>
        </select>
    </div>
    <div class="col-sm-3">
    	<label>อ้างถึงลูกค้า</label>
        <input type="text" class="form-control input-sm" name="customer" id="customer" value="<?php echo $customer; ?>"  <?php echo $active; ?> />
    </div>
    <div class="col-sm-10">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $remark; ?>" <?php echo $active; ?> />
    </div>
    <div class="col-sm-2">
    	<label class="display-block not-show">แก้ไข</label>
    <?php if( $order === FALSE && $add ) : ?>
    	<button type="button" class="btn btn-sm btn-success" onClick="addNewTransform()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
	<?php elseif( $order !== FALSE && $add ) : ?>
    	<button type="button" class="btn btn-sm btn-warning" id="btn-edit-doc" onClick="getEdit(<?php echo $id_order; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success hide" id="btn-update-doc" onClick="updateTransform(<?php echo $id_order; ?>)"><i class="fa fa-save"></i> อัพเดต</button>
    <?php endif; ?>        
    </div>
</div><!--/ row101 -->
<hr class="margin-top-15"/>    

<?php elseif( isset( $_GET['edit'] ) ) : ?>


<?php elseif( isset( $_GET['view_detail'] ) ) : ?>


<?php else: ?>
<?php
	$reference 	= isset( $_POST['t_ref'] ) ? $_POST['t_ref'] : (getCookie('t_ref') ? getCookie('t_ref') : '');
	$employee	= isset( $_POST['t_emp'] ) ? $_POST['t_emp'] : (getCookie('t_emp') ? getCookie('t_emp') : '');
	$customer	= isset( $_POST['t_cus'] ) ? $_POST['t_cus'] : (getCookie('t_cus') ? getCookie('t_cus') : '');
	$fromDate	= isset( $_POST['from_date'] ) ? $_POST['form_date'] : (getCookie('from_date') ? getCookie('from_date') : '');
	$toDate		= isset( $_POST['to_date'] ) ? $_POST['to_date'] : (getCookie('to_date') ? getCookie('to_date') : '');
	$role_2		= isset( $_POST['role_2'] ) ? $_POST['role_2'] : (getCookie('role_2') ? getCookie('role_2') : 0);
	$role_6		= isset( $_POST['role_6'] ) ? $_POST['role_6'] : (getCookie('role_6') ? getCookie('role_6') : 0);
	$remark		= isset( $_POST['remark'] ) ? $_POST['remark'] : (getCookie('remark') ? getCookie('remark') : '' );
	$active2		= $role_2 == 1 ? 'btn-primary' : '';
	$active6		= $role_6 == 1 ? 'btn-primary' : '';

?>
<form id="searchForm">
<div class="row"><!-- row401 -->
	<div class="col-sm-2 padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" name="t_ref" id="t_ref" value="<?php echo $reference; ?>" placeholder="กรองตามเลขที่เอกสาร" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label>ผู้เบิก</label>
        <input type="text" class="form-control input-sm" name="t_emp" id="t_emp" value="<?php echo $employee; ?>" placeholder="กรองตามชื่อผู้เบิก" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" name="t_cus" id="t_cus" value="<?php echo $customer; ?>" placeholder="กรองตามชื่อลูกค้า" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label class="display-block">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" name="from_date" id="from_date" value="<?php echo $fromDate; ?>" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" name="to_date" id="to_date" value="<?php echo $toDate; ?>" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label class="display-block">วัตถุประสงค์</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm width-50 <?php echo $active2; ?>" id="btn-role-2" onClick="toggleRole(2)">เพื่อขาย</button>
            <button type="button" class="btn btn-sm width-50 <?php echo $active6; ?>" id="btn-role-6" onClick="toggleRole(6)">เพื่อสปอนเซอร์</button>
        </div>
    </div>
    <div class="col-sm-2 padding-5 last">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="t_remark" id="t_remark" value="<?php echo $remark; ?>" placeholder="ค้นหาหมายเหตุ" />
    </div>
</div><!--/row401 -->
<hr class="margin-top-15"/>
<input type="hidden" name="role_2" id="role_2" value="<?php echo $role_2; ?>" />
<input type="hidden" name="role_6" id="role_6" value="<?php echo $role_6; ?>" />
</form>


<?php endif; ?>	



</div><!--/ container -->
<script src="script/transform.js"></script>