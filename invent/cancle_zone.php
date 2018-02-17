<?php 
	$page_name = "ตรวจสอบโซนยกเลิก";
	$id_tab = 9;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  
	?>

<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-lg-8 col-sm-12 col-xs-12" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-4 col-sm-12 col-xs-12">
       <p class="pull-right" style="margin-bottom:0px;">
       <?php if( ! isset( $_GET['verify'] ) && isset($_GET['under_zero'] ) ) : ?>
       <button type="button" class="btn btn-primary btn-sm" onclick="all_items()"><i class="fa fa-list"></i> รายการทั้งหมด</button>
       <?php endif; ?>
       <?php if( ! isset( $_GET['verify'] ) && ! isset( $_GET['under_zero'] ) ) : ?>
       	<button type="button" class="btn btn-info btn-sm" onclick="get_under_zero()"><i class="fa fa-list"></i> รายการที่ติดลบ</button>
       <?php endif; ?>
       <?php if( ! isset( $_GET['verify'] ) ) : ?>
       	<button type="button" class="btn btn-default btn-sm" onClick="verify()">การตรวจสอบ</button>
       <?php else : ?>
       <button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
       <?php endif; ?>
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->
<?php if( isset( $_GET['under_zero'] ) ) : ?>

<style>
label {
	font-weight:normal;
}
</style>
<div class="row">
    <div class='col-sm-12'>
    	<p class="pull-right" style="margin-bottom:5px;"><button type="button" class="btn btn-danger btn-sm" onclick="delete_checked()"><i class="fa fa-trash"></i> ลบที่เลือก</button></p>
        <form id="cancle_form">
        <table class="table table-striped" style="border-top:solid 1px #ccc;">
            <thead>
            <th style="width:5%; text-align:center" ><input type="checkbox" id="check_all" /></th>
            <th style="width:10%">บาร์โค้ด</th>
            <th style="width:30%;">สินค้า</th>
            <th style="width:5%; text-align:right">จำนวน</th>
            <th style="width:15%; text-align:center">จากออเดอร์</th>
            <th style="width: 20%; text-align:center;">จากโซน</th>
            <th style="text-align:right;">การกระทำ</th>
            </thead>
<?php $qs = dbQuery("SELECT * FROM tbl_cancle WHERE qty < 0 ORDER BY id_order"); ?>
<?php if( dbNumRows($qs) > 0 ) : ?>

<?php 	while( $rs = dbFetchArray($qs) ) : ?>
            <tr id="row_<?php echo $rs['id_cancle']; ?>">
                <td align="center"><input type="checkbox" class="check_cancle" name="check[<?php echo $rs['id_cancle']; ?>]" id="check_<?php echo $rs['id_cancle']; ?>" value="<?php echo $rs['id_cancle']; ?>" /></td>
                <td><label for="check_<?php echo $rs['id_cancle']; ?>"><?php echo get_barcode($rs['id_product_attribute']); ?></label></td>
                <td><label for="check_<?php echo $rs['id_cancle']; ?>"><?php echo get_product_reference($rs['id_product_attribute']); ?></label></td>
                <td align="right"><label for="check_<?php echo $rs['id_cancle']; ?>"><?php echo $rs['qty']; ?></label></td>
                <td align="center"><label for="check_<?php echo $rs['id_cancle']; ?>"><?php echo get_order_reference($rs['id_order']); ?></label></td>
                <td align="center"><label for="check_<?php echo $rs['id_cancle']; ?>"><?php echo get_zone($rs['id_zone']); ?></label></td>
                <td align="right"><button type="button" class="btn btn-danger btn-xs" onclick="delete_row(<?php echo $rs['id_cancle']; ?>)"><i class="fa fa-trash"></i> ลบ</button></td>
            </tr>
<?php 	endwhile; ?>		
<?php else : ?>
		<tr><td colspan="7" align="center"><h4>-----  ไม่มีรายการติดลบ -----</h4></td></tr>
<?php endif; ?>            
		</table>
        </form>
	</div>
</div>	  

<?php elseif( isset( $_GET['verify'] ) ) : ?>
<div class="row">
	<div class="col-lg-6">
	<?php $qs = dbQuery("SELECT id_order FROM tbl_cancle GROUP BY id_order"); ?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    	<table class="table table-striped">
        <thead>
        	<th style="width:5%; text-align:center;">ลำดับ</th><th>เอกสาร</th><th style="width:20%">ตรวจสอบ</th>
        </thead>
        <tbody>
	<?php $n = 1; ?>        
	<?php while( $rs = dbFetchArray($qs) ) : ?>
    <?php $ref = get_order_reference($rs['id_order']); ?>
    	<tr id="row_<?php echo $rs['id_order']; ?>">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $ref; ?></td>
            <td><button type="button" class="btn btn-sm btn-default" onClick="verifyOrder(<?php echo $rs['id_order']; ?>, '<?php echo $ref; ?>')">ตรวจสอบ</button></td>
        </tr>
	<?php	$n++; ?>        
    <?php endwhile; ?>      
        </tbody>
        </table>
    <?php endif; ?>
     </div>
</div>      

<div class='modal fade' id='orderVerfy' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:800px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
				 </div>
				 <div class='modal-body' id='modal_body'></div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				 </div>
			</div>
		</div>
	</div>
        
<?php else : ?>
<div class="row">
<div class='col-sm-12'>
<table class="table table-striped">
<thead>
<th style="width:5%; text-align:center" >ลำดับ</th>
<th style="width:10%">บาร์โค้ด</th>
<th style="width:40%;">สินค้า</th>
<th style="width:5%; text-align:right">จำนวน</th>
<th style="width:15%; text-align:center">จากออเดอร์</th>
<th>จากโซน</th>
</thead>
<?php $sql = dbQuery("SELECT * FROM tbl_cancle"); ?>
<?php $row = dbNumRows($sql); ?>
<?php if($row > 0 ) : ?>
	<?php $n = 1; ?>
	<?php while($rs = dbFetchArray($sql)) : ?>
    <tr>
    	<td align="center"><?php echo $n; ?></td>
        <td><?php echo get_barcode($rs['id_product_attribute']); ?></td>
        <td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
        <td align="right"><?php echo $rs['qty']; ?></td>
        <td align="center"><?php echo get_order_reference($rs['id_order']); ?></td>
        <td><?php echo get_zone($rs['id_zone']); ?></td>
    </tr>
    <?php  $n++; ?>
    <?php endwhile; ?>

<?php else : ?>
	<tr><td colspan="6" align="center"><h4>----------  ไม่มีสินค้า  ----------</h4></td></tr>
<?php endif; ?>
</table>
</div>
<?php endif; ?>
</div>
<script>
function verifyOrder(id, ref)
{
	$.ajax({
		url: "controller/storeController.php?verifyOrder",
		type:"POST", cache: "false", data: { "id_order" : id },
		success: function(rs){
			$("#modal_title").text(ref);
			$("#modal_body	").html(rs);
			$("#orderVerfy").modal("show");
		}
	});
}

function removeCancle(id)
{
	$("#orderVerfy").modal("hide");
	$.ajax({
		url:"controller/storeController.php?removeItemsFromCancleZone",
		type:"POST", cache:"false", data:{ "id_order" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({title: "เรียบร้อย", timer: 1000, type: "success"});
				$("#row_"+id).remove();
			}
			else
			{
				swal("ไม่สำเร็จ", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function deleteCancleItem(id_order, id_pa)
{
	$.ajax({
		url:"controller/storeController.php?deleteCancleItem",
		type:"POST", cache: "false", data:{ "id_order" : id_order, "id_product_attribute" : id_pa },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				$("#row_"+id_order+"_"+id_pa).remove();
			}
		}
	});
}

function verify()
{
	window.location.href = "index.php?content=cancle_zone&verify";	
}


function goBack()
{
	window.location.href = "index.php?content=cancle_zone";	
}


function delete_checked()
{
	var i = 0;
	$(".check_cancle").each(function(index, element) {
        if($(this).is(":checked")){ i++; }
    });	
	if(i == 0 ){ swal("คุณยังไม่ได้เลือกรายการที่ต้องการลบ"); return false; }
	load_in();
	$.ajax({
		url:"controller/billController.php?delete_checked_cancle_item",
		type: "POST", cache:"false", data: $("#cancle_form").serialize(),
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				swal({ tile: "สำเร็จ", text: "ลบรายการที่เลือกเรียบร้อย", timer: 1000, type: "success" });
				window.location.reload();
			}
			else
			{
				swal("ไม่สำเร็จ", "ลบรายการที่เลือกไม่สำเร็จ", "error");
				window.location.reload();
			}
		}
		
	});	
}
function delete_row(id)
{
	load_in();
	$.ajax({
		url:"controller/billController.php?delete_cancle_item&id_cancle="	+id,
		type:"GET", cache:"false", success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				$("#row_"+id).remove();
				swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อย", timer: 1000, type: "success"});
			}
			else
			{
				swal("ไม่สำเร็จ", "ลบรายการไม่สำเร็จ", "error");
			}
		}
	});
}

$("#check_all").change(function(e) {
    if($(this).is(":checked")){
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});


function get_under_zero()
{
	window.location.href = "index.php?content=cancle_zone&under_zero";	
}
function all_items()
{
	window.location.href = "index.php?content=cancle_zone";	
}
</script>