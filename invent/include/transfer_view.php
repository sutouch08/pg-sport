<?php if( ! isset( $_GET['id_tranfer'] ) )  : 	?>
<?php		include 'include/page_error.php';	?>

<?php else : 	?>
<?php	$id		= 	$_GET['id_tranfer'];		?>
<?php	$cs 	= 	new transfer($id);		?>

<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" value="<?php echo $cs->reference; ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm text-center header-box" name="dateAdd" id="dateAdd" value="<?php echo thaiDate($cs->date_add); ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>ต้นทาง</label>
        <select class="form-control input-sm header-box" name="fromWH" id="fromWH" disabled>
        	<?php echo WHList($cs->warehouse_from); ?>
        </select>
    </div>
    <div class="col-sm-2">
    	<label>ปลายทาง</label>
        <select class="form-control input-sm header-box" name="toWH" id="toWH" disabled >
        	<?php echo WHList($cs->warehouse_to); ?>
        </select>
    </div>
    <div class="col-sm-8">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm header-box" name="remark" id="remark" value="<?php echo $cs->comment; ?>" disabled />
    </div>
    <input type="hidden" id="id_tranfer" value="<?php echo $cs->id_tranfer; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15" />
<div class="row">
	<div class="col-sm-12 hide" id="zone-table">
    <form id="productForm">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr>
                	<th colspan="6">
                    	<div class="col-sm-2">
                        	<label class="margin-right-15px padding-10"><input type="checkbox" name="allowUnderZero" id="allowUnderZero" value="1" style="margin-right:5px;" />ติดลบได้ทั้งหมด</label>
						</div>
                        <div class="col-sm-4">
                        	<button type="button" class="btn btn-sm btn-warning" onclick="addAllToTransfer()">ย้ายรายการทั้งหมด</button>
                        </div>
                        <div class="col-sm-6">
                            <p class="pull-right top-p">
                            <button type="button" class="btn btn-sm btn-primary" onclick="addToTransfer()">ย้ายรายการที่เลือก</button>
                            </p>
                        </div>
                    </th>
                                 
                </tr>
                <tr>
                	<th class="width-10 text-center">ลำดับ</th>
                    <th class="width-20 text-center">บาร์โค้ด</th>
                    <th class="width-40 text-center">สินค้า</th>
                    <th class="width-10 text-center">จำนวน</th>
                    <th class="width-10 text-center">ย้ายออก</th>
                    <th class="width-10 text-center">ตัวเลือก</th>
                </tr>
            </thead>
            <tbody id="zone-list">
            
            
            </tbody>
        </table>
        </form>
    </div>



	<div class="col-sm-12" id="transfer-table">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr>
                	<th colspan="7" class="text-center">รายการโอนย้าย</th>
                </tr>
            	<tr>
                	<th class="width-5 text-center">ลำดับ</th>
                    <th class="width-15 text-center">บาร์โค้ด</th>
                    <th class="width-30 text-center">สินค้า</th>
                    <th class="width-20 text-center">ต้นทาง</th>
                    <th class="width-20 text-center">ปลายทาง</th>
                    <th class="width-10 text-center">จำนวน</th>
                </tr>
            </thead>
            <tbody id="transfer-list">
<?php	$qs = $cs->getMoveList($id); ?>
<?php 	if( dbNumRows($qs) > 0 ) : ?>
<?php		$no = 1;						?>
<?php		while( $rs = dbFetchObject($qs) ) : 	?>
<?php			$pReference = get_product_reference($rs->id_product_attribute);	?>
<?php			$id_td	 = $rs->id_tranfer_detail;			?>
				<tr class="font-size-12">
                	<td class="middle text-center"><?php echo $no; ?></td>
                    <td class="middle"><?php echo getBarcode($rs->id_product_attribute); ?></td>
                    <td class="middle"><?php echo $pReference; ?></td>
                    <td class="middle text-center"><?php echo get_zone($rs->id_zone_from); ?></td>
                    <td class="middle text-center">
                    <?php if( $rs->id_zone_to == 0 ) : ?>
                    	<span class="red">ยังไม่ย้ายเข้าโซน</span>
                    <?php else : ?>
					<?php 	echo get_zone($rs->id_zone_to); 				?>
                    <?php endif; ?>
                    </td>
                    <td class="middle text-center" ><?php echo number_format($rs->tranfer_qty); ?></td> 
                </tr>
<?php			$no++;									?>
<?php		endwhile;			?>
<?php	else : ?>
 				<tr>
                	<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
                </tr>
<?php	endif; ?>            
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
