<?php 
	$id				= 	isset( $_GET['id_tranfer'] ) ? $_GET['id_tranfer'] : 0;
	$cs 			= 	$id != 0 ? new transfer($id) : new transfer();
	$reference 	= 	$id === 0 ? '' : $cs->reference;
	$date_add 	= 	$id === 0 ? thaiDate() : thaiDate($cs->date_add);
	$fromWH		= 	$id === 0 ? '' : $cs->warehouse_from;
	$toWH			= 	$id === 0 ? '' : $cs->warehouse_to;
	$remark		= 	$id === 0 ? '' : $cs->comment;
	$disabled	=	$id === 0 ? '' : 'disabled';
	
?>

<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" value="<?php echo $reference; ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm text-center header-box" name="dateAdd" id="dateAdd" value="<?php echo $date_add; ?>" <?php echo $disabled; ?> />
    </div>
    <div class="col-sm-2">
    	<label>ต้นทาง</label>
        <select class="form-control input-sm header-box" name="fromWH" id="fromWH" <?php echo $disabled; ?> >
        	<?php echo WHList($fromWH); ?>
        </select>
    </div>
    <div class="col-sm-2">
    	<label>ปลายทาง</label>
        <select class="form-control input-sm header-box" name="toWH" id="toWH" <?php echo $disabled; ?> >
        	<?php echo WHList($toWH); ?>
        </select>
    </div>
    <div class="col-sm-8">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm header-box" name="remark" id="remark" value="<?php echo $remark; ?>" <?php echo $disabled; ?> />
    </div>
    <div class="col-sm-2">
    	<label class="display-block not-show">add</label>
<?php if( $add && $id == 0 ) : ?>        
    	<button type="button" class="btn btn-sm btn-success btn-block" onClick="addNew()" id="btn-add">เพิ่ม</button>
<?php endif; ?>        
<?php if( $edit && $id != 0 ) : ?>
		<button type="button" class="btn btn-sm btn-warning btn-block" id="btn-edit" onClick="editHeader()">แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block hide" id="btn-update" onClick="updateHeader(<?php echo $id; ?>)">บันทึก</button>
<?php endif; ?>
    </div>
    <div class="col-sm-2">
    	<label class="display-block not-show">add</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="goUseBarcode(<?php echo $id; ?>)"><i class="fa fa-barcode"></i> &nbsp;ใช้บาร์โค้ด</button>
    </div>
    <input type="hidden" name="id_tranfer" id="id_tranfer" value="<?php echo $id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15" />
<?php if( $add OR $edit ) : ?>
<div class="row">
    <div class="col-sm-4">
    	<div class="input-group">
        	<span class="input-group-addon">โซนต้นทาง</span>
            <input type="text" class="form-control input-sm" id="fromZone" placeholder="ค้นหาชื่อโซน" />
        </div>
    </div>
    <div class="col-sm-1">
    	<button type="button" class="btn btn-sm btn-block btn-primary" onclick="getProductInZone()">ตกลง</button>
    </div>
    
    <div class="col-sm-4">
    	<div class="input-group">
        	<span class="input-group-addon">โซนปลายทาง</span>
            <input type="text" class="form-control input-sm" id="toZone" placeholder="ค้นหาชื่อโซน" />
        </div>
    </div>
    <div class="col-sm-1 padding-5">
    	<button type="button" class="btn btn-sm btn-block btn-primary not-show" id="btn-move-all" onclick="move_in_all()">ย้ายเข้าทั้งหมด</button>
    </div>
    
    <div class="col-sm-2">
    	<button type="button" class="btn btn-sm btn-default btn-block" onclick="showTransferTable()">แสดงรายการ</button>
    </div>
    
    <input type="hidden" id="id_zone_from" />
    <input type="hidden" id="id_zone_to" />
    <input type="hidden" id="id_wh_from" value="<?php echo $fromWH; ?>" />
    <input type="hidden" id="id_wh_to" value="<?php echo $toWH; ?>" />
</div>

<hr class="margin-top-15 margin-bottom-15" />
<?php endif; ?>
<div class="row">
	<div class="col-sm-12 hide" id="zone-table">
    <form id="productForm">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr>
                	<th colspan="6">
                    	<div class="col-sm-2">
                        <?php if( $delete ) : ?>
                        	<label class="margin-right-15px padding-10"><input type="checkbox" name="allowUnderZero" id="allowUnderZero" value="1" style="margin-right:5px;" />ติดลบได้ทั้งหมด</label>
                        <?php endif; ?>
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
                    <th class="width-15 text-center">ต้นทาง</th>
                    <th class="width-15 text-center">ปลายทาง</th>
                    <th class="width-10 text-center">จำนวน</th>
                    <th class="width-10 text-center">การกระทำ</th>
                </tr>
            </thead>
            <tbody id="transfer-list">
<?php	$qs = $cs->getMoveList($id); ?>
<?php 	if( dbNumRows($qs) > 0 ) : ?>
<?php		$no = 1;						?>
<?php		while( $rs = dbFetchObject($qs) ) : 	?>
<?php			$pReference = get_product_reference($rs->id_product_attribute);	?>
<?php			$id_td	 = $rs->id_tranfer_detail;			?>
				<tr class="font-size-12" id="row-<?php echo $id_td; ?>">
                	<td class="middle text-center"><?php echo $no; ?></td>
                    <td class="middle"><?php echo getBarcode($rs->id_product_attribute); ?></td>
                    <td class="middle"><?php echo $pReference; ?></td>
                    <td class="middle text-center">
                    	<input type="hidden" class="row-zone-from" id="row-from-<?php echo $id_td; ?>" value="<?php echo $rs->id_zone_from; ?>" />
						<?php echo get_zone($rs->id_zone_from); ?>
                    </td>
                    <td class="middle text-center" id="row-label-<?php echo $id_td; ?>">
                    <?php if( $rs->id_zone_to == 0 ) : ?>
                    	<button type="button" class="btn btn-xs btn-primary" id="btn_<?php echo $id_td; ?>" onclick="move_in(<?php echo $id_td; ?>, <?php echo $rs->id_zone_from; ?>)">ย้ายเข้าโซน</button>
                    <?php else : ?>
					<?php 	echo get_zone($rs->id_zone_to); 				?>
                    <?php endif; ?>
                    </td>
                    <td class="middle text-center" ><?php echo number_format($rs->tranfer_qty); ?></td>
                    <td class="middle text-center">
                    <?php if( $edit ) : ?>
                    	<button type="button" class="btn btn-xs btn-danger" onclick="deleteMoveItem(<?php echo $id_td; ?>, '<?php echo $pReference; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
<?php			$no++;									?>
<?php		endwhile;			?>
<?php	else : ?>
 				<tr>
                	<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
                </tr>
<?php	endif; ?>            
            </tbody>
        </table>
    </div>
</div>

<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
{{#if nodata}}
<tr>
	<td colspan="6" class="text-center"><h4>ไม่พบสินค้าในโซน</h4></td>
</tr>            
{{else}}            
<tr>
	<td align="center">{{ no }}</td>
    <td align="center">{{ barcode }}</td>
    <td>{{ products }}</td>
    <td align="center" class="qty-label">{{ qty }}</td>
    <td align="center">
    	<input type="text" class="form-control input-sm text-center input-qty" id="moveQty_{{ id_stock }}" name="moveQty[{{id_stock}}]" onkeyup="validQty({{ id_stock}}, {{ qty }})" />
		<input type="hidden" name="id_pa[{{ id_stock }}]" id="id_pa_{{ id_stock }}" value="{{ id_pa }}" />
    </td>
    <td align="center">
<?php if( $delete ) : ?>	
    	<input type="checkbox" name="underZero[{{id_stock}}]" id="underZero_{{ id_stock }}" onChange="validQty({{ id_stock }}, {{ qty }})" value="1" />
        <label for="underZero_{{ id_stock }}">ติดลบได้</label>
<?php endif; ?>		
    </td>
</tr>
{{/if}}
{{/each}}
</script>
<script id="transferTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle text-center">
				<input type="hidden" class="row-zone-from" id="row-from-{{ id }}" value="{{ id_zone_from }}" />
				{{ fromZone }}
			</td>
			<td class="middle text-center" id="row-label-{{id}}">{{{ toZone }}}</td>
			<td class="middle text-center">{{ qty }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
		</tr>
	{{/if}}
{{/each}}
</script>