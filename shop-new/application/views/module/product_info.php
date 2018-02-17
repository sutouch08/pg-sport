<?php if( isset($product_info) && $product_info !== FALSE ) : ?>
<section class="section-product-info-bottom">
    <div class="product-details-bottom-bar">
        <div class="container-1400 container">
            <div class="row">
                <div class="col-lg-12">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs flat list-unstyled list-inline social-inline" role="tablist">
                        <li class="active"><a style="border:0px;">Product details</a></li>
                    </ul>

                </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section-tab-content">

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1">
            <div class="product-story-inner ">

                <div class="container">

                    <div class="row ">

                        <div class="col-lg-12 ">
                            <div class="hw100 display-table">
                                <div class="hw100 display-table-cell">


                                    <div class="product-story-info-box">
                                        <div class="product-story-info-text ">


                                            <!-- <h5 class="subtitle">The Story</h5>

                                            <h3 class="title">Product Features</h3> -->

                                            
											<?php echo $product_info; ?>

                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>


                </div>

            </div>
        </div>

</section>

<!-- product details additional section -->
<?php endif; ?>