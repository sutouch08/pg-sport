  <!--- this part will be hidden for mobile version -->
            <div class="nav navbar-nav navbar-right hidden-xs">
                <div class="dropdown  cartMenu ">
                	<a href="<?php echo base_url(); ?>shop/main/cart/<?php echo $this->id_cart; ?>" class="dropdown-toggle" > 
                    <i class="fa fa-shopping-basket fa-lg"> </i>
                    <?php if( $this->cart_qty > 0 ) : ?>
                    <span id="cartLabel" class="label labelRounded label-danger" style="position: relative; margin-left:-10px; top:-10px;"><?php echo $this->cart_qty; ?></span>
                    <?php else : ?>
                    <span id="cartLabel" class="label labelRounded label-danger" style="position: relative; margin-left:-10px; top:-10px; visibility:hidden;"><?php echo $this->cart_qty; ?></span>
                    <?php endif; ?>
                    </a>
                </div>
                <!--/.cartMenu-->

                <div class="search-box">
                    <div class="input-group">
                        <button class="btn btn-nobg getFullSearch" type="button"><i class="fa fa-search"> </i></button>
                    </div>
                    <!-- /input-group -->

                </div>
                <!--/.search-box -->
            </div>
            <!--/.navbar-nav hidden-xs-->