<?php 
	$page_menu = "invent_order_closed";
	$page_name = "รายการเปิดบิลแล้ว";
	$id_tab = 20;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	include "function/support_helper.php";
	require 'function/product_helper.php';
	require 'function/qc_helper.php';
	require 'function/order_helper.php';
	function get_temp_qty($id_order, $id_product_attribute)
	{
		$qty = 0;
		$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
		if(dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			$qty = $rs['qty'];
		}
		return $qty;
	}
	 function show_discount($percent, $amount)
		 {
			 $unit 	= " %";
			 $dis	= 0.00;
			if($percent != 0.00){ $dis = $percent; }else{ $dis = number_format($amount, 2); $unit = ""; }
			return $dis.$unit;
		 }
	
	function get_sold_data($id_order, $id_product_attribute)
	{
		$rs = false;
		$qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
		if( dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);	
		}
		return $rs;
	}

	?>
<div class="container">
<!-- page place holder -->
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-file-text-o"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
       <p class="pull-right top-p">
       <?php if(isset($_GET['view_detail'])&&isset($_GET['id_order'])) : ?>
		    <a href='index.php?content=order_closed' style="text-decoration:none:"><button type='button' class='btn btn-warning'><i class="fa fa-arrow-left" style="margin-right:5px;"></i>กลับ</button></a>   		
		<?php else : ?>
        <button type="button" class="btn btn-sm btn-warning" onclick="clearFilter()"><i class="fa fa-refresh"></i> Reset</button>               
	   <?php endif; ?>
       </p>
    </div>
</div>
<hr />
<!-- End page place holder -->
<!------------------------------------------ แสดงรายละเอียด ------------------------------------>
<?php if(isset($_GET['print_packing_list']) && isset( $_GET['id_order']) ) : ?>
	<div class="col-lg-12">
<?php	
	$id_order = $_GET['id_order'];
	$qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order);       
	if(dbNumRows($qs) > 0 ) :
		$i = 1; 
		while($ro = dbFetchArray($qs)) :
			$qo = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$ro['id_box']." AND valid = 1"); 
			$rs = dbFetchArray($qo);  
?>		<a href='controller/qcController.php?print_packing_list&id_order=<?php echo $id_order; ?>&id_box=<?php echo $ro['id_box']; ?>&number=<?php echo $i; ?>' target='_blank'>
			<button type='button' id='print_<?php echo $ro['id_box']; ?>' class="btn btn-success" >
            	<i class="fa fa-print" style="margin-right:5px;"></i>กล่องที่ <?php echo $i; ?> :  <span id='<?php echo $ro['id_box']; ?>'><?php echo $rs['qty']; ?></span> pcs.
             </button>  
 		</a>
<?php   $i++;  
	endwhile;
	else :
			echo "ยังไม่มีการตรวจสินค้าหรือไม่ได้ใช้ระบบกล่อง";
	endif;
	?>   
	</div>	
<?php elseif(isset($_GET['view_detail'])&&isset($_GET['id_order'])) :
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$bill_discount = bill_discount($id_order);
	$order = new order($id_order);
	$role = $order->role;
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
?>	
<?php if( $order->current_state == 9 || $order->current_state == 10 ) : ?>
<?php if($role == 3) {
				$reference = $order->reference; 
				$cus_label = " ";
				$cus_info = "";
				$em_label = "ผู้ยืม : ";
				$em_info = employee_name($order->id_employee);
			}else if($role == 7) {
				$reference = $order->reference; 
				$cus_label = "  &nbsp;ผู้รับ : ";
				$cus_info = $customer->full_name;
				$em_label = "ผู้เบิก : ";
				$em_info = employee_name($order->id_employee);
				$user = employee_name(get_id_user_support($id_order));
			}else if($role == 2 || $role == 6){
				$reference = $order->reference; 
				$cus_label = "  &nbsp;ลูกค้า : ";
				$cus_info = $customer->full_name;
				$em_label = "ผู้เบิก : ";
				$em_info = employee_name($order->id_employee);
			}else{
				$reference = $order->reference;
				$cus_label = "ลูกค้า : ";
				$cus_info = $customer->full_name;
				$em_label = "พนักงานขาย : ";
				$em_info = $sale->full_name;
			}	
			
			$invoice = getInvoice($id_order);
?>	
	  <div class='row'>
        	<div class='col-lg-2'>	<strong><?php echo $reference; ?></strong></div>
            <div class="col-lg-4"><strong><?php echo $cus_label . $cus_info; ?></strong></div>
            <div class="col-lg-6"><strong><p class="pull-right"><?php echo $em_label . $em_info; ?></p></strong> </div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo thaiDate($order->date_add); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
        <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:0px'>อ้างอิง : &nbsp;</dt>
        <dd style='float:left; margin:0px; padding-right:10px'>
        	<span id="invoice_<?php echo $id_order; ?>"><?php echo $invoice; ?> </span>
			<?php if( $invoice != "" ) : ?>
            	<button type="button" class="btn btn-warning btn-mini" onclick="editInvoice(<?php echo $id_order; ?>)">แก้ไข</button>
                <button type="button" class="btn btn-danger btn-mini" onclick="deleteInvoice(<?php echo $id_order; ?>)">ลบ</button>
            <?php endif; ?>
        </dd>  |</dt></dl>
<?php if($order->role == 7) : ?>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ผู้ดำเนินการ : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo $user; ?></dd> </dt></dl>    
<?php endif; ?>            
		
        <?php if($order->current_state == 9) : ?>
        	<p class="pull-right top-p">
            	<button type="button" class="btn btn-primary btn-sm" onClick="printBill(<?php echo $id_order; ?>)"><i class="fa fa-print"></i> พิมพ์</button>
                <button type="button" class="btn btn-success btn-sm" onClick="printBarcode(<?php echo $id_order; ?>)"><i class="fa fa-print"></i> พิมพ์บาร์โค้ด</button>
                <button type="button" class="btn btn-default btn-sm" onClick="printPackingList(<?php echo $id_order; ?>)"><i class="fa fa-file-text-o"></i> Picking List</button>
                <button type="button" class="btn btn-info btn-sm" onClick="printAddress(<?php echo $id_order; ?>, <?php echo $order->id_customer; ?>)"><i class="fa fa-file-text-o"></i> พิมพ์ใบปะหน้า</button>
            </p>
            <?php if( $order->payment == 'ออนไลน์' ) : ?>
            	<input type="hidden" name="online" id="online" value="1" />
            <?php endif; ?>
        <?php endif; ?>      
		</div></div>
		<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
		
		<div class='row'>
        <div class='col-lg-12'>
        <table class="table table-bordered">
        <thead>
        	<th style="width:4%; text-align:center">ลำดับ</th>
            <th style="width:10%; text-align:center">บาร์โค้ด</th>
            <th style="width:30%; text-align:center">สินค้า</th>
            <th style="width:10%; text-align:center">ราคา</th>
            <th style="width:8%; text-align:center">จำนวนสั่ง</th>
            <th style="width:8%; text-align:center">จำนวนจัด</th>
            <th style="width:8%; text-align:center">จำนวนที่ได้</th>
            <th style="width:10%; text-align:center">ส่วนลด</th>
            <th style="width:10%; text-align:center">มูลค่า</th>
		</thead>     
        <!------------------------------  Start  ------------------>
           
        <?php if($role != 5 ) : ?>  
		<?php	$qr = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);  ?>
        <?php 	$n = 1; $total_amount = 0; $total_discount = 0; $full_amount = 0; $total_qty = 0; $total_valid_qty = 0; $total_temp= 0; ?>
        <?php 	while($rr = dbFetchArray($qr) ) : 													?>
        <?php 		$isVisual = isVisual($rr['id_product_attribute']) == 1 ? TRUE : FALSE; ?>
        <?php 		$order_qty 	= $rr['product_qty']; 												?>
        <?php 		$id_product_attribute = $rr['id_product_attribute']; 						?>
        <?php		$sold 	= $isVisual === TRUE ? FALSE : get_sold_data($id_order, $id_product_attribute);	?>
        <?php		$temp_qty 	= $isVisual === TRUE ? $order_qty : get_temp_qty($id_order, $rr['id_product_attribute']); 	?>
        <?php 		$sold_qty 	= $isVisual === TRUE ? $order_qty : ($sold === FALSE ? 0 : $sold['sold_qty'] );			?>
        <?php 		if($order_qty != $sold_qty || $sold_qty != $temp_qty ) { $hilight = " color: red;"; }else{ $hilight = ""; } ?>
        <?php 		$p_name = $rr['product_reference']. " : ". $rr['product_name']; 			?>
         <?php 		$p_name = substr($p_name, 0, 100); 											?>
         <?php		$discount = $sold == false ? show_discount($rr['reduction_percent'], $rr['reduction_amount']) : show_discount($sold['reduction_percent'], $sold['reduction_amount']); ?>
         
        <tr style="font-size:12px;<?php echo $hilight; ?>">
        	<td align="center"><?php echo $n; ?></td>
            <td align="center"><?php echo $rr['barcode']; ?></td>
            <td><?php echo $p_name; ?></td>
            <td align="center"><?php echo number_format($rr['product_price'],2); ?></td>
            <td align="center"><?php echo number_format($order_qty); ?></td>
            <td align="center"><?php echo number_format($temp_qty); ?></td>
            <td align="center"><?php echo number_format($sold_qty); ?></td>
            <td align="center"><?php echo $discount; ?></td>
            <td align="right"><?php echo number_format($sold == false ? 0.00 : $sold['total_amount'],2); ?></td>
        </tr>
        <?php 
			$total_discount 	+= $sold == false ? 0.00 : $sold['discount_amount'];  
			$total_amount 		+= $sold == false ? 0.00 : $sold['total_amount']; 
			$full_amount 		+= $sold_qty * $rr['product_price']; 
			$total_qty 			+= $order_qty; 
			$total_valid_qty 	+= $sold_qty; 
			$total_temp			+= $temp_qty;
			$n ++; 
			?>
        <?php 	endwhile; ?>
        <?php else: ?>
        <?php $qr = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order); ?>
        <?php 	$n = 1; $total_amount = 0; $total_discount = 0; $full_amount = 0; $total_qty = 0; $total_valid_qty = 0;  $total_temp = 0; ?>
        <?php 	while($rs = dbFetchArray($qr) ) : ?>
       	<?php		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_product_attribute = ".$rs['id_product_attribute']." AND id_order = ".$id_order." AND valid = 1")); ?>
        <?php			$temp_qty = get_temp_qty($id_order, $rs['id_product_attribute']); ?>
        <?php		if($rs['product_qty'] != $qty || $qty != $temp_qty){ $hilight = " color: red;"; }else{ $hilight = ""; } ?>
         <?php 			$p_name = $rs['product_reference']. " : ". $rs['product_name']; ?>
         <?php 			$p_name = substr($p_name, 0, 100); ?>
         
		<tr style="font-size:12px; <?php echo $hilight; ?>">
        	<td align="center"><?php echo $n; ?></td>
            <td align="center"><?php echo $rs['barcode']; ?></td>
            <td><?php echo $p_name; ?></td>
            <td align="center"><?php echo number_format($rs['product_price'],2); ?></td>
            <td align="center"><?php echo number_format($rs['product_qty']); ?></td>
            <td align="center"><?php echo number_format($temp_qty); ?></td>
            <td align="center"><?php echo number_format($qty); ?></td>
            <td align="center">
			<?php 
				if($rs['reduction_percent'] != 0.00){ 
						$amount = $qty * $rs['product_price'];
						$discount = $rs['reduction_percent']." %"; 
						$discount_amount = $qty * ($rs['product_price'] * ($rs['reduction_percent']/100)); 
					}else if($rs['reduction_amount'] != 0.00){ 
						$amount = $qty * $rs['product_price'];
						$discount = ($qty * $rs['reduction_amount']) . " ฿";
						$discount_amount = $qty * $rs['reduction_amount'];
					}else{
						$discount = "0.00 %";
						$discount_amount = 0;
						$amount = $qty * $rs['product_price']; 
					}
				
				echo $discount;
			?>
            </td>
            <td align="right"><?php echo number_format(($amount - $discount_amount),2); ?></td>
        </tr>
        <?php 	
				$total_amount += $amount; 
				$total_discount += $discount_amount; 
				$full_amount += $amount;  
				$total_qty += $rs['product_qty']; 
				$total_valid_qty += $qty;  
				$total_temp	+= $temp_qty;
				$n++; 
		?>
        <?php 	endwhile; ?>
        <?php endif; ?> 
         <tr>
        	<td colspan="4" align="right">รวม</td>
            <td align="center"><?php echo number_format($total_qty); ?></td>
            <td align="center"><?php echo number_format($total_temp); ?></td>
            <td align="center"><?php echo number_format($total_valid_qty); ?></td>
            <td >ส่วนลดท้ายบิล</td>
            <td align="right"><?php echo number_format($bill_discount, 2); ?></td>
        </tr>
        <tr >
        	<td colspan="4" rowspan="3"><strong>หมายเหตุ : </strong><?php echo $order->comment; ?></td>
            <td colspan="3"><strong>ราคารวม</strong></td><td colspan="2" align="right"><?php echo number_format($full_amount,2); ?></td>
        </tr>
        <tr>
        	<td colspan="3"><strong>ส่วนลด</strong></td><td colspan="2" align="right"><?php echo number_format($total_discount + $bill_discount, 2); ?></td>
        </tr>
         <tr>
        	<td colspan="3"><strong>ยอดเงินสุทธิ</strong></td><td colspan="2" align="right"><?php echo number_format($full_amount - ($total_discount + $bill_discount) ,2); ?></td>
        </tr>
        </table>
        </div>
        </div>
        
        <!------------------------------- end --------------------------->
	<?php else : ?>
    	<h3 style="text-align:center; margin-top:100px; color:red;" ><i class="fa fa-exclamation-triangle fa-2x"></i></h3>
        <h4 style="text-align:center; margin-top:5px; color:red;" >สถานะออเดอร์ไม่ถูกต้อง</h4>
	<?php endif; ?>        
	
<?php else : ?>
<!----------------------------------------------------- แสดงรายการ -------------------------------------------------->
<?php
	$ds	= array(
								'fromDate' 	=> isset( $_POST['from_date'] ) ? $_POST['from_date'] : ( getCookie('fromDate') ? getCookie('fromDate') : '' ),
								'toDate'		=> isset( $_POST['to_date'] ) ? $_POST['to_date'] : ( getCookie('toDate') ? getCookie('toDate') : '' ),
								'reference'	=> isset( $_POST['reference'] ) ? $_POST['reference'] : ( getCookie('reference') ? getCookie('reference') : '' ),
								'invoice'		=> isset( $_POST['invoice_no'] ) ? $_POST['invoice_no'] : ( getCookie('invoice_no') ? getCookie('invoice_no') : ''),
								'cusName'	=> isset( $_POST['cusName'] ) ? $_POST['cusName'] : ( getCookie('cusName') ? getCookie('cusName') : '' ),
								'empName'	=> isset( $_POST['empName'] ) ? $_POST['empName'] : ( getCookie('empName') ? getCookie('empName') : '' )
						);
	$os	= array(							
								'order'		=> isset( $_POST['order'] ) ? $_POST['order'] : ( getCookie('order') ? getCookie('order') : 0),
								'consign'		=> isset( $_POST['consign'] ) ? $_POST['consign'] : ( getCookie('consign') ? getCookie('consign') : 0 ),
								'sponsor'		=> isset( $_POST['sponsor'] ) ? $_POST['sponsor'] : ( getCookie('sponsor') ? getCookie('sponsor') : 0 ),
								'support'		=> isset( $_POST['support'] ) ? $_POST['support'] : ( getCookie('support') ? getCookie('support') : 0 ),
								'reform'		=> isset( $_POST['reform'] ) ? $_POST['reform'] : ( getCookie('reform') ? getCookie('reform') : 0 ),
								'sortDate'	=> isset( $_POST['sortDate'] ) ? $_POST['sortDate'] : ( getCookie('sortDate') ? getCookie('sortDate') : 0 ), //--- 0 => sort by date_add,  1 => sort by date_upd
								'noInvoice'	=> isset( $_POST['noInvoice'] ) ? $_POST['noInvoice'] : ( getCookie('noInvoice') ? getCookie('noInvoice') : 0)
						);
							
	$btn = array();		
	$role_in = '';				
	foreach( $ds as $key => $val )
	{
		if( $val !== '' )
		{
			createCookie($key, $val);
		}
	}
	
	foreach( $os as $key => $val )
	{
		createCookie($key, $val);
		$btn[$key]	= $val != 0 ? 'btn-primary' : '';
		$role_in 		.= $key != 'sortDate' ? ($key != 'noInvoice' ?  ($val == 0 ? '' : ($val == 26 ? '2,6,' : $val.',')) : '') : '';
	}
	$role_in = trim($role_in, ',');
	$dateAdd = $os['sortDate'] == 0 ? 'btn-primary' : '';
	$dateUpd = $os['sortDate'] == 1 ? 'btn-primary' : '';
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50 );
	$paginator->setcookie_rows($get_rows);
?>

<form  method='post' id='form'>
<div class="row">
	<div class="col-sm-2 padding-5 first">
    	<label>เอกสาร</label>
        <input type="text" class="form-control input-sm filter" name="reference" id="reference" placeholder="ค้นเลขที่เอกสาร" value="<?php echo $ds['reference']; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label>เลขที่อ้างอิง</label>
        <input type="text" class="form-control input-sm filter" name="invoice_no" id="invoice_no" placeholder="ค้นเลขที่อ้างอิง" value="<?php echo $ds['invoice']; ?>" />
    </div>    
    <div class="col-sm-2 padding-5">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm filter" name="cusName" id="cusName" placeholder="ค้นหาชื่อลูกค้า" value="<?php echo $ds['cusName']; ?>" />
    </div>
    <div class="col-sm-2 padding-5">
    	<label>พนักงาน</label>
        <input type="text" class="form-control input-sm filter" name="empName" id="empName" placeholder="ค้นหาชื่อพนักงาน" value="<?php echo $ds['empName']; ?>" />        
    </div>
    <div class="col-sm-2 padding-5">
    	<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" name="from_date" id="from_date" value="<?php echo $ds['fromDate']; ?>" />
        <input type="text" class="form-control input-sm input-unit text-center" name="to_date" id="to_date" value="<?php echo $ds['toDate']; ?>" />
    </div>
    <div class="col-sm-2 padding-5 last">
    	<label style="display:block; visibility:hidden">sort date</label>
        <div class="btn-group width-100">
    		<button type="button" class="btn btn-sm width-50 <?php echo $dateAdd; ?>" id="btn-dateAdd" onclick="toggleDate('dateAdd')">วันที่เอกสาร</button>
            <button type="button" class="btn btn-sm width-50 <?php echo $dateUpd; ?>" id="btn-dateUpd" onclick="toggleDate('dateUpd')">วันที่ปรับปรุง</button>
		</div>            
    </div>
    <div class="col-sm-4 padding-5 first">
    	<label style="display:block; visibility:hidden;">วันที่</label>
    	<div class="btn-group width-100">
            <button type="button" class="btn btn-sm width-20 <?php echo $btn['order']; ?>" id="btn-order" onclick="toggleOrderType('order', 1)">ขาย</button>
            <button type="button" class="btn btn-sm width-20 <?php echo $btn['consign']; ?>" id="btn-consign" onclick="toggleOrderType('consign', 5)">ฝากขาย</button>
            <button type="button" class="btn btn-sm width-20 <?php echo $btn['sponsor']; ?>" id="btn-sponsor" onclick="toggleOrderType('sponsor', 4)">สโมสร</button>
            <button type="button" class="btn btn-sm width-20 <?php echo $btn['support']; ?>" id="btn-support" onclick="toggleOrderType('support', 7)">อภินันท์</button>
            <button type="button" class="btn btn-sm width-20 <?php echo $btn['reform']; ?>" id="btn-reform" onclick="toggleOrderType('reform', 26)">แปรสภาพ</button> 
        </div>
    </div>
    <div class="col-sm-1">
    	<label style="display:block; visibility:hidden;">noinvoice</label>
    	<button type="button" class="btn btn-sm <?php echo $btn['noInvoice']; ?>" id="btn-noInvoice" onclick="toggleNoInvoice()">ไม่มีเลขที่อ้างอิง</button>
    </div>
    <input type="hidden" name="order" id="order" value="<?php echo $os['order']; ?>" />
    <input type="hidden" name="consign" id="consign" value="<?php echo $os['consign']; ?>" />
    <input type="hidden" name="sponsor" id="sponsor" value="<?php echo $os['sponsor']; ?>" />
    <input type="hidden" name="support" id="support" value="<?php echo $os['support']; ?>" />
    <input type="hidden" name="reform" id="reform" value="<?php echo $os['reform']; ?>" />
    <input type="hidden" name="sortDate" id="sortDate" value="<?php echo $os['sortDate']; ?>" />
    <input type="hidden" name="noInvoice" id="noInvoice" value="<?php echo $os['noInvoice']; ?>" />
    
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
		
		
		
		/****  เงื่อนไขการแสดงผล *****/
		
	$where = "WHERE current_state = 9 ";
	if( $ds['reference'] != '' )
	{
		$where .= "AND reference LIKE '%".$ds['reference']."%' ";	
	}
	
	if( $ds['invoice'] != "" )
	{
		$where .= "AND invoice LIKE '%".$ds['invoice']."%' ";	
	}
	
	if( $os['noInvoice'] != 0 )
	{
		$where .= "AND invoice IS NULL ";
	}
	
	if( $ds['cusName'] != '' )
	{
		$cus_in 	= customer_in($ds['cusName']); //---- tools.php
		$where .= $cus_in === FALSE ? '' : "AND id_customer IN(".$cus_in.") ";	
	}
	
	if( $ds['empName'] != '' ) 
	{
		$emp_in = employee_in($ds['empName']); //---- tools.php
		$where 	.= $emp_in === FALSE ? '' : "AND id_employee IN(".$emp_in.") ";
	}
	
	if( $ds['fromDate'] != '' && $ds['toDate'] != '')
	{
		$from	= fromDate($ds['fromDate']);
		$to	= toDate($ds['toDate']);
		if( $os['sortDate'] == '0' )
		{
			$where .= "AND date_add >= '".$from."' AND date_add <= '".$to."' ";
		}
		else
		{
			$where .= "AND tbl_order.date_upd >= '".$from."' AND tbl_order.date_upd <= '".$to."' ";	
		}
	}
	
	if( $role_in != '')
	{
		$where .= "AND role IN(".$role_in.") ";	
	}
	$where .= "ORDER BY tbl_order.date_upd DESC ";
	
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50 );
	$paginator->setcookie_rows($get_rows);
	$paginator->Per_Page('tbl_order LEFT JOIN tbl_order_invoice ON tbl_order.id_order = tbl_order_invoice.id_order ', $where, $get_rows);
	$paginator->display($get_rows, 'index.php?content=order_closed');
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
	//echo "SELECT tbl_order.*, invoice FROM tbl_order LEFT JOIN tbl_order_invoice ON tbl_order.id_order = tbl_order_invoice.id_order " . $where . " LIMIT ". $paginator->Page_Start .", ". $paginator->Per_Page;
?>		
<div class='row'>
<div class='col-sm-12'>
	<table class='table table-striped table-bordered'>
    	<thead style="font-size:12px;">
        	<th style='width:5%; text-align:center;'>ลำดับ</th>
			<th style='width:10%; text-align:center;'>เลขที่เอกสาร</th>
            <th style='width:20%; text-align:center;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:10%; text-align:center;'>เงื่อนไข</th>
			<th style='width:10%; text-align:center;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่เอกสาร</th>
			<th style='width:15%; text-align:center;'>วันที่ปรับปรุง</th>
            <th style='width:10%; text-align:center;'>เลขที่อ้างอิง</th>
        </thead>      
<?php  	$qs	= dbQuery("SELECT tbl_order.*, invoice FROM tbl_order LEFT JOIN tbl_order_invoice ON tbl_order.id_order = tbl_order_invoice.id_order " . $where . " LIMIT ". $paginator->Page_Start .", ". $paginator->Per_Page);	?>
<?php 	$n 	= 1;		?>
<?php	if(dbNumRows($qs) > 0) :	?>
<?php		while( $rs = dbFetchObject($qs) ) :			?>
			<tr style="font-size:12px;">
            	<td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo $n; ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo $rs->reference; ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo customer_name($rs->id_customer); ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo number_format(orderAmount($rs->id_order), 2); ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo $rs->payment; ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo employee_name($rs->id_employee); ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo thaiDate($rs->date_add); ?></td>
                <td align="center" class="pointer" onclick="viewOrder(<?php echo $rs->id_order; ?>)"><?php echo thaiDateTime($rs->date_upd); ?></td>
                <td align="center">
                	<?php if( ! is_null($rs->invoice) ) : ?>
                    <?php	echo $rs->invoice; ?>	
                    <?php else : ?>
                    <input type="text" class="form-control input-sm invoice" id="invoice_<?php echo $rs->id_order; ?>" />
                    <span id="span_invoice_<?php echo $rs->id_order; ?>" class="hide"></span>
                    <?php endif; ?>
                </td>
            </tr>
<?php	$n++;  ?>
<?php 	endwhile;  ?>		
<?php endif;  ?>
	</table>
<?php	echo $paginator->display_pages(); ?>
<h3>&nbsp;</h3>
<?php endif; ?>
	<div class='modal fade' id='infoModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:500px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <input type="hidden" id="id_customer"/><input type="hidden" id="id_order" />
				 </div>
				 <div class='modal-body' id='info_body'>
                 	
                 </div>
				 <div class='modal-footer'>
                 	<button type="button" class="btn btn-primary btn-sm" onClick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
				 </div>
			</div>
		</div>
	</div>
    
    <div class='modal fade' id='invoiceModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:250px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title-site text-center' > แก้ไขเลขที่อ้างอิง </h4>
                    <input type="hidden" id="id_order_inv" />
				 </div>
				 <div class='modal-body'>
                 <input type="text" class="form-control input-sm" id="input-invoice" />    
                 <button type="button" class="btn btn-primary btn-sm pull-right top-col" onClick="updateInvoice()"><i class="fa fa-save"></i> บันทึก</button>             	
                 </div>
				 <div class='modal-footer'>
				 </div>
			</div>
		</div>
	</div>
    
<script>
function deleteInvoice(id) {
    swal({
        title: 'ต้องการลบ ?',
        text: 'คุณแน่ใจว่าต้องการลบเลขที่อ้างอิงนี้ ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6855',
        confirmButtonText: 'ใช่ ฉันต้องการลบ',
        cancelButtonText: 'ยกเลิก',
        closeOnConfirm: false
    }, function() {
        $.ajax({
            url: "controller/orderController.php?deleteInvoice",
            type: "POST",
            cache: "false",
            data: { "id_order": id },
            success: function(rs) {
                var rs = $.trim(rs);
                if (rs == 'success') {
                    swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
					setTimeout(function(){ window.location.reload(); }, 2000);
                } else {
                    swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
                }
            }
        });
    });
}
function editInvoice(id)
{
	var invoice = $.trim($("#invoice_"+id).text());
	$("#input-invoice").val(invoice);
	$("#id_order_inv").val(id);
	$("#invoiceModal").modal('show');	
	$("#invoiceModal").on('shown.bs.modal', function(){
		$("#input-invoice").focus();
	});
}

function updateInvoice()
{
	$("#invoiceModal").modal('hide');
	var id = $("#id_order_inv").val();
	var invoice	= $("#input-invoice").val();
	if( invoice.length > 4 && id_order != "")
	{
		$.ajax({
			url:"controller/orderController.php?updateInvoice",
			type:"POST", cache:"false", data:{ "invoice" : invoice, "id_order" : id },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' )
				{
					swal({ title: 'เรียบร้อย', text: 'แก้ไขข้อมูลเรียบร้อยแล้ว', type: 'success', timer: 2000 });
					setTimeout(function(){ window.location.reload(); }, 3000);
				}
				else
				{
					swal("ข้อผิดพลาด", "แก้ไขข้อมูลไม่สำเร็จ", "error");	
				}
			}
		});
	}
}

$(".invoice").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		var name = $(this).attr('id');
		var arr = name.split('_');
		var id_order = arr[1];
		saveInvoice(name, id_order);
	}
});
function saveInvoice(name, id)
{
	var invoice = $("#"+name).val();
	if( invoice.length > 4 ){
		$.ajax({
			url:"controller/orderController.php?updateInvoice",
			type:"POST", cache:"false", data:{ "invoice" : invoice, "id_order" : id },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					$("#span_"+name).text(invoice);
					$("#"+name).remove();
					$("#span_"+name).removeClass('hide');
					$('.invoice:first').focus();	
				}else{
					swal("ข้อผิดพลาด", "บันทึกข้อมูลไม่สำเร็จ", "error");
				}
			}
		});
	}
}

function toggleNoInvoice()
{
	if( $("#noInvoice").val() == 0 )
	{
		$("#noInvoice").val(1);
		$("#btn-noInvoice").addClass('btn-primary');	
	}else{
		$("#noInvoice").val(0);
		$("#btn-noInvoice").removeClass('btn-primary');	
	}
	getSearch();
}

function toggleDate(type)
{
	if( type == 'dateAdd')
	{
		$("#sortDate").val(0);
		$("#btn-dateUpd").removeClass('btn-primary');
		$("#btn-dateAdd").addClass('btn-primary');
	}else{
		$("#sortDate").val(1);
		$("#btn-dateAdd").removeClass('btn-primary');
		$("#btn-dateUpd").addClass('btn-primary');
	}
	getSearch();
}


function toggleOrderType(type, id)
{
	if( $("#"+type).val() == 0 ){
		$("#"+type).val(id);
		$("#btn-"+type).addClass("btn-primary");
	}else{
		$("#"+type).val(0);
		$("#btn-"+type).removeClass("btn-primary");
	}
	getSearch();
}

$(".filter").keyup(function(e) {
    if( e.keyCode == 13 ){
		getSearch();
	}
});

function viewOrder(id_order)
{
	window.location.href = "index.php?content=order_closed&view_detail&id_order="+id_order;
}

function printBill(id_order)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/billController.php?print_order&id_order="+id_order, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}
 
function printBarcode(id_order)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/billController.php?print_order_barcode&id_order="+id_order, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printPackingList(id_order)
{
	window.open("index.php?content=order_closed&print_packing_list&id_order="+id_order, "_blank");
}

function printAddress(id_order, id_customer)
{
	if( $("#online").length ){
		getOnlineAddress(id_order);
	}else{
		getAddressForm(id_order, id_customer);	
	}
}

function getOnlineAddress(id_order)
{
	$.ajax({
		url:"controller/orderController.php?getOnlineAddress",
		type:"POST", cache:"false", data:{"id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'noaddress' || isNaN( parseInt(rs) ) ){
				noAddress();
			}else{
				printOnlineAddress(id_order, rs);
			}
		}
	});
}
function getAddressForm(id_order, id_customer)
{
	$.ajax({
		url:"controller/addressController.php?getAddressForm",
		type:"POST",cache: "false", data:{ "id_order" : id_order, "id_customer" : id_customer },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'no_address' ){
				noAddress();
			}else if( rs == 'no_sender' ){
				noSender();
			}else if( rs == 1 ){
				printPackingSheet(id_order, id_customer);
			}else{
				$("#id_customer").val(id_customer);
				$("#id_order").val(id_order);
				$("#info_body").html(rs);
				$("#infoModal").modal("show");
			}
		}
	});
}

function printPackingSheet(id_order, id_customer)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printAddressSheet&id_order="+id_order+"&id_customer="+id_customer, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printOnlineAddress(id_order, id_address)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printOnlineAddressSheet&id_order="+id_order+"&id_address="+id_address, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printSelectAddress()
{
	var id_order = $("#id_order").val();
	var id_cus = $("#id_customer").val();
	var id_ad =	$('input[name=id_address]:radio:checked').val();
	var id_sen	= $('input[name=id_sender]:radio:checked').val();
	if( isNaN(parseInt(id_ad)) ){ swal("กรุณาเลือกที่อยู่", "", "warning"); return false; }
	if( isNaN(parseInt(id_sen)) ){ swal("กรุณาเลือกขนส่ง", "", "warning"); return false; }
	$("#infoModal").modal('hide');
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printAddressSheet&id_order="+id_order+"&id_customer="+id_cus+"&id_address="+id_ad+"&id_sender="+id_sen, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}
function noAddress()
{
	swal("ข้อผิดพลาด", "ไม่พบที่อยู่ของลูกค้า กรุณาตรวจสอบว่าลูกค้ามีที่อยู่ในระบบแล้วหรือยัง", "warning");	
}
function noSender()
{
	swal("ไม่พบผู้จัดส่ง", "ไม่พบรายชื่อผู้จัดส่ง กรุณาตรวจสอบว่าลูกค้ามีการกำหนดชื่อผู้จัดส่งในระบบแล้วหรือยัง", "warning");	
}

$("#from_date").datepicker({
     dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
       $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
	   if( $(this).val() !== "" && $("#to_date").val() == ""){
		   $("#to_date").focus();
	   }else if( $(this).val() !== "" && $("#to_date").val() !== ""){
		   getSearch();
	   }
     }
 });
$( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
		if( $(this).val() !== "" && $("#from_date").val() !== "" ){
			getSearch();
		}
      }
 });

function getSearch()
{
	var from = $("#from_date").val();
	var to 	= $("#to_date").val();
	if( (from !== "" || to !== "" ) && ( isDate(from) == false || isDate(to) == false ) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;	
	}
	$("#form").submit();
}

function clearFilter()
{
	$.ajax({
		url:"controller/billController.php?clear_filter",
		type:"GET", cache:"false", success: function(rs){
			window.location.href = "index.php?content=order_closed";
		}
	});
}

</script>