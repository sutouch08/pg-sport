
    <?php
	$pop_on = "front";
	$sql = dbQuery("SELECT delay, start, end, content, width, height FROM tbl_popup WHERE pop_on = '$pop_on' AND active =1");
	$row = dbNumRows($sql);
	if($row>0){
		list($delay, $start, $end, $content, $width, $height ) = dbFetchArray($sql);
		$popup_content ="<div class='row' ><div class='col-lg-12'>$content</div></div>";
		include "../library/popup.php";
		$today = date('Y-m-d H:i:s');
		if(isset($_COOKIE['pop_front'])&&$_COOKIE['pop_front'] !=$delay){ setcookie('pop_front','',time()-3600); }
		if($start<=$today &&$end>=$today){  
			if(!isset($_COOKIE['pop_front'])){
				setcookie("pop_front", $delay, time()+$delay);
				echo" <script> $(document).ready(function(e) {  $('#modal_popup').modal('show'); }); </script>";
			}
		}
	}
		
?>
<!-- include custom script for only homepage  --> 
<div class="container main-container"> 
  
  <!-- Main component call to action -->
  
  <div class="row featuredPostContainer globalPadding style2">
    <h3 class="section-title style2 text-center" style="margin-top:50px;"><span>สินค้ามาใหม่</span></h3>
    <div id="productslider" class="owl-carousel owl-theme">
    <?php newArrival(getConfig("NEW_PRODUCT_QTY"),$id_customer); ?>
    </div>
    <!--/.productslider--> 
    
  </div>
  <!--/.featuredPostContainer--> 
</div>
<!-- /main container -->

<!--/.parallax-image-1-->

<div class="container main-container"> 
  
  <!-- Main component call to action -->
  
  <div class="morePost row featuredPostContainer style2 globalPaddingTop " >
    <h3 class="section-title style2 text-center"><span>สินคาแนะนำ</span></h3>
    <div class="container">
      <?php
	  if(isset($id_customer)){ $id_cus = $id_customer; }else{ $id_cus = 0; }
	   featureProduct(getConfig("FEATURES_PRODUCT"),$id_cus); 
	   ?>
      
  <!--    <div class="row">
      	<div class="load-more-block text-center">
               <a class="btn btn-thin" href="#">
               <i class="fa fa-plus-sign">+</i>  load more products</a>
         </div>
      </div>-->
      
    </div>
    <!--/.container--> 
  </div>
  <!--/.featuredPostContainer-->
  

  
</div>
<!--main-container-->

<div class="parallax-section parallax-image-2">
  
</div>
<!--/.parallax-section-->
<!-- <script src="assets/js/home.js"></script> -->