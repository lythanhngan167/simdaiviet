<?php

namespace Rtrsp\Controllers\Admin; 

use Rtrs\Helpers\Functions;
use Rtrsp\Traits\SingletonTrait;

class HookFilter {  

    use SingletonTrait;

    function init() {    
        add_filter('rtrs_schema_sub_sections', array(&$this, 'rtrs_schema_sub_sections') );  
        add_filter('rtrs_schema_sub_organization_settings_options', array(&$this, 'rtrs_schema_sub_organization_settings_options') );  
        add_filter('rtrs_schema_archive_settings_options', array(&$this, 'rtrs_schema_archive_settings_options') );  
        add_filter( 'rtrs_media_settings_options', array(&$this, 'rtrs_media_settings_options') );   
        add_filter('rtrs_register_settings_tabs', array(&$this, 'register_settings_tabs') );     
        add_filter('rtrs_rich_snippet_cats', array($this, 'rtrs_rich_snippet_cats') );
        add_action( 'wp_footer', array( $this, 'archive_page' ), 999 );
    }  

    function rtrs_rich_snippet_cats( $snippet_cats ) {   
        $snippet_cats['tv_series']  = esc_html__('TV Series', 'review-schema-pro');
        return $snippet_cats;  
    }

    function rtrs_schema_sub_sections( $default ) {   
        $new = array(  
            'sub_organization' => esc_html__("Sub Organization", 'review-schema')
        );   
        $default = array_merge(array_slice($default, 0, 2), $new, array_slice($default, 2)); 

        $new = array(  
            'archive' => esc_html__("Archive Page", 'review-schema')
        );   
        $default = array_merge(array_slice($default, 0, 6), $new, array_slice($default, 6)); 

        return apply_filters('rtrsp_schema_sub_sections', $default);  
    }  

    function rtrs_schema_sub_organization_settings_options( $default ) {   
        $new = array(  
            'sub_organization_section' => array(
                'title'       => esc_html__( 'Sub Organization', 'review-schema' ),
                'description'   => esc_html__('Provide your sub organization information to a Google Knowledge panel', 'review-schema'),
                'type'        => 'title', 
            ),
            'sub_organization' => array(
                "type"   => "group", 
                "title"  => esc_html__("Sub Organization", 'review-schema'),  
                "fields" => [
                    'name' => array(
                        'type'  => 'text',
                        'class' => 'regular-text',
                        'title' => esc_html__('Name', "review-schema"),
                    ), 
                    'url' => array(
                        'type'  => 'text',
                        'class' => 'regular-text',
                        'title' => esc_html__('URL', "review-schema"),
                    ) 
                ]
            )
        );  
  
        return apply_filters('rtrsp_schema_sub_organization_settings_options', $new);  
    }  

    function rtrs_schema_archive_settings_options( $default ) {   
        $new = array(  
            'archive'  => array(
                'title'   => esc_html__('Archive?', 'review-schema'),
                'description' => esc_html__('This archive page is for blog post', 'review-schema'),
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable', 'review-schema')
            ),
            'schema_type' => array( 
                'title'    => esc_html__('Schema Type', "review-schema"),
                'type'     => 'select',
                'class'    => 'regular-text',
                'options'  => array(
                    'article' => esc_html__("Article", 'review-schema'), 
                    'news_article' => esc_html__("News Article", 'review-schema'), 
                    'blog_posting' => esc_html__("Blog Posting", 'review-schema'), 
                ),
                'empty'    => esc_html__('Select One', "review-schema"), 
            ),  
        );  

        if ( is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('easy-digital-downloads/easy-digital-downloads.php') ) { 
            $new['product_archive'] = array(
                'title'   => esc_html__('Product Archive?', 'review-schema'),
                'description' => esc_html__('This archive page is for WooCommerce and EDD product', 'review-schema'),
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable', 'review-schema')
            );
        }

        if ( is_plugin_active('classified-listing/classified-listing.php') ) { 
            $new['cl_archive'] = array(
                'title'   => esc_html__('Classified Listing Archive?', 'review-schema'),
                'description' => esc_html__('This archive page is for Classified Listing', 'review-schema'),
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable', 'review-schema')
            );
        } 
  
        return apply_filters('rtrsp_schema_archive_settings_options', $new);  
    }  

    function rtrs_media_settings_options( $default ) {  

        $video_type = apply_filters( 'rtrs_media_video_type', array(
            'video/mp4' => esc_html__('mp4', 'review-schema'), 
            'video/m4v' => esc_html__('m4v', 'review-schema'), 
            'video/mov' => esc_html__('mov', 'review-schema'),  
            'video/wmv' => esc_html__('wmv', 'review-schema'),  
            'video/avi' => esc_html__('avi', 'review-schema'),  
            'video/mpg' => esc_html__('mpg', 'review-schema'),  
            'video/mov' => esc_html__('mov', 'review-schema'),  
            'video/ogv' => esc_html__('ogv', 'review-schema'),  
            'video/3gp' => esc_html__('3gp', 'review-schema'),  
            'video/3g2' => esc_html__('3g2', 'review-schema'),  
        ) );

        $new = array(  
            'video_section' => array(
                'title' => esc_html__( 'Video Upload Settings', 'review-schema' ),
                'type'  => 'title', 
            ), 
            'video_max_size'  => array(
                'title'   => esc_html__('Video Max Size', 'review-schema'),
                'type'    => 'number',  
                'default' => 2048,  
                'css'     => 'width: 70px',
                'description' => esc_html__('Change the value as KB, Like 1M = 1024KB', 'review-schema')
            ),
            'video_type' => array(
                'title'   => esc_html__( 'Supported Video Type', 'review-schema' ),
                'type'    => 'multi_checkbox',
                'default' => array(
                    'video/mp4',
                    'video/m4v',
                    'video/mov',
                    'video/wmv',
                    'video/avi',
                    'video/mpg',
                    'video/mov',
                    'video/ogv',
                    'video/3gp',
                    'video/3g2', 
                ),
                'options' => $video_type
            ),  
        );  

        $default = array_merge($default, $new);
  
        return apply_filters('rtrsp_media_settings_options', $default);  
    }   

    function register_settings_tabs( $default ) {
        $default['tools'] = esc_html__('Tools', 'review-schema');

        return apply_filters('rtrsp_register_settings_tabs', $default); 
    }

    function archive_page() {   

        $schema_obj = new \Rtrs\Models\Schema();

        global $wp_query;

        $archive_main_home = false;
        $product_main_home = false;
        $classified_main_home = false;
        $blog_main_home = false;
        
        if ( Functions::is_plugin_active('woocommerce/woocommerce.php') && is_shop() ) {
            $archive_main_home = true;
            $product_main_home = true; 
        } else if ( Functions::is_plugin_active('classified-listing/classified-listing.php') && \Rtcl\Helpers\Functions::is_listings() ) {
            $archive_main_home = true;
            $product_main_home = true;
            $classified_main_home = true;
        }  else if ( is_home() ) {
            $archive_main_home = true;
            $blog_main_home = true; 
        }; 

        if ( is_tax() || is_category() || is_tag() || $archive_main_home ) { 
            $settings_schema = get_option('rtrs_schema_archive_settings');
            
            $product_archive = false; 
            if (  
                is_tax('product_cat') ||
                is_tax('product_tag') ||
                is_tax('download_category') ||
                is_tax('download_tag')  
            ) {
                $product_archive = true; 
            }

            $classified_archive = false; 
            if (   
                is_tax('rtcl_category') || 
                is_tax('rtcl_location')  
            ) {
                $classified_archive = true; 
            }

            if ( $classified_main_home ) {
                $classified_archive = true; 
            }

            $schema_type = 'article';
            
            $schema = false;
            if ( $product_archive && isset($settings_schema['product_archive']) && $settings_schema['product_archive'] == 'yes' ) {
                $schema = true;
            } 

            if ( $classified_archive && isset($settings_schema['cl_archive']) && $settings_schema['cl_archive'] == 'yes' ) {
                $schema = true;
            }
             
            if ( ( isset($settings_schema['archive']) && $settings_schema['archive'] == 'yes' ) && ( !$product_archive && !$classified_archive && !$archive_main_home ) ) {
                $schema = true;
            } 
           
            if ( $schema ) {
                if ( isset($settings_schema['schema_type']) && $settings_schema['schema_type'] ) {
                    $schema_type = $settings_schema['schema_type']; 
                }

                if ( $product_archive || $classified_archive || $product_main_home ) {
                    $schema_type = 'product';
                }
                
                $category = get_queried_object();  
                if ( !$archive_main_home && is_object($category) ) {
                    $category_id 	   = intval($category->term_id);
                    $category_link 	   = get_category_link($category_id);
                    $category_link     = get_term_link($category_id);
                    $category_headline = single_cat_title('', false) . esc_html__(' Category', 'review-schema');

                    $archive_data = array(
                        '@context'      => "https://schema.org",
                        '@type' 		=> 'CollectionPage',
                        '@id' 		    => trailingslashit(esc_url($category_link)).'#CollectionPage',
                        'headline' 		=> esc_attr($category_headline),
                        'description' 	=> strip_tags(get_term($category_id)->description),
                        'url'		 	=> esc_url($category_link),
                    );
                } else {
                    if ( $blog_main_home ) {
                        $archive_data = array(
                            '@context'      => "https://schema.org",
                            '@type' 		=> 'CollectionPage',
                            '@id' 		    => trailingslashit(esc_url(home_url( '/' ))).'#CollectionPage',
                            'headline' 		=> get_bloginfo( 'name' ),
                            'description' 	=> get_bloginfo( 'description', 'display' ),
                            'url'		 	=> esc_url(home_url( '/' )),
                        );
                    } else {
                        if ( $classified_main_home ) {
                            $archive_data = array(
                                '@context'      => "https://schema.org",
                                '@type' 		=> 'CollectionPage',
                                '@id' 		    => trailingslashit(esc_url(get_the_permalink())).'#CollectionPage',
                                'headline' 		=> get_the_title(),
                                'description' 	=> '',
                                'url'		 	=> esc_url(get_the_permalink()),
                            );
                        } else {
                            $archive_data = array(
                                '@context'      => "https://schema.org",
                                '@type' 		=> 'CollectionPage',
                                '@id' 		    => trailingslashit(esc_url(get_post_type_archive_link(get_queried_object()->name))).'#CollectionPage',
                                'headline' 		=> get_queried_object()->label,
                                'description' 	=> '',
                                'url'		 	=> esc_url(get_post_type_archive_link(get_queried_object()->name)),
                            );
                        } 
                    } 
                }

                $itemData = [];   
                $per_page = get_option('posts_per_page');
                if ( get_query_var( 'taxonomy' ) == 'product_cat' || get_query_var( 'taxonomy' ) == 'product_tag' ) { 
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => $per_page,
                        'paged' => get_query_var( 'paged' ),
                        'tax_query' => array(
                            array(
                                'taxonomy' => get_query_var( 'taxonomy' ),
                                'field'    => 'slug',
                                'terms'    => get_query_var( 'term' ),
                            ),
                        ),
                    );

                    // Set the query
                    $wp_query = new \WP_Query( $args );
                }  

                if ( $classified_main_home ) {
                    $args = array(
                        'post_type' => 'rtcl_listing',
                        'posts_per_page' => $per_page,
                        'paged' => get_query_var( 'paged' ), 
                    );

                    // Set the query
                    $wp_query = new \WP_Query( $args );
                }

                while ( $wp_query->have_posts() ) {
                    $wp_query->the_post(); 

                    $prefix = 'rtrs_';  
                    $post_id = get_the_ID();  

                    $custom_snippet = get_post_meta( $post_id, '_rtrs_custom_rich_snippet', true );

                    if ( $custom_snippet ) {
                        $schemaCat = get_post_meta( $post_id, '_rtrs_rich_snippet_cat', false );
                        foreach ( $schemaCat as $singleCat ) {
                            $metaData = get_post_meta( $post_id, $prefix.$singleCat.'_schema', true );
                            foreach ($metaData as $meta) {
                                if ( $meta['status'] == 'show' ) {
                                    $itemData[] = $schema_obj->schemaOutput($singleCat, $meta, true); 
                                }  
                            }
                        }
                    } else { //auto generate 
                        $itemData[] = $schema_obj->autoSchemaOutput($schema_type, $post_id, true); 
                    }
                } 

                $archive_data["hasPart"] = $itemData;
                echo $schema_obj->getJsonEncode( apply_filters('rtseo_archive_page', $archive_data) ); 
            }
        }
    }
} 