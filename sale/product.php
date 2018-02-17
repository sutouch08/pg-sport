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


<?php $id_product = $_GET['id_product'];
	if(isset($_COOKIE['id_customer'])){
		$id_customer = $_COOKIE['id_customer'];
	}else{
		$id_customer = "";
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
      <?php if($product_price > "$product_sell"){
         echo  "<span class='price-sales'>".number_format($product_sell,2)." ฿</span> 
          <span class='price-standard'>$product_price ฿</span>";
	  }else{
		  echo  "<span class='price-sales'>".number_format($product_price,2)."  ฿</span>";
	  }
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
          <!--<div class="tab-pane" id="size"> 16" waist<br>
            34" inseam<br>
            10.5" front rise<br>
            8.5" knee<br>
            7.5" leg opening<br>
            <br>
            Measurements taken from size 30<br>
            Model wears size 31. Model is 6'2 <br>
            <br>
          </div>
          
          <div class="tab-pane" id="shipping">
            <table >
              <colgroup>
              <col style="width:33%">
              <col style="width:33%">
              <col style="width:33%">
              </colgroup>
              <tbody>
                <tr>
                  <td>Standard</td>
                  <td>1-5 business days</td>
                  <td>$7.95</td>
                </tr>
                <tr>
                  <td>Two Day</td>
                  <td>2 business days</td>
                  <td>$15</td>
                </tr>
                <tr>
                  <td>Next Day</td>
                  <td>1 business day</td>
                  <td>$30</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3">* Free on orders of $50 or more</td>
                </tr>
              </tfoot>
            </table>
          </div>-->
          
        </div> <!-- /.tab content -->
        
      </div><!--/.product-tab-->
           
      
      <div style="clear:both"></div>
      
     <!--/ <div class="product-share clearfix">
        <p> SHARE </p>
        <div class="socialIcon"> 
        	<a href="#"> <i  class="fa fa-facebook"></i></a> 
            <a href="#"> <i  class="fa fa-twitter"></i></a> 
            <a href="#"> <i  class="fa fa-google-plus"></i></a> 
            <a href="#"> <i  class="fa fa-pinterest"></i></a> </div>
      </div>
      .product-share--> 
      
    </div><!--/ right column end -->
    
  </div>
  <!--/.row-->
  <div class="row featuredPostContainer globalPadding style2">
  	<div class="details-description">
        <p><?php $product->attributeGrid($id_product);?></p>
      </div>

      <div class="cart-actions">
        <div class="addto">
          <button onclick="checknewcart(<?php echo $id_product?>)"  class="button btn-cart cart first" title="Add to Cart" type="button"><i class="fa fa-shopping-cart"></i>&nbsp; หยิบใส่ตระกร้า</button>   
      </div>
      <!--/.cart-actions-->
  </div>
  </div>
  </div>
  <?php //data-toggle="modal" data-target="#myModal"
  echo "<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					  <div class='modal-dialog'>
						<div class='modal-content'>
						  <div class='modal-body'>
							<center>กำลังหยิบสินค้าใส่ตระกร้า.............</center>
						  </div>
						  <div class='modal-footer' >
						  </div>
						</div>
					  </div>
					</div>";?>

<!-- include smoothproducts // product zoom plugin  --> 
<script type="text/javascript" src="assets/js/smoothproducts.min.js"></script> 