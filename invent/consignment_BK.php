<?php 
	$page_name = $pageTitle;
	$id_tab = 16;
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
	$role = 5; /// ฝากขาย 
	 if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
	   	$id_order = $_GET['id_order'];
	 	$order= new order($id_order);
	    $btn = "<a href='index.php?content=consignment' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	   if($order->current_state == 1 || $order->current_state == 3){ 
	 	  	$btn .= can_do($edit, "&nbsp;&nbsp;<a href='index.php?content=consignment&add=y&id_order=$id_order' ><button type='button' class='btn btn-info'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button></a>");
	   }
	}else if(isset($_GET['edit']) || isset($_GET['add'])){
		$btn = "<a href='index.php?content=consignment' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	    if(isset($_GET['id_order'])){
			$id_order = $_GET['id_order'];
			$btn .= can_do($edit, "&nbsp;&nbsp;<a href='controller/consignmentController.php?save_order&id_order=$id_order' ><button type='button' class='btn btn-success' onclick='edit_stock()'><i class='fa fa-save'></i>&nbsp; บันทึก</button></a>");
			}
  	}else{
	   $btn = can_do($add, "<a href='controller/consignmentController.php?check_add' ><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
   }
	   ?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-xs-6"><h3 class="title"><i class="fa fa-could"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
       <p class="pull-right"> <?php echo $btn; ?></p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
	//*********************************************** เพิ่มรายการใหม่ ********************************************************// 
if(isset($_GET['add'])) :
	if(!isset($_GET['id_order'])){ 
			$reference = get_max_role_reference("PREFIX_CONSIGNMENT",$role);
			$user_id = $_COOKIE['user_id'];
			$active = "";
			$id_customer = "";
			$customer_name = "";
			$comment = "";
			$payment = "credit";
			$id_zone = "";
			$doc_date = date("d-m-Y");
		}else{
			$id_order = $_GET['id_order'];
			$active = "disabled"; 
			$order = new order($id_order);
			$reference = $order->reference;
			$customer = new customer($order->id_customer);
			$doc_date = thaiDate($order->date_add);
			$id_customer = $customer->id_customer;
			$customer_name = $customer->full_name; 
			$comment = $order->comment;
			$payment = $order->payment;
			list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_order = $id_order"));
		}
function select_zone_consign($selected=""){
	echo"<option value='0'>-------เลือกพื้นที่--------</option>";
	$sql = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = 2");
	while($rs = dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$zone_name = $rs['zone_name'];
		if($selected==$id_zone){ $select = "selected='selected'";}else{ $select = "";}
		echo"<option value='$id_zone' $select>$zone_name</option>";
	}
}
?>
<!--++++++++++++++++++++++++++  เพิ่มออเดอร์ ID ใหม่  ++++++++++++++++++++++++++++++++-->
<form id='add_order_form' action='controller/consignmentController.php?add=y' method='post'>
	<input type='hidden' name='role' value='<?php echo $role; ?>' >
    <input type='hidden' name='id_employee' value='<?php echo $user_id; ?>' />
	<div class='row'>
	<div class='col-xs-3'>
    	<div class='input-group'>
        	<span class='input-group-addon'>เลขที่เอกสาร</span>
            <input type='text' id='doc_id' class='form-control' value='<?php echo $reference; ?>' disabled='disabled'/>
        </div> 
    </div> 
	<div class='col-xs-2'>
    	<div class='input-group'>
        	<span class='input-group-addon'>วันที่</span>
            <input type='text' id='doc_date' name='doc_date' class='form-control' value='<?php echo $doc_date; ?>' <?php echo $active; ?> />
        </div> 
    </div>
	<div class='col-xs-4'>
    	<div class='input-group'>
        	<span class='input-group-addon'>ชื่อลูกค้า</span>
            <input type='text' id='customer_name' class='form-control' value='<?php echo $customer_name; ?>' autocomplete='off' <?php echo $active; ?> />
        </div> 
    </div>
	<div class='col-xs-3'>
    	<!-- <input type='checkbox' id='auto_zone' name='auto_zone'  /><label for='auto_zone' style='margin-left:10px;'>สร้างโซนอัตโนมัติ</label> -->
    </div>
	</div>
	<div class='row' style='margin-top:15px;'>
    <input type='hidden' name='id_customer' id='id_customer' value='<?php echo $id_customer; ?>' />
	<div class='col-xs-3'>
    	<div class='input-group'>
        	<span class='input-group-addon'>เลือกโซน</span>
			<select name='zone_id' id='zone_id' class='form-control' <?php echo $active; ?>>
			<?php select_zone_consign($id_zone); ?>
			</select>
		</div>
    </div>
	<div class='col-xs-6'>
    	<div class='input-group'>
        	<span class='input-group-addon'>หมายเหตุ</span>
            <input type='text' id='comment' name='comment' class='form-control' value='<?php echo $comment; ?>' autocomplete='off' <?php echo $active; ?> />
        </div> 
    </div>
	<div class='col-xs-2'>
    	<button class='btn btn-default' type='button' id='add_order'  style=" <?php if(isset($_GET['id_order'])){ echo "display:none;"; } ?>" ><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
        <button class="btn btn-warning" type="button" id="btn_edit" style=" <?php if(!isset($_GET['id_order'])){ echo "display:none;"; } ?>" ><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button class="btn btn-success" type="button" id="btn_update" style="display:none;" ><i class="fa fa-save"></i>&nbsp; บันทึก</button>
    </div>
	</div>
    </form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<!----  ได้ออเดอร์ ID แล้ว   ---->
<?php if(isset($_GET['id_order'])) : ?>
	<form id='add_detail_form' action='controller/consignmentController.php?add&insert_detail' method='post'>
		<input type='hidden' name='id_role' value='<?php echo $role; ?>' >
        <input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
        <input type='hidden' name='stock_qty' id='stock_qty' />
        <input name='id_product_attribute' id='id_product_attribute' type='hidden' />
	<div class='row'>		
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode' class='form-control' autocomplete='off' autofocus /></div> </div> 
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>สินค้า</span><input type='text' id='product_code' class='form-control' autocomplete='off' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>ในสต็อก</span><input type='text' id='stock_label' class='form-control' disabled='disabled' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' autocomplete='off' autofocus /></div> </div>
	<div class='col-xs-1'><button class='btn btn-default' type='button' id='add_detail' onclick='submit_detail()'>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp;</button></div>
	</div>
    </form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<!-------- เริ่ม ORDER GRID ----------->
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
<?php		$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
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
?>
	</ul>
	</div>
	</div>
<div class='row'>
<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<div class='tab-content'>
<?php 
	$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)) :
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0) :
			$i=0;
			while($i<$row) :
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);	
				echo
		 		"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
				<div class='product' style='padding:5px;'>
				<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
				<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code." : <span style='color:red'>".$product->available_product_qty($id_product)."</span>"."</a></div>
			  	</div></div>";
				$i++;
				$r++;
			endwhile;
		else :
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		endif;
		echo "</div>";
	endwhile; 
	?>
	</div> 
    <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div>
</div>
<!----------------------------------------- จบ ORDER GRID ---------------------------------->		
	<form action='controller/orderController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    	<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
			  <div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_order' value='<?php echo $id_order; ?>'/>
			   </div>
			  <div class='modal-body' id='modal_body'></div>
			  <div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
			  </div>
			</div>
		  </div>
		</div>
    </form>
	
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th>
                <th style='text-align:center;'>รูป</th>
                <th style='width:15%;'>บาร์โค้ด</th>
                <th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th>
               <th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th>
               <th style='width:10%; text-align:center;'>มูลค่า</th>
               <th style='text-align:center;'>การกระทำ</th>
	</thead>
<?php    
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
		while($i<$row) :
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
			<td style='text-align:center; vertical-align:middle;'><a href='controller/orderController.php?delete=y&id_order_detail=$id_order_detail' >
					<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_reference : $product_name'); \" >
					<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
					</td></tr>";
					$i++;
					$n++;
		endwhile;
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	?>	
	</table>	
	<?php endif; /// if(isset($g['id_order'] /// ?>	
<?php  elseif(isset($_GET['view_detail'])&&isset($_GET['id_order'])) : ?>
<?php 
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	list($zone) =  dbFetchArray(dbQuery("SELECT zone_name FROM tbl_order_consignment JOIN tbl_zone ON tbl_order_consignment.id_zone = tbl_zone.id_zone WHERE id_order = ".$id_order));
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
	$state = $order->orderState();
	?>	
        <div class='row'>
        	<div class='col-xs-12'><strong><?php echo $order->reference." - ".$customer->full_name." - เข้าโซน : ".$zone."<p class='pull-right'>พนักงาน : &nbsp;".$sale->full_name."</p>"; ?></strong></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-xs-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo date("d-m-Y H:i:s", strtotime($order->date_add)); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_amount,2); ?></dd> </dt></dl>
        <p class='pull-right'>
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
        
        <button class="btn btn-success" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
      
        </p>
		</div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
        <form id='state_change' action='controller/consignmentController.php?edit&state_change' method='post'>
        	<input type='hidden' name='id_order' value='<?php echo $order->id_order; ?>' /><input type='hidden' name='id_employee' value='<?php echo $id_employee; ?>' />
		<div class='row'>
		<div class='col-xs-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
        	<tr>
				<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td>
                <td style='width:40%; padding-right:10px;'><select name='order_state' id='order_state' class='form-control input-sm'><?php orderStateList(); ?></select></td>
                <td style='padding-right:10px;'><button class='btn btn-default' type='button' onclick='state_change()'>เพิ่ม</button></td>
           </tr>
<?php $row = dbNumRows($state);
		 $i=0;
		 if($row>0) :
			while($i<$row) :
				list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state); 
?>
			<tr  style='background-color: <?php echo state_color($id_order_state); ?>'>
            	<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $state_name; ?></td>
				<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $first_name." ". $last_name; ?></td>
				<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($date_add)); ?></td>
            </tr>
<?php 		$i++; 	?>
<?php 	endwhile; ?>			
<?php else : ?>
		<tr>
        	<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $order->currentState(); ?></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($order->date_upd)); ?></td>
        </tr>
<?php endif; ?>		
		</table>
        </div>
        </form>
        <input type='hidden' id='id_customer' value='<?php echo $customer->id_customer; ?>' />
		<div class='col-xs-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr>
        	<td colspan='3' >ข้อมูลลูกค้า</td>
        </tr>
		<tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; <?php echo $customer->full_name; ?></td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp;<?php echo $customer->credit_term; ?></td>
		</tr>
        <tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;<?php echo $customer->email; ?></td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp;<?php echo number_format($customer->credit_amount,2); ?></td>
		</tr>
        <tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
        	อายุ :&nbsp;<?php  if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} ?>
			</td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp; <?php echo number_format($customer->credit_used,2); ?></td>
		</tr>
        <tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
        	เพศ : &nbsp;<?php if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} ?>
        	</td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp;<?php echo number_format($customer->credit_balance,2); ?></td>
		</tr>
        <tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;<?php echo thaiTextDate($customer->date_add); ?></td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;<?php echo number_format($customer->total_spent,2); ?></td>
		</tr>
        <tr>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
			<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp;<?php echo $customer->total_order_place; ?></td>
        </tr>
		</table>
		</div><!--col -->
        </div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
		<form id='edit_order_form' action='controller/orderController.php?edit_order&add_detail' method='post' autocomplete='off'>
		<div class='row'>
       		<div class="col-lg-12">
            <!---------------------------------------------------------------------  Order Table  ----------------------------------------------------------->
<?php
 if($order->valid==1 || $order->current_state !=1 && $order->current_state !=3){ $active = "disabled='disabled'";}else{$active = ""; }
		$role = $order->role;
		switch($role) :
			case 1 :
				$content = "order";
				break;
			case 2 :
				$content = "requisition";
				break;
			case 3 :
				$content = "lend";
				break;
			case 4 :
				$content = "sponsor";
				break;
			case 5 :
				$content = "consignment";
				break;
			case 6 :
				$content = "tranfromation";
				break;
			default :
				$content = "order";
				break;
		endswitch;
?>	
<?php
	$qm = dbQuery("SELECT discount_amount FROM tbl_order_discount WHERE id_order = ".$id_order);
	if( dbNumRows($qm) )
	{
		$rm = dbFetchArray($qm);
		$l_discount = $rm['discount_amount'];
	}else{
		$l_discount = 0;
	}
?>

<?php if($order->current_state != 9 && $order->current_state != 8 ) : ?>	
	<?php if($edit || $add) : ?>
        <button type='button' id='edit_reduction' class='btn btn-default' >แก้ไขส่วนลด</button>
        <button type='button' id='save_reduction' class='btn btn-default' onclick="check_discount()" style="display:none;" >บันทึกส่วนลด</button>
       <?php if(!$l_discount) : ?>
       		<button type="button" id="btn_add_discount" class="btn btn-default" ><i class="fa fa-plus"></i>&nbsp;เพิ่มส่วนลดท้ายบิล</button>
            <button type="button" id="btn_save_discount" class="btn btn-success" onclick="add_discount(<?php echo $id_order; ?>)" style="display:none;"><i class="fa fa-save"></i>&nbsp;บันทึกส่วนลดท้ายบิล</button>
       <?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
	<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc; margin-top:10px;'>
    <thead>
    	<th style='width:10%; text-align:center;'>รูปภาพ</th>
        <th style='width:40%'>สินค้า</th>
        <th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:10%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='width:10% text-align:center;'>การกระทำ</th>
    </thead>
<?php    
		$q = "SELECT tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount,reduction_percent,reduction_amount,id_order_detail ";
		$q .= "FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = ".$id_order." ORDER BY barcode ASC";
		$sql = dbQuery($q);
		$row = dbNumRows($sql);
		if($row>0) :
			$discount ="0.00";
			$amount = "0.00";
			$total_amount = "";
			$n = 1;
			while($i = dbFetchArray($sql)) :
				$product = new product();
				$total = $i['total_amount'];
				$id_order_detail = $i['id_order_detail'];
				$id_product_attribute = $i['id_product_attribute'];
				$reduction_percent = $i['reduction_percent'];
				$reduction_amount = $i['reduction_amount'];
				$total_price = $i['product_price']*$i['product_qty'];
				$id_o = $i['id_order']; 
				$id_p = $i['id_product_attribute'];
				$el = $i['id_order'].$i['id_product_attribute'];  //// ไอดีของปุ่ม
				if($reduction_amount != "0.00") :
					$reduction = $reduction_amount;
					$unit = "฿";
				elseif($reduction_percent != "0.00") :
					$reduction = $reduction_percent;
					$unit = "%";
				else :
					$reduction = 0;
					$unit = "%";
				endif;
?>				
			<tr>
				<td style='text-align:center; vertical-align:middle;'><img src='<?php echo $product->get_product_attribute_image($id_p,1); ?>' /></td>
				<td style='vertical-align:middle;'><?php echo $i['product_reference']." : ".$i['product_name']." : ".$i['barcode']; ?></td>
				<td style='text-align:center; vertical-align:middle;'><p id='price<?php echo $n; ?>'><?php echo number_format($i['product_price'],2); ?></p></td>
                <td style='text-align:center; vertical-align:middle;'>
                	<p class='reduction' id='reduction' ><?php echo $reduction." ".$unit; ?></p>
                   <div class='input_reduction' style='display:none;'>
                        <div class='row'>
                            <input type='text' class='form-control input-sm' id='reduction<?php echo $n; ?>' value='<?php echo $reduction; ?>' />                   
							<select class="form-control input-sm" id="unit<?php echo $n; ?>" >
                                	<option value="percent" <?php echo isSelected($unit, "%"); ?> >%</option>
                                    <option value="amount" <?php echo isSelected($unit, "฿"); ?> >฿</option>
                            </select>
                        </div>
                    </div>
                </td>
				<td style='text-align:center; vertical-align:middle;'>
                	<p id='qty<?php echo $el; ?>'><?php echo number_format($i['product_qty']); ?></p>
                    <input type='text' id='edit_qty<?php echo $el; ?>' class="form-control input-sm" style='display:none;' />
					<input type='hidden' id='id_order_detail<?php echo $n; ?>' name='id_order_detail' value='<?php echo $id_order_detail; ?>' />
                </td>
				
				<td style='text-align:center; vertical-align:middle;'><p id='total<?php echo $el; ?>'><?php echo number_format($total,2); ?></p></td>
				<td style='text-align:center; vertical-align:middle;'>
                <?php if($order->current_state == 3 || $order->current_state == 1 ) : ?>
					<button type='button' id='edit<?php echo $el; ?>' class='btn btn-warning btn-sx' onclick='edit_product(<?php echo $id_o.",".$id_p; ?>)' <?php echo $active; ?> <?php echo $can_edit; ?> ><i class="fa fa-pencil"></i></button>
                    <button type='button' id='update<?php echo $el; ?>' onclick='update(<?php echo $id_o.",".$id_p.",".$id_order_detail; ?>)' class='btn btn-default' style='display:none;' <?php echo $active; ?>>Update</button>
                    <?php if($order->valid !=1 || $order->current_state !=8) : ?>	
                    <?php $link = "controller/".$content."Controller.php?delete=y&id_order=".$id_o."&id_product_attribute=".$id_p; ?>
                    <?php $text = $i['product_reference']." : ".$i['product_name']." : ".$i['barcode']; ?>
					<button type='button' id='delete<?php echo $el; ?>' class='btn btn-danger btn-sx' 
                        onclick="confirm_delete('คุณแน่ใจว่าต้องการลบ ?', '<?php echo $text; ?>', '<?php echo $link; ?>')" <?php echo $active; ?> <?php echo $can_delete; ?> >
							<i class="fa fa-trash"></i>
						</button>
					</a>
                    <?php endif; ?>
                    <?php endif; ?>
                    </td>
                <tr>
<?php                
					$discount += $i['discount_amount'];
					$total_amount += $total_price;
					$amount += $i['total_amount'];
					$n++;
			endwhile;
?>		
<?php if( $l_discount ) : ?>	
		<tr id="last_discount_row" >
        	<td colspan="5" style="text-align: right; vertical-align:middle; padding-right:20px;">ส่วนลดท้ายบิล</td>
            <td style='text-align:center; vertical-align:middle;'><span id="discount_label"><?php echo number_format($l_discount, 2); ?></span><input type="text" id="last_discount" class="form-control" style="text-align:right; display:none;" value="<?php echo $l_discount; ?>" /></td>
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
            <td><input type="text" id="last_discount" class="form-control" style="text-align:right" /></td>
            <td>บาท</td>
        </tr>
<?php endif; ?>        
			<input type='hidden' id='loop' value='<?php echo ($n-1); ?>'>
            <input type='hidden' id='id_order' value='<?php echo $id_order; ?>'>
		<tr>
        	<input type='hidden' name='new_qty' id='new_qty' />
			<td rowspan='3' colspan='4'>
            
            </td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td>
            <td colspan='2' align='right'><b><?php echo number_format($total_amount,2); ?> </b></td></tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td>
        	<td colspan='2' align='right'><b><?php echo number_format(($discount + $l_discount),2); ?> </b></td>
        </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>สุทธิ </b></td>
        	<td colspan='2' align='right'><b><?php echo number_format(($amount-$l_discount),2); ?> </b></td>
        </tr>			
<?php	else :  ?>
		<tr>
            <td colspan='7' align='center'><h4>ไม่มีรายการสินค้า</h4></td>
       	</tr>

<?php endif;  ?>
   	</table>


<!--------------------------------------------------------------------  End order table  ---------------------------------------------------------> 
            </div>
        </div>
        <div class='row'>
        	<div class='col-lg-12'>
            	<p><h4>ข้อความ :  <?php if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; } ?></h4></p>
            </div>
         </div>
         <h4></h4>
         </form>
         <div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus">
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='check_password()' >
				<!--userForm--> 
			</div>
			<p style='text-align:center; color:red;' id='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login --> 
<script> 
$('#ModalLogin').on('shown.bs.modal', function () {  $('#password').focus(); }); 
$("#password").keyup(function(e) { if(e.keyCode == 13 ){ $("#login").focus(); }});
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
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_password()' >
				<!--userForm--> 
			</div>
			<p style='text-align:center; color:red;' id='bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login --> 
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
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login --> 
<script> 
$('#modal_approve_edit').on('shown.bs.modal', function () {  $('#edit_bill_password').focus(); }); 
</script>
<!-------------------------------------------------- จบหน้าแก้ไข ---------------------------------------------->
<?php else : ?>
<?php 	//************************************************ แสดงรายการ *************************************************// ?>
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ setcookie("consign_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }else{ setcookie("consign_from_date", "", time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("consign_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }else{ setcookie("consign_to_date", "", time() + 3600, "/"); }
	$paginator = new paginator();
?>	
<form  method='post' id='form'>
<div class='row'>
	<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
		<label>เงื่อนไข</label>
		<select class='form-control' name='filter' id='filter'>
        <option value="reference" <?php if( isset( $_POST['filter'] ) ){ echo isSelected($_POST['filter'], "reference"); }else if( isset( $_COOKIE['consign_filter'] ) ){ echo isSelected($_COOKIE['consign_filter'], "reference"); } ?>>เลขที่เอกสาร</option>
		<option value="customer" <?php if( isset( $_POST['filter'] ) ){ echo isSelected($_POST['filter'], "customer");   }else if( isset( $_COOKIE['consign_filter'] ) ){ echo isSelected($_COOKIE['consign_filter'], "customer");  } ?>>ลูกค้า</option>
		</select>
		
	</div>	
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
    	<label>คำค้น</label>
        <?php 
			$value = '' ; 
			if(isset($_POST['search_text'])) : 
				$value = $_POST['search_text']; 
			elseif(isset($_COOKIE['consign_search_text'])) : 
				$value = $_COOKIE['consign_search_text']; 
			endif; 
		?>
		<input class='form-control' type='text' name='search_text' id='search_text' placeholder="ระบุคำที่ต้องการค้นหา" value='<?php echo $value; ?>' />	
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>จากวันที่</label>
            <?php 
				$value = ""; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['consign_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['consign_from_date'])); 
				endif; 
				?>
			<input type='text' class='form-control' name='from_date' id='from_date' placeholder="ระบุวันที่" style="text-align:center;"  value='<?php echo $value; ?>'/>
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>ถึงวันที่</label>
            <?php
				$value = "";
				if( isset($_POST['to_date']) && $_POST['to_date'] != "" ) :
				 	$value = date("d-m-Y", strtotime($_POST['to_date'])); 
				 elseif( isset($_COOKIE['consign_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['consign_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-block' id='search-btn' type='submit' onclick="load_in()" ><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php

		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['consign_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['consign_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['consign_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['consign_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search_text'])/* && $_POST['search_text'] !="" */) :
			$text = $_POST['search_text'];
			$filter = $_POST['filter'];
			setcookie("consign_search_text", $text, time() + 3600, "/");
			setcookie("consign_filter",$filter, time() +3600,"/");
		elseif(isset($_COOKIE['consign_search_text']) && isset($_COOKIE['consign_filter'])) :
			$text = $_COOKIE['consign_search_text'];
			$filter = $_COOKIE['consign_filter'];
		else : 
			$text	= "";
			$filter	= "";
		endif;
		$where = "WHERE role = 5 AND order_status = 1 ";
		if( $text != "" ) :
			switch( $filter) :				
				case "customer" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					if($rs>0) :
						while($i<$rs) :
							list($in) = dbFetchArray($qs);
							$in_cause .="$in";
							$i++;
							if($i<$rs){ $in_cause .=","; 	}
						endwhile;
						$where .= "AND id_customer IN($in_cause)" ; 
					else :
						$where .= "AND id_customer = 0" ; 
					endif;
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
			if($from != "" && $to != "" ) : 
				$where .= " AND (date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;
		else :
			if($from != "" && $to != "" ) : 
				$where .= "AND (date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;	
		endif;
		$where .= " ORDER BY date_add DESC";
		
?>		
<?php
	$paginator = new paginator();
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_order", $where, $get_rows);
		$paginator->display($get_rows,"index.php?content=consignment");
?>		
		
<div class='row'>
<div class='col-xs-12'>
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
			<th style='width:15%;'>ลูกค้า</th>
            <th style='width:10%;'>พนักงาน</th><th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th><th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>

<?php 
		$result = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." ,". $paginator->Per_Page);
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			$rs = dbFetchArray($result);	
			$id_order = $rs['id_order'];		
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = ".$id_order));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = ".$rs['current_state']));
		echo"<tr style='color:#FFF; background-color:".state_color($rs['current_state']).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">".$id_order."</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">".$rs['reference']."</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">".customer_name($rs['id_customer'])."</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">".employee_name($rs['id_employee'])."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">"; echo thaiDate($rs['date_add'])."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consignment&id_order=$id_order&view_detail=y'\">"; echo thaiDate($rs['date_upd'])."</td>
			</tr>";
		$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
?>	
	</table>
<?php endif; ?>
<script>
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
		 source: "controller/orderController.php?product",
		 close: function(event,ui){
			 var ref = $(this).val();
			var id_cus = $("#id_cus").val();	
		$.ajax({ 
			 url: "controller/orderController.php?reference="+ref+"&id_customer="+id_cus,
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
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});
$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(' | ');
			var id = arr[0];
			var name = arr[1];
			var id_customer = arr[2];
			$("#id_customer").val(id_customer);
			$(this).val(name);
		}
	});			
});
$(document).ready(function(e) {
    $("#auto_zone").change(function(e) {
		if($(this).prop("checked")){
        $("#zone_id").attr("disabled","disabled");
		}else{
		 $("#zone_id").removeAttr("disabled");
		}
    });
});
/// คลิ๊กปุ่มเพิ่มออเดอร์ใหม่ ////
$("#add_order").click(function(e) {
    var date = $("#doc_date").val();
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	var id_zone = $("#zone_id").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else if(!$("#auto_zone").prop("checked")){
		if(id_zone == 0){
			alert("ยังไม่ได้เลือกพื้นที่เก็บ");
		}else{
		$("#add_order_form").submit();
		}
	}else{
		$("#add_order_form").submit();
	}
});

$(document).ready(function(e) {
    $("#product_code").autocomplete({
		source:"controller/orderController.php?product_code",
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
		url:"controller/orderController.php?check_stock&product_code="+product_code
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
		url:"controller/orderController.php?check_stock&barcode="+barcode
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
		url:"controller/orderController.php?check_stock&id_order="+id_order+"&id_product_attribute="+id_product_attribute, 
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
	}else if(parseInt(order_qty)>parseInt(stock_qty)){
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
	}else if(parseInt(order_qty)>parseInt(stock_qty)){
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
		url:"controller/orderController.php?insert_detail",
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
function get_row(){
	$("#rows").submit();
}
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
var comment = $("#comment").val();
var id_cus = $("#id_custmer").val();  // id_customer
var customer_name = $("#customer_name").val();
var id_zo = $("#zone_id").val();  // id_zone;
var date_doc = $("#doc_date").val(); // doc_date;
$("#btn_edit").click(function(e) {
    $("#comment").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#zone_id").removeAttr("disabled");
	$("#doc_date").removeAttr("disabled");
	$(this).css("display","none");
	$("#btn_update").css("display","");
});

$("#btn_update").click(function(e) {
	var id_order = $("#id_order").val();
	var id_customer = $("#id_customer").val();
	var id_zone = $("#zone_id").val();
	var date = $("#doc_date").val();
	var remark = $("#comment").val();
	$.ajax({
		url: "controller/consignmentController.php?edit_doc_head&id_order="+id_order,
		type:"GET", cache:false, data: {"id_customer" : id_customer, "id_zone" : id_zone, "doc_date" : date, "remark" : remark},
		success: function(rs){
			if(rs == "success"){
				$("#comment").attr("disabled","disabled");
				$("#customer_name").attr("disabled","disabled");
				$("#zone_id").attr("disabled", "disabled");
				$("#doc_date").attr("disabled", "disabled");
			}else{
				swal("ไม่สามารถบันทึกการเปลี่ยแปลงได้");
				$("#comment").val(comment);
				$("#customer_name").val(customer_name);
				$("#id_customer").val(id_cus);
				$("#zone_id").val(id_zo);
				$("#doc_date").val(date_doc);
			}
		}
	});
});

$("#edit_reduction").click(function(e) {
    $(".reduction").css("display","none");
	$(".input_reduction").css("display","block");
	$("#edit_reduction").css("display", "none");
	$("#save_reduction").css("display","");
});

function check_discount()
{
	var era = 0;
	var erx = 0;
	for ( i = 0 ;i < $("#loop").val(); i++ ){
		var n = i+1;
		var reduction = $("#reduction"+n).val();
		var price = parseInt($("#price"+n).text());
		var unitx = $("#unit"+n).val();
		if(unitx == "percent" && reduction >100){  era ++; }
		if(unitx == "amount" && reduction > price){ erx ++; }
	}
	if(era > 0 ){
		load_out();
		$("#ModalLogin").modal('hide');
		swal("ส่วนลดเกิน 100 % กรุณาแก้ไข"); 
		return false;
	}else if(erx > 0 ){
		load_out();
		$("#ModalLogin").modal('hide');
		swal("ส่วนลดเกิน ราคาสินค้า กรุณาแก้ไข"); 
		return false;
	}else{
		$("#ModalLogin").modal('show');
	 }
}
function check_password(){
	$("#loader").css("z-index","1100");
	load_in();
	var password = $("#password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				load_out();
				$("#message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#password").val("");
			}else{
				update_discount(data);
			}
		}
	});
}
function update_discount(id_employee){
	var id_order_detail_array = [];
	var reduction_array = [];
	var unit_array = [];
	var id_order = $("#id_order").val();
	for ( i = 0 ;i < $("#loop").val(); i++ ){
		var n = i+1;
		var id_order_detail = $("#id_order_detail"+n).val();
		var reduction = $("#reduction"+n).val();
		var unitx = $("#unit"+n).val();
		id_order_detail_array[n] = id_order_detail;
		reduction_array[n] = reduction;
		unit_array[n] = unitx;
	}
	$.ajax({
		url:"controller/orderController.php?edit_discount",
		data: {"id_employee" : id_employee, "reduction_array" : reduction_array, "id_order_detail_array" : id_order_detail_array, "unit_array" : unit_array, "id_order" : id_order }, type:"POST",cache:false,
		success: function(complete){
			window.location.reload();
		}
	});
}

function valid_password(){
	$("#loader").css("z-index","1100");
	load_in();
	var password = $("#bill_password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				load_out();
				$("#bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#bill_password").val("");
			}else{
				insert_discount(data);
			}
		}
	});
}

function valid_approve()
{
	$("#loader").css("z-index","1100");
	load_in();
	var password = $("#edit_bill_password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				load_out();
				$("#edit_bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#edit_bill_password").val("");
			}else{
				update_bill_discount(data);
			}
		}
	});
}

function update_bill_discount(id_approve)
{
	var id_order = $("#id_order").val();
	var discount = $("#last_discount").val();
	$.ajax({
		url:"controller/orderController.php?update_bill_discount", type:"POST", cache:false,
		data: { "id_order" : id_order, "id_approve" : id_approve, "discount" : discount },
		success: function(rs){
			var rs = $.trim(rs);
			load_out();
			if(rs == "success")
			{
				window.location.reload();
			}else{
				$("#modal_approve_edit").modal("hide");
				swal("แก้ไขส่วนลดไม่สำเร็จ");
				$("#btn_update_discount").css("display","none");
				$("#btn_edit_discount").css("display","");
				$("#btn_delete_discount").css("display","");
				$("#edit_reduction").removeAttr("disabled");
			}
		}
		
	});
}

$("#btn_update_discount").click(function(e) {
    var discount = $("#last_discount").val();
	if( discount == "" || discount < 1)
	{
		swal("ส่วนลดต้องมากกว่า 0");
		return false;
	}else{
		$("#modal_approve_edit").modal("show");
	}
});

function action_delete(id_order, amount)
{
	var text = "คุณต้องการลบส่วนลดท้ายบิล มูลค่า "+ amount;
	var url  = "controller/orderController.php?delete_bill_discount&id_order="+id_order;
	confirm_delete('คุณแน่ใจนะ ?', text, url );
}
function edit_discount()
{
	$("#discount_label").css("display","none");
	$("#last_discount").css("display","");
	$("#btn_edit_discount").css("display","none");
	$("#btn_delete_discount").css("display","none");
	$("#edit_reduction").attr("disabled", "disabled");
	$("#btn_update_discount").css("display","");	
}
function insert_discount(id_approve)
{
	var id_order = $("#id_order").val();
	var discount = $("#last_discount").val();
	$.ajax({
		url:"controller/orderController.php?insert_bill_discount", type:"POST", cache:false,
		data: { "id_order" : id_order, "id_approve" : id_approve, "discount" : discount },
		success: function(rs){
			var rs = $.trim(rs);
			load_out();
			if(rs == "success")
			{
				window.location.reload();
			}else{
				swal("เพิ่มส่วนลดไม่สำเร็จ");
				$("#btn_save_discount").css("display","none");
				$("#btn_add_discount").css("display","");
				$("#edit_reduction").removeAttr("disabled");
			}
		}
		
	});
}


function add_discount(id_order)
{
	load_in();
	var discount = $("#last_discount").val();
	if( discount == "" || discount < 1 )
	{
		load_out();
		swal("ส่วนลดท้ายบิลต้องมากกว่า 0");
		return false;
	}else{
		load_out();
		$("#modal_approve").modal("show");	
	}
}

$("#btn_add_discount").click(function(e){
	$("#edit_reduction").attr("disabled", "disabled");
	$(this).css("display", "none");
	$("#btn_save_discount").css("display","");
	$("#last_discount_row").css("display","");
	$("#last_discount").focus();
});
function clear_filter()
{
	$.ajax({
		url:"controller/consignmentController.php?clear_filter",
		type:"GET", cache:"false", 
		success: function(){
			window.location.href = "index.php?content=consignment";
		}
	});
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
</script>