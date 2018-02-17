<?php 
	$page_menu = "invent_customer";
	$page_name = "ลูกค้า";
	$id_tab = 21;
	$id_profile = $_COOKIE['profile_id'];
   $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	?>
<?php
	if( isset( $_GET['edit'] ) || isset( $_GET['id_customer'] ) ) : 		 
		$btn = "<a href='index.php?content=customer'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= can_do($edit, "&nbsp;<button type='button' id='btn_submit' class='btn btn-success'><i class='fa fa-save'></i>&nbsp; บันทึก</button>");
	elseif( isset( $_GET['edit'] ) || isset( $_GET['add'] ) ) :
		$btn = "<a href='index.php?content=customer'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= can_do($add, "&nbsp;<button type='button' id='btn_submit' class='btn btn-success'><i class='fa fa-save'></i>&nbsp; บันทึก</button>");
	elseif( isset( $_GET['view_detail'] ) ) :
		$btn = "<a href='index.php?content=customer'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	else :
		$btn = can_do($add, "<a href='index.php?content=customer&add=y' ><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	endif;
	   ?>    
    
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])) : ?>
	
<?php elseif(isset($_GET['add'])) : ?>
	
<?php endif; ?>

<div class="row">
	<div class="col-sm-6"><h3 class="title"><i class="fa fa-users"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
    	<p class="pull-right">
			<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<!--------------------------------------------------------------- เพิ่มลูกค้าใหม่  --------------------------------------------->
<?php if( isset( $_GET['add'] ) ) : ?>
<form id='customer_form' action='controller/customerController.php?add=y' method='post'>
<button type='submit' id="btn_submit2" class='btn btn-success' style="display:none" ><i class="fa fa-save"></i>&nbsp;บันทึก</button>
<div class="row">
	<div class="col-sm-4 label-left">คำนำหน้า</div>
    <div class="col-sm-4">
    	<input name="gender" id="2" value="2" style="margin-left:15px;" type="radio"><label for="2" style="margin-left:15px;">นาย</label>
        <input name="gender" id="3" value="3" style="margin-left:15px;" type="radio"><label for="3" style="margin-left:15px;">นางสาว</label>
        <input name="gender" id="4" value="4" style="margin-left:15px;" type="radio"><label for="4" style="margin-left:15px;">นาง</label>
    </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">รหัส</div>
    <div class="col-sm-4"><input type='text' name='customer_code' id='customer_code' class='form-control input-sm' placeholder="รหัสลูกค้า" /></div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">ชื่อ</div>
    <div class="col-sm-4"><input type='text' name='first_name' id='first_name' class='form-control input-sm' required='required' placeholder="ชื่อลูกค้า" /></div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> นามสกุล</div>
    <div class="col-sm-4"> <input type='text' name='last_name' id='last_name' class='form-control input-sm' placeholder="นามสกุลลูกค้า"/></div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> ร้าน/บริษัท</div>
    <div class="col-sm-4"><input type='text' name='company' id='company' class='form-control input-sm' placeholder="ชื่อร้าน หรือ บริษัท" autocomplete='off'/> </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">อีเมล์</div>
    <div class="col-sm-4"><input type='text' name='email' id='email' class='form-control input-sm' placeholder="อีเมล์ลูกค้า" autocomplete='off'/> </div>
    <div class="col-sm-4"><span style="color:#CCC;">กำหนดอีเมล์หากต้องการเปิดบัญชีผู้ใช้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">รหัสผ่าน</div>
    <div class="col-sm-4"> <input type='password' name='password' id='password' placeholder="กำหนดรหัสผ่าน" class='form-control input-sm' /></div>
    <div class="col-sm-4"><span style="color:#CCC;">กำหนดรหัสผ่านหากต้องการเปิดบัญชีผู้ใช้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> วันเกิด</div>
    <div class="col-sm-4">
    	<select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectDay(); ?></select>
		<select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectMonth(); ?></select>
		<select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectYear(); ?></select> 
   </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> วงเงินเครดิต</div>
    <div class="col-sm-4">
    	<input type='text' name='credit_amount' id='credit_amount'  class='form-control input-sm' value='0'/> 
			</div>
    <div class="col-sm-4">บาท</div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> เครดิตเทอม</div>
    <div class="col-sm-4"> <input type='text' name='credit_term' id='credit_term' class="form-control input-sm"  value='0'/> </div>
    <div class="col-sm-4">วัน</div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">สถานะ</div>
    <div class="col-sm-4">
    	<input type='radio' name='active' id='yes' value='1' checked='checked' /><label for='yes' style='margin-left:5px; margin-right:25px;'><i class="fa fa-check" style="color:green"></i></label>
		<input type='radio' name='active' id='no' value='0' /><label for='no' style='margin-left:5px; margin-right:25px;'><i class="fa fa-remove" style="color:red;"></i></label>
    </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <!--
    <div class="col-sm-4 label-left"> กลุ่มลูกค้า</div>
    <div class="col-sm-4"><?php customerGroupTable(); ?> </div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12"></div>
    -->
    
     <div class="col-sm-4 label-left"> กลุ่มหลัก</div>
    <div class="col-sm-4">
    	<select name='default_group' class="form-control input-sm"><?php selectCustomerGroup(); ?></select>
    </div>
    <div class="col-sm-4"><span style="color:#CCC;">หมวดหมู่หลักของลูกค้าสำหรับใช้กับส่วนลด</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
     <div class="col-sm-4 label-left">พนักงานขาย</div>
    <div class="col-sm-4">
    	<select name='id_sale' class="form-control input-sm"><?php  saleList(); ?></select>
    </div>
    <div class="col-sm-4"><span style="color:red;">*&nbsp;&nbsp;</span><span style="color:#CCC;">เลือกพนักงานขายที่รับผิดชอบลูกค้าคนนี้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    <div class="col-sm-12"><h3>&nbsp;</h3></div>
    
</div><!--/ Row -->
</form>
    
<!-------------------------------------------------  จบหน้าเพิ่ม -------------------------------------------->
<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_customer'] ) ) : ?>
<!-------------------------------------------------  แก้ไข -------------------------------------------->

<?php	$id_customer = $_GET['id_customer']; ?>
<?php	$customer = new customer($id_customer); ?>
<form id='customer_form' action='controller/customerController.php?edit=y' method='post'>
<div class="row">
	<input type='hidden' name='id_customer' id='id_customer' value='<?php echo $id_customer; ?>' />
	<div class="col-sm-4 label-left">คำนำหน้า</div>
    <div class="col-sm-4"><?php getTitleRadio($customer->id_gender); ?></div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">รหัส</div>
    <div class="col-sm-4"><input type='text' name='customer_code' id='customer_code' class='form-control input-sm' placeholder="รหัสลูกค้า" value="<?php echo $customer->customer_code; ?>" /></div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">ชื่อ</div>
    <div class="col-sm-4"><input type='text' name='first_name' id='first_name' class='form-control input-sm' required='required' placeholder="ชื่อลูกค้า" value="<?php echo $customer->first_name; ?>" /></div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> นามสกุล</div>
    <div class="col-sm-4"> <input type='text' name='last_name' id='last_name' class='form-control input-sm' placeholder="นามสกุลลูกค้า" value="<?php echo $customer->last_name; ?>"/></div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> ร้าน/บริษัท</div>
    <div class="col-sm-4"><input type='text' name='company' id='company' class='form-control input-sm' placeholder="ชื่อร้าน หรือ บริษัท" autocomplete='off' value="<?php echo $customer->company; ?>"/> </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">อีเมล์</div>
    <div class="col-sm-4"><input type='text' name='email' id='email' class='form-control input-sm' placeholder="อีเมล์ลูกค้า" autocomplete='off' value="<?php echo $customer->email; ?>"/> </div>
    <div class="col-sm-4"><span style="color:#CCC;">กำหนดอีเมล์หากต้องการเปิดบัญชีผู้ใช้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">รหัสผ่าน</div>
    <div class="col-sm-4"> <input type='password' name='password' id='password' placeholder="กำหนดรหัสผ่าน" class='form-control input-sm' /></div>
    <div class="col-sm-4"><span style="color:#CCC;">กำหนดรหัสผ่านหากต้องการเปิดบัญชีผู้ใช้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> วันเกิด</div>
    <div class="col-sm-4">
    	<select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectDay(date('d',strtotime($customer->birthday))); ?></select>
		<select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectMonth(date('m',strtotime($customer->birthday))); ?></select>
		<select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'><?php selectYear(date('Y',strtotime($customer->birthday))); ?></select> 
   </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> วงเงินเครดิต</div>
    <div class="col-sm-4">
    	<input type='text' name='credit_amount' id='credit_amount'  class='form-control input-sm' value='<?php echo $customer->credit_amount; ?>'/> 
			</div>
    <div class="col-sm-4">บาท</div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left"> เครดิตเทอม</div>
    <div class="col-sm-4"> <input type='text' name='credit_term' id='credit_term' class="form-control input-sm"  value='<?php echo $customer->credit_term; ?>'/> </div>
    <div class="col-sm-4">วัน</div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
    <div class="col-sm-4 label-left">สถานะ</div>
    <div class="col-sm-4">
    	<input type='radio' name='active' id='yes' value='1' <?php echo isChecked($customer->active, 1); ?> /><label for='yes' style='margin-left:5px; margin-right:25px;'><i class="fa fa-check" style="color:green"></i></label>
		<input type='radio' name='active' id='no' value='0' <?php echo isChecked($customer->active, 0); ?> /><label for='no' style='margin-left:5px; margin-right:25px;'><i class="fa fa-remove" style="color:red;"></i></label>
    </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
   <!-- 
    <div class="col-sm-4 label-left"> กลุ่มลูกค้า</div>
    <div class="col-sm-4"><?php customerGroupTable($id_customer); ?> </div>
    <div class="col-sm-4"><span style="color:red;">*</span></div>
    <div class="col-sm-12"></div> -->
    
    <div class="col-sm-4 label-left"> กลุ่มหลัก</div>
    <div class="col-sm-4">
    	<select name='default_group' class="form-control input-sm"><?php selectCustomerGroup($customer->id_default_group); ?></select>
    </div>
    <div class="col-sm-4"><span style="color:#CCC;">หมวดหมู่หลักของลูกค้าสำหรับใช้กับส่วนลด</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
     <div class="col-sm-4 label-left">พนักงานขาย</div>
    <div class="col-sm-4">
    	<select name='id_sale' class="form-control input-sm"><?php  saleList($customer->id_sale); ?></select>
    </div>
    <div class="col-sm-4"><span style="color:red;">*&nbsp;&nbsp;</span><span style="color:#CCC;">เลือกพนักงานขายที่รับผิดชอบลูกค้าคนนี้</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    <div class="col-sm-12"><h3>&nbsp;</h3></div>
    
</div><!--/ Row -->
<!-----------------------------------------------------  ส่วนลดลูกค้า ----------------------------------------------->

	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
	<div class='row'>
	<div class='col-sm-6'><h3 class="title">ส่วนลดลูกค้า</h3></div>
	<div class='col-sm-6'>
       <p class="pull-right">
	   	 <button type='submit' id="btn_submit2" class='btn btn-success' ><i class="fa fa-save"></i>&nbsp;บันทึก</button>
		</p>
		 </div>
	</div>	 
	   
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class="row"> 
	<input type='hidden' name='customer_discount' id='customer_discount' value='0'/>   
    <div class="col-sm-4 label-left"><input type='radio' name='apply' id='apply1' value='1'/><label for='apply1' style='padding-left:15px;'>ส่วนลด :&nbsp;</label></div>
    <div class="col-sm-2">
    	<div class='input-group'>
		<input type='text' name='discount_all' id='discount_all' class='form-control input-sm'  disabled='disabled' />
		<span class='input-group-addon'> % </span></div>
    </div>
    <div class="col-sm-6"><span style="color:#666;">กำหนดส่วนลดในช่องนี้หากต้องการให้ส่วนลดนี้ในทุกรายการสินค้า</span></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
    
</div><!--/ Row -->  
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class="row">    
    <div class="col-sm-4 label-left"><input type='radio' name='apply' id='apply2' value='2' checked='checked'/><label for='apply2' style='padding-left:15px;'>ส่วนลด :&nbsp;</label></div>
    <div class="col-sm-8">
    	<span style="color:#666;">กำหนดส่วนลดตามหมวดหมู่ หากกำหนดล่วนลดนี้จะยกเลิกการให้ส่วนลดด้านบน</span>
    </div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>

<?php
	$cate = new category;
	$list = $cate->categoryList();
	$row = dbNumRows($list);
	$i =0;
	while($i<$row) : 
		list($id_category, $category_name, $array) = dbFetchArray($list); 
?>
	<div class="col-sm-4 label-left"><?php echo $category_name; ?></div>
    <div class="col-sm-4">
    	<div class='input-group'>
		<input type='text' class='form-control input-sm' name='category[<?php echo $id_category; ?>]' id='discount[]' value="<?php echo $customer->getDiscount($id_category); ?>" />
		<span class='input-group-addon'> % </span></div>
    </div>
    <div class="col-sm-4"></div>
    <div class="col-sm-12">&nbsp;<!-------- Divider ------>&nbsp;</div>
<?php $i++;  ?>
<?php endwhile; ?>	

</div><!--/ Row -->   
</form>
<!-- --------------------------------------------- จบหน้าแก้ไข ------------------------------------->
<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_customer'] ) ) :
//********************** แสดงรายละเอียด *********************************************************//
	$id_customer = $_GET['id_customer'];
	$customer = getCustomerDetail($id_customer);
	echo"
	<div class='row'>
	<div class='col-sm-6'>
		<table style='width:100%; padding:10px; border-right: 1px solid #ccc;'>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>ชื่อ : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".$customer['first_name']." &nbsp;".$customer['last_name'] ."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>อีเมล์ : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".$customer['email']."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>อายุ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>";if($customer['birthday'] !="0000-00-00"){ echo round(dateDiff($customer['birthday'],date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer['birthday']).")" ;}else{echo "-";} echo"</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>เพศ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>"; if($customer['id_gender']==1){ echo"ไม่ระบุ";}else if($customer['id_gender']==2){echo"ชาย";}else{echo"หญิง";} echo"</td></tr>
		</table>
	</div>
	<div class='col-sm-6'>
		<table style='width:100%; padding:10px;'>		
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>วันที่เป็นสมาชิก : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".thaiTextDate($customer['date_add'])."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>วงเงินเครดิต : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".number_format($customer['credit_amount'])." &nbsp;บาท</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>เครดิตเทอม : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".number_format($customer['credit_term'])."&nbsp;วัน</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>สถานะ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>"; if($customer['active']==1){echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>";}else{ echo "<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>";} echo"</td></tr>
		</table>
	</div>
	</div>
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />";
	echo"
	<div class='row'>
	<div class='col-sm-12'>
	<h4>ที่อยู่ </h4>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<thead>
			<th style='width:20%;'>บริษัท/ร้าน</th><th style='width:20%;'>ชื่อ</th><th style='width:40%;'>ที่อยู่</th><th style='width:20%;'>เบอร์โทรศัพท์</th>
		</thead>";
		$sql = getCustomerAddress($id_customer);
		$row = dbNumRows($sql);
		if($row>0){
			$i = 0;
			while($i<$row){
				$result = dbFetchArray($sql);
				$company = $result['company'];
				$customer_name = $result['firstname']."&nbsp;".$result['lastname'];
				$address = $result['address1']."&nbsp;".$result['address2']."&nbsp; จ.".$result['city']."&nbsp;".$result['postcode'];
				$phone = $result['phone'];
				echo"<tr><td>$company</td><td>$customer_name</td><td>$address</td><td>$phone</td></tr>";
				$i++;
			}
		}else{
			echo "<tr><td colspan='4' align='center'><h4>ยังไม่มีข้อมูลที่อยู่</h4></td></tr>";
		}
		echo"		
		</table>
	</div></div>";
//************************************************ จบหน้าแสดงรายละเอียด ************************************************//
else :
//************************************************ แสดงรายการ *************************************************//
	$text = "";
	if( isset( $_POST['search-text'] )  )
	{ 
	$text = $_POST['search-text']; setcookie("customer_search_text", $_POST['search-text'], time()+3600, "/"); 
	}
	else if( isset( $_COOKIE['customer_search_text'] ) ) 
	{
		$text = $_COOKIE['customer_search_text'];	
	}

?>
<form id="search_form" method="post">
<div class='row'>
	<div class='col-xs-4 col-xs-offset-3'>
		<div class='input-group'>
    		<input type='text' name='search-text' id='search-text' class='form-control' value="<?php echo $text; ?>" />
       		<span class='input-group-btn'>
       			 <button type='button' class='btn btn-default' id='search-btn'><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
       		</span>
    	 </div>
	</div>
	<div class='col-xs-1'>
		<a style='text-align:center; background-color:transparent; padding-bottom:0px;' href='controller/customerController.php?clear_filter'>
       		<button type='button' class='btn btn-default'><i class='fa fa-refresh'></i>&nbsp;รีเซต</button>
       	</a>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
			
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12' id='result'>
<?php     
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	if($text !=""){
		$where = "WHERE company LIKE '%$text%' OR first_name LIKE '%$text%' OR last_name LIKE'%$text%' OR customer_code LIKE'%$text%' ORDER BY date_upd DESC";
	}else{
		$where = "WHERE id_customer != '' ORDER BY date_upd DESC";
	}
	$paginator->Per_Page("tbl_customer",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=customer");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page; 
?>	
<table class='table table-striped table-hover'>
	<thead style='background-color:#48CFAD; font-size:10px;'>
		<th style='width:5%; text-align:center;'>ID</th>
        <th style='width:10%;'>รหัส</th>
        <th style='width:35%;'>ชื่อ</th>
        <th style='width:35%;'>ร้าน/บริษัท</th>
		<th style='width:5%; text-align:center;'>สถานะ</th>
        <th style='text-align:right;'>การกระทำ</th>
	</thead>
<?php    
	$sql = dbQuery("SELECT id_customer, customer_code, company, first_name, last_name, credit_amount, credit_term, active, date_add FROM tbl_customer $where LIMIT $Page_Start , $Per_Page");
	$row = dbNumRows($sql);
	$i = 0;
	if($row>0) :
		while($i<$row) :
			list($id_customer, $customer_code, $company, $first_name, $last_name, $credit_amount, $credit_term, $active, $date_add) = dbFetchArray($sql);	
?>		
	<tr style="font-size:12px;">
		<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=customer&view_detail=y&id_customer=<?php echo $id_customer; ?>'"><?php echo $id_customer; ?></td>
		<td style='cursor:pointer;' onclick="document.location='index.php?content=customer&view_detail=y&id_customer=<?php echo $id_customer; ?>'"><?php echo $customer_code; ?></td>
		<td style='cursor:pointer;' onclick="document.location='index.php?content=customer&view_detail=y&id_customer=<?php echo $id_customer; ?>'"><?php echo $first_name." ". $last_name; ?></td>
		<td style='cursor:pointer;' onclick="document.location='index.php?content=customer&view_detail=y&id_customer=<?php echo $id_customer; ?>'"><?php echo $company; ?></td>
		<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=customer&view_detail=y&id_customer=<?php echo $id_customer; ?>'"><?php echo isActived($active); ?></td>
		<td align='right' >
			<?php if($edit) : ?>
            <a href='index.php?content=customer&edit=y&id_customer=<?php echo $id_customer; ?>'><button class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></button></a>
        	<?php endif; ?>
            <?php if($delete) : ?>
            <button class='btn btn-danger btn-xs' onclick="confirm_delete('คุณแน่ใจว่าต้องการลบ $first_name', 'โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้','controller/customerController.php?delete=y&id_customer=<?php echo $id_customer; ?>')">
			<i class='fa fa-trash'></i></button>
            <?php endif; ?>
        </td>
	</tr>
<?php 		$i++; ?>
<?php 	endwhile; ?>			
<?php else : ?>
	<tr><td colspan='10' align='center'><h3>--------- ไม่มีรายการ  ----------</h3></td></tr>
<?php endif; ?>
	</table>
<?php echo $paginator->display_pages(); ?>
    <h3>&nbsp;</h3>
</div>
</div>
<?php endif;	 ?>
</div>
<script src="../library/js/jquery.cookie.js"></script>
<script>
function validate(){
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var email = $("#email").val();
	$("#customer_form").submit();
}
$(document).ready(function() {
	$("#apply1").change(function() {
        $("#discount_all").removeAttr("disabled");
		$("#discount\\[\\]").each(function() {
            $(this).attr("disabled","disabled");
        });
		$("#discount_all").focus();
    });
});
$(document).ready(function() {
	$("#apply2").change(function() {
		$("#discount_all").attr("disabled","disabled");
		$("#discount\\[\\]").each(function() {
            $(this).removeAttr("disabled");
        });
		$("#discount").focus();
    });
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
$("#search-btn").click(function(e){
	$("#search_form").submit();
});
/*$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var rows = $.cookie("get_rows");
	var profile_id = $.cookie("profile_id");
	//swal("Profile_id : "+profile_id);
	$.ajax({
		url:"controller/customerController.php?text="+query_text+"&get_rows="+rows+"&id_profile="+profile_id, type: "GET", cache:false,
		success: function(result){		
			$("#result").html(result);
		}
	});
});*/

$("#btn_submit").click(function(e) {
    $("#btn_submit2").click();
});
</script>
