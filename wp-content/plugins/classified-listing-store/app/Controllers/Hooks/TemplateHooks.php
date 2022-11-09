<?php

namespace RtclStore\Controllers\Hooks;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Functions as RtclFunctions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Shortcodes\Checkout;
use RtclPro\Helpers\Fns;
use RtclStore\Helpers\Functions as StoreFunctions;
use RtclStore\Models\Membership;
use RtclStore\Models\Store;
use RtclStore\Resources\Options as StoreOptions;
use WP_User;

class TemplateHooks
{
    static function init() {
        add_filter('rtcl_is_rtcl', [__CLASS__, 'store_is_rtcl']);
        add_filter('post_class', [__CLASS__, 'store_post_class'], 20, 3);
        add_action('the_content', [__CLASS__, 'store_content_restriction']);

        add_action('rtcl_store_sidebar', array(__CLASS__, 'get_store_sidebar'), 10);

        add_action('rtcl_single_store_information', [__CLASS__, 'store_hours'], 10);
        if (!Fns::registered_user_only('store_contact') || Fns::registered_user_only('store_contact') && is_user_logged_in()) {
            add_action('rtcl_single_store_information', [__CLASS__, 'store_address'], 20);
            add_action('rtcl_single_store_information', [__CLASS__, 'store_phone'], 30);
            add_action('rtcl_single_store_information', [__CLASS__, 'store_social_media'], 40);
            add_action('rtcl_single_store_information', [__CLASS__, 'store_social_email'], 50);
        } else {
            add_action('rtcl_single_store_information', [__CLASS__, 'add_user_login_link'], 20);
        }
        add_action('rtcl_single_store_detail_modal', [__CLASS__, 'store_detail_modal']);

        add_action('rtcl_single_store_expired_content', [__CLASS__, 'single_store_expired_content']);


        // Store loop
        add_action('rtcl_before_store_loop', [__CLASS__, 'store_actions'], 20);
        add_action('rtcl_store_loop_action', [__CLASS__, 'result_count'], 10);
        add_action('rtcl_store_loop_action', [__CLASS__, 'catalog_ordering'], 20);
        add_action('rtcl_no_stores_found', [__CLASS__, 'no_stores_found']);

        add_action('rtcl_before_store_loop_item', [__CLASS__, 'open_store_link'], 10);
        add_action('rtcl_store_loop_item_thumbnail', [__CLASS__, 'store_thumbnail']);

        add_action('rtcl_store_loop_item', [__CLASS__, 'loop_item_content_start'], 5);
        add_action('rtcl_store_loop_item', [__CLASS__, 'loop_item_store_title'], 10);
        add_action('rtcl_store_loop_item', [__CLASS__, 'store_meta'], 20);
        add_action('rtcl_store_loop_item', [__CLASS__, 'loop_item_content_end'], 100);

        add_action('rtcl_after_store_loop_item', [__CLASS__, 'close_store_link'], 5);

        add_action('rtcl_after_store_loop', [__CLASS__, 'pagination'], 10);

        add_action('rtcl_checkout_form_start', [__CLASS__, 'add_membership_pricing_options_at_checkout_form'], 10);
        if (StoreFunctions::is_enable_store_manager()) {
            add_action('rtcl_account_dashboard', [__CLASS__, 'store_manager_action'], 40);
        }
        if (StoreFunctions::is_membership_enabled()) {
            add_action('rtcl_account_dashboard', [__CLASS__, 'membership_statistic_report'], 50);
        }
        add_action('rtcl_checkout_membership_endpoint', [__CLASS__, 'membership_endpoint_content'], 10, 2);
        add_action('rtcl_account_store_endpoint', [__CLASS__, 'account_store_endpoint']);
        add_action('rtcl_add_user_information', [__CLASS__, 'add_store_link_to_user_information']);

        add_action('rtcl_before_checkout_form', [__CLASS__, 'add_membership_promotions_heading'], 20, 2);
        add_action('rtcl_before_checkout_form', [__CLASS__, 'add_membership_promotions_from'], 30, 2);
        add_action('rtcl_before_checkout_form', [__CLASS__, 'add_checkout_from_heading'], 40, 2);
        add_action('rtcl_membership_promotion_form', [__CLASS__, 'add_membership_promotions'], 10, 3);
        add_action('rtcl_membership_promotion_form_submit_button', [__CLASS__, 'membership_promotion_form_submit_button'], 10);
        add_action('rtcl_membership_promotion_form_end', [__CLASS__, 'add_membership_promotions_hidden_field'], 50);

        add_action('rtcl_store_contact_form', [__CLASS__, 'g_recaptcha_v2_placeholder']);
    }

    public static function g_recaptcha_v2_placeholder() {
        $misc_settings = Functions::get_option('rtcl_misc_settings');
        if (!empty($misc_settings['recaptcha_site_key']) && !empty($misc_settings['recaptcha_forms']) && in_array('store_contact', $misc_settings['recaptcha_forms']) && (!empty($misc_settings['recaptcha_version']) && $misc_settings['recaptcha_version'] !== 3))
            ?>
            <div class="form-group">
                <div class="rtcl-g-recaptcha-wrap"></div>
            </div>
            <?php
    }

    public static function add_user_login_link() {
        ?>
        <div class='list-group-item'>
            <?php echo wp_kses(sprintf(__("Please <a href='%s'>login</a> to view the store contact.", "classified-listing-pro"), esc_url(Link::get_my_account_page_link())), ['a' => ['href' => []]]); ?>
        </div>
        <?php
    }

    static function loop_item_content_start() {
        echo apply_filters('rtcl_store_loop_item_content_start', '<div class="item-content">');
    }

    static function store_meta() {
        global $store;
        $store->the_metas();
    }

    static function loop_item_store_title() {
        global $store;
        if (is_a($store, Store::class)) {
            echo '<h3 class="rtcl-store-title">' . $store->get_the_title() . '</h3>';
        }
    }

    static function loop_item_content_end() {
        echo apply_filters('rtcl_store_loop_item_content_end', '</div>');
    }

    static function open_store_link() {
        global $store;
        if (is_a($store, Store::class)) {
            echo sprintf('<a href="%s" class="rtcl-store-link">', $store->get_the_permalink());
        }
    }

    static function close_store_link() {
        echo '</a>';
    }

    static function store_thumbnail() {
        RtclFunctions::get_template('store/loop/thumbnail', '', '', rtclStore()->get_plugin_template_path());
    }

    static function pagination() {
        StoreFunctions::pagination();
    }

    static function store_actions() {
        RtclFunctions::get_template('store/loop/actions', null, '', rtclStore()->get_plugin_template_path());
    }

    /**
     * Output the result count text (Showing x - x of x results).
     */
    static function result_count() {
        if (!StoreFunctions::get_loop_prop('is_paginated')) {
            return;
        }
        $args = array(
            'total'    => StoreFunctions::get_loop_prop('total'),
            'per_page' => StoreFunctions::get_loop_prop('per_page'),
            'current'  => StoreFunctions::get_loop_prop('current_page'),
        );

        RtclFunctions::get_template('listing/loop/result-count', $args);
    }


    static function no_stores_found() {
        RtclFunctions::get_template('store/loop/no-stores-found', null, '', rtclStore()->get_plugin_template_path());
    }

    /**
     * Output the Listing sorting options.
     */
    static function catalog_ordering() {
        if (!StoreFunctions::get_loop_prop('is_paginated')) {
            return;
        }
        $orderby = RtclFunctions::get_option_item('rtcl_general_settings', 'orderby');
        $order = RtclFunctions::get_option_item('rtcl_general_settings', 'order');
        $orderby_order = $orderby . "-" . $order;
        $is_orderby_selected = 'date-desc' === apply_filters('rtcl_store_default_catalog_orderby', $orderby_order);
        $catalog_orderby_options = StoreOptions::get_store_orderby_options();

        $default_orderby = StoreFunctions::get_loop_prop('is_search') ? 'relevance' : $orderby_order;
        $orderby = isset($_GET['orderby']) ? RtclFunctions::clean(wp_unslash($_GET['orderby'])) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

        if (StoreFunctions::get_loop_prop('is_search')) {
            $catalog_orderby_options = array_merge(array('relevance' => esc_html__('Relevance', 'classified-listing-store')), $catalog_orderby_options);

            unset($catalog_orderby_options['menu_order']);
        }

        if (!$is_orderby_selected) {
            unset($catalog_orderby_options['date-desc']);
        }

        if (!array_key_exists($orderby, $catalog_orderby_options)) {
            $orderby = current(array_keys($catalog_orderby_options));
        }

        RtclFunctions::get_template(
            'listing/loop/orderby',
            array(
                'catalog_orderby_options' => $catalog_orderby_options,
                'orderby'                 => $orderby,
                'is_orderby_selected'     => $is_orderby_selected,
            )
        );
    }

    static function single_store_expired_content() {
        ?>
        <div class="rtcl store-content-wrap">
            <p><?php _e('This store is unavailable due to membership is expired for this store owner.', 'classified-listing-store') ?></p>
        </div>
        <?php
    }

    static function store_detail_modal() {
        RtclFunctions::get_template('store/details-modal', null, '', rtclStore()->get_plugin_template_path());
    }

    static function store_social_email( $store = false ) {
        if (!$store) global $store;
        
        if ($store_email = $store->get_email()): ?>
            <div class="store-info-item store-email">
                <div class="store-email-label">
                    <span class="icon"><span class="rtcl-icon-mail"></span></span>
                    <span class="text"><?php echo apply_filters('rtcl_store_single_store_email_button_text', esc_html__("Message Store Owner", "classified-listing-store")); ?></span>
                </div>
                <?php RtclFunctions::get_template('store/contact-form', null, '', rtclStore()->get_plugin_template_path()); ?>
            </div>
        <?php endif;
    }

    static function store_social_media( $store = false ) {
        if (!$store) global $store;
        
        if ($store->get_social_media()): ?>
            <div class="store-info-item store-social-media">
                <?php  echo wp_kses_post($store->get_social_media_html()) ?>
            </div>
        <?php endif;
    }

    static function store_phone( $store = false ) {
        if (!$store) global $store;
        
        if ($store_phone = $store->get_phone()):
            $safe_phone = substr_replace($store_phone, 'XXX', -3);
            $phone_options = [
                'safe_phone'   => $safe_phone,
                'phone_hidden' => substr($store_phone, -3)
            ];
            ?>
            <div class="store-info-item store-phone reveal-phone"
                 data-options="<?php echo htmlspecialchars(wp_json_encode($phone_options)); ?>">
                <div class="icon"><span class="rtcl-icon-mobile" aria-hidden="true"></span></div>
                <div class="text">
                    <div class='numbers'><?php echo esc_html($safe_phone); ?></div>
                    <small class='text-muted'><?php esc_html_e("Click to reveal phone number",
                            "classified-listing-store") ?></small>
                </div>
            </div>
        <?php endif;
    }

    static function store_address( $store = false ) {
        if (!$store) global $store;
        
        if ($store_address = $store->get_address()): ?>
            <div class="store-info-item store-address">
                <div class="icon"><span class="rtcl-icon-location" aria-hidden="true"></span></div>
                <div class="text"><?php echo esc_html($store_address); ?></div>
            </div>
        <?php endif;
    }

    static function store_hours( $store = false ) {
        if (!$store) global $store;
        $store_oh_type = get_post_meta($store->get_id(), 'oh_type', true);
        $oh_hours = get_post_meta($store->get_id(), 'oh_hours', true);
        $oh_hours = is_array($oh_hours) ? $oh_hours : ($oh_hours ? (array)$oh_hours : []);
        $today = strtolower(date('l'));
        $oh_current_hour = array();
        $now_status = '';
        $now_open = false;
        if ($store_oh_type == 'selected' && !empty($oh_hours) && isset($oh_hours[$today]['active'])) {
            $oh_current_hour = $oh_hours[$today];
            $now_status = esc_html__("Close now", "classified-listing-store");
            $local = get_date_from_gmt(date("Y-m-d H:i:s"));
            $now = \DateTime::createFromFormat('Y-m-d H:i:s', $local);
            $store_time_format = StoreFunctions::get_store_time_format();
            try {
                $date_open = new \DateTime(isset($oh_hours[$today]['open']) ? $oh_hours[$today]['open'] : '9:00 AM');
            } catch (\Exception $e) {
                $date_open = new \DateTime('9:00 AM');
            }
            try {
                $date_close = new \DateTime(isset($oh_hours[$today]['close']) ? $oh_hours[$today]['close'] : '9:00 PM');
            } catch (\Exception $e) {
                $date_open = new \DateTime('9:00 PM');
            }

            $oh_current_hour['open'] = $date_open->format($store_time_format);
            $oh_current_hour['close'] = $date_close->format($store_time_format);
            $date_close->add(new \DateInterval('PT1M'));
            if ($now >= $date_open && $now <= $date_close) {
                $now_status = esc_html__("Open now", "classified-listing-store");
                $now_open = true;
            }

        }
        ?>
        <div class="store-info-item store-hour">
            <div class="icon"><span class="rtcl-icon-clock"></span></div>
            <div class="text">
                <?php if ($store_oh_type == 'selected'): ?>
                    <?php if (!empty($oh_current_hour)): ?>
                        <span class="open-day">
                                    <?php $now_open_class = $now_open ? ' store-open' : ' store-close'; ?>
                                    <span class="store-now<?php echo esc_attr($now_open_class); ?>"><?php echo esc_html($now_status); ?></span>
                                    <span class="label"><?php esc_html_e("Open Today:", "classified-listing-store") ?></span>
                                    <span class="hours">
                                        <span class="open-hour"><?php echo isset($oh_current_hour['open']) ? esc_html($oh_current_hour['open']) : ''; ?></span>
                                        <span class="close-hour"><?php echo isset($oh_current_hour['close']) ? esc_html($oh_current_hour['close']) : ''; ?></span>
                                    </span>
                                </span>
                    <?php else: ?>
                        <span class="close-day"><?php esc_html_e("Close Today", "classified-listing-store") ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="open-day always">
                        <span class="label"><?php esc_html_e("Always Open", "classified-listing-store") ?></span>
                    </span>
                <?php endif; ?>
                <?php do_action( 'rtcl_after_store_hours_text' ); ?>
            </div>
        </div>
        <?php
    }

    static function store_content_restriction($content) {
        if (is_singular(rtclStore()->post_type) && rtclStore()->post_type === get_post_type() && in_the_loop() && is_main_query() && StoreFunctions::is_store_expired()) {
            ob_start();
            do_action('rtcl_single_store_expired_content');
            return ob_get_clean();
        }

        return $content;

    }

    static function store_is_rtcl($rtcl) {
        if (StoreFunctions::is_store() || StoreFunctions::is_single_store() || StoreFunctions::is_store_taxonomy()) {
            return true;
        }
        return $rtcl;

    }


    /**
     * Adds extra post classes for listings via the WordPress post_class hook, if used.
     *
     * Note: For performance reasons we instead recommend using listing_class/get_listing_class instead.
     *
     * @param array        $classes Current classes.
     * @param string|array $class   Additional class.
     * @param int          $post_id Post ID.
     *
     * @return array
     * @since 1.3.21
     */
    static function store_post_class($classes, $class = '', $post_id = 0) {
        if (!$post_id || rtclStore()->post_type !== get_post_type($post_id)) {
            return $classes;
        }

        $store = rtclStore()->factory->get_store($post_id);

        if (!$store) {
            return $classes;
        }
        $classes[] = 'rtcl-store-item';

        return $classes;
    }

    static function get_store_sidebar() {
        RtclFunctions::get_template('store/sidebar', null, '', rtclStore()->get_plugin_template_path());
    }


    public static function add_membership_pricing_options_at_checkout_form($type) {
        if ('membership' === $type) {
            $pricing_options = get_posts(apply_filters('rtcl_store_membership_pricing_query_args', [
                'post_type'        => rtcl()->post_type_pricing,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'orderby'          => 'menu_order',
                'order'            => 'ASC',
                'no_found_rows'    => true,
                'meta_query'       => [
                    [
                        'key'   => 'pricing_type',
                        'value' => 'membership',
                    ]
                ],
                'suppress_filters' => false
            ]));
            Functions::get_template("checkout/membership", array(
                'pricing_options' => $pricing_options
            ), '', rtclStore()->get_plugin_template_path());
        }
    }

    /**
     * @param WP_User $current_user
     */
    public static function store_manager_action($current_user) {
        if (is_a($current_user, WP_User::class) && $store = StoreFunctions::get_manager_store($current_user->ID)) {
            Functions::get_template('myaccount/store-manager-action', compact('store', 'current_user'), '', rtclStore()->get_plugin_template_path());
        }

    }

    public static function membership_statistic_report($current_user) {
        Functions::get_template('myaccount/membership-statistic', compact('current_user'), '', rtclStore()->get_plugin_template_path());
    }


    static function membership_endpoint_content($type, $value) {
        Checkout::checkout_form($type, $value);
    }

    public static function account_store_endpoint() {

        if (RtclFunctions::get_option_item('rtcl_membership_settings', 'enable_store_only_membership', false, 'checkbox')) {
            $member = rtclStore()->factory->get_membership();
            if (!$member || !$member->has_membership() || $member->is_expired()) {
                $denied_message = sprintf('<p class="rtcl-store-access-denied">' . __("You have no membership to access this page. You can buy a membership from <a href='%s'>here</a>.", "classified-listing-store") . '</p>', Link::get_checkout_endpoint_url('membership'));
                echo apply_filters('rtcl_store_access_denied_message', $denied_message);
                return;
            }
        }

        $getStore = get_posts(array(
            'post_type'        => rtclStore()->post_type,
            'posts_per_page'   => 1,
            'post_status'      => 'publish',
            'suppress_filters' => false,
            'meta_query'       => array(
                array(
                    'key'     => 'store_owner_id',
                    'value'   => get_current_user_id(),
                    'type'    => 'numeric',
                    'compare' => '=',
                )
            )
        ));
        $store = null;
        if (!empty($getStore[0])) {
            $store = new Store($getStore[0]);
        }

        // Process output
        Functions::get_template("myaccount/store", compact('store'), '', rtclStore()->get_plugin_template_path());
    }

    public static function add_store_link_to_user_information($listing_id) {
        $listing = rtcl()->factory->get_listing($listing_id);
        if ($store = StoreFunctions::get_user_store($listing->get_owner_id())) {
            Functions::get_template('store/store-link-to-user-information', compact('listing_id', 'store'), '', rtclStore()->get_plugin_template_path());
        }
    }

    /**
     * @param int        $listing_id
     * @param array      $promotions
     * @param Membership $membership
     */
    public static function add_membership_promotions($listing_id, $promotions, $membership) {
        Functions::get_template('checkout/membership-promotions', compact('membership', 'promotions', 'listing_id'), '', rtclStore()->get_plugin_template_path());
    }

    public static function add_membership_promotions_heading($type, $listing_id) {
        if ("submission" === $type && $listing_id) {
            $membership = rtclStore()->factory->get_membership();
            if ($membership && !$membership->is_expired() && !empty($promotions = $membership->get_promotions())) {
                echo '<h4 id="rtcl-membership-promotions-heading" class="rtcl-promotions-heading active" data-target=".rtcl-membership-promotions-form-wrap"><span>' . apply_filters('rtcl_checkout_membership_promotion_title', __("Membership Promotions", "classified-listing-store")) . '</span></h4>';
            }
        }
    }

    public static function add_checkout_from_heading($type, $listing_id) {
        if ("submission" === $type && $listing_id) {
            $membership = rtclStore()->factory->get_membership();
            if ($membership && !$membership->is_expired() && !empty($promotions = $membership->get_promotions())) {
                echo '<h4 id="rtcl-regular-promotions-heading" class="rtcl-promotions-heading" data-target="#rtcl-woo-checkout-form"><span>' . apply_filters('rtcl_checkout_regular_promotion_title', __("Regular Promotions", "classified-listing-store")) . '</span></h4>';
            }
        }
    }

    public static function add_membership_promotions_from($type, $listing_id) {
        if ("submission" === $type && $listing_id) {
            $membership = rtclStore()->factory->get_membership();
            if ($membership && !$membership->is_expired() && !empty($promotions = $membership->get_promotions())) {
                Functions::get_template('checkout/membership-promotions-form', compact('membership', 'promotions', 'listing_id'), '', rtclStore()->get_plugin_template_path());
            }
        }
    }

    static function membership_promotion_form_submit_button() {
        ?>
        <div class="rtcl-membership-promotion-actions">
            <a class="btn btn-primary"
               href="<?php echo esc_url(Link::get_my_account_page_link()) ?>"><?php echo apply_filters('rtcl_checkout_myaccount_btn_text', __( 'Go to My Account', 'classified-listing-store')); ?></a>
            <button type="submit" id="rtcl-membership-promotion-action" name="_rtcl_membership_promotions_submit"
                    class="btn btn-primary"
                    value="1"><?php echo apply_filters('rtcl_checkout_promotion_btn_text', __( 'Proceed to promotion', 'classified-listing-store')); ?></button>
        </div>
        <?php
    }

    /**
     * @param int $listing_id
     */
    public static function add_membership_promotions_hidden_field($listing_id) {
        wp_nonce_field('rtcl_membership_promotions', 'rtcl_membership_promotions_nonce');
        printf('<input type="hidden" name="listing_id" value="%d"/>', absint($listing_id));
    }
}
