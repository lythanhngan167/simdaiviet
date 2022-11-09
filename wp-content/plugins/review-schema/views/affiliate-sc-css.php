<?php

/**
 * @var integer $scID
 * @var bool    $old
 * @var array   $metaData 
 */
use Rtrs\Controllers\Admin\Meta\AddMetaBox;
 
$filter = new AddMetaBox();

$sc_id = $scID;
$sc_meta = []; 
$metaData = get_post_meta($sc_id);

$sc_meta['layout'] = isset( $metaData['layout'][0] ) ? $filter->sanitize_field('text', $metaData['layout'][0] ) : null;
$sc_meta['width'] = isset( $metaData['width'][0] ) ? $filter->sanitize_field( 'text', $metaData['width'][0] ) : null; 
$sc_meta['product_title'] = isset( $metaData['product_title'][0] ) ? $filter->sanitize_field('style', unserialize($metaData['product_title'][0]) ) : null;  
$sc_meta['product_desc'] = isset( $metaData['product_desc'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['product_desc'][0]) ) : null; 
$sc_meta['style_regular_price'] = isset( $metaData['style_regular_price'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['style_regular_price'][0]) ) : null; 
$sc_meta['style_offer_price'] = isset( $metaData['style_offer_price'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['style_offer_price'][0]) ) : null; 

$sc_meta['border_color'] = isset( $metaData['border_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['border_color'][0] ) : null; 
$sc_meta['border_size'] = isset( $metaData['border_size'][0] ) ? $filter->sanitize_field( 'text', $metaData['border_size'][0] ) : null; 
$sc_meta['border_radius'] = isset( $metaData['border_radius'][0] ) ? $filter->sanitize_field( 'text', $metaData['border_radius'][0] ) : null; 

$sc_meta['circle_fill_color'] = isset( $metaData['circle_fill_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['circle_fill_color'][0] ) : null; 
$sc_meta['circle_empty_color'] = isset( $metaData['circle_empty_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['circle_empty_color'][0] ) : null; 
$sc_meta['circle_border_width'] = isset( $metaData['circle_border_width'][0] ) ? $filter->sanitize_field( 'text', $metaData['circle_border_width'][0] ) : null;  

$sc_meta['btn'] = isset( $metaData['btn'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['btn'][0]) ) : null; 
$sc_meta['btn_bg'] = isset( $metaData['btn_bg'][0] ) ? $filter->sanitize_field( 'color', $metaData['btn_bg'][0] ) : null; 
$sc_meta['btn_border_color'] = isset( $metaData['btn_border_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['btn_border_color'][0] ) : null; 
$sc_meta['btn_hover'] = isset( $metaData['btn_hover'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['btn_hover'][0]) ) : null; 
$sc_meta['btn_hover_bg'] = isset( $metaData['btn_hover_bg'][0] ) ? $filter->sanitize_field( 'color', $metaData['btn_hover_bg'][0] ) : null; 
$sc_meta['btn_border_hover_color'] = isset( $metaData['btn_border_hover_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['btn_border_hover_color'][0] ) : null; 

$css  = null;

if ( $value = $sc_meta['width'] ) { 
    $css  .= ".rtrs-affiliate-sc-{$sc_id}{";
    $css .= "width:" . $value . ";";
    $css .= "}";
} 

$typo = ( ! empty( $sc_meta['product_title'] ) ? $sc_meta['product_title'] : array() );  
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-title h3{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . ";";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . ";";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . ";";
        }
        $css .= "}";  
    }
} 

$typo = ( ! empty( $sc_meta['product_desc'] ) ? $sc_meta['product_desc'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-text p{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . ";";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . ";";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . ";";
        }
        $css .= "}"; 
    } 
}    

$typo = ( ! empty( $sc_meta['style_regular_price'] ) ? $sc_meta['style_regular_price'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-price-area .rtrs-regular-price{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . ";";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . ";";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . ";";
        }
        $css .= "}"; 
    } 
}

$typo = ( ! empty( $sc_meta['style_offer_price'] ) ? $sc_meta['style_offer_price'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-price-area .rtrs-offer-price{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . ";";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . ";";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . ";";
        }
        $css .= "}"; 
    } 
}
//.rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary .rtrs-feedback-box
//border

if ( $value = $sc_meta['border_radius'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary{";
    $css .= "border-radius:" . $value . " !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['border_size'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary{";
    $css .= "border:" . $value . " solid !important;";
    $css .= "}";

    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-rating-item, .rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary .rtrs-feedback-box{";
    $css .= "border-right:" . $value . " solid !important;";
    $css .= "}";

    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-rating-item:last-child, .rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary .rtrs-feedback-box:last-child{";
    $css .= "border-right: none !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['border_color'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-rating-item, .rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary, .rtrs-affiliate-sc-{$sc_id} .rtrs-feedback-summary .rtrs-feedback-box{";
    $css .= "border-color:" . $value . " !important;";
    $css .= "}";
} 

//circle
if ( $value = $sc_meta['circle_border_width'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-circle-bar svg circle{";
    $css .= "stroke-width:" . $value . " !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['circle_empty_color'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-circle-bar svg circle{";
    $css .= "stroke:" . $value . " !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['circle_fill_color'] ) {
    $css .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-summary-2 .rtrs-circle-bar svg circle:nth-child(2n){";
    $css .= "stroke:" . $value . " !important;";
    $css .= "}";
} 

// button
$typo = ( ! empty( $sc_meta['btn'] ) ? $sc_meta['btn'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . " !important;";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px !important;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . " !important;";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . "!important;";
        }
        $css .= "}";  
    }
}  
 
if ( $value = $sc_meta['btn_bg'] ) {
    $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn{";
    $css .= "background:" . $value . " !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['btn_border_color'] ) {
    $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn{";
    $css .= "border-color:" . $value . " !important;";
    $css .= "}";
} 

$typo = ( ! empty( $sc_meta['btn_hover'] ) ? $sc_meta['btn_hover'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn:hover{";
        if ( $typo_color ) {
            $css .= "color:" . $typo_color . " !important;";
        }
        if ( $typo_size ) {
            $css .= "font-size:" . $typo_size . "px !important;";
        }
        if ( $typo_weight ) {
            $css .= "font-weight:" . $typo_weight . " !important;";
        }
        if ( $typo_alignment ) {
            $css .= "text-align:" . $typo_alignment . "!important;";
        }
        $css .= "}";  
    }
}  
 
if ( $value = $sc_meta['btn_hover_bg'] ) {
    $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn:hover{";
    $css .= "background:" . $value . " !important;";
    $css .= "}";
} 
if ( $value = $sc_meta['btn_border_hover_color'] ) {
    $css  .= ".rtrs-affiliate-sc-{$sc_id} .rtrs-buy-btn:hover{";
    $css .= "border-color:" . $value . " !important;";
    $css .= "}";
} 

if ( $css ) {
    echo esc_html( $css );
} 