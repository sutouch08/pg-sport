<?php 
	$page_menu = "invent_product_out";
	$page_name = $pageTitle;
	$id_tab = 8;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$ps = checkAccess($id_profile, 37);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	if( $pm['add'] || $pm['edit'] ){ $return = 1; }else{ $return = 0; }
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$role = 3; ///--------------- เบิกสินค้า
	if(isset($_GET['edit']) && isset($_GET['id_order']) ){
		$id_order = $_GET['id_order'];
		$order= new order($id_order);
		$btn = "<a href='index.php?content=lend'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		if($order->valid ==1 || $order->current_state !=1 && $order->current_state !=3){ 
			
		}else{
			$btn .= can_do($edit, "&nbsp;<a href='index.php?content=lend&add=y&id_order=$id_order'><button type='button' class='btn btn-info'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button></a>");
		}
	}else if( isset($_GET['edit']) || isset($_GET['add']) ){
		$btn = "<a href='index.php?content=lend'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		if(isset($_GET['id_order'])){
			$id_order = $_GET['id_order'];
			$btn .= can_do($add, "<a href='controller/lendController.php?save_order&id_order=$id_order'><button typ='button' class='btn btn-success'><i class='fa fa-save'></i>&nbsp; บันทึก</button></a>");
		}	
	}else if(isset($_GET['return_detail'])){
		$btn = "<a href='index.php?content=lend&return=y'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";			
		$btn .= can_do($return,"&nbsp;<a href='#' onclick='return_detail()'><button typ='button' class='btn btn-success'><i class='fa fa-save'></i>&nbsp; บันทึกรับคืน</button></a>");	
	}else{
		if(isset($_GET['return'])){
			$btn = "<a href='index.php?content=lend'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		}else{
			$btn = can_do($add, "<a href='controller/lendController.php?check_add'><button typ='button' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; ยืมสินค้า</button></a>");
			$btn .= can_do($return, "&nbsp;<a href='index.php?content=lend&return=y'><button typ='button' class='btn btn-primary'><i class='fa fa-retweet'></i>&nbsp; คืนสินค้า</button></a>");
		}		
	}
	
	
	
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-xs-6">
    	<h3 class="title"><i class="fa fa-archive"></i>&nbsp;<?php if(isset($_GET['return'])){echo "คืนสินค้า";}else{echo $page_name;} ?></h3>
	</div>
    <div class="col-xs-6">
    	<p class="pull-right"><?php echo $btn; ?></p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
//----------------------------------------------เพิ่ม-----------------------------------------//
if(isset($_GET['add'])){ 
$new_ref = get_max_role_reference("PREFIX_LEND",$role);
$user_id = $_COOKIE['user_id'];
if(isset($_GET['id_order'])){ 
$id_order = $_GET['id_order'];
$active = "disabled='disabled'"; 
$order = new order($id_order);
$employee = new employee($order->id_employee);
$id_employee = $employee->id_employee;
$employee_name = $employee->full_name; 
$comment = $order->comment;
$payment = $order->payment;
}else{ 
	$active = "";
	$id_employee = "";
	$employee_name = "";
	$comment = "";
	$payment = "credit";
}
echo"<form id='add_order_form' action='controller/lendController.php?add=y' method='post'>
	<input type='hidden' name='role' value='$role' >
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$new_ref' disabled='disabled'/></div> </div> 
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> </div>
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>ผู้ยืม</span><input type='text' id='employee_name' class='form-control' value='$employee_name' autocomplete='off' $active/></div> </div>
	</div>
	<div class='row' style='margin-top:15px;'><input type='hidden' name='employee' id='id_employee' value='$id_employee' />
	<div class='col-xs-6'><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div>
	<div class='col-xs-3'></div> </div>
	<div class='col-xs-2'><button class='btn btn-default' type='button' id='add_order' $active>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($_GET['id_order'])){ 
		echo"<form id='add_detail_form' action='controller/lendController.php?add&insert_detail' method='post'>
		<input type='hidden' name='role' value='$role' >
			<div class='row'><input type='hidden' name='id_order' id='id_order' value='$id_order' />
			<input type='hidden' name='stock_qty' id='stock_qty' /><input name='id_product_attribute' id='id_product_attribute' type='hidden' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode' class='form-control' autocomplete='off' autofocus /></div> </div> 
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>สินค้า</span><input type='text' id='product_code' class='form-control' autocomplete='off' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>ในสต็อก</span><input type='text' id='stock_label' class='form-control' disabled='disabled' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' autocomplete='off' autofocus /></div> </div>
	<div class='col-xs-1'><button class='btn btn-default' type='button' id='add_detail' onclick='submit_detail()'>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
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
//0จบ
echo"			
	<form action='controller/lendController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_order' value='$id_order'/>
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
	<div class='row'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='text-align:center;'>รูป</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>มูลค่า</th><th style='text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
	while($i<$row){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr><td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle;'>$discount</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($total_amount,2)."</td>
		<td style='text-align:center; vertical-align:middle;'><a href='controller/lendController.php?delete=y&id_order_detail=$id_order_detail' >
				<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_reference : $product_name'); \" >
				<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"
				
	</table>	
	";
	
	 }
}else if(isset($_GET['edit'])&&isset($_GET['id_order'])){
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	//echo"<form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>";
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = new employee($order->id_employee);
	$state = $order->orderState();
	echo"		
        <div class='row'>
        	<div class='col-xs-12'><h4>".$order->reference." - ยืมสินค้า<p class='pull-right'>ผู้ยืม : &nbsp;".$employee->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-xs-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่เบิก : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".date("d-m-Y H:i:s", strtotime($order->date_add))."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
		<p class='pull-right'>
			<button type='button' class='btn btn-success btn-sm' onclick='print_order($id_order)'><i class='fa fa-print'></i>&nbsp; พิมพ์</button>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'><form id='state_change' action='controller/lendController.php?edit&state_change' method='post'>
		<div class='col-xs-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><tr><input type='hidden' name='id_order' value='".$order->id_order."' /><input type='hidden' name='id_employee' value='$id_employee' />
		<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td><td style='width:40%; padding-right:10px;'><select name='order_state' id='order_state' class='form-control input-sm'>"; orderStateList(); echo"</select></td><td style='padding-right:10px;'><button class='btn btn-default' type='button' onclick='state_change()'>เพิ่ม</button></td></tr>";
		$row = dbNumRows($state);
		$i=0;
		if($row>0){
			while($i<$row){
				list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state);
			echo"
			<tr  style='background-color:".state_color($id_order_state).";'><td style='padding-top:10px; padding-bottom:10px; text-align:center;'>$state_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center;'>$first_name  $last_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".date('d-m-Y H:i:s', strtotime($date_add))."</td></tr>";
			$i++;
			}
		}else{
		echo"<tr><td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".$order->currentState()."</td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".date('d-m-Y H:i:s', strtotime($order->date_upd))."</td></tr>";
		}
		echo"
		</table></div></form><div class='col-xs-6'>";
		
		echo "</div><!--col --></div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
		<form id='edit_order_form' action='controller/lendController.php?edit_order&add_detail' method='post' autocomplete='off'>
		<div class='row'>"; echo $order->orderProductTable(); echo "</div><div class='row'><p><h4>ข้อความ :  "; echo $order->comment; echo "</h4></p></div><h4></h4></form>";
//*********************************************** จบหน้าแก้ไข ****************************************************//
}else if(isset($_GET['return'])){
//*************************************************คืนสินค้า*******************************************************//
	$active = "";
	$id_employee = "";
	$employee_name = "";
	$comment = "";
	$payment = "credit";
	if(isset($_POST['doc_date'])){
		$reference = $_POST['reference'];
		$employee_name = $_POST['employee_name'];
		if($_POST['doc_date'] == ""){
			$doc_date = "";
		}else{
			$doc_date = dbDate($_POST['doc_date']);
		}
		}else{
			$doc_date = "";
			$reference = "";
			$employee_name = "";
		}
	echo"<form id='add_order_form' action='index.php?content=lend&return=y' method='post'>
	<div class='row'>
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='reference' name='reference' class='form-control' value='$reference' /></div> </div> 
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date1' name='doc_date' class='form-control' value='$doc_date' $active/></div> </div>
	<div class='col-xs-4'></div>
	</div>
	<div class='row' style='margin-top:15px;'><input type='hidden' name='employee' id='id_employee' value='$id_employee' />
	
	<input type='hidden' name='stock_qty' id='stock_qty' /><input name='id_product_attribute' id='id_product_attribute' type='hidden' />
		<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>ผู้ยืม</span><input type='text' id='employee_name1' name='employee_name' class='form-control' value='$employee_name' autocomplete='off' $active/></div> </div>
	<div class='col-xs-2'><button class='btn btn-default' type='button' id='add_order' $active>&nbsp&nbsp;ค้นหา&nbsp;&nbsp</button></div>
	</div></form>
	<br>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
	<div class='col-xs-12'>
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
            <th style='width:10%;'>ผู้ยืม</th><th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th><th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		if(isset($_POST['doc_date'])){
		$result = dbQuery("SELECT id,reference,cus_first_name,cus_last_name,employee_name,amount,payment,current_state,status,valid,date_add,date_upd FROM order_table WHERE date_add LIKE '%$doc_date%' AND reference LIKE '%$reference%' AND employee_name LIKE '%$employee_name%' AND role IN(3) ORDER BY date_add DESC");
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference, $cus_first_name, $cus_last_name, $employee_name, $amount, $payment, $current_state, $status, $valid, $date_add, $date_upd)=dbFetchArray($result);
	echo "<tr style='color:#FFF; background-color:".state_color($current_state).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการที่ค้นหา</h3></td></tr>";
		}
		}else{
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;กรุณาใส่ข้อมูลที่ต้องการค้นหา</h3></td></tr>";
		}
		echo"</table>";
echo "</div>";
//**************************************************จบคืนสินค้า***********************************************//
}else if(isset($_GET['return_detail'])){
//************************************************รายระเอียดสินค้าที่ยืม******************************************//
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = new employee($order->id_employee);
	$state = $order->orderState();
	echo"		
        <div class='row'>
        	<div class='col-xs-12'><h4>".$order->reference." - ยืมสินค้า<p class='pull-right'>ผู้ยืม : &nbsp;".$employee->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-xs-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่เบิก : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
		</div></div>
		
		<div class='row'><div class='col-xs-6'>";
		echo "</div><!--col --></div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
		<form id='return_product' action='controller/lendController.php?return_product' method='post' autocomplete='off'>
		<input type='hidden' name='id_order' value='$id_order'>
		<input type='hidden' name='id_employee' value='$user_id'>
		<input type='hidden' name='reference' value='".$order->reference."' >
		<div class='row'>";
		$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order");
		$row = dbNumRows($sql);
		echo"
		<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><thead><th style='width:10%'>รูปภาพ</th><th style='width:45%'>สินค้า</th><th style='width:15%; text-align:center;'>จำนวนที่ยืม</th><th style='width:15%; text-align:center;'>จำนวนที่คืนแล้ว</th><th style='width:10%; text-align:center;'>คืน</th></thead>";
		if($row>0){
			$discount ="";
			$amount = "";
			$total_amount = "";
			$sumreturn = 0;
			while($i = dbFetchArray($sql)){
				$product = new product();
				$id_product_attribute = $i['id_product_attribute'];
				$product_qty = $i['product_qty'];
				list($return) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute and status = 5"));
				$return1 =$product_qty - $return;
				$sumreturn = $sumreturn + $return;
				echo"<tr>
				<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /><input type='hidden' name='id_product_attribute[]' value='".$i['id_product_attribute']."' ></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']." : ".$i['barcode'].$order->valid."</td>
				<td style='text-align:center; vertical-align:middle;'><p id='qty".$i['id_order'].$i['id_product_attribute']."'>".number_format($i['product_qty'])."</p></td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($return)."</td>
				<td style='text-align:center; vertical-align:middle;'><input type='text' id='qty$id_product_attribute' name='qty[]' class='form-control' value='$return1' /></td>
				</tr>";
			?>
            					<script>
									$(document).ready(function(){
										$("#qty<?php echo $id_product_attribute;?>").keyup(function(){
											var product_qty = <?php echo $product_qty?>;
											var return_product = $('#qty<?php echo $id_product_attribute;?>').val();
											var return1 = <?php echo $return1;?>;
											if(parseInt(return_product)>parseInt(return1)){
												alert('ใส่จำนวนสินค้าที่คืนเกิน');
												$("#qty<?php echo $id_product_attribute;?>").val(return1);
											}
										});
									}); 
								</script>
            <?php
			}echo" <tr><td rowspan='4' colspan='2'></td>
			<tr>
			<td style='border-left:1px solid #ccc' colspan='2' >&nbsp;&nbsp;&nbsp;&nbsp;คืนแล้ว</td><td align='right'>".number_format($sumreturn)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='border-left:1px solid #ccc' colspan='2' >&nbsp;&nbsp;&nbsp;&nbsp;ยังไม่ได้คืน</td><td align='right'>".number_format($order->total_qty-$sumreturn)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='border-left:1px solid #ccc' colspan='2' ><h4>&nbsp;&nbsp;&nbsp;จำนวนทั้งหมด</h4></td><td align='right'><h4>".number_format($order->total_qty)."&nbsp;&nbsp;&nbsp;&nbsp;</h4></td>
			</tr>
			</table>";		
		}else{
			echo" <tr><td colspan='5' align='center'><h4>ไม่มีรายการสินค้า</h4></td></tr></table>";
		}
		echo "</div><div class='row'><p><h4>ข้อความ :  "; echo $order->comment; echo "</h4></p></div><h4></h4></form>";
//***********************************************จบรายระเอียดสินค้าที่ยืม*****************************************//
}else{
//************************************************ แสดงรายการ **********************************************//
$paginator = new paginator();
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
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
				$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = "week";
			}
		}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_order","WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND current_state !=1 AND role IN($role) ORDER BY id_order DESC",$get_rows);
		$paginator->display($get_rows,"index.php?content=lend");
echo "<div class='row'>
<div class='col-xs-12'>
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
            <th style='width:10%;'>ผู้ยืม</th><th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th><th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		
			if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		$result = dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd,order_status FROM tbl_order WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND current_state !=1 AND role IN($role) ORDER BY id_order DESC LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference,$id_customer,$id_employee,  $payment,   $date_add,$current_state,$date_upd,$order_status)=dbFetchArray($result);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
	if($order_status == 0 ){
				$location = "content=lend&add=y&id_order=$id_order";
			}else{
				$location = "content=lend&edit=y&id_order=$id_order&view_detail=y";
			}
	echo "<tr ";if($order_status == 0 ){}else{echo "style='color:#FFF;background-color:".state_color($current_state).";'";}echo ">
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">";if($order_status == 0 ){echo "ยังไม่ได้บันทึก";}else{echo $status;}echo "</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?$location'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
}
?>
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
	function state_change(){
		var state = $("#order_state").val();
		if(state == 0){
			alert("ยังไม่ได้เลือกสถานะ");
		}else{
			$("#state_change").submit();
		}
	}
$("#add_product").click(function() {
    $("#new_row").css("display","");
	var id_cus = $("#id_customer").val();
	$("#id_cus").val(id_cus);
});
$(document).ready(function(e) {
	$("#product").autocomplete(
	{
		 source: "controller/lendController.php?product",
		 close: function(event,ui){
			 var ref = $(this).val();
			var id_cus = $("#id_cus").val();	
		$.ajax({ 
			 url: "controller/lendController.php?reference="+ref+"&id_customer="+id_cus,
			 type: "GET", cache:false, 
			 success: function(data){
				 if(data !=""){ 
				 	var arr = data.split(':');
					var id = arr[0];
					var price = arr[1];
					var stock = arr[2];
					 $("#id_product_attribute").val(id);
					 $("#price").val(price);
					 $("#available").text("คงเหลือ : "+stock);
					 $("#stock_qty").val(stock);
				 }
			 }
		});
    }
	});

});
$(document).ready(function(e) {
    $("#product_code").autocomplete({
		source:"controller/lendController.php?product_code",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#product_code").val();
		}
	});			
});
//// product code
$("#product_code").focusout(function(e) {
    var product_code = $(this).val();
	if(product_code !=""){
	$.ajax({
		url:"controller/lendController.php?check_stock&product_code="+product_code
		,type:"GET",cache:false,success: function(stock_qty){
			if(stock_qty !=""){
				var arr = stock_qty.split(":");
				$.trim($("#id_product_attribute").val(arr[0]));
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
			}
		}
	});
	}
});
//// barcode 
$("#barcode").focusout(function(e) {
    var barcode = $(this).val();
	if(barcode !=""){
	$.ajax({
		url:"controller/lendController.php?check_stock&barcode="+barcode
		,type:"GET",cache:false,success: function(data){
			if(data !=""){
				var arr = data.split(":");
				$("#id_product_attribute").val(arr[0]);
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
				$("#product_code").val(arr[2]);
			}
		}
	});
	}
});
/////
$("#qty").keyup(function(e) {
    var limit = parseInt($("#stock_qty").val());
	var qty = parseInt($("#qty").val());
	if(qty>limit){
		alert("มีสินค้าในสต็อกแค่ "+limit+" ตัวเท่านั้น");
		$("#qty").val(limit);
	}
});
$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
///// ชื่อสินค้า เมื่อกดปุ่ม enter
	$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
$("#product_code").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
/// barcode 
$("#barcode").bind("enterKey",function(){
	if($("#barcode").val() != ""){
		$("#qty").focus();
	}else{
		$("#product_code").focus();
	}
});
$("#barcode").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
		$("#add_detail").click();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$(document).ready(function(e) {
    $("#qty").keyup(function(e) {
        var qty = parseInt($(this).val());
		var stock = parseInt($("#stock_qty").val());
		var price = $("#price").val();
		if(qty>stock){
			alert("มีสินค้าในสต็อกแค่ "+stock);
			$(this).val(stock);
			var total = price * qty;
			$("#total_amount").val(total);
			$("#total").html(total +" ฿");
		}else{
			var total = price * qty;
		$("#total_amount").val(total);
		$("#total").html(total +" ฿");
		}
    });
});
function edit_product(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = parseInt($("#qty"+name).text());
	$("#edit"+name).css("display","none");
	$("#delete"+name).css("display","none");
	$("#update"+name).css("display","");
	$("#edit_qty"+name).val(qty);
	$("#qty"+name).css("display","none");
	$("#edit_qty"+name).css("display","");	
}
function update(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = $("#edit_qty"+name).val();
	var old_qty = parseInt($("#qty"+name).text());
	if(qty<=0){
		alert("จำนวนที่สั่งอย่างน้อย 1 ตัว");
	}else{
	$.ajax({
		url:"controller/lendController.php?check_stock&id_order="+id_order+"&id_product_attribute="+id_product_attribute, 
		cache:false,
		success: function(stock_qty){
			var stock = parseInt(stock_qty);
			if(qty<=old_qty){
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}else if(qty>stock){
				alert("มีสินค้าในสต็อกแค่ "+stock+" เท่านั้น");
				$("#edit_qty"+name).val(stock);
				//$("#new_qty").val(stock);
			}else{
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}
		}
	});
	}
}
//////// เพิ่มรายการสั่งซื้อสินค้าแต่ไม่เปลียนหน้าใหม่ ///
function submit_detail(){
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	}else if(parseInt(order_qty) > parseInt(stock_qty)){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);
	}else{
		$("#add_detail_form").submit();
	}
}
//// เพิ่มรายการสั่งซื้อสินค้าแล้วเปลียนหน้าใหม่ ///
function add_detail(){ 
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	}else if(order_qty>stock_qty){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);
	}else{
		$("#edit_order_form").submit();
	}
}
var no = 0;
//////// เพิ่มรายการสั่งซื้อสินค้าแต่ไม่เปลียนหน้าใหม่ ///
function insert_detail(id_order,id_product_attribute, qty){
	$.ajax({
		url:"controller/lendController.php?insert_detail",
		data: {id_order:id_order, id_product_attribute:id_product_attribute, qty:qty},type:"POST",cache:false,
		success: function(complete){
			if(complete !="error"){
				no = no +1;
				var data = complete.split(":");
				var barcode = data[0];
				var product = data[1];
				var price = data[2];
				var quantity = data[3];
				var discount = data[4];
				var amount = data[5];
				$("#order_detail").append("<tr><td align='center'>"+no+"</td><td>"+barcode+"</td><td>"+product+"</td><td>"+price+"</td><td>"+quantity+"</td><td>"+discount+"</td><td>"+amount+"</td></tr>");
			}else{ 
			alert("เพิ่มข้อมูลไม่ได้");
			}
		}
	});
}
$(document).ready(function(e) {
    if($("#error").length){
		alert($("#error").text());
	}
});
$(document).ready(function(e) {
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});
$(document).ready(function(e) {
    $("#doc_date1").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});
$(document).ready(function(e) {
    $("#employee_name").autocomplete({
		source:"controller/lendController.php?employee_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#employee_name").val();
			var arr = data.split(':');
			var id = arr[0];
			$("#id_employee").val(id);
		}
	});			
});
$(document).ready(function(e) {
    $("#employee_name1").autocomplete({
		source:"controller/lendController.php?employee_name1",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#employee_name1").val();
			var arr = data.split(':');
			var id = arr[0];
			$("#id_employee").val(id);
		}
	});			
});


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
function return_detail(){
			$("#return_product").submit();
	}
	function get_row(){
	$("#rows").submit();
}
function getData(id_product){
	var id_cus = 0;
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

function print_order(id)
{
	var left = ($(document).width() - 800) /2;
	var url = "controller/orderController.php?print_order&id_order="+id;
	window.open(url, "_blank", "width=800, height=900, scrollbars=yes, left="+left);	
}
</script>

<?php
//-------------------------------