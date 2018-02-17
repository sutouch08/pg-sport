<script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.maskMoney.js"></script>
<script src="<?php echo WEB_ROOT; ?>library/js/bootstrap-file-input.js"></script>

<?php
	$page_menu = "invent_product";
	$page_name = "รายการสินค้า";
	$id_tab = 1;
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
	$option = "<option value='0'>-- ไม่มี --</potion><option value='1'>++ เพิ่มขึ้น ++</option><option value='-1'>ลดลง</option>";
	?>
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])){
	echo"<form name='product_form' id='product_form' action='controller/productController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form name='product_form' id='product_form' action='controller/productController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-tasks"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=product' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li $can_add><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='validate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
       		<li $can_add ><a href='index.php?content=product&add=y&tab=1' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:large;'></span><br />เพิ่ม$page_name</a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<?php 
 if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['add'])){ /////****เพิ่มสินค้า*****/////
////*****แสดงหน้าเพิ่มสินค้า******/////
//********************************************************** TAB1 ******************************************//
	if($_GET['tab']==1){ 
	$tab1="display:block;";
	$tab2="display:none;";
	$tab3="display:none;";
	$class1="active";
	$class2="";
	$class3="";
	}else if($_GET['tab']==2){
		$tab1="display:none;";
		$tab2="display:block;";
		$tab3="display:none;";
		$class1="";
		$class2="active";
		$class3="";
	}else if($_GET['tab']==3){
		$tab1="display:none;";
		$tab2="display:none;"; 
		$tab3="display:block;";
		$class1="";
		$class2="";
		$class3="active";
	}
	echo"
	<ul class='nav nav-tabs nav-justified'>
		<li class='$class1' id='tab1'><a href='#'>ข้อมูลสินค้า</a></li>
	</ul>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<div class='col-sm-12' id='info' style='$tab1'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;<input type='hidden' name='valid' id='valid' /></td></tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ชื่อสินค้า :&nbsp;</td>
		<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='product_name' /></td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px;'>รหัสสินค้า :&nbsp;</td>
		<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' id='product_code' name='product_code' required='required'/></td><td id='validate'>&nbsp;</td>
	</tr>
	<tr>
		<td width='10%' align='right' valign='top' style='padding-bottom:10px;'>หมวดหมู่ :&nbsp;</td>
		<td width='30%' align='left' style='padding-bottom:10px;'>"; 	category_tree(); 	echo"	</td><td></td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>หมวดหมู่หลัก :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><select name='default_category' class='form-control input-sm'>".categoryList()."</select></td><td>&nbsp;</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ราคาทุน :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='cost' class='form-control input-sm' id='cost' style='text-align: right;' value='0.00' /></td><td>&nbsp;</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ราคาขาย :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='price'  class='form-control input-sm' id='price' style='text-align: right;' value='0.00'/></td><td>&nbsp;</td>
		</tr>
		<tr>
		<td width='10%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ส่วนลด :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='discount'  class='form-control input-sm' id='discount' style='text-align: right;' value='0.00' /><span class='help-block'>กำหนดส่วนลด</span></td>
			<td style='padding-bottom:10px; padding-left:10px;'><select name='discount_type' class='form-control input-sm' style='width:25%;' >
						<option value='percentage' selected='selected'>เปอร์เซ็นต์</option>
						<option value='amount'>จำนวนเงิน</option>
					</select><span class='help-block'>เลือกให้ส่วนลดเป็นเปอร์เซ็นต์หรือเป็นจำนวนเงิน</span>
			</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>น้ำหนัก :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='weight'  class='form-control input-sm' id='weight' style='text-align: right;' value='0.00'/></td><td style='padding-bottom:10px;'>&nbsp;กิโลกรัม</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>กว้าง :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='width'  class='form-control input-sm' id='width' style='text-align: right;' value='0.00'/></td><td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ยาว :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='length'  class='form-control input-sm' id='length' style='text-align: right;' value='0.00' style='text-align: right;' /></td><td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>สูง :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='height'  class='form-control input-sm' id='height' style='text-align: right;' value='0.00' /></td><td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1' checked='checked' style='margin-left:15px;' /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0' style='margin-left:15px;' /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td><td>&nbsp;</td>
		</tr>
		<tr>
		<td width='10%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>คำอธิบาย :&nbsp;</td>
			<td colspan='3' align='left' style='padding-bottom:10px;'><textarea name='description' class='form-control input-sm' style='width: 60%;' rows='8'></textarea></td>	
		</tr>
		
	</table></form><h2>&nbsp;</h2>
	</div>";
}else if(isset($_GET['edit'])&&isset($_GET['id_product'])){
	$product = new product();
	$id_pro_detail = $_GET['id_product'];
	$product->product_detail($id_pro_detail);
	if($_GET['tab']==1){ 
	$tab1="display:block;";
	$tab2="display:none;";
	$tab3="display:none;";
	$class1="active";
	$class2="";
	$class3="";
	}else if($_GET['tab']==2){
		$tab1="display:none;";
		$tab2="display:block;";
		$tab3="display:none;";
		$class1="";
		$class2="active";
		$class3="";
	}else if($_GET['tab']==3){
		$tab1="display:none;";
		$tab2="display:none;"; 
		$tab3="display:block;";
		$class1="";
		$class2="";
		$class3="active";
	}
		echo"
	<ul class='nav nav-tabs nav-justified'>
		<li class='$class1' id='tab1'><a href='#'>ข้อมูลสินค้า</a></li>
		<li class='$class2' id='tab2'><a href='#'>รายการสินค้า</a></li>
		<li class='$class3' id='tab3'><a href='#'>รูปภาพ</a></li>
	</ul>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<div class='col-sm-12' id='info' style='$tab1'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;<input type='hidden' name='valid' id='valid' /><input type='hidden' name='id_product' value='$id_pro_detail' /><input type='hidden' name='edit_product' value='y'/></td></tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ชื่อสินค้า :&nbsp;</td>
		<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='product_name'  value='".$product->product_name."' /></td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px;'>รหัสสินค้า :&nbsp;</td>
		<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' id='product_code' name='product_code' value='".$product->product_code."' required='required'/></td>
		<td id='validate'>&nbsp;</td>
	</tr>
	<tr>
		<td width='10%' align='right' valign='top' style='padding-bottom:10px;'>หมวดหมู่ :&nbsp;</td>
		<td colspan='2' align='left' style='padding-bottom:10px;'>";	category_tree($id_pro_detail); 	echo"	</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>หมวดหมู่หลัก :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><select name='default_category' class='form-control input-sm'>".categoryList($product->default_category_id)."</select></td><td>&nbsp;</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ราคาทุน :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='cost' class='form-control input-sm' id='cost' style='text-align: right;' value='".$product->product_cost."' /></td><td>&nbsp;</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ราคาขาย :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='price'  class='form-control input-sm' id='price' style='text-align: right;' value='".$product->product_price."' /></td><td>&nbsp;</td>
		</tr>
		<td width='10%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ส่วนลด :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='discount'  class='form-control input-sm' style='text-align: right;' id='discount' value='".$product->product_discount1."' /><span class='help-block'>กำหนดส่วนลด</span></td>
			<td style='padding-bottom:10px; padding-left:10px;'><select name='discount_type' class='form-control input-sm' style='width:25%;' >
						<option value='percentage'"; if($product->discount_type=="percentage"){ echo" selected='selected' ";} echo ">เปอร์เซ็นต์</option>
						<option value='amount'"; if($product->discount_type=="amount"){ echo" selected='selected' ";} echo ">จำนวนเงิน</option>
					</select><span class='help-block'>เลือกให้ส่วนลดเป็นเปอร์เซ็นต์หรือเป็นจำนวนเงิน</span>
			</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>น้ำหนัก :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='weight'  class='form-control input-sm' id='weight' style='text-align: right;' value='".$product->weight."'/></td>
			<td style='padding-bottom:10px;'>&nbsp;กิโลกรัม</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>กว้าง :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='width'  class='form-control input-sm' id='width' style='text-align: right;' value='".$product->width."'/></td>
			<td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>ยาว :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='length'  class='form-control input-sm' id='length' style='text-align: right;' value='".$product->length."' style='text-align: right;' /></td>
			<td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>สูง :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><input type='text' name='height'  class='form-control input-sm' id='height' style='text-align: right;' value='".$product->height."' /></td>
			<td style='padding-bottom:10px;'>&nbsp;เซ็นติเมตร</td>
		</tr>
		<tr>
			<td width='10%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
			<td width='30%' align='left' style='padding-bottom:10px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1' "; if($product->active==1){echo"checked='checked' ";} echo" style='margin-left:15px;' /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0' "; if($product->active==0){echo"checked='checked' ";} echo" style='margin-left:15px;' /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div><td><td>&nbsp;</td>
		</tr>
		<tr>
		<td width='10%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>คำอธิบาย :&nbsp;</td>
			<td colspan='3' align='left' style='padding-bottom:10px;'><textarea name='description' class='form-control input-sm' style='width: 60%' rows='8'>".$product->product_detail."</textarea></td>
			
		</tr>
		
	</table></form><h2>&nbsp;</h2>
	</div>";
	//******************************  END TAB 1  *************************************************//
	//********************************** TAB 2 ************************************************//
	if(isset($_GET['id_product_attribute'])){
		$id_product_attribute = $_GET['id_product_attribute'];
		$product->product_attribute_detail($id_product_attribute);
		$pack = $product->get_pack($id_product_attribute); //ดึงข้อมูลแพ็คสินค้า ได้ค่ากลับมาเป็นอาเรย์ qty and barcode 
		$qty = $pack['qty']; // จำนวนสินค้า ต่อ แพ็ค
		$barcode_pack = $pack['barcode'];
		echo"
		<div class='row' style='margin-top:10px; margin-bottom:10px;'>
		<div class='col-lg-9' style='vertical-align: middle;'><h4>เพิ่ม หรือ แก้ไข  คุณลักษณะต่างๆของสินค้านี้</h4> </div>
		<div class='col-lg-3'></div>
		</div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
	<div class='col-sm-12' id='combination' style=''>
	<form action='controller/productController.php?edit=y' name='product_attribute_form' id='product_attribute_form' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='6'>&nbsp;<input type='hidden' name='valid_ref' id='valid_ref' /><input type='hidden' name='id_product_attribute' value='$id_product_attribute' />
			<input type='hidden' name='id_product' value='".$product->id_product."' /></td></tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รหัสอ้างอิง :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='reference' id='reference' value='".$product->reference."'/></td>
		<td width='10%' id='error_ref' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ทุน :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='cost' id='cost' value='".$product->product_cost."' /></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>สี :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_color' class='form-control input-sm'>"; colorList($product->id_color); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ราคาขาย :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='price' id='price' value='".$product->product_price."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ไซด์ :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_size' class='form-control input-sm'>"; sizeList($product->id_size); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>น้ำหนัก :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='weight' id='weight' value='".$product->weight."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> กิโลกรัม</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>คุณลักษณะ :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_attribute' class='form-control input-sm'>"; attributeList($product->id_attribute); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความกว้าง :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='width' id='width' value='".$product->width."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รหัสบาร์โค้ด :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='barcode' id='barcode'  value='".$product->barcode."'/><input type='hidden' name='valid_code' id='valid_code' /></td>
		<td width='10%' id='error_code' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความยาว :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='length' id='length' value='".$product->length."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>บาร์โค้ดแพ็ค :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='barcode_pack' id='barcode_pack' value='".$barcode_pack."' />
				<input type='hidden' name='valid_code_pack' id='valid_code_pack' /></td>
		<td width='10%' id='error_code_pack' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความสูง :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='height' id='height' value='".$product->height."' /></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>จำนวนในแพ็ค :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='qty' id='qty' value='".$qty."' /></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'></td>
		<td width='10%' align='left' style='padding-bottom:10px;'></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr><td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รูปภาพ :&nbsp;</td><td colspan='5'>"; 
	list($checked) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = ".$id_product_attribute));
	$qm = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$product->id_product." ORDER BY position ASC");
	$qrow = dbNumRows($qm);
	if($qrow>0){
	$m = 0 ;
	while($m<$qrow){
		list($id_image) = dbFetchArray($qm);
		if($id_image==$checked){ $is_checked = "checked = 'checked'";}else{ $is_checked = "";}
		$image = get_image_path($id_image,1);
		echo"<input type='radio' name='id_image' id='image$id_image' value='$id_image' $is_checked /><label for='image$id_image' style='padding-left:10px; padding-right:10px;'><img src='$image' /></label>";
		$m++;
	}
	}else{
		//echo "<input type='radio' name='id_image' id='image' value=''  />";
		echo "***** ยังไม่มีรูปภาพ ******";
	}
	echo"</td></tr>
	<tr>
		<td></td><td><button type='button' class='btn btn-default btn-block' onclick='edit_attribute()' $can_edit>บันทึก</button></td><td colspan='3'></td>
	</tr>
	</table></form>";
	}else{
		$id_product = $_GET['id_product'];
		echo "
	<div class='col-sm-12' id='combination' style='$tab2'>
	<div class='row' style='margin-top:10px; margin-bottom:10px;'>
		<div class='col-lg-9' style='text-align: right;'><h4 style='margin-bottom:5px; margin-top:5px;'>เพิ่ม หรือ แก้ไข  คุณลักษณะต่างๆของสินค้านี้ หรือ </h4></div>
		<div class='col-lg-3'><a href='index.php?content=attribute_gen&id_product=$id_product&step=1'><button type='button' class='btn btn-primary btn-block'>สร้างรายการสินค้าอัตโนมัติ</button></a></div>
	</div>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
	<p class='pull-right'></p>
	<form action='controller/productController.php?add=y' name='product_attribute_form' id='product_attribute_form' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='6'>&nbsp;<input type='hidden' name='valid_ref' id='valid_ref' /><input type='hidden' name='id_product' value='$id_pro_detail' /></td></tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รหัสอ้างอิง :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='reference' id='reference' /></td>
		<td width='10%' id='error_ref' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ทุน :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='cost' id='cost' value='".$product->product_cost."' /></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>สี :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_color' class='form-control input-sm'>"; colorList(); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ราคาขาย :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='price' id='price' value='".$product->product_price."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ไซด์ :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_size' class='form-control input-sm'>"; sizeList(); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>น้ำหนัก :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='weight' id='weight' value='".$product->weight."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> กิโลกรัม</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>คุณลักษณะ :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><select name='id_attribute' class='form-control input-sm'>"; attributeList(); echo"</select></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความกว้าง :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='width' id='width' value='".$product->width."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รหัสบาร์โค้ด :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='barcode' id='barcode' /><input type='hidden' name='valid_code' id='valid_code' /></td>
		<td width='10%' id='error_code' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความยาว :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='length' id='length' value='".$product->length."'/></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รหัสบาร์โค้ดแพ็ค :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='barcode_pack' id='barcode_pack' /><input type='hidden' name='valid_code_pack' id='valid_code_pack' /></td>
		<td width='10%' id='error_code_pack' style='padding-bottom:10px;color: red;'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>ความสูง :&nbsp;</td>
		<td width='10%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='height' id='height' value='".$product->height."' /></td>
		<td style='padding-bottom:10px; padding-left:10px;'> เซ็นติเมตร</td>
	</tr>
	<tr>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>จำนวนในแพ็ค :&nbsp;</td>
		<td width='20%' align='left' style='padding-bottom:10px;'><input type='text' class='form-control input-sm' name='qty' id='qty' /></td>
		<td width='10%'></td>
		<td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'></td>
		<td width='10%' align='left' style='padding-bottom:10px;'></td>
		<td style='padding-bottom:10px; padding-left:10px;'></td>
	</tr>
	<tr><td width='10%' align='right' style='padding-bottom:10px; border-left: thick;'>รูปภาพ :&nbsp;</td><td colspan='5' style='padding-bottom:10px; border-left: thick;'>"; 
	$qm = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$product->id_product." ORDER BY position ASC");
	$qrow = dbNumRows($qm);
	if($qrow>0){
	$m = 0 ;
	while($m<$qrow){
		list($id_image) = dbFetchArray($qm);
		$image = get_image_path($id_image,1);
		echo"<input type='radio' name='id_image' id='image$id_image' value='$id_image'  /><label for='image$id_image' style='padding-left:10px; padding-right:10px;'><img src='$image' /></label>";
		$m++;
	}
	}else{
		//echo "<input type='radio' name='id_image' id='image' value=''  />";
		echo "***** ยังไม่มีรูปภาพ ******";
	}
	echo"</td></tr>
	<tr>
		<td></td><td><button type='button' class='btn btn-default btn-block' onclick='edit_attribute()' $can_add>เพิ่ม</button></td><td colspan='3'></td>
	</tr>
	</table></form>";}
	echo"
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
	<button type='button' id='edit_barcode' class='btn btn-default'>แก้ไขบาร์โค้ด</button>&nbsp;&nbsp;&nbsp; <button type='button' id='edit_barcode_pack' class='btn btn-default'>แก้ไขบาร์โค้ดแพ็ค</button>&nbsp;&nbsp;&nbsp; <button type='button' id='edit_qty_pack' class='btn btn-default'>แก้ไขจำนวนในแพ็ค</button>
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
	<table class='table table-striped table-hover'>
	<thead>
	<tr style='font-size:12px;'>
		<th width='5%'>รูป</th><th width='15%'>รหัสอ้างอิง</th><th width='15%'>บาร์โค้ด</th><th width='15%'>บาร์โค้ดแพ็ค</th><th width='5%'>จำนวน/แพ็ค</th><th width='10%' style='text-align:center;'>สี</th><th width='5%' style='text-align:center;'>ไซด์</th><th width='5%' style='text-align:center;'>คุณลักษณะ</th><th style='width:10%; text-align: right;'>ราคาทุน</th><th style='width:10%; text-align: right;'>ราคาขาย</th><th colspan='2' style='text-align:center;'>การกระทำ</th></tr>
	</thead>";
	$id_product = $_GET['id_product'];
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product AND active !=0");
	$rows = dbNumRows($qs);
	if($rows>0){
	$is=0;
	$in=0;
	while($is<$rows){
		list($id_product_attribute) = dbFetchArray($qs);
		$product->product_attribute_detail($id_product_attribute);
		$pack = $product->get_pack($id_product_attribute); //ดึงข้อมูลแพ็คสินค้า ได้ค่ากลับมาเป็นอาเรย์ qty and barcode 
		$qty = $pack['qty']; // จำนวนสินค้า ต่อ แพ็ค
		$barcode_pack = $pack['barcode'];
		$barcode_input = "<input type='text' class='form-control input-sm' id='input".$is."' name='input_barcode' value='".$product->barcode."' style='display:none;' />"; 
		$barcode_input_pack = "<input type='text' class='form-control input-sm' id='input_pack".$is."' name='input_barcode_pack' value='".$barcode_pack."' style='display:none;' />";
		$qty_input_pack = "<input type='text' class='form-control input-sm' id='input_qty".$is."' name='input_qty_pack' value='".$qty."' style='display:none;' />"; 
		$image_path = get_product_attribute_image($id_product_attribute,1);
		$no_image = WEB_ROOT."img/product/no_image_mini.jpg";
		echo"<tr style='font-size:12px;'>
		<td style='vertical-align: middle;'>"; if(file_exists($image_path)){ echo "<img src='".$image_path."' width='40px' height='40px' />";}else{ echo "<img src='".$no_image."'/>";} echo "</td>
		<td style='vertical-align: middle;'>".$product->reference."</td>
		<td style='vertical-align: middle;'><p class='barcode' id='p_".$id_product_attribute."' >".$product->barcode."</p>".$barcode_input."</td>
		<td style='vertical-align: middle;'><p class='barcode_pack' id='p_pack".$id_product_attribute."' >".$barcode_pack."</p>".$barcode_input_pack."</td>
		<td align='center' style='vertical-align: middle;'><p class='qty_pack' id='p_qty".$id_product_attribute."' >".$qty."</p>".$qty_input_pack."</td>
		<td align='center' style='vertical-align: middle;'>".$product->color_code." : ".$product->color_name."</td>
		<td align='center' style='vertical-align: middle;'>".$product->size_name."</td>
		<td align='center' style='vertical-align: middle;'>".$product->attribute_name."</td>
		<td align='right' style='vertical-align: middle;'>".$product->product_cost."</td>
		<td align='right' style='vertical-align: middle;'>".$product->product_price."</td>
		<td align='center' style='vertical-align: middle;'>
			<a href='index.php?content=product&edit=y&id_product_attribute=".$id_product_attribute."&id_product=".$id_product."&tab=2' ".$can_edit.">
				<button type='button' class='btn btn-warning btn-sx'><i class='fa fa-pencil' style='color: #fff;'></i></button>
			</a>
		</td>
		<td align='center' style='vertical-align: middle;'>
			<a href='controller/productController.php?delete=y&id_product_attribute=".$id_product_attribute."' ".$can_edit.">
				<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ ".$product->reference."');\"><i class='fa fa-trash' style='color: #fff;'></i>	</button>
			</a>
		</td>
		</tr>
		<script>
		$('#input".$is."').bind('enterKey',function(){
			var barcode = $(this).val();
			if(barcode !=''){
			$.ajax({
				url:'controller/productController.php?check&id_product_attribute=".$id_product_attribute."&code='+barcode
				,type:'GET',cache:false,success: function(data){
					if(data == 0){
						$('#p_".$id_product_attribute."').text(barcode);
						$('#input".$is."').css('display','none');
						$('#p_".$id_product_attribute."').css('display','block');
						$('#input".($is+1)."').focus();
					}else if(data == 1){
						alert('บาร์โค้ดซ้ำ');
						$(this).focus();
					}else if( data == 2){
						alert('เพิ่มข้อมูลไม่สำเร็จ');
						$(this).focus();
					}
				}
			});
			}
		});
		
		$('#input".$is."').keyup(function(e){
			if(e.keyCode == 13)
			{
				$(this).trigger('enterKey');
			}
		});
		$('#input_pack".$is."').bind('enterKey',function(){
			var barcode = $(this).val();
			if(barcode !=''){
			$.ajax({
				url:'controller/productController.php?check_pack&id_product_attribute=".$id_product_attribute."&code='+barcode
				,type:'GET',cache:false,success: function(data){
					if(data == 0){
						$('#p_pack".$id_product_attribute."').text(barcode);
						$('#input_pack".$is."').css('display','none');
						$('#p_pack".$id_product_attribute."').css('display','block');
						$('#input_pack".($is+1)."').focus();
					}else if(data == 1){
						alert('บาร์โค้ดซ้ำ');
						$(this).focus();
					}else if( data == 2){
						alert('เพิ่มข้อมูลไม่สำเร็จ');
						$(this).focus();
					}
				}
			});
			}
		});
		
		$('#input_pack".$is."').keyup(function(e){
			if(e.keyCode == 13)
			{
				$(this).trigger('enterKey');
			}
		});
		$('#input_qty".$is."').bind('enterKey',function(){
			var qty = $(this).val();
			if(qty !=''){
			$.ajax({
				url:'controller/productController.php?check_qty&id_product_attribute=".$id_product_attribute."&qty='+qty
				,type:'GET',cache:false,success: function(data){
						$('#p_qty".$id_product_attribute."').text(qty);
						$('#input_qty".$is."').css('display','none');
						$('#p_qty".$id_product_attribute."').css('display','block');
						$('#input_qty".($is+1)."').focus();
				}
			});
			}
		});
		
		$('#input_qty".$is."').keyup(function(e){
			if(e.keyCode == 13)
			{
				$(this).trigger('enterKey');
			}
		});
		
		</script>
		";
				$is++;
	}
	}else{
		echo" <tr><td colspan='10'><h4 align='center'>ยังไม่มีรายการ</h4></td></tr>	";
	}
	echo"
	</table>		
	</div>";
	//************************************************************************* END TAB 2 ***********************************************************//
	//************************************************************************* TAB 3 *********************************************************************//
	echo"
	<div class='col-sm-12' id='image' style='$tab3'>
	
	<div class='row'><div class='col-sm-12'><h3>เพิ่มรูปภาพสำหรับสินค้านี้</h3></div></div>
	<form id='image_form' enctype='multipart/form-data' action='controller/productController.php?img_upload=true' method='post'>
	<table width='100%' border='0'>
		<tr><td colspan='2'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' /></td></tr>
		<tr>
			<td width='15%'>&nbsp; <input type='hidden' name='id_product' value='$id_pro_detail' /></td>
			<td>
			<div class='row'>
					<div class='col-sm-6' style='margin-bottom:10px;'><input type='file' title='เลือกรูปภาพ' name='image[]' id='browse' class='btn btn-success' multiple $can_edit/></div>
					<div class='col-sm-3 style='margin-bottom:10px;' id='upload_btn' style='display:none;'><button type='button'  class='btn btn-primary' onclick='upload_image()' $can_edit>อัพโหลดรูปภาพ</button></div>
			</div>
            <div style='margin-bottom:10px; color:#999;' >Format: JPG, PNG, GIF ขนาดสูงสุด 2 MB.</div></td>
		</tr>
		<tr><td colspan='2'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' /></td></tr>
		<tr><td colspan='2' style='padding-top:15px'> 
				<table width='35%' style='border:1px solid #CCC;'>
                <thead>
                	<th width='40%' style='text-align:center;'>รูปภาพ</th><th width='20%' style='text-align:center;'>ตำแหน่ง</th><th width='20%' style='text-align:center;'>หน้าปก</th><th width='20%' style='text-align:center;'>การกระทำ</th>
				</thead>";
				imagesTable($id_pro_detail,2);
				echo"
				
	</table></form>
	<h1>&nbsp;</h1>
	</div>";
//**************************************************** END TAB 3 ****************************************************//
}else{ 
////**************************** ตารางรายการสินค้า ********************************///
if(isset($_GET['text'])){ $text = $_GET['text'];}else{ $text=""; }
echo "<div class='row'>
			<div class='col-sm-12'>
				<div class='row'>
					<div class='col-lg-4 col-md-4 col-sm-8 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-2'>
						<div class='input-group'>
            				<span class='input-group-addon'>&nbsp;&nbsp; ค้นหา &nbsp;&nbsp;</span>
            				<input type='text' name='search-text' id='search-text' class='form-control' value='$text' />
                			<span class='input-group-btn'>
               				 <button type='button' class='btn btn-default' id='search-btn'>&nbsp;&nbsp;<span class='glyphicon glyphicon-search'></span>&nbsp;&nbsp;</button>
                			</span>
           				 </div>
					</div>
					 <div class='col-xs-2'><a style='text-align:center; background-color:transparent; padding-bottom:0px;' href='index.php?content=product'>
                	<button type='button' class='btn btn-default'>รีเซต</button>
                </a></div>
				</div>
			</div><div class='col-sm-12'>	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' /></div></div>
				";
echo"<div class='row'>	<div class='col-xs-12' id='result'>";
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	if(isset($_GET['searchtext'])){ $where = $_GET['searchtext']; }else{ $where = ""; }
	$paginator->Per_Page("tbl_product",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=product&searchtext=$where&text=$text");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page; 
echo"
<table class='table table-striped'>
<thead>
	<th width='5%' style='text-align:center;'>ID</th><th width='10%' style='text-align:center;' >รูปภาพ</th><th width='15%'>รหัสสินค้า</th><th width='25%'>ชื่อสินค้า</th><th width='15%'>หมวดหมู่</th>
      <th width='8%' style='text-align:center;'>ราคาทุน</th><th width='8%' style='text-align:center;'>ราคาขาย</th><th width='5%' style='text-align:center;'>สถานะ</th><th colspan='2'style='text-align:center;'>การกระทำ</th>
</thead>";
	$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, category_name, product_cost, product_price, tbl_product.active FROM tbl_product LEFT JOIN tbl_category ON tbl_product.default_category_id = tbl_category.id_category $where ORDER BY tbl_product.date_upd DESC LIMIT $Page_Start, $Per_Page  ");
	$row = dbNumRows($sql);
	$i=0;
	
	while($i<$row){
			list($id_product, $product_code, $product_name, $category_name, $product_cost, $product_price, $active) = dbFetchArray($sql);
		echo "
		<tr>
			<td align='center' style='vertical-align:middle;'>$id_product</td>
			<td "; if($edit==1){ echo "style='text-align:center; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">".getCoverImage($id_product,1)."
			<td "; if($edit==1){ echo "align='left' style='vertical-align:middle; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">$product_code</td>
			<td "; if($edit==1){ echo "align='left' style='vertical-align:middle; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">$product_name</td>
			<td "; if($edit==1){ echo "align='left' style='vertical-align:middle; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">$category_name</td>
			<td "; if($edit==1){ echo "align='center' style='vertical-align:middle; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">". number_format($product_cost,2)."</td>
			<td "; if($edit==1){ echo "align='center' style='vertical-align:middle; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\""; } echo ">". number_format($product_price,2)."</td>
			<td align='center' style='vertical-align:middle;'>"; if($active ==1){ if($edit==1){echo "<a href='controller/productController.php?active=$active&id_product=$id_product'>";} echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>"; if($edit==1){echo "</a>";} }else{ if($edit==1){echo "<a href='controller/productController.php?active=$active&id_product=$id_product'>"; } echo"<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>"; if($edit==1){echo "</a>";} } echo"</td>
			<td align='center' style='vertical-align:middle;'>
				<a href='index.php?content=product&edit=y&id_product=$id_product&tab=1' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a>
			</td>
			<td align='center' style='vertical-align:middle;'><a href='controller/productController.php?delete=y&id_product=$id_product' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_code : $product_name ? การกระทำนี้ไม่สามารถย้อนคืนได้');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
			</td>
		</tr>";
		$i++;
	}			
echo"
</table>";
echo $paginator->display_pages();
echo"</div></div>";
		echo "<br><br>";
}
?>
</div><!--  end Container -->
<script src="../library/js/jquery.cookie.js"></script>
<script> 
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};
$("#cost").ForceNumericOnly();
$("#price").ForceNumericOnly();
$("#discount").ForceNumericOnly();
$('input[type=file]').bootstrapFileInput();
$('.file-inputs').bootstrapFileInput();
</script>
<script language="javascript">
$(document).ready(function(){
	$("#product_code").keyup(function(){
		var product_code = $("#product_code").val();
		$.ajax({	
			type: "GET", 
			url:"controller/productController.php",
			cache:false, data:"product_code="+product_code,
			success: function(msg)	{
				if( $("#product_code").val().length >3){
						if(msg==1){
							$("#valid").val(msg);
							$("#validate").html("&nbsp;&nbsp;รหัสสินค้านี้มีอยู่แล้ว");
						}else if(msg==0){
							$("#valid").val(msg);
							$("#validate").html(" ");
						}
				}
			}
		});
	});
});
$(document).ready(function(){
	$("#reference").keyup(function(){
		var reference = $("#reference").val();
		$.ajax({	
			type: "GET", 
			url:"controller/productController.php",
			cache:false, data:"reference="+reference,
			success: function(ref_msg)	{
				if( $("#reference").val().length >3){
						if(ref_msg==1){
							$("#valid_ref").val(ref_msg);
							$("#error_ref").html("&nbsp;&nbsp;รหัสอ้างอิงซ้ำ");
						}else if(ref_msg==0){
							$("#valid_ref").val(ref_msg);
							$("#error_ref").html(" ");
						}
				
				}
			}
		});
	});
});
$(document).ready(function(){
	$("#barcode").keyup(function(){
		var barcode = $("#barcode").val();
		$.ajax({	
			type: "GET", 
			url:"controller/productController.php",
			cache:false, data:"barcode="+barcode,
			success: function(code_msg)	{
				if( $("#barcode").val().length >3){
						if(code_msg==1){
							$("#valid_code").val(code_msg);
							$("#error_code").html("&nbsp;&nbsp;บาร์โค้ดซ้ำ");
						}else if(code_msg==0){
							$("#valid_code").val(code_msg);
							$("#error_code").html(" ");
						}
				}
			}
		});
	});
});
$(document).ready(function(){
	$("#barcode_pack").keyup(function(){
		var barcode = $("#barcode_pack").val();
		$.ajax({	
			type: "GET", 
			url:"controller/productController.php",
			cache:false, data:"barcode="+barcode,
			success: function(code_msg)	{
				if( $("#barcode_pack").val().length >3){
						if(code_msg==1){
							$("#valid_code_pack").val(code_msg);
							$("#error_code_pack").html("&nbsp;&nbsp;บาร์โค้ดซ้ำ");
						}else if(code_msg==0){
							$("#valid_code_pack").val(code_msg);
							$("#error_code_pack").html(" ");
						}
				}
			}
		});
	});
});
function edit_attribute(){
	var ref_check = $("#valid_ref").val();
	var code_check =$("#valid_code").val();
	if(ref_check==1){
		alert("รหัสอ้างอิงซ้ำ");
	}else if(code_check==1){
		alert("บาร์โค้ดซ้ำ");
	}else{
		$("#product_attribute_form").submit();
	}
}
function validate(){
	var checked = $("#valid").val();
	if(checked==1){
		alert ("รหัสสินค้านี้มีอยู่แล้ว");
		}else{
		$("#product_form").submit();
	}
}	
</script>
<script>
$("#tab1").click(function(){
	$("#info").css("display","block");
	$("#combination").css("display","none");
	$('#image').css("display","none");
	$("#tab1").addClass("active");
	$("#tab2").removeClass();
	$("#tab3").removeClass();
});
$("#tab2").click(function(){
	$("#info").css("display","none");
	$("#combination").css("display","block");
	$("#image").css("display","none");
	$("#tab2").addClass("active");
	$("#tab1").removeClass();
	$("#tab3").removeClass();
});
$("#tab3").click(function(){
	$("#info").css("display","none");
	$("#combination").css("display","none");
	$("#image").css("display","block");
	$("#tab3").addClass("active");
	$("#tab2").removeClass();
	$("#tab1").removeClass();
});
$('#browse').click(function(){
	$("#upload_btn").css("display","block");
});
function upload_image(){
	$("#image_form").submit();
}
$("#edit_barcode_pack").click(function(e) {
    $(".barcode_pack").css("display","none");
	$("input[name=input_barcode_pack]").css("display","block");
});
$("#edit_barcode").click(function(e) {
    $(".barcode").css("display","none");
	$("input[name=input_barcode]").css("display","block");
});
$("#edit_qty_pack").click(function(e) {
    $(".qty_pack").css("display","none");
	$("input[name=input_qty_pack]").css("display","block");
});
function get_row(){
	$("#rows").submit();
}
	$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var rows = $.cookie("get_rows");
	$.ajax({
		url:"controller/productController.php?text="+query_text+"&get_rows="+rows, type: "GET", cache:false,
		success: function(result){
			$("#result").html(result);
		}
	});
});
</script>



