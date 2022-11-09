(function($){
    "use strict";

    $(document).on('rtcl.mark_as_sold', function (e, res) {
        if (res.success) {
            var data = res.data;
            var $target = $(res.target);
            var $targetListing = $target.closest('.listing-list-each');

            if (data.type == 'sold') {
                $targetListing.addClass('is-sold');
                $targetListing.find('.rtin-thumb').append('<span class="rtcl-sold-out">'+ClassimaObj.sold_out_text+'</span>');
            } else {
                $targetListing.removeClass('is-sold');
                $targetListing.find('.rtcl-sold-out').remove();
            }
        }
    });

    jQuery(document).ready(function($){
        /* Scroll to top */
        $('.scrollToTop').on('click',function(){
            $('html, body').animate({scrollTop : 0},800);
            return false;
        });
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollToTop').fadeIn();
            }
            else {
                $('.scrollToTop').fadeOut();
            }
        });

        /* Bootstrap Tooltip */
        $('body').tooltip({
            selector: '[data-toggle=tooltip]'
        });

        /*  Mobile Menu */

        var a = $('.offscreen-navigation .menu');
        if (a.length) {
            a.children("li").addClass("menu-item-parent");
            a.find(".menu-item-has-children > a").on("click", function (e) {
                e.preventDefault();
                $(this).toggleClass("opened");
                var n = $(this).next(".sub-menu"),
                    s = $(this).closest(".menu-item-parent").find(".sub-menu");
                a.find(".sub-menu").not(s).slideUp(250).prev('a').removeClass('opened'), n.slideToggle(250)
            });
            a.find('.menu-item:not(.menu-item-has-children) > a').on('click', function (e) {
                $('.rt-slide-nav').slideUp();
                $('body').removeClass('slidemenuon');
            });
        }

        $('.sidebarBtn').on('click', function (e) {
            e.preventDefault();
            if ($('.rt-slide-nav').is(":visible")) {
                $('.rt-slide-nav').slideUp();
                $('body').removeClass('slidemenuon');
            } else {
                $('.rt-slide-nav').slideDown();
                $('body').addClass('slidemenuon');
            }

        });

        $('#myaccount-collapse-menu').on('click', function () {
            $('.rtcl-MyAccount-navigation').slideToggle();
        });

        /* Mega Menu */
        $('.site-header .main-navigation ul > li.mega-menu').each(function() {
            // total num of columns
            var items = $(this).find(' > ul.sub-menu > li').length;
            // screen width
            var bodyWidth = $('body').outerWidth();
            // main menu link width
            var parentLinkWidth = $(this).find(' > a').outerWidth();
            // main menu position from left
            var parentLinkpos = $(this).find(' > a').offset().left;

            var width = items * 240;
            var left  = (width/2) - (parentLinkWidth/2);

            var linkleftWidth  = parentLinkpos + (parentLinkWidth/2);
            var linkRightWidth = bodyWidth - ( parentLinkpos + parentLinkWidth );

            // exceeds left screen
            if( (width/2)>linkleftWidth ){
                $(this).find(' > ul.sub-menu').css({
                    width: width + 'px',
                    right: 'inherit',
                    left:  '-' + parentLinkpos + 'px'
                });        
            }
            // exceeds right screen
            else if ( (width/2)>linkRightWidth ) {
                $(this).find(' > ul.sub-menu').css({
                    width: width + 'px',
                    left: 'inherit',
                    right:  '-' + linkRightWidth + 'px'
                }); 
            }
            else {
                $(this).find(' > ul.sub-menu').css({
                    width: width + 'px',
                    left:  '-' + left + 'px'
                });            
            }
        });

        /* Sticky Menu */

        if ( ClassimaObj.hasStickyMenu == 1 ) {
            run_sticky_menu();
        }

        // Scripts needs loading inside content area
        rdtheme_content_ready_scripts();

    });

    // Window Load
    $(window).on('load', function () {

        // Scripts needs loading inside content area
        rdtheme_content_load_scripts();

        // Preloader
        $('#preloader').fadeOut('slow', function () {
            $(this).remove();
        });
    });

    // Elementor Frontend Load
    $( window ).on( 'elementor/frontend/init', function() {
        if (elementorFrontend.isEditMode()) {
            elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function(){
                rdtheme_content_ready_scripts()
                rdtheme_content_load_scripts();
            } );
        }
    } );

    function rdtheme_content_ready_scripts(){

        /*---------------------------------------
          Background Parallax
          --------------------------------------- */
        if ($(".rt-parallax-bg-yes").length) {
            $(".rt-parallax-bg-yes").each(function () {
                var speed = $(this).data('speed');
                $(this).parallaxie({
                    speed: speed ? speed : 0.5,
                    offset: 0,
                });
            })
        }

        // Isotope
        if ( typeof $.fn.isotope == 'function' && typeof $.fn.imagesLoaded == 'function') {

            // Blog Layout 2
            var $blogIsotopeContainer = $('.post-isotope');
            $blogIsotopeContainer.imagesLoaded( function() {
                $blogIsotopeContainer.isotope();
            });

            // Run 1st time
            var $isotopeContainer = $('.rt-el-isotope-container');
            $isotopeContainer.imagesLoaded( function() {
                $isotopeContainer.each(function() {
                    var $container = $(this).find('.rt-el-isotope-wrapper'),
                    filter = $(this).find('.rt-el-isotope-tab a.current').data('filter');
                    runIsotope($container,filter);
                });
            });

            // Run on click even
            $('.rt-el-isotope-tab a').on('click',function(){
                $(this).closest('.rt-el-isotope-tab').find('.current').removeClass('current');
                $(this).addClass('current');
                var $container = $(this).closest('.rt-el-isotope-container').find('.rt-el-isotope-wrapper'),
                filter = $(this).attr('data-filter');
                runIsotope($container,filter);
                return false;
            });
        }

        /* Counter */
        if ( typeof $.fn.counterUp == 'function') {
            $('.rt-el-counter .rt-counter-num').counterUp();
        }

        /* Zoom */
        if (typeof $.fn.zoom == 'function') {
            if (typeof rtcl_single_listing_params != 'undefined') {
                if ( rtcl_single_listing_params.zoom_enabled ) {
                    $('.classima-single-details .rtin-slider-box .rtcl-slider-item').zoom();
                }
            }
        }

        /* Listing Search Dropdown */
        $('.classima-listing-search-dropdown .dropdown-item').on('click',function(){
            var $parent = $(this).closest('.classima-listing-search-dropdown'),
            type = $(this).data('adtype'),
            text = $(this).text();

            $parent.find('input').val(type);
            $parent.find('button').text(text);
            $parent.dropdown('hide');

            return false;
        });  

        /* Listing - Reveal Phone */
        $('.classima-phone-reveal').on('click',function(){
            if ( $(this).hasClass('not-revealed') ) {
                $(this).removeClass('not-revealed').addClass('revealed');
                var phone = $(this).data('phone');
                $(this).find('span').text(phone);
            }

            return false;
        });

        /* Listing - Toggle Filter */
        $('#classima-toggle-sidebar').on('click',function(){

            var $main = $('.sidebar-listing-archive');
            var display = $main.css('display');

            if ( display == 'block' ) {
                $main.hide();
            }
            if ( display == 'none' ) {
                $main.show();
            }

            return false;
        });

        $(".headerCategoriesMenu").on({
            mouseenter: function () {
                $('.headerCategoriesMenu__dropdown').css({
                    'opacity': '1',
                    'visibility': 'visible',
                    'transform': 'translateY(0px)',
                    '-webkit-transform': 'translateY(0px)'
                });
            },
            mouseleave: function () {
                $('.headerCategoriesMenu__dropdown').css({
                    'opacity': '0',
                    'visibility': 'hidden',
                    'transform': 'translateY(20px)',
                    '-webkit-transform': 'translateY(20px)'
                });
            }
        });

        $("#el_load_more").on("click", function(e) {
            e.preventDefault();

            var $this = $(this),
                page = $this.parent('.load-more-wrapper').data('page'),
                display_options = $this.parent('.load-more-wrapper').data('options'),
                query = $this.parent('.load-more-wrapper').data('query'),
                layout = $this.parent('.load-more-wrapper').data('layout'),
                col_class = $this.parent('.load-more-wrapper').data('col-class'),
                total_pages = $this.parent('.load-more-wrapper').data('total-pages'),
                post_per_page = $this.parent('.load-more-wrapper').data('posts-per-page');

            $.ajax({
                // use the ajax object url
                type : "POST",
                url: ClassimaObj.ajax_url,
                data: {
                    action: "listing_load_more_ad", // add your action to the data object
                    offset: page * post_per_page, //  page # x your default posts per page
                    layout: layout,
                    col_class: col_class,
                    post_per_page: post_per_page,
                    display: display_options,
                    queryArg: query
                },
                success: function(data) {
                    // add the posts to the container and add to your page count
                    page++;
                    $this.parent('.load-more-wrapper').data('page', page);
                    $('.rt-el-listing-grid .row').append(data);
                    if(page > total_pages) {
                        $this.parent('.load-more-wrapper').hide();
                    }
                },
                error: function(data) {
                    // test to see what you get back on error
                    console.log(data);
                }
            });

        });

        /* Listing Mapview - Sidebar top */
        if ( $('body').hasClass('mean-activated') ) {
            var height = $('#meanmenu').outerHeight();
        }
        else {
            var height = $('#site-header').outerHeight();
        }
        
        if ( ClassimaObj.hasAdminBar == 1 ) {
            height += $('#wpadminbar').outerHeight();
        }

        $('.listing-mapview-sidebar').css('top', height+'px');

        /* Listing Mapview - Sticky Map */
        if (typeof StickySidebar == 'function') {
            var topSpacing= 0;

            if ( ClassimaObj.hasStickyMenu == 1 ) {
                topSpacing += $('#site-header').outerHeight();
            }

            if ( ClassimaObj.hasAdminBar == 1 ) {
                topSpacing += $('#wpadminbar').outerHeight();
            }
            if($('.listing-mapview-map').length) {
                var sidebar = new StickySidebar('.listing-mapview-map', {
                    topSpacing: topSpacing,
                    minWidth: 1200,
                    containerSelector: '.listing-mapview-content-wrap',
                    innerWrapperSelector: '.rtcl-map-view'
                });
            }
        }
    }

    function rdtheme_content_load_scripts(){

        /* Animated text */
        if (typeof Typed == 'function' && $(window).width() > 1199.98) {
            $('.title-typejs').each(function(index, el) {
                var options = $(this).data('options');
                new Typed(this, options);
            });
        }
        else {
            $('.title-typejs').each(function(index, el) {
                var options = $(this).data('options');
                $(this).text(options.strings);
            });
        }
    }

    function run_sticky_menu() {

        var wrapperHtml  = $('<div class="main-header-sticky-wrapper"></div>');
        var wrapperClass = '.main-header-sticky-wrapper';
        
        $('.main-header').clone(true).appendTo(wrapperHtml);
        $('body').append(wrapperHtml);

        var height = $(wrapperClass).outerHeight() + 30;

        $(wrapperClass).css('margin-top', '-' + height + 'px');

        $(window).scroll(function(){
            if ($(this).scrollTop() > 300) {
                $('body').addClass('rdthemeSticky');
            }
            else {
                $('body').removeClass('rdthemeSticky');
            }
        });
    }

    function runIsotope($container,filter){
        $container.isotope({
            filter: filter,
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
        });
    }

})(jQuery);


/* Generate class based on container width */
(function ($) {
    "use strict";

    $(window).on('load resize', elementWidth);

    function elementWidth(){
        $('.elementwidth').each(function() {
            var $container = $(this),
            width = $container.outerWidth(),
            classes = $container.attr("class").split(' '); // get all class

            var classes1 = startWith(classes,'elwidth'); // class starting with "elwidth"
            classes1 = classes1[0].split('-'); // "elwidth" classnames into array
            classes1.splice(0, 1); // remove 1st element "elwidth"

            var classes2 = startWith(classes,'elmaxwidth'); // class starting with "elmaxwidth"
            classes2.forEach(function(el){
                $container.removeClass(el);
            });

            classes1.forEach(function(el){
                var maxWidth = parseInt(el);

                if (width <= maxWidth) {
                    $container.addClass('elmaxwidth-'+maxWidth);
                }
            });
        });
    }

    function startWith(item, stringName){
        return $.grep(item, function(elem) {
            return elem.indexOf(stringName) == 0;
        });
    }

}(jQuery));