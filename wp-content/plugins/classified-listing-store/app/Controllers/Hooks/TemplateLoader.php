<?php

namespace RtclStore\Controllers\Hooks;


use Rtcl\Helpers\Functions;
use RtclStore\Helpers\Functions as StoreFunctions;
use RtclStore\Shortcodes\Stores;
use WP_Post;

class TemplateLoader
{

    /**
     * Listing page ID.
     *
     * @var integer
     */
    private static $store_page_id = 0;


    /**
     * Store whether we're processing a listing inside the_content filter.
     *
     * @var boolean
     */
    private static $in_content_filter = false;

    /**
     * Is ClassifiedListing support defined?
     *
     * @var boolean
     */
    private static $theme_support = false;

    static function init() {
        if (!StoreFunctions::is_store_enabled()) {
            return;
        }
        self::$theme_support = current_theme_supports('rtcl');
        self::$store_page_id = absint(Functions::get_page_id('store'));

        if (self::$theme_support) {
            add_filter('template_include', [__CLASS__, 'template_loader']);
        } else {
            // Unsupported themes.
            add_action('template_redirect', [__CLASS__, 'unsupported_theme_init']);
        }
    }

    /**
     *
     * @param string $template Template to load.
     *
     * @return string
     */
    public static function template_loader($template) {
        if (is_embed()) {
            return $template;
        }

        $default_file = self::get_template_loader_default_file();


        if ($default_file) {
            /**
             * Filter hook to choose which files to find before WooCommerce does it's own logic.
             *
             * @since 3.0.0
             * @var array
             */
            $search_files = self::get_template_loader_files($default_file);
            $template = locate_template($search_files);

            if (!$template || RTCL_STORE_TEMPLATE_DEBUG_MODE) {
                $template = rtclStore()->plugin_path() . '/templates/' . $default_file;
                $template = apply_filters('rtcl_store_template_loader_fallback_file', $template, $default_file);
            }
        }

        return $template;
    }

    private static function get_template_loader_default_file() {
        $default_file = '';

        if (StoreFunctions::is_single_store()) {
            $default_file = 'single-store.php';
        } elseif (StoreFunctions::is_store_taxonomy()) {
            $object = get_queried_object();
            if (StoreFunctions::is_store_category()) {
                $default_file = 'taxonomy-' . $object->taxonomy . '.php';
            } else {
                $default_file = 'archive-store.php';
            }
        } elseif (StoreFunctions::is_store() && self::$theme_support) {
            $default_file = 'archive-store.php';
        }

        return $default_file;
    }

    private static function get_template_loader_files($default_file) {

        $templates = apply_filters('rtcl_store_template_loader_files', array(), $default_file);
        $templates[] = 'rtcl.php';

        if (is_page_template()) {
            $templates[] = get_page_template_slug();
        }

        if (is_singular('store')) {
            $object = get_queried_object();
            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "single-store-{$name_decoded}.php";
            }
            $templates[] = "single-store-{$object->post_name}.php";
        }

        if (StoreFunctions::is_store_taxonomy()) {
            $object = get_queried_object();
            $templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = rtcl()->get_template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
            $templates[] = 'taxonomy-' . $object->taxonomy . '.php';
            $templates[] = rtcl()->get_template_path() . 'taxonomy-' . $object->taxonomy . '.php';
        }

        $templates[] = $default_file;
        $templates[] = rtcl()->get_template_path() . $default_file;

        return array_unique($templates);
    }




    /**
     * Unsupported theme compatibility methods.
     */

    /**
     * Hook in methods to enhance the unsupported theme experience on pages.
     *
     * @since 1.3.21
     */
    public static function unsupported_theme_init() {

        if (0 < self::$store_page_id) {
            if (StoreFunctions::is_store_taxonomy()) {
                self::unsupported_theme_tax_archive_init();
            } elseif (StoreFunctions::is_single_store()) {
                self::unsupported_theme_store_page_init();
            } else {
                self::unsupported_theme_stores_page_init();
            }
        }
    }


    /**
     * Enhance the unsupported theme experience on Product Category and Attribute pages by rendering
     * those pages using the single template and shortcode-based content. To do this we make a dummy
     * post and set a shortcode as the post content. This approach is adapted from bbPress.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_tax_archive_init() {
        global $wp_query, $post;

        $queried_object = get_queried_object();
        $args = self::get_current_stores_args();
        $shortcode_args = array(
            'page'     => $args->page,
            'paginate' => true,
            'cache'    => false,
            'limit'    => apply_filters('rtcl_loop_store_per_page', Functions::get_option_item('rtcl_general_settings', 'listings_per_page'))
        );

        if (StoreFunctions::is_store_category()) {
            $shortcode_args['category'] = sanitize_title($queried_object->slug);
        } else {
            // Default theme archive for all other taxonomies.
            return;
        }

        // Description handling.
        if (!empty($queried_object->description) && (empty($_GET['store-page']) || 1 === absint($_GET['store-page']))) { // WPCS: input var ok, CSRF ok.
            $prefix = '<div class="term-description">' . Functions::format_content($queried_object->description) . '</div>'; // WPCS: XSS ok.
        } else {
            $prefix = '';
        }

        $shortcode = new Stores($shortcode_args);
        $store_page = get_post(self::$store_page_id);

        $dummy_post_properties = array(
            'ID'                    => 0,
            'post_status'           => 'publish',
            'post_author'           => $store_page->post_author,
            'post_parent'           => 0,
            'post_type'             => 'page',
            'post_date'             => $store_page->post_date,
            'post_date_gmt'         => $store_page->post_date_gmt,
            'post_modified'         => $store_page->post_modified,
            'post_modified_gmt'     => $store_page->post_modified_gmt,
            'post_content'          => $prefix . $shortcode->get_content(),
            'post_title'            => Functions::clean($queried_object->name),
            'post_excerpt'          => '',
            'post_content_filtered' => '',
            'post_mime_type'        => '',
            'post_password'         => '',
            'post_name'             => $queried_object->slug,
            'guid'                  => '',
            'menu_order'            => 0,
            'pinged'                => '',
            'to_ping'               => '',
            'ping_status'           => '',
            'comment_status'        => 'closed',
            'comment_count'         => 0,
            'filter'                => 'raw',
        );

        // Set the $post global.
        $post = new WP_Post((object)$dummy_post_properties); // @codingStandardsIgnoreLine.

        // Copy the new post global into the main $wp_query.
        $wp_query->post = $post;
        $wp_query->posts = array($post);

        // Prevent comments form from appearing.
        $wp_query->post_count = 1;
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->is_single = true;
        $wp_query->is_archive = false;
        $wp_query->is_tax = true;
        $wp_query->max_num_pages = 0;

        // Prepare everything for rendering.
        setup_postdata($post);
        remove_all_filters('the_content');
        remove_all_filters('the_excerpt');
        add_filter('template_include', array(__CLASS__, 'force_single_template_filter'));
    }


    /**
     * Hook in methods to enhance the unsupported theme experience on the Shop page.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_stores_page_init() {
        add_filter('the_content', [__CLASS__, 'unsupported_theme_stores_content_filter'], 10);
    }


    /**
     * Filter the title and insert WooCommerce content on the shop page.
     *
     * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
     *
     * @param string $title Existing title.
     * @param int    $id    ID of the post being filtered.
     *
     * @return string
     * @since 1.5.56
     */
    public static function unsupported_theme_title_filter($title, $id) {
        if (self::$theme_support || !$id !== self::$store_page_id) {
            return $title;
        }

        if (is_page(self::$store_page_id) || (is_home() && 'page' === get_option('show_on_front') && absint(get_option('page_on_front')) === self::$store_page_id)) {
            $args = self::get_current_stores_args();
            $title_suffix = array();

            if ($args->page > 1) {
                /* translators: %d: Page number. */
                $title_suffix[] = sprintf(esc_html__('Page %d', 'classified-listing-store'), $args->page);
            }

            if ($title_suffix) {
                $title = $title . ' &ndash; ' . implode(', ', $title_suffix);
            }
        }
        return $title;
    }


    /**
     * Filter the content and insert ClassifiedListing content on the Listings page.
     *
     * For non-RTCL themes, this will setup the main Listings page to be shortcode based to improve default appearance.
     *
     * @param string $content Existing post content.
     *
     * @return string
     * @since 1.5.56
     */
    public static function unsupported_theme_stores_content_filter($content) {
        global $wp_query;

        if (self::$theme_support || !is_main_query() || !in_the_loop()) {
            return $content;
        }

        self::$in_content_filter = true;

        // Remove the filter we're in to avoid nested calls.
        remove_filter('the_content', [__CLASS__, 'unsupported_theme_stores_content_filter']);

        // Unsupported theme shop page.
        if (is_page(self::$store_page_id)) {
            $args = self::get_current_stores_args();
            $shortcode = new Stores(
                array_merge(
                    rtclStore()->query->get_store_catalog_ordering_args(),
                    array(
                        'page'     => $args->page,
                        'paginate' => true,
                        'cache'    => false,
                        'limit'    => apply_filters('rtcl_loop_store_per_page', Functions::get_option_item('rtcl_general_settings', 'listings_per_page'))
                    )
                )
            );

            // Allow queries to run e.g. layered nav.
            add_action('pre_get_posts', [rtclStore()->query, 'store_query']);

            $content = $content . $shortcode->get_content();

            // Remove actions and self to avoid nested calls.
            remove_action('pre_get_posts', [rtcl()->query, 'store_query']);
            rtclStore()->query->remove_ordering_args();
        }

        self::$in_content_filter = false;

        return $content;
    }

    /**
     * Hook in methods to enhance the unsupported theme experience on Listing pages.
     *
     * @since 1.5.56
     */
    private static function unsupported_theme_store_page_init() {
        add_filter('the_content', [__CLASS__, 'unsupported_theme_store_content_filter'], 10);
    }


    /**
     * Filter the content and insert ClassifiedListing content on the shop page.
     *
     * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
     *
     * @param string $content Existing post content.
     *
     * @return string
     * @since 1.2.31
     */
    public static function unsupported_theme_store_content_filter($content) {
        global $wp_query;

        if (self::$theme_support || !is_main_query() || !in_the_loop()) {
            return $content;
        }

        self::$in_content_filter = true;

        // Remove the filter we're in to avoid nested calls.
        remove_filter('the_content', [__CLASS__, 'unsupported_theme_shop_content_filter']);

        if (StoreFunctions::is_single_store()) {
            if (StoreFunctions::is_store_expired()) {
                ob_start();
                do_action('rtcl_single_store_expired_content');
                return ob_get_clean();
            }
            $content = do_shortcode('[rtcl_store_page id="' . get_the_ID() . '" show_title=0 status="any"]');
        }

        self::$in_content_filter = false;

        return $content;
    }


    /**
     * Force the loading of one of the single templates instead of whatever template was about to be loaded.
     *
     * @param string $template Path to template.
     *
     * @return string
     * @since 1.5.56
     */
    public static function force_single_template_filter($template) {
        $possible_templates = array(
            'page',
            'single',
            'singular',
            'index',
        );

        foreach ($possible_templates as $possible_template) {
            $path = get_query_template($possible_template);
            if ($path) {
                return $path;
            }
        }

        return $template;
    }

    /**
     * Get information about the current listing page view.
     *
     * @return object
     * @since 1.5.56
     */
    private static function get_current_stores_args() {
        return (object)array(
            'page' => absint(max(1, absint(get_query_var('paged'))))
        );
    }
}