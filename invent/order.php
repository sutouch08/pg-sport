<script src="<?php echo WEB_ROOT; ?>library/js/clipboard.min.js"></script>
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.md5.js"></script>
<?php
	$page_name	= "ออเดอร์";
	$id_tab 			= 14;
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/order_helper.php';
	include "function/address_helper.php";

	//-------------  ตรวจสอบออเดอร์ที่หมดอายุทุกๆ 24 ชั่วโมง  -----------//
	if( ! getCookie('expirationCheck') )
	{
		orderExpiration();
	}
	//-------------/  ตรวจสอบออเดอร์ที่หมดอายุทุกๆ 24 ชั่วโมง  /-----------//
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-shopping-bag"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
	<?php if( isset($_GET['add'] ) || isset( $_GET['edit'] ) || isset( $_GET['view_stock'] ) ) : ?>
    	<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php endif; ?>

    <?php if( isset( $_GET['add'] ) && isset( $_GET['id_order'] ) && $add ) : ?>
    <?php 	if( isSaved($_GET['id_order']) === FALSE ) : ?>
        	<button type="button" class="btn btn-success btn-sm" onClick="save(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-save"></i> บันทึก</button>
    <?php 	endif; ?>
    <?php endif; ?>

    <?php if( isset( $_GET['edit'] ) && isset( $_GET['id_order'] ) && isset( $_GET['view_detail'] ) ) : ?>
    	<?php $order = new order($_GET['id_order']); ?>
    	<?php if( $order->valid == 0 && ($order->current_state ==1 || $order->current_state == 3 ) ) : ?>
        	<?php if( $edit OR $add ) : ?>
        	<button type="button" class="btn btn-warning btn-sm" onClick="getEdit(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( !isset($_GET['add'] ) && !isset( $_GET['edit'] ) && !isset( $_GET['view_stock'] ) ) : ?>
    	<?php if( $add ) : ?>
       <!-- <button type="button" class="btn btn-primary btn-sm" onClick="addNewOnline()"><i class="fa fa-plus"></i> เพิ่มใหม่ ( ออนไลน์ )</button> -->
        <button type="button" class="btn btn-success btn-sm" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่ ( ปกติ )</button>
		<?php endif; ?>
        <button type="button" class="btn btn-info btn-sm" onClick="viewStock()"><i class="fa fa-search"></i> ดูสต็อกคงเหลือ</button>
    <?php endif; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />

<?php
//*********************************************** เพิ่มออเดอร์ใหม่ ********************************************************//
if(isset($_GET['add'])) :
	$vt_c					= getCookie('viewType') ? getCookie('viewType') : '';
	$user_id 			= $_COOKIE['user_id'];
	$id_order			= isset($_GET['id_order']) ? $_GET['id_order'] : '';
	$active 				= isset($_GET['id_order']) ? 'disabled' : '';
	$order 				= isset($_GET['id_order']) ? new order($id_order) : '';
	$new_ref 			= isset($_GET['id_order']) ? $order->reference: get_max_role_reference("PREFIX_ORDER",1);
	$customer 			= isset($_GET['id_order']) ? new customer($order->id_customer) : '';
	$id_customer 		= isset($_GET['id_order']) ? $customer->id_customer : '';
	$customer_name 	= isset($_GET['id_order']) ? $customer->full_name : '';
	$comment 			= isset($_GET['id_order']) ? $order->comment : '';
	$payment 			= isset($_GET['id_order']) ? $order->payment : '';
	$onlineCustomer	= isset($_GET['id_order']) ? getCustomerOnlineReference($id_order) : '';
	$date_add  = isset($_GET['id_order']) ? thaiDate($order->date_add) : date('d-m-Y');

?>
<form id='addForm'>
<div class='row'>
	<input type='hidden' name='id_employee' value='<?php echo $user_id; ?>' />
    <input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
    <input type='hidden' name='id_customer' id='id_customer' value='<?php echo $id_customer; ?>' />
    <input type="hidden" name="role" id="role" value="1" />
	<div class='col-sm-2'>
    	<label>เลขที่เอกสาร</label>
        <input type='text' id='doc_id' class='form-control input-sm' value='<?php echo $new_ref; ?>' disabled='disabled'/>
    </div>
	<div class='col-sm-2'>
		<label>วันที่</label>
		<input type='text' id='doc_date' name='doc_date' class='form-control input-sm text-center' value='<?php echo $date_add; ?>' <?php echo $active; ?> />
    </div>
	<div class='col-sm-4'>
        	<label>ชื่อลูกค้า</label>
            <input type='text' id='customer_name' class='form-control input-sm' value='<?php echo $customer_name; ?>' autocomplete='off' <?php echo $active; ?> />
    </div>
    <?php if( isset( $_GET['online'] ) OR $payment == 'ออนไลน์' ) : ?>
    <div class="col-sm-2">
    	<label>อ้างอิงลูกค้า</label>
        <input type="text" name="online" id="online" class="form-control input-sm" value="<?php echo $onlineCustomer; ?>" <?php echo $active; ?> />
        <input type="hidden" name="payment" id="payment" value="ออนไลน์" />
    </div>
    <?php else : ?>
	<div class='col-sm-2'>
        <label>การชำระเงิน</label>
        <select name='payment' id='payment' class='form-control input-sm' <?php echo $active; ?> ><?php echo paymentMethod($payment); ?></select>
    </div>
    <?php endif; ?>
	<div class='col-sm-10'>
		<label>หมายเหตุ</label>
    	<input type='text' id='comment' name='comment' class='form-control input-sm' value='<?php echo $comment; ?>' autocomplete='off' <?php echo $active; ?> />
    </div>
	<div class='col-sm-2'>
    	<label style="display:block; visibility:hidden">button</label>
    <?php if( !isset( $_GET['id_order'] ) ) : ?>
    	<?php 	if( $add ) : ?>
		<button class='btn btn-default btn-sm btn-block' type='button' id='btnAdd' onClick="newOrder()">สร้างออเดอร์</button>
		<?php 	endif; ?>
	<?php else : ?>
		<?php if( $edit ) : ?>
        	<button class='btn btn-default btn-sm btn-block' type='button' id='btnEdit' onClick="editOrder()"><i class="fa fa-pencil"></i> แก้ไขออเดอร์</button>
            <button type="button" class="btn btn-sm btn-success btn-block" id="btnUpdate" onClick="updateOrder(<?php echo $id_order; ?>)" style="display:none;"><i class="fa fa-save"></i> ปรับปรุง</button>
		<?php endif; ?>
	<?php endif; ?>
    </div>
</div><!--/ row -->
</form>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />

<?php if( isset( $_GET['id_order'] ) ) :  ?>

<div class='row'>
	<div class="col-sm-3">
    	<input type="text" class="form-control input-sm text-center" id="sProduct" placeholder="ค้นหาสินค้า" />
    </div>
    <div class="col-sm-2">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onClick="getProduct()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
    </div>
</div>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />

<!----------------------------------------- Category Menu ---------------------------------->
<div class='row'>
	<div class='col-sm-12'>
		<ul class='nav navbar-nav' role='tablist' style='background-color:#EEE'>
		<?php echo categoryTabMenu('order'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>
		<div class='tab-content' style="min-height:1px; padding:0px;">
		<?php echo getCategoryTab(); ?>
		</div>
	</div>
</div>
<!------------------------------------ End Category Menu ------------------------------------>

<form id="gridForm">
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
                    <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
                    <input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />
				 </div>
				 <div class='modal-body' id='modal_body'></div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onClick="addToOrder(<?php echo $id_order; ?>)" >เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
</form>

<div class='row'>
<div class='col-sm-12'>
	<table class='table' id='order_detail' style="border: solid 1px #ddd;">
	<thead>
    <tr style='font-size: 12px;'>
		<th stype='width:5%; text-align:center;'>ลำดับ</th>
        <th style='width:5%; text-align:center;'>รูป</th>
        <th style='width:10%;'>บาร์โค้ด</th>
        <th style='width:30%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
		<th style='width:10%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='text-align:center;'>การกระทำ</th>
	</tr>
    </thead>
    <tbody id="orderProductTable">
<?php	$qs 		= dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order." ORDER BY id_order_detail DESC"); 	?>
<?php	$n 		= 1;	?>
<?php	$tq 		= 0;	?>
<?php	if( dbNumRows($qs) > 0 ) :	?>
<?php		$product = new product();	?>
<?php		while( $rs = dbFetchArray($qs) ) :		?>
		<tr style='font-size: 12px;'>
        	<td style='text-align:center; vertical-align:middle;'><?php echo $n; ?></td>
            <td style='text-align:center; vertical-align:middle;'><img src='<?php echo $product->get_product_attribute_image($rs['id_product_attribute'], 1); ?>' width='35px' height='35px' /> </td>
            <td style='vertical-align:middle;'><?php echo $rs['barcode']; ?></td>
            <td style='vertical-align:middle;'><?php echo $rs['product_reference']." : ".$rs['product_name']; ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['product_price'], 2); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['product_qty']); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo discountLabel($rs['reduction_percent'], $rs['reduction_amount']);  ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['total_amount'], 2); ?></td>
            <td style='text-align:center; vertical-align:middle;'>
            	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow(<?php echo $rs['id_order_detail']; ?>, '<?php echo $rs['product_reference']; ?>')"><i class="fa fa-trash"></i></button>
            </td>
      	</tr>
<?php	$tq += $rs['product_qty'];	$n++;		?>
<?php endwhile; ?>
	<tr>
		<td colspan='6'></td>
        <td><h4>จำนวน</h4></td>
        <td style='text-align:center; vertical-align:middle;'><h4><?php echo number_format($tq); ?></h4></td>
        <td><h4>ชิ้น<h4></td>
	</tr>
<?php else : ?>
	<tr>
    	<td colspan='9' align='center'><h4>&nbsp;</h4></td>
    </tr>
<?php endif; ?>
	</tbody>
</table>
</div>
</div>
<?php endif; ?>

<!-----------------------------------------------------------จบหน้าเพิ่มออเดอร์ ---------------------------------------------->
<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_order'] ) ) : ?>
<!--------------------------------------------------------- แก้ไขออเดอร์ ----------------------------------------------------->

<?php
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	if($order->id_customer != "0") :
		$customer = new customer($order->id_customer);
		$customer->customer_stat();
	endif;
	$sale = new sale($order->id_sale);
	$state = dbQuery("SELECT * FROM tbl_order_state_change WHERE id_order = ".$id_order." ORDER BY date_add DESC, id_order_state_change DESC");
	$role = $order->role;
	$fee = getDeliveryFee($id_order);
	$onlineCustomer = getCustomerOnlineReference($id_order);
	$online = $order->payment == 'ออนไลน์' ? TRUE : FALSE;
?>
<div class="row">
	<div class="col-sm-12">
    	<h5 class="title">
		<?php 	echo $order->reference." - ";  	if($order->id_customer != "0") : echo $customer->full_name; endif; ?>
        <?php 	if( $online && $onlineCustomer != '') : echo ' ( '.$onlineCustomer.' ) '; endif; ?>
        <p class='pull-right'>พนักงาน : &nbsp; <?php echo $sale->full_name; ?></p>
        </h5>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
<div class="row">
	<div class="col-sm-12">
		<dl><dt>วันที่สั่ง : <dd><?php echo thaiDate($order->date_add); ?></dd> | </dt></dl>
		<dl><dt>สินค้า : <dd><?php echo number_format($order->total_product); ?></dd> | </dt></dl>
		<dl><dt>จำนวน : <dd><?php echo number_format($order->total_qty); ?></dd> | </dt></dl>
		<dl><dt>ยอดเงิน : <dd><?php echo number_format($order->total_amount,2); ?></dd> </dt></dl>

        <p class='pull-right' style="margin-bottom:0px;">
        <?php if( $online ) : ?>
			<?php if( ! $fee ) : ?>
                <input type="text" id="deliveryFee" class="form-control input-sm input-mini" style="display:none;" placeholder="ค่าจัดส่ง" />
                <button type="button" id="btn-add-fee" class="btn btn-sm btn-info" onClick="addDeliveryFee()" ><i class="fa fa-plus"></i> เพิ่มค่าจัดส่ง</button>
                <button type="button" id="btn-update-fee" class="btn btn-sm btn-success" style="display:none;" onClick="updateDeliveryFee(<?php echo $id_order; ?>)" ><i class="fa fa-save"></i> บันทึกค่าส่ง</button>
            <?php else : ?>
                <input type="text" id="deliveryFee" class="form-control input-sm input-mini" style="display:inline; " placeholder="ค่าจัดส่ง" value="<?php echo $fee; ?>" disabled />
                <?php if( $edit OR $add ) : ?>
                <button type="button" id="btn-edit-fee" class="btn btn-sm btn-warning" onClick="editDeliveryFee()" ><i class="fa fa-pencil"></i> แก้ไขค่าจัดส่ง</button>
                <button type="button" id="btn-update-fee" class="btn btn-sm btn-success" style="display:none;" onClick="updateDeliveryFee(<?php echo $id_order; ?>)" ><i class="fa fa-save"></i> บันทึกค่าส่ง</button>
                <?php endif; ?>
            <?php endif; ?>
                <button type="button" class="btn btn-sm btn-primary" onClick="getSummary()"><i class="fa fa-list"></i> ข้อมูลสรุป</button>
        <?php endif; ?>
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info btn-sm" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
			<button type="button" class="btn btn-success btn-sm" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
        </p>
	</div>
</div><!-- /row -->
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<div class='row'>
	<form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>
	<div class='col-sm-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
        	<tr>
				<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td>
                <td style='width:40%; padding-right:10px;'>
        			<input type='hidden' name='id_order' id="id_order" value='<?php echo $order->id_order; ?>' />
                    <input type='hidden' name='id_employee' value='<?php echo $id_employee; ?>' />
                    <?php if( $edit ) : ?>
                        <select name='order_state' id='order_state' class='form-control input-sm'>
                            <?php echo orderStateList($order->id_order); ?>
                        </select>
					<?php endif; ?>
                </td>
                <td style='padding-right:10px;'>
                <?php if($edit) : ?>
               	 	<button class='btn btn-default' type='button' onclick='state_change()' $can_edit>เพิ่ม</button>
                <?php endif; ?>
                </td>
            </tr>
<?php	if(dbNumRows($state) > 0 ) :		?>
<?php		while($rd = dbFetchArray($state) ) :	?>
                <tr  style='background-color:<?php echo state_color($rd['id_order_state']); ?>'>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $order->stateName($rd['id_order_state']); ?></td>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo employee_name($rd['id_employee']); ?></td>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo thaiDateTime($rd['date_add']); ?></td>
                </tr>
<?php		endwhile;		?>
<?php else :	?>
            <tr>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $order->currentState(); ?></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($order->date_upd)); ?></td>
            </tr>
<?php endif; ?>
 		</table>
 	</div>
    </form>

</div><!-- /row-->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<form id='editOrderForm'>
<div class='row'>
    <div class='col-sm-12'>
<!---------------------------------------------------------------------  Order Table  ----------------------------------------------------------->
<?php
	$qm = dbQuery("SELECT discount_amount FROM tbl_order_discount WHERE id_order = ".$id_order);
	if( dbNumRows($qm) )
	{
		$rm = dbFetchArray($qm);
		$l_discount = $rm['discount_amount'];
	}else{
		$l_discount = 0;
	}
	$orderTxt	= '';
?>

<?php if($order->current_state != 9 && $order->current_state != 8 ) : ?>
	<?php if($edit || $add) : ?>
        <button type='button' id='edit_reduction' class='btn btn-default' >แก้ไขส่วนลด</button>
        <button type='button' id='save_reduction' class='btn btn-primary' onclick="verifyPassword()" style="display:none;" >บันทึกส่วนลด</button>
        <button type="button" id="btn-edit-price" class="btn btn-default" onClick="getEditPrice()">แก้ไขราคา</button>
        <button type="button" id="btn-update-price" class="btn btn-primary" onClick="confirmEditPrice()" style="display:none;">บันทึกราคา</button>
       <?php if(!$l_discount) : ?>
       		<button type="button" id="btn_add_discount" class="btn btn-default" ><i class="fa fa-plus"></i>&nbsp;เพิ่มส่วนลดท้ายบิล</button>
            <button type="button" id="btn_save_discount" class="btn btn-success" onclick="add_discount()" style="display:none;"><i class="fa fa-save"></i>&nbsp;บันทึกส่วนลดท้ายบิล</button>
       <?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
	<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc; margin-top:10px;'>
    <thead>
    	<th style='width:10%; text-align:center;'>รูปภาพ</th>
        <th>สินค้า</th>
        <th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:12%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='width:5% text-align:center;'></th>
    </thead>
    <tbody id="orderTable">
<?php	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);		?>
<?php 	$orderTxt = 'สรุปการสั่งซื้อ<br/> Order No : '.$order->reference.' <br/>';		?>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php	if( dbNumRows($qs) > 0 ) :	?>
<?php	$total_disc = 0;  $total_price = 0; $total_amount = 0; 	?>
<?php		$product = new product();	?>
<?php		while( $rs = dbFetchArray($qs) ) : 	?>
<?php			$id 		= $rs['id_order_detail'];	?>
<?php 			$id_pa 	= $rs['id_product_attribute']; 	?>
<?php 			$disc		= $rs['reduction_percent'] > 0 ? $rs['reduction_percent'] : $rs['reduction_amount']; 	?>
				<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
                    <td style='text-align:center; vertical-align:middle;'><img src="<?php echo $product->get_product_attribute_image($id_pa,1); ?>" width="35px" height="35px" /></td>
                    <td style='vertical-align:middle;'><?php echo $rs['product_reference']." : ".get_product_name($rs['id_product'])." : ".$rs['barcode']; ?></td>
                    <td style='text-align:center; vertical-align:middle;'>
                    	<span id="price_<?php echo $id; ?>" class="price_label"><?php echo number_format($rs['product_price'], 2); ?></span>
                        <input type="text" class="form-control input-sm input-price" name="price[<?php echo $id; ?>]" id="price<?php echo $id; ?>"
                        		 value="<?php echo $rs['product_price']; ?>" style="display:none;" />
                    </td>
                    <td style='text-align:center; vertical-align:middle;'>
                        <p class='reduction'><?php echo discountLabel($rs['reduction_percent'], $rs['reduction_amount']); ?></p>
                       <div class='input_reduction' style='display:none;'>
                       		<input type='text' class='form-control input-sm input-discount' id="reduction<?php echo $id; ?>"
                       				name="reduction[<?php echo $id; ?>]" value='<?php echo $disc; ?>' onKeyUp="verifyDiscount(<?php echo $id; ?>, '<?php echo $rs['product_price']; ?>')" />
                            <select class="form-control input-sm input-unit" id="unit<?php echo $id; ?>" name="unit[<?php echo $id; ?>]" onChange="verifyDiscount(<?php echo $id; ?>, '<?php echo $rs['product_price']; ?>')" >
                                <option value="percent" <?php if( $rs['reduction_percent'] > 0 ) { echo "selected"; } ?> >%</option>
                                <option value="amount" <?php if( $rs['reduction_amount'] > 0 ) { echo "selected"; } ?> >฿</option>
                            </select>
                        </div>
                    </td>
                    <td style='text-align:center; vertical-align:middle;'>	<?php echo number_format($rs['product_qty']); ?></td>
                    <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['total_amount'], 2); ?></td>
                    <td style='text-align:center; vertical-align:middle;'>
                    <?php if($edit && ($order->current_state == 3 || $order->current_state == 1 ) ) : ?>
                            <button type="button" class="btn btn-danger btn-sm" onClick="deleteItem(<?php echo $id; ?>, '<?php echo $rs['product_reference']; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
<?php
					$orderTxt .=   $rs['product_reference'].' :  ('.number_format($rs['product_qty']).') x '.number_format($rs['product_price'], 2).' <br/>';
					$total_disc 		+= $rs['discount_amount'];
					$total_price 	+= $rs['product_qty'] * $rs['product_price'];
					$total_amount 	+= $rs['total_amount'];
			endwhile;

?>
<?php if( $l_discount ) : ?>
		<tr id="last_discount_row" >
        	<td colspan="5" style="text-align: right; vertical-align:middle; padding-right:20px;">ส่วนลดท้ายบิล</td>
            <td style='text-align:center; vertical-align:middle;'>
            	<span id="discount_label"><?php echo number_format($l_discount, 2); ?></span>
                <input type="text" id="last_discount" class="form-control input-sm" style="text-align:right; display:none;" value="<?php echo $l_discount; ?>" />
            </td>
            <td style='text-align:center; vertical-align:middle;'>
            <?php if($order->current_state == 3 || $order->current_state == 1 ) : ?>
            	<?php if($edit) : ?>
            	<button type="button" class="btn btn-warning" id="btn_edit_discount" onclick="edit_discount()"><i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-danger" id="btn_delete_discount" onclick="action_delete(<?php echo $id_order.", ".number_format($l_discount, 2); ?>)"><i class="fa fa-trash"></i></button>
                <button type="button" class="btn btn-success" id="btn_update_discount" style="display:none;"><i class="fa fa-save"></i>&nbsp; Update</button>
                <?php endif; ?>
            <?php endif; ?>
            </td>
        </tr>
<?php else : ?>
		<tr id="last_discount_row" style="display:none;" >
        	<td colspan="5" align="right" style="padding-right:20px;">ส่วนลดท้ายบิล</td>
            <td><input type="text" id="last_discount" class="form-control input-sm" style="text-align:right" /></td>
            <td>บาท</td>
        </tr>
<?php endif; ?>

		<tr>
			<td rowspan='3' colspan='4'></td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td>
            <td colspan='2' align='right' id="total_price"><b><?php echo number_format($total_price,2); ?> </b></td>
       </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td>
        	<td colspan='2' align='right' id="total_disc"><b><?php echo number_format(($total_disc + $l_discount), 2); ?> </b></td>
        </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>สุทธิ </b></td>
        	<td colspan='2' align='right' id="net"><b><?php echo number_format(($total_amount-$l_discount),2); ?> </b></td>
        </tr>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php 	if( ($total_disc + $l_discount) > 0 )
			{
				$orderTxt .= 'ส่วนลดรวม'.getSpace(number_format( ($total_disc + $l_discount), 2), 27).'<br/>';
			 	$orderTxt .= '--------------------------------------- <br/>';
			}
?>
<?php 	if( $fee > 0 ){
				$orderTxt .= 'ค่าจัดส่ง'.getSpace(number_format($fee, 2), 31).'<br/>';
			 	$orderTxt .= '--------------------------------------- <br/>';
			}
?>
<?php	$orderTxt .= 'ยอดชำระ' . getSpace(number_format( ($total_amount - $l_discount)+$fee, 2), 29).'<br/>';	?>
<?php 	$orderTxt .= '---------------------------------------';	?>
<?php	else :  ?>
		<tr>
            <td colspan='7' align='center'><h4>ไม่มีรายการสินค้า</h4></td>
       	</tr>

<?php endif;  ?>
		</tbody>
   	</table>

<!--------------------------------------------------------------------  End order table  --------------------------------------------------------->
	</div>
</div>
<div class='row'>
	<div class='col-sm-12'>
    	<p><h4>ข้อความ :  <?php if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; } ?></h4></p>
    </div>
</div>
<h4>&nbsp;</h4>
</form>
<!----------  สรุปยอดส่ง Line --------->
<div class='modal fade' id='orderSummaryTab' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
            <div class='modal-body' id="summaryText">
            		<?php echo $orderTxt; ?>
            </div>
            <div class='modal-footer'>
                <button class="btn btn-sm btn-info btn-block" data-dismiss='modal' data-clipboard-action="copy" data-clipboard-target="#summaryText">Copy</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee' />
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus" />
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='checkPassword()' />
			</div>
			<p style='text-align:center; color:red;' id='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#ModalLogin').on('shown.bs.modal', function () {  $('#password').focus(); });
$("#password").keyup(function(e) { if(e.keyCode == 13 ){ checkPassword(); }});
</script>

<div class='modal fade' id='modal_approve' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจอนุมัติส่วนลด</h4>
			</div>
			<input type='hidden' id='id_approve' name='id_approve'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='bill_password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus">
				</div>
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_password()' />
			</div>
			<p style='text-align:center; color:red;' id='bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#modal_approve').on('shown.bs.modal', function () {  $('#bill_password').focus(); });
</script>

<div class='modal fade' id='modal_approve_edit' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจอนุมัติส่วนลด</h4>
			</div>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='edit_bill_password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus">
				</div>
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_approve()' >
				<!--userForm-->
			</div>
			<p style='text-align:center; color:red;' id='edit_bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#modal_approve_edit').on('shown.bs.modal', function () {  $('#edit_bill_password').focus(); });
</script>

<div class='modal fade' id='editPriceModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' >รหัสลับผู้มีอำนาจอนุมัติราคา</h4>
			</div>
			<div class='modal-body'>
            	<div class="row">
                	<div class="col-sm-12"><input type="password" id="confirmPricePassword" class="form-control input-sm text-center" /></div>

                    <div class="col-sm-12 top-col"><button type="button" class="btn btn-primary btn-block" onClick="validConfirmPrice()">ยืนยัน</button></div>
                    <div class="col-sm-12 top-col text-center" style="padding-bottom:20px;"><span id="confirmPrice-error" class="label-left red" style="margin-left:15px; margin-top:15px;">ตัวเลขไม่ถูกต้อง</span></div>
                </div>
			</div>
		</div>
	</div>
</div>
<!---------------------------------------------------------------- จบหน้าแก้ไข -------------------------------------------------->
<?php elseif( isset( $_GET['view_stock'] ) ) : ?>
<!---------------------------------------------------- ดูยอดสต็อกคงเหลือนำยอดที่สั่งมาคำนวนแล้ว --------------------------------->
<!----------------------------------------- Category Menu ---------------------------------->
<div class='row'>
	<div class='col-sm-12'>
		<ul class='nav navbar-nav' role='tablist' style='background-color:#EEE'>
			<?php echo categoryTabMenu('view'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>
		<div class='tab-content' style="min-height:1px; padding:0px;">
		<?php echo getCategoryTab('view'); ?>
		</div>
	</div>
</div>
<!------------------------------------ End Category Menu ------------------------------------>
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='modal_title'></h4>
                <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
            </div>
            <div class='modal-body' id='modal_body'></div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>

<!---------------------------------------------------- จบหน้าดูสต็อก  ------------------------------------------------>
<?php else : ?>
<!----------------------------------------------------- แสดงรายการ -------------------------------------------------->
<?php
	$s_ref 	= isset( $_POST['s_ref'] ) ? $_POST['s_ref'] : ( getCookie('s_ref') ? getCookie('s_ref') : '');
	$s_cus 	= isset( $_POST['s_cus'] ) ? $_POST['s_cus'] : ( getCookie('s_cus') ? getCookie('s_cus') : '' );
	$s_emp	= isset( $_POST['s_emp'] ) ? $_POST['s_emp'] : ( getCookie('s_emp') ? getCookie('s_emp') : '');
	$from		= isset( $_POST['from_date'] ) ? $_POST['from_date'] : ( getCookie('orderFrom') ? getCookie('orderFrom') : '');
	$to		= isset( $_POST['to_date'] ) ? $_POST['to_date'] : ( getCookie('orderTo') ? getCookie('orderTo') : '');
	$vt			= isset( $_POST['viewType'] ) ? $_POST['viewType'] : (getCookie('viewType') ? getCookie('viewType') : 1 );
	$selectState = isset( $_POST['selectState'] ) ? $_POST['selectState'] : ( getCookie('selectState') ? getCookie('selectState') : '');
	$fhour 	= isset( $_POST['fhour'] ) ? $_POST['fhour'] : ( getCookie('fhour') ? getCookie('fhour') : '');
	$thour 	= isset( $_POST['thour'] ) ? $_POST['thour'] : ( getCookie('thour') ? getCookie('thour') : '');
	$state 	= array(
							'state_1'	=> isset( $_POST['state_1'] ) ? $_POST['state_1'] : (getCookie('state_1') ? getCookie('state_1') : 0), //-- รอชำระเงิน
							'state_3'	=> isset( $_POST['state_3'] ) ? $_POST['state_3'] : (getCookie('state_3') ? getCookie('state_3') : 0), //-- รอจัดสินค้า
							'state_4'	=> isset( $_POST['state_4'] ) ? $_POST['state_4'] : (getCookie('state_4') ? getCookie('state_4') : 0), //-- กำลังจัดสินค้า
							'state_5'	=> isset( $_POST['state_5'] ) ? $_POST['state_5'] : (getCookie('state_5') ? getCookie('state_5') : 0), //-- รอตรวจสินค้า
							'state_11'	=> isset( $_POST['state_11'] ) ? $_POST['state_11'] : (getCookie('state_11') ? getCookie('state_11') : 0), //-- กำลังตรวจสินค้า
							'state_10'	=> isset( $_POST['state_10'] ) ? $_POST['state_10'] : (getCookie('state_10') ? getCookie('state_10') : 0) //-- รอเปิดบิล
							);
	$stateName = array(
							'state_1'	=> 'รอชำระเงิน', //-- รอชำระเงิน
							'state_3'	=> 'รอจัดสินค้า', //-- รอจัดสินค้า
							'state_4'	=> 'กำลังจัดสินค้า', //-- กำลังจัดสินค้า
							'state_5'	=> 'รอ QC', //-- รอตรวจสินค้า
							'state_11'	=> 'กำลัง QC', //-- กำลังตรวจสินค้า
							'state_10'	=> 'รอเปิดบิล' //-- รอเปิดบิล
							);

	$all		= $vt == 1 ? 'btn-info' : '';
	$online	= $vt == 2 ? 'btn-info' : '';
	$normal 	= $vt == 3 ? 'btn-info' : '';


	if( isset( $_POST['from_date'] ) ){ createCookie('orderFrom', $from); }
	if( isset( $_POST['to_date'] ) ){ createCookie('orderTo', $to); }
	if( isset( $_POST['viewType'] ) ){ createCookie('viewType', $vt, 3600*24*60); }
	if( isset( $_POST['selectState'] ) ){ createCookie('selectState', $selectState, 3600*24*60); }
	if( isset( $_POST['fhour'] ) ){ createCookie('fhour', $fhour, 3600*24*60); }
	if( isset( $_POST['thour'] ) ){ createCookie('thour', $thour, 3600*24*60); }
	foreach($state as $key => $val){  if( isset( $_POST[$key] ) ){ createCookie($key, $val, 3600*24*60); } }
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);
?>
<form  method='post' id='searchForm'>
<div class="row">
	<div class="col-sm-2 padding-5" style="padding-left:15px;">
 		<label>เอกสาร</label>
        <input type="text" class="form-control input-sm" id="s_ref" name="s_ref" value="<?php echo $s_ref; ?>" placeholder="พิมพ์เลขที่เอกสาร" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="s_cus" name="s_cus" value="<?php echo $s_cus; ?>" placeholder="พิมพ์ชื่อลูกค้า" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>พนักงาน</label>
        <input type="text" class="form-control input-sm" id="s_emp" name="s_emp" value="<?php echo $s_emp; ?>" placeholder="พิมพ์ชื่อพนักงาน" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" id="from_date" name="from_date" value="<?php echo $from; ?>" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" id="to_date" name="to_date" value="<?php echo $to; ?>" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" id="btn-show-all" class="btn btn-sm width-33 <?php echo $all; ?>" onClick="showAll()" >ทั้งหมด</button>
            <button type="button" id="btn-show-online" class="btn btn-sm width-33 <?php echo $online; ?>"  onClick="showOnline()" >ออนไลน์</button>
            <button type="button" id="btn-show-normal" class="btn btn-sm width-33 <?php echo $normal; ?>" onClick="showNormal()">เครดิต</button>
        </div>
    </div>
    <div class="col-sm-1 col-sm-offset-1 padding-5 last">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()">Reset</button>
    </div>
    <div style="width:100%; float:left; height:1px; margin-top:5px; margin-bottom:5px;"></div>


    <?php $first = 1; ?>
    <?php foreach($state as $key => $val ) : ?>
    <?php	$st = $val == 1 ? 'btn-info' : ''; ?>
    <div class="col-sm-1 padding-5 <?php echo ($first == 1 ? 'first' : ''); ?>">
    	<label style="display:block; visibility:hidden;">&nbsp;</label>
		<button type="button" id="btn-<?php echo $key; ?>" class="btn btn-sm btn-block <?php echo $st; ?>" onclick="toggleState('<?php echo $key; ?>')"><?php echo $stateName[$key]; ?></button>
        <input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $val; ?>" />
	<?php $first++; ?>
	</div>
	<?php endforeach; ?>

    <div class="col-sm-2 padding-5">
    	<label style="display:block; visibility:hidden;">&nbsp;</label>
    	<select class="form-control input-sm" name="selectState" id="selectState">
        	<?php echo selectStateTime($selectState); ?>
        </select>
    </div>
    <div class="col-sm-1 col-1-harf padding-5">
    	<label>เริ่มต้น</label>
        <select class="form-control input-sm" name="fhour" id="fhour">
        	<?php echo selectTime($fhour); ?>
        </select>
    </div>
    <div class="col-sm-1 col-1-harf padding-5 last">
    	<label>สิ้นสุด</label>
        <select class="form-control input-sm" name="thour" id="thour">
        	<?php echo selectTime($thour); ?>
        </select>
    </div>
    <div class="col-sm-1 padding-5 last">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" class="btn btn-primary btn-sm btn-block" onClick="getSearch()">ใช้ตัวกรอง</button>
    </div>


</div>

<input type="hidden" name="viewType" id="viewType" value="<?php echo $vt; ?>" />
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
//--------------------  เงื่อนไข ------------------//

	$where = "WHERE order_status = 1 AND role IN(1, 4) ";

	if( $s_ref != '' OR $s_cus != '' OR $s_emp != '' OR $from != '' )
	{
		if( $s_ref != '' )
		{
			createCookie('s_ref', $s_ref);
			$where .= "AND reference LIKE '%".$s_ref."%' ";
		}
		if( $s_cus != '' )
		{
			createCookie('s_cus', $s_cus);
			if( $vt == 2 )
			{
				$in = onlineOrderByCustomer($s_cus);
				if( $in !== FALSE )
				{

					$where .= "AND id_order IN(".$in.") ";
				}
				else
				{
					$in = customer_in($s_cus);
					if( $in !== FALSE )
					{
						$where .= "AND id_customer IN(".$in.") ";
					}
					else
					{
						$where .= "AND id_customer = '' ";
					}
				}
			}
			else
			{
				$in = customer_in($s_cus);
				if( $in !== FALSE )
				{
					$where .= "AND id_customer IN(".$in.") ";
				}
				else
				{
					$where .= "AND id_customer = '' ";
				}
			}
		}
		if( $s_emp != '' )
		{
			createCookie('s_emp', $s_emp);
			$in = employee_in($s_emp);
			if( $in !== FALSE )
			{
				$where .= "AND id_employee IN(".$in.") ";
			}
			else
			{
				$where .= "AND id_employee = '' ";
			}
		}
		if( $from != '' )
		{
				if( $selectState != '' )
				{
					$from = dbDate($from).' '.$fhour.':00';
					$to	= $to == '' ? dbDate($from). ' '.$thour.':00' : dbDate($to). ' '.$thour.':00';
					$in 	= getOrderStateInTime($selectState, $from, $to);
					if( $in != FALSE )
					{
						$where .= "AND id_order IN(".$in.") ";
					}
					else
					{
						$where .= "AND id_order IN(0) ";
					}
				}
				else
				{
					$to	= $to == '' ? toDate($from) : toDate($to);
					$from = fromDate($from);
					$where .= "AND date_add>= '".$from."' AND date_add <='".$to."' ";
				}
		}
	}
	//$where .= "AND valid != 2 ";
	$where .= $vt == 2 ? "AND payment = 'ออนไลน์' " : ($vt == 3 ? "AND payment IN('เครดิต', 'เงินสด') " : '');
	$state_in = getStateIn($state);
	$where .= $state_in == '' ? "" : "AND current_state IN(".$state_in.") ";
	$where .= "ORDER BY id_order DESC";
?>
<div class='row' id='result'>
	<div class='col-sm-12' id='search-table'>
<?php
	$paginator->Per_Page("tbl_order",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=order");
	?>
		<table class="table" style="border:solid 1px #ccc;">
            <thead>
                <th style='width:5%; text-align:center;'>ID</th>
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:20%;'>ลูกค้า</th>
                <th style="width:10%;">จังหวัด</th>
                <th style='width:10%;'>พนักงาน</th>
                <th style='width:10%; text-align:center;'>ยอดเงิน</th>
                <th style='width:10%; text-align:center;'>การชำระเงิน</th>
                <th style='width:10%; text-align:center;'>สถานะ</th>
                <th style='width:8%; text-align:center;'>วันที่เพิ่ม</th>
                <th style='width:8%; text-align:center;'>วันที่ปรับปรุง</th>
            </thead>
<?php	$qs = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);		?>
<?php	if( dbNumRows($qs) > 0) :		?>
<?php 		while( $rs = dbFetchArray($qs) ) :			?>
<?php			$id = $rs['id_order'];		?>
<?php			$order = new order($id);		?>
<?php			$online = getCustomerOnlineReference($id); ?>
<?php			$customer_name = customer_name($order->id_customer); ?>
<?php			$province = customerProvince($order->id_customer); ?>
<?php			$customer  = $order->payment != 'ออนไลน์' ? $customer_name : ( $online != '' ? $customer_name.' ( '.$online.' )' : $customer_name );	?>
<?php			if( $order->valid != 2 ) : ?>
			<tr style='color:#FFF; background-color:<?php echo state_color($order->current_state); ?>; font-size:10px;'>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $id; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->reference; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $customer; ?></td>
                <td style="cursor:pointer;" onclick="viewOrder(<?php echo $id; ?>)"><?php echo $province; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo employee_name($order->id_employee); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo number_format(orderAmount($id)); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->payment; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->current_state_name; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_add); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_upd); ?></td>
			</tr>
<?php		else : ?>
			<tr style='color:#FFF; background-color:#434A54; font-size:10px;'>
				<td align='center'><?php echo $id; ?></td>
				<td><?php echo $order->reference; ?></td>
				<td><?php echo $customer; ?></td>
                <td><?php echo $province; ?></td>
				<td><?php echo employee_name($order->id_employee); ?></td>
				<td align='center'><?php echo number_format(orderAmount($id)); ?></td>
				<td align='center'><?php echo $order->payment; ?></td>
				<td align='center'><?php echo $order->current_state_name; ?></td>
				<td align='center'><?php echo thaiDate($order->date_add); ?></td>
				<td align='center'><?php echo thaiDate($order->date_upd); ?></td>
			</tr>
<?php			endif; ?>
<?php	endwhile; ?>
<?php else : ?>
			<tr><td colspan='9' align='center'><h4>ไม่พบรายการตามเงื่อนไขที่กำหนด</h4></td></tr>
<?php endif; ?>
		</table>
<?php 	echo $paginator->display_pages(); ?>
<br/><br/>
<script>  var x = setTimeout(function () { location.reload(); }, 60 * 5000); </script>
	</div><!--/ col-sm-12 -->
</div><!--/ row -->
<?php endif; ?>
</div><!--/ Container -->


<script id="orderProductTemplate" type="text/x-handlebars-template" >
	{{#each this }}
		{{#if @last}}
			<tr>
				 <td colspan="7" align="right"><h4>จำนวนรวม</h4></td>
				 <td  align="right"><h4>{{ total_qty }}</h4></td>
				 <td align="center"><h4>ชิ้น</h4></td>
			</tr>
		{{else}}
    	<tr style="font-size:12px;">
        	<td align="center" style="vertical-align:middle;">{{ no }}</td>
            <td align="center" style="vertical-align:middle;">{{{ img }}}</td>
            <td align="center" style="vertical-align:middle;">{{ barcode }}</td>
            <td style="vertical-align:middle;">{{ product }}</td>
            <td align="center" style="vertical-align:middle;">{{ price }}</td>
            <td align="center" style="vertical-align:middle;">{{ qty }}</td>
            <td align="center" style="vertical-align:middle;">{{ discount }}</td>
            <td align="center" style="vertical-align:middle;">{{ amount }}</td>
            <td align="right" style="vertical-align:middle;">
            	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow({{ id }}, '{{ product }}')"><i class="fa fa-trash"></i></button>
            </td>
      	</tr>
		{{/if}}
	{{/each}}
</script>
<script>
	function expandCategory(el)
	{
		var className = 'open';
		if (el.classList)
		{
    		el.classList.add(className)
		}else if (!hasClass(el, className)){
			el.className += " " + className
		}
	}

	function collapseCategory(el)
	{
		var className = 'open';
		if (el.classList)
		{
			el.classList.remove(className)
		}else if (hasClass(el, className)) {
			var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
			el.className=el.className.replace(reg, ' ')
  		}
	}
</script>

<script src="script/order.js"></script>
