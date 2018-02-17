<style>
	.step {
		border-bottom: solid 3px #CCC;
		font-size:24px;
		color: #CCC;
		height:50px;
		text-align:center;
		vertical-align:middle;
		padding-left:0px;
		padding-right:0px;
	}
	.row-step {
		border-bottom: solid 3px #CCC;
		font-size:24px;
		color: #CCC;
		height:50px;
		text-align:center;
		vertical-align:middle;
		padding-left:0px;
		padding-right:0px;	
	}
	.active-step{
		border-bottom: solid 3px #09F;
		color:#666;
	}
	.multiselect {
    width:100%;
    height:500px;
    border:solid 1px #c0c0c0;
    overflow:auto;
}
 
.multiselect label {
    display:block;
	margin-left:10px;
}
 
.multiselect-on {
    color:#ffffff;
    background-color:#000099;
}
</style>
<?php 
	require_once 'function/product_helper.php';
	$id_pd	= isset( $_GET['id_product'] ) ? $_GET['id_product'] : FALSE;
	$step 	= isset( $_GET['step'] ) ? $_GET['step'] : 1 ; 
	$first 		= $step == 1 ? 'active-step' : '';
	$second = $step == 2 ? 'active-step' : '';
	$third		= $step == 3 ? 'active-step' : '';	
	
?>
<div class="container">
<div class="row">
    <div class="col-sm-12">
        <div class="row-step">
            <div class="col-sm-2 step <?php echo $first; ?>">เลือกคุณลักษณะ</div>
            <div class="col-sm-2 step <?php echo $second; ?>">จับคู่รูปภาพ</div>
            <div class="col-sm-2 step <?php echo $third; ?>">สร้างรายการ</div>   
        </div> 
    </div>     
</div><!--/ row -->

<?php  //------- step 1     เลือก สี / ไซด์ /  อื่นๆ -------//  ?>
<?php if( $id_pd !== FALSE && $step == 1 ) : ?>
<?php	$colors 		= dbQuery("SELECT id_color, color_code, color_name, position FROM tbl_color ORDER BY color_code ASC");		?>
<?php	$sizes 		= dbQuery("SELECT id_size, size_name, position FROM tbl_size ORDER BY position ASC");		?>
<?php	$attributes 	= dbQuery("SELECT id_attribute, attribute_name FROM tbl_attribute ORDER BY attribute_name ASC");		?>
	<form id="form-step1" method="post" action="index.php?content=attribute_gen&id_product=<?php echo $id_pd; ?>&step=2">
	<div class="row">
	    <div class="col-sm-4 top-col">
        	<span style="width:100%; font-size:18px;">เลือกสี</span>
            <div class="multiselect">
            <?php while( $color = dbFetchArray($colors) ) : ?>
            	<label><input type="checkbox" name="color[]" value="<?php echo $color['id_color']; ?>" style="margin-right:15px;" /> <?php echo $color['color_code'].' | '.$color['color_name']; ?></label>
			<?php endwhile; ?>                
            </div>
        </div>
        
         <div class="col-sm-4 top-col">
        	<span style="width:100%; font-size:18px;">เลือกไซส์</span>
            <div class="multiselect">
            <?php while( $size = dbFetchArray($sizes) ) : ?>
            	<label><input type="checkbox" name="size[]" value="<?php echo $size['id_size']; ?>" style="margin-right:15px;" /> <?php echo $size['size_name']; ?></label>
			<?php endwhile; ?>                
            </div>
        </div>
        
        <div class="col-sm-4 top-col">
        	<span style="width:100%; font-size:18px;">เลือกคุณลัษณะอื่นๆ</span>
            <div class="multiselect">
            <?php while( $attr = dbFetchArray($attributes) ) : ?>
            	<label><input type="checkbox" name="attribute[]" value="<?php echo $attr['id_attribute']; ?>" style="margin-right:15px;" /> <?php echo $attr['attribute_name']; ?></label>
			<?php endwhile; ?>                
            </div>
        </div>
        
        <div class="col-sm-4 top-col">
        	<h4><label><input type='radio' name='matching' value='color'  checked='checked'style='margin-right:10px;'/>  จับรูปคู่กับสี</label></h4>
        </div>
        <div class="col-sm-4 top-col">
        	<h4><label><input type='radio' name='matching' value='size' style='margin-right:10px;'/>  จับรูปคู่กับไซต์</label></h4>
        </div>
        <div class="col-sm-4 top-col">
        	<h4><label><input type='radio' name='matching' value='attribute'  style='margin-right:10px;'/>  จับรูปคู่กับคุณลักษณะอื่นๆ</label></h4>
        </div>
        <div class="divider-hidden"></div>
        <div class="col-sm-12">
        	<p class="pull-right">
            	<button type="button" class="btn btn-warning input-medium" onClick="goBack(<?php echo $id_pd; ?>)">ยกเลิก</button>
                <button type="button" class="btn btn-info input-medium" onClick="goToStep2()">ถัดไป <i class="fa fa-arrow-circle-right"></i></button>
            </p>
        </div>
    
    </div><!--/ row -->
	</form>

<?php	endif; ?>

<?php //-----------------------------    Step 2    จับคู่รูปภาพ / ลำดับรหัส    ------------------------// ?>
<?php  if( $id_pd !== FALSE && $step == 2 ) :    
			$colors 	= isset( $_POST['color'] ) ? $_POST['color'] : FALSE;	   
			$sizes	= isset( $_POST['size'] ) ? $_POST['size'] : FALSE;
			$attrs		= isset( $_POST['attribute'] ) ? $_POST['attribute'] : FALSE;
			$match	= isset( $_POST['matching'] ) ? $_POST['matching'] : FALSE;
			$op1		= $colors !== FALSE ? 1 : 0;
			$op2		= $sizes !== FALSE ? 1 : 0;
			$op3		= $attrs !== FALSE ? 1 : 0;
			$ops		= $op1 + $op2 + $op3;
			
?>

<form id="genForm">
    <div class="row">
    <div class="divider-hidden"></div>
<?php	if( $colors !== FALSE ) : ?>
                <div class="col-sm-4">
                    <select name="set_color" id="set_color" class="form-control" onChange="selected_color()" style="margin-bottom:15px;">
                        <option value="1" selected>1</option>
                    <?php if( $ops == 3 OR ( $ops == 2 && ( $attrs !== FALSE OR $sizes !== FALSE  ) ) ) : ?>
                        <option value="2">2</option>
                    <?php endif; ?>
                    <?php if( $ops == 3 ) : ?>
                        <option value="3">3</option>       
                    <?php endif; ?>
                    </select>
                    <input type='hidden' id='hid_color' value='1'>      
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">สี</h3></div>
                        <div class="panel-body">
                        <?php foreach( $colors as $id_color ) : ?>
                            <p>
                                <?php echo get_color_code($id_color); ?>  |  <?php echo color_name($id_color); ?>
                                <input type="hidden" name="color[]" value="<?php echo $id_color; ?>" />
                            </p>
                        <?php endforeach; ?>               	
                        </div>
                    </div>
                </div>
<?php	endif; ?>
<?php 	if( $sizes !== FALSE ) : ?>
                <div class="col-sm-4">
                    <select name="set_size" id="set_size" class="form-control" onChange="selected_size()" style="margin-bottom:15px;">
                    <?php if( $ops == 3 ) : ?>
                        <option value="1">1</option>
                        <option value="2" selected>2</option>
                        <option value="3">3</option>
                    <?php else : ?>
                        <?php if( $ops == 2 && $colors !== FALSE) : ?>
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                        <?php elseif( $ops == 2 && $colors === FALSE ) : ?>
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                        <?php else : ?>
                            <option value="1" selected>1</option>
                        <?php endif; ?>
                    <?php endif; ?>                
                    </select>
                    <input type="hidden" id="hid_size" value="<?php echo $colors !== FALSE ? 2 : 1; ?>" />
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">ไซส์</h3></div>
                        <div class="panel-body">
                        <?php foreach( $sizes as $id_size ) : ?>
                            <p>
                                <?php echo get_size_name($id_size); ?>
                                <input type="hidden" name="size[]" value="<?php echo $id_size; ?>" />
                            </p>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>                
<?php 	endif; ?>

<?php 	if( $attrs !== FALSE ) : ?>
                <div class="col-sm-4">
                    <select name="set_attribute" id="set_attribute" class="form-control" onChange="selected_attribute()" style="margin-bottom:15px;">
                    <?php if( $ops == 3 ) : ?>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3" selected>3</option>
                    <?php else : ?>
                        <?php if( $ops == 2 ) : ?>
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                        <?php else : ?>
                            <option value="1" selected>1</option>
                        <?php endif; ?>            
                    <?php endif; ?>
                    </select>
                    <input type="hidden" id="hid_attribute" value="<?php echo $ops == 3 ? 3 : 2; ?>" />
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">คุณลักษณะอื่นๆ</h3></div>
                        <div class="panel-body">
                        <?php foreach( $attrs as $id_attr ) : ?>
                            <p>
                                <?php echo get_attribute_name($id_attr); ?>
                                <input type="hidden" name="attribute[]" value="<?php echo $id_attr; ?>" />
                            </p>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>    <!--/ col-sm-4 -->            
<?php 	endif; ?>
	</div><!--/ row -->
  
    <hr />

	<div class="row">
<?php $qs = dbQuery("SELECT * FROM tbl_image WHERE id_product = ".$id_pd);	?>
<?php	if( $colors !== FALSE OR $sizes !== FALSE OR $attrs !== FALSE ) :  ?>
<?php	if( dbNumRows($qs) > 0 ) :  ?>
<?php		while( $rs = dbFetchArray($qs) ) : ?>
<?php			$id_image	= $rs['id_image'];		?>
				<div class="col-sm-2">
                	<p><img src="<?php echo imagePath($id_image, 2); ?>" width="100%" /></p>
                    <p>
                    	<select name="image[<?php echo $id_image; ?>]" class="form-control input-sm img">
                        <option value="0">เลือกภาพให้ตรงกับ<?php echo $match == 'color' ? 'สี' : ( $match == 'size' ? 'ไซส์' : 'คุณลักษณะอื่นๆ' ); ?></option>
		<?php if( $match == 'color' ) : ?>
        <?php	foreach( $colors as $id ) : ?>
        				<option value="<?php echo $id; ?>"><?php echo get_color_code($id) .' | '.color_name($id); ?></option>
        <?php 	endforeach;	?>
        <?php endif; ?> 
        <?php if( $match == 'size' ) : ?>
        <?php 	foreach( $sizes as $id ) : ?>
        				<option value="<?php echo $id; ?>"><?php echo get_size_name($id); ?></option>
        <?php	endforeach; ?>
        <?php endif; ?>  
        <?php if( $match == 'attribute' ) : ?>
        <?php	foreach( $attrs as $id ) : ?>
        				<option value="<?php echo $id; ?>"><?php echo get_attribute_name($id); ?></option>
        <?php 	endforeach; ?>
        <?php endif; ?>
       					</select>
					</p>                       
                </div><!--/ col-sm-3 -->
<?php	endwhile; ?>
<?php	endif; ?>        
<?php endif; ?>         
	</div><!--/ row -->
    <input type="hidden" name="matching" id="matching" value="<?php echo $match; ?>" />
    <input type="hidden" name="options" id="options" value="<?php echo $ops; ?>" />
    <input type="hidden" name="id_product" id="id_product" value="<?php echo $id_pd; ?>" />
    <div class="row">
    	<div class="divider-hidden"></div>
        <div class="col-sm-6">
            <a href="javascript:history.back()">
            	<button type="button" class="btn btn-info input-large"><i class="fa fa-arrow-circle-left"></i> ย้อนกลับ</button>
            </a>
        </div>
        <div class="col-sm-6">
        	<p class="pull-right">
            	<button type="button" id="btn-genner" class="btn btn-info input-large" onClick="genProduct()">สร้างรายการ</button>
            </p>
        </div>
        
        </div>
    </div>
</form>    
<?php endif; ?>
</div><!--/ Container -->
<style>

</style>
<script>
function testimg()
{
	var image = $('.img').serialize();  //-- It works 
	console.log(image);
}	

function genProduct()
{
	var id_pd = $("#id_product").val();
	$("#btn-genner").attr('disabled', 'disabled');
	load_in();
	$.ajax({
		url:"controller/productController.php?generateProductAttribute",
		type:"POST", cache:"false", data: $("#genForm").serialize(),
		success: function(rs){
			load_out();
			$("#btn-genner").removeAttr('disabled');
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({ title : 'สำเร็จ', text: 'สร้างรายการสินค้าใหม่เรียบร้อยแล้ว', timer: 1000, type: 'success' });	
				setTimeout(function(){ window.location.href = "index.php?content=product&edit=y&id_product="+id_pd+"&tab=2"; }, 1200);
			}
		}
	});		
}
function goToStep2()
{
	var n = $("input[type=checkbox]:checked").length;
	if( n > 0 ){
		$("#form-step1").submit();
	}else{
		swal("ยังไม่ได้เลือกคุณลักษณะใดๆ");
	}
}

function goBack(id)
{
	window.location.href = "index.php?content=product&edit&id_product="+id+"&tab=2";
}

function selected_color(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_color = $("#hid_color").val();
	if(color == size){
		$("#set_size").val(hid_color);
		$("#hid_size").val(hid_color);
	}else if(color == attribute){
		$("#set_attribute").val(hid_color);
		$("#hid_attribute").val(hid_color);
	}
	$("#hid_color").val(color);
}
function selected_size(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_size = $("#hid_size").val();
	if(size == color){
		$("#set_color").val(hid_size);
		$("#hid_color").val(hid_size);
	}else if(size == attribute){
		$("#set_attribute").val(hid_size);
		$("#hid_attribute").val(hid_size);
	}
	$("#hid_size").val(size);
}
function selected_attribute(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_attribute = $("#hid_attribute").val();
	if(attribute == color){
		$("#set_color").val(hid_attribute);
		$("#hid_color").val(hid_attribute);
	}else if(attribute == size){
		$("#set_size").val(hid_attribute);
		$("#hid_size").val(hid_attribute);
	}
	$("#hid_attribute").val(attribute);
}
</script>