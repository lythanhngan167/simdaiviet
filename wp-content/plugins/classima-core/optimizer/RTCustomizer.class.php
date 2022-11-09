<?php 
// Security check
defined('ABSPATH') || die();

class RTCustomizer extends RTOptimizerHooks implements RTOptionFramework{

    public $config; 

    public function __construct($config){

        $this->config = $config;
        
        add_action('customize_register', [&$this, 'register']);


    }

    public function get_option($id){
        
        return get_theme_mod( $id, '' );

    }

    public function register( $wp_customize = [] ){

        // Panel
        foreach($this->config['sections'] as $section){

            $wp_customize->add_panel($section['id'], array(
                'priority'       => 160,
                'title'          => __($section['title'], $this->config['TextDomain']),
                'description'    => __($section['description'], $this->config['TextDomain']),
            ));

            // Section
            foreach($section['sub_sections'] as $sub_sections){

                $wp_customize->add_section($sub_sections['id'], [
                    'title' => __($sub_sections['title'], $this->config['TextDomain']),
                    'description' => __($sub_sections['description'], $this->config['TextDomain']),
                    'description_hidden' => true,
                    'panel' => $section['id'],
                ]);

                // Fields
                foreach($sub_sections['fields'] as $field){

                    $wp_customize->add_setting($field['id'], [
                        'default' => $field['default'],
                        'sanitize_callback' => $field['sanitize_callback']
                    ]);

                    $wp_customize->add_control(
                        new WP_Customize_Control(
                            $wp_customize,
                            $field['id'],
                            array(
                                'label'    => __($field['label'], $this->config['TextDomain']),
                                'section'  => $sub_sections['id'],
                                'settings' => $field['id'],
                                'type'     => $field['type'],
                                'description' => __($field['description'], $this->config['TextDomain']),
                            )
                        )
                    );


                }

            }

        }


    }

}