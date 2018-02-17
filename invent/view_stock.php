<!---------------------------------------------------- ดูยอดสต็อกคงเหลือนำยอดที่สั่งมาคำนวนแล้ว --------------------------------->
<!----------------------------------------- Category Menu ---------------------------------->
<div class="container">
<div class='row'>
	<div class='col-sm-12'>
		<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
<?php	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC"); ?>
<?php	if( dbNumRows($sql) > 0 ) :	?>
<?php		while( $rs = dbFetchArray($sql) ) : ?>
<?php			$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = ".$rs['id_category']." ORDER BY position ASC");	 ?>
<?php			if( dbNumRows($sqr) > 0 ) :							?>
					<li class="dropdown">
                    	<a id="ul-<?php echo $rs['id_category']; ?>" class="dropdown-toggle" data-toggle="dropdown" href="javescript:void(0)"><?php echo $rs['category_name']; ?> <span class="caret"></span></a>
                        	<ul class="dropdown-menu" role="menu" aria-labelledby="ul-<?php echo $rs['id_category']; ?>">
                            	<li class="">
                                	<a href="#cat-<?php echo $rs['id_category']; ?>" tabindex="-1" role="tab" data-toggle="tab" onClick="getViewCategory(<?php echo $rs['id_category']; ?>)"><?php echo $rs['category_name']; ?></a>
								</li>
				<?php 	while( $rd = dbFetchArray($sqr) ) : ?>
                				<li class="">
                                	<a href="#cat-<?php echo $rd['id_category']; ?>" tabindex="-1" role="tab" data-toggle="tab" onClick="getViewCategory(<?php echo $rd['id_category']; ?>)"><?php echo $rd['category_name']; ?></a>
								</li> 
                <?php 	endwhile; ?>
							</ul>
						</li>                           
<?php 			else : 		?>
					<li class="">
                    	<a href="#cat-<?php echo $rs['id_category']; ?>" role="tab" data-toggle="tab" onClick="getViewCategory(<?php echo $rs['id_category']; ?>)"><?php echo $rs['category_name']; ?></a>
<?php			endif; 														?>
					</li>
<?php		endwhile; 								?>
<?php	endif; 											?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>	
		<div class='tab-content' style="min-height:1px; padding:0px;">
<?php	$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0"); ?>
<?php	while($c = dbFetchArray($query)) : 	?>
				<div class="tab-pane" id="cat-<?php echo $c['id_category']; ?>"></div>
<?php 	endwhile; ?>
		</div>
	</div>
</div>
<!------------------------------------ End Category Menu ------------------------------------>	
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='modal_title'></h4>
                <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
            </div>
            <div class='modal-body' id='modal_body'></div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>
</div>	
<!---------------------------------------------------- จบหน้าดูสต็อก  ------------------------------------------------>

<script>
//--------------------------------  โหลดรายการสินค้าสำหรับดูยอดคงเหลือ  -----------------------------//
function getViewCategory(id)
{
	var output = $("#cat-"+id);
	if( output.html() == '')
	{
		load_in();
		$.ajax({
			url:"controller/orderController.php?getCategoryGrid",
			type:"POST", cache:"false", data:{ "id_category" : id },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs != 'no_product' ){
					output.html(rs);
				}else{
					swal("ไม่พบข้อมูล", "ไม่พบข้อมูลสินค้าในหมวดหมู่ที่เลือก", "warning");		
				}
			}
		});
	}
}


function view_data(id_product){
	$.ajax({
		url:"controller/orderController.php?view_stock_data&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal('show');
			}else{
				swal("NO DATA");
			}		
		}
	});
}


//----------------------------  โหลดตารางใส่จำนวนสั่งซื้อของสินค้า  --------------------//
function getProduct()
{
	var st 		= $("#sProduct").val();
	var id_cus	= $("#id_customer").val();
	
	if( st == '' ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	
	load_in();
	$.ajax({
		url:"controller/orderController.php?getProductGrid",
		type:"POST", cache: "false", data:{ "product_code" : st, "id_customer" : id_cus },
		success: function(dataset){
			load_out();
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal("show");
			}else{
				swal("ไม่พบสินค้า", "รหัสสินค้าไม่ถูกต้อง หรือ ไม่มีสินค้านี้ในระบบ กรุณาตรวจสอบ", "error");
			}		
		}
	});		
}

</script>