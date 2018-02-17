<?php 
	$page_menu = "invent_order";
	$page_name = "ออเดอร์";
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
	
	$btn = "";
	if(isset($_GET['edit'])&&isset($_GET['id_order'])) {
 	    $id_order = $_GET['id_order']; 
	    $order= new order($id_order); 
		if($order->valid==1 || $order->current_state !=1 && $order->current_state !=3){ 
			$btn .= "<a href='index.php?content=order' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		}else{
			$btn .= "<a href='index.php?content=order' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
			$btn .= can_do($edit, "&nbsp;<a href='index.php?content=order&add=y&id_order=$id_order' style='text-decoration:none;' ><button type='button' class='btn btn-warning' ><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button></a>");
		}
		
	}else if(isset($_GET['edit']) || isset($_GET['add']) || isset($_GET['view_stock'])){
			$btn .= "<a href='index.php?content=order' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
			 if(isset($_GET['id_order'])){
				 $id_order = $_GET['id_order'];
				 if( $add || $edit){
					$btn .= "&nbsp;<a href='controller/orderController.php?save_order&id_order=$id_order'><button type='button' class='btn btn-success' onclick='edit_stock()'><i class='fa fa-save'></i>&nbsp;บันทึก</button></a>";
				 }
			 }
	  }else{
		$btn .= can_do($add, "<a href='controller/orderController.php?check_add' ><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
		$btn .= "&nbsp; <a href='index.php?content=order&view_stock' ><button type='button' class='btn btn-info' ><i class='fa fa-search'></i>&nbsp; ดูสต็อกคงเหลือ</button></a>";
	}
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3 class="title"><i class="fa fa-archive"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right">
		<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
function payment_method($se)
{ 
	return true;
}
?>
<!---------------------------------------- ข้อมูลลูกค้า --------------------------------------->
<button data-toggle='modal' data-target='#myModal' id='info' style='display:none;'>xxx</button>
<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style='width:600px;'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='myModalLabel'></h4>
            </div>
            <div class='modal-body'>
            </div>
            <div class='modal-footer'>
            	<button type='button' class='btn btn-default' data-dismiss='modal'>รับทราบ</button>
            </div>
        </div>
    </div>
</div>
<?php                                
//*********************************************** เพิ่มออเดอร์ใหม่ ********************************************************// 
if(isset($_GET['add'])) :
	$user_id = $_COOKIE['user_id'];
	if(isset($_GET['id_order'])) :
		$id_order = $_GET['id_order'];
		$active = "disabled='disabled'"; 
		$add = "style='display:none;'";
		$edit = "";
		$order = new order($id_order);
		$new_ref = $order->reference;
		$customer = new customer($order->id_customer);
		$id_customer = $customer->id_customer;
		$customer_name = $customer->full_name; 
		$comment = $order->comment;
		$payment = $order->payment;
	else :
		$id_order="";
		$new_ref = get_max_role_reference("PREFIX_ORDER",1);
		$active = "";
		$add="";
		$edit = "style='display:none;'";
		$id_customer = "";
		$customer_name = "";
		$comment = "";
		$payment = "เครดิต";
	endif;

?>
<form id='add_order_form' action='controller/orderController.php?add=y' method='post'>
<div class='row'>
	<input type='hidden' name='id_employee' value='<?php echo $user_id; ?>' />
    <input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
    	<div class='input-group'>
        	<span class='input-group-addon'>เลขที่เอกสาร</span>
            <input type='text' id='doc_id' class='form-control' value='<?php echo $new_ref; ?>' disabled='disabled'/>
        </div>
    </div> 
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<div class='input-group'>
        	<span class='input-group-addon'>วันที่</span>
            <input type='text' id='doc_date' name='doc_date' class='form-control' value='<?php echo date('d-m-Y'); ?>' <?php echo $active; ?> />
        </div>
    </div>
	<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'>
    	<div class='input-group'>
        	<span class='input-group-addon'>ชื่อลูกค้า</span>
            <input type='text' id='customer_name' class='form-control' value='<?php echo $customer_name; ?>' autocomplete='off' <?php echo $active; ?> />
        </div>
    </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<button class='btn btn-default' type='button' id='get_info' >&nbsp&nbsp;ดูข้อมูล&nbsp;&nbsp </button>
    </div>
</div>
<div class='row' style='margin-top:15px;'>
	<input type='hidden' name='id_customer' id='id_customer' value='<?php echo $id_customer; ?>' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
        <div class='input-group'>
        	<span class='input-group-addon'>การชำระเงิน</span>
        	<select name='payment' id='payment' class='form-control' <?php echo $active; ?> ><?php echo payment_method($payment); ?></select>
        </div> 
    </div>
	<div class='col-lg-6 col-md-6 col-sm-6 col-sx-6 '>
    <div class='input-group'>
    <span class='input-group-addon'>หมายเหตุ</span>
    <input type='text' id='comment' name='comment' class='form-control' value='<?php echo $comment; ?>' autocomplete='off' <?php echo $active; ?> />
    </div> 
    </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<button class='btn btn-default' type='button' id='add_order' <?php echo $add." ".$can_add; ?> >&nbsp&nbsp;เพิ่ม&nbsp;&nbsp </button>
		<button class='btn btn-default' type='button' id='edit_order' <?php echo $edit." ".$can_edit; ?> >&nbsp&nbsp;แก้ไข&nbsp;&nbsp </button>
    </div>
</div>
    </form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<?php if(isset($_GET['id_order'])) :  ?>
<form id='add_detail_form' action='controller/orderController.php?add&insert_detail' method='post'>
<div class='row'>
	<input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
	<input type='hidden' name='stock_qty' id='stock_qty' />
    <input name='id_product_attribute' id='id_product_attribute' type='hidden' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode' class='form-control' autocomplete='off' autofocus /></div> </div> 
	<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'><div class='input-group'><span class='input-group-addon'>สินค้า</span><input type='text' id='product_code' class='form-control' autocomplete='off' /></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><div class='input-group'><span class='input-group-addon'>ในสต็อก</span><input type='text' id='stock_label' class='form-control' disabled='disabled' /></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' autocomplete='off' autofocus /></div> </div>
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'><button class='btn btn-default' type='button' id='add_detail' onclick='submit_detail()'>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
 
<!-----------------------------------------  เริ่ม ORDER GRID ---------------------------------->
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
		<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
<?php    
				$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row) :
					list($id_category, $category_name) = dbFetchArray($sql);
					$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = ".$id_category." ORDER BY position ASC");
					$rs = dbNumRows($sqr);
					$n=0;
					if($rs<1) :
						echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
					else :		
						echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
						echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
						echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
						while($n<$rs) :
							list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
							echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
							$n++;
						endwhile;
						echo"</ul></li>";
					endif;
					echo "</li>";
					$i++;
				endwhile;
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
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category AND tbl_product.active = 1 ORDER BY product_code ASC");
		//$sql = dbQuery("SELECT id_product FROM tbl_product WHERE default_category_id = ".$id_category." AND active = 1 ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0) :
			$i=0;
			while($i<$row) :
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				//$product->product_detail($id_product);
?>				
		 <div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div class='product' style='padding:5px;'>
                <div class='image'>
                    <a href='javascript:void(0)' onclick='getData(<?php echo $id_product; ?>)'>
                        <?php echo $product->getCoverImage($id_product,1,"img-responsive"); ?>
                    </a>
                </div>
				<div class='description' style='font-size:10px; min-height:50px;'>
                	<a href='javascript:void(0)'  onclick='getData(<?php echo $id_product; ?>)'>
						<?php echo $product->product_code($id_product). "<br/>".$product->product_price($id_product); ?> : <span style='color:red'><?php echo $product->available_product_qty($id_product); ?></span>
					</a>
                </div>
			</div>
          </div>
<?php	$i++; ?>
<?php 	endwhile; ?>
<?php else : ?>
		<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>
<?php endif; ?>
		<?php $r++;  ?>
		</div>
<?php endwhile; ?>
</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>
<!------------------------------------ จบ ORDER GRID ------------------------------------>	
<form action='controller/orderController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
                    <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
                    <input type='hidden' name='id_order' value='<?php echo $id_order; ?>'/>
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
<div class='col-lg-12'>
	<table class='table' id='order_detail'>
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
            <td style='text-align:center; vertical-align:middle;'><img src='<?php echo $product->get_product_attribute_image($id_product_attribute,1); ?>' width='35px' height='35px' /> </td>
            <td style='vertical-align:middle;'><?php echo $barcode; ?></td>
            <td style='vertical-align:middle;'><?php echo $product_reference." : ".$product_name; ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($product_price,2); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($product_qty); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo $discount; ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($total_amount,2); ?></td>
            <td style='text-align:center; vertical-align:middle;'>
                <a href='controller/orderController.php?delete=y&id_order_detail=<?php echo $id_order_detail; ?>' >
                    <button type='button' class='btn btn-danger btn-sx' onclick="return confirm('คุณแน่ใจว่าต้องการลบ <?php echo $product_reference." : ".$product_name;?>')" >
                        <span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
                    </button>
                </a>
            </td>
      	</tr>
<?php        
			$sumproduct_qty += $product_qty;
			$i++;
			$n++;
?>			
<?php endwhile; ?>	
	<tr>
		<td colspan='6'></td>
        <td><h4>จำนวน</h4></td>
        <td style='text-align:center; vertical-align:middle;'><h4><?php echo $sumproduct_qty; ?></h4></td>
        <td><h4>ชิ้น<h4></td>
	</tr>	
<?php else : ?>
	<tr>
    	<td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td>
    </tr>
<?php endif; ?>
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
	$state = $order->orderState();
	$role = $order->role;
?>
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
    	<h4><?php 	echo $order->reference." - ";  	if($order->id_customer != "0") : echo $customer->full_name; endif; ?> <p class='pull-right'>พนักงาน : &nbsp; <?php echo $sale->full_name; ?></p></h4>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt>
        <dd style='float:left; margin:0px; padding-right:10px'><?php echo thaiDate($order->date_add); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt>
        <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt>
        <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt>
        <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_amount,2); ?></dd> </dt></dl>
        <p class='pull-right'>
        <?php  if($order->current_state ==3) : ?>	
        	<a href='controller/orderController.php?print_prepare&id_order=<?php echo $id_order; ?>' ><button type='button' class='btn btn-success'>ปริ๊นใบสั่งจัด</button></a>
		<?php endif; ?>
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
			<button type="button" class="btn btn-success" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
        </p>
	</div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class='row'>
	<form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>
	<div class='col-lg-6 col-md-6 col-sm-6 col-sx-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
        	<tr>     	
				<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td>
                <td style='width:40%; padding-right:10px;'>
        			<input type='hidden' name='id_order' value='<?php echo $order->id_order; ?>' />
                    <input type='hidden' name='id_employee' value='<?php echo $id_employee; ?>' />
					<select name='order_state' id='order_state' class='form-control input-sm' <?php echo $can_edit; ?>>
                    	<?php echo orderStateList($order->id_order); ?>
                    </select>
                </td>
                <td style='padding-right:10px;'>
                <?php if($edit) : ?>
               	 	<button class='btn btn-default' type='button' onclick='state_change()' $can_edit>เพิ่ม</button>
                <?php endif; ?>
                </td>
            </tr>
<?php            
		$row = dbNumRows($state);
		$i=0;
		if($row>0) :
			while($i<$row) :
				list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state);
?>			
			<tr  style='background-color:<?php echo state_color($id_order_state); ?>'>
            	<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $state_name; ?></td>
				<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $first_name." ".$last_name; ?></td>
				<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo date('d-m-Y H:i:s', strtotime($date_add)); ?></td>
            </tr>
<?php			
				$i++;
			endwhile;
		else :
?>		
            <tr>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $order->currentState(); ?></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($order->date_upd)); ?></td>
            </tr>
<?php endif; ?>
 		</table>
 	</div>
    </form>
    <div class='col-lg-6 col-md-6 col-sm-6 col-sx-6'>
<?php    
if($order->id_customer != "0") :
	if($role == 4) :
	$customer->sponsor_detail();
?>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
            <tr>
            	<td colspan='3' >ข้อมูลสปอนเซอร์</td>
            </tr>
            <tr>
                <input type='hidden' id='id_customer' value='<?php echo $customer->id_customer; ?>' />
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; <?php echo $customer->full_name; ?></td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เลขที่เอกสาร : &nbsp; <?php echo $customer->sponsor_reference; ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp; <?php echo $customer->email; ?></td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงิน :&nbsp; <?php echo number_format($customer->sponsor_amount,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
                	อายุ :&nbsp; <?php if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} ?>
                </td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ใช้ไป :&nbsp; <?php echo number_format($customer->sponsor_used,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
                	เพศ : &nbsp; <?php if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} ?>
                </td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>คงเหลือ : &nbsp;<?php echo number_format($customer->sponsor_balance,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp; <?php echo thaiTextDate($customer->date_add); ?></td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>จำนวนครั้งที่เบิก : &nbsp; <?php echo $customer->total_sponsor_place; ?> ครั้ง</td>
            </tr>
            <tr>
            	<td colspan='2' style='width:100%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
                	ระยะสัญญา : &nbsp;<?php echo thaiTextDate($customer->sponsor_start)." ถึง ".thaiTextDate($customer->sponsor_end); ?>
                </td>
            </tr>
		</table>
<?php	else :    ?>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
            <tr>
            	<td colspan='3' >ข้อมูลลูกค้า</td>
            </tr>
            <tr>
            	<input type='hidden' id='id_customer' value='<?php echo $customer->id_customer; ?>' />
            	<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; <?php echo $customer->full_name; ?></td>
            	<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp; <?php echo $customer->credit_term; ?></td>
            </tr>
            <tr>
            	<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;<?php echo $customer->email; ?></td>
            	<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp; <?php echo number_format($customer->credit_amount,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
                	อายุ :&nbsp; <?php if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} ?>
                </td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp; <?php echo number_format($customer->credit_used,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>
                	เพศ : &nbsp; <?php if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} ?>
                </td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp; <?php echo number_format($customer->credit_balance,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp; <?php echo thaiTextDate($customer->date_add); ?></td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;<?php echo number_format($customer->total_spent,2); ?></td>
            </tr>
            <tr>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
                <td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp; <?php echo $customer->total_order_place; ?></td>
            </tr>
		</table>
<?php        
	endif;
endif;
?>
	</div><!--col -->
</div><!--row-->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<form id='edit_order_form' action='controller/orderController.php?edit_order&add_detail' method='post' autocomplete='off'>
<div class='row'>
    <div class='col-lg-12'>
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
				<td style='text-align:center; vertical-align:middle;'>
                	<input type="hidden" id="price<?php echo $n; ?>" value="<?php echo $i['product_price']; ?>" />
                    <p id='price_<?php echo $n; ?>'><?php echo number_format($i['product_price'],2); ?></p>
                </td>
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
<!---------------------------------------------------------------- จบหน้าแก้ไข -------------------------------------------------->
<?php elseif( isset( $_GET['view_stock'] ) ) : ?>
<!---------------------------------------------------- ดูยอดสต็อกคงเหลือนำยอดที่สั่งมาคำนวนแล้ว --------------------------------->

<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
		<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
<?php        
				$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row) :
					list($id_category, $category_name) = dbFetchArray($sql);
					$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
					$rs = dbNumRows($sqr);
					$n=0;
						if($rs<1) :
							echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
						else :			
							echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
							echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
							echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
							while($n<$rs) :
								list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
								echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
								$n++;
							endwhile;
						echo"</ul></li>";
						endif;	
					echo "</li>";
					$i++;
				endwhile; 
?>
		</ul>
	</div>
</div>
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
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
	<div class='tab-content'>
<?php    
	$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r = 0;
	while($c = dbFetchArray($query)) :
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane "; if($r == 0){ echo "active"; } echo "'  id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category AND tbl_product.active = 1 ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0) :
			$i=0;
			while($i<$row) :
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
				echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>			
				<div class='product' style='padding:5px;'>
				<div class='image' style='text-align:center;'><a href='#' onclick='view_data(".$product->id_product.")' >".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
				<div class='description' style='min-height:50px; font-size:10px;'>
					<a href='#' onclick='view_data(".$product->id_product.")' >".$product->product_code."</a><br/>
					<a href='javascript:void(0)' onclick='view_data(".$product->id_product.")'>".$product->product_price." : <span style='color:red;'>".$product->available_product_qty($id_product)."</span></a>
				</div>
				</div>
				</div>";
				$i++;
				
			endwhile;
			$r++;
		else  :
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		endif;
		echo "</div>";
	endwhile;
?>	
	<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button></div>
				</div>
		</div>
	</div>
</div>
<!---------------------------------------------------- จบหน้าดูสต็อก  ------------------------------------------------>
<?php else : ?>
<!----------------------------------------------------- แสดงรายการ -------------------------------------------------->
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !="เลือกวัน"){ setcookie("order_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน"){ setcookie("order_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }
	$paginator = new paginator();
?>	
<form  method='post' id='form'>
<div class='row'>
	<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
		<div class='input-group'>
			<span class='input-group-addon'>เงื่อนไข</span>
			<select class='form-control' name='filter' id='filter'>
				<option value='customer' <?php if( isset($_POST['filter']) && $_POST['filter'] =="customer"){ echo "selected"; }else if( isset($_COOKIE['order_filter']) && $_COOKIE['order_filter'] == "customer"){ echo "selected"; } ?> >ลูกค้า</option>
				<option value='reference'<?php if( isset($_POST['filter']) && $_POST['filter'] =="reference"){ echo "selected"; }else if( isset($_COOKIE['order_filter']) && $_COOKIE['order_filter'] == "reference"){ echo "selected"; } ?>>เลขที่เอกสาร</option>
				<option value='sale' <?php if( isset($_POST['filter']) && $_POST['filter'] =="sale"){ echo "selected"; }else if( isset($_COOKIE['order_filter']) && $_COOKIE['order_filter'] == "sale"){ echo "selected"; } ?>>พนักงานขาย</option>
			</select>
		</div>		
	</div>	
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
		<div class='input-group'>
		<span class='input-group-addon'>ค้นหา</span>
        <?php 
			$value = '' ; 
			if(isset($_POST['search-text']) && $_POST['search-text'] !="") : 
				$value = $_POST['search-text']; 
			elseif(isset($_COOKIE['order_search-text'])) : 
				$value = $_COOKIE['order_search-text']; 
			endif; 
		?>
		<input class='form-control' type='text' name='search-text' id='search-text' value='<?php echo $value; ?>' />
		<span class='input-group-btn'><button class='btn btn-default' id='search-btn' type='button'><span id='load'><span class='glyphicon glyphicon-search'></span></span></button>
		</div>		
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<div class='input-group'>
			<span class='input-group-addon'> จาก :</span>
            <?php 
				$value = "เลือกวัน"; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "เลือกวัน") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['order_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['order_from_date'])); 
				endif; 
				?>
			<input type='text' class='form-control' name='from_date' id='from_date'  value='<?php echo $value; ?>'/>
		</div>		
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<div class='input-group'>
			<span class='input-group-addon'>ถึง :</span>
            <?php
				$value = "เลือกวัน";
				if( isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน" ) :
				 	$value = date("d-m-Y", strtotime($_POST['to_date'])); 
				 elseif( isset($_COOKIE['order_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['order_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' value='<?php echo $value; ?>' />
		</div>
	</div>
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
		<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
		<button type='button' class='btn btn-default' onclick="window.location.href='controller/orderController.php?clear_filter'"><i class='fa fa-refresh'></i> เคลียร์ฟิลเตอร์</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
		$view = "";
		if(isset($_POST['from_date']) && $_POST['from_date'] != "เลือกวัน"){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['order_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['order_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน"){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['order_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['order_to_date'])); }else{ $to = "";}
		if($from=="" || $to ==""){ $view = getConfig("VIEW_ORDER_IN_DAYS"); 	}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search-text']) && $_POST['search-text'] !="" ) :
			$text = $_POST['search-text'];
			$filter = $_POST['filter'];
			setcookie("order_search-text", $text, time() + 3600, "/");
			setcookie("order_filter",$filter, time() +3600,"/");
			switch( $_POST['filter']) :
				case "customer" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
					$rs = dbNumRows($qs);
					$i=0;
					if($rs>0) :
						while($i<$rs) :
							list($in) = dbFetchArray($qs);
							$in_cause .="$in";
							$i++;
							if($i<$rs){ $in_cause .=","; 	}
						endwhile;
						$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC" ; 
					else :
						$where = "WHERE id_order != NULL";
					endif;
				break;
				case "sale" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_sale FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					$in ="";
					if($rs>0) :
						while($i<$rs) :
							list($id_sale) = dbFetchArray($qs);
							$in .="$id_sale";
							$i++;
							if($i<$rs){ $in .=","; }
						endwhile;
						$sq = dbQuery("SELECT id_customer FROM tbl_customer WHERE id_sale IN($in)");
						$rs = dbNumRows($sq);
						$n =0;
						while($n<$rs) :
							list($id_customer) = dbFetchArray($sq);
							$in_cause .= "$id_customer";
							$n++;
							if($n<$rs){ $in_cause .= ","; }
						endwhile;
						$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC";
					else :
						$where = "WHERE id_order = NULL";
					endif;
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' AND role IN(1,4) AND order_status = 1 ORDER BY reference";
				break;
			endswitch;
		elseif(isset($_COOKIE['order_search-text']) && isset($_COOKIE['order_filter'])) :
			$text = $_COOKIE['order_search-text'];
			$filter = $_COOKIE['order_filter'];
			switch( $filter) :
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0) :
					while($i<$rs) :
						list($in) = dbFetchArray($qs);
						$in_cause .="$in";
						$i++;
						if($i<$rs){ $in_cause .=","; 	}
					endwhile;
					$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC";
					else :
						$where = "WHERE id_order != NULL";
					endif;
				break;
				case "sale" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_sale FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					$in ="";
					if($rs>0) :
						while($i<$rs) :
							list($id_sale) = dbFetchArray($qs);
							$in .="$id_sale";
							$i++;
							if($i<$rs){ $in .=","; }
						endwhile;
						$sq = dbQuery("SELECT id_customer FROM tbl_customer WHERE id_sale IN($in)");
						$rs = dbNumRows($sq);
						$n =0;
						while($n<$rs) :
							list($id_customer) = dbFetchArray($sq);
							$in_cause .= "$id_customer";
							$n++;
							if($n<$rs){ $in_cause .= ","; }
						endwhile;
						$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC";
					else :
						$where = "WHERE id_order = NULL";
					endif;
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' AND role IN(1,4) AND order_status = 1 ORDER BY reference";
				break;
			endswitch;
		else :
			$where = "WHERE (date_add BETWEEN '$from' AND '$to') AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC";
		endif;
?>		
<div class='row' id='result'>			
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>
<?php    
	$paginator->Per_Page("tbl_order",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=order");
	?>
		<table class='table'>
            <thead style='color:#FFF; background-color:#48CFAD;'>
                <th style='width:5%; text-align:center;'>ID</th>
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:20%;'>ลูกค้า</th>
                <th style='width:10%;'>พนักงาน</th>
                <th style='width:10%; text-align:center;'>ยอดเงิน</th>
                <th style='width:15%; text-align:center;'>การชำระเงิน</th>
                <th style='width:10%; text-align:center;'>สถานะ</th>
                <th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
                <th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
            </thead>
<?php          
		$result = dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);
		$i=0;
		$row = dbNumRows($result);
		if($row>0) :
		while($i<$row) :
			list($id_order, $reference,$id_customer,$id_employee,  $payment,   $date_add,$current_state,$date_upd)=dbFetchArray($result);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			if($id_employee ==""){ $employee_name = ""; }else{ list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'")); }
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
?>			
			<tr style='color:#FFF; background-color:<?php echo state_color($current_state); ?>; font-size:12px;'>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $id_order; ?></td>
				<td style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $reference; ?></td>
				<td style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $cus_first_name." ".$cus_last_name; ?></td>
				<td style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $employee_name; ?></td>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo number_format($amount); ?></td>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $payment; ?></td>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo $status; ?></td>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo thaiDate($date_add); ?></td>
				<td align='center' style='cursor:pointer;' onclick="document.location='index.php?content=order&edit=y&id_order=<?php echo $id_order; ?>&view_detail=y'"><?php echo thaiDate($date_upd); ?></td>
			</tr>
<?php 	$i++;  ?>
<?php	endwhile; ?>		
<?php elseif($row==0) : ?>
			<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>
<?php endif; ?>		
		</table>
<?php 	echo $paginator->display_pages(); ?>
<br><br>
<script>  var x = setTimeout(function () { location.reload(); }, 60 * 5000); </script>
<?php endif; ?>
</div>
<script>  
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
function update(id_order, id_product_attribute, id_order_detail){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = $("#edit_qty"+name).val();
	var old_qty = parseInt($("#qty"+name).text());
	var btn = $("#update"+name);
	btn.attr("disabled", "disabled");
	load_in();
	if(qty<=0){
		load_out();
		swal("จำนวนที่สั่งอย่างน้อย 1 ตัว");
		btn.removeAttr("disabled");
		return false;
	}else{
	$.ajax({
		url:"controller/orderController.php?edit_order&edit_qty", 
		cache:false, type:"POST",
		data:{ "id_order" : id_order, "id_product_attribute" : id_product_attribute, "id_order_detail" : id_order_detail, "qty" : old_qty, "edit_qty" : qty},
		success: function(rs){  /// x0 = วงเงินคงเหลือไม่พอ  x1 = สินค้าคงเหลือไม่พอ  xx = สำเร็จ
			var rs = $.trim(rs);
			if(rs == "x0"){
				load_out();
				swal("วงเงินคงเหลือไม่เพียงพอ");
				btn.removeAttr("disabled");
				return false;
			}else if(rs == "x1"){
				load_out();
				swal("สินค้าคงเหลือไม่พอ");
				btn.removeAttr("disabled");
				return false;
			}else{
				$("#qty"+name).text(qty);
				$("#edit_qty"+name).val('');
				$("#total"+name).text(rs);
				$("#qty"+name).css("display", "");
				$("#edit_qty"+name).css("display","none");
				$("#update"+name).css("display", "none");
				$("#edit"+name).css("display","");
				$("#delete"+name).css("display","");
				load_out()
				btn.removeAttr("disabled");
			}
		}
	});
	}
}
$(document).ready(function(e) {
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
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
$("#edit_order").click(function(e) {
    $("#doc_date").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#payment").removeAttr("disabled");
	$("#comment").removeAttr("disabled");
	$("#add_order").text("บันทึก");
	$(this).css("display", "none");
	$("#add_order").css("display","");
});
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
$("#get_info").click(function(e) {
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	 if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else{
		$.ajax({
			url:"controller/customerController.php?get_info&id_customer="+cus_id,
			type:"GET", cache:false, success: function(data){
				$(".modal-title").text("ข้อมูล : "+cus_name);
				$(".modal-body").html(data);
				$("#info").click();
			}
		});
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
function view_data(id_product){
	$.ajax({
		url:"controller/orderController.php?view_stock_data&id_product="+id_product,
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
		var price = parseFloat($("#price"+n).val());
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

$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var filter = $("#filter").val();
	if(query_text !=""){
		$("#form").submit();
	}else{
		alert("กรุณาใส่คำค้นหา");
	}
});
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