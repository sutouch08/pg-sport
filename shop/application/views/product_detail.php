
<link href="<?php echo base_url(); ?>shop/assets/css/style.css" rel="stylesheet">

<link href="<?php echo base_url(); ?>shop/assets/css/cart-nav.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>shop/assets/css/product-details-5.css" rel="stylesheet">

    <!-- gall-item Gallery for gallery page -->
<link href="<?php echo base_url(); ?>shop/assets/plugins/magnific/magnific-popup.css" rel="stylesheet">
<?php $this->load->view('module/product_detail'); ?>
<?php $this->load->view('module/product_info'); ?>
<!-- include carousel slider plugin  -->
<script src="<?php echo base_url(); ?>shop/assets/js/owl.carousel.min.js"></script>

<script src="<?php echo base_url(); ?>shop/assets/plugins/magnific/jquery.magnific-popup.min.js"></script>


<!-- 
<script src="<?php echo base_url(); ?>shop/assets/js/bootstrap.touchspin.js"></script>

include touchspin.js // touch friendly input spinner component   -->
<!--
<script src="<?php echo base_url(); ?>shop/assets/js/script.js"></script>

 include custom script for site  -->

<script src="<?php echo base_url(); ?>shop/assets/plugins/rating/bootstrap-rating.min.js"></script>

<script>

    // For Demo purposes only
    [].slice.call(document.querySelectorAll('nav.nav-narrow-svg > a')).forEach(function (el) {
        el.addEventListener('click', function (ev) {
            ev.preventDefault();
        });
    });

    // productShowCase  carousel
    var pic = $(".product-images-carousel");

    pic.owlCarousel({
        autoPlay: false,
        lazyLoad: true,
        navigation: false,
        paginationSpeed: 1000,
        goToFirstSpeed: 2000,
        singleItem: true,
        autoHeight: true


    });

    // Custom Navigation Events
    $(".product-images-carousel-wrapper nav.slider-nav .next").click(function () {
        pic.trigger('owl.next');
    })
    $(".product-images-carousel-wrapper nav.slider-nav  .prev").click(function () {
        pic.trigger('owl.prev');
    })

</script>
<script>
 /*   $(function () {
        $('.rating-tooltip-manual').rating({
            extendSymbol: function () {
                var title;
                $(this).tooltip({
                    container: 'body',
                    placement: 'bottom',
                    trigger: 'manual',
                    title: function () {
                        return title;
                    }
                });
                $(this).on('rating.rateenter', function (e, rate) {
                    title = rate;
                    $(this).tooltip('show');
                })
                        .on('rating.rateleave', function () {
                            $(this).tooltip('hide');
                        });
            }
        });

    });*/
</script>


<script src="<?php echo base_url(); ?>shop/assets/plugins/intense-images-master/intense.js"></script>

<script>
    var elements = document.querySelectorAll('.zoom-image-overly');
    Intense(elements);
</script>


<script type="text/javascript" src="<?php echo base_url(); ?>shop/assets/js/skrollr.min.js"></script>
<script type="text/javascript">
    var isMobile = function () {
        //console.log("Navigator: " + navigator.userAgent);
        return /(iphone|ipod|ipad|android|blackberry|windows ce|palm|symbian)/i.test(navigator.userAgent);
    };

    if (isMobile()) {
        // For  mobile , ipad, tab

    } else {

        if ($(window).width() < 768) {
        } else {
            var s = skrollr.init({forceHeight: false});
        }

    }


</script>


<!-- Reveal Animations When You Scroll  -->
<script src="<?php echo base_url(); ?>shop/assets/js/wow.min.js"></script>
<script>
    new WOW().init();
</script>

