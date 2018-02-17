<?php //include SRV_ROOT."library/class/category.php";
		$NEW = new category();
?>
<div class="container main-container headerOffset"> 
   <div class="row">
    <div class="breadcrumbDiv col-lg-12">
      <ul class="breadcrumb">
        <li><a href="index.php">Home</a> </li>
        <?php 
		if(isset($_GET['search'])){
			echo "<li class='active'>ผลการค้นหา</li>";
			$category_name2 = $_POST['search'];
		}else{
			$id_category = $_GET['id_category'];
			list($category_name2,$parent_id) = dbFetchArray(dbQuery("SELECT category_name,parent_id FROM tbl_category where id_category = '$id_category'"));
		if($parent_id == "0"){
			echo "<li class='active'>$category_name2</li>";
		}else{
			list($category_name1) = dbFetchArray(dbQuery("SELECT category_name FROM tbl_category where id_category = '$parent_id'"));
			echo "<li class='active'><a href='index.php?content=category&id_category=$parent_id'> $category_name1</a></li><li class='active'>$category_name2</li>";
		}
		}
			?> 
      </ul>
    </div>
  </div>  <!-- /.row  -->
 <div class="w100 clearfix category-top">
        <h2> <?php echo $category_name2;?></h2>
      </div><!--/.category-top-->
<div class="w100 productFilter clearfix">
<?php 
if(isset($_GET['search'])){
	$search = $_POST['search'];
	$sql = dbQuery("SELECT id_product,product_code,product_name,product_price,date_upd FROM  tbl_product  where (product_code LIKE '%$search%' OR product_name LIKE '%$search%') and active = '1'  ORDER BY tbl_product.date_add DESC");
	//$sql = dbQuery("SELECT id_product,product_code,product_name,product_price,date_upd FROM  product_table where (product_code LIKE '%$search%' OR product_name LIKE '%$search%') and active = '1' ORDER BY date_add DESC");
	$row = dbNumRows($sql);
}else{
	$sql = dbQuery("SELECT tbl_category_product.id_product,product_code,product_name,product_price,date_upd FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product where tbl_category_product.id_category = '$id_category' and active = '1' ORDER BY tbl_product.date_add DESC");
	$row = dbNumRows($sql);
}
		?>
        <p class="pull-left"> มีสินค้า <strong><?php echo $row;?></strong> รายการ </p>
        <div class="pull-right ">
          <div class="change-order pull-right">
           
          </div>
          <div class="change-view pull-right"> 
          <a href="#" title="Grid" class="grid-view"> <i class="fa fa-th-large"></i> </a> 
          <a href="#" title="List" class="list-view "><i class="fa fa-th-list"></i></a> </div>
        </div>
      </div> <!--/.productFilter-->
      <div class='row  categoryProduct xsResponse clearfix'>
	<?php 
	
		$i = 0;
		while($i<$row){
		list($id_product,$product_code,$product_name,$product_price,$date_upd) = dbFetchArray($sql);
	
 echo "
        <div class='item col-lg-3 col-md-3 col-sm-4 col-xs-6'>
              <div class='product'>
                <div class='image'> <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'>".getCoverImage($id_product,4)."</a>
                  <div class='promotion'> ";
					$NEW->category_show_new($company->product_new,$id_product);
					echo "".$NEW->NEW."
				";
				
				$product->product_detail($id_product,$id_customer);
				/*if($product->product_discount != "0.00"){ echo "<span class='discount'>".$product->product_discount."OFF</span>";}*/echo "
					</div>
                </div>
                <div class='description'>
                  <h4><a href='index.php?content=product&id_category=$id_category&id_product=$id_product'>$product_code : $product_name</a></h4>
                  <p>".substr_replace($product->product_detail,'....',200)."</p>
				  <br>
				  <div class='price'><span>&nbsp;</span>";
				  /* if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}*/ echo"
                  </div></div>
                  <div class='action-control'>
				  
				  <a href='index.php?content=product&id_category=$id_category&id_product=$id_product'>
					
                     <span class='btn btn-primary' style='width:50%;'>".number_format($product->product_price,2)." ฿</span> 

					  </a>
				</div>
              </div>
            </div>";
        
			$i++;
		}
		?>
      
    </div>
    </div> <!--/.categoryProduct || product content end-->
<!-- Le javascript
================================================== --> 

<!-- Placed at the end of the document so the pages load faster --> 
<!--<script type="text/javascript" src="assets/js/jquery/1.8.3/jquery.js"></script> 
<script src="assets/bootstrap/js/bootstrap.min.js"></script> 

<!-- include custom script for site  --> 
<!--<script src="assets/js/script.js"></script>