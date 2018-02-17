<?php 
	$pageName	= "การตั้งค่าการแจ้งเตือน";
	$id_tab 		= 25;
	$id_profile 	= $_COOKIE['profile_id'];
    $pm		= checkAccess($id_profile, $id_tab);
	$view 	= $pm['view'];
	$add 		= $pm['add'];
	$edit 		= $pm['edit'];
	$delete 	= $pm['delete'];
	accessDeny($view);
	require 'function/setting_helper.php';
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
<div class="row">
    <div class="col-sm-2 padding-right-0" style="padding-top:15px;">
        <ul id="myTab1" class="setting-tabs">
                <li class="li-block active"><a href="#invent" data-toggle="tab">หลังบ้าน</a></li>
                <li class="li-block"><a href="#sale" data-toggle="tab">พนักงานขาย</a></li>
                <li class="li-block"><a href="#shop" data-toggle="tab">ลูกค้า</a></li>        
        </ul>
    </div>
	<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; max-height:1000px;">
		<div class="tab-content">
        <!-------------------------------------------------------  แจ้งเตือนหลังบ้าน  ----------------------------------------------------->
        <?php $qs = dbQuery("SELECT * FROM tbl_popup WHERE pop_on = 'back'");	?>
        <?php $rs = dbFetchArray($qs); ?>
        <?php $open	= $rs['active'] == 1 ? 'btn-success' : ''; ?>
        <?php $close	= $rs['active'] == 0 ? 'btn-danger' : ''; ?>
            <div class="tab-pane fade active in" id="invent">
            	<form id="inventForm">
                <input type="hidden" name="pop_on" value="back" />
            	<div class="row">
                    <div class="col-sm-3"><span class="form-control left-label">ความถี่ในการแสดงผล</span></div>
                    <div class="col-sm-9">
                    	<select class="form-control input-sm input-medium input-line" name="delay">
                        <?php echo selectInterval($rs['delay']); ?>
                        </select>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความกว้าง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="width" value="<?php echo $rs['width']; ?>" />
                        <span class="help-block">กำหนดขนาดความกว้างของหน้าต่างแจ้งเตือน หน่วยเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความสูง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="height" value="<?php echo $rs['height']; ?>" />
                        <span class="help-block">กำหนดขนาดความสูงของหน้าต่างแจ้งเตือน หน่วนเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ช่วงเวลาที่ใช้งาน</span></div>
                    <div class="col-sm-6">
                    	<div class="input-group">
                            <span class="input-group-addon" style="border-radius:0px;">เริ่มต้น</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="start" id="invent-From" style="border-radius:0px;" value="<?php echo thaiDate($rs['start']); ?>" />
                            <span class="input-group-addon" style="border-radius:0px;">สิ้นสุด</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="end" id="invent-To" style="border-radius:0px;" value="<?php echo thaiDate($rs['end']); ?>" />
                        </div>
                    </div> 
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เนื้อหา</span></div>
                    <div class="col-sm-9">
                    	<textarea id="invent-content" name="content"><?php echo $rs['content']; ?></textarea>
						<p style='margin-top:15px;'><label ><input type='checkbox' name="updateAll" value="1" style="margin-right:15px;" /> ใช้ร่วมกันทั้งหมด</label></p>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เปิดใช้งาน</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-inventActive" onClick="inventActive()"><i class="fa fa-check"></i>  เปิด</button>
                            <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-inventDisactive" onClick="inventDisactive()"><i class="fa fa-ban"></i>  ปิด</button>
                        </div>
                    	<input type="hidden" name="active" id="invent-Active" value="<?php echo $rs['active']; ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-9 col-sm-offset-3"><button type="button" class="btn btn-sm btn-success input-mini" onClick="updatePopup('inventForm')"><i class="fa fa-save"></i> บันทึก</button></div>
                    <div class="divider-hidden"></div>
                  
            	</div><!--/ row -->
                </form>
            </div>
            
		<!---------------------------------------------------------------------  แจ้งเติอนพนักงานขาย  ---------------------------------------------------->    
        <?php $qs = dbQuery("SELECT * FROM tbl_popup WHERE pop_on = 'sale'");	?>
        <?php $rs = dbFetchArray($qs); ?>
        <?php $open	= $rs['active'] == 1 ? 'btn-success' : ''; ?>
        <?php $close	= $rs['active'] == 0 ? 'btn-danger' : ''; ?>      
            <div class="tab-pane fade" id="sale">
            	<form id="saleForm">
                <input type="hidden" name="pop_on" value="sale" />
            	<div class="row">
                    <div class="col-sm-3"><span class="form-control left-label">ความถี่ในการแสดงผล</span></div>
                    <div class="col-sm-9">
                    	<select class="form-control input-sm input-medium input-line" name="delay">
                        <?php echo selectInterval($rs['delay']); ?>
                        </select>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความกว้าง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="width" value="<?php echo $rs['width']; ?>" />
                        <span class="help-block">กำหนดขนาดความกว้างของหน้าต่างแจ้งเตือน หน่วยเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความสูง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="height" value="<?php echo $rs['height']; ?>" />
                        <span class="help-block">กำหนดขนาดความสูงของหน้าต่างแจ้งเตือน หน่วนเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ช่วงเวลาที่ใช้งาน</span></div>
                    <div class="col-sm-6">
                    	<div class="input-group">
                            <span class="input-group-addon" style="border-radius:0px;">เริ่มต้น</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="start" id="sale-From" style="border-radius:0px;" value="<?php echo thaiDate($rs['start']); ?>" />
                            <span class="input-group-addon" style="border-radius:0px;">สิ้นสุด</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="end" id="sale-To" style="border-radius:0px;" value="<?php echo thaiDate($rs['end']); ?>" />
                        </div>
                    </div> 
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เนื้อหา</span></div>
                    <div class="col-sm-9">
                    	<textarea id="sale-content" name="content"><?php echo $rs['content']; ?></textarea>
						<p style='margin-top:15px;'><label ><input type='checkbox' name="updateAll" value="1" style="margin-right:15px;" /> ใช้ร่วมกันทั้งหมด</label></p>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เปิดใช้งาน</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-saleActive" onClick="saleActive()"><i class="fa fa-check"></i>  เปิด</button>
                            <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-saleDisactive" onClick="saleDisactive()"><i class="fa fa-ban"></i>  ปิด</button>
                        </div>
                    	<input type="hidden" name="active" id="sale-Active" value="<?php echo $rs['active']; ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-9 col-sm-offset-3"><button type="button" class="btn btn-sm btn-success input-mini" onClick="updatePopup('saleForm')"><i class="fa fa-save"></i> บันทึก</button></div>
                    <div class="divider-hidden"></div>
                  
            	</div><!--/ row -->
                </form>
            </div>
            
        <!--------------------------------------------------  แจ้งเตือนลูกค้า  ------------------------------------------------------->      
        <?php $qs = dbQuery("SELECT * FROM tbl_popup WHERE pop_on = 'front'");	?>
        <?php $rs = dbFetchArray($qs); ?>
        <?php $open	= $rs['active'] == 1 ? 'btn-success' : ''; ?>
        <?php $close	= $rs['active'] == 0 ? 'btn-danger' : ''; ?>      
            <div class="tab-pane fade" id="shop">
            	<form id="shopForm">
                <input type="hidden" name="pop_on" value="front" />
            	<div class="row">
                    <div class="col-sm-3"><span class="form-control left-label">ความถี่ในการแสดงผล</span></div>
                    <div class="col-sm-9">
                    	<select class="form-control input-sm input-medium input-line" name="delay">
                        <?php echo selectInterval($rs['delay']); ?>
                        </select>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความกว้าง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="width" value="<?php echo $rs['width']; ?>" />
                        <span class="help-block">กำหนดขนาดความกว้างของหน้าต่างแจ้งเตือน หน่วยเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ความสูง (px)</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini input-line" name="height" value="<?php echo $rs['height']; ?>" />
                        <span class="help-block">กำหนดขนาดความสูงของหน้าต่างแจ้งเตือน หน่วนเป็นพิกเซล</span>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">ช่วงเวลาที่ใช้งาน</span></div>
                    <div class="col-sm-6">
                    	<div class="input-group">
                            <span class="input-group-addon" style="border-radius:0px;">เริ่มต้น</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="start" id="shop-From" style="border-radius:0px;" value="<?php echo thaiDate($rs['start']); ?>" />
                            <span class="input-group-addon" style="border-radius:0px;">สิ้นสุด</span>
                            <input type="text" class="form-control input-sm input-mini text-center" name="end" id="shop-To" style="border-radius:0px;" value="<?php echo thaiDate($rs['end']); ?>" />
                        </div>
                    </div> 
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เนื้อหา</span></div>
                    <div class="col-sm-9">
                    	<textarea id="shop-content" name="content"><?php echo $rs['content']; ?></textarea>
						<p style='margin-top:15px;'><label ><input type='checkbox' name="updateAll" value="1" style="margin-right:15px;" /> ใช้ร่วมกันทั้งหมด</label></p>
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-3"><span class="form-control left-label">เปิดใช้งาน</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $open; ?>" style="width:50%;" id="btn-shopActive" onClick="shopActive()"><i class="fa fa-check"></i>  เปิด</button>
                            <button type="button" class="btn btn-sm <?php echo $close; ?>" style="width:50%;" id="btn-shopDisactive" onClick="shopDisactive()"><i class="fa fa-ban"></i>  ปิด</button>
                        </div>
                    	<input type="hidden" name="active" id="shop-Active" value="<?php echo $rs['active']; ?>" />
                    </div>
                    <div class="divider-hidden"></div>
                    
                    <div class="col-sm-9 col-sm-offset-3"><button type="button" class="btn btn-sm btn-success input-mini" onClick="updatePopup('shopForm')"><i class="fa fa-save"></i> บันทึก</button></div>
                    <div class="divider-hidden"></div>
                  
            	</div><!--/ row -->
                </form>
            </div>
		</div><!--/ tab-content -->
	</div><!--/ col-sm-9  -->    
</div><!--/ row  -->

</div><!---/ container -->
<script>
CKEDITOR.replace( 'invent-content',{
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

CKEDITOR.replace( 'sale-content',{
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

CKEDITOR.replace( 'shop-content',{
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
</script>

<script>
function updatePopup(formName)
{
	load_in();
	CKupdate();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url:"controller/settingController.php?updatePopup",
		type:"POST", cache:"false", data: formData,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title: 'ปรับปรุงข้อมูลเรียบร้อย', timer: 1000, type: 'success' });
			}
		}
	});			
}

//------------  UPDATE TEXT AREA BEFORE SERIALIZE ---------------//
function CKupdate()
{
    for ( instance in CKEDITOR.instances )
	{
        CKEDITOR.instances[instance].updateElement();
	}
}

$("#invent-From").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#invent-To").datepicker("option", "minDate", se); } });
$("#invent-To").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#invent-From").datepicker("option", "maxDate", se); } });
$("#sale-From").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#sale-To").datepicker("option", "minDate", se); } });
$("#sale-To").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#sale-From").datepicker("option", "maxDate", se); } });
$("#shop-From").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#shop-To").datepicker("option", "minDate", se); } });
$("#shop-To").datepicker({ dateFormat: 'dd-mm-yy', onClose: function(se){ $("#shop-From").datepicker("option", "maxDate", se); } });

function inventActive()
{
	$("#invent-Active").val(1);
	$("#btn-inventDisactive").removeClass('btn-danger');
	$("#btn-inventActive").addClass('btn-success');
}

function inventDisactive()
{
	$("#invent-Active").val(0);
	$("#btn-inventActive").removeClass('btn-success');
	$("#btn-inventDisactive").addClass('btn-danger');	
}

function saleActive()
{
	$("#sale-Active").val(1);
	$("#btn-saleDisactive").removeClass('btn-danger');
	$("#btn-saleActive").addClass('btn-success');
}

function saleDisactive()
{
	$("#sale-Active").val(0);
	$("#btn-saleActive").removeClass('btn-success');
	$("#btn-saleDisactive").addClass('btn-danger');	
}

function shopActive()
{
	$("#shop-Active").val(1);
	$("#btn-shopDisactive").removeClass('btn-danger');
	$("#btn-shopActive").addClass('btn-success');
}

function shopDisactive()
{
	$("#shop-Active").val(0);
	$("#btn-shopActive").removeClass('btn-success');
	$("#btn-shopDisactive").addClass('btn-danger');	
}

</script>