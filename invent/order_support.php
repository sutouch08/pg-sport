<?php 
	$page_menu = "invent_order";
	$page_name = "เบิกอภินันทนาการ";
	$id_tab = 39;
	$id_profile = $_COOKIE['profile_id'];
	$pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	include "function/support_helper.php";
	include "function/order_helper.php";
	 if(isset( $_GET['id_order'] )) : 
			$id_order = $_GET['id_order'];
			$id_order_support = isset($_GET['id_order_support'])? $_GET['id_order_support'] : get_id_order_support($id_order);
			$rs = new order($id_order);
			$reference = $rs->reference;
			$state = $rs->current_state;
			$date = thaiDate($rs->date_add);
			$id_customer = $rs->id_customer;
			$id_employee = $rs->id_employee;
			$employee = employee_name($id_employee);
			$customer = customer_name($id_customer);
			$remark = $rs->comment;
			$qr = dbQuery("SELECT * FROM tbl_order_support WHERE id_order_support = ".$id_order_support);
			$ro = dbFetchArray($qr);
			$id_budget = $ro['id_budget'];
			$id_user = $_COOKIE['user_id'];
			     
      endif; 
	//**********************  ปุ่มด้านบน  *************************/
	if( isset($_GET['add']) ){
		
		$btn = can_do($add, "<a href='index.php?content=order_support' ><button type='button' id='btn_back' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>");	
		
	}else if( isset($_GET['edit']) ){
		
		$btn = can_do($edit, "<a href='index.php?content=order_support' ><button type='button' id='btn_back' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>");
		$btn .= can_do($add, "&nbsp;<button type='button' id='btn_save' class='btn btn-success' onclick='save()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>");
		
	}else if(isset($_GET['view_detail']) ){
		
		$btn = "<a href='index.php?content=order_support' ><button type='button' id='btn_back' class='btn btn-warning' ><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		
		if($state == 1 || $state == 2 || $state == 3){	
		$btn .= can_do($edit, "&nbsp;<button type='button' id='btn_edit' class='btn btn-success' onclick='go_edit()'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button>");	
		}
		
	}else{
		
		$btn = can_do($add, "<a href='controller/supportController.php?check_add'><button type='button' id='btn_add' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
		
	}
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row">
<div class="col-lg-8"><h3 class="title"><i class="fa fa-trophy"></i>&nbsp; <?php echo $page_name; ?></h3></div>
<div class="col-lg-4"><p class="pull-right"><?php echo $btn; ?></p></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<?php if( isset( $_GET['add'] ) ) : ?>
    
<!-----------------------------------------  ADD  ------------------------------------------->

<div class="row"><form action="controller/supportController.php?add_order" method="post">
	<div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">เลขที่เอกสาร</span>
            <span class="form-control"><center></center></span>
        </div>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">วันที่</span>
            <input type="text" class="form-control" name="doc_date" id="doc_date" value="<?php echo date("d-m-Y"); ?>" required="required"  />
        </div>
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้เบิก</span>
            <input type="text" class="form-control" name="employee" id="employee" value="" placeholder="ระบุชื่อผู้เบิก(พนักงาน)ที่ได้รับงบประมาณ" autocomplete="off" required="required"   />
        </div>
    </div>
    <div class="col-lg-12"><!--------- Divider -------->&nbsp;</div>
    <div class="col-lg-4">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้รับ</span>
            <input type="text" class="form-control" name="customer" id="customer" value="" placeholder="ระบุชื่อลูกค้าที่รับสินค้าอภินันท์" autocomplete="off"  required="required" />
        </div>
    </div>
    <div class="col-lg-5">
    	<div class="input-group">
        	<span class="input-group-addon">หมายเหตุ</span>
            <input type="text" class="form-control" name="remark" id="remark" value="" placeholder="ใส่หมายเหตุ (ถ้ามี)"  autocomplete="off"  />
        </div>
    </div>
    <div class="col-lg-2">
    	<button type="button" id="btn_add" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>    
    	<input type="hidden" name="id_customer" id="id_customer" />
        <input type="hidden" name="id_employee" id="id_employee" />
        <input type="hidden" name="balance" id="balance" />
        <button type="button" id="btn_submit" class="btn btn-default" style="display:none">Submit</button>
	</div> </form>   
</div>   
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' /> 
<!--------------------------------------- End Add ------------------------------------------>

<?php elseif( isset( $_GET['edit'] ) ) : ?>
<!-----------------------------------------  Edit  ------------------------------------------->

<div class="row">
<form id="header_form" method="post">
<input type="hidden" id="id_order" value="<?php echo $id_order; ?>" />
<input type="hidden" id="id_order_support" value="<?php echo $id_order_support; ?>"  />
<input type="hidden" id="save_link" value="controller/supportController.php?save_order&id_order=<?php echo $id_order; ?>"  />
	<div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">เลขที่เอกสาร</span>
            <span class="form-control"><center><?php echo $reference; ?></center></span>
        </div>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">วันที่</span>
            <input type="text" class="form-control" name="doc_date" id="doc_date" value="<?php echo $date; ?>" required="required" disabled />
        </div>
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้เบิก</span>
            <input type="text" class="form-control" name="employee" id="employee" value="<?php echo $employee; ?>" placeholder="ระบุชื่อผู้เบิก(พนักงาน)ที่ได้รับงบประมาณ" autocomplete="off" required="required"  disabled />
        </div>
    </div>
    <div class="col-lg-12"><!--------- Divider -------->&nbsp;</div>
    <div class="col-lg-4">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้รับ</span>
            <input type="text" class="form-control" name="customer" id="customer" value="<?php echo $customer; ?>" placeholder="ระบุชื่อลูกค้าที่รับสินค้าอภินันท์" autocomplete="off"  required="required" disabled />
        </div>
    </div>
    <div class="col-lg-5">
    	<div class="input-group">
        	<span class="input-group-addon">หมายเหตุ</span>
            <input type="text" class="form-control" name="remark" id="remark" value="<?php echo $remark; ?>" placeholder="ใส่หมายเหตุ (ถ้ามี)"  autocomplete="off" disabled />
        </div>
    </div>
    <div class="col-lg-2">
    <?php echo can_do($edit, "
    	<button type=\"button\" id=\"btn_edit_order\" class=\"btn btn-warning\"><i class=\"fa fa-pencil\"></i>&nbsp; แก้ไข</button>   
        <button type=\"button\" id=\"btn_update_order\" class=\"btn btn-success\" style=\"display:none;\"><i class=\"fa fa-save\"></i>&nbsp; อัพเดต</button> "); ?>
    	<input type="hidden" name="id_customer" id="id_customer" value="<?php echo $id_customer; ?>" />
        <input type="hidden" name="id_employee" id="id_employee" value="<?php echo $id_employee; ?>" />
        
	</div> </form>   
</div>   
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' /> 
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

	<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
	<form action='controller/supportController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4>
										<input type='hidden' name='id_order' value="<?php echo $id_order; ?>"/>
										<input type='hidden' name='id_order_support' value="<?php echo $id_order_support; ?>" />
										<input type="hidden" name="id_budget" id="id_budget" value="<?php echo $id_budget; ?>" />
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>
					
<!--------------------------------------- End Edit ------------------------------------------>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
<div class='col-lg-12'>
<table class='table' id='order_detail'>
	<thead><tr style='font-size: 12px;'>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='width:5%; text-align:center;'>รูป</th><th style='width:10%;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>มูลค่า</th><th style='text-align:center;'>การกระทำ</th>
	</tr></thead>
<?php    
	$order = new order($id_order);
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order  ORDER BY id_order_detail DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumproduct_qty = 0;
	if($row>0) :
	while($i<$row) :
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
		$product->product_attribute_detail($id_product_attribute);
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
?>		
		<tr style='font-size: 12px;'><td style='text-align:center; vertical-align:middle;'><?php echo $n; ?></td>
		<td style='text-align:center; vertical-align:middle;'><img src="<?php echo $product->get_product_attribute_image($id_product_attribute,1); ?>" width='35px' height='35px' /> </td>
		<td style='vertical-align:middle;'><?php echo $barcode; ?></td>
		<td style='vertical-align:middle;'><?php echo $product_reference." : ".$product_name; ?></td>
		<td style='text-align:center; vertical-align:middle;'><?php echo number_format($product_price,2); ?></td>
		<td style='text-align:center; vertical-align:middle;'><?php echo number_format($product_qty); ?></td>
		<td style='text-align:center; vertical-align:middle;'><?php echo $discount; ?></td>
		<td style='text-align:center; vertical-align:middle;'><?php echo number_format($total_amount,2); ?></td>
		<td style='text-align:center; vertical-align:middle;'>
		<?php echo can_do($delete, "<button type='button' class='btn btn-danger' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบรายการนี้','การกระทำนี้ไม่สามารถกู้คืนได้','controller/supportController.php?delete_item&id_order_detail=".$id_order_detail."&id_order=".$id_order."&id_order_support=".$id_order_support."&id_budget=".$id_budget."&amount=".$total_amount."', 'ใช่ ต้องการลบ','ยกเลิก'); \"><i class='fa fa-trash'></i>&nbsp; ลบ</button>"); ?>
		</td>
        </tr>
        <?php 	$sumproduct_qty += $product_qty; ?>
		<?php	$i++; ?>
		<?php 	$n++; ?>
<?php endwhile; ?>
	<tr>
	<td colspan='6'></td><td><h4>จำนวน</h4></td><td style='text-align:center; vertical-align:middle;'><h4><?php echo number_format($sumproduct_qty); ?></h4></td><td><h4>ชิ้น<h4></td>
	</tr>	
<?php else : ?>
	<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>
<?php endif; ?>

		
	</table>	</div></div>
<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) : ?>
<!-----------------------------------------  Detail  ------------------------------------------->
<?php
		$id_order = $_GET['id_order']; 
		$id_order_support = get_id_order_support($id_order);
		$order = new order($id_order);
?>		
<div class="row">
	<div class="col-lg-4" style="font-size:18px;"><strong><?php echo $order->reference; ?></strong></div>
    <div class="col-lg-4"><center><strong> ผู้รับ : </strong>&nbsp;<?php echo customer_name($order->id_customer); ?></center></div>
    <div class="col-lg-4"><p class="pull-right"><strong>ผู้เบิก : </strong><?php echo employee_name($order->id_employee); ?></p></div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<div class="row">               
    <div class="col-lg-12">
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo thaiDate($order->date_add,"/"); ?></dd>  |</dt></dl>
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_amount,2); ?>&nbsp;฿</dd> |</dt></dl>
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ผู้ทำรายการ : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo employee_name(get_id_user_support($id_order));?></dd> </dt></dl>
        <p class="pull-right">
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
        <button class="btn btn-success" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
        </p>
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class="row">
<div class="col-lg-6">
<form action="controller/supportController.php?state_change&id_order=<?php echo $id_order; ?>" method="post">
<input type="hidden" name="id_user" value="<?php echo $id_user; ?>" />
<table class="table" style='width:100%; padding:10px; border: 1px solid #ccc;'>
<tr>
	<td style="width:25%; text-align:right; vertical-align:middle">สถานะ : </td>
    <td style="width:40%">
    	<select name="id_state" id="state" class="form-control" placeholder="xxx">
        	<option value="0" selected="selected">----  เลือกสถานะ  ---</option>
            <option value="1">รอการยืนยัน</option>
            <option value="3">รอจัดสินค้า</option>
	<?php if( $delete ==1 ) : ?>            
            <option value="8">ยกเลิก</option>
	<?php endif; ?>            
        </select>
   </td>
   <td style="padding-left:15px;"><button class="btn btn-primary" id="btn_state_change"><i class="fa fa-plus"></i>&nbsp;เพิ่ม</button></td>
</tr> 
<?php $state = $order->orderState(); ?>
<?php $row = dbNumRows($state); ?>
<?php if($row>0) : ?>
<?php 	while($ra = dbFetchArray($state) ) :  ?>
			<tr style="background-color:<?php echo state_color($ra['id_order_state']); ?>">
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php if($ra['id_order_state'] != 1){ echo $ra['state_name']; }else{ echo "รอการยืนยัน"; } ?></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $ra['first_name']." ".$ra['last_name']; ?></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo date('d-m-Y H:i:s', strtotime($ra['date_add'])); ?></td>
			</tr>
<?php	endwhile; ?>
<?php else : ?>
		<tr>
        <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $order->currentState(); ?></td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($order->date_upd)); ?></td></tr>
<?php endif; 	?>  
</table>
</div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class="row">
<div class="col-lg-12">
	<table class="table" style="border:solid 1px #CCC;">
    	<thead>
        <th style="width:10%; text-align:center">รูปภาพ</th>
        <th style="width:45%">รายการ</th>
        <th style="width:10%; text-align:right">ราคา</th>
        <th style="width:10%; text-align:right">จำนวน</th>
        <th style="width:10%; text-align:center">ส่วนลด</th>
        <th style="width:15%; text-align:right">จำนวนเงิน</th>
        </thead>
<?php $rd = $order->getDetailOrder($id_order); ?>
<?php $total_order = 0;  $total_discount = 0; ?>
<?php while($rs = dbFetchArray($rd) ) : ?>
<?php 	$product = new product(); ?>
		<tr>
        	<td align="center" style="vertical-align:middle;"><img src="<?php echo $product->get_product_attribute_image($rs['id_product_attribute'],1); ?>" /></td>
            <td style="vertical-align:middle;"><?php echo $rs['product_reference']." : ".$rs['product_name']." : ".$rs['barcode']; ?></td>
            <td align="right" style="vertical-align:middle;"><?php echo number_format($rs['product_price'],2); ?></td>
            <td align="right" style="vertical-align:middle;"><?php echo number_format($rs['product_qty']); ?></td>
            <td align="center" style="vertical-align:middle;"><?php if($rs['discount_amount'] != 0){ if($rs['reduction_percent'] != 0){ echo $rs['reduction_percent']." %"; }else{ echo number_format($rs['reduction_amount'],2); } }else{ echo "-"; } ?></td>
            <td align="right" style="vertical-align:middle;"><?php echo number_format($rs['total_amount'],2); ?></td>
        </tr>
<?php	$total_discount += $rs['discount_amount'];  $total_order += $rs['total_amount']; ?>        
<?php endwhile; ?>
        <tr><td colspan="4" rowspan="3" align="right" style="border-right:solid 1px #ccc;"></td><td><strong>สินค้า</strong></td><td align="right"><strong><?php echo number_format($total_order,2); ?></strong></td></tr>
        <tr><td><strong>ส่วนลด</strong></td><td align="right"><strong><?php echo number_format($total_discount,2); ?></strong></td></tr>
        <tr><td><strong>สุทธิ</strong></td><td align="right"><strong><?php echo number_format(($total_order - $total_discount),2); ?></strong></td></tr>         
    </table>
</div>
<div class="col-lg-12">&nbsp;</div>
<div class="col-lg-12"><strong>หมายเหตุ : </strong><?php echo $order->comment; ?></div>
</div>


<!--------------------------------------- End Detail ------------------------------------------>

<?php else : ?>
<!-----------------------------------------  List  ------------------------------------------->

<!-- //*************************************************************** Filter ***************************************************************// -->
<?php
if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ setcookie("order_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }
if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("order_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }
$paginator = new paginator();
echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เงื่อนไข</span>
						<select class='form-control' name='filter' id='filter'>
							<option value='employee'"; if( isset($_POST['filter']) && $_POST['filter'] =="employee"){ echo "selected"; }else if( isset($_COOKIE['support_filter']) && $_COOKIE['support_filter'] == "employee"){ echo "selected"; } echo ">ผู้เบิก</option>
							<option value='customer'"; if( isset($_POST['filter']) && $_POST['filter'] =="customer"){ echo "selected"; }else if( isset($_COOKIE['support_filter']) && $_COOKIE['support_filter'] == "customer"){ echo "selected"; } echo ">ผู้รับ</option>
							<option value='reference'"; if( isset($_POST['filter']) && $_POST['filter'] =="reference"){ echo "selected"; }else if( isset($_COOKIE['support_filter']) && $_COOKIE['support_filter'] == "reference"){ echo "selected"; } echo ">เอกสาร</option>	
							<option value='not_save'"; if( isset($_POST['filter']) && $_POST['filter'] =="not_save"){ echo "selected"; }else if( isset($_COOKIE['support_filter']) && $_COOKIE['support_filter'] == "not_save"){ echo "selected"; } echo ">ยังไม่บันทึก</option>
						</select>
				</div>		
			</div>	
			<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
				<div class='input-group'>
					<span class='input-group-addon'>ค้นหา</span>
						<input class='form-control' type='text' name='search-text' id='search-text' placeholder='ระบุคำค้น' value='";
						if(isset($_POST['search-text']) && $_POST['search-text'] !=""){ echo $_POST['search-text']; }else if(isset($_COOKIE['support_search-text'])){ echo $_COOKIE['support_search-text']; }
						echo "' />
					<span class='input-group-btn'><button class='btn btn-default' id='search-btn' type='button'><span id='load'><span class='glyphicon glyphicon-search'></span></span></button>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date' placeholder='เลือกวัน'  value='";
					if(isset($_POST['from_date']) && $_POST['from_date'] != ""){ 
						echo date("d-m-Y", strtotime($_POST['from_date'])); 
						}else if( isset($_COOKIE['support_from_date'])){ 
						echo date("d-m-Y", strtotime($_COOKIE['support_from_date'])); 
						}
					 echo "'/>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' placeholder='เลือกวัน' value='";
				  if( isset($_POST['to_date']) && $_POST['to_date'] != "" ){ 
				 	echo date("d-m-Y", strtotime($_POST['to_date'])); 
				 }else if( isset($_COOKIE['support_to_date']) ){
					 echo date("d-m-Y", strtotime($_COOKIE['support_to_date']));
				 }
				  echo"' />
				</div>
			</div>
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='window.location.href=\"controller/supportController.php?clear_filter\"'><i class='fa fa-refresh'></i> เคลียร์ฟิลเตอร์</button>
			</div>
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
				$view = "";
		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['support_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['support_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['support_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['support_to_date'])); }else{ $to = "";}
		if($from=="" || $to ==""){ $view = getConfig("VIEW_ORDER_IN_DAYS"); 	}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search-text']) && $_POST['search-text'] !="" )
		{
			$text = $_POST['search-text'];
			$filter = $_POST['filter'];
			setcookie("support_search-text", $text, time() + 3600, "/");
			setcookie("support_filter",$filter, time() +3600,"/");
			switch( $_POST['filter']){
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0){
				while($i<$rs){
					list($in) = dbFetchArray($qs);
					$in_cause .="$in";
					$i++;
					if($i<$rs){ $in_cause .=","; 	}
				}
				$where = "WHERE id_customer IN($in_cause) AND role = 7 AND order_status = 1 ORDER BY id_order DESC" ; 
				}else{
					$where = "WHERE id_order != NULL AND role = 7";
				}
				break;
				case "employee" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					$in ="";
					if($rs>0){
					while($i<$rs){
						list($id_employee) = dbFetchArray($qs);
						$in .="$id_employee";
						$i++;
						if($i<$rs){ $in .=","; }
					}
					
						$where = "WHERE id_employee IN($in) AND role = 7 AND order_status = 1 ORDER BY id_order DESC";
					}else{
						$where = "WHERE id_order != NULL AND role = 7";
					}
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' AND role = 7 AND order_status = 1 ORDER BY reference";
				break;
				case "not_save" :
				$where = "WHERE role = 7 AND order_status = 0 ORDER BY id_order DESC";
				break;
			}
		}else if(isset($_COOKIE['support_search-text']) && isset($_COOKIE['support_filter'])){
			$text = $_COOKIE['support_search-text'];
			$filter = $_COOKIE['support_filter'];
			switch( $filter){
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0){
				while($i<$rs){
					list($in) = dbFetchArray($qs);
					$in_cause .="$in";
					$i++;
					if($i<$rs){ $in_cause .=","; 	}
				}
				$where = "WHERE id_customer IN($in_cause) AND role = 7 AND order_status = 1 ORDER BY id_order DESC" ; 
				}else{
					$where = "WHERE id_order != NULL AND role = 7";
				}
				break;
				case "employee" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					$in ="";
					if($rs>0){
					while($i<$rs){
						list($id_employee) = dbFetchArray($qs);
						$in .="$id_employee";
						$i++;
						if($i<$rs){ $in .=","; }
					}
					
						$where = "WHERE id_employee IN($in) AND role = 7 AND order_status = 1 ORDER BY id_order DESC";
					}else{
						$where = "WHERE id_order != NULL AND role = 7";
					}
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' AND role = 7 AND order_status = 1 ORDER BY reference";
				break;
				case "not_save" :
				$where = "WHERE role = 7 AND order_status = 0 ORDER BY id_order DESC";
				break;
			}
		}else{
			$where = "WHERE (date_add BETWEEN '$from' AND '$to') AND role = 7 AND order_status = 1 ORDER BY id_order DESC";
		}
echo "<div class='row' id='result'>
			
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>";
	$paginator->Per_Page("tbl_order",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=order_support");
	?>
<?php $sql = dbQuery("SELECT id_order,reference,id_customer,id_employee,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page); ?>
<!-- //*************************************************************** จบ Filter ***************************************************************// -->
<table class="table">
<thead style="font-size:12px; background-color:#37BC9B; color:white;">
<th style="width:10%;">เลขที่อ้างอิง</th>
<th style="width:20%;">ผู้เบิก</th>
<th style="width:20%;">ผู้รับ</th>
<th style="width:15%;">ผู้ทำรายการ</th>
<th style="width:8%;">ยอดเงิน</th>
<th style="width:10%;">สถานะ</th>
<th style="width:8%;">วันที่เพิ่ม</th>
<th style="width:8%;">วันที่ปรับปรุง</th>
</thead>
	<?php $row = dbNumRows($sql); ?>
	<?php if($row > 0) : ?>
    	<?php while($rs = dbFetchArray($sql) ) : ?>
        <?php $link = "style='cursor:pointer;' onclick=\"document.location='index.php?content=order_support&id_order=".$rs['id_order']."&id_order_support=".get_id_order_support($rs['id_order'])."&view_detail=y' \" "; ?>
        <tr style="color:#FFF; font-size:12px; background-color:<?php echo state_color($rs['current_state']); ?>;">  	
            <td <?php echo $link; ?> ><?php echo $rs['reference']; ?></td>
            <td <?php echo $link; ?> ><?php echo employee_name($rs['id_employee']); ?></td>
            <td <?php echo $link; ?> ><?php echo customer_name($rs['id_customer']); ?></td>
             <td <?php echo $link; ?> ><?php echo employee_name(get_id_user_support($rs['id_order'])); ?></td>
            <td <?php echo $link; ?> ><?php echo number_format(order_amount($rs['id_order']),2); ?></td>
            <td <?php echo $link; ?> ><?php echo current_order_state($rs['id_order']); ?></td>          
            <td <?php echo $link; ?> ><?php echo thaiDate($rs['date_add']); ?></td>
            <td <?php echo $link; ?> ><?php echo thaiDate($rs['date_upd']); ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else : ?>
    <tr><td colspan="7"><center><h4>---------------  ยังไม่มีออเดอร์  ---------------</h4></center></td></tr>
    <?php endif; ?>
</table>
<?php echo $paginator->display_pages(); ?>
<!--------------------------------------- End List ------------------------------------------>

<?php endif; ?>

</div><!-- container -->
<?php if(isset($_GET['add']) || isset($_GET['edit'])) : ?>
<?php $sql = dbQuery("SELECT tbl_employee.id_employee, first_name, last_name, tbl_support.id_support, id_support_budget, balance, start, end FROM tbl_employee JOIN tbl_support ON tbl_employee.id_employee = tbl_support.id_employee JOIN tbl_support_budget ON tbl_support.id_support = tbl_support_budget.id_support AND tbl_support.year = tbl_support_budget.year WHERE tbl_support.active = 1 AND tbl_support_budget.active = 1"); ?>
<?php if(dbNumRows($sql) > 0 ) : ?>
        	<script>
			var EmployeeList = [
        	<?php while($rs = dbFetchArray($sql)) : ?>
				<?php $today = date("Y-m-d H:i:s"); ?>
				<?php if( $rs['start'] <= $today && $rs['end'] >= $today ) : ?>
            	<?php echo "\"".$rs['id_employee']." : ".$rs['first_name']." ".$rs['last_name']." : "."คงเหลือ"." : ".$rs['balance']."\","; ?>
				<?php endif; ?>
            <?php endwhile; ?>
			];
			</script>
        <?php endif; ?>
<?php $sqr = dbQuery("SELECT id_customer, first_name, last_name FROM tbl_customer"); ?>
<?php if(dbNumRows($sqr) > 0 ) : ?>
        	<script>
			var CustomerList = [
        	<?php while($rs = dbFetchArray($sqr)) : ?>
            	<?php echo "\"".$rs['id_customer']." : ".$rs['first_name']." ".$rs['last_name']."\","; ?>
            <?php endwhile; ?>
			];
			</script>
        <?php endif; ?>
<script>        
$("#employee").autocomplete({
	source: EmployeeList,
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var id = arr[0];
		var name = arr[1];
		var balance = arr[3]
		$("#id_employee").val(id);
		$(this).val(name);	
		$("#balance").val(balance);
	}
}); 

$("#customer").autocomplete({
	source : CustomerList,
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var id = arr[0];
		var name = arr[1];
		$("#id_customer").val(id);
		$(this).val(name);	
	}
});
</script>
<?php endif; ?>        
<script>
$(function() {
    $("#doc_date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
 });

$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  
  function validate() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(!isDate(from_date) || !isDate(to_date)){	
		swal("วันที่ไม่ถูกต้อง");
	}else{
		$("#form").submit();
	}
}	

$("#btn_add").click(function(){
	var id_customer = $("#id_customer").val();
	var customer = $("#customer").val();
	var id_employee = $("#id_employee").val();
	var employee = $("#employee").val();
	var date = $("#doc_date").val();
	var balance = $("#balance").val();
	if(!isDate(date)){
		swal("วันที่ไม่ถูกต้อง");
	}else if(id_employee == "" || employee == "")	{
		swal("โปรดระบุผู้เบิก");
		$("#employee").focus();
		
	}else if(id_customer =="" || customer ==""){
		swal("โปรดระบุผู้รับ");
		$("#customer").focus();
	}else if(balance <1){
		swal("งบประมาณคงเหลือหมดโปรดติดต่อผู้ดูแลระบบ");
	}else{
		$("#btn_submit").attr("type","submit");
		$("#btn_submit").click();
	}
});

$("#btn_edit_order").click(function(e) {
    //$("#employee").removeAttr("disabled");
	$("#customer").removeAttr("disabled");
	$("#doc_date").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$(this).css("display","none");
	$("#btn_update_order").css("display","");
});

$("#btn_update_order").click(function(e) {
   var id_customer = $("#id_customer").val();
	var customer = $("#customer").val();
	var id_employee = $("#id_employee").val();
	var employee = $("#employee").val();
	var date = $("#doc_date").val();
	var id_order = $("#id_order").val();
	var id_order_support = $("#id_order_support").val();
	var id_budget = $("#id_budget").val();
	if(!isDate(date)){
		swal("วันที่ไม่ถูกต้อง");
	}else if(id_employee == "" || employee == "")	{
		swal("โปรดระบุผู้เบิก");
		$("#employee").focus();
		
	}else if(id_customer =="" || customer ==""){
		swal("โปรดระบุผู้รับ");
		$("#customer").focus();
		
	}else{
		$.ajax({
			url: "controller/supportController.php?edit_order=y&id_order="+id_order+"&id_order_support="+id_order_support+"&id_budget="+id_budget,
			type:"POST", cache:false, data: $("#header_form").serialize(),
			success: function(res){
				var arr = res.split(" : ");
				var rs = arr[0];
				var new_id_budget = arr[1];
				if(rs =="success"){
					$("#doc_date").attr("disabled", "disabled");
					$("#employee").attr("disabled", "disabled");
					$("#customer").attr("disabled", "disabled");
					$("#remark").attr("disabled", "disabled");
					$("#btn_update_order").css("display","none");
					$("#btn_edit_order").css("display","");
					$("#id_budget").val(new_id_budget);
				}else if(rs== "over_budget" ){
					swal("ไม่สำเร็จ","ไม่สามารถแก้ไขรายการได้เนื่องจากรายการที่เบิกมีมูลค่าเกินงบประมาณของผู้รับ","error");
				}else{
					swal("ไม่สามารถแก้ไขรายการได้");
				}
			}
		});
	}
});

function getData(id_product){
	var id_cus = $("#id_customer").val();
	$.ajax({
		url:"controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}

function save()
{
	var target = $("#save_link").val()
	document.location=target;
}
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-text").bind("enterKey",function(){
	$("#search-btn").click();
});

$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	if(query_text !=""){
		$("#form").submit();
	}else{
		swal("กรุณาระบุคำค้นหา","หากคุณกำลังค้นหาโดยใช้เงื่อนไข 'ยังไม่บันทึก' ให้ระบุคำค้นใดๆก็ได้","warning");
	}
});

function go_edit(){
	var id_order = <?php if(isset($id_order)){ echo $id_order;}else{ echo "''"; } ?>;
	var id_order_support = <?php if(isset($id_order_support)){ echo $id_order_support; }else{ echo "''"; } ?>;
	var target = "index.php?content=order_support&edit=y&id_order="+id_order+"&id_order_support="+id_order_support;
	document.location = target;
}
function check_order(id)
{
	var wid = $(document).width();
	var left = (wid - 1100) /2;
	window.open("index.php?content=order_check&id_order="+id+"&view_detail=y&nomenu", "_blank", "width=1100, height=800, left="+left+", location=no, scrollbars=yes");	
}

function print_order(id)
{
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/orderController.php?print_order&id_order="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");	
}

//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getCategory(id) {
    var output = $("#cat-" + id);
    if (output.html() == '') {
        load_in();
        $.ajax({
            url: "controller/orderController.php?getCategoryProductGrid",
            type: "POST",
            cache: "false",
            data: { "id_category": id },
            success: function(rs) {
                load_out();
                var rs = $.trim(rs);
                if (rs != 'no_product') {
                    output.html(rs);
                } else {
                    output.html('<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>');
                    $('.tab-pane').removeClass('active');
                    output.addClass('active');
                }
            }
        });
    }
}

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