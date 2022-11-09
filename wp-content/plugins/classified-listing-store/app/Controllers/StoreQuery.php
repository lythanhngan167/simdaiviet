<?php

namespace RtclStore\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;
use WP_Query;

class StoreQuery
{
    /**
     * Query vars to add to wp.
     *
     * @var array
     */
    public $query_vars = array();

    /**
     * Reference to the main stores query on the page.
     *
     * @var array
     */
    private static $store_query;

    /**
     * Constructor for the query class. Hooks in methods.
     */
    public function __construct() {
        if (!is_admin()) {
            add_action('pre_get_posts', array($this, 'pre_get_posts'));
        }
    }


    /**
     * Controls WP displays the courses in a page which setup to display on homepage
     *
     * @param $q WP_Query
     *
     * @return WP_Query
     */
    public function pre_get_posts($q) {
        // We only want to affect the main query and not in admin
        if (!$q->is_main_query() || is_admin()) {
            return $q;
        }

        remove_action('pre_get_posts', array($this, 'pre_get_posts'), 10);

        $store_page_id = absint(Functions::get_page_id('store'));
        $front_page_id = absint(get_option('page_on_front'));
        // Fixes for queries on static homepages.
        if ($q->is_home() && 'page' === get_option('show_on_front')) {
            $_query = wp_parse_args($q->query);
            // When orderby is set, WordPress shows posts on the front-page. Get around that here.
            if ($store_page_id && $front_page_id === $store_page_id) {
                if (empty($_query) || !array_diff(array_keys($_query), array(
                        'preview',
                        'page',
                        'paged',
                        'cpage',
                        'orderby'
                    ))) {
                    $q->set('page_id', $front_page_id);
                    $q->is_page = true;
                    $q->is_home = false;

                    // WP supporting themes show post type archive.
                    if (current_theme_supports('rtcl')) {
                        $q->set('post_type', rtclStore()->post_type);
                    } else {
                        $q->is_singular = true;
                    }
                }
            }

        }

        // Fix product feeds.
        if ($q->is_feed() && $q->is_post_type_archive(rtclStore()->post_type)) {
            $q->is_comment_feed = false;
        }
        // Special check for shops with the PRODUCT POST TYPE ARCHIVE on front.
        if (current_theme_supports('rtcl') && $q->is_page() && 'page' === get_option('show_on_front') && $store_page_id && absint($q->get('page_id')) === $store_page_id) {
            // This is a front-page shop.
            $q->set('post_type', rtclStore()->post_type);
            $q->set('page_id', '');

            if (isset($q->query['paged'])) {
                $q->set('paged', $q->query['paged']);
            }

            // Define a variable so we know this is the front page shop later on.
            rtcl()->define('RTCL_STORES_IS_ON_FRONT', true);

            // Get the actual WP page to avoid errors and let us use is_front_page().
            // This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
            global $wp_post_types;

            $stores_page = get_post($store_page_id);

            $wp_post_types[rtcl()->post_type]->ID = $stores_page->ID;
            $wp_post_types[rtcl()->post_type]->post_title = $stores_page->post_title;
            $wp_post_types[rtcl()->post_type]->post_name = $stores_page->post_name;
            $wp_post_types[rtcl()->post_type]->post_type = $stores_page->post_type;
            $wp_post_types[rtcl()->post_type]->ancestors = get_ancestors($stores_page->ID, $stores_page->post_type);

            // Fix conditional Functions like is_front_page.
            $q->is_singular = false;
            $q->is_post_type_archive = true;
            $q->is_archive = true;
            $q->is_page = true;


            // Remove post type archive name from front page title tag.
            add_filter('post_type_archive_title', '__return_empty_string', 5);

            // Fix WP SEO.
            if (class_exists('WPSEO_Meta')) {
                add_filter('wpseo_metadesc', [$this, 'wpseo_metadesc']);
                add_filter('wpseo_metakey', [$this, 'wpseo_metakey']);
            }
        } elseif (!$q->is_post_type_archive(rtclStore()->post_type) && !$q->is_tax(get_object_taxonomies(rtclStore()->post_type))) {
        	// Only apply to listing categories, the listing post archive, the Listings page, listing location taxonomies.
            return $q;
        }
        add_action('pre_get_posts', array($this, 'pre_get_posts'), 10);
        $this->store_query($q);
    }

    /**
     * WP SEO meta description.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metadesc() {
        return \WPSEO_Meta::get_value( 'metadesc', Functions::get_page_id( 'store' ) );
    }

    /**
     * WP SEO meta key.
     *
     * Hooked into wpseo_ hook already, so no need for function_exist.
     *
     * @return string
     */
    public function wpseo_metakey() {
        return \WPSEO_Meta::get_value( 'metakey', Functions::get_page_id( 'store' ) );
    }

    /**
     * Remove the query.
     */
    public function remove_product_query() {
        remove_action('pre_get_posts', array($this, 'pre_get_posts'));
    }

    /**
     * Remove ordering queries.
     */
    public function remove_ordering_args() {
        // TODO : need to add here
    }


    /**
     * Returns an array of arguments for ordering products based on the selected values.
     *
     * @param string $orderby Order by param.
     * @param string $order   Order param.
     *
     * @return array
     */
    public function get_store_catalog_ordering_args($orderby = '', $order = '') {
        // Get ordering from query string unless defined.
        if (!$orderby) {
            $orderby_value = isset($_GET['orderby']) ? Functions::clean((string)wp_unslash($_GET['orderby'])) : Functions::clean(get_query_var('orderby')); // WPCS: sanitization ok, input var ok, CSRF ok.

            if (!$orderby_value) {
                if (is_search()) {
                    $orderby_value = 'relevance';
                } else {
                    $order_by = Functions::get_option_item('rtcl_general_settings', 'orderby', 'date');
                    $order = Functions::get_option_item('rtcl_general_settings', 'order', 'desc');
                    $orderby_value = apply_filters('rtcl_default_catalog_orderby', $order_by . "-" . $order, $order_by, $order);
                }
            }

            // Get order + orderby args from string.
            $orderby_value = is_array($orderby_value) ? $orderby_value : explode('-', $orderby_value);
            $orderby = esc_attr($orderby_value[0]);
            $order = !empty($orderby_value[1]) ? $orderby_value[1] : $order;
        }

        // Convert to correct format.
        $orderby = strtolower(is_array($orderby) ? (string)current($orderby) : (string)$orderby);
        $order = strtoupper(is_array($order) ? (string)current($order) : (string)$order);
        $args = array(
            'orderby'  => $orderby,
            'order'    => ('DESC' === $order) ? 'DESC' : 'ASC',
            'meta_key' => '', // @codingStandardsIgnoreLine
        );

        switch ($orderby) {
            case 'id':
                $args['orderby'] = 'ID';
                break;
            case 'menu_order':
                $args['orderby'] = 'menu_order title';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order'] = ('DESC' === $order) ? 'DESC' : 'ASC';
                break;
            case 'date' :
                $args['orderby'] = 'date';
                $args['order'] = ('DESC' === $order) ? 'DESC' : 'ASC';
                break;
            case 'rand' :
                $args['orderby'] = 'rand';
                break;
        }

        return apply_filters('rtcl_get_store_catalog_ordering_args', $args, $orderby, $order);
    }

    /**
     * Query the stores, applying sorting/ordering etc.
     * This applies to the main WordPress loop.
     *
     * @param WP_Query $q Query instance.
     */
    public function store_query($q) {
        if (!is_feed()) {
            $ordering = $this->get_store_catalog_ordering_args();
            $q->set('orderby', $ordering['orderby']);
            $q->set('order', $ordering['order']);

            if (isset($ordering['meta_key'])) {
                $q->set('meta_key', $ordering['meta_key']);
            }
        }

        if (isset($_GET['q']) && rtclStore()->post_type === $q->get('post_type')) {
            $q->set('s', $_GET['q']);
        }

        // Meta query for listing
        $q->set('meta_query', $this->get_meta_query($q->get('meta_query'), true));
        $q->set('tax_query', $this->get_tax_query($q->get('tax_query'), true));
        $q->set('rtcl_store_query', 'rtcl_store_query');
        $q->set('post__in', array_unique((array)apply_filters('rtcl_loop_store_post_in', array())));

        // Listings per page.
        $q->set('posts_per_page', $q->get('posts_per_page') ? $q->get('posts_per_page') : apply_filters('rtcl_stores_per_page', Functions::get_option_item('rtcl_general_settings', 'listings_per_page')));

        // Store reference to this query.
        self::$store_query = $q;

        do_action('rtcl_store_query', $q, $this);
    }

    /**
     * Appends meta queries to an array.
     *
     * @param array $meta_query Meta query.
     * @param bool  $main_query If is main query.
     *
     * @return array
     */
    public function get_meta_query($meta_query = array(), $main_query = false) {
        if (!is_array($meta_query)) {
            $meta_query = array(
                'relation' => 'AND'
            );
        }

        return array_filter(apply_filters('rtcl_store_query_meta_query', $meta_query, $this));
    }


    /**
     * Appends tax queries to an array.
     *
     * @param array $tax_query  Tax query.
     * @param bool  $main_query If is main query.
     *
     * @return array
     */
    public function get_tax_query($tax_query = array(), $main_query = false) {
        if (!is_array($tax_query)) {
            $tax_query = array(
                'relation' => 'AND',
            );
        }

        return array_filter(apply_filters('rtcl_store_query_tax_query', $tax_query, $this));
    }
}
