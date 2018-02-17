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
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="goEdit(<?php echo $id; ?>)"><i class="fa fa-keyboard-o"></i> คีย์มือ</button>
    </div>
    <input type="hidden" name="id_tranfer" id="id_tranfer" value="<?php echo $id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15" />
<?php if( $add OR $edit ) : ?>
<div class="row">
	<div class="col-sm-2">
    	<button type="button" class="btn btn-sm btn-default btn-block" onclick="showTransferTable()">แสดงรายการ</button>
    </div>
	<div class="col-sm-2 control-btn">
    	<button type="button" class="btn btn-sm btn-danger btn-block" onclick="getMoveOut()">ย้ายสินค้าออก</button>
    </div>
    <div class="col-sm-2 control-btn">
    	<button type="button" class="btn btn-sm btn-info btn-block" onclick="getMoveIn()">ย้ายสินค้าเข้า</button>
    </div>
</div>

<hr class="margin-top-10 margin-bottom-10"	/>

<div class="row">
	<div class="col-sm-12 moveOut-zone hide">
        <div class="col-sm-4 ">
            <div class="input-group">
                <span class="input-group-addon">โซนต้นทาง</span>
                <input type="text" class="form-control input-sm" id="fromZone-barcode" placeholder="ยิงบาร์โค้ดโซน" />
            </div>
        </div>
        <div class="col-sm-1">
            <button type="button" class="btn btn-sm btn-primary" onclick="getZoneFrom()">ตกลง</button>
        </div>
        <div class="col-sm-2">
            <button type="button" class="btn btn-sm btn-info btn-block" onclick="newFromZone()">โซนใหม่</button>
        </div>
    </div>
    
    <div class="col-sm-12 moveIn-zone hide">
        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">โซนปลายทาง</span>
                <input type="text" class="form-control input-sm" id="toZone-barcode" placeholder="ยิงบาร์โค้ดโซน" />
            </div>
        </div>
        <div class="col-sm-2">
        	<button type="button" class="btn btn-sm btn-info btn-block" onclick="newToZone()">โซนใหม่</button>
        </div>
        <div class="col-sm-6">
        	<h4 class="title" style="margin-top:5px;" id="zoneName-label"></h4>
        </div>
    </div>
     <input type="hidden" id="id_zone_from" />
    <input type="hidden" id="id_zone_to" />
    <input type="hidden" id="id_wh_from" value="<?php echo $fromWH; ?>" />
    <input type="hidden" id="id_wh_to" value="<?php echo $toWH; ?>" />
    <div class="divider-hidden"></div>
</div>
    

<?php endif; ?>
<div class="row">
	<div class="col-sm-12 hide" id="zone-table">
    <form id="productForm">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr><th colspan="4" class="text-center"><h4 class="title" id="zoneName"></h4></th></tr>
            	<tr>
                	<th colspan="4">
                    	<div class="col-sm-3">
                        <?php if( $delete ) : ?>
                        	<label class="margin-right-15px padding-10"><input type="checkbox" name="underZero" id="underZero" style="margin-right:5px;" />ติดลบได้</label>
                        <?php endif; ?>
						</div>
                         <div class="col-sm-2">
                        	<div class="input-group">
                                <span class="input-group-addon">จำนวน</span>
                                <input type="text" class="form-control input-sm" id="qty-from" value="1" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                        	<div class="input-group">
                                <span class="input-group-addon">บาร์โค้ดสินค้า</span>
                                <input type="text" class="form-control input-sm" id="barcode-item-from" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" />
                            </div>
                        </div>
                    </th>
                                 
                </tr>
                <tr>
                	<th class="width-10 text-center">ลำดับ</th>
                    <th class="width-20 text-center">บาร์โค้ด</th>
                    <th class="text-center">สินค้า</th>
                    <th class="width-10 text-center">จำนวน</th> 
                </tr>
            </thead>
            <tbody id="zone-list">
            
            
            </tbody>
        </table>
        </form>
    </div>

	<div class="col-sm-12 hide" id="temp-table">
    	<table class="table table-striped table-bordered">
        	<thead>
                <tr>
                	<th colspan="7">                 
                     <div class="col-sm-2 col-sm-offset-3">
                        	<div class="input-group">
                                <span class="input-group-addon">จำนวน</span>
                                <input type="text" class="form-control input-sm" id="qty-to" value="1" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                        	<div class="input-group">
                                <span class="input-group-addon">บาร์โค้ดสินค้า</span>
                                <input type="text" class="form-control input-sm" id="barcode-item-to" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" />
                            </div>
                        </div>
                    </th>
                </tr>
            	<tr>
                	<th class="width-5 text-center">ลำดับ</th>
                    <th class="width-15 text-center">บาร์โค้ด</th>
                    <th class="width-45 text-center">สินค้า</th>
                    <th class="width-25 text-center">ต้นทาง</th>
                    <th class="width-10 text-center">จำนวน</th>  
                </tr>
            </thead>
            <tbody id="temp-list">
            
            </tbody>
            </table>
    </div>

	<div class="col-sm-12" id="transfer-table">
    	<table class="table table-striped table-bordered">
        	<thead>
                <tr id="moveIn-input" class="hide">
                	<th colspan="7">                 
                     <div class="col-sm-2 col-sm-offset-3">
                        	<div class="input-group">
                                <span class="input-group-addon">จำนวน</span>
                                <input type="text" class="form-control input-sm" id="qty-to" value="1" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                        	<div class="input-group">
                                <span class="input-group-addon">บาร์โค้ดสินค้า</span>
                                <input type="text" class="form-control input-sm" id="barcode-item-to" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" />
                            </div>
                        </div>
                    </th>
                </tr>
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
<?php			$barcode = getBarcode($rs->id_product_attribute);		?>
				<tr class="font-size-12" id="row-<?php echo $id_td; ?>">
                	<td class="middle text-center"><?php echo $no; ?></td>
                    <td class="middle"><?php echo $barcode; ?></td>
                    <td class="middle"><?php echo $pReference; ?></td>
                    <td class="middle text-center">
                    	<input type="hidden" class="row-zone-from" id="row-from-<?php echo $id_td; ?>" value="<?php echo $rs->id_zone_from; ?>" />
						<?php echo get_zone($rs->id_zone_from); ?>
                    </td>
                    <td class="middle text-center" id="row-label-<?php echo $id_td; ?>">
                    <input type="hidden" id="row_<?php echo $barcode; ?>" value="<?php echo $id_td; ?>" />
                    <?php if( $rs->valid == 0 ) : ?>
                    	<input type="hidden" id="qty-<?php echo $barcode; ?>" value="<?php echo $rs->tranfer_qty; ?>" />
                    <?php endif; ?>
                    <?php if( $rs->id_zone_to == 0 ) : ?>
                    	<button type="button" class="btn btn-xs btn-primary" id="btn_<?php echo $id_td; ?>" onclick="move_in(<?php echo $id_td; ?>, <?php echo $rs->id_zone_from; ?>)">ย้ายเข้าโซน</button>
                    <?php else : ?>
					<?php 	echo get_zone($rs->id_zone_to); 				?>
                    <?php endif; ?>
                    </td>
                    <td class="middle text-center" id="qty-label-<?php echo $barcode; ?>"><?php echo number_format($rs->tranfer_qty); ?></td>
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
	<td colspan="4" class="text-center"><h4>ไม่พบสินค้าในโซน</h4></td>
</tr>            
{{else}}            
<tr>
	<td align="center">{{ no }}</td>
    <td align="center">{{ barcode }}</td>
    <td>
		{{ products }}
		<input type="hidden" id="qty_{{barcode}}" value="{{qty}}" />
	</td>
    <td align="center" id="qty-label_{{barcode}}">	{{ qty }}	</td>
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

<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		<tr class="font-size-12" id="row-temp-{{ id }}">
			<td class="middle text-center">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle text-center">
				<input type="hidden" id="from-{{ barcode }}" value="{{ id_zone_from }}" />
				<input type="hidden" id="row_{{barcode}}" value="{{id}}" />
				<input type="hidden" id="qty-{{barcode}}" value="{{qty}}" />
				{{ fromZone }}
			</td>

			<td class="middle text-center" id="qty-label-{{barcode}}">
				{{ qty }}
			</td>
		</tr>
	{{/if}}
{{/each}}
</script>