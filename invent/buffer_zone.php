<?php 
	$page_menu = "invent_product_move";
	$page_name = "ตรวจสอบ BUFFER ZONE";
	$id_tab = 9;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	?>

<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-lg-8 col-sm-12 col-xs-12" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-4 col-sm-12 col-xs-12">
       <p class="pull-right" style="margin-bottom:0px;">
       <?php if(isset($_GET['completed'])) : ?>
       <button type="button" class="btn btn-primary btn-sm" onclick="all_items()"><i class="fa fa-list"></i> รายการทั้งหมด</button>
       <?php else : ?>
       	<button type="button" class="btn btn-info btn-sm" onclick="find_completed()"><i class="fa fa-list"></i> รายการที่เปิดบิลแล้ว</button>
       <?php endif; ?>
       		
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->
<?php if( isset($_GET['completed']) ) : ?>
<div class="row">
<div class="col-lg-12">
	<table class="table table-striped">
    	<thead style="font-size:12px;">
            <th style="width: 30%;">เลขที่เอกสาร</th>
            <th style="width: 10%; text-align:right;">จำนวน</th>
            <th style="width: 20%; text-align:center;">สถานะ</th>
            <th style="text-align:right;">การกระทำ</th>        
        </thead>
<?php $qs = dbQuery("SELECT tbl_buffer.id_order, SUM(qty) AS qty, tbl_order.reference, current_state, tbl_order.date_upd FROM tbl_buffer JOIN tbl_order ON tbl_buffer.id_order = tbl_order.id_order WHERE tbl_order.current_state IN(9,8) GROUP BY tbl_buffer.id_order"); ?>
<?php if( dbNumRows($qs) > 0 ) : ?>
<?php	while( $rs = dbFetchArray($qs) ) : ?>			
			<tr id="row_<?php echo $rs['id_order']; ?>">
                <td><?php echo $rs['reference']; ?></td>
                <td align="right"><?php echo number_format($rs['qty']); ?></td>
                <td align="center"><?php echo current_order_state($rs['id_order']); ?></td>
                <td align="right"><button type="button" class="btn btn-danger btn-sm" onclick="clear_buffer(<?php echo $rs['id_order']; ?>)"><i class="fa fa-exclamation-triangle"></i> เคลีร์ยรายการที่ค้าง</button></td>
            </tr>    
<?php 	endwhile; ?>
<?php else : ?>
			<tr><td colspan="5" align="center"><h4>-----  ไม่มีรายการค้าง  -----</h4></td></tr>
<?php endif; ?>
</table>
</div>
</div>
<?php else : ?>
<div class="row">
<div class='col-sm-12'>
<table class="table table-striped">
<thead><th style="width:5%; text-align:center" >ลำดับ</th><th style="width:30%;">สินค้า</th><th style="width:10%; text-align:right">จำนวน</th><th style="width:15%; text-align:center">จากออเดอร์</th><th style="width:15%;">สถานะ</th><th>จากโซน</th></thead>
<?php $sql = dbQuery("SELECT * FROM tbl_buffer"); ?>
<?php $row = dbNumRows($sql); ?>
<?php if($row > 0 ) : ?>
	<?php $n = 1; ?>
	<?php while($rs = dbFetchArray($sql)) : ?>
    <tr>
    	<td align="center"><?php echo $n; ?></td>
        <td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
        <td align="right"><?php echo $rs['qty']; ?></td>
        <td align="center"><?php echo get_order_reference($rs['id_order']); ?></td>
        <td><?php echo current_order_state($rs['id_order']); ?></td>
        <td><?php echo get_zone($rs['id_zone']); ?></td>
    </tr>
    <?php 	$n++; ?>
    <?php endwhile; ?>

<?php else : ?>
	<tr><td colspan="6" align="center"><h4>----------  ไม่มีสินค้า  ----------</h4></td></tr>
<?php endif; ?>
</table>
</div>

<?php endif; ?>
</div>
<script>
function find_completed()
{
	window.location.href = "index.php?content=buffer_zone&completed";	
}
function all_items()
{
	window.location.href = "index.php?content=buffer_zone";	
}

function clear_buffer(id)
{
	load_in();
	$.ajax({
		url: "controller/billController.php?clear_buffer&id_order="+id,
		type:"GET", cache:"false", 
		success: function(rs)
		{
			load_out();
			window.location.reload();
		}
	});
}
</script>
