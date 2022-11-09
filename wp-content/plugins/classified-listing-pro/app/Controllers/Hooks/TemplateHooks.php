<?php

namespace RtclPro\Controllers\Hooks;

use WP_Query;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Widgets\Filter;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Options;
use RtclPro\Models\UserAuthentication;
use Rtcl\Controllers\Hooks\TemplateHooks as RtclTemplateHooks;

class TemplateHooks
{

    public static function init() {
        add_action('rtcl_after_listing_loop_item', [__CLASS__, 'sold_out_banner']);
        add_action('rtcl_single_listing_content', [__CLASS__, 'sold_out_banner'], 20);


        /**
         * Reviews
         *
         */
        add_action('rtcl_review_before', [__CLASS__, 'review_display_gravatar'], 10);
        add_action('rtcl_review_meta', [__CLASS__, 'review_display_meta'], 10);
        add_action('rtcl_review_after_meta', [__CLASS__, 'review_display_rating'], 10);
        add_action('rtcl_review_comment_text', [__CLASS__, 'review_display_comment_title'], 10);
        add_action('rtcl_review_comment_text', [__CLASS__, 'review_display_comment_text'], 20);

        add_action('rtcl_listing_loop_action', [__CLASS__, 'view_switcher'], 30);

        if (Fns::is_enable_top_listings()) {
            add_action('rtcl_listing_loop_prepend_data', [__CLASS__, 'top_listing_items'], 20);
        }

        add_action('rtcl_listing_badges', [__CLASS__, 'listing_popular_badge'], 30);
        add_action('rtcl_listing_badges', [__CLASS__, 'listing_top_badge'], 40);
        add_action('rtcl_listing_badges', [__CLASS__, 'listing_bump_up_badge'], 50);

	    add_action("rtcl_widget_filter_form", [__CLASS__, 'widget_filter_form_rating_item'], 40, 2);
        add_action("rtcl_widget_filter_form", [__CLASS__, 'widget_filter_form_cf_item'], 50, 2);

        add_action('rtcl_account_chat_endpoint', [__CLASS__, 'account_chat_endpoint']);

        if (Fns::is_enable_quick_view()) {
            /**
             * Quick View
             */
            add_action('rtcl_quick_view_gallery', [__CLASS__, 'quick_view_gallery'], 10);
            add_action('rtcl_quick_view_summary', [__CLASS__, 'quick_view_summary_title'], 10);
            add_action('rtcl_quick_view_summary', [__CLASS__, 'quick_view_summary_label'], 20);
            add_action('rtcl_quick_view_summary', [__CLASS__, 'quick_view_summary_meta'], 30);
            add_action('rtcl_quick_view_summary', [__CLASS__, 'quick_view_summary_custom_fields'], 40);
            add_action('rtcl_quick_view_summary', [__CLASS__, 'quick_view_summary_price'], 50);
            add_action('rtcl_listing_meta_buttons', [__CLASS__, 'add_quick_view_button'], 20);
        }
        if (Fns::is_enable_compare()) {
            add_action('rtcl_listing_meta_buttons', [__CLASS__, 'add_compare_button'], 30);
        }

        add_action('rtcl_listing_loop_item', [__CLASS__, 'loop_item_listable_fields'], 40);

        add_action('rtcl_my_account_verify', [__CLASS__, 'user_verification_action']);

        add_action('rtcl_my_listing_actions', [__CLASS__, 'my_listing_mark_as_sold_button'], 40);

        add_action('rtcl_add_user_information', [__CLASS__, 'add_chat_link']);// TODO: need to remove in future version
        add_action('rtcl_listing_seller_information', [__CLASS__, 'add_chat_link'], 40);

        if (Fns::registered_user_only('listing_seller_information') && !is_user_logged_in()) {
            remove_all_actions('rtcl_listing_seller_information');
            add_action('rtcl_listing_seller_information', [__CLASS__, 'add_user_login_link'], 1);
        }
    }

    public static function add_user_login_link() {
        ?>
        <div class='list-group-item'>
            <?php echo wp_kses(sprintf(__("Please <a href='%s'>login</a> to view the seller information.", "classified-listing-pro"), esc_url(Link::get_my_account_page_link())), ['a' => ['href' => []]]); ?>
        </div>
        <?php
    }

    /**
     * @param Listing $listing
     */
    public static function my_listing_mark_as_sold_button($listing) {
        if (is_a($listing, Listing::class) && Fns::is_enable_mark_as_sold()) {
            $is_mark_as_sold = Fns::is_mark_as_sold($listing->get_id());
            ?>
            <a data-id="<?php echo absint($listing->get_id()) ?>" href="javascript:;"
               class="btn btn-sm btn-primary mark-as-sold<?php echo $is_mark_as_sold ? " sold" : "" ?>"><?php $is_mark_as_sold ? esc_html_e("Mark as unsold", "classified-listing-pro") : esc_html_e("Mark as sold", "classified-listing-pro"); ?></a>
            <?php
        }
    }

    public static function user_verification_action() {
        global $wp;
        if (isset($wp->query_vars['verify'])) {
            UserAuthentication::verify_if_valid();
        }
    }


    public static function loop_item_listable_fields() {
        global $listing;

        $category_id = Functions::get_term_child_id_for_a_post($listing->get_categories());

        // Get custom fields
        $custom_field_ids = Functions::get_custom_field_ids($category_id);

        $fields = array();
        if (!empty($custom_field_ids)) {
            $args = array(
                'post_type'        => rtcl()->post_type_cf,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'post__in'         => $custom_field_ids,
                'orderby'          => 'menu_order',
                'order'            => 'ASC',
                'suppress_filters' => false,
                'meta_query'       => array(
                    array(
                        'key'     => '_listable',
                        'compare' => '=',
                        'value'   => 1,
                    )
                )
            );
            $args = apply_filters( 'rtcl_loop_item_listable_fields', $args, $listing );
            $fields = get_posts($args);
        }

        Functions::get_template("listing/listable-fields", array(
            'fields'     => $fields,
            'listing_id' => $listing->get_id()
        ), '', rtclPro()->get_plugin_template_path());
    }


    /**
     * @param Listing $listing
     */
    public static function add_compare_button($listing) {
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        $compare_ids = rtcl()->session->get('rtcl_compare_ids', []);
        $selected_class = '';
        if (is_array($compare_ids) && in_array($listing->get_id(), $compare_ids)) {
            $selected_class = ' selected';
        }
        ?>
        <div class="rtcl-compare rtcl-btn<?php echo esc_attr($selected_class); ?>"
             data-tooltip="<?php esc_html_e("Add to compare list", "classified-listing-pro") ?>"
             data-listing_id="<?php echo absint($listing->get_id()) ?>"><i
                    class="rtcl-icon rtcl-icon-exchange"></i></div>
        <?php
    }

    /**
     * @param Listing $listing
     */
    public static function add_quick_view_button($listing) {
        ?>
        <div class="rtcl-quick-view rtcl-btn" data-tooltip="<?php esc_html_e("Quick view", "classified-listing-pro") ?>"
             data-listing_id="<?php echo absint($listing->get_id()) ?>"><i
                    class="rtcl-icon rtcl-icon-zoom-in"></i></div>
        <?php
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_gallery($listing) {
        if (!$listing) {
            return;
        }
        $listing->the_gallery();
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_summary_title($listing) {
        if (!$listing) {
            return;
        }
        printf('<h3 class="rtcl-qv-title"><a href="%s">%s</a></h3>',
            $listing->get_the_permalink(),
            $listing->get_the_title()
        );
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_summary_label($listing) {
        if (!$listing) {
            return;
        }
        $global_listing = '';
        if (isset($GLOBALS['listing'])) {
            $global_listing = $GLOBALS['listing'];
        }
        $GLOBALS['listing'] = $listing;
        $listing->the_badges();
        if ($global_listing) {
            $GLOBALS['listing'] = $global_listing;
        } else {
            unset($GLOBALS['listing']);
        }
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_summary_meta($listing) {
        if (!$listing) {
            return;
        }
        $global_listing = '';
        if (isset($GLOBALS['listing'])) {
            $global_listing = $GLOBALS['listing'];
        }
        $GLOBALS['listing'] = $listing;
        $listing->the_meta();
        if ($global_listing) {
            $GLOBALS['listing'] = $global_listing;
        } else {
            unset($GLOBALS['listing']);
        }
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_summary_price($listing) {
        if (!$listing) {
            return;
        }
        echo sprintf('<div class="rtcl-qv-price">%s</div>', $listing->get_price_html());
    }

    /**
     * @param Listing $listing
     */
    static function quick_view_summary_custom_fields($listing) {
        if (!$listing) {
            return;
        }
        $listing->the_custom_fields();
    }

    public static function account_chat_endpoint() {
        Functions::get_template("myaccount/chat-conversation", '', null, rtclPro()->get_plugin_template_path());
    }

    /**
     * @param Filter $object
     * @param array  $data
     */
    public static function widget_filter_form_cf_item($object, $data) {
        if (!empty($data['custom_field_filter'])) {
            Functions::print_html($data['custom_field_filter'], true);
        }
    }

	/**
	 * @param Filter $object
	 */
	static function widget_filter_form_rating_item($object, $data) {
		if (!empty($data['rating_filter'])) {
			Functions::print_html($data['rating_filter'], true);
		}
	}

    /**
     * @param Listing $listing
     */
    static function listing_bump_up_badge($listing) {

        $display_option = is_singular(rtcl()->post_type) ? 'display_options_detail' : 'display_options';
        $can_show = apply_filters('rtcl_listing_can_show_top_badge', true, $listing);
        $can_show_settings = Functions::get_option_item('rtcl_moderation_settings', $display_option, 'bump_up', 'multi_checkbox');
        $can_show_settings = apply_filters('rtcl_listing_can_show_bump_up_badge_settings', $can_show_settings );
        if (!$can_show || !$can_show_settings || !get_post_meta($listing->get_id(), '_bump_up', true)) {
            return;
        }
        $label = Functions::get_option_item('rtcl_moderation_settings', 'listing_bump_up_label');
        $label = $label ?: esc_html__("Bump Up", "classified-listing-pro");
        echo '<span class="badge rtcl-badge-_bump_up">' . esc_html($label) . '</span>';
    }

    /**
     * @param Listing $listing
     */
    static function listing_top_badge($listing) {

        $display_option = is_singular(rtcl()->post_type) ? 'display_options_detail' : 'display_options';
        $can_show = apply_filters('rtcl_listing_can_show_top_badge', true, $listing);
        $can_show_settings = Functions::get_option_item('rtcl_moderation_settings', $display_option, 'top', 'multi_checkbox');
        $can_show_settings = apply_filters('rtcl_listing_can_show_top_badge_settings', $can_show_settings );
        if (!$can_show || !$can_show_settings || !get_post_meta($listing->get_id(), '_top', true)) {
            return;
        }

        $label = Functions::get_option_item('rtcl_moderation_settings', 'listing_top_label');
        $label = $label ?: esc_html__("Top", "classified-listing-pro");
        echo '<span class="badge rtcl-badge-_top">' . esc_html($label) . '</span>';
    }

    /**
     * @param Listing $listing
     */
    static function listing_popular_badge($listing) {

        $views = absint(get_post_meta($listing->get_id(), '_views', true));
        $popular_threshold = Functions::get_option_item('rtcl_moderation_settings', 'popular_listing_threshold', 0, 'number');
        if ($views >= $popular_threshold) {
            $popular_label = Functions::get_option_item('rtcl_moderation_settings', 'popular_listing_label');
            $popular_label = $popular_label ?: esc_html__("Popular", "classified-listing-pro");
            echo '<span class="badge rtcl-badge-popular popular-badge badge-success">' . esc_html($popular_label) . '</span>';
        }
    }

    /**
     * @param null| WP_Query $query
     */
    static function top_listing_items($query = null) {
        $query = !empty($query) && is_a($query, WP_Query::class) ? $query : Fns::top_listings_query();

        $paginated = !$query->get('no_found_rows');
        $listings = (object)array(
            'total'        => $paginated ? (int)$query->found_posts : count($query->posts),
            'total_pages'  => $paginated ? (int)$query->max_num_pages : 1,
            'per_page'     => (int)$query->get('posts_per_page'),
            'current_page' => $paginated ? (int)max(1, $query->get('paged', 1)) : 1,
        );
        Functions::setup_loop(
            [
                'is_shortcode' => true,
                'is_search'    => false,
                'is_paginated' => false,
                'as_top'       => true,
                'total'        => $listings->total,
                'total_pages'  => $listings->total_pages,
                'per_page'     => $listings->per_page,
                'current_page' => $listings->current_page
            ]
        );
        if (Functions::get_loop_prop('total')) {
            while ($query->have_posts()) : $query->the_post();
                Functions::get_template_part('content', 'listing');
            endwhile;
            wp_reset_postdata();
        }

        Functions::reset_loop();
    }


    /**
     * Output the Listing view switcher
     */
    static function view_switcher() {
        $views = Options::get_listings_view_options();
        $default_view = Functions::get_option_item('rtcl_general_settings', 'default_view', 'list');
        $current_view = (!empty($_GET['view']) && array_key_exists($_GET['view'], $views)) ? $_GET['view'] : $default_view;
        Functions::get_template(
            'listing/view-switcher',
            compact('views', 'current_view', 'default_view'),
            '',
            rtclPro()->get_plugin_template_path()
        );
    }

    public static function add_chat_link($listing) {
        if (Fns::is_enable_chat() && is_a($listing, Listing::class) && ((is_user_logged_in() && $listing->get_author_id() !== get_current_user_id()) || !is_user_logged_in())) {
            $chat_btn_class = ['rtcl-chat-link'];
            $chat_url = Link::get_my_account_page_link();
            if (is_user_logged_in()) {
                $chat_url = '#';
                array_push($chat_btn_class, 'rtcl-contact-seller');
            } else {
                array_push($chat_btn_class, 'rtcl-no-contact-seller');
            }
            ?>
            <div class='rtcl-contact-seller list-group-item'>
                <a class="<?php echo esc_attr(implode(' ', $chat_btn_class)) ?>"
                   href="<?php echo esc_url($chat_url) ?>" data-listing_id="<?php echo absint($listing->get_id()) ?>">
                    <i class='rtcl-icon rtcl-icon-chat mr-1'> </i><?php esc_html_e("Chat", "classified-listing-pro") ?>
                </a>
            </div>
        <?php }
    }


    static function sold_out_banner() {
        global $listing;
        if ($listing && Fns::is_enable_mark_as_sold() && Fns::is_mark_as_sold($listing->get_id())) {
            echo '<span class="rtcl-sold-out">' . apply_filters('rtcl_sold_out_banner_text', esc_html__("Bán hết", 'classified-listing-pro')) . '</span>';
        }
    }

    /**
     * Display the review authors gravatar
     *
     * @param array $comment \WP_Comment.
     *
     * @return void
     */
    public static function review_display_gravatar($comment) {
        $gravatar = get_avatar($comment, apply_filters('rtcl_review_gravatar_size', '60'));
        echo apply_filters( 'rtcl_review_gravatar_image', $gravatar ); // Hook Added By Rashid
    }


    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    public static function review_display_meta() {
        Functions::get_template('listing/review-meta', [], '', rtclPro()->get_plugin_template_path());
    }


    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    public static function review_display_rating() {
        if (post_type_supports(rtcl()->post_type, 'comments')) {
            Functions::get_template('listing/review-rating', [], '', rtclPro()->get_plugin_template_path());
        }
    }


    /**
     * Display the review content.
     */
    public static function review_display_comment_title($comment) {
        echo '<span class="rtcl-review__title">';
        echo esc_html(get_comment_meta($comment->comment_ID, 'title', true));
        echo '</span>';
    }


    /**
     * Display the review content.
     */
    public static function review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}
