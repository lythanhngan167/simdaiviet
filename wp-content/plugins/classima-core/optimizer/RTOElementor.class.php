<?php 
// Security check
defined('ABSPATH') || die();

class RTOElementor{

    public function __construct(){

        global $rt_optimize;
        
        if( !empty($rt_optimize->options->get_option('rt_elementor_bg_lazy')) && $rt_optimize->options->get_option('rt_elementor_bg_lazy') == true ){

            add_filter('wp_head', [&$this, 'add_style']);
            add_action('wp_enqueue_scripts', [&$this, 'add_script'], PHP_INT_MAX);
            add_action('elementor/frontend/the_content', [&$this, 'add_lazy_class']);
        
        }

    }

    public function add_lazy_class($content){
        return preg_replace(['/(\selementor-section\s)/m', '/(elementor-column-wrap)/m'], ' $1 lazy-background ', $content);
    }

    public function add_script(){

        global $lazy_elementor_background_images_js_added;
        ob_start(); 
        ?>
            jQuery( function ( $ ) {
                if ( ! ( window.Waypoint ) ) {
                    // if Waypoint is not available, then we MUST remove our class from all elements because otherwise BGs will never show
                    $('.elementor-section.lazy-background,.elementor-column-wrap.lazy-background').removeClass('lazy-background');
                    if ( window.console && console.warn ) {
                        console.warn( 'Waypoint library is not loaded so backgrounds lazy loading is turned OFF' );
                    }
                    return;
                } 
                $('.lazy-background').each( function () {
                    var $section = $( this );
                    new Waypoint({
                        element: $section.get( 0 ),
                        handler: function( direction ) {
                            //console.log( [ 'waypoint hit', $section.get( 0 ), $(window).scrollTop(), $section.offset() ] );
                            $section.removeClass('lazy-background');
                        },
                        offset: $(window).height()*1.5 // when item is within 1.5x the viewport size, start loading it
                    });
                } );
            });
        <?php
        $skrip = ob_get_clean();
        
        if (! wp_script_is('jquery', 'enqueued')) {
            wp_enqueue_script('jquery');
        }
                                                
        $lazy_elementor_background_images_js_added = wp_add_inline_script('jquery', $skrip);


    }

    public function add_style(){

        global $lazy_elementor_background_images_js_added;
        if (! ($lazy_elementor_background_images_js_added)) {
            return;
        } // don't add css if scripts weren't added
        ob_start(); 
        ?>
            <style>
                .lazy-background:not(.elementor-motion-effects-element-type-background) {
                    background-image: none !important; /* lazyload fix for elementor */
                }
            </style>
        <?php
        echo ob_get_clean();

    }

}

new RTOElementor();