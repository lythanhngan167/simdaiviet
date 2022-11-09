<?php

namespace RtclStore\Widgets;

use Rtcl\Helpers\Functions;
use Rtcl\Models\WidgetFields;
use RtclStore\Resources\Options;

class Search extends \WP_Widget {

	protected $style = [];

	protected $widget_slug;

	public function __construct() {

		$this->widget_slug = 'rtcl-widget-store-search';

		parent::__construct(
			$this->widget_slug,
			esc_html__( 'Classified Listing Store Search', 'classified-listing-store' ),
			array(
				'classname'   => 'rtcl ' . $this->widget_slug,
				'description' => esc_html__( 'A Search feature for store', 'classified-listing-store' )
			)
		);
	}

	public function widget( $args, $instance ) {
		$data             = [
			'id'                          => wp_rand(),
			'style'                       => ! empty( $instance['style'] ) && $instance['style'] === 'inline' ? 'inline' : 'vertical',
			'can_search_by_keyword'      => ! empty( $instance['search_by_keyword'] ) ? 1 : 0,
			'can_search_by_category'      => ! empty( $instance['search_by_category'] ) ? 1 : 0,
			'selected_category'           => false,
		];
		$data['template'] = "widgets/search/{$data['style']}";
		$data             = apply_filters( 'rtcl_widget_store_search_values', $data, $args, $instance, $this );
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		Functions::get_template( $data['template'], $data, '', rtclStore()->get_plugin_template_path() );

		echo $args['after_widget'];

	}

	public function update( $new_instance, $old_instance ) {
		$instance                           = $old_instance;
		$instance['title']                  = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['style']                  = ! empty( $new_instance['style'] ) ? strip_tags( $new_instance['style'] ) : 'vertical';
		$instance['search_by_keyword']      = ! empty( $new_instance['search_by_keyword'] ) ? 1 : 0;
		$instance['search_by_category']     = ! empty( $new_instance['search_by_category'] ) ? 1 : 0;

		return apply_filters( 'rtcl_widget_store_search_update_values', $instance, $new_instance, $old_instance, $this );
	}

	public function form( $instance ) {

		// Define the array of defaults
		$defaults = [
			'title'                 => esc_html__( 'Search Store', 'classified-listing-store' ),
			'style'                 => 'vertical',
			'search_by_keyword'     => 1,
			'search_by_category'    => 1,
		];

		// Parse incoming $instance into an array and merge it with $defaults
		$instance     = wp_parse_args(
			(array) $instance,
			apply_filters( 'rtcl_widget_store_search_default_values', $defaults, $instance, $this )
		);
		$fields       = Options::store_search_widget_fields();
		$widgetFields = new WidgetFields( $fields, $instance, $this );
		$widgetFields->render();
	}

}