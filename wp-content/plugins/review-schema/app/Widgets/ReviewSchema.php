<?php

namespace Rtrs\Widgets;
 
use Rtrs\Helpers\Functions;

class ReviewSchema extends \WP_Widget {

    protected $widget_slug;

    public function __construct() {

        $this->widget_slug = 'rtrs-widget-review';

        parent::__construct(
            $this->widget_slug,
            esc_html__('Affiliate', 'review-schema'),
            array(
                'classname'   => 'rtrs ' . $this->widget_slug,
                'description' => esc_html__('A list of Affiliate.', 'review-schema') 
            )
        );

    }

    public function widget($args, $instance) { 

        echo $args['before_widget'];

        if ( !empty($instance['title']) ) {
            echo $args['before_title'] . apply_filters('widget_title', wp_kses( $instance['title'], [ 'span' => [] ] ) ) . $args['after_title'];
        }
        
        if ( $instance['shortcode_id'] ) {
            echo do_shortcode( '[rtrs-affiliate id="'. absint( $instance['shortcode_id'] ) .'"]' );  
        } 

        echo $args['after_widget']; 
    }

    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['shortcode_id'] = isset($new_instance['shortcode_id']) ? absint( $new_instance['shortcode_id'] ) : ''; 

        return $instance;
    }

    public function form($instance) {

        // Define the array of defaults
        $defaults = array(
            'title'        => '',
            'shortcode_id' => '', 
        );

        // Parse incoming $instance into an array and merge it with $defaults
        $instance = wp_parse_args(
            (array)$instance,
            $defaults
        );

        // Display the admin form
        include(RTRS_PATH . "views/widgets/review-schema.php");
    } 
}