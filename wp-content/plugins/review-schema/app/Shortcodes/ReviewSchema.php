<?php
namespace Rtrs\Shortcodes; 

use Rtrs\Helpers\Functions; 
use Rtrs\Models\Review;   
use Rtrs\Models\Schema;
use Rtrs\Controllers\Admin\Meta\MetaOptions;

class ReviewSchema {    
	
	public static function output( $atts ) { 
		// wp_enqueue_style( 'rtrs-app' );         
		wp_enqueue_style( 'rtrs-sc' );    // Already ( 'rtrs-app' ) Depandancy added here.
		$sc_meta = shortcode_atts( array(
			'id'  	 => '',
			'title'  => '',  
		), $atts );   

		$layout = get_post_meta($sc_meta['id'], 'layout', true);
		if ( !$layout ) {
			$layout = 'one';
		} 

		$p_meta = [
			'parent_class' => '',
			'total_rating' => null,
			'avg_rating' => null,
			'recommendation' => null,
			'title' => null,
			'summary' => null,
			'featured_image' => null,
			'criteria' => [],
			'pros' => [],
			'cons' => [],
			'regular_price' => null,
			'offer_price' => null,
			'btn_txt' => null,
			'btn_url' => null,
		];

		if ( $parent_class = get_post_meta($sc_meta['id'], 'parent_class', true) ) {
			$p_meta['parent_class'] = $parent_class;
		}

		if ( $total_rating = get_post_meta($sc_meta['id'], 'total_rating', true) ) {
			$p_meta['total_rating'] = $total_rating;
		}

		if ( $avg_rating = get_post_meta($sc_meta['id'], 'avg_rating', true) ) {
			$p_meta['avg_rating'] = $avg_rating;
		}

		if ( $recommendation = get_post_meta($sc_meta['id'], 'recommendation', true) ) {
			$p_meta['recommendation'] = $recommendation;
		}

		if ( $title = get_post_meta($sc_meta['id'], 'title', true) ) {
			$p_meta['title'] = $title;
		}

		if ( $short_desc = get_post_meta($sc_meta['id'], 'short_desc', true) ) {
			$p_meta['summary'] = $short_desc;
		}

		if ( $featured_image = get_post_meta($sc_meta['id'], 'featured_image', true) ) {
			$img = wp_get_attachment_image_src($featured_image, 'medium');
			if ( $img ) {
				$p_meta['featured_image'] = $img[0];
			}
		}

		if ( $criteria = get_post_meta($sc_meta['id'], 'rating_criteria', true) ) {
			$p_meta['criteria'] = $criteria;
		}

		if ( $pros = get_post_meta($sc_meta['id'], 'pros', true) ) {
			$p_meta['pros'] = $pros;
		}

		if ( $cons = get_post_meta($sc_meta['id'], 'cons', true) ) {
			$p_meta['cons'] = $cons;
		}

		if ( $offer_price = get_post_meta($sc_meta['id'], 'offer_price', true) ) {
			$p_meta['offer_price'] = $offer_price;
		}

		if ( $regular_price = get_post_meta($sc_meta['id'], 'regular_price', true) ) {
			$p_meta['regular_price'] = $regular_price;
		}

		if ( $btn_txt = get_post_meta($sc_meta['id'], 'btn_txt', true) ) {
			$p_meta['btn_txt'] = $btn_txt;
		}

		if ( $btn_url = get_post_meta($sc_meta['id'], 'btn_url', true) ) {
			$p_meta['btn_url'] = $btn_url;
		}
 
		$no_stroke = !function_exists('rtrsp') ? 'rtrs-affiliate-no-stroke' : '';

		echo '<div class="rtrs-review-wrap '.esc_attr( $p_meta['parent_class'] ) . ' ' . esc_attr( $no_stroke ) . ' rtrs-affiliate-wrap rtrs-affiliate-sc-'.esc_attr($sc_meta['id']).'">';
		Functions::get_template_part( 'affiliate/layout-' . $layout, 
			array(
				'p_meta' => $p_meta,
			) 
		);
		echo '</div>';

		$rich_snippet = get_post_meta($sc_meta['id'], 'rich_snippet', true);
		if ( $rich_snippet == 'custom' ) {  
			$snippet = new Schema; 
			$schema_meta = get_post_meta($sc_meta['id']);
			$all_metas = [];
			foreach ($schema_meta as $key => $value) {
				$all_metas[$key] = $value[0]; 
			} 
			echo $snippet->schemaOutput('product', $all_metas); //this data already escaped XSS issues
		}
	}   
}   
 