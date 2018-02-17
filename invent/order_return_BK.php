<?php 
	$page_name = "คืนสินค้าจากการขาย";
	$id_tab = 6;
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
	$btn = "";
	if(isset($_GET['edit']) && isset($_GET['id_return_order']) ) :
		$btn .= "<a href='index.php?content=order_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='edit_stock()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['edit']) || isset($_GET['add']) ) :
		$btn .= "<a href='index.php?content=order_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='submit_stock()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['view_detail']) ) :
		$btn .= "<a href='index.php?content=order_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	else :
		$btn .= can_do($add, "<a href='index.php?content=order_return&add=y'><button type='button' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	endif;
	
	?>
  
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])){
	echo"<form name='order_return_form' id='order_return_form' action='controller/storeController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form name='order_return_form' id='order_return_form' action='controller/storeController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-xs-6"><h3 class="title"><span class="glyphicon glyphicon-import"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
       <p class="pull-right">
       	<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
function select_return_reason($selected = ""){
	echo"<option value='0'>------ระบุเหตุผล---------</option>";
	$sql = dbQuery("SELECT * FROM tbl_return_reason");
	while($row = dbFetchArray($sql)){
		$id_return_reason = $row['id_return_reason'];
		$reason_name = $row['reason_name'];
		if($id_return_reason ==$selected){ $select = "selected='selected'"; }else{ $select = ""; }
		echo"<option value='$id_return_reason' $select>$reason_name</option>";
	}
}

if(isset($_GET['barcode_zone'])){ $barcode_zone = $_GET['barcode_zone']; }else{ $barcode_zone = "";}
if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone']; }else{ $id_zone = "";}
if(isset($_GET['name_zone'])){ $name_zone = $_GET['name_zone'];  }else{ $name_zone = ""; }
if(isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	$id_customer = $ro->id_customer;
	$customer = new customer($id_customer);
	$customer_name = $customer->full_name;
	$employee = new employee($ro->id_employee);
	$employee_name = $employee->first_name;
	$reference = $ro->reference;
	$remark = $ro->remark;
	$id_return_reason = $ro->id_return_reason;
	$return_reason = $ro->return_reason;
	$date_add = thaiDate($ro->date_add);
}else{
	$id_return_order = "";
	$id_customer = "";
	$customer_name = "";
	$employee_name = "";
	$reference = get_max_reference("PREFIX_RETURN","tbl_return_order", "reference");
	$remark = "";
	$id_return_reason = "";
	$return_reason = "";
	$date_add = thaiDate(date('Y-m-d'));
}

	if($id_zone !=""){ 
			$active = "disabled=disabled"; 
			$actived = "";
			}else{ 
			$active = "";
			$actived = "disabled=disabled"; 
		}
if(isset($_GET['add'])){ 
///***************************** หน้ารับสินค้าเข้า *******************************//
echo"	<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>";
					echo"<input type='hidden' name='id_return_order' id='id_return_order' value='$id_return_order' /><input type='hidden' name='return_no' value='$reference' />
					<input type='text' class='form-control' name='reference' id='reference'  value='$reference' disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>ลูกค้า</span>
					<input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
				 <input type='text' class='form-control'  name='customer_name' id='customer_name' value='$customer_name' "; if(isset($_GET['id_return_order'])){  echo"disabled='disabled' "; } echo"/>
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เหตุผล</span>
				 	<select name='id_reason' id='id_reason' class='form-control'"; if(isset($_GET['id_return_order'])){  echo"disabled='disabled' "; } echo" >"; select_return_reason($id_return_reason); echo"</select>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>
					<input type='text' class='form-control'  name='date' id='date' value='$date_add'"; 	if(isset($_GET['id_return_order'])){	echo"' disabled='disabled'";} echo" />
				</div>
			</div>
         </div>
		 <div class='row' style='margin-top:15px;'>
		 	<div class='col-xs-9'>
				<div class='input-group'>
					<span class='input-group-addon'>หมายเหตุ</span>
					<input type='text' class='form-control'  name='remark' id='remark' value='$remark'"; 	if(isset($_GET['id_return_order'])){	echo"' disabled='disabled'";} echo" />
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='submit' class='btn btn-default'"; if(isset($_GET['id_return_order'])){ echo" disabled='disabled'";} echo">รับสินค้า</button>
			</div>	
		 </div>
			</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />";
			if(isset($_GET['id_return_order'])){
			echo"<form id='detail_form' action='controller/storeController.php?add_detail=y&id_return_order=$id_return_order' method='post'>
					<div class='row'>
					<div class='col-xs-4 col-xs-offset-4'>
						<div class='input-group'>
						<span class='input-group-addon'>คลัง</span>";
					 if(isset($_GET['id_warehouse'])){	$id_warehouse = $_GET['id_warehouse']; }else{ $id_warehouse ="";}
						echo"
						<select class='form-control' name='id_warehouse' >"; warehouseList($id_warehouse); echo"</select>
						</div>
					</div><div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone()' $actived >เปลี่ยนโซน(F2)</button></div>
					</div>
					<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<div class='row'>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดโซน</span>
					<input type='text' class='form-control' name='barcode_zone' id='barcode_zone' $active value='$barcode_zone' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>ชื่อโซน</span>
					<input type='text' class='form-control' name='zone_name' id='zone_name' $active value='$name_zone'/>
					</div></div>
					<div class='col-xs-2'>
					<div class='input-group'>
					<span class='input-group-addon'>จำนวน</span>
					<input type='text' class='form-control' name='qty' id='qty' value='1' /><input type ='hidden' name='date_add' value='$date_add' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดสินค้า</span>
					<input type='text' class='form-control' name='barcode_item' id='barcode_item' autofocus />
					</div></div>
					<div class='col-xs-1'>
					<button type='button' id='ok' class='btn btn-default' onclick='submit_detail()'>OK</button>
					</div>
					</div></form><hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='10%' style='text-align:center;'>โซน</th>
						<th width='10%' style='text-align:center;'>วันที่</th><th width='10%' style='text-align:center;'>พนักงาน</th><th width='10%' style='text-align:center;'>การกระทำ</th>
					</thead>";	
					$sql = dbQuery("SELECT id_return_order_detail FROM tbl_return_order_detail WHERE id_return_order = $id_return_order ORDER BY date_add DESC");
					$row = dbNumRows($sql);
					if($row>0){
						$i = 0;
						$n =$row;
						$total = 0;
						$table = "";
						$total_row = "";
						while($i<$row){
							list($id_return_order_detail) = dbFetchArray($sql);
							//เรียกใช้ method return_order_detail จาก class return_order ที่สร้าง object ไว้ข้างบน
							$ro->return_order_detail($id_return_order_detail);
							$product = new product();
							$id_product = $product->getProductId($ro->id_product_attribute);
							$product->product_detail($id_product);
							$product->product_attribute_detail($ro->id_product_attribute);
							$zone = get_zone($ro->id_zone);
							$id_warehouse = get_warehouse_by_zone($ro->id_zone);
							$warehouse = get_warehouse_name_by_id($id_warehouse);
							$detail_add = thaiDate($ro->detail_date_add);
							$em = new employee($ro->id_employee);
							$employee =  $em->first_name;
							$status = $ro->detail_status;
							$table .="<tr>
						<td align='center'>$n</td><td>".$product->reference."</td><td>".number_format($ro->qty)."</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>$detail_add</td><td align='center'>$employee</td><td align='center'>"; if($status==1){ $table .="<a href='controller/storeController.php?delete_stocked=y&id_return_order_detail=$id_return_order_detail'>";}else if($status==0){$table .="<a href='controller/storeController.php?delete=y&id_return_order_detail=$id_return_order_detail'>";} $table .="<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ".$product->reference." ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td></tr>";	
						$total = $total +$ro->qty;
	$i++;
	$n--;
						}
						$total_row .="<tr><td colspan='8' align='center'><h4>รวม ".number_format($total)." หน่วย</h4></td><tr>";
						$total_row .= $table;
						echo $total_row;
						}else{
							echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
						}
					echo"</table>";
				}
				

//****************************** จบหน้ารับสินค้าเข้า *********************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_return_order'])){
	//********************************************  หน้าแก้ไข  *********************************************//
	echo"	</form>
	<form id='edit_return_order_form' action='controller/storeController.php?edit=y&id_return_order=$id_return_order' method='post'>
		<input type='hidden' name='id_return_order' id='id_return_order' value='$id_return_order' />
		<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>";
					echo"<input type='hidden' name='return_no' value='$reference' />
					<input type='text' class='form-control' name='reference' id='reference'  value='$reference' disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>ลูกค้า</span>
					<input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
				 <input type='text' class='form-control'  name='customer_name' id='customer_name' value='$customer_name' "; if(isset($_GET['id_return_order'])){  echo"disabled='disabled' "; } echo"/>
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เหตุผล</span>
				 	<select name='id_reason' id='id_reason' class='form-control'"; if(isset($_GET['id_return_order'])){  echo"disabled='disabled' "; } echo" >"; select_return_reason($id_return_reason); echo"</select>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>
					<input type='text' class='form-control'  name='date' id='date' value='$date_add'"; 	if(isset($_GET['id_return_order'])){	echo"' disabled='disabled'";} echo" />
				</div>
			</div>
         </div>
		 <div class='row' style='margin-top:15px;'>
		 	<div class='col-xs-9'>
				<div class='input-group'>
					<span class='input-group-addon'>หมายเหตุ</span>
					<input type='text' class='form-control'  name='remark' id='remark' value='$remark'"; 	if(isset($_GET['id_return_order'])){	echo"' disabled='disabled'";} echo" />
				</div>
			</div>
			<div class='col-xs-1'>
			<button type='button' id='edit_btn' class='btn btn-default'>แก้ไข</button><button type='button' class='btn btn-default' id='update_btn' style='display:none;'>Update</button>
			</div>	
		 </div>
			</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />";
			if(isset($_GET['id_return_order'])){
			echo"<form id='detail_form' action='controller/storeController.php?add_detail=y&id_return_order=$id_return_order' method='post'>
					<div class='row'>
					<div class='col-xs-4 col-xs-offset-4'>
						<div class='input-group'>
						<span class='input-group-addon'>คลัง</span>";
					 if(isset($_GET['id_warehouse'])){	$id_warehouse = $_GET['id_warehouse']; }else{ $id_warehouse ="";}
						echo"
						<select class='form-control' name='id_warehouse' >"; warehouseList($id_warehouse); echo"</select>
						</div>
					</div><div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone()' $actived >เปลี่ยนโซน(F2)</button></div>
					</div>
					<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<div class='row'>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดโซน</span>
					<input type='text' class='form-control' name='barcode_zone' id='barcode_zone' $active value='$barcode_zone' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>"; if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone'];  echo "<input type ='hidden' name='id_zone' value='$id_zone' />";} echo"
					<span class='input-group-addon'>ชื่อโซน</span>
					<input type='text' class='form-control' name='zone_name' id='zone_name' $active value='$name_zone'/>
					</div></div>
					<div class='col-xs-2'>
					<div class='input-group'><input type ='hidden' name='date_add' value='$date_add' />
					<span class='input-group-addon'>จำนวน</span>
					<input type='text' class='form-control' name='qty' id='qty' value='1' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดสินค้า</span>
					<input type='text' class='form-control' name='barcode_item' id='barcode_item' autofocus />
					</div></div>
					<div class='col-xs-1'>
					<button type='button' id='ok' class='btn btn-default' onclick='submit_detail()'>OK</button>
					</div>
					</div></form><hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='10%' style='text-align:center;'>โซน</th>
						<th width='10%' style='text-align:center;'>วันที่</th><th width='10%' style='text-align:center;'>พนักงาน</th><th width='10%' style='text-align:center;'>การกระทำ</th>
					</thead>";	
					$sql = dbQuery("SELECT id_return_order_detail FROM tbl_return_order_detail WHERE id_return_order = $id_return_order ORDER BY date_add DESC");
					$row = dbNumRows($sql);
					if($row>0){
						$i = 0;
						$n =$row;
						$total = 0;
						$table = "";
						$total_row = "";
						while($i<$row){
							list($id_return_order_detail) = dbFetchArray($sql);
							//เรียกใช้ method return_order_detail จาก class return_order ที่สร้าง object ไว้ข้างบน
							$ro->return_order_detail($id_return_order_detail);
							$product = new product();
							$id_product = $product->getProductId($ro->id_product_attribute);
							$product->product_detail($id_product);
							$product->product_attribute_detail($ro->id_product_attribute);
							$zone = get_zone($ro->id_zone);
							$id_warehouse = get_warehouse_by_zone($ro->id_zone);
							$warehouse = get_warehouse_name_by_id($id_warehouse);
							$detail_add = thaiDate($ro->detail_date_add);
							$em = new employee($ro->id_employee);
							$employee =  $em->first_name;
							$status = $ro->detail_status;
							if($status == 0){ $table .="<tr style='color:red;'>"; }else{ $table .= "<tr>";} $table .= "
						<td align='center'>$n</td><td>".$product->reference."</td><td>".number_format($ro->qty)."</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>$detail_add</td><td align='center'>$employee</td><td align='center'>"; if($status==1){ $table .="<a href='controller/storeController.php?delete_stocked=y&id_return_order_detail=$id_return_order_detail'>";}else if($status==0){ $table .="<a href='controller/storeController.php?delete=y&id_return_order_detail=$id_return_order_detail'>";} $table .="<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ".$product->reference." ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td></tr>";	
						$total = $total + $ro->qty;				
	$i++;
	$n--;
						}
						$total_row .="<tr><td colspan='8' align='center'><h4>รวม ".number_format($total)." หน่วย</h4></td><tr>";
						$total_row .= $table;
						echo $total_row;
						}else{
							echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
						}
					echo"</table>";
				}
	//********************************************* จบหน้าแก้ไข ******************************************//
	
		}else if(isset($_GET['view_detail'])&&isset($_GET['id_return_order'])){
//******************************** หน้ารายละเอียดการรับสินค้าเข้า ***********************//
echo"	<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>
					<input type='hidden' name='id_return_order' id='id_return_order' value='$id_return_order' />
					<input type='text' class='form-control' name='reference' id='reference'  value='$reference' disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>ลูกค้า</span>
				 <input type='text' class='form-control'  name='customer_name' id='customer_name' value='$customer_name' disabled='disabled' />
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เหตุผล</span>
				 	<select name='id_reason' id='id_reason' class='form-control' disabled='disabled' >"; select_return_reason($id_return_reason); echo"</select>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>
						<input type='text' class='form-control'  name='date' id='date' value='$date_add' disabled='disabled' />
				</div>
			</div>"; if($ro->status == 1){ echo"
			<div class='col-xs-1'>
				<p class='pull-right'><a href='controller/storeController.php?print&id_return_order=$id_return_order' ><span class='glyphicon glyphicon-print' style='color:#5cb85c; font-size:30px;'></span></a></p>
			</div>"; } echo"
         </div>
		 <div class='row' style='margin-top:15px;'>
		 	<div class='col-xs-9'>
				<div class='input-group'>
					<span class='input-group-addon'>หมายเหตุ</span>
					<input type='text' class='form-control'  name='remark' id='remark' value='$remark' disabled='disabled' />
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>พนักงาน</span>
					<input type='text' class='form-control'   value='$employee_name' disabled='disabled' />
				</div>
			</div>
		</div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='10%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='20%' style='text-align:center;'>โซน</th>
						<th width='15%' style='text-align:center;'>วันที่</th>
					</thead>";	
					$sql = dbQuery("SELECT id_return_order_detail, id_product_attribute, qty, id_zone, date_add FROM tbl_return_order_detail WHERE id_return_order = '$id_return_order'");
					$row = dbNumRows($sql);
					if($row>0){
						$i = 0;
						$n = 1;
						while($i<$row){
							list($id_order_return, $id_product_attribute, $qty, $id_zone, $date_add) = dbFetchArray($sql);
							$product = new product();
							$id_product = $product->getProductId($id_product_attribute);
							$product->product_detail($id_product);
							$product->product_attribute_detail($id_product_attribute);
							$id_warehouse = get_warehouse_by_zone($id_zone);
							$warehouse_name = get_warehouse_name_by_id($id_warehouse);
							$zone_name = get_zone($id_zone);						
							echo"<tr><td align='center'>$n</td><td>".$product->reference."</td><td align='center'>$qty</td><td align='center'>$warehouse_name</td>
							<td align='center'>$zone_name</td><td align='center'>".thaiDate($date_add)."</td></tr>";
							$i++; $n++;
						}
					}else{
						echo"<tr><td colspan='6' align='center'><h3 style='text-align:center;'>ยังไม่มีรายการ</h3></td></tr>";
					}
					echo"</table>";
			

//******************************** จบหน้ารายละเอียด ************************************//

}else{
		echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-xs-2 col-xs-offset-4'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-xs-2 '>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<div class='row'>
<div class='col-xs-12'>
	<table class='table table-striped table-hover'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:10%; text-align:center;'>ลำดับ</th><th style='width:20%;'>รายการ</th><th style='width:20%;'>อ้างอิง</th>
            <th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>วันที่</th>
			<th style='width:10%; text-align:center;'>พนักงาน</th><th style='width:10%; text-align:center;'>สถานะ</th>
			<th colspan='2' style='width:10%; text-align:center;'>การกระทำ</th>
        </thead>";
		$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
					$view = "month";
				}
		}
		$result = return_order_table($view,$from, $to);
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		if($row<1){ echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}else{
		while($i<$row){
			list($id_return_order, $reference, $id_customer, $id_employee, $date_add, $status) = dbFetchArray($result);
			list($totay_return_qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_return_order_detail WHERE id_return_order = '$id_return_order'"));
			list($first_name,$last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = $id_customer"));
			list($first,$last) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_employee WHERE id_employee = $id_employee"));
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">$n</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">$reference</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">$first_name $last_name</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">"; 
			if($totay_return_qty<1){ echo"0";}else{ echo $totay_return_qty; } echo"</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">"; echo thaiDate($date_add); echo"</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">$first $last</td>";
			 if($status == 1){echo" 
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">บันทึกแล้ว</td>";
			}else{ echo"
			<td align='center' style='cursor:pointer; color: red;' onclick=\"document.location='index.php?content=order_return&view_detail=y&id_return_order=$id_return_order'\">ยังไม่บันทึก</td>";
			} echo"
			<td align='center'>
				<a href='index.php?content=order_return&edit=y&id_return_order=$id_return_order' $can_edit>
					<button class='btn btn-warning btn-sx'>
						<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			<td align='center'>
				<a href='controller/storeController.php?delete=y&id_return_order=$id_return_order' $can_delete>
					<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference ? ');\">
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			</tr>";
			$i++;
			$n++;
		}
		echo"       
    </table>
</div> </div>";
}
}
?>	
</div>
<script language="javascript">  
$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			var id_customer = arr[2];
			$("#id_customer").val(id_customer);
			$(this).val(name);
		}
	});			
});

$(document).load(function(e) {
  var ss = $("#barcode_zone").val();
	if(ss != ""){
		$("#barcode_item").focus();
	}  
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
  $(function() {
    $("#date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
   function validate() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="เลือกวัน"){	
		alert("คุณยังไม่ได้เลือกช่วงเวลา");
		}else if(to_date ==""){
		alert("คุณยังไม่ได้เลือกวันสุดท้าย");	
	}else{
		$("#form").submit();
	}
}	
$("#barcode_zone").focus(); /// โฟกัสที่ช่องบาร์โค้ดโซน
//// เมื่อยิงบาร์โค้ด หรือ ใส่รหัสด้วยมือแล้ว enter////
///************  บาร์โค้ดโซน **********************//
$("#barcode_zone").bind("enterKey",function(){
	if($("#barcode_zone").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#barcode_zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///***************** รหัสโซน *********************///
$("#zone_name").bind("enterKey",function(){
	if($("#zone_name").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#zone_name").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///********************* จำนวนสินค้า ***********************///
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///********************* รหัสสินค้า ***********************///
$("#barcode_item").bind("enterKey",function(){
	if($("#barcode_item").val() != ""){
	$("#ok").click();
	}
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
//****************ตรวจสอบรายการ*****************************//
function submit_detail(){
	var barcode_zone = $("#barcode_zone").val();
	var zone_name = $("#zone_name").val();
	var qty = $("#qty").val();
	var barcode_item = $("#barcode_item").val();
	if(barcode_zone ==""){
		if(zone_name==""){  
		alert("ต้องระบุบาร์โค้ดโซน หรือ ชื่อโซน อย่างน้อย 1 อย่าง"); 
		}else if(qty==""){
			alert("ยังไม่ได้ใส่จำนวน");
		}else if(qty<=0){
			alert("จำนวนที่จะรับเข้าต้องมากกว่า 0 ");
		}else if(barcode_item ==""){
			alert("อย่าลืมยิงบาร์โค้ดสินค้า");
		}else{
			$("#detail_form").submit();
		}
	}else if(qty==""){
			alert("ยังไม่ได้ใส่จำนวน");
		}else if(qty<=0){
			alert("จำนวนที่จะรับเข้าต้องมากกว่า 0 ");
		}else if(barcode_item ==""){
			alert("อย่าลืมยิงบาร์โค้ดสินค้า");
		}else{
			$("#detail_form").submit();
	}
}
function submit_stock(){
	$("#order_return_form").submit();
}
function edit_stock(){
	$("#edit_return_order_form").submit();
}
function reset_zone(){
	var barcode_val = "";
	var id_zone_val = "";
	var zone_name_val = "";
	$("#barcode_zone").val(barcode_val);
	$("#id_zone").val(id_zone_val);
	$("#zone_name").val(zone_name_val);
	$("#barcode_zone").removeAttr("disabled");
	$("#zone_name").removeAttr("disabled");
	$("#barcode_zone").focus();
}
//// เปลี่ยนโซน///
$(document).bind("F2",function(){
	$("#change_zone").click();
});
$(document).keyup(function(e){
	if(e.keyCode == 113)
	{
		$(this).trigger("F2");
	}
});

$("#edit_btn").click(function(e){
	$(this).css("display","none");
	$("#update_btn").css("display","");
	$("#customer_name").removeAttr("disabled");
	$("#id_reason").removeAttr("disabled");
	$("#date").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
});

$("#update_btn").click(function(e){
	var id_customer = $("#id_customer").val();
	var id_return_order = $("#id_return_order").val(); 
	var id_reason = $("#id_reason").val();
	var date = $("#date").val();
	var remark = $("#remark").val();
	if(id_return_order == ""){
		alert("ไม่พบ id_return_order กด F5 แล้วลองอีกครั้ง");
		return;
	}else if(id_customer==""){
		alert("ไม่พบ id_customer เลือกลูกค้าใหม่อีกครั้ง");
		return;
	}else if(date ==""){
		alert("วันที่ ไม่สามารถเว้นว่างได้");
		return;
	}
	$.ajax({
		url:"controller/storeController.php?edit&id_return="+id_return_order+"&id_customer="+id_customer+"&id_reason="+id_reason+"&date="+date+"&remark="+remark, type:"GET", cache:false,
		success: function(data){
			arr = data.split(":");
			if(arr[0] ==1){
			$("#update_btn").css("display","none");
			$("#edit_btn").css("display","");
			$("#customer_name").attr("disabled","disabled");
			$("#id_reason").attr("disabled","disabled");
			$("#date").attr("disabled","disabled");
			$("#remark").attr("disabled","disabled");	
			}else{
				alert(arr[1]);
			}
		}
	});
});
   </script>
