<?php

namespace Rtrs\Widgets; 

class Widget {

    public function __construct() {
        add_action('widgets_init', array( $this, 'register_widget' ) );
        add_action('init', [__CLASS__, 'widget_support']);
    }

    static function widget_support() {
        add_filter('elementor/widgets/wordpress/widget_args', [__CLASS__, 'elementor_wordpress_widget_support'], 10, 2);
    }

    public function register_widget() {
        register_widget(ReviewSchema::class); 
    }

    public static function elementor_wordpress_widget_support($default_widget_args, $object) {
        if (false !== strpos($object->get_widget_instance()->id_base, 'rtrs-widget-')) {
            $default_widget_args['before_widget'] = sprintf('<div id="%1$s" class="widget %2$s">', $object->get_widget_instance()->id_base, $object->get_widget_instance()->widget_options['classname']);
            $default_widget_args['after_widget'] = '</div>';
        }
        return $default_widget_args;
    }
}