<?php 
	$pageName	= "รายการยอดต่าง";
	$id_tab 		= 11;
	$id_profile 	= getCookie('profile_id');
    $pm 			= checkAccess($id_profile, $id_tab);
	$view 		= $pm['view'];
	$add 			= $pm['add'];
	$edit 			= $pm['edit'];
	$delete 		= $pm['delete'];
	accessDeny($view); 
	require 'function/adjust_helper.php';
	$qs = dbQuery("SELECT * FROM tbl_diff WHERE status_diff = 0");
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-adjust"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <?php if( isset( $_GET['id_adjust'] ) ) : ?>
        	<button type="button" class="btn btn-sm btn-warning" onClick="goBack(<?php echo $_GET['id_adjust']; ?>)"><i class="fa fa-arrow-left"></i> กลับ</button>
            <?php if( dbNumRows($qs) > 0 ) : ?>
            <button type="button" class="btn btn-sm btn-danger" onClick="confirmDelete()" ><i class="fa fa-trash"></i> ลบรายการ</button>
            <button type="button" class="btn btn-sm btn-info" onClick="toggleCheckAll()"><i class="fa fa-check-square"></i> <span id="btn-label">เลือกทั้งหมด</span></button>
            <button type="button" class="btn btn-sm btn-success" onClick="loadDiff(<?php echo $_GET['id_adjust']; ?>)"><i class="fa fa-cloud-upload"></i> นำเข้ายอดต่าง</button>
            <?php endif; ?>
		<?php else : ?>
        	<button type="button" class="btn btn-sm btn-warning" onClick="goToAdjust()"><i class="fa fa-arrow-left"></i> กลับ</button>
            <?php if( dbNumRows($qs) > 0 ) : ?>
        	<button type="button" class="btn btn-sm btn-danger" onClick="confirmDelete()" ><i class="fa fa-trash"></i> ลบรายการ</button>
            <button type="button" class="btn btn-sm btn-info" onClick="toggleCheckAll()"><i class="fa fa-check-square"></i> <span id="btn-label">เลือกทั้งหมด</span></button>      
            <?php endif; ?>
        <?php endif; ?>
        
        </p>
    </div>
</div><!--/ row -->
<hr/>
<?php if( isset( $_GET['id_adjust'] ) ) : ?>
<?php 	$id_adj = $_GET['id_adjust']; ?>
<?php 	$adj 	= new adjust($id_adj); ?>
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" value="<?php echo $adj->adjust_no; ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>เลขที่อ้างอิง</label>
        <input type="text" class="form-control input-sm" value="<?php echo $adj->adjust_reference; ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" value="<?php echo thaiDate($adj->adjust_date); ?>" disabled />
    </div>
    <div class="col-sm-6">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" value="<?php echo $adj->adjust_note; ?>" disabled />
    </div>
</div>
<hr/>
<?php endif; ?>
<div class="row">
	<div class="col-sm-12">
    <form id="formDiff">
    	<input type="hidden" name="id_adjust" value="<?php echo $id_adj; ?>" />
    	<table class="table table-stiped">
        	<thead>
            	<tr style="font-size:12px;">
                	<th style="width:5%; text-align:center;">
                    	<input type="checkbox" class="hide" id="check-all" onChange="doChecking()" style="margin-top:0px;" /> เลือก
                    </th>
                	<th style="width:5%; text-align:center;">ลำดับ</th>
                    <th style="width:25%;">สินค้า</th>
                    <th style="width:25%;">โซน</th>
                    <th style="width:10%; text-align:center;">จำนวนเพิ่ม</th>
                    <th style="width:10%; text-align:center;">จำนวนลด</th>
                    <th style="width:15%; text-align:center;">วันที่</th> 
                </tr>
            </thead>
            <tbody>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    <?php 	$n = 1;	?>
    <?php	while( $rs = dbFetchObject($qs) ) : ?>
    			<tr style="font-size:12px;">
                	<td align="center">
                    	<input type="checkbox" class="chk" id="diff_<?php echo $rs->id_diff; ?>" name="diff[<?php echo $rs->id_diff; ?>]" value="<?php echo $rs->id_diff; ?>" style="margin-top:0px;" />
                    </td>
                	<td align="center"><?php echo $n; ?></td>
                    <td>
                    	<label for="diff_<?php echo $rs->id_diff; ?>" style="margin-bottom:0px;"><?php echo get_product_reference($rs->id_product_attribute); ?></label>
                    </td>
                    <td><?php echo get_zone($rs->id_zone); ?></td>
                    <td align="center"><?php echo $rs->qty_add; ?></td>
                    <td align="center"><?php echo $rs->qty_minus; ?></td>
                    <td align="center"><?php echo thaiDateTime($rs->date_diff); ?></td>                    
                </tr>
    <?php	endwhile; ?>
    <?php endif; ?>            	
            </tbody>            
        </table>
        </form>
    </div>
</div>

</div><!--/ container -->
<script>
function confirmDelete()
{
	var count = $(".chk:checked").length;
	if( count < 1 )
	{
		swal("กรุณาเลือกรายการที่ต้องการลบ");
	}else{
		swal({
			title: 'ต้องการลบ ?',
			text: 'คุณแน่ใจว่าต้องการลบ ' + count + ' รายการที่เลือก ?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#DD6855',
			confirmButtonText: 'ใช่ ฉันต้องการลบ',
			cancelButtonText: 'ยกเลิก',
			closeOnConfirm: false
		}, function() {
			deleteDiff();
		});
	}
}

function deleteDiff()
{
	var ds = $("#formDiff").serialize();
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?deleteDiff",
		type:"POST", cache:"false", data: ds,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', text: 'ลบรายการที่เลือกเรียบร้อยแล้ว', type: 'success', timer: 1000 });
				setTimeout(function(){ window.location.reload(); }, 1500);
			}else{
				swal({ title: 'ข้อผิดพลาด', text: rs , type: 'error' }, function(){ window.location.reload(); });
			}
		}
	});
}


function loadDiff(id)
{
	$.ajax({
		url:"controller/productAdjustController.php?loadDiff",
		type:"POST", cache:"false", data: $("#formDiff").serialize(),
		success: function(rs){
			var rs = $.trim(rs);
			if(rs == 'success' ){
				goBack(id);
			}else{
				swal("ข้อผิดพลาด !", rs, "error");	
			}
		}
	});
}
function toggleCheckAll()
{
	$("#check-all").click();
	if( $("#check-all").is(":checked") ){
		$("#btn-label").text('ไม่เลือกทั้งหมด');
	}else{
		$("#btn-label").text('เลือกทั้งหมด');
	}
}

function doChecking()
{
	if( $("#check-all").is(":checked") ){
		$(".chk").prop("checked", true);
	}else{
		$(".chk").prop("checked", false);	
	}
}

function goBack(id)
{
	window.location.href = "index.php?content=ProductAdjust&add&id_adjust="+id;	
}

function goToAdjust()
{
	window.location.href = "index.php?content=ProductAdjust";	
}
</script>
