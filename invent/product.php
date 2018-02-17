<?php
	$page_name	= "เพิ่ม/แก้ไข รายการสินค้า";
	$id_tab 			= 1;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	require 'function/product_helper.php';
	?>
<script src="<?php echo WEB_ROOT; ?>library/js/dropzone.js"></script>
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/dropzone.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>library/css/colorbox.css" />
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-tags"></i> <?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) ) : ?>
        		<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if( $add OR $edit ) : ?>               
				<?php if( ! isset( $_GET['id_product'] ) ) : ?>               
                    <button type="button" class="btn btn-sm btn-success" onClick="addProduct()"><i class="fa fa-save"></i> บันทึก</button>
                <?php endif; ?>
			<?php endif; ?>                           
        <?php else : ?>
        	<button type="button" class="btn btn-sm btn-success" onClick="newProduct()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
		<?php endif; ?>
        </p>
    </div>
</div>
<hr style="margin-bottom:0px;"/>
<?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) ) : ?>
<?php	$id_product 	= isset( $_GET['id_product'] ) ? $_GET['id_product'] : '' ; ?>
<?php	$rs				= getProductDetail($id_product); //-----  ดึงข้อมูลจากตาราง tbl_product	?>
<?php 	$id_pd			= $id_product == '' ? 0 : $id_product;		?>
<?php	$vs				= $rs['is_visual'] == 1 ? 'btn-success' : '';	?>
<?php	$nvs				= $rs['is_visual'] == 0 ? 'btn-danger' : '';		?>
<?php	$ac				= $rs['active'] == 1 ? 'btn-success' : ''; 		?>
<?php	$dac				= $rs['active'] == 0 ? 'btn-danger' : ''; 		?>
<?php	$is					= $rs['show_in_shop'] == 1 ? 'btn-success' : ''; ?>
<?php	$nis				= $rs['show_in_shop'] == 0 ? 'btn-danger' : ''; 	?>
<?php	$activeTab		= isset( $_GET['tab'] ) ? $_GET['tab'] : 1 ; ?>
<?php	$tab1				= $activeTab == 1 ? 'active in' : '';		?>
<?php	$tab2				= $activeTab == 2 ? 'active in' : '' ; 	?>
<?php	$tab3				= $activeTab == 3 ? 'active in' : '';	?>
<div class="row">
    <div class="col-sm-2 padding-right-0" style="padding-top:15px;">
        <ul id="myTab1" class="setting-tabs">
    <?php if( ! isset( $_GET['id_product'] ) ) : ?>
               <li class="li-block active"><a href="#tab1" data-toggle="tab">ข้อมูลสินค้า</a></li>
    <?php else: ?>
                <li class="li-block <?php echo $tab1; ?>" onClick="changeURL(1)"><a href="#tab1" data-toggle="tab">ข้อมูลสินค้า</a></li>
                <li class="li-block <?php echo $tab2; ?>" onClick="changeURL(2)"><a href="#tab2" data-toggle="tab">รายการสินค้า</a></li>
                <li class="li-block <?php echo $tab3; ?>" onClick="changeURL(3)"><a href="#tab3" data-toggle="tab">รูปภาพ</a></li>        
    <?php endif; ?>
       </ul>
    </div>
	<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px;" >
		<div class="tab-content">
		<!-------------------------------------------------------  ข้อมูลสินค้า  ----------------------------------------------------->
            <div class="tab-pane fade <?php echo $tab1; ?>" id="tab1">
            <form id="productForm">
            	<div class="row">
                	<div class="col-sm-3"><span class="form-control label-left">รหัสสินค้า</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-large inline" name="pCode" id="pCode" value="<?php echo $rs['product_code']; ?>" placeholder="กำหนดรหัสของรุ่นสินค้า" autofocus  />
                        <span id="pCode-error" class="label-left red" style="margin-left:15px; display:none;">รหัสสินค้าซ้ำ</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ชื่อสินค้า</span></div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control input-sm input-large inline" name="pName" id="pName" value="<?php echo $rs['product_name']; ?>" placeholder="กำหนดชื่อของสินค้า"  />
                        <span id="pName-error" class="label-left red" style="margin-left:15px; display:none;">จำเป็นต้องกำหนดช่องนี้</span>
                    </div>                    
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    
                    <div class="col-sm-3"><span class="form-control label-left">กลุ่มสินค้า</span></div>
                    <div class="col-sm-9">
                        <select class="form-control input-sm input-large" name="pGroup" id="pGroup">
                        <?php echo selectProductGroup($rs['id_product_group']); ?>
                        </select>
                    </div>                    
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    
                    <div class="col-sm-3"><span class="form-control label-left">หมวดหมู่หลัก</span></div>
                    <div class="col-sm-9">
                    	<select class="form-control input-sm input-large" name="dCategory" id="dCategory">
                        	<?php echo categoryList($rs['default_category_id']); ?>
                        </select>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">หมวดหมู่ย่อย</span></div>
                    <div class="col-sm-9"><?php echo categoryTree($id_pd);  ?></div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ทุน</span></div>
                    <div class="col-sm-9">
                    <input type="text" class="form-control input-sm input-mini inline" name="cost" id="cost" value="<?php echo $rs['product_cost']; ?>"  />
                    <span id="cost-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ราคาขาย</span></div>
                    <div class="col-sm-9">
                    <input type="text" class="form-control input-sm input-mini inline" name="price" id="price" value="<?php echo $rs['product_price']; ?>" />
                    <span id="price-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ส่วนลด</span></div>
                    <div class="col-sm-9">
                    <input type="text" class="form-control input-sm input-mini" style="position:relative; float:left; margin-right:5px;" name="discount" id="discount" value="<?php echo $rs['discount']; ?>" />
                    <select class="form-control input-sm input-mini inline" name="discount_type" id="dType">
                        <option value="percentage" <?php echo isSelected('percentage', $rs['discount_type']); ?>>เปอร์เซ็นต์</option>
                        <option value="amount" <?php echo isSelected('amount', $rs['discount_type']); ?>>จำนวนเงิน</option>
                     </select>
                     <span id="discount-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
                    </div>
                   
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">น้ำหนัก</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini inline ops" name="weight" id="weight" value="<?php echo $rs['weight']; ?>" />
                        <span class="label-left inline" style="margin-left:15px;">กิโลกรัม</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ความกว้าง</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini inline ops" name="width" id="width" value="<?php echo $rs['width']; ?>" />
                        <span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">ยาว</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini inline ops" name="length" id="length" value="<?php echo $rs['length']; ?>" />
                        <span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">สูง</span></div>
                    <div class="col-sm-9">
                    	<input type="text" class="form-control input-sm input-mini inline ops" name="height" id="height" value="<?php echo $rs['height']; ?>" />
                        <span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">สินค้าเสมือน</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $vs; ?>" id="btn-vs" onClick="toggleVisual(1)" style="width:50%;">ใช่</button>
                            <button type="button" class="btn btn-sm <?php echo $nvs; ?>" id="btn-nvs" onClick="toggleVisual(0)" style="width:50%;">ไม่ใช่</button>
                        </div>
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">เปิดใช้งาน</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $ac; ?>" id="btn-ac" onClick="toggleActived(1)" style="width:50%;">ใช่</button>
                            <button type="button" class="btn btn-sm <?php echo $dac; ?>" id="btn-dac" onClick="toggleActived(0)" style="width:50%;">ไม่ใช่</button>
                        </div>
                        
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">แสดงในหน้าลูกค้า</span></div>
                    <div class="col-sm-9">
                    	<div class="btn-group input-small">
                        	<button type="button" class="btn btn-sm <?php echo $is; ?>" id="btn-is" onClick="toggleInShop(1)" style="width:50%;">ใช่</button>
                            <button type="button" class="btn btn-sm <?php echo $nis; ?>" id="btn-nis" onClick="toggleInShop(0)" style="width:50%;">ไม่ใช่</button>
                        </div>
                        
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
                    
                    <div class="col-sm-3"><span class="form-control label-left">คำอธิบายสินค้า</span></div>
                    <div class="col-sm-9">
                    	<textarea class="form-control input-xlarge" name="description" rows="4" placeholder="กำหนดคำอธิบายสินค้า ( สำหรับลูกค้า )"><?php echo productDescription($id_pd); ?></textarea>
                    </div>
                    <input type="hidden" name="id_product" id="id_product" value="<?php echo $id_product; ?>" /><!-- ถ้าเพิ่มใหม่ยังไม่บันทึก id_product = '' --->
                    <input type="hidden" name="isVisual" id="isVisual" value="<?php echo $rs['is_visual']; ?>" />
                    <input type="hidden" name="active" id="active" value="<?php echo $rs['active']; ?>" />
                    <input type="hidden" name="inShop" id="inShop" value="<?php echo $rs['show_in_shop']; ?>" />
                    <input type="hidden" id="isDuplicated" value="0" />
                    <div class="divider-hidden" style="margin-top:25px; margin-bottom:25px;"></div>
                    
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                    <?php if( $id_pd != 0 ) : ?>
                        <button type="button" class="btn btn-success input-xlarge" onClick="saveProduct(<?php echo $id_pd; ?>)" ><i class="fa fa-save"></i> บันทึก</button>
					<?php endif; ?>                        
                    </div>
                    <div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

				</div>                    
            </form>
            </div><!--/ tab-pane #tab1 -->
		<!-------------------------------------------------------  รายการสินค้า  ----------------------------------------------------->        
         <div class="tab-pane fade <?php echo $tab2; ?>" id="tab2">
         	<div class="row">
            	<div class="col-sm-12">
                	<button type="button" class="btn btn-sm btn-primary" onClick="getGenerate()">สร้างรายการสินค้า</button>
                    <button type="button" class="btn btn-sm btn-info" onClick="setImage()">เชื่อมโยงรูปภาพ</button>
                    <!-- <button type="button" class="btn btn-sm btn-warning" onClick="groupEdit()">แก้ไขหลายรายการ</button> -->
                </div>
            </div>
            <hr/>
        	
                <?php if( $id_pd != 0 ) : ?>
                <?php 	$qs = dbQuery("SELECT * FROM tbl_product_attribute WHERE id_product = ".$id_pd); ?>
                <?php	if( dbNumRows($qs) > 0 ) : ?>
                <div class="row">
                	<div class="col-sm-12">
                    	<p class="pull-right top-p">
                            <button type="button" class="btn btn-sm btn-default" onClick="editBarcode()">แก้ไขบาร์โค้ด</button>
                            <button type="button" class="btn btn-sm btn-default" onClick="editBarcodePack()">แก้ไขบาร์โค้ดแพ็ค</button>
                            <button type="button" class="btn btn-sm btn-default" onClick="editPackQty()">แก้ไขจำนวนในแพ็ค</button>
						</p>                            
                    </div>
                </div>
                <hr/>
                <div class="row">
				<div class="col-sm-12">
                	<table class="table table-striped">
                    	<thead>
                        	<tr style="font-size:12px;">
                            	<th style="width:5%; text-align:center">รูปภาพ</th>
                                <th style="width:15%; ">รหัสอ้างอิง</th>
                                <th style="width:15%; text-align:center;">บาร์โค้ด</th>
                                <th style="width:15%; text-align:center;">บาร์โค้ดแพ็ค</th>
                                <th style="width:5%; text-align:center;">จำนวน</th>
                                <th style="width:8%; text-align:center;">สี</th>
                                <th style="width:8%; text-align:center;">ไซส์</th>
                                <th style="width:8%; text-align:center;">อื่นๆ</th>
                                <th style="width:5%; text-align:center;">ทุน</th>
                                <th style="width:5%; text-align:center;">ขาย</th>
                                <th style="width:5%; text-align:center;">สถานะ</th> 
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="sku-table">
                        <?php $n = 1; ?>
                        <?php while( $rs = dbFetchArray($qs) ) : ?>
                        <?php		$id_pa = $rs['id_product_attribute'];	?>
                        <?php		$iPath		= getProductAttributeImagePath($id_pa, 1); 	?>
                        <?php		$imgPath	= $iPath === FALSE ? getCoverImagePath($rs['id_product'], 1) : $iPath;	?>
                        <?php		$pack		= getProductPack($id_pa);	?>
                        	<tr id="row-<?php echo $id_pa; ?>" style="font-size:10px;">
                            	<td align="center"><img src="<?php echo $imgPath; ?>" width="40px" /></td>
                                <td class="middle"><?php echo $rs['reference']; ?></td>
                                <td class="middle text-center">
                                	<span class="bc-label" id="bc-<?php echo $id_pa; ?>-label"><?php echo $rs['barcode']; ?></span>
                                    <input type="text" class="form-control input-sm barcode hide no-<?php echo $n; ?>" id="bc-<?php echo $id_pa; ?>" value="<?php echo $rs['barcode']; ?>" />
                                    <input type="hidden" id="bc-<?php echo $id_pa; ?>-id" value="<?php echo $id_pa; ?>" />
                                    <input type="hidden" id="bc-<?php echo $id_pa; ?>-no" value="<?php echo $n; ?>" />
                                </td>
                                <td class="middle text-center">
									<span class="bp-label" id="bp-<?php echo $id_pa; ?>-label"><?php echo $pack['barcode']; ?></span>
                                    <input type="text" class="form-control input-sm barcode-pack hide bp-no-<?php echo $n; ?>" id="bp-<?php echo $id_pa; ?>" value="<?php echo $pack['barcode']; ?>" />
                                    <input type="hidden" id="bp-<?php echo $id_pa; ?>-id" value="<?php echo $id_pa; ?>" />                                    
                                    <input type="hidden" id="bp-<?php echo $id_pa; ?>-no" value="<?php echo $n; ?>" />
                                </td>
                                <td align="center" class="middle">
                                	<span class="pqty-label" id="pqty-<?php echo $id_pa; ?>-label"><?php echo $pack['qty']; ?></span>
                                    <input type="text" class="form-control input-sm pack-qty hide pqty-no-<?php echo $n; ?>"	 id="pqty-<?php echo $id_pa; ?>" value="<?php echo $pack['qty'];?>" />
                                    <input type="hidden" id="pqty-<?php echo $id_pa; ?>-id" value="<?php echo $id_pa; ?>" />
                                    <input type="hidden" id="pqty-<?php echo $id_pa; ?>-no" value="<?php echo $n; ?>" />
                                </td>
                                <td align="center" class="middle"><?php echo get_color_code($rs['id_color']); ?></td>
                                <td align="center" class="middle"><?php echo get_size_name($rs['id_size']); ?></td>
                                <td align="center" class="middle"><?php echo get_attribute_name($rs['id_attribute']); ?></td>
                                <td align="center" class="middle"><?php echo number_format($rs['cost'], 2); ?></td>
                                <td align="center" class="middle"><?php echo number_format($rs['price'], 2); ?></td>
                                <td align="center" class="middle"><a href="javascript:void(0)" id="active_<?php echo $id_pa; ?>" onclick="toggleActiveItem(<?php echo $id_pa; ?>)"><?php echo isActived($rs['active']); ?></a></td>
                                <td align="right" class="middle">
                                	<button type="button" class="btn btn-xs btn-warning" onClick="getEdit(<?php echo $id_pa; ?>)"><i class="fa fa-pencil"></i></button>
                                    <button type="button" class="btn btn-xs btn-danger" onClick="getDelete(<?php echo $id_pa; ?>,'<?php echo $rs['reference'] ; ?>')"><i class="fa fa-trash"></i></button>                                
                                </td>
                            </tr>
                           <?php $n++; ?>
						<?php endwhile; ?>                             
                        </tbody>
                    </table>
					</div>
                </div>                    
				<?php else : ?>
                <div class="row">
				<div class="col-sm-12">
						<h4 style="text-align:center; padding-top:50px; color:#AAA;"><i class="fa fa-tags fa-2x"></i> No SKU Now</h4>     
				</div> 
                </div>                                                 
				<?php	endif; ?>                    
				<?php endif; ?>	
             
            <div class="modal fade" id="itemEditModal" tabindex="-1" role="dialog" aria-labelledby="itemedit" aria-hidden="true">
            	<div class="modal-dialog" style="width:600px">
                	<div class="modal-content">
                    	<div class="modal-header">
                            <h4 class="modal-title text-center">แก้ไขรายการสินค้า</h4>
                        </div>
                        <div class="modal-body" id="itemEditModalBody">
                            
                        </div>
                        <div class="modal-footer">
                        	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
                            <button type="button" class="btn btn-sm btn-primary" onClick="saveEdit()"><i class="fa fa-save"></i> บันทึก</button>
                        </div>
                    </div>
                </div>
            </div>
			<form id="mappingForm">
            <div class="modal fade" id="imageMappingTable" tabindex="-1" role="dialog" aria-labelledby="mapping" aria-hidden="true">
            	<div class="modal-dialog" style="width:1000px">
                	<div class="modal-content">
                    	<div class="modal-header">
                            <h4 class="modal-title">จับคู่รูปภาพกับสินค้า</h4>
                        </div>
                        <div class="modal-body" id="mappingBody">
                        
                        </div>
                        <div class="modal-footer">
                        	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
                            <button type="button" class="btn btn-sm btn-primary" onClick="doMapping()">ดำเนินการ</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>    				
         </div><!--/ tab-pane #tab2 -->
         
         <!-----------------------------------------------------  รูปภาพ  ---------------------------------------------------->
         <div class="tab-pane fade <?php echo $tab3; ?>" id="tab3">
         	<div class="row">
         		<div class="col-sm-4"><span class="form-control label-right"><h4 class="title">เพิ่มรูปภาพสำหรับสินค้านี้</h4></span></div>
                    <div class="col-sm-4">
                    	<button type="button" class="btn btn-primary btn-block" onClick="showUploadBox()"><i class="fa fa-cloud-upload"></i> เพิ่มรูปภาพ</button>
                    </div>
                    <div class="col-sm-4"><span class="help-block" style="margin-top:15px; margin-bottom:0px;">ไฟล์ : jpg, png, gif ขนาดสูงสุด 2 MB</span></div>
			</div><!--/ row -->
            <hr/>
            <div class="row" id="imageTable">
            <?php if( $id_pd != 0 ) : ?>
            <?php 	$qs = getProductImage($id_pd);	?>
            <?php 	if( dbNumRows($qs) > 0 )	:	?>
            <?php		while( $rs = dbFetchArray($qs) ) : 	?>
            <?php			$id_img 	= $rs['id_image'];		?>
            <?php			$cover	= $rs['cover'] == 1 ? 'btn-success' : ''; ?>
                <div class="col-sm-3" id="div-image-<?php echo $id_img; ?>">
                    <div class="thumbnail">
                        <a data-rel="colorbox" href="<?php echo imagePath($id_img, 4); ?>">
                            <img class="img-rounded" src="<?php echo imagePath($id_img, 3); ?>" />
                        </a>
                        <div class="caption">
                            <button type="button" id="btn-cover-<?php echo $id_img; ?>" class="btn btn-sm <?php echo $cover; ?> btn-cover" style="position:relative;" onClick="setAsCover(<?php echo $id_pd; ?>, <?php echo $id_img; ?>)">
                            <i class="fa fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" style="position:absolute; right:25px;" onClick="removeImage(<?php echo $id_pd; ?>, <?php echo $id_img; ?>)"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
			<?php		endwhile; ?>
            <?php	else : ?>
            	<div class="col-sm-12"><h4 style="text-align:center; padding-top:50px; color:#AAA;"><i class="fa fa-file-image-o fa-2x"></i> No image now</h4></div>
            <?php	endif;	?>            	
            <?php endif; ?>
	
         			<!-- Load Image Table with ajax -->            
            </div><!--/ row -->
        
            
            <div class="modal fade" id="uploadBox" tabindex="-1" role="dialog" aria-labelledby="uploader" aria-hidden="true">
            	<div class="modal-dialog" style="width:800px">
                	<div class="modal-content">
                    	<div class="modal-header">
                            <h4 class="modal-title">อัพโหลดรูปภาพสำหรับสินค้านี้</h4>
                        </div>
                        <div class="modal-body">
                        	<form class="dropzone" id="imageForm" action="">
                            </form> 
                        </div>
                        <div class="modal-footer">
                        	<button type="button" class="btn btn-sm btn-default" onClick="clearUploadBox()">ปิด</button>
                            <button type="button" class="btn btn-sm btn-primary" onClick="doUpload()">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
            
         </div><!--/ tab-pane #tab3 -->

		</div><!--/ tab-content -->
	</div><!--/ col-sm-10 -->
</div><!--/ row -->


<script id="editItemTemplate" type="text/x-handlebars-template">
	<form id="editForm">
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4 label-left">
                	<label>รหัสอ้างอิง</label>
                </div>
                <div class="col-sm-8">
                	<input type="text" name="reference" id="editReference" class="form-control input-sm" value="{{ reference }}" />  
					<input type="hidden" name="id_pa" id="id_pa" value="{{ id_pa }}" />  
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>สี</label>
                </div>
                <div class="col-sm-8 top-col">
                	<select class="form-control input-sm" name="color" id="editColor">{{{ colors }}}</select>   
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>ไซส์</label>       
                </div>
                <div class="col-sm-8 top-col">
                	<select class="form-control input-sm" name="size" id="editSize">{{{ sizes }}}</select>   
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>คุณลักษณะ</label>
                </div>
                <div class="col-sm-8 top-col">
                	<select class="form-control input-sm" name="attribute" id="editAttribute">{{{ attributes }}}</select>   
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>บาร์โค้ด</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm" name="barcode" id="editBarcode" value="{{barcode}}" />
                </div>
                
            </div><!--/ row -->
        </div>
                                        
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4 label-left">
                	<label>ทุน</label>
                </div>
                <div class="col-sm-8">
                	<input type="text" name="cost" id="editCost" class="form-control input-sm input-mini input-number" value="{{ cost }}" />    
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>ราคาขาย</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm input-mini input-number" name="price" id="editPrice" value="{{price}}" />
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>น้ำหนัก</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm input-mini inline input-number" name="weight" id="editWeight" value="{{weight}}" />
                <span>กรัม</span>
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>กว้าง</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm input-mini inline input-number" name="width" id="editWidth" value="{{width}}" />
                <span>เซนติเมตร</span>
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>ยาว</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm input-mini inline input-number" name="length" id="editLength" value="{{length}}" />
                <span>เซนติเมตร</span>
                </div>
                <div class="col-sm-4 label-left top-col">
                	<label>สูง</label>
                </div>
                <div class="col-sm-8 top-col">
                	<input type="text" class="form-control input-sm input-mini inline input-number" name="height" id="editHeight" value="{{height}}" />
                <span>เซนติเมตร</span>
                </div>
            </div><!--/ row -->
        </div>
    </div><!--/ row -->
	</form>
</script>

<script id="imageTableTemplate" type="text/x-handlebars-temlate">
{{#each this}}
	{{#if id_img}}
		<div class="col-sm-3" id="div-image-{{ id_img }}">
			<div class="thumbnail">
				<a data-rel="colorbox" href="{{ bigImage }}">
					<img class="img-rounded" src="{{ thumbImage }}" />
				</a>
				<div class="caption">
					<button type="button" id="btn-cover-{{ id_img }}" class="btn btn-sm {{ isCover }} btn-cover" style="position:relative;" onClick="setAsCover({{ id_pd }}, {{ id_img }})"><i class="fa fa-check"></i></button>
					<button type="button" class="btn btn-sm btn-danger" style="position:absolute; right:25px;" onClick="removeImage({{ id_pd }}, {{ id_img }})"><i class="fa fa-trash"></i></button>
				</div>
			</div>
		</div>
	{{else}}
		<div class="col-sm-12"><h4 style="text-align:center; padding-top:50px; color:#AAA;"><i class="fa fa-file-image-o fa-2x"></i> No image now</h4></div>
	{{/if}}
{{/each}}
</script>
<script>
function toggleActiveItem(id_pa)
{
	$.ajax({
		url: 'controller/productController.php?toggleActiveItem',
		type:'POST', cache:'false', data:{"id_pa" : id_pa },
		success: function(rs){
			rs = $.trim(rs);
			if( rs == '1' ){
				$("#active_"+id_pa).html('<i class="fa fa-check" style="color:green"></i>');
			}else{
				$("#active_"+id_pa).html('<i class="fa fa-remove" style="color:red"></i>');	
			}
		}
	});
}
$(document).ready(function(e) {
   setColorbox();
});
//----------------  Dropzone --------------------//
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("#imageForm", {
	url: "controller/productController.php?upload&id_style="+$("#id_product").val(),
	paramName: "file", // The name that will be used to transfer the file
	maxFilesize: 2, // MB
	uploadMultiple: true,
	maxFiles: 5,
	acceptedFiles: "image/*",
	parallelUploads: 5,
	autoProcessQueue: false,
	addRemoveLinks: true
});

myDropzone.on('complete', function(){ 
	clearUploadBox();
	loadImageTable();
});
					
function doUpload()
{
	myDropzone.processQueue();	
}

function clearUploadBox()
{
	$("#uploadBox").modal('hide');
	myDropzone.removeAllFiles();
}

function showUploadBox()
{
	$("#uploadBox").modal('show');	
}


function loadImageTable()
{
	var id_pd = $("#id_product").val();
	load_in();
	$.ajax({
		url:"controller/productController.php?getImageTable",
		type:"POST", cache:"false", data:{ "id_product" : id_pd },
		success: function(rs){
			load_out();
			var source 	= $("#imageTableTemplate").html();
			var data		= $.parseJSON(rs);
			var output	= $("#imageTable");
			render(source, data, output);
			setColorbox();
		}
	});
}

function removeImage(id_pd, id_img)
{
	load_in();
	$.ajax({
		url:"controller/productController.php?removeImage",
		type:"POST", cache:"false", data:{ "id_product" : id_pd, "id_image" : id_img },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				$("#div-image-"+id_img).remove();	
			}
			else
			{
				swal("ข้อผิดพลาด", "ลบรูปภาพไม่สำเร็จ", "error");
			}
		}
	});
}



function setAsCover(id_pd, id_img)
{
	$.ajax({
		url:"controller/productController.php?setCoverImage",
		type:"POST", cache:"false", data:{ "id_product" : id_pd, "id_image" : id_img },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				$(".btn-cover").removeClass('btn-success');
				$("#btn-cover-"+id_img).addClass('btn-success');
			}
		}
	});			
}


function setColorbox()
{
	var colorbox_params = {
				rel: 'colorbox',
				reposition: true,
				scalePhotos: true,
				scrolling: false,
				previous: '<i class="fa fa-arrow-left"></i>',
				next: '<i class="fa fa-arrow-right"></i>',
				close: 'X',
				current: '{current} of {total}',
				maxWidth: '800px',
				maxHeight: '800px',
				opacity:0.5,
				speed: 500,
				onComplete: function(){
					$.colorbox.resize();
				}
		}
		
	$('[data-rel="colorbox"]').colorbox(colorbox_params);
}
</script>
<?php else : ?>
<?php 
	$sCode	= isset( $_POST['sCode'] ) ? $_POST['sCode'] : ( getCookie('sCode') ? getCookie('sCode') : '' );
	$sName	= isset( $_POST['sName'] ) ? $_POST['sName'] : ( getCookie('sName') ? getCookie('sName') : '');
	$sCategory	= isset( $_POST['sCategory'] ) ? $_POST['sCategory'] : ( getCookie('sCategory') ? getCookie('sCategory') : '');
	$sGroup	= isset( $_POST['sGroup'] ) ? $_POST['sGroup'] : ( getCookie('sGroup') ? getCookie('sGroup') : 0 );
	$sStatus	= isset( $_POST['sStatus'] ) ? $_POST['sStatus'] : ( getCookie('sStatus') ? getCookie('sStatus') : 3 );
	$sShop	= isset( $_POST['sShop'] ) ? $_POST['sShop'] : ( getCookie('sShop') ? getCookie('sShop') : 3 );
	$all		= $sStatus == 3 ? 'btn-primary' : '';
	$yes		= $sStatus == 1 ? 'btn-primary'	 : '';
	$no		= $sStatus == 0 ? 'btn-primary' : '';
	$sAll		= $sShop == 3 ? 'btn-primary' : '';
	$sYes	= $sShop == 1 ? 'btn-primary' : '';
	$sNo		= $sShop == 0 ? 'btn-primary' : '';
	
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);	
?>
<style>
	.table > tbody > tr > td {
		vertical-align:middle !important;	
	}
</style>
<form id="searchForm" method="post">
<div class="row">
	<div class="col-sm-2">
    	<label>รหัสสินค้า</label>
        <input type="text" class="form-control input-sm" name="sCode" id="sCode" placeholder="ค้นหารหัสสินค้า" value="<?php echo $sCode; ?>" />
    </div>
    <div class="col-sm-2">
    	<label>ชื่อสินค้า</label>
        <input type="text" class="form-control input-sm" name="sName" id="sName" placeholder="ค้นหาชื่อสินค้า" value="<?php echo $sName; ?>" />
    </div>
    <div class="col-sm-2">
    	<label>หมวดหมู่</label>
        <select class="form-control input-sm" name="sCategory" id="sCategory">
            <?php echo selectCategory($sCategory); ?>
        </select>
    </div>
    <div class="col-sm-2">
    	<label>กลุ่มสินค้า</label>
        <select class="form-control input-sm" name="sGroup" id="sGroup">
        	<option value="0">ทั้งหมด</option>
            <?php echo selectProductGroup($sGroup); ?>
        </select>
    </div>
    <div class="col-sm-1 padding-0" >
    	<label style="display:block;">สถานะ</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm <?php echo $all; ?>" style="width:34%;" id="btn-all" onClick="toggleStatus(3)">All</button>
            <button type="button" class="btn btn-sm <?php echo $yes; ?>" style="width:33%;" id="btn-yes" onClick="toggleStatus(1)"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-sm <?php echo $no; ?>" style="width:33%;" id="btn-no" onClick="toggleStatus(0)"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="col-sm-1 padding-5">
    	<label style="display:block;">แสดงในเว็บ</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm <?php echo $sAll; ?>" style="width:34%;" id="btn-sAll" onClick="toggleShop(3)">All</button>
            <button type="button" class="btn btn-sm <?php echo $sYes; ?>" style="width:33%;" id="btn-sYes" onClick="toggleShop(1)"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-sm <?php echo $sNo; ?>" style="width:33%;" id="btn-sNo" onClick="toggleShop(0)"><i class="fa fa-times"></i></button>
        </div>        
    </div>
    <div class="col-sm-1 padding-0">
    	<label style="display:block; visibility:hidden;">xx</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" onClick="getSearch()">ใช้ตัวกรอง</button>
    </div>
    <div class="col-sm-1" style="padding-left:5px; padding-right:15px;">
    	<label style="display:block; visibility:hidden;">xx</label>
        <button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()">รีเซ็ต</button>
    </div>
</div><!--/ row -->
<input type="hidden" name="sStatus" id="sStatus" value="<?php echo $sStatus; ?>" />
<input type="hidden" name="sShop" id="sShop" value="<?php echo $sShop; ?>" />
</form>
<hr style="margin-top:10px; margin-bottom:10px;"/>

<?php  
	//--------------- เงื่อนไขตัวกรอง -------------//
	$where = "WHERE id_product != 0 ";
	if( $sCode != '' )
	{
		createCookie('sCode', $sCode);
		$where .= "AND product_code LIKE '%".$sCode."%' ";	
	}
	if( $sName != '' )
	{
		createCookie('sName', $sName);
		$where .= "AND product_name LIKE '%".$sName."%' ";	
	}
	if( $sCategory != 0 )
	{
		createCookie('sCategory', $sCategory);
		$in		= productInCategory($sCategory);
		if( $in !== FALSE )
		{
			$where .= "AND id_product IN(".$in.") ";	
		}
	}
	if( $sGroup != 0 )
	{
		createCookie('sGroup', $sGroup);
		$where .= "AND id_product_group = ".$sGroup." ";
	}
	if( $sStatus != 3 )
	{
		createCookie('sStatus', $sStatus);
		$where .= "AND active = ".$sStatus." ";	
	}
	if( $sShop != 3 )
	{
		createCookie('sShop', $sShop);
		$where .= "AND show_in_shop = ".$sShop." ";
	}
	//----------- End เงื่อนไข -----------//
	$where .= "ORDER BY id_product DESC";
?>	

<div class="row">
    <div class="col-sm-12">
    <?php	$paginator->Per_Page("tbl_product",$where,$get_rows);	?>
    <?php	$paginator->display($get_rows,"index.php?content=product"); ?>
    </div>
    <div class="col-sm-12">
        <table class="table table-striped" style="border:solid 1px #ccc;">
        <thead style="font-size:12px;">
            <th style="width:10%; text-align:center;">รูปภาพ</th>
            <th style="width:15%;">รหัสสินค้า</th>
            <th style="width:20%;">ชื่อสินค้า</th>
            <th style="width:10%; text-align:center;">หมวดหมู่</th>
            <th style="width:10%; text-align:center;">กลุ่มสินค้า</th>
            <th style="width:10%; text-align:center;">ราคา</th>
            <th style="width:5%; text-align:center;">สถานะ</th>
            <th style="width:5%; text-align:center;">เว็บ</th>
            <th ></th>
        </thead>
        <tbody>
	<?php	$qs = dbQuery("SELECT * FROM tbl_product ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);		?> 
    <?php 	if( dbNumRows($qs) > 0 ) : ?>
    <?php		while( $rs = dbFetchArray($qs) ) : ?>
    <?php		$id = $rs['id_product']; 			?>
    		<tr id="row-<?php echo $id; ?>" style="font-size:12px;">
            	<td align="center"><img src="<?php echo getCoverImagePath($rs['id_product'], 1); ?>" width="60px" height="60px" /></td>
                <td><?php echo $rs['product_code']; ?></td>
                <td><?php echo $rs['product_name']; ?></td>
                <td align="center"><?php echo categoryName($rs['default_category_id']); ?></td>
                <td align="center"><?php echo productGroupName($rs['id_product_group']); ?></td>
                <td align="center"><?php echo number_format($rs['product_price'], 2); ?></td>
                <td align="center" id="td-active-<?php echo $id; ?>">
                	<button type="button" class="btn btn-link" id="btn-active-<?php echo $id; ?>" onClick="toggleActive(<?php echo $id; ?>)">
					<?php echo isActived($rs['active']); ?>
                    </button>
                </td>
                <td align="center" id="td-shop-<?php echo $id; ?>">
					<button type="button" class="btn btn-link" id="btn-shop-<?php echo $id; ?>" onClick="toggleShowInShop(<?php echo $id; ?>)">
					<?php echo isActived($rs['show_in_shop']); ?>
                    </button>
                </td>
                <td align="right">
                <?php if( $edit OR $add ) : ?>
                	<button type="button" class="btn btn-sm btn-warning" onClick="goEdit(<?php echo $id; ?>)"><i class="fa fa-pencil"></i></button>
				<?php endif; ?>                    
                <?php if( $delete ) : ?>
                	<button type="button" class="btn btn-sm btn-danger" onClick="confirmRemove(<?php echo $id; ?>, '<?php echo $rs['product_code']; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
                </td>
            </tr>
	<?php			endwhile; ?>			
    <?php 	endif; ?>       
        </tbody>
        </table>
<?php 	echo $paginator->display_pages(); ?>        
    </div><!--/ col-sm-12 -->
</div><!--/ row -->
         
            
<?php endif; ?>

</div><!--  end Container -->

<script src="../library/js/jquery.forcenumber.js"></script>
<script src="script/product.js"></script>
<script>
function confirmRemove(id_pd, pCode)
{
	swal({
		title: 'ต้องการลบสินค้า ?',
		text: 'คุณแน่ใจว่าต้องการลบ <span style="color:red; font-weight:bold;">'+pCode+'</span>  โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',	
		type: 'warning',
		html: true,
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false,
		showLoaderOnConfirm: true
		}, function(){
			deleteProduct(id_pd);
		});		
}

//-------------  ตรวจสอบก่อนว่าลบได้หรือไม่ -----------//
function deleteProduct(id_pd)
{
	$.ajax({
		url:"controller/productController.php?deleteProduct",
		type:"POST", cache:"false", data:{ "id_product" : id_pd },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({ title : 'สำเร็จ', text: 'ลบสินค้าเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				$("#row-"+id_pd).remove();
			}else if( rs == 'stockExists' ){
				swal("ข้อผิดพลาด ! ", "ไม่สามารถลบสินค้าได้เนื่องจากมีสินค้าคงเหลือในสต็อก", "error");
			}else{
				swal("ข้อผิดพลาด ! ", "ไม่สามารถลบสินค้าได้เนื่องจากสินค้ามีทรานเซ็คชั่นเกิดขึ้นแล้ว", "error");
			}
		}
	});
}

function goEdit(id)
{
	window.location.href = "index.php?content=product&edit&id_product="+id;	
}

function toggleActived(i)
{
	$("#active").val(i);
	if( i == 0 ){
		$("#btn-dac").addClass('btn-danger');
		$("#btn-ac").removeClass('btn-success');
	}else if( i == 1 ){
		$("#btn-ac").addClass('btn-success');
		$("#btn-dac").removeClass('btn-danger');
	}
}

function toggleActive(id_pd)
{
	$.ajax({
		url:"controller/productController.php?setActive",
		type:"POST", cache:"false", data:{ "id_product" : id_pd },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != "fail" )
			{
				$("#btn-active-"+id_pd).html(rs);
			}
		}
	});
}

function toggleInShop(i)
{
	$("#inShop").val(i);
	if( i == 0 ){
		$("#btn-is").removeClass('btn-success');
		$("#btn-nis").addClass('btn-danger');
	}else if( i == 1 ){
		$("#btn-nis").removeClass('btn-danger');
		$("#btn-is").addClass('btn-success');
	}
}

function toggleShowInShop(id_pd)
{
	$.ajax({
		url:"controller/productController.php?setShowInShop",
		type:"POST", cache:"false", data:{ "id_product" : id_pd },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != "fail" )
			{
				$("#btn-shop-"+id_pd).html(rs);
			}
		}
	});
}


function toggleShop(i)
{
	$("#sShop").val(i);
	if( i == 0 ){	
		$("#btn-sAll").removeClass('btn-primary');
		$("#btn-sYes").removeClass('btn-primary');
		$("#btn-sNo").addClass('btn-primary');
	}else if( i == 1 ){
		$("#btn-sAll").removeClass('btn-primary');
		$("#btn-sYes").addClass('btn-primary');
		$("#btn-sNo").removeClass('btn-primary');
	}else{
		$("#btn-sAll").addClass('btn-primary');
		$("#btn-sYes").removeClass('btn-primary');
		$("#btn-sNo").removeClass('btn-primary');
	}
	getSearch();
}

function toggleStatus(i)
{
	$("#sStatus").val(i);
	if( i == 0 ){	
		$("#btn-all").removeClass('btn-primary');
		$("#btn-yes").removeClass('btn-primary');
		$("#btn-no").addClass('btn-primary');
	}else if( i == 1 ){
		$("#btn-all").removeClass('btn-primary');
		$("#btn-yes").addClass('btn-primary');
		$("#btn-no").removeClass('btn-primary');
	}else{
		$("#btn-all").addClass('btn-primary');
		$("#btn-yes").removeClass('btn-primary');
		$("#btn-no").removeClass('btn-primary');
	}
	getSearch();
}

function getSearch()
{
	$("#searchForm").submit();	
}


function clearFilter()
{
	$.ajax({
		url:"controller/productController.php?clearFilter",
		type: "POST", cache:"false", success: function(rs){
			goBack();
		}
	});
}

$("#sCode").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		if( $(this).val() != '' )
		{
			getSearch();	
		}
	}
});

$("#sName").keyup(function(e){
	if( e.keyCode == 13 )
	{
		if( $(this).val() != '')
		{
			getSearch();
		}
	}
});

$("#sCategory").change(function(e) {
    getSearch();
});

$("#sGroup").change(function(e) {
    getSearch();
});

$("#cost").numberOnly();
$("#price").numberOnly();
$("#discount").numberOnly();
$("#weight").numberOnly();
$("#width").numberOnly();
$("#length").numberOnly();
$("#height").numberOnly();

</script>




