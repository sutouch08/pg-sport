<!-- Fixed navbar start -->
  <div class="navbar navbar-tshop navbar-fixed-top megamenu" role="navigation">
  <?php 
if(isset($_COOKIE['id_customer'])){ $id_customer = $_COOKIE['id_customer']; }else{$id_customer = 0;}
if(isset($_COOKIE['id_request_order'])){ $id_request_order = $_COOKIE['id_request_order']; }else{ $id_request_order="";}
  include "../minicart.php"; ?>
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> 
      <span class="sr-only"> Toggle navigation </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> </button>
      <div id='total_for_mobile'>
      <?php $cart_mini->request_total_for_mobile($id_request_order);?>
      </div>
     <!-- <a class="navbar-brand " href="index.php"> <img src="images/logo.png" alt="<?php //echo COMPANY; ?>"> </a> -->
       <!-- this part for mobile -->  
    </div>
    <div id='cart_mini_for_mobile'>
	<?php  $cart_mini->request_cartmini_for_mobile($id_request_order,$id_customer);?>
	</div>
  <!--------------------- แสดงเมนู ------------------->    
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
      <?php /*
      $sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}*/
				?>
      <li> <a href="../index.php?content=order"> Home </a> </li>
       <!-- <li class="dropdown megamenu-fullwidth"> <a data-toggle="dropdown" class="dropdown-toggle" href="#"> Products <b class="caret"> </b> </a>
          <ul class="dropdown-menu">
            <li class="megamenu-content "> 
				<?php /*
				$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND id_category !=0 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				echo" <ul class='col-lg-3  col-sm-3 col-md-3 unstyled noMarginLeft newCollectionUl'>
					<li class=''><a href='#cat-$id_category' role='tab' data-toggle='tab' style='display:block;'>".strtoupper($category_name)."</a></li>";
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo"<li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab' style='display:block;'>".strtoupper($sub_category_name)."</a></li>";
				$n++;
				}
				echo "</ul>";
				$i++;
				} */
				?>
            </li>
          </ul>
        </li> -->
      </ul> 
     
  <!--------------------- ตะกร้าสินค้า ------------------->  
     <div id="txtHint"><?php  $cart_mini->request_cartmini($id_request_order);?></div>
  <!--------------------- จบตะกร้าสินค้า ------------------->        
	
     </div>
      <!--/.navbar-av hidden-xs--> 
      
    </div>
    <!--/.nav-collapse --> 
  
  </div>
  <!--/.container -->
 
</div>
<!-- /.Fixed navbar  -->
