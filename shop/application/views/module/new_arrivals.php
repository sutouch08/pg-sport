<?php if( $new_arrivals !== false ) : ?>

<div class="container main-container head-offset">
    <!-- Main component call to action -->

    <div class="row featuredPostContainer globalPadding style2">
        <h3 class="section-title style2 text-center"><span>NEW ARRIVALS</span></h3>

        <div id="productslider" class="owl-carousel owl-theme">
        <!-- Items -->
        <?php foreach( $new_arrivals as $item ) : ?>
        <?php 	$link = 'main/productDetail/'.$item->id_product; ?>
            <div class="item">
                <div class="product">
                   <!--  Wishlist  <a class="add-fav tooltipHere" data-toggle="tooltip" data-original-title="Add to Wishlist" data-placement="left"><i class="glyphicon glyphicon-heart"></i></a>  -->

                    <div class="image">
                        <div class="quickview">
                            <a data-toggle="modal" class="btn btn-xs btn-quickview" href="javascript: void(0)"
                               data-target="#productSetailsModalAjax">Quick View </a>
                        </div>
                        <a href="<?php echo $link; ?>">
                        	<img src="<?php echo get_image_path(get_id_cover_image($item->id_product), 3); ?>" alt="img" class="img-responsive">
                        </a>
                        <div class="promotion">
                        	<span class="new-product"> NEW</span>
							<?php if( $item->discount != 0 ) : ?>
                             <span class="discount"><?php echo discount_label($item->discount, $item->discount_type); ?> OFF </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="description">
                        <h4><a href="<?php echo $link; ?>"><?php echo $item->product_code; ?></a></h4>

                        <p><?php echo $item->product_name; ?></p>
                      <!--   <span class="size">XL / XXL / S </span>  -->
                    </div>
                    <div class="price"><span><?php echo $item->product_price; ?> <?php echo getCurrency(); ?></span></div>
                    <div class="action-control"><a class="btn btn-primary"> <span class="add2cart"><i
                            class="glyphicon glyphicon-shopping-cart"> </i> Add to cart </span> </a></div>
                </div>
            </div>
          <?php endforeach; ?> 
            <!-- End items -->
        </div>
        <!--/.productslider-->

    </div>
    <!--/.featuredPostContainer-->
</div>    
<?php endif; ?>
