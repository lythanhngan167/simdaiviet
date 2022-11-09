<?php 
// Security check
defined('ABSPATH') || die();

define( 'RT_SITE_URL',  get_site_url() );

class RTOptimizeConfig {
    
    /**
     * Overall configuration of optimization
    */
    static private $config = [

        'TextDomain' => 'classima',

        'sections' => [

            [
                'id' => 'rt_optimize',
                'title' => 'Optimize',
                'description' => 'Optimize your website for <b>google pagespeed</b>, <b>gtmatrix</b> etc.',

                'sub_sections' => [

                    [

                        'id' => 'rt_preload',
                        'title' => 'Preload',
                        'description' => 'Preload resources, like <b>fonts</b>, <b>styles</b> etc.',
                        'icon' => 'el el-fast-forward',
                        'fields' => [

                            [
                                'id' => 'rt_preload_list',
                                'sanitize_callback' => '',
                                'label' => 'Preload Resources',
                                'type' => 'textarea',
                                'description' => 'A comma(,) seperated list of absolute URL to preload the resources.',
                                'desc' => 'Set the textarea empty to if you don\'t want to use this feature.',
                                'default' => RT_SITE_URL.'/wp-content/themes/classima/assets/fonts/fa-regular-400.woff2,'.RT_SITE_URL.'/wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/fa-solid-900.woff2'
                            ],
                            [
                                'id' => 'rt_preconnect_list',
                                'sanitize_callback' => '',
                                'label' => 'Preconnect Domains',
                                'type' => 'textarea',
                                'description' => 'A comma(,) seperated list of sites to preconnect domains.',
                                'default' => 'https://fonts.gstatic.com/'
                            ]

                        ]

                    ],
                    [
                        'id' => 'rt_wpo_exclude_script',
                        'title' => 'Exclude',
                        'description' => 'Exclude script(s) from async loading for WP Optimize',
                        'icon' => 'el el-exclamation-sign',
                        'fields' => [
                            [
                                'id' => 'rt_wpo_exclude_list',
                                'label' => 'Exclude scripts',
                                'description' => 'A comma(,) seperated list of scripts. Partial url are allowed.',
                                'type' => 'textarea',
                                'default' => '/wp-includes/js/tinymce/tinymce.min.js',
                                'sanitize_callback' => ''
                            ]
                        ]
                    ],

                    [
                        'id' => 'rt_jquery',
                        'title' => 'jQuery',
                        'description' => 'jQuery optimization options.',
                        'icon' => 'el el-usd',
                        'fields' => [
                            [
                                'id' => 'rt_jquery_migrate',
                                'label' => 'JQuery Migrate',
                                'description' => 'Exclude jQuery migrate from loading.',
                                'type' => 'checkbox',
                                'default' => '',
                                'sanitize_callback' => ''
                            ],
                            [
                                'id' => 'rt_jquery_passive_event_listener',
                                'label' => 'JQuery Passive Event',
                                'description' => 'Turn on jQuery passive event listener.',
                                'type' => 'checkbox',
                                'default' => '',
                                'sanitize_callback' => ''
                            ]
                        ]
                    ],


                    [
                        'id' => 'rt_elementor',
                        'title' => 'Elementor',
                        'description' => 'Elementor optimization options',
                        'icon' => 'el el-dashboard',
                        'fields' => [
                            [
                                'id' => 'rt_elementor_bg_lazy',
                                'label' => 'Lazy Load BG Image',
                                'description' => 'Turn on elementor background image lazy load',
                                'type' => 'checkbox',
                                'default' => '',
                                'sanitize_callback' => ''
                            ],
                        ]
                    ],

                ]

            ]

        ]

    ];

    static public function get_config(){
        return self::$config;
    }

}