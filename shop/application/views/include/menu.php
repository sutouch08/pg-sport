<ul class="nav navbar-nav">
  <!--li class=""><a href="<?php echo base_url();?>shop/main"> Home </a></li -->
  <li class="dropdown megamenu-fullwidth">
    <a data-toggle="dropdown" class="dropdown-toggle" href="#"> Brand <b class="caret pull-right"> </b> </a>

    <?php $pdBrand = $this->menu_model->getProductGroup(); ?>
    <?php if(!empty($pdBrand)) : ?>

    <ul class="dropdown-menu">
      <li class="megamenu-content ">
        <ul class="col-lg-3  col-sm-3 col-md-3 unstyled noMarginLeft newCollectionUl">
          <?php foreach($pdBrand as $brand) : ?>

          <li><a href="<?php echo base_url().'view_product_by_brand/'.$brand->id; ?>"> <?php echo $brand->name; ?> </a></li>

          <?php endforeach; ?>

        </ul>
      </li>
    </ul>
  <?php endif; ?>
  </li>


  <!-- change width of megamenu = use class > megamenu-fullwidth, megamenu-60width, megamenu-40width -->
  <li class="dropdown megamenu-80width ">
    <a data-toggle="dropdown" class="dropdown-toggle" href="#">หมวดหมู่ <b class="caret"> </b></a>
    <?php  //-------- Menu by first level category  ?>
    <?php  $category = $this->menu_model->getCategory(1, 0); //--- level = 1, parent_id = 0 ?>
    <?php  if(!empty($category)) : ?>
        <ul class="dropdown-menu">
    <?php   foreach($category as $cate) : ?>
          <li><a href="<?php echo base_url().'view_product_by_category/'.$cate->id_category; ?>"> <?php echo $cate->category_name; ?> </a></li>
    <?php   endforeach; ?>
      </ul>
    <?php endif; ?>
  </li>


</ul>
