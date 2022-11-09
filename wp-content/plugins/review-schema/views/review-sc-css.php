<?php

/**
 * @var integer $scID
 * @var bool    $old 
 * @var array    $metaData
 */
use Rtrs\Controllers\Admin\Meta\AddMetaBox;
 
$filter = new AddMetaBox();

$sc_id = $scID;
$sc_meta = []; 
$metaData = get_post_meta($sc_id);

$sc_meta['layout'] = isset( $metaData['layout'][0] ) && !empty( $metaData['layout'][0] ) ? $filter->sanitize_field('text', $metaData['layout'][0] ) : null;
$sc_meta['width'] = isset( $metaData['width'][0] ) && !empty( $metaData['width'][0] ) ? $filter->sanitize_field( 'text', $metaData['width'][0] ) : null; 
$sc_meta['margin'] = isset( $metaData['margin'][0] ) && !empty( $metaData['margin'][0] ) ? $filter->sanitize_field( 'text', $metaData['margin'][0] ) : null; 
$sc_meta['padding'] = isset( $metaData['padding'][0] ) && !empty( $metaData['padding'][0] ) ? $filter->sanitize_field( 'text', $metaData['padding'][0] ) : null; 
$sc_meta['author_name'] = isset( $metaData['author_name'][0] ) && !empty( $metaData['author_name'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['author_name'][0]) ) : null; 
$sc_meta['author_name_hover'] = isset( $metaData['author_name_hover'][0] ) && !empty( $metaData['author_name_hover'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['author_name_hover'][0]) ) : null;
$sc_meta['review_title'] = isset( $metaData['review_title'][0] ) && !empty( $metaData['review_title'][0] ) ? $filter->sanitize_field('style', unserialize($metaData['review_title'][0]) ) : null;   
$sc_meta['review_text'] = isset( $metaData['review_text'][0] ) && !empty( $metaData['review_text'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['review_text'][0]) ) : null;  
$sc_meta['date_text'] = isset( $metaData['date_text'][0] ) && !empty( $metaData['date_text'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['date_text'][0]) ) : null;      
$sc_meta['star_color'] = isset( $metaData['star_color'][0] ) && !empty( $metaData['star_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['star_color'][0] ) : null;   
$sc_meta['meta_icon_color'] = isset( $metaData['meta_icon_color'][0] ) && !empty( $metaData['meta_icon_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['meta_icon_color'][0] ) : null;   
 
$sc_meta['helper_btn'] = isset( $metaData['helper_btn'][0] ) && !empty( $metaData['helper_btn'][0] ) ? $filter->sanitize_field( 'style', unserialize($metaData['helper_btn'][0]) ) : null;
$sc_meta['helper_btn_color'] = isset( $metaData['helper_btn_color'][0] ) && !empty( $metaData['helper_btn_color'][0] ) ? $filter->sanitize_field( 'color', $metaData['helper_btn_color'][0] ) : null;
$sc_meta['helper_btn_hover'] = isset( $metaData['helper_btn_hover'][0] ) && !empty( $metaData['helper_btn_hover'][0] ) ? $filter->sanitize_field( 'color', $metaData['helper_btn_hover'][0] ) : null; 

$css  = null;

$css  .= ".rtrs-review-sc-{$sc_id} .rtrs-review-list .depth-2 .rtrs-reply-btn{display:none}";
if ( $sc_meta['width'] || $sc_meta['margin'] || $sc_meta['padding']) { 
    $css  .= "@media only screen and (min-width: 768px) { .rtrs-review-sc-{$sc_id}{";

    if ( $value = $sc_meta['width'] ) { 
        $css .= "width:" . $value . ";";
    } 
    if ( $value = $sc_meta['margin'] ) { 
        $css .= "margin:" . $value . ";";
    } 
    if ( $value = $sc_meta['padding'] ) { 
        $css .= "padding:" . $value . ";";
    } 

    $css .= "} }";
} 

$typo = ( ! empty( $sc_meta['author_name'] ) ? $sc_meta['author_name'] : array() );
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-author-link a{";
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

$typo = ( ! empty( $sc_meta['author_name_hover'] ) ? $sc_meta['author_name_hover'] : array() );
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-author-link a:hover{";
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
$typo = ( ! empty( $sc_meta['review_title'] ) ? $sc_meta['review_title'] : array() );  
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-review-title{";
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

$typo = ( ! empty( $sc_meta['review_text'] ) ? $sc_meta['review_text'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css             .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body p{";
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

$typo = ( ! empty( $sc_meta['date_text'] ) ? $sc_meta['date_text'] : array() ); 
if ( ! empty( $typo ) ) {
    $typo_color     = ( ! empty( $typo['color'] ) ? $typo['color'] : null );
    $typo_size      = ( ! empty( $typo['size'] ) ? absint( $typo['size'] ) : null );
    $typo_weight    = ( ! empty( $typo['weight'] ) ? $typo['weight'] : null );
    $typo_alignment = ( ! empty( $typo['align'] ) ? $typo['align'] : null ); 
    if ( $typo_color || $typo_size || $typo_weight || $typo_alignment ) {
        $css  .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-review-meta .rtrs-review-date{";
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
if ( $value = $sc_meta['star_color'] ) {
    $css  .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-review-meta .rtrs-review-rating{";
    $css .= "color:" . $value . ";";
    $css .= "}";
}

if ( $value = $sc_meta['meta_icon_color'] ) {
    $css  .= ".rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-review-meta .rtrs-calendar:before, .rtrs-review-sc-{$sc_id} .rtrs-review-box .rtrs-review-body .rtrs-review-meta .rtrs-share:before{";
    $css .= "color:" . $value . ";";
    $css .= "}";
} 

if ( $css ) {
    echo esc_html( $css );
} 