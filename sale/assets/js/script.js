/*	Table OF Contents
	==========================
	Carousel
	Customs Script
	Custom Parallax 
	Custom Scrollbar
	Custom animated.css effect
	Equal height ( subcategory thumb)
	responsive fix
	*/
	
	

$(document).ready(function () {
	
    /*==================================
	Carousel
	====================================*/


    // NEW ARRIVALS Carousel
    $("#productslider").owlCarousel({
        navigation: true,
        items: 4,
        itemsTablet: [768, 2]
    });


    // BRAND  carousel
    var owl = $(".brand-carousel");

    owl.owlCarousel({
        //navigation : true, // Show next and prev buttons
        navigation: false,
        pagination: false,
        items: 8,
        itemsTablet: [768, 4],
        itemsMobile: [400, 2]


    });

    // Custom Navigation Events
    $("#nextBrand").click(function () {
        owl.trigger('owl.next');
    })
    $("#prevBrand").click(function () {
        owl.trigger('owl.prev');
    })


    // YOU MAY ALSO LIKE  carousel

    $("#SimilarProductSlider").owlCarousel({
        navigation: true

    });


    /*==================================
	Customs Script
	====================================*/
    // collapse according add  active class
    $('.collapseWill').on('click', function (e) {
        $(this).toggleClass("pressed"); //you can list several class names 
        e.preventDefault();
    });

    $('.search-box .btn').on('click', function (e) {
        $('.search-full').addClass("active"); //you can list several class names 
        e.preventDefault();
    });
    $('.search-close').on('click', function (e) {
        $('.search-full').removeClass("active"); //you can list several class names 
        e.preventDefault();
    });



    // Customs tree menu script	
    $(".dropdown-tree-a").click(function () { //use a class, since your ID gets mangled
        $(this).parent('.dropdown-tree').toggleClass("open-tree active"); //add the class to the clicked element
    });

    $('.dropdown-treex').bind('click', function () {
        $(this).find('a:first-child').css('color', 'red');
    });

    // List view and Grid view 

    $('.list-view').click(function (e) { //use a class, since your ID gets mangled
        e.preventDefault();
        $('.item').addClass("list-view"); //add the class to the clicked element
    });

    $('.grid-view').click(function (e) { //use a class, since your ID gets mangled
        e.preventDefault();
        $('.item').removeClass("list-view"); //add the class to the clicked element
    });


    // product details color switch 
    $(".swatches li").click(function () {
        $(".swatches li.selected").removeClass("selected");
        $(this).addClass('selected');

    });
	
	
	if( /IEMobile/i.test(navigator.userAgent) ) {
 	// Detect windows phone//..
	 $('.navbar-brand').addClass('windowsphone');
	}



    // top navbar IE & Mobile Device fix
    var isMobile = function () {
        //console.log("Navigator: " + navigator.userAgent);
        return /(iphone|ipod|ipad|android|blackberry|windows ce|palm|symbian)/i.test(navigator.userAgent);
    };
	

    if (isMobile()) {
        // For  mobile , ipad, tab

    } else {
        // For All Desktop

        if ($.browser.msie && parseInt($.browser.version, 10) === 8) {
            // alert('IE8'); 
        } else {
            // alert('Non IE8');
			
			// track of last scroll
                $(function () {

                    //Keep track of last scroll
                    var tshopScroll = 0;
                    $(window).scroll(function (event) {
                        //Sets the current scroll position
                        var ts = $(this).scrollTop();
                        //Determines up-or-down scrolling
                        if (ts > tshopScroll) {
                            // downward-scrolling
                            $('.navbar').addClass('stuck');
                            //$('.parallaxOffset').addClass('down');
                            //$('.banner').addClass('down');

                        } else {
                            // upward-scrolling
                            $('.navbar').removeClass('stuck');
                           // $('.parallaxOffset').removeClass('down');
                           // $('.banner').removeClass('down');
                        }
                        //Updates scroll position
                        tshopScroll = ts;
                    });
                });


        }

    } // end Desktop else


    /*==================================
	Parallax  
	====================================*/
	if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
		// Detect ios User // 
		$('.parallax-section').addClass('isios');
		$('.navbar-header').addClass('isios');
	}
	
	if (/Android|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		// Detect Android User // 
		 $('.parallax-section').addClass('isandroid');
    }
	
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		// Detect Mobile User // No parallax
		 $('.parallax-section').addClass('ismobile');
		 $('.parallaximg').addClass('ismobile');

    } else {
		// All Desktop 
        $(window).bind('scroll', function (e) {
            parallaxScroll();
        });

        function parallaxScroll() {
            var scrolledY = $(window).scrollTop();
            $('.parallaximg').css('marginTop', '' + ((scrolledY * 0.3)) + 'px');
        }


        if ($(window).width() < 600) {} else {
            $('.parallax-image-aboutus').parallax("50%", 0, 0.2, true);
        }
    }



    /*==================================
	 Custom Scrollbar for Dropdown Cart 
	====================================*/
    $(".scroll-pane").mCustomScrollbar({
        advanced: {
            updateOnContentResize: true

        },

        scrollButtons: {
            enable: false
        },

        mouseWheelPixels: "200",
        theme: "dark-2"

    });


    $(".smoothscroll").mCustomScrollbar({
        advanced: {
            updateOnContentResize: true

        },

        scrollButtons: {
            enable: false
        },

        mouseWheelPixels: "100",
        theme: "dark-2"

    });


    /*==================================
	Custom  animated.css effect
	====================================*/


    window.onload = (function () {
        $(window).scroll(function () {
            if ($(window).scrollTop() > 86) {
                // Display something
                $('.parallax-image-aboutus .animated').removeClass('fadeInDown');
                $('.parallax-image-aboutus .animated').addClass('fadeOutUp');
            } else {
                // Display something
                $('.parallax-image-aboutus .animated').addClass('fadeInDown');
                $('.parallax-image-aboutus .animated').removeClass('fadeOutUp');


            }

            if ($(window).scrollTop() > 250) {
                // Display something
            } else {
                // Display something

            }

        })
    })


    /*=======================================================================================
	Code for equal height - // your div will never broken if text get overflow  
	========================================================================================*/

    $(function () {
        $('.thumbnail.equalheight').responsiveEqualHeightGrid(); // add class with selector class equalheight
    });



    /*=======================================================================================
	 Code for tablet device || responsive check
	========================================================================================*/


    if ($(window).width() < 989) {


        $('.collapseWill').trigger('click');

    } else {
        // ('More than 960');
    }


    $(".tbtn").click(function () {
        $(".themeControll").toggleClass("active");
    });




    /*==================================
	Global Plugin
	====================================*/

    // For stylish input check boX 
/*
    $(function () {
        $("input[type='radio'], input[type='checkbox']").ionCheckRadio();

    });*/

    // bootstrap tooltip 
    $('.tooltipHere').tooltip();

    // customs select by minimalect
    $("select").minimalect();

    // cart quantity changer sniper
    $("input[name='quanitySniper']").TouchSpin({
        buttondown_class: "btn btn-link",
        buttonup_class: "btn btn-link"
    });


    /*=======================================================================================
		end  
	========================================================================================*/


}); // end Ready