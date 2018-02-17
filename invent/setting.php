<?php 
	$pageName	= "การตั้งค่า";
	$id_tab 		= 25;
	$id_profile 	= $_COOKIE['profile_id'];
    $pm		= checkAccess($id_profile, $id_tab);
	$view 	= $pm['view'];
	$add 		= $pm['add'];
	$edit 		= $pm['edit'];
	$delete 	= $pm['delete'];
	
	$su		= checkAccess($id_profile, 61); //-------  ตรวจสอบว่ามีสิทธิ์ในการปิดระบบหรือไม่  -----//
	$cando	= ($su['view'] + $su['add'] + $su['edit'] + $su['delete'] ) > 0 ? TRUE : FALSE;
	accessDeny($view);
	?>
<script src="<?php echo WEB_ROOT; ?>library/ckeditor/ckeditor.js"></script>
<script src="<?php echo WEB_ROOT; ?>library/ckfinder/ckfinder.js"></script>    
<div class="container">
<div class="row top-row">
	<div class="col-lg-12 top-col">
    	<h4 class="title"><?php echo $pageName; ?></h4>
	</div>
</div>
<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:0px;" />
<!----------------------------- ตั้งค่าทั่วไป  ----------------------->
<div class="row">
<div class="col-sm-2 padding-right-0" style="padding-top:15px;">
<ul id="myTab1" class="setting-tabs">
        <li class="li-block active"><a href="#general" data-toggle="tab">ตั้งค่าทั่วไป</a></li>
        <li class="li-block"><a href="#product" data-toggle="tab">ตั้งค่าสินค้า</a></li>
        <li class="li-block"><a href="#document" data-toggle="tab">ตั้งค่าเอกสาร</a></li>        
        <li class="li-block"><a href="#other" data-toggle="tab">ตั้งค่าอื่นๆ</a></li>
</ul>
</div>
<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; max-height:1000px;">
<div class="tab-content">
        <!-------------------------------------------------------  ตั้งค่าทั่วไป  ----------------------------------------------------->
            <div class="tab-pane fade active in" id="general">
            	<form id="generalForm">
            	<div class="row">
                	<div class="col-sm-3"><span class="form-control left-label">แบรนด์สินค้า</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="COMPANY_NAME" id="brand" value="<?php echo getConfig('COMPANY_NAME'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ชื่อบริษัท</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-large input-line padding-left-0" name="COMPANY_FULL_NAME" id="cName" value="<?php echo getConfig('COMPANY_FULL_NAME'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ที่อยู่</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-line padding-left-0" name="COMPANY_ADDRESS" id="cAddress" value="<?php echo getConfig('COMPANY_ADDRESS'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">รหัสไปรษณีย์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line padding-left-0" name="COMPANY_POST_CODE" id="postCode" value="<?php echo getConfig('COMPANY_POST_CODE'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">โทรศัพท์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="COMPANY_PHONE" id="phone" value="<?php echo getConfig('COMPANY_PHONE'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">แฟกซ์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="COMPANY_FAX_NUMBER" id="fax" value="<?php echo getConfig('COMPANY_FAX_NUMBER'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">อีเมล์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="COMPANY_EMAIL" id="email" value="<?php echo getConfig('COMPANY_EMAIL'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เลขประจำตัวผู้เสียภาษี</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="COMPANY_TAX_ID" id="taxID" value="<?php echo getConfig('COMPANY_TAX_ID'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">สกุลเงิน</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-medium input-line padding-left-0" name="CURRENCY" id="currency" value="<?php echo getConfig('CURRENCY'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-9 col-sm-offset-3"><button type="button" class="btn btn-sm btn-success input-mini" onClick="updateConfig('generalForm')"><i class="fa fa-save"></i> บันทึก</button></div>
                    <div class="divider-hidden"></div>
                  
            	</div><!--/ row -->
                </form>
            </div>
		<!---------------------------------------------------------------------  ตั้งค่าสินค้า  ---------------------------------------------------->          
            <div class="tab-pane fade" id="product">
            	<form id="productForm">
            	<div class="row">
                	<div class="col-sm-3"><span class="form-control left-label">อายุของสินค้าใหม (วัน)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="NEW_PRODUCT_DATE" id="newProductAge" value="<?php echo getConfig('NEW_PRODUCT_DATE'); ?>" />
                        <span class="help-block">กำหนดจำนวนวัน ที่จะแสดงไฮไลท์ว่าเป็นสินค้าใหม่</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">สินค้าใหม่</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="NEW_PRODUCT_QTY" id="newProductQty" value="<?php echo getConfig('NEW_PRODUCT_QTY'); ?>" />
                    	<span class="help-block">กำหนดจำนวนสินค้าที่จะแสดงรายการสินค้าใหม่บนหน้าแรก (สำหรับลูกค้า และ พนักงานขาย) </span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">สินค้าหน้าแรก</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="FEATURES_PRODUCT" id="featuresProduct" value="<?php echo getConfig('FEATURES_PRODUCT'); ?>" />
                    	<span class="help-block">กำหนดจำนวนสินค้าที่จะแสดงเป็นรายการสินค้าแนะนำบนหน้าแรก (สำหรับลูกค้า และ พนักงานขาย) </span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">Stock Filter</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="MAX_SHOW_STOCK" id="stockFilter" value="<?php echo getConfig('MAX_SHOW_STOCK'); ?>" />
                    	<span class="help-block">กำหนดจำนวนสินค้าคงเหลือสูงสุดที่จะแสดงให้ลูกค้าเห็น (สำหรับลูกค้า) ถ้าไม่ต้องการใช้กำหนดเป็น 0 </span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">รูปแบบบาร์โค้ด</span></div>
                    <div class="col-sm-9">
                    	<?php $barcodeType = getConfig('BARCODE_TYPE'); ?>
                    	<select class="form-control input-sm input-mini input-line" name="BARCODE_TYPE" id="barcodeType">
                        	<option value="code39" <?php echo isSelected($barcodeType, 'code39'); ?>>CODE 39</option>
                            <option value="code93" <?php echo isSelected($barcodeType, 'code93'); ?>>CODE 93</option>
                            <option value="code128" <?php echo isSelected($barcodeType, 'code128'); ?>>CODE 128</option>
                            <option value="ean13" <?php echo isSelected($barcodeType, 'ean13'); ?>>EAN 13</option>
                        </select>
                        <span class="help-block">เลือก Format ของบาร์โค้ดที่ใช้กับเอกสารต่างๆ</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="cols-sm-9 col-sm-offset-3">
                    	<button type="button" class="btn btn-sm btn-success input-mini" onClick="updateConfig('productForm')"><i class="fa fa-save"></i> บันทึก</button>
                    </div>
                    <div class="divider-hidden"></div>
                    
            	</div><!--/ row -->
                </form>
            </div>
            
        <!--------------------------------------------------  ตั้งค่าเอกสาร  ------------------------------------------------------->            
            <div class="tab-pane fade" id="document">
            	<form id="documentForm">
                <div class="row">
                	<div class="col-sm-3"><span class="form-control left-label">ขายสินค้า</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_ORDER" id="preOrder" value="<?php echo getConfig('PREFIX_ORDER'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ฝากขาย</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_CONSIGNMENT" id="pConsignment" value="<?php echo getConfig('PREFIX_CONSIGNMENT'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ตัดยอดฝากขาย</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_CONSIGN" id="preConsign" value="<?php echo getConfig('PREFIX_CONSIGN'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ใบสั่งซื้อ</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_PO" id="prePO" value="<?php echo getConfig('PREFIX_PO'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">รับสินคาเข้าจากการซื้อ</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_RECIEVE" id="preReceiveFromPO" value="<?php echo getConfig('PREFIX_RECIEVE'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">รับสินค้าเข้าจากการแปรสภาพ</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_RECEIVE_TRANFORM" id="preReceiveFromTransform" value="<?php echo getConfig('PREFIX_RECEIVE_TRANFORM'); ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เบิกแปรสภาพ</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line" name="PREFIX_REQUISITION" id="preTransform" value="<?php echo getConfig('PREFIX_REQUISITION'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ยืมสินค้า</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_LEND" id="preLend" value="<?php echo getConfig('PREFIX_LEND'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เบิกสปอนเซอร์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_SPONSOR" id="preSponsor" value="<?php echo getConfig('PREFIX_SPONSOR'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เบิกอภินันท์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_SUPPORT" id="preSupport" value="<?php echo getConfig('PREFIX_SUPPORT'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">คืนสินค้าจากการขาย</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_RETURN" value="<?php echo getConfig('PREFIX_RETURN'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ค้นสินค้าจากการสปอนเซอร์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_RETURN_SPONSOR" value="<?php echo getConfig('PREFIX_RETURN_SPONSOR'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">คืนสินค้าจากอภินันท์</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_RETURN_SUPPORT" value="<?php echo getConfig('PREFIX_RETURN_SUPPORT'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ร้องขอสินค้า</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_REQUEST_ORDER" value="<?php echo getConfig('PREFIX_REQUEST_ORDER'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">กระทบยอด</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_CONSIGN_CHECK" value="<?php echo getConfig('PREFIX_CONSIGN_CHECK'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">โอนสินค้าระหว่างคลัง</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_TRANFER" id="preTransfer" value="<?php echo getConfig('PREFIX_TRANFER'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ปรับยอดสต็อก</span></div>
                    <div class="col-sm-9"><input type="text" class="form-control input-sm input-mini input-line prefix" name="PREFIX_ADJUST" id="preAdjust" value="<?php echo getConfig('PREFIX_ADJUST'); ?>" /></div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-9 col-sm-offset-3">
                    	<button type="button" class="btn btn-sm btn-success input-mini" onClick="updateConfig('documentForm')"><i class="fa fa-save"></i> บันทึก</button>
                    </div>
                    <div class="divider-hidden"></div>
                      
                </div><!--/ row -->
                </form>
            </div>
           
		<!----------------  ตั้งค่าอื่นๆ  ------------>            
            <div class="tab-pane fade" id="other">
            <?php $closed 	= getConfig('CLOSED');  ?>
            <?php $open		= $closed == 0 ? 'btn-success' : ''; 	?>
            <?php $close		= $closed == 1 ? 'btn-danger' : '';		?>
            <?php $shop		= getConfig('SHOP_OPEN'); 	?>
			<?php $sOpen		= $shop == 1 ? 'btn-success' : ''; ?>
            <?php $sClose		= $shop == 0 ? 'btn-danger' : ''; ?>
            	<form id="otherForm">
                <div class="row">
                	<?php if( $cando === TRUE ): //---- ถ้ามีสิทธิ์ปิดระบบ ---//	?>
                	<div class="col-sm-3"><span class="form-control left-label">ปิดระบบ</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-open" onClick="openSystem()">เปิด</button>
                            <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-close" onClick="closeSystem()">ปิด</button>
                        </div>
                        <span class="help-block">กรณีปิดระบบจะไม่สามารถเข้าใช้งานระบบได้ในทุกส่วน โปรดใช้ความระมัดระวังในการกำหนดค่านี้</span>
                    	<input type="hidden" name="CLOSED" id="closed" value="<?php echo $closed; ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    <?php endif; ?>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ข้อความแจ้งปิดระบบ</span></div>
                    <div class="col-sm-9">
                    	<textarea id="content" class="form-control input-sm input-500 input-line" rows="4" name="MAINTENANCE_MESSAGE" ><?php echo getConfig('MAINTENANCE_MESSAGE'); ?></textarea>
                        <span class="help-block">กำหนดข้อความที่จะแสดงบนหน้าเว็บเมื่อมีการปิดระบบ ( รองรับ HTML Code )</span>
					</div>                        
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ID ของเว็บ</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="ITEMS_GROUP" id="itemGroup" value="<?php echo getConfig('ITEMS_GROUP'); ?>" />
                        <span class="help-block">กำหนดตัวเลข ID ของเว็บเพื่อใช้ในการระบุสินค้าว่ามาจากเว็บไหน ใช้ในกรณีที่มีการส่งออกรายการสินค้าไปนำเข้า POS (กรณีมีหลายเว็บ แต่ละเว็บห้ามซ้ำกัน)</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">อนุญาติให้ลูกค้าสั่งสินค้าเอง</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $sOpen; ?>" style="width:50%;" id="btn-sopen" onClick="openShop()">เปิด</button>
                            <button type="button" class="btn btn-sm <?php echo $sClose; ?>" style="width:50%;" id="btn-sclose" onClick="closeShop()">ปิด</button>
                        </div>
                        <span class="help-block">เปิดหรือปิดการใช้งานหน้าเว็บสำหรับให้ลูกค้าสั่งเอง</span>
                    	<input type="hidden" name="SHOP_OPEN" id="shopOpen" value="<?php echo $shop; ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">รับสินค้าเกินใบสั่งซื้อ ( % )</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="RECEIVE_OVER_PO" id="overPO" value="<?php echo getConfig('RECEIVE_OVER_PO'); ?>" />
                        <span class="help-block">จำกัดการรับสินค้าเข้าเกินกว่ายอดในใบสั่งซื้อได้ไม่เกินกี่เปอร์เซ็น</span>
                    </div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">อายุของออเดอร์ ( วัน )</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="ORDER_EXPIRATION" id="orderAge" value="<?php echo getConfig('ORDER_EXPIRATION'); ?>" />
                        <span class="help-block">กำหนดวันหมดอายุของออเดอร์ หากออเดอร์อยู่ในสถานะ รอการชำระเงิน, รอจัดสินค้า หรือ ไม่บันทึก เกินกว่าจำนวนวันที่กำหนด</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    
                     <div class="col-sm-9 col-sm-offset-3">
                    	<button type="button" class="btn btn-sm btn-success input-mini" onClick="updateConfig('otherForm')"><i class="fa fa-save"></i> บันทึก</button>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    
                </div><!--/row-->
                </form>
            </div>            
</div>
</div><!--/ col-sm-9  -->    
</div><!--/ row  -->

</div><!---/ container -->

<script>
CKEDITOR.replace( 'content',{
	toolbarGroups: [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links' }, 
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'about' }
	]
});

//------------  UPDATE TEXT AREA BEFORE SERIALIZE ---------------//
function CKupdate()
{
    for ( instance in CKEDITOR.instances )
	{
        CKEDITOR.instances[instance].updateElement();
	}
}

function updateConfig(formName)
{
	load_in();
	CKupdate();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url:"controller/settingController.php?updateConfig",
		type:"POST", cache:"false", data: formData,
		success: function(rs){
			load_out();
			console.log(rs);
		}
	});
}


function openShop()
{
	$("#shopOpen").val(1);
	$("#btn-sclose").removeClass('btn-danger');
	$("#btn-sopen").addClass('btn-success');
}

function closeShop()
{
	$("#shopOpen").val(0);
	$("#btn-sopen").removeClass('btn-success');
	$("#btn-sclose").addClass('btn-danger');
}

function openSystem()
{
	$("#closed").val(0);
	$("#btn-close").removeClass('btn-danger');
	$("#btn-open").addClass('btn-success');
}

function closeSystem()
{
	$("#closed").val(1);
	$("#btn-open").removeClass('btn-success');
	$("#btn-close").addClass('btn-danger');
}


$(".prefix").keyup(function(e) {
    var pf = $(this).val();
	var du = 0;
	if(pf != "")
	{
		$(".prefix").each(function(index, element) {
            var val = $(this).val();
			if(val == pf){ du += 1; }
        });
		if(du > 1 )
		{
			swal("ตัวย่อซ้ำ");
			$(this).val('');
			return false;
		}
	}
});
</script>