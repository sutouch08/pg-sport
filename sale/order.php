<?php
	$pop_on = "sale";
	$sql = dbQuery("SELECT delay, start, end, content, width, height FROM tbl_popup WHERE pop_on = '$pop_on' AND active =1");
	$row = dbNumRows($sql);
	if($row>0){
		list($delay, $start, $end, $content, $width, $height ) = dbFetchArray($sql);
		$popup_content ="<div class='row' ><div class='col-lg-12'>$content</div></div>";
		include "../library/popup.php";
		$today = date('Y-m-d H:i:s');
		if($start<=$today &&$end>=$today){  
			if(!isset($_COOKIE['pop_sale'])){
				setcookie("pop_sale", $pop_on, time()+$delay);
				echo" <script> $(document).ready(function(e) {  $('#modal_popup').modal('show'); }); </script>";
			}
		}
	}	
?>
<?php 

if(isset($_GET['id_category'])){ $id_cate = $_GET['id_category']; } 
?>
<div class='modal fade' id='customer_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:300px;'>
		<div class='modal-content'>
		  <div class='modal-header'>
			<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
			<h4 class='modal-title' id='myModalLabel' style="text-align:center;">--- เลือกลูกค้า ---</h4>
		 </div>
		 <div class='modal-body'>
		 <form action ="controller/orderController.php?new=y&id_sale=<?php echo $id_sale;?>&content=order" method='post'>
		
           <select name='id_customer' id='id_customer' class='form-control input-sm input-sx'>
		   		<?php customerList( getSaleId( $_COOKIE['user_id'] ) ); ?>
           </select> 
      
		<input type='hidden' name='id_cart' value='<?php echo $id_cart; ?>'>
        
		 </div>
		<div class='modal-footer'>
			<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
			<button type='submit' class='btn btn-primary'>ตกลง</button></form>
		</div>
	</div>
 </div>
</div>

		<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
		
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<form id='order_form' action='controller/orderController.php?add_to_cart' method='post'>
	<input type='hidden' id='id_product' name='id_product'>
	<input type='hidden' id='id_cart' name='id_cart' value="<?php echo $id_cart;?>" >
	<input type='hidden' name='id_customer' value="<?php echo $id_customer; ?>" >
	<div class='modal-dialog' id='modal'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title' id='modal_title'>title</h4>
			</div>
			<div class='modal-body'  id='modal_body'></div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				<button type='button' class='btn btn-primary' onclick="submit_product()">หยิบใส่ตะกร้า</button>
			</div>
             </form>
		</div>
	</div>
</div> 

<div class='row' style='margin-top:10px;'>
				<div class='col-lg-3 col-lg-offset-4 col-md-3 col-md-offset-4 col-sm-5 col-sm-offset-3 col-xs-9'>
            		<input type='text' name='search-text' id='search-text' class='form-control input-xs'  />
                    <input type='hidden' id='id_customer' value='<?php echo $id_customer; ?>' />
				</div>
				<div class='col-lg-1 col-md-1 col-sm-2 col-xs-3'>
                <button type='button' class='btn btn-success btn-block' id='search-btn'><i class="fa fa-search"></i></button>
        		</div>
				</div>
<div class='row'>
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' >
        	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
        </div>
</div>

	<form action ="controller/orderController.php?new=y&id_sale=<?php echo $id_sale;?>" method='post'>
        <div class='row'>
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>
        	<p class='pull-right'>
		<?php if($id_customer !=0) : ?>
		คุณกำลังสั่งสินค้าให้กับ&nbsp; <i class="fa fa-arrow-right"></i>&nbsp; <strong><?php echo customer_name($id_customer); ?></strong>&nbsp;
		<a href='#' data-toggle='modal' data-target='#customer_change'>	
			<button type='button' class='btn btn-warning btn-xs'><i class="fa fa-pencil"></i>&nbsp;แก้ไข</button> 
        </a>
		หรือ 
        	<button type='button' class='btn btn-danger btn-xs' onclick="confirm_delete('โปรดตรวจสอบ','คุณแน่ใจว่าต้องการยกเลิกรายการในตะกร้าสินค้านี้ทั้งหมด','controller/orderController.php?cancle=true&id_cart=<?php echo $id_cart; ?>')"><i class="fa fa-trash"></i>&nbsp;ยกเลิก</button>
        <?php else : ?>
			คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา 
			<a href='#' data-toggle='modal' data-target='#customer_change'>	
            	<button type='button' class='btn btn-success btn-xs'><i class="fa fa-user"></i>&nbsp;เลือกลูกค้า</button> 
            </a>
        <?php endif; ?>             
		</p>
		 </div></div>
		</form>
		<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' ><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div></div>
		
		
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' id='product_grid'>
<?php if(isset($id_cate) ) : ?>    
<?php $sql = dbQuery("SELECT tbl_product.id_product FROM tbl_product  LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = ".$id_cate." AND tbl_product.active =1"); ?>
<?php $row = dbNumRows($sql);  ?>
<?php	if($row>0) :  ?>
<?php		$i=0;  ?>
<?php		while($i<$row) : ?>
<?php			list($id_product) = dbFetchArray($sql); ?>
<?php			$product = new product();	?>
<?php			$product->product_detail($id_product, $id_customer);  ?>

		<div class='item2 col-lg-3 col-md-3 col-sm-6 col-xs-12' style="height:auto;">					
		<div class='product'>
		<div class='image'><a href='#' onclick='getData(<?php echo $product->id_product; ?>)'><?php echo $product->getCoverImage($product->id_product,3,"img-responsive"); ?></a></div>
			<div class='description'>
				<a href='#' onclick='getData(<?php echo $product->id_product; ?>)'><?php echo $product->product_code; ?><br/><?php echo $product->product_name; ?></a>
			</div>
			  <div class='price'>
			  <?php  if($product->product_discount>0) : ?>
			  		<span class='old-price'><?php echo number_format($product->product_price,2); ?></span>
			 <?php else : ?>
             		<span class='old-price'></span>
             <?php endif; ?>
             </div>
			  <div class='action-control'> 
              	<a href='#' data-toggle='modal' data-target='#<?php echo $product->id_product; ?>'>
                	<span class='btn btn-primary' style='width:80%;'><?php echo number_format($product->product_sell,2); ?></span>
                </a> 
             </div>
          </div>
       </div>
<?php $i++;  ?>
<?php	endwhile; ?>		

<?php else : ?>
		<h4 style='align:center;'>ไม่มีรายการสินค้าในหมวดหมู่นี้</h4>
<?php endif; ?>
<?php else : ?>
<?php $i		= getConfig("NEW_PRODUCT_QTY");  ?>
<?php newProduct($i, $id_customer);  ?>

<?php endif; ?>
        
        
       
	</div>
</div>	
<script>
	$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var id_customer = $("#id_customer").val();
	if(query_text !=""){
	$.ajax({
		url:"controller/orderController.php?text="+query_text+"&id_customer="+id_customer , type: "GET", cache:false,
		success: function(result){
			$("#product_grid").html(result);
		}
	});
	}
});
function getData(id_product){
	var id_cus = $("#id_customer").val();
	$("#id_product").val(id_product);
	$.ajax({
		url:"../invent/controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
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
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
/*	function submit_product(){
		var id_product = $("#id_product").val();
		if(id_product != ""){
			$("#order_form").submit();
		}
	}*/
	
	function submit_product(){
		$.ajax({
			url: "controller/orderController.php?add_to_cart",
			type:"POST",
			data: $("#order_form").serialize(),
			success: function(msg){
				$("#modal_title").html('');
				$("#modal_body").html('');
				$("#order_grid").hide();
				reload_page();
			}
		});
	}
	
	function reload_page(){
		location.reload();
	}

</script>
