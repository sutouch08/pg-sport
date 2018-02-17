<?php 
	$page_menu = "invent_order_qc";
	$page_name = "ตรวจสินค้า";
	$id_tab = 18;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	include 'function/qc_helper.php';
	$ps = checkAccess($id_profile, 58);
	$suppervisor = $ps['add'] + $ps['edit'] + $ps['delete'] > 0 ? TRUE : FALSE;
	   ?>
<div class="container">
<!-- page place holder -->
<div class="row top-row">
	<div class="col-lg-6 top-col"><h4 class="title"><i class="fa fa-check-square-o"></i>&nbsp;<?php echo $page_name; ?></h4> </div>
    <div class="col-lg-6">
       <p class="pull-right top-p">
	    <?php if( isset( $_GET['id_order'] ) OR isset( $_GET['process'] ) ) : ?>
    			<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php endif; ?>	     
           
	<?php if( ! isset( $_GET['id_order'] ) && ! isset( $_GET['process'] ) ) : ?>
    <?php	if( $add ) : ?>
    				<button type="button" class="btn btn-info btn-sm" onClick="orderInProcess()"><i class="fa fa-hourglass-half"></i> รายการกำลังตรวจ</button>
	<?php	endif; ?>                   
    <?php endif; ?>	
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />


<?php if( isset( $_GET['id_order'] ) ) :	
	$id_order 	= $_GET['id_order'];
	$id_user 	= getCookie('user_id');
	$order 		= new order($id_order);
	$customer 	= new customer($order->id_customer);
	$id_box		= isset( $_GET['id_box'] ) ? $_GET['id_box'] : '' ;
	$cfe			= isset( $_GET['confirm_error'] ) ?  $_GET['confirm_error'] : '';
	$id_zone		= isset( $_GET['id_zone'] ) ? $_GET['id_zone'] : '';
	$id_pa		= isset( $_GET['id_product_attribute'] ) ? $_GET['id_product_attribute'] : '';
	$zoneName	= $id_zone != '' ? get_zone($id_zone) : '';
	
	$idc = dbNumRows(dbQuery("SELECT id_order_state_change FROM tbl_order_state_change WHERE id_order = $id_order AND id_order_state = 11 AND id_employee != $id_user"));
	$ids = dbNumRows(dbQuery("SELECT id_order_state_change FROM tbl_order_state_change WHERE id_order = $id_order AND id_order_state = 11 AND id_employee = $id_user"));
	?>

<?php if( $suppervisor === FALSE ) : ?> 
	<?php if($idc > 0 && $ids < 1) : ?>
    <script type="text/javascript">
		window.location = "index.php?content=qc&error=เออเดอร์นี้มีคนตรวจแล้ว";	
	</script>
	<?php endif; ?>
<?php endif; ?>
<?php if( $id_box == '' ) : ?> <input type="hidden" id="box_detect" /><?php endif; ?>
	<div class="row">
		<div class="col-xs-2">เลขที่ : <?php echo $order->reference; ?></div>
        <div class="col-xs-2">ลูกค้า : <?php echo $customer->full_name; ?></div>
        <div class="col-xs-2">วันที่สั่ง : <?php echo thaiDate($order->date_add); ?></div>	
		<div class="col-xs-2">จำนวนรายการ : <?php echo $order->total_product; ?> รายการ </div>
        <div class="col-xs-2">จำนวนตัว : <?php echo $order->total_qty; ?> ตัว </div>
        <div class="col-xs-2">มูลค่า : <?php echo number_format($order->total_amount,2); ?> </div>
		<div class="col-xs-12">&nbsp;</div>
		<div class="col-xs-12">หมายเหตุ : <?php echo $order->comment; ?></div>
        <div class="col-xs-12">&nbsp;</div>
        <div class="col-xs-12">  
        <p class="pull-right" id="print_row" >
<?php $qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order); ?>        
<?php if(dbNumRows($qs) > 0 ) : ?>
<?php 	$i = 1; ?>
<?php 	while($ro = dbFetchArray($qs)) : ?>
<?php 	$qo = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$ro['id_box']." AND valid = 1"); ?>
<?php 	$rs = dbFetchArray($qo) ; ?>
<a href="controller/qcController.php?print_packing_list&id_order=<?php echo $id_order; ?>&id_box=<?php echo $ro['id_box']; ?>&number=<?php echo $i; ?>" target="_blank">
 <button type="button" id="print_<?php echo $ro['id_box']; ?>" class="btn btn-success" ><i class="fa fa-print"></i>&nbsp;กล่องที่<?php echo $i; ?> :  <span id="<?php echo $ro['id_box'];?>"><?php echo $rs['qty']; ?></span> pcs.</button>  
 </a>          
            <?php $i++; ?>   
<?php 	endwhile; ?>
<?php else : ?>
			ยังไม่มีการตรวจสินค้า
<?php endif; ?>    
			</p>     
            </div>
	</div>
	
	<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:15px;" />
    <input type="hidden" id="id_user" name="id_user" value="<?php echo $id_user; ?>"/>
    <input type="hidden" id="id_order" name="id_order" value="<?php echo $id_order; ?>" />
    <input type="hidden" id="id_box" name="id_box" value="<?php echo $id_box; ?>" />
    <input type="hidden" id="id_customer" name="id_customer" value="<?php echo $order->id_customer; ?>" />
	<div class="row">
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">บาร์โค้ดกล่อง</span>
                <input type="text" name="barcode_box" id="barcode_box" class="form-control input-sm" autocomplete="off" <?php echo $id_box == '' ? 'autofocus' : 'disabled' ;  ?>  />
            </div>
        </div>
        <div class="col-xs-3">
            <div class="input-group">
                <span class="input-group-addon">บาร์โค้ดสินค้า</span>
                <input type="text" id="barcode_item" name="barcode_item" class="form-control input-sm" autocomplete="off" <?php echo $id_box != '' ? 'autofocus' : 'disabled'; ?>  />
            </div> 
        </div>
        <div class="col-xs-2" id="load">
            <button type="button" class="btn btn-default btn-sm" id="add" onclick="qc_process()" >ตกลง</button>
        </div>
        <div class="col-xs-2">
            <button type="button" class="btn btn-warning btn-sm" id="change_box" onclick="change_box()" ><i class="fa fa-refresh"></i>&nbsp; เปลี่ยนกล่อง</button>
        </div>
        <div class="col-xs-2">
            <button type="button" class="btn btn-info btn-sm" id="btnPrintAddress" onclick="printAddress(<?php echo $id_order; ?>, <?php echo $order->id_customer; ?>)" ><i class="fa fa-refresh"></i>&nbsp; เปลี่ยนกล่อง</button>
        </div>
	</div>
	<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:15px;" />	
	
	
	<div id="value">
	<table class='table' id='table1'>
	<thead id='head'>
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th>
        <th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th>
        <th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	
        <th style='width:10%; text-align:center;'>ตรวจแล้ว</th>
        <th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>
    <?php
	if($order->current_state == 5)
	{ 
		$order->state_change($order->id_order, 11, $id_user); 
	}
	
	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);
	$row1 = 0;
	while( $rs = dbFetchObject( $qs ) ) :
		$product 	= new product();
		$prepared	= sumPreparedQty($id_order, $rs->id_product_attribute);
		$checked	= sumCheckedQty($id_order, $rs->id_product_attribute);
		$balance_qty = $prepared - $checked;
		if( $prepared != $checked)	:	?>
        	<tr id="row<?php echo $rs->id_product_attribute; ?>" <?php echo $rs->product_qty > $prepared ? 'style="color:#FF0000;"' : ''; ?>>
            	<td align="center" class="barcode"><?php echo $rs->barcode; ?></td>
                <td><?php echo $rs->product_reference ." : ".$rs->product_name; ?></td>
                <td align="center"><?php echo number_format( $rs->product_qty ); ?></td>
                <td align="center"><p id="prepare<?php echo $rs->id_product_attribute; ?>"><?php echo number_format($prepared); ?></p></td>
                <td align="center" id="checked<?php echo $rs->id_product_attribute; ?>"><?php echo number_format($checked); ?></td>
                <td align="center">
                <?php if( $checked > $rs->product_qty ) : ?>
                	<button type="button" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> แก้ไข</button>
                    <input type="hidden" name="must_edit" value="1" />
				<?php else : ?>
                	<button type="button" class="btn btn-xs btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="<?php echo product_from_zone($id_order, $rs->id_product_attribute); ?>">
                    	จากโซน
                    </button>
                <?php endif; ?>	
                </td>
            </tr>
<?php	$row1++;				?>
<?php	endif; 					?>
<?php	endwhile;				?>
			<tr id="finish" style="display:none;">
            	<td colspan="6" align="center">
                	<button type="button" class="btn btn-sm btn-success" onClick="closeJob(<?php echo $id_order; ?>, <?php echo $id_user; ?>)">ตรวจเสร็จแล้ว</button>
                </td>
            </tr>
			<tr id="force_close" style="display:none;">
            	<td colspan="6"  align="center">
            		<label style="display:block;"><input type='checkbox' id='checkboxes' onChange="getcondition()" /> สินค้ามีไม่ครบ</label>
            		<button type="button" class="btn btn-sm btn-success" id="btn_close_job" style="display:none;"  onClick="closeJob(<?php echo $id_order; ?>, <?php echo $id_user; ?>)">บังคับจบ</button>
				</td>
			</tr>                                   
		</table>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'>
    	<div class='col-xs-12'><h4 style='text-align:center;'>รายการที่ครบแล้ว</h4></div>
    </div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<table class='table'>
	<thead id='head2'>
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th>
        <th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th>
        <th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	
        <th style='width:10%; text-align:center;'>ตรวจแล้ว</th>
        <th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>
<?php    
	
	$qs = "SELECT SUM(qty) AS qty, tbl_qc.id_product_attribute AS id_pa, product_reference AS reference, product_name, barcode ";
	$qs .= "FROM tbl_qc ";
	$qs .= "JOIN tbl_order_detail ON tbl_qc.id_product_attribute = tbl_order_detail.id_product_attribute ";
	$qs .= "AND tbl_qc.id_order = tbl_order_detail.id_order ";
	$qs .= "WHERE tbl_qc.id_order = ".$id_order." AND valid = 1 GROUP BY tbl_qc.id_product_attribute ORDER BY tbl_qc.id_product_attribute ASC";
	$qs = dbQuery($qs);
	$n=0;
	if( dbNumRows($qs) > 0 ) : 
		while($rs = dbFetchObject( $qs ) ) : 
			$id_pa  		= $rs->id_pa;
			$order_qty 	= sumOrderQty($id_order, $id_pa);
			$product		= new product();
			$prepared	= sumPreparedQty($id_order, $id_pa);
			$checked	= sumCheckedQty($id_order, $id_pa);
			$balance_qty = $prepared - $checked;
			if( $prepared == $checked OR ($order_qty != 0 && $order_qty < $checked) ) :	?>
            <tr id="row<?php echo $id_pa; ?>" <?php echo $order_qty > $prepared ? 'style="color:#FF0000;"' : ''; ?>>
            	<td align="center"><?php echo $rs->barcode; ?></td>
                <td><?php echo $rs->reference . ' " '.$rs->product_name;  ?></td>
                <td align="center"><?php echo number_format( $order_qty ); ?></td>
                <td align="center"><?php echo number_format($prepared); ?></td>
                <td align="center" id="checked<?php echo $id_pa; ?>"><?php echo number_format($checked); ?></td>
                <td align="center">
                <?php if( $checked > $order_qty ) : ?>
                	<button type="button" class="btn btn-xs btn-warning" onClick="getItemChecked(<?php echo $id_order; ?>, <?php echo $id_pa; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
                    <input type="hidden" id="must_edit_<?php echo $id_pa; ?>" name="must_edit" value="1" />
				<?php else : ?>
                	<button type="button" class="btn btn-xs btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="<?php echo product_from_zone($id_order, $id_pa); ?>">
                    	จากโซน
                    </button>
                <?php endif; ?>	
                </td>
            </tr>
<?php	elseif( number_format( $order_qty ) == 0 ) :	?>
			<tr id="row<?php echo $id_pa; ?>" <?php echo $order_qty > $prepared ? 'style="color:#FF0000;"' : ''; ?>>
            	<td align="center"><?php echo $rs->barcode; ?></td>
                <td><?php echo $rs->reference . ' " '.$rs->product_name; ?></td>
                <td align="center"><?php echo number_format( $order_qty ); ?></td>
                <td align="center"><?php echo number_format($prepared); ?></td>
                <td align="center" id="checked<?php echo $id_pa; ?>"><?php echo number_format($checked); ?></td>
                <td align="center">
                    <button type='button' class='btn btn-xs btn-danger' onClick="moveToCancle(<?php echo $id_order; ?>, <?php echo $id_pa; ?>)">ย้ายไปโซนยกเลิก</button>
                    <input type="hidden" id="must_edit_<?php echo $id_pa; ?>" name="must_edit" value="1" />
                </td>
            </tr>
<?php		endif; ?>		
<?php	endwhile; ?>
<?php	endif; ?>
	</table>	
<div>

<script id="template" type="text/x-handlebars-template">
 	{{#each this}}
	{{#if nocontent}}
		{{ nocontent }}
	{{else}}
		<a href="controller/qcController.php?print_packing_list&id_order=<?php echo $id_order; ?>&id_box={{id_box}}&number={{i}}" target="_blank">
 		<button type="button" id="print_{{id_box}}" class="btn {{class}}" ><i class="fa fa-print"></i>&nbsp;กล่องที่{{i}} :  <span id="{{id_box}}">{{qty}}</span> pcs.</button>  
 		</a> 
		
	{{/if}}
	{{/each}}	 
</script>
<script id="rowTemplate" type="text/x-handlebars-template">
<td align="center">{{ barcode }}</td>
<td>{{ product }}</td>
<td align="center">{{ orderQty }}</td>
<td align="center">{{ prepared }}</td>
<td align="center" id="checked{{ id_pa }}"> {{ checked }}</td>
<td align="center">
{{#if must_edit }}
<button type="button" class="btn btn-xs btn-warning" onClick="getItemChecked({{ id_order }}, {{ id_pa }})"><i class="fa fa-pencil"></i> แก้ไข</button>
<input type="hidden" id="must_edit" name="must_edit" value="1" />
{{else}}
<button type="button" class="btn btn-xs btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="{{{ content }}}">จากโซน</button>
{{/if}}
</td>
</script>
<!-------------------------------------------  	รายการระหว่างจัด  ------------------------------------------------------>
<?php elseif(isset($_GET['process'])) :  /////  ?>
<div class="row">
	<div class="col-xs-12">
		<table class="table">
		<thead>
			<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">เลขที่เอกสาร</th>
			<th style="width: 15%; text-align:center;">ลูกค้า</th>
            <th style="width: 15%; text-align:center;">รูปแบบ</th>
            <th style="width: 15%; text-align:center;">วันที่สั่ง</th>
            <th style="width: 20%; text-align:center;">พนักงานจัด</th>
            <th style="width: 5%; text-align:center;">&nbsp;</th>
		</thead>
<?php         
		$sql = dbQuery("SELECT tbl_order.id_order, tbl_temp.id_employee FROM tbl_order LEFT JOIN tbl_temp ON tbl_order.id_order = tbl_temp.id_order WHERE current_state = 11  GROUP BY tbl_order.id_order");
		$n = 1;
		while($row = dbFetchArray($sql)) :
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			$employee = new employee($row['id_employee']); ?>
			<tr>
				<td align="center"><?php echo $n; ?></td>
				<td align="center"><?php echo $order->reference; ?></td>
				<td align="center"><?php echo $customer->full_name; ?></td>
				<td align="center"><?php echo $order->role_name; ?></td>
				<td align="center"><?php echo thaiDate($order->date_add); ?></td>
				<td align="center"><?php echo $employee->full_name; ?></td>
				<td align="center"><a href="index.php?content=qc&process=y&id_order=<?php echo $order->id_order; ?>"><span class="btn btn-default">ตรวจสินค้าต่อ</span></a></td>
			</tr>
		<?php $n++; ?>
<?php endwhile; ?>
		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->
<script>
setInterval(function(){ window.location.reload(); }, 60000);
</script>
<!----------------------------------------------  จบรายการระหว่างจัด  ----------------------------------------------->
<?php  else : ?>
<!----------------------------------------------- แสดงรายการรอตรวจ ------------------------------------------------>

<div class="row">
	<div class="col-xs-12" id="reload">
		<table class="table">
		<thead>
			<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">เลขที่เอกสาร</th>
			<th style="width: 15%; text-align:center;">ลูกค้า</th>
            <th style="width: 15%; text-align:center;">รูปแบบ</th>
            <th style="width: 15%; text-align:center;">วันที่สั่ง</th>
			<th style="width: 15%; text-align:center;">พนักงานจัด</th>
            <th style="width: 15%; text-align:center;">&nbsp;</th>
		</thead>
<?php        
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 5");
		$n = 1;
		while($row = dbFetchArray($sql)) :
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			list($id_employee) = dbFetchArray(dbQuery("SELECT id_employee FROM tbl_temp WHERE id_order =".$order->id_order." AND status = 1 GROUP BY id_employee"));
			$employee = new employee($id_employee); ?>
			<tr>
				<td align="center"><?php echo $n; ?></td>
				<td align="center"><?php echo $order->reference; ?></td>
				<td align="center"><?php echo $customer->full_name; ?></td>
				<td align="center"><?php echo $order->role_name; ?></td>
				<td align="center"><?php echo thaiDate($order->date_add); ?></td>
				<td align="center"><?php echo $employee->full_name; ?></td>
				<td align="center"><a href="index.php?content=qc&process=y&id_order=<?php echo $order->id_order; ?>"><span class='btn btn-default'>ตรวจสินค้า</span></a></td>
			</tr>
		<?php $n++; ?>
<?php endwhile; ?>
		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->

<script>
setInterval(function(){ window.location.reload(); }, 60000);
</script>
<?php
endif;
?>

<div class="modal" id="qc_error" tabindex="-1" role="dialog" aria-labelledby="qcModal" data-backdrop="static">
  <div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel">&nbsp;</h4>
      </div>
      <div class="modal-body">
        <center><h4 id="error_label">Error</h4></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <div class='modal fade' id='edit_qc' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
        <div class='modal-dialog' id='modal'>
            <div class='modal-content'>
                <div class='modal-header'>
                	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
               	 <h4 class='modal-title' id='modal_title'></h4><input type='hidden' id="id_pa" name='id_pa' value='' />
                </div>
                <div class='modal-body' id='modal_body'></div>
                <div class='modal-footer'>
                	<button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>ปิด</button>
                	<button type='button' class='btn btn-sm btn-primary' onClick="confirmEdit()">แก้ไขรายการ</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class='modal fade' id='infoModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:500px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				 </div>
				 <div class='modal-body' id='info_body'>
                 	
                 </div>
				 <div class='modal-footer'>
                 	<button type="button" class="btn btn-primary btn-sm" onClick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
				 </div>
			</div>
		</div>
	</div>
<script>	
//----------------------------------------------  NEW CODE -------------------------------------------//

$(document).ready(function(e) {
    validQc();
});

function validQc()
{
	count = $("#table1 td").size();
	if(count<3){
		$("#force_close").css("display","none");
		$("#finish").css("display", "");
	}else{
		$("#force_close").css("display","");
		$("#finish").css("display", "none");
		 if( $("#box_detect").length ){ swal("ยิงบาร์โค้ดกล่องก่อน"); }
	}
}

$(".barcode").click(function(e) {
    var barcode = $.trim($(this).text());
	$("#barcode_item").val(barcode);
	$("#barcode_item").focus();
});
//-----------------------  Edit Qc --------------------//
function getItemChecked(id_order, id_pa)
{
	$.ajax({
		url:"controller/qcController.php?getItemChecked",
		type:"POST", cache:"false", data:{ "id_order" : id_order, "id_product_attribute" : id_pa },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '' ){
				$("#modal").css("width", "500px");
				$("#modal_title").text('แก้ไขรายการ QC เกิน');
				$("#id_pa").val(id_pa);
				$("#modal_body").html(rs);
				$("#edit_qc").modal('show');
			}else{
				swal("ไม่พบข้อมูล", "ไม่พบข้อมูลการตรวจของสินค้าที่เลือก", "error");
			}
		}
	});
}

function confirmEdit()
{
	var ci		= $(".edit-qc:checked").length;
	if( ci == 0 ){ 
		$("#edit_qc").modal('hide');
		swal({ title: "Warning!!", text: "กรุณาเลือกรายการที่ต้องการเอาออก"}, function(){ $("#edit_qc").modal("show"); }); 
		return false; 
	}
	$.ajax({
		url:"controller/qcController.php?editQc",
		type:"POST", cache:"false", data: $(".edit-qc:checked").serialize(),
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'done' )
			{
				var id_pa = $("#id_pa").val();
				var id_order = $("#id_order").val();
				updateQcChecked(id_order, id_pa);
			}
		}
	});		
}

function updateQcChecked(id_order, id_pa)
{
	$.ajax({
		url:"controller/qcController.php?getRowQcChecked",
		type:"POST", cache:"false", data:{ "id_order" : id_order, "id_product_attribute" : id_pa },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '' ){
				var source = $("#rowTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#row"+id_pa);
				render(source, data, output);
				setPopover(); /// set pop over on button
				validQc();
			}else{
				window.location.reload();
			}
		}
	});
}
//------------------  Clear cancle product to cancle zone ----------------//
function moveToCancle(id_order, id_pa)
{
	load_in();
	$.ajax({
		url:"controller/qcController.php?clearCancleItem",
		type:"POST", cache:"false", data:{ "id_order" : id_order, "id_product_attribute" : id_pa },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				$("#row"+id_pa).remove();
				swal({ title: "สำเร็จ", text: "ย้ายรายการไปโซนยกเลิกเรียบร้อยแล้ว", timer: 1000, type: "success" });
				validQc();
			}else{
				swal("ข้อผิดพลาด", "ทำรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

//------------ Show order in process ----------//
function orderInProcess()
{
	window.location.href = 'index.php?content=qc&process';
}	

function goBack()
{
	window.location.href = 'index.php?content=qc';	
}

function goPrepare(id_order)
{
	window.location.href = 'index.php?content=prepare&process&id_order='+id_order;	
}


function closeJob(id_order, id_user)
{
	var diff = $("#mus_edit").length;
	if( diff > 0 )
	{
		swal("มีข้อผิดพลาด", "มีรายการสินค้าที่ถูกยกเลิก กรุณาแก้ไขรายการก่อนบันทึก", "error");	
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/qcController.php?closeQcJob",
		type:"POST", cache:"false", data:{ "id_order" : id_order, "id_user" : id_user },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: "สำเร็จ", text: "ปิดการตรวจเรียบร้อยแล้ว", timer: 1000, type: "success" });
				setTimeout(function(){ goBack(); }, 1500);
			}else if( rs == 'state_change' ){
				goBack();
			}else{
				swal("Error!!", "ปิดการจัดไม่สำเร็จ", "error");
			}
		}
	});
}


$(".btn-pop").mouseenter(function(e) {
    $(this).popover("show");
});
$(".btn-pop").mouseleave(function(e) {
    $(this).popover("hide");
});

function setPopover()
{
	$(".btn-pop").mouseenter(function(e) {
		$(this).popover("show");
	});
	$(".btn-pop").mouseleave(function(e) {
		$(this).popover("hide");
	});	
}

function getcondition(){
	if( $("#checkboxes").prop("checked") )
	{
		
		$("#btn_close_job").css("display", "");	
	}else{
		$("#btn_close_job").css("display","none");
	}
}

function printAddress(id_order, id_customer)
{
	$.ajax({
		url:"controller/addressController.php?getAddressForm",
		type:"POST",cache: "false", data:{ "id_customer" : id_customer },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'no_address' ){
				noAddress();
			}else if( rs == 'no_sender' ){
				noSender();
			}else if( rs == 1 ){
				printPackingSheet(id_order, id_customer);
			}else{
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


function printSelectAddress()
{
	var id_order	= $("#id_order").val();
	var id_cus		= $("#id_customer").val();
	var id_ad 	=	$('input[name=id_address]:radio:checked').val();
	var id_sen	= $('input[name=id_sender]:radio:checked').val();
	if( isNaN(parseInt(id_ad)) ){ swal("กรุณาเลือกที่อยู่", "", "warning"); return false; }
	if( isNaN(parseInt(id_sen)) ){ swal("กรุณาเลือกขนส่ง", "", "warning"); return false; }
	$("#infoModal").modal('hide');
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printAddress&id_order="+id_order+"&id_customer="+id_cus+"&id_address="+id_ad+"&id_sender="+id_sen, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}
function noAddress()
{
	swal("ข้อผิดพลาด", "ไม่พบที่อยู่ของลูกค้า กรุณาตรวจสอบว่าลูกค้ามีที่อยู่ในระบบแล้วหรือยัง", "warning");	
}
function noSender()
{
	swal("ไม่พบผู้จัดส่ง", "ไม่พบรายชื่อผู้จัดส่ง กรุณาตรวจสอบว่าลูกค้ามีการกำหนดชื่อผู้จัดส่งในระบบแล้วหรือยัง", "warning");	
}


//---------------------------------------------------  END NEW CODE --------------------------------------------------//

$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        qc_process();
    }
});		

$("#barcode_box").keyup(function(e){
    if(e.keyCode == 13)
    {
        get_box();
    }
});	

function change_box(){
	$("#barcode_item").attr("disabled", "disabled");
	$("#id_box").val("");
	$("#barcode_box").removeAttr("disabled");
	$("#barcode_box").val("");
	swal("ยิงบาร์โค้ดกล่องก่อน");
	$("#barcode_box").focus();
}

function get_box(){	
    var code = $("#barcode_box").val();
	if(code ==""){
		swal("ยังไม่ได้ระบุบาร์โค้ดกล่อง");
		$("#barcode_box").focus();
	}else{
		var id_order = $("#id_order").val();
		$.ajax({
			url: "controller/qcController.php?add_box",
			type:"GET", cache:false, data:{"id_order" : id_order, "barcode_box" : code},
			success: function(rs){  ///// ได้ id_box กลับมา ในรูปแบบ   success : id_box  หากกล่องนั้นถูกส่งไปแล้ว ( valid = 1)  closed : id_box  หากไม่สำเร็จ  fail : xx
				var arr = rs.split(" : ");
				var status = $.trim(arr[0]);
				var id_box = $.trim(arr[1]);
				if(status == "success"){
					$("#id_box").val(id_box);
					$("#barcode_box").attr("disabled", "disabled");
					$("#barcode_item").removeAttr("disabled");
					$("#barcode_item").focus();
					update_box(id_box);
					$("#box_detect").remove();
				}else if(status == "closed"){
					swal("กล่องนี้ถูกบันทึกว่าจัดส่งไปแล้ว");					
				}else{
					swal("เพิ่มกล่องเข้าระบบไม่สำเร็จ");
				}
			}
		});
	}
}

function qc_process(){
	var barcode_item = $("#barcode_item").val();
	var id_box = $("#id_box").val();
	var id_order = $("#id_order").val();
	var id_user = $("#id_user").val();
	if(barcode_item != "")
	{
		$("#barcode_item").attr("disabled", "disabled");
		$("#barcode_item").val('');
		//$("#add").focus();
		$("#add").html("<i class='fa fa-spinner fa-spin'></i>");
		$.ajax({
		url:"controller/qcController.php?checked=y",
		data: {"id_order" : id_order, "id_user" : id_user, "barcode_item" : barcode_item, "id_box" : id_box},
		type:"GET", cache:false, 
		success: function(dataset){
			dataset = $.trim(dataset);
			arr = dataset.split(":");
			if(arr[0].trim()=="ok"){
				$("#add").html("ตกลง");
				id_product_attribute = arr[1];
				qc_qty = parseInt(arr[2]);
				pre_qty = parseInt($("#prepare"+id_product_attribute).html());
				if(qc_qty == pre_qty){
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head2"));
				}else{
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head"));
				}
				var is = parseInt($("#"+id_box).html());
				$("#"+id_box).html(is+1);
				$("#barcode_item").removeAttr("disabled");
				$("#barcode_item").focus();
			}else{
				$("#add").html("ตกลง");
				error = arr[1];
				beep();
				$("#error_label").text(error);
				$("#qc_error").modal("show");				
			}
			validQc();
			$("#barcode_item").focus();
		}
	});
	}else{
		swal("คุณยังไม่ได้ใส่บาร์โค้ด");
		$("#barcode_item").focus();
	}
	
}

$("#qc_error").on("hidden.bs.modal", function(e){
	$("#barcode_item").removeAttr("disabled"); 
	$("#barcode_item").focus();
});

$(document).ready(function(e) {
    if($("input[name='must_edit']").length){ 
		$("#checkboxes").css("display","none");
		$("#btn_close_job").css("display", "none");
		$("#force_close").html("<span style='color:red;'>รายการตรวจสินค้าที่ไม่ถูกต้อง จำเป็นต้องแก้ไขก่อนจึงจะทำงานลำดับต่อไปได้</span>");
	}
});

function update_box(id_box)
{
	var id_order = $("#id_order").val();
	$.ajax({
		url: "controller/qcController.php?update_box&id_order="+id_order+"&id_box="+id_box,
		type:"GET", cache:false,
		success: function(data){
			var source = $("#template").html();
			var data = $.parseJSON(data);
			var output = $("#print_row");
			render(source, data, output);
		}
	});
}

</script>
<script src="../library/js/beep.js"></script>
