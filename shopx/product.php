<!-- styles needed by smoothproducts.js for product zoom  -->
<link rel="stylesheet" href="assets/css/smoothproducts.css">
<!-- styles needed by minimalect -->
<!-- Placed at the end of the document so the pages load faster --> 
<script>
function showminicart(str) {
  if (str=="") {
    document.getElementById("txtHint").innerHTML="";
    return;
  } 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","minicart.php?id_cart="+str,true);
  xmlhttp.send();
}

</script>


<?php 
	$id_product = $_GET['id_product'];
	if(isset($_COOKIE['id_customer'])){
		$id_customer = $_COOKIE['id_customer'];
	}else{
		$id_customer = "";
	}
	if(isset($_COOKIE['id_cart'])){
		$id_cart = $_COOKIE['id_cart'];
	}else{
		$id_cart = "";
	}
	$product->product_detail($id_product,$id_customer);
	$product->showImage($id_product,4);
	$product_price = $product->product_price;
	$product_sell = $product->product_sell;
	
?>
<div class="container main-container headerOffset"> 
   <div class="row">
    <div class="breadcrumbDiv col-lg-12">
      <ul class="breadcrumb">
        <li><a href="index.php">Home</a> </li>
        <?php 
		$id_category = $_GET['id_category'];
		list($category_name2,$parent_id) = dbFetchArray(dbQuery("SELECT category_name,parent_id FROM tbl_category where id_category = '$id_category'"));
		if($parent_id == "0"){
		echo "<li class='active'><a href='index.php?content=category&id_category=$id_category'>$category_name2</a></li><li class='active'>".$product->product_code."</li>";
		}else{
			list($category_name1) = dbFetchArray(dbQuery("SELECT category_name FROM tbl_category where id_category = '$parent_id'"));
			echo "<li class='active'><a href='index.php?content=category&id_category=$parent_id'> $category_name1</a></li><li class='active'><a href='index.php?content=category&id_category=$id_category'>$category_name2</a></li><li class='active'>".$product->product_code."</li>";
		}
			?> 
      </ul>
    </div>
  </div>  <!-- /.row  -->
  
   <div class="row featuredPostContainer globalPadding style2">
  
   <!-- left column -->
    <div class="col-lg-6 col-md-6 col-sm-6">
    	<!-- product Image and Zoom -->

    <div class="main-image sp-wrap col-lg-12 no-padding style2"> 
        <?php echo $product->image_product;?>
      </div>
    </div><!--/ left column end -->
    
    
    <!-- right column -->
    <div class="col-lg-6 col-md-6 col-sm-5">
    
      <h1 class="product-title"><?php echo $product->product_code;?></h1>
      <h3 class="product-code"><?php echo $product->product_name;?></h3>
      <div class="product-price"> 
      <?php //if($product_price > "$product_sell"){
        // echo  "<span class='price-sales'>".number_format($product_sell,2)." ฿</span> 
        //  <span class='price-standard'>$product_price ฿</span>";
	//  }else{
		  echo  "<span class='price-sales'>".number_format($product_price,2)."  ฿</span>";
	//  }
	  ?>
      </div>
 
      
      <div class="clear"></div>
      
      <div class="product-tab w100 clearfix">
      
        <ul class="nav nav-tabs">
          <li class="active"><a href="#details" data-toggle="tab">รายละเอียด</a></li>
         <!-- <li><a href="#size" data-toggle="tab">Size</a></li>
          <li><a href="#shipping" data-toggle="tab">Shipping</a></li>-->
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active" id="details"><?php 
		  $search = array("\n","\s");
		  $replace = array("<br>","&nbsp;");
		  echo str_replace($search,$replace,$product->product_detail);?></div>
          
        </div> <!-- /.tab content -->
        
      </div><!--/.product-tab-->
      <div style="clear:both"></div>     
    </div><!--/ right column end -->
    
  </div>
  <!--/.row-->
  <div class="row featuredPostContainer globalPadding style2">
  <form id="order_form">
  <input type="hidden" name="id_customer" value="<?php echo $id_customer; ?>" />
  <input type="hidden" name="id_cart" value="<?php echo $id_cart; ?>"  />
  <?php echo  $product->attributeGrid($id_product); ?>
  </form>
  	<div class="details-description">
      <div class="cart-actions">
      
        <div class="addto">
          <button onclick="submit_product()"  class="button btn-cart cart first" title="Add to Cart" type="button"><i class="fa fa-shopping-cart"></i>&nbsp; หยิบใส่ตระกร้า</button>   
      </div>
      </div>
      <!--/.cart-actions-->
  </div>
  </div>
  </div>
  </div>


<!-- include smoothproducts // product zoom plugin  --> 
<script type="text/javascript" src="assets/js/smoothproducts.min.js"></script> 
<script>
function submit_product(){
		load_in();
		$.ajax({
			url: "controller/cartController.php?add_to_cart",
			type:"POST",
			data: $("#order_form").serialize(),
			success: function(msg){
				load_out();
				arr = msg.split(" | ");
				var text = arr[2].trim();
				if(text != ""){
					var warning = arr[1]+" : "+arr[2];
					alert(warning);
				}				
				reload_page();
			}
		});
	}
	
	function reload_page(){
		location.reload();
	}
</script>