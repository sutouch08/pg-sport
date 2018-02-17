<?php 
	$page_menu = "invent_order";
	$page_name = "Request Products";
	$id_tab = 14;
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
	
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_request_order'])){
		   $id_request_order = $_GET['id_request_order'];
		    echo"
		   <li><a href='index.php?content=request' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li $can_edit ><a href='controller/orderController.php?save_request_order&id_request_order=$id_request_order' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
				 echo "<li><a href='index.php?content=request' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			 if(isset($_GET['id_request_order'])){$id_request_order = $_GET['id_request_order'];
		  echo"	<li><a href='controller/orderController.php?save_request_order&id_request_order=$id_request_order' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";}
	  		}else if(isset($_GET['view_detail'])){
				echo "<li><a href='index.php?content=request' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			}else{
		   echo"
		   <li $can_add><a href='index.php?content=request&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['missing'])){
	$missing = $_GET['missing'];
	echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$missing</div>";
}
if(isset($_GET['message1'])){
	$message1 = $_GET['message1'];
echo "<div class='alert alert-warning' role='alert'>$message1</div>";
}

//*********************************************** เพิ่มการร้องขอใหม่ ********************************************************// 
if(isset($_GET['add'])){ 
$id_employee = $_COOKIE['user_id'];
if(isset($_GET['id_request_order'])){ 
	$id_request_order = $_GET['id_request_order'];
	$sql = dbQuery("SELECT reference, id_customer, id_employee, date_upd FROM tbl_request_order WHERE id_request_order = $id_request_order");
	$rs = dbFetchArray($sql);
	$active = "disabled='disabled'"; 
	$add = "style='display:none;'";
	$edit = "";
	$reference = $rs['reference'];
	$customer = new customer($rs['id_customer']);
	$id_customer = $customer->id_customer;
	$customer_name = $customer->full_name; 
}else{ 
	$id_request_order="";
	$reference = get_max_request_reference("PREFIX_REQUEST_ORDER");
	$active = "";
	$add="";
	$edit = "style='display:none;'";
	$id_customer = "";
	$customer_name = "";
}
echo"<form id='add_order_form' action='controller/orderController.php?add_request=y' method='post'>
	<div class='row'>
		<input type='hidden' name='id_employee' value='$id_employee' />
		<input type='hidden' name='id_request_order' id='id_request_order' value='$id_request_order' />
		<input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
		<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
			<div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> 
		</div> 
		<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
			<div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> 
		</div>
		<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'>
			<div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' class='form-control' value='$customer_name' autocomplete='off' $active/></div> 
		</div>
		<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
			<button class='btn btn-default' type='button' id='add_order' $add $can_add>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button>
			<button class='btn btn-default' type='button' id='edit_order' $edit $can_edit>&nbsp&nbsp;แก้ไข&nbsp;&nbsp</button>
		</div>
	</div>
	</form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($_GET['id_request_order'])){ 
	//*********************************  เริ่ม ORDER GRID ******************************************//
	echo"
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>";
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
	echo"
	</ul>
	</div>
	</div>
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<div class='tab-content'>";	
$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
		 echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div class='product' style='padding:5px;'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code."</a></div>
			  </div></div>";
				$i++;
				$r++;
			}
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}	
	echo"</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>";	
//************************************ จบ ORDER GRID **********************************************//		
echo"			
	<form action='controller/orderController.php?add_to_request_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_request_order' value='$id_request_order'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";
//	order_grid($customer->id_customer, $order->id_request_order);	
	
	echo"<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'><div class='col-lg-12'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='width:15%; text-align:center;'>รูป</th><th style='width:50%;'>สินค้า</th>
			   <th style='width:15%; text-align:center;'>จำนวน</th><th style='width:15%; text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT id_request_order_detail, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumproduct_qty = 0;
	if($row>0){
	while($i<$row){
		list($id_request_order_detail, $id_product_attribute, $qty)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$product->product_detail($product->id_product);
		$barcode = $product->barcode;
		$product_reference = $product->reference;
		$product_name = $product->product_name;
		echo"<tr>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>$qty</td>
		<td style='text-align:center; vertical-align:middle;'><a href='controller/orderController.php?delete=y&id_request_order_detail=$id_request_order_detail'>
				<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_reference : $product_name'); \" >
				<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td></tr>";
				$sumproduct_qty += $qty;
				$i++;
				$n++;
	}
	echo "<tr>
	<td colspan='2'></td><td align='right'><h4>จำนวน</h4></td><td style='text-align:center; vertical-align:middle;'><h4>$sumproduct_qty</h4></td><td><h4>ชิ้น<h4></td>
	</tr>		";
	}else{
		echo"<tr><td colspan='6' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"
		
	</table>	</div>
	";
	
	 }

//*********************************************** จบหน้าเพิ่มออเดอร์ ****************************************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_request_order'])){
//*********************************************** แก้ไขออเดอร์ **************************************************************//
$id_employee = $_COOKIE['user_id'];
if(isset($_GET['id_request_order'])){ 
	$id_request_order = $_GET['id_request_order'];
	$sql = dbQuery("SELECT reference, id_customer, id_employee, date_upd FROM tbl_request_order WHERE id_request_order = $id_request_order");
	$rs = dbFetchArray($sql);
	$active = "disabled='disabled'"; 
	$add = "style='display:none;'";
	$edit = "";
	$reference = $rs['reference'];
	$customer = new customer($rs['id_customer']);
	$id_customer = $customer->id_customer;
	$customer_name = $customer->full_name; 
}else{ 
	$id_request_order="";
	$reference = get_max_request_reference("PREFIX_REQUEST_ORDER");
	$active = "";
	$add="";
	$edit = "style='display:none;'";
	$id_customer = "";
	$customer_name = "";
}
echo"<form id='add_order_form' action='controller/orderController.php?add_request=y' method='post'>
	<div class='row'>
		<input type='hidden' name='id_employee' value='$id_employee' />
		<input type='hidden' name='id_request_order' id='id_request_order' value='$id_request_order' />
		<input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
		<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
			<div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> 
		</div> 
		<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
			<div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> 
		</div>
		<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'>
			<div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' class='form-control' value='$customer_name' autocomplete='off' $active/></div> 
		</div>
		<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
			<button class='btn btn-default' type='button' id='add_order' $add $can_add>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button>
			<button class='btn btn-default' type='button' id='edit_order' $edit $can_edit>&nbsp&nbsp;แก้ไข&nbsp;&nbsp</button>
		</div>
	</div>
	</form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($_GET['id_request_order'])){ 
	//*********************************  เริ่ม ORDER GRID ******************************************//
	echo"
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>";
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
	echo"
	</ul>
	</div>
	</div>
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<div class='tab-content'>";	
$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
		 echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div class='product' style='padding:5px;'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code."</a></div>
			  </div></div>";
				$i++;
				$r++;
			}
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}	
	echo"</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>";	
//************************************ จบ ORDER GRID **********************************************//		
echo"			
	<form action='controller/orderController.php?add_to_request_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_request_order' value='$id_request_order'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";
	
	echo"<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'><div class='col-lg-12'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='width:15%; text-align:center;'>รูป</th><th style='width:50%;'>สินค้า</th>
			   <th style='width:15%; text-align:center;'>จำนวน</th><th style='width:15%; text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT id_request_order_detail, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumproduct_qty = 0;
	if($row>0){
	while($i<$row){
		list($id_request_order_detail, $id_product_attribute, $qty)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$product->product_detail($product->id_product);
		$barcode = $product->barcode;
		$product_reference = $product->reference;
		$product_name = $product->product_name;
		echo"<tr>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>$qty</td>
		<td style='text-align:center; vertical-align:middle;'><a href='controller/orderController.php?delete=y&id_request_order_detail=$id_request_order_detail'>
				<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_reference : $product_name'); \" >
				<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td></tr>";
				$sumproduct_qty += $qty;
				$i++;
				$n++;
	}
	echo "<tr>
	<td colspan='2'></td><td align='right'><h4>จำนวน</h4></td><td style='text-align:center; vertical-align:middle;'><h4>$sumproduct_qty</h4></td><td><h4>ชิ้น<h4></td>
	</tr>		";
	}else{
		echo"<tr><td colspan='6' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"
		
	</table>	</div>
	";
	
	 }
//*********************************************** จบหน้าแก้ไข ****************************************************//

}else if(isset($_GET['view_detail'])){
//***********************************************  หน้าดูรายละเอียด  **********************************************//	
	$id_employee = $_COOKIE['user_id'];
if(isset($_GET['id_request_order'])){ 
	$id_request_order = $_GET['id_request_order'];
	$sql = dbQuery("SELECT reference, id_customer, id_employee, date_upd FROM tbl_request_order WHERE id_request_order = $id_request_order");
	$rs = dbFetchArray($sql);
	$active = "disabled='disabled'"; 
	$add = "style='display:none;'";
	$edit = "";
	$reference = $rs['reference'];
	$date_upd = thaiDate($rs['date_upd']);
	$customer = new customer($rs['id_customer']);
	$id_customer = $customer->id_customer;
	$customer_name = $customer->full_name; 
}else{ 
	$id_request_order="";
	$reference = get_max_request_reference("PREFIX_REQUEST_ORDER");
	$active = "";
	$add="";
	$edit = "style='display:none;'";
	$id_customer = "";
	$customer_name = "";
	$date_upd = thaiDate(date('Y-m-d'));
}
echo"
	<div class='row'>
		<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
		เลขที่เอกสาร :  $reference 
		</div> 
		<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
			วันที่ : $date_upd
		</div>
		<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'>
			ลูกค้า : $customer_name
		</div>
	</div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'><div class='col-lg-12'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='width:10%; text-align:center;'>รูป</th><th style='width:75%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>จำนวน</th>
	</thead>";
	$sql = dbQuery("SELECT id_request_order_detail, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumproduct_qty = 0;
	if($row>0){
	while($i<$row){
		list($id_request_order_detail, $id_product_attribute, $qty)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$product->product_detail($product->id_product);
		$barcode = $product->barcode;
		$product_reference = $product->reference;
		$product_name = $product->product_name;
		echo"<tr>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>$qty</td>
		</tr>";
				$sumproduct_qty += $qty;
				$i++;
				$n++;
	}
	echo "<tr>
	<td colspan='4' align='right'><h4>จำนวน $sumproduct_qty ชิ้น<h4></td>
	</tr>		";
	}else{
		echo"<tr><td colspan='4' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"
		
	</table>	</div>
	";
	
}else{
//************************************************ แสดงรายการ *************************************************//
$paginator = new paginator();
echo"<form  method='post' id='form'>
		<div class='row'>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<a href='index.php?content=request'><button type='button' class='btn btn-default'><i class='fa fa-refresh'></i></button></a>
			</div>
         </div>
				</form>
		<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
		if(isset($_POST['from_date'])){	$from = dbDate($_POST['from_date']); }else{ $from = "";} if(isset($_POST['to_date'])){  $to = dbDate($_POST['to_date']); }else{ $to = "";}
		if($from==""){
			if($to==""){ $view = getConfig("VIEW_ORDER_IN_DAYS"); 	$where = ""; }
		}else{
			$where = "WHERE date_upd BETWEEN '$from 00:00:00' AND '$to 23:59:59' "; 
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_request_order","$where ORDER BY id_request_order DESC",$get_rows);
echo "<div class='row' id='result'>	
		<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>";
		$paginator->display($get_rows,"index.php?content=request");
echo"	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>วันที่</th><th style='width:10%;'>พนักงาน</th>
			<th style='width:10%;'>สถานะ</th><th colspan='2' style='width:10%; text-align:center;'>การกระทำ</th>
        </thead>";
		$result = dbQuery("SELECT id_request_order, reference, id_customer, id_employee, date_upd, status FROM tbl_request_order $where ORDER BY id_request_order DESC LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);
		$i=0;
		$row = dbNumRows($result);
		if($row>0){ 
		while($i<$row){
			list($id_request_order, $reference, $id_customer, $id_employee, $date_upd, $status)=dbFetchArray($result);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
			if($status == 0){ $saved = "<span style='color:red;'>ยังไม่บันทึก</span>"; }else{ $saved = "บันทึกแล้ว"; }
	echo"<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">$id_request_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">$cus_first_name &nbsp; $cus_last_name</td>
				
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">$employee_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=request&id_request_order=$id_request_order&view_detail=y'\">$saved</td>
				<td>
					<a href='index.php?content=request&edit=y&id_request_order=$id_request_order' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>	</button></a>
				</td>
				<td>
				<a href='controller/orderController.php?delete_request=y&id_request_order=$id_request_order' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference ? ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='6' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
		
}
?>
</div>
<script language="javascript">  
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

$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(':');
			var id = arr[2];
			var name = arr[1];
			$("#id_customer").val(id);
			$(this).val(name);
		}
	});			
});

$(document).ready(function(e) {
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});

$(document).ready(function(e) {
    if($("#error").length){
		alert($("#error").text());
	}
});

function get_row(){
	$("#rows").submit();
}
function getData(id_product){
	var id_cus = $("#id_customer").val();
	$.ajax({
		url:"controller/orderController.php?get_request_data&id_product="+id_product+"&id_customer="+id_cus,
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
$("#add_order").click(function(e) {
    var date = $("#doc_date").val();
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else{
		$("#add_order_form").submit();
	}
});
$("#edit_order").click(function(e) {
    $("#doc_date").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#payment").removeAttr("disabled");
	$("#comment").removeAttr("disabled");
	$("#add_order").text("บันทึก");
	$(this).css("display", "none");
	$("#add_order").css("display","");
});
</script>