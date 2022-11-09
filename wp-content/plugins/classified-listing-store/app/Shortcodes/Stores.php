<?php

namespace RtclStore\Shortcodes;

use WP_Query;
use Rtcl\Helpers\Pagination;
use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Helpers\Functions as StoreFunctions;

class Stores
{

    /**
     * Attributes.
     *
     * @since 1.3.21
     * @var   array
     */
    protected $attributes = array();

    /**
     * Query args.
     *
     * @since 1.3.21
     * @var   array
     */
    protected $query_args = array();

    /**
     * Initialize shortcode.
     *
     * @param array  $attributes Shortcode attributes.
     * @param string $type       $type
     *
     * @since 1.3.21
     */
    public function __construct($attributes = array(), $type = 'store_list') {
        $this->type = $type;
        $this->attributes = $this->parse_attributes($attributes);
        $this->query_args = $this->parse_query_args();
    }


    /**
     * Parse attributes.
     *
     * @param array $attributes Shortcode attributes.
     *
     * @return array
     * @since  1.3.21
     */
    protected function parse_attributes($attributes) {
        $attributes = shortcode_atts(
            array(
                'limit'          => '-1',      // Results limit.
                'columns'        => '',        // Number of columns.
                'orderby'        => '',        // menu_order, title, date, rand, price, popularity, rating, or id.
                'order'          => '',        // ASC or DESC.
                'ids'            => '',        // Comma separated IDs.
                'category'       => '',        // Comma separated category slugs or ids.
                'cat_operator'   => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
                'terms'          => '',        // Comma separated term slugs or ids.
                'terms_operator' => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
                'class'          => '',        // HTML class.
                'page'           => 1,         // Page for pagination.
                'paginate'       => false,     // Should results be paginated.
                'cache'          => true,      // Should shortcode output be cached.,
                'map'            => 0
            ),
            $attributes,
            $this->type
        );

        return $attributes;
    }


    /**
     * Parse query args.
     *
     * @return array
     * @since  1.3.21
     */
    protected function parse_query_args() {
        $query_args = array(
            'post_type'           => rtclStore()->post_type,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => false === RtclFunctions::string_to_bool($this->attributes['paginate']),
            'orderby'             => empty($_GET['orderby']) ? $this->attributes['orderby'] : RtclFunctions::clean(wp_unslash($_GET['orderby'])), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            'posts_per_page'      => intval($this->attributes['limit']),
            'paged'               => Pagination::get_page_number()
        );

        $orderby_value = explode('-', $query_args['orderby']);
        $orderby = esc_attr($orderby_value[0]);
        $order = !empty($orderby_value[1]) ? $orderby_value[1] : strtoupper($this->attributes['order']);
        $query_args['orderby'] = $orderby;
        $query_args['order'] = $order;

        if (RtclFunctions::string_to_bool($this->attributes['paginate'])) {
            $this->attributes['page'] = absint(empty($_GET['store-page']) ? 1 : $_GET['store-page']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }

        $ordering_args = rtclStore()->query->get_store_catalog_ordering_args($query_args['orderby'], $query_args['order']);
        $query_args['orderby'] = $ordering_args['orderby'];
        $query_args['order'] = $ordering_args['order'];
        if ($ordering_args['meta_key']) {
            $query_args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        }

        if (1 < $this->attributes['page']) {
            $query_args['paged'] = absint($this->attributes['page']);
        }

        $query_args['meta_query'] = rtcl()->query->get_meta_query(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
        $query_args['tax_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

        // IDs.
        $this->set_ids_query_args($query_args);

        // Categories.
        $this->set_categories_query_args($query_args);

        $query_args = apply_filters('rtcl_shortcode_store_query_args', $query_args, $this->attributes, $this->type);

        // Always query only IDs.
        $query_args['fields'] = 'ids';

        return $query_args;
    }


    /**
     * Set ids query args.
     *
     * @param array $query_args Query args.
     *
     * @since 1.3.21
     */
    protected function set_ids_query_args(&$query_args) {
        if (!empty($this->attributes['ids'])) {
            $ids = array_map('trim', explode(',', $this->attributes['ids']));

            if (1 === count($ids)) {
                $query_args['p'] = $ids[0];
            } else {
                $query_args['post__in'] = $ids;
            }
        }
    }


    /**
     * Set categories query args.
     *
     * @param array $query_args Query args.
     *
     * @since 1.3.21
     */
    protected function set_categories_query_args(&$query_args) {
        if (!empty($this->attributes['category'])) {
            $categories = array_map('sanitize_title', explode(',', $this->attributes['category']));
            $field = 'slug';

            if (is_numeric($categories[0])) {
                $field = 'term_id';
                $categories = array_map('absint', $categories);
                // Check numeric slugs.
                foreach ($categories as $cat) {
                    $the_cat = get_term_by('slug', $cat, rtclStore()->category);
                    if (false !== $the_cat) {
                        $categories[] = $the_cat->term_id;
                    }
                }
            }

            $query_args['tax_query'][] = array(
                'taxonomy'         => rtclStore()->category,
                'terms'            => $categories,
                'field'            => $field,
                'operator'         => $this->attributes['cat_operator'],

                /*
                 * When cat_operator is AND, the children categories should be excluded,
                 * as only products belonging to all the children categories would be selected.
                 */
                'include_children' => 'AND' === $this->attributes['cat_operator'] ? false : true,
            );
        }
    }


    /**
     * Get shortcode content.
     *
     * @return string
     * @since 1.3.21
     */
    public function get_content() {
        wp_enqueue_style('rtcl-store-public');
        wp_enqueue_script('rtcl-store-public');
        return $this->listing_loop();
    }

    protected function get_query_results() {

        $query = new WP_Query($this->query_args);

        $paginated = !$query->get('no_found_rows');
        // TODO : Need to  caching here
        $results = (object)array(
            'ids'          => wp_parse_id_list($query->posts),
            'total'        => $paginated ? (int)$query->found_posts : count($query->posts),
            'total_pages'  => $paginated ? (int)$query->max_num_pages : 1,
            'per_page'     => (int)$query->get('posts_per_page'),
            'current_page' => $paginated ? (int)max(1, $query->get('paged', 1)) : 1,
        );

        return $results;
    }


    /**
     * Loop over found products.
     *
     * @return string
     * @since  1.3.21
     */
    protected function listing_loop() {

        $wrapper_classes = apply_filters('rtcl_shortcode_store_wrapper_class', ['rtcl'], $this);
        $stores = $this->get_query_results();

        ob_start();

        if ($stores && $stores->ids) {
            // Prime caches to reduce future queries.
            if (is_callable('_prime_post_caches')) {
                _prime_post_caches($stores->ids);
            }

            // Setup the loop.
            StoreFunctions::setup_loop(
                array(
                    'name'         => $this->type,
                    'is_shortcode' => true,
                    'is_search'    => false,
                    'is_paginated' => RtclFunctions::string_to_bool($this->attributes['paginate']),
                    'total'        => $stores->total,
                    'total_pages'  => $stores->total_pages,
                    'per_page'     => $stores->per_page,
                    'current_page' => $stores->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action("rtcl_shortcode_before_{$this->type}_loop", $this->attributes);

            // Fire standard shop loop hooks when paginating results so we can show result counts and so on.
            if (RtclFunctions::string_to_bool($this->attributes['paginate'])) {
                do_action('rtcl_before_store_loop');
            }

            StoreFunctions::store_loop_start();

            if (StoreFunctions::get_loop_prop('total')) {
                foreach ($stores->ids as $product_id) {
                    $GLOBALS['post'] = get_post($product_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    setup_postdata($GLOBALS['post']);

                    // Render product template.
                    RtclFunctions::get_template_part('content', 'store');
                }
            }

            $GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            StoreFunctions::store_loop_end();

            // Fire standard shop loop hooks when paginating results so we can show result counts and so on.
            if (RtclFunctions::string_to_bool($this->attributes['paginate'])) {
                do_action('rtcl_after_store_loop');
            }

            do_action("rtcl_shortcode_after_{$this->type}_loop", $this->attributes);

            wp_reset_postdata();
            StoreFunctions::reset_loop();
        } else {
            do_action("rtcl_shortcode_{$this->type}_loop_no_results", $this->attributes);
        }

        $stores_html = ob_get_clean();

        return sprintf('<div class="%s">%s</div>',
            esc_attr(implode(' ', $wrapper_classes)),
            $stores_html
        );
    }
}