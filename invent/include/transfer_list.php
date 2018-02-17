<?php
$sCode	= isset( $_POST['sCode'] ) ? trim( $_POST['sCode'] ) : ( getCookie('sCode') ? getCookie('sCode') : '' );
$sEmp	= isset( $_POST['sEmp'] ) ? trim( $_POST['sEmp'] ) : ( getCookie('sEmp') ? getCookie('sEmp') : '' );
$fromDate	= isset( $_POST['fromDate'] ) ? $_POST['fromDate'] : ( getCookie('fromDate') ? getCookie('fromDate') : '' );
$toDate	= isset( $_POST['toDate'] ) ? $_POST['toDate'] : ( getCookie('fromDate') ? getCookie('fromDate') : '' );
$sStatus	= isset( $_POST['sStatus'] ) ? $_POST['sStatus'] : ( getCookie('sStatus') ? getCookie('sStatus') : 0 );
$isActive	= $sStatus == 1 ? 'btn-info' : '' ;
?>
<form id="searchForm" method="post">
<div class="row">
	<div class="col-sm-2">
    	<label>เอกสาร</label>
        <input type="text" class="form-control input-sm text-center search-box" name="sCode" id="sCode" value="<?php echo $sCode; ?>" />
    </div>
    <div class="col-sm-2">
    	<label>พนักงาน</label>
        <input type="text" class="form-control input-sm text-center search-box" name="sEmp" id="sEmp" value="<?php echo $sEmp; ?>" />
    </div>
    <div class="col-sm-2 col-2-harf">
    	<label class="display-block">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center search-box" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" />
        <input type="text" class="form-control input-sm input-unit text-center search-box" name="toDate" id="toDate" value="<?php echo $toDate; ?>" />
    </div>
    <div class="col-sm-1">
    	<label class="display-block not-show">incomplete</label>
        <button type="button" class="btn btn-sm <?php echo $isActive; ?>" id="btn-inComplete" onclick="toggleActive()">ไม่สมบูรณ์</button>
    </div>
    <div class="col-sm-1 col-1-harf">
    	<label class="display-block not-show">search</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-sm-1 col-1-harf">
    	<label class="display-block not-show">Reset</label>
        <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
</div>
<input type="hidden" name="sStatus" id="sStatus" value="<?php echo $sStatus; ?>" />
</form>
<hr class="margin-top-15" />

<?php
	$where = "WHERE id_tranfer != 0 ";
	
	if( $sCode != "" )
	{
		createCookie('sCode', $sCode);
		$where .= "AND reference LIKE '%".$sCode."%' ";
	}
	
	if( $sEmp != "" )
	{
		createCookie('sEmp', $sEmp);
		$emp	= employee_in($sEmp);
		$where .= "AND id_employee IN(". ($emp === FALSE ? 0 : $emp) .") ";
	}
	
	createCookie('sStatus', $sStatus);
	
	if( $sStatus == 1 )
	{
		$where .= "AND id_tranfer IN(".not_complete_in().") ";
	}
	
	
	if( $fromDate != "" && $toDate != "" )
	{
		createCookie('fromDate', $fromDate);
		createCookie('toDate', $toDate);
		$where .= "AND date_add >= '". fromDate($fromDate)."' AND date_add <= '".toDate($toDate)."' ";
	}
	
	$where .= "ORDER BY reference DESC";
	
	$paginator = new paginator();
	$get_rows = get_rows();
	$paginator->Per_Page('tbl_tranfer', $where, $get_rows);
	$paginator->display($get_rows, 'index.php?content=tranfer');
	
	$qs = dbQuery("SELECT * FROM tbl_tranfer ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);
	
	?>
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped" style="border:solid 1px #CCC;">
        	<thead>
            	<tr>
                	<th class="width-5 text-center">ลำดับ</th>
                    <th class="width-15">เอกสาร</th>
                    <th class="width-15">ต้นทาง</th>
                    <th class="width-15">ปลายทาง</th>
                    <th class="width-15">พนักงาน</th>
                    <th class="width-10 text-center">วันที่</th>
                    <th class="width-10 text-center">สถานะ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
<?php	if( dbNumRows($qs) > 0 ) : ?>
<?php		$no	= row_no();		?>
<?php		while( $rs = dbFetchObject($qs) ) :		?>
			<tr class="font-size-10" id="row_<?php echo $rs->id_tranfer; ?>">
            	<td class="middle text-center"><?php echo number_format($no); ?></td>
                <td class="middle"><?php echo $rs->reference; ?></td>
                <td class="middle"><?php echo get_warehouse_name_by_id($rs->warehouse_from); ?></td>
                <td class="middle"><?php echo get_warehouse_name_by_id($rs->warehouse_to); ?></td>
                <td class="middle"><?php echo employee_name($rs->id_employee); ?></td>
                <td class="middle text-center"><?php echo thaiDate($rs->date_add); ?></td>
                <td class="middle text-center">
                	<?php if( isComplete($rs->id_tranfer) === FALSE ) : ?>
                    <span style='color:red;'>ไม่สมบูรณ์</span>
                    <?php endif; ?>
                </td>
                <td class="middle text-right">
                	<button type="button" class="btn btn-xs btn-info" onclick="goDetail(<?php echo $rs->id_tranfer; ?>)" ><i class="fa fa-eye"></i></button>
	<?php	if( $edit ) : ?>
    				<button type="button" class="btn btn-xs btn-warning" onclick="goEdit(<?php echo $rs->id_tranfer; ?>)"><i class="fa fa-pencil"></i></button>
    <?php	endif; ?>     
    <?php	if( $edit ) : ?>
    				<button type="button" class="btn btn-xs btn-danger" onclick="deleteTransfer(<?php echo $rs->id_tranfer; ?>, '<?php echo $rs->reference; ?>')">
                    	<i class="fa fa-trash"></i>
                    </button>
    <?php	endif; ?>               
                </td>
            </tr>
<?php		$no++; 	?>
<?php		endwhile; ?>
<?php	else : ?>
				<tr>
                	<td colspan="8" class="text-center">
                    	<h4>ไม่พบรายการตามเงื่อนไข</h4>
                    </td>
                </tr>
<?php	endif; 	?>            
            </tbody>
        </table>
    </div>
</div>    
	
    