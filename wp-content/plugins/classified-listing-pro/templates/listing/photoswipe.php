<?php
/**
 * Photoswipe markup
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/listing/photoswipe.php.
 *
 * @author  RadiusTheme
 * @package ClassifiedListing/Templates
 * @version 1.2.23
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" aria-label="<?php esc_attr_e( 'Close (Esc)', 'classified-listing-pro' ); ?>"></button>
                <button class="pswp__button pswp__button--share" aria-label="<?php esc_attr_e( 'Share', 'classified-listing-pro' ); ?>"></button>
                <button class="pswp__button pswp__button--fs" aria-label="<?php esc_attr_e( 'Toggle fullscreen', 'classified-listing-pro' ); ?>"></button>
                <button class="pswp__button pswp__button--zoom" aria-label="<?php esc_attr_e( 'Zoom in/out', 'classified-listing-pro' ); ?>"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" aria-label="<?php esc_attr_e( 'Previous (arrow left)', 'classified-listing-pro' ); ?>"></button>
            <button class="pswp__button pswp__button--arrow--right" aria-label="<?php esc_attr_e( 'Next (arrow right)', 'classified-listing-pro' ); ?>"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>
