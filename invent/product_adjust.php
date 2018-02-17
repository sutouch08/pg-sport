<?php 
	$pageName	= "ปรับปรุงยอดสินค้า";
	$id_tab 		= 11;
	$id_profile 	= getCookie('profile_id');
    $pm 			= checkAccess($id_profile, $id_tab);
	$view 		= $pm['view'];
	$add 			= $pm['add'];
	$edit 			= $pm['edit'];
	$delete 		= $pm['delete'];
	accessDeny($view); 
	require 'function/adjust_helper.php';
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-adjust"></i> <?php echo $pageName; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) ) : ?>
        	<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
            <?php if( isset( $_GET['id_adjust'] ) ) : ?>
            <button type="button" class="btn btn-sm btn-primary" onclick="getDiff()"><i class="fa fa-list"></i> ยอดต่าง</button>
            <button type="button" class="btn btn-sm btn-info" onclick="unsaveAdjust()"><i class="fa fa-refresh"></i> ยกเลิกการปรับยอด</button>
            <button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if( ! isset( $_GET['add'] ) && ! isset( $_GET['edit'] ) && $add) : ?>
        	<button type="button" class="btn btn-sm btn-primary" onclick="viewDiff()"><i class="fa fa-list"></i> ยอดต่าง</button>
        	<button type="button" class="btn btn-sm btn-success" onClick="newAdjust()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
        <?php endif; ?>
        </p>
    </div>
</div><!--/ row -->
<hr/>
<?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) ) : ?>
<?php	$id 		= isset( $_GET['id_adjust'] ) ? $_GET['id_adjust'] : "";	?>
<?php	$ds 		= $id !== "" ? getAdjustData($id) : FALSE;					?>
<?php	$adj_no	= $ds !== FALSE ? $ds['adjust_no'] : ''; 					?>
<?php	$adj_ref	= $ds !== FALSE ? $ds['adjust_reference'] : '';			?>
<?php	$date		= $ds !== FALSE ? $ds['adjust_date'] : thaiDate();		?>
<?php	$remark	= $ds !== FALSE ? $ds['adjust_note'] : '';					?>
<?php	$dis		= $ds !== FALSE ? 'disabled' : '' ;							?>

<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" name="adj_no" id="adj_no" value="<?php echo $adj_no; ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>เลขที่อ้างอิง</label>
        <input type="text" class="form-control input-sm" name="adj_ref" id="adj_ref" autofocus value="<?php echo $adj_ref; ?>" <?php echo $dis; ?>  />
    </div>
    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo thaiDate($date); ?>" <?php echo $dis; ?> />
    </div>
    <div class="col-sm-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $remark; ?>" <?php echo $dis; ?> />
    </div>
    <div class="col-sm-1">
    	<label style="display:block; visibility:hidden;">btn</label>
    <?php if( $ds === FALSE && $add ) : ?>
    	<button type="button" class="btn btn-sm btn-success" onClick="addNewAdjust()"><i class="fa fa-save"></i> บันทึก</button>
	<?php else : ?>
    	<?php if( $edit ) : ?>
        	<button type="button" class="btn btn-sm btn-warning" id="btn-edit-header" onClick="editHeader()"><i class="fa fa-pencil"></i> แก้ไข</button>
            <button type="button" class="btn btn-sm btn-success hide" id="btn-update-header" onClick="updateHeader()"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>        
    <?php endif; ?>
    </div>
    <input type="hidden" name="id_adjust" id="id_adjust" value="<?php echo $id; ?>" />
</div><!--/ row -->
<hr class="margin-top-15"/>

<?php	if( isset( $_GET['id_adjust'] ) ) : ?>
	<div class="row">
    	<div class="col-sm-3">
        	<label>โซน</label>
            <input type="text" class="form-control input-sm" name="zoneName" id="zoneName" placeholder="ระบุชื่อโซนที่ต้องการปรับปรุง" autofocus />
        </div>
        <div class="col-sm-1">
        	<label style="display:block; visibility:hidden;">btn</label>
        	<button type="button" class="btn btn-sm btn-default" id="btn-setZone" onClick="setZone()">ตกลง</button>
            <button type="button" class="btn btn-sm btn-warning hide" id="btn-changeZone" onClick="changeZone()">เปลี่ยนโซน</button>
        </div>
        <div class="col-sm-2">
        	<label>รหัสสินค้า</label>
            <input type="text" class="form-control input-sm adj" name="paCode" id="paCode" placeholder="ระบุสินค้าที่ต้องการปรับปรุง" disabled />
        </div>
        <div class="col-sm-1">
        	<label>เพิ่ม</label>
            <input type="text" class="form-control input-sm adj" name="increase" id="increase" placeholder="จำนวน" disabled />
        </div>
        <div class="col-sm-1">
        	<label>ลด</label>
            <input type="text" class="form-control input-sm adj" name="decrease" id="decrease" placeholder="จำนวน" disabled />
        </div>
        <div class="col-sm-1">
        	<label style="display:block; visibility:hidden;">btn</label>
        	<button type="button" class="btn btn-sm btn-default adj" id="btn-insert" onClick="insertDetail()" disabled>ตกลง</button>
            <button type="button" class="btn btn-sm btn-warning hide" id="btn-update" onclick="updateDetail()" >ตกลง</button>
        </div>
        <input type="hidden" name="id_adjust_detail" id="id_adjust_detail" value="" />
        <input type="hidden" name="id_zone" id="id_zone" value="" />
        <input type="hidden" name="id_pa" id="id_pa" value="" />
        
    </div><!--/ row -->
    <hr class="margin-top-15"/>
    <div class="row">
    	<div class="col-sm-12">
        	<table class="table table-striped" style="border:solid 1px #CCC;">
            	<thead>
                	<tr style="font-size:12px;">
                    	<th style="width:5%; text-align:center;">ลำดับ</th>
                        <th style="width:15%; text-align:center;">บาร์โค้ด</th>
                        <th style="width:25%;">สินค้า</th>
                        <th style="width:25%; text-align:center;">โซน</th>
                        <th style="width:10%; text-align:center;">เพิ่ม</th>
                        <th style="width:10%; text-align:center;">ลด</th>
                        <th style="text-align:center;">การกระทำ</th>
                    </tr>
                </thead>
                 <tbody id="result">
	<?php $qs = dbQuery("SELECT * FROM tbl_adjust_detail WHERE id_adjust = ".$id); ?>           
    <?php if( dbNumRows($qs) > 0 ) : ?>     
    <?php	$n = 1 ?>
    <?php 	while( $rs = dbFetchObject($qs) ) : ?>
    <?php 		$id_pa = $rs->id_product_attribute; ?>
    <?php		$item =  get_product_reference($id_pa); ?>
    				<tr style="font-size:12px;" id="row_<?php echo $rs->id_adjust_detail; ?>">
                    	<td align="center">
							<?php echo $n; ?>
                        </td>
                        <td align="center" id="barcode_<?php echo $rs->id_adjust_detail; ?>">
							<?php echo get_barcode($id_pa); ?>
                        </td>
                        <td id="product_<?php echo $rs->id_adjust_detail; ?>">
							<?php echo $item; ?>
                            <input type="hidden" id="id_pa_<?php echo $rs->id_adjust_detail; ?>" value="<?php echo $id_pa; ?>" />
                        </td>
                        <td align="center" id="zone_<?php echo $rs->id_adjust_detail; ?>">
							<?php echo get_zone($rs->id_zone); ?>
                            <input type="hidden" id="id_zone_<?php echo $rs->id_adjust_detail; ?>" value="<?php echo $rs->id_zone; ?>" />
                        </td>
                        <td align="center" id="add_<?php echo $rs->id_adjust_detail; ?>">
							<?php echo $rs->adjust_qty_add; ?>
                        </td>
                        <td align="center" id="minus_<?php echo $rs->id_adjust_detail; ?>">
							<?php echo $rs->adjust_qty_minus; ?>
                        </td>
                        <td align="right">
                        <?php if( $rs->status_up == 0 ) : ?>
                        	<button type="button" class="btn btn-xs btn-warning" onclick="editAdjustDetail(<?php echo $rs->id_adjust_detail; ?>)"><i class="fa fa-pencil"></i></button>
                        <?php endif; ?>                        
                        	<button type="button" class="btn btn-xs btn-danger" onClick="confirmDelete(<?php echo $rs->id_adjust_detail; ?>, '<?php echo $item; ?>')"><i class="fa fa-trash"></i></button>                
                        </td>
                    </tr>
	<?php $n++; ?>                    
    <?php	endwhile; ?>    
    <?php endif; ?>
               
                
                </tbody>
            </table>
        </div><!--/ col-sm-12 -->
    </div><!--/ row -->

<?php 	endif; ?>

<?php else : ?>
<?php
	$adj_no	= isset( $_POST['adj_no'] ) ? $_POST['adj_no'] : ( getCookie('adj_no') ? getCookie('adj_no') : '' );
	$adj_ref 	= isset( $_POST['adj_ref'] ) ? $_POST['adj_ref'] : ( getCookie('adj_ref') ? getCookie('adj_ref') : '' );
	$adj_rm	= isset( $_POST['adj_rm'] ) ? $_POST['adj_rm'] : ( getCookie('adj_rm') ? getCookie('adj_rm') : '' );
	$from		= isset( $_POST['from'] ) ? $_POST['from'] : ( getCookie('from') ? getCookie('from') : '' );
	$to		= isset( $_POST['to'] ) ? $_POST['to'] : ( getCookie('to') ? getCookie('to') : '' );
	$vt			= isset( $_POST['adj_vt'] ) ? $_POST['adj_vt'] : ( getCookie('adj_vt') ? getCookie('adj_vt') : '' );
	$saved 	= $vt == 1 ? 'btn-info' : '';
	$unsave	= $vt === '' ? '' : ($vt == 0 ? 'btn-info' : '');
	if( isset( $_POST['adj_vt'] ) ){ createCookie('adj_vt', $vt); }
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);	
?>
<form id="searchForm" method="post">
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
    	<input type="text" name="adj_no" id="adj_no" class="form-control input-sm sf" value="<?php echo $adj_no; ?>" placeholder="ค้นหาเลขที่เอกสาร" />
    </div>
    <div class="col-sm-2">
    	<label>อ้างถึง</label>
    	<input type="text" name="adj_ref" id="adj_ref" class="form-control input-sm sf" value="<?php echo $adj_ref; ?>" placeholder="ค้นหาการอ้างอิง" />
    </div>
    <div class="col-sm-2">
    	<label>หมายเหตุ</label>
    	<input type="text" name="adj_rm" id="adj_rm" class="form-control input-sm sf" value="<?php echo $adj_rm; ?>" placeholder="ค้นหาหมายเหตุ" />
    </div>
    <div class="col-sm-3">
    	<label style="display:block;">วันที่</label>
    	<input type="text" name="from" id="from" class="form-control input-sm input-discount text-center" value="<?php echo $from; ?>" placeholder="เริมต้น" />
        <input type="text" name="to" id="to" class="form-control input-sm input-unit text-center sf" value="<?php echo $to; ?>" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-2">
    	<label style="display:block; visibility:hidden;">สถานะ</label>
        <div class="btn-group width-100">
        	<button type="button" class="btn btn-sm <?php echo $saved; ?> width-50" id="btn-saved" onClick="toggleStatus(1)">บันทึกแล้ว</button>
            <button type="button" class="btn btn-sm <?php echo $unsave; ?> width-50" id="btn-unsave" onClick="toggleStatus(0)">ยังไม่บันทึก</button>
        </div>
    </div>
    <div class="col-sm-1">
    	<label style="display:block; visibility:hidden;">reset</label>
        <button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()">Clear</button>
    </div>
</div>
<input type="hidden" name="adj_vt" id="adj_vt" value="<?php echo $vt; ?>" />
</form>
<hr/>
<?php
	//-------------------- เงื่อนไข ------------------//
	$where	= "WHERE id_adjust != 0 ";
	if( $adj_no !== '' )
	{
		createCookie('adj_no', $adj_no);
		$where .= "AND adjust_no LIKE '%".$adj_no."%' ";	
	}
	if( $adj_ref !== '' )
	{
		createCookie('adj_ref', $adj_ref);
		$where .= "AND adjust_reference LIKE '%".$adj_ref."%' ";	
	}
	if( $adj_rm !== '' )
	{
		createCookie('adj_rm', $adj_rm);
		$where .= "AND adjust_note LIKE '%".$adj_rm."%' ";	
	}
	if( $vt !== '' )
	{
		$where .= "AND adjust_status = ".$vt." ";
	}
	if( $from !== '' && $to !== '')
	{
		createCookie('from', $from);
		createCookie('to', $to);
		$where .= "AND adjust_date >= '".dbDate($from)."' AND adjust_date <= '".dbDate($to)."' ";
	}
	$where .= "ORDER BY adjust_date DESC";
?>
<div class="row">
	<div class="col-sm-12">
<?php 
		$paginator->Per_Page('tbl_adjust', $where, $get_rows);    
		$paginator->display($get_rows, 'index.php?content=ProductAdjust');
?>		
    	<table class="table table-striped" style="border:solid 1px #ccc;">
            <thead>
                <tr style="font-size:12px;">
                	<th style="width:5%; text-align:center;">ลำดับ</th>
                    <th style="width:15%;">เลขที่เอกสาร</th>
                    <th style="width:15%;">อ้างถึง</th>
                    <th style="width:15%;">พนักงาน</th>
                    <th style="width:20%;">หมายเหตุ</th>
                    <th style="width:10%;">สถานะ</th>
                    <th style="width:10%; text-align:center;">วันที่</th>
                    <th style="width:10%;"></th>
                </tr>
            </thead>            
            <tbody>
<?php 	$qs = dbQuery("SELECT * FROM tbl_adjust ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);	?>
<?php	if( dbNumRows($qs) > 0 ) : ?>
<?php	$n	= ! isset($_GET['Page']) ? 1 : ( isset( $_GET['Page'] ) && $_GET['Page'] > 1 ? ($_GET['Page'] - 1 ) * $paginator->Per_Page + 1 : 1);	?>
<?php		while( $rs = dbFetchArray($qs) ) : ?>
				<tr style="font-size:12px;">
                	<td align="center"><?php echo $n; ?></td>
                    <td><?php echo $rs['adjust_no']; ?></td>
                    <td><?php echo $rs['adjust_reference']; ?></td>
                    <td><?php echo employee_name($rs['id_employee']); ?></td>
                    <td><?php echo $rs['adjust_note']; ?></td>
                    <td><?php echo $rs['adjust_status'] == 1 ? 'บันทึกแล้ว' : '<span style="color:red;">ยังไม่บันทึก</span>'; ?></td>
                    <td align="center"><?php echo thaiDate($rs['adjust_date']); ?></td>
                    <td align="right">
                   	<?php if( $edit ) : ?>
                    	<button type="button" class="btn btn-warning btn-xs" onClick="editAdjust(<?php echo $rs['id_adjust']; ?>)"><i class="fa fa-pencil"></i></button>
                    <?php endif; ?>
                    <?php if( $delete ) : ?>
                    	<button type="button" class="btn btn-danger btn-xs" onClick="getDelete(<?php echo $rs['id_adjust']; ?>, '<?php echo $rs['adjust_no']; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
                <?php $n++; ?>
<?php		endwhile; 	?>
<?php 	endif; ?>            
            </tbody>
        </table>
<?php	echo $paginator->display_pages(); ?>
	<div class="divider-hidden"></div>
    </div><!--/ col-sm-12 -->
</div><!--/ row -->

<?php endif; ?>


</div><!--/ container -->
<script id="adjTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	<tr style="font-size:12px;" id="row_{{ id }}">
		<td align="center">
			{{ no }}
		</td>
		<td align="center" id="barcode_{{ id }}">
			{{ barcode }}
		</td>
		<td id="product_{{ id }}">
			{{ product }}
			<input type="hidden" id="id_pa_{{ id }}" value="{{ id_pa }}" />
		</td>
		<td align="center" id="zone_{{ id }}">
			{{ zone }}
			<input type="hidden" id="id_zone_{{ id }}" value="{{ id_zone }}" />
		</td>
		<td align="center" id="add_{{ id }}">
			{{ upQty }}
		</td>
		<td align="center" id="minus_{{ id }}">
			{{ downQty }}
		</td>
		<td align="right">
		{{#if edit}}
		<button type="button" class="btn btn-xs btn-warning" onclick="editAdjustDetail({{ id }})"><i class="fa fa-pencil"></i></button>
		{{/if}}
		<button type="button" class="btn btn-xs btn-danger" onClick="confirmDelete({{ id }}, '{{ product }}')"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
{{/each}}	
</script>

<script>

</script>
<script src="script/adjust.js"></script>