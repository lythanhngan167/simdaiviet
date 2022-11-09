<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use Rtcl\Models\Pricing;
use Rtcl\Resources\Options;
use RtclStore\Helpers\Functions as StoreFunctions;

class RtclApplyHook
{

    static function init() {
        add_filter('rtcl_register_settings_tabs', [__CLASS__, 'add_membership_tab_item_at_settings_tabs_list']); // Admin Hook
        add_filter('rtcl_settings_option_fields', [__CLASS__, 'add_membership_tab_options'], 10, 2);

        self::register_store_menu();
        add_filter('rtcl_locate_template', [__CLASS__, 'locate_store_template'], 20, 2);
        add_filter('rtcl_get_template_part', [__CLASS__, 'get_template_part'], 20, 3);
        add_filter('rtcl_misc_settings_options', [__CLASS__, 'add_misc_options']);
        add_filter('rtcl_all_ids_for_remove_attachment', [__CLASS__, 'remove_attachment_for_store']);

        // For single page
        // add_filter('rtcl_current_user_can', array(__CLASS__, 'current_user_can'), 10, 3);

        add_filter('rtcl_checkout_endpoints', [__CLASS__, 'add_checkout_membership_endpoint']);
        add_filter('rtcl_payment_pricing_types', [__CLASS__, 'add_payment_pricing_type']);
        add_filter('rtcl_pricing_admin_options', [__CLASS__, 'add_pricing_options'], 10, 2);
        add_action('save_post_' . rtcl()->post_type_pricing, [__CLASS__, 'save_pricing_meta'], 20, 2);
        //add_filter('rtcl_payment_receipt_html', [__CLASS__, 'rtcl_payment_receipt_html'], 10, 2);
        remove_action('rtcl_payment_receipt', [\Rtcl\Controllers\Hooks\TemplateHooks::class, 'payment_receipt_pricing_info'], 20);
        add_action('rtcl_payment_receipt', [__CLASS__, 'payment_receipt_pricing_info'], 20, 2);
        add_action('rtcl_get_payment_option_features', [__CLASS__, 'rtcl_get_payment_option_features'], 10, 2);
        add_filter('rtcl_get_admin_email_notification_options', [__CLASS__, 'add_store_update_admin_email_notification']);

        add_filter('rtcl_recaptcha_form_list', [__CLASS__, 'add_recaptcha_store_contact_form']);
        add_action('rtcl_before_enqueue_script', [__CLASS__, 'add_recaptcha_script_store_contact_form']);

        add_filter('rtcl_public_inline_style', [__CLASS__, 'add_public_style'], 10, 2);
        add_filter('rtcl_custom_pages_list', [__CLASS__, 'add_custom_page']);

        add_action('rtcl_pricing_promotions_column_content', [__CLASS__, 'add_membership_pricing_promotions'], 20);
        add_action('rtcl_payment_promotions_content', [__CLASS__, 'add_membership_payment_promotions'], 20, 2);
        add_action('rtcl_payment_item_details', [__CLASS__, 'add_payment_membership_item_details'], 10, 2);

        add_filter('rtcl_registered_only_options', [__CLASS__, 'add_registered_only_store_contact_option']);

        if (StoreFunctions::is_enable_store_manager()) {
            add_filter('rtcl_current_user_can', [__CLASS__, 'manager_can_manage'], 100, 3);
            add_filter('rtcl_listing_save_update_args', [__CLASS__, 'update_store_id_as_owner_for_manager_post'], 100, 2);
            add_filter('rtcl_insert_temp_post_for_gallery', [__CLASS__, 'update_gallery_temp_post_id_as_owner_for_manager_post']);
            add_filter('rtcl_listing_post_user_id', [__CLASS__, 'rtcl_listing_manager_post_user_id']);
            add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'add_manager_id_after_save_new_listing'], 100, 2);
            add_action('rtcl_listing_loop_extra_meta', [__CLASS__, 'add_listing_managed_by']);
            add_filter('rtcl_my_listings_args', [__CLASS__, 'add_store_manager_listing_args']);
            add_action('rtcl_my_account_before_my_listing', [__CLASS__, 'add_manager_store_listing_sub_menu'], 10);
            add_action('rtcl_my_account_before_my_listing', [__CLASS__, 'add_store_manager_listing_for'], 50);
        }

    }

    /**
     * @param array $options
     *
     * @return array mixed
     */
    public static function add_registered_only_store_contact_option($options) {
        $options['store_contact'] = esc_html__('Store contact information', 'classified-listing-store');
        return $options;
    }

    public static function add_store_manager_listing_for() {
        if (isset($_GET['manager']) && ($current_user_store = StoreFunctions::get_current_user_store()) && ($manager = get_user_by('login', sanitize_text_field($_GET['manager']))) && $current_user_store->owner_id() !== $manager->ID && in_array($manager->ID, $current_user_store->get_manager_ids(), true)) {
            $name = trim(implode(' ', [$manager->first_name, $manager->last_name]));
            $name = $name ? $name : $manager->display_name;
            $pp_id = absint(get_user_meta($manager->ID, '_rtcl_pp_id', true));
            ?>
            <div class="rtcl-store-manager-listing">
                <h5><?php esc_html_e("Store manager listing:"); ?></h5>
                <div class="rtcl-store-manager">
                    <div class="rtcl-store-m-avatar"><?php echo $pp_id ? wp_get_attachment_image($pp_id, [100, 100]) : get_avatar($manager->ID) ?></div>
                    <div class="rtcl-store-m-info">
                        <div class="rtcl-m-info"><?php esc_html_e($name) ?></div>
                        <div class="rtcl-m-info"><?php echo esc_html($manager->user_email); ?></div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public static function add_manager_store_listing_sub_menu() {
        if ($store = StoreFunctions::get_manager_store()) {
            ?>
            <div class="rtcl-account-sub-menu">
                <ul>
                    <li class="rtcl-my-listing<?php echo !isset($_GET['store_listing']) ? ' active' : ''; ?>">
                        <a href="<?php echo esc_url(Link::get_account_endpoint_url('listings')) ?>"><?php esc_html_e("My Listing", "classified-listing-store"); ?></a>
                    </li>
                    <li class="rtcl-store-listing<?php echo isset($_GET['store_listing']) ? ' active' : ''; ?>">
                        <a href="<?php echo esc_url(add_query_arg('store_listing', 1, Link::get_account_endpoint_url('listings'))) ?>"><?php esc_html_e("Listing for store", "classified-listing-store"); ?></a>
                    </li>
                </ul>
            </div>
            <?php
        }
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public static function add_store_manager_listing_args($args) {
        $current_user_store = StoreFunctions::get_current_user_store();
        $managerStore = StoreFunctions::get_manager_store();
        if (isset($_GET['manager']) && $current_user_store && ($manager = get_user_by('login', sanitize_text_field($_GET['manager']))) && $current_user_store->owner_id() !== $manager->ID && in_array($manager->ID, $current_user_store->get_manager_ids(), true)) {
            $meta_query = [
                'key'   => '_rtcl_manager_id',
                'value' => $manager->ID
            ];

            if (isset($args['meta_query'])) {
                $args['meta_query'][] = $meta_query;
            } else {
                $args['meta_query'][] = $meta_query;
            }

            if (count($args['meta_query']) > 1) {
                $args['meta_query']['relation'] = 'AND';
            }
        }
        if (isset($_GET['store_listing']) && $managerStore) {
            $args['author'] = $managerStore->owner_id();
            $meta_query = [
                'key'   => '_rtcl_manager_id',
                'value' => get_current_user_id()
            ];

            if (isset($args['meta_query'])) {
                $args['meta_query'][] = $meta_query;
            } else {
                $args['meta_query'][] = $meta_query;
            }

            if (count($args['meta_query']) > 1) {
                $args['meta_query']['relation'] = 'AND';
            }
        }
        return $args;
    }

    /**
     * @param Listing $listing
     */
    public static function add_listing_managed_by($listing) {
        $manager_id = absint(get_post_meta($listing->get_id(), '_rtcl_manager_id', true));
        if ($manager_id && is_a($listing, Listing::class) && $listing->get_owner_id() !== $manager_id && $user = get_user_by('id', $manager_id)) {
            $author[] = $user->first_name;
            $author[] = $user->last_name;
            $author = array_filter($author);
            if (!empty($author)) {
                $author_name = implode(' ', $author);
            } else {
                $author_name = $user->display_name;
            }
            ?>
            <div class="rtcl-managed-by">
                <strong><?php esc_html_e('Managed by', 'classified-listing-store'); ?></strong>:
                <?php echo esc_html($author_name); ?>
            </div>
            <?php
        }
    }

    /**
     * @param Listing $listing
     * @param         $type
     */
    public static function add_manager_id_after_save_new_listing($listing, $type) {
        if ($type === "new" && is_a($listing, Listing::class) && $listing->get_owner_id() !== get_current_user_id()) {
            update_post_meta($listing->get_id(), '_rtcl_manager_id', get_current_user_id());
        }
    }

    /**
     * @param array  $post_arg
     * @param string $type
     *
     * @return array
     */
    public static function update_store_id_as_owner_for_manager_post($post_arg, $type) {
        $current_user_id = get_current_user_id();
        if ($type === 'new' && $current_user_id && isset($post_arg['post_author']) && absint($post_arg['post_author']) && ($store = StoreFunctions::get_manager_store($current_user_id)) && $post_arg['post_author'] !== $store->owner_id()) {
            $post_arg['post_author'] = $store->owner_id();
        }

        return $post_arg;
    }

    public static function rtcl_listing_manager_post_user_id( $current_user_id ) {
	    if ($current_user_id && ($store = StoreFunctions::get_manager_store($current_user_id)) && $current_user_id !== $store->owner_id()) {
		    $current_user_id = $store->owner_id();
	    }

	    return $current_user_id;
    }

    public static function update_gallery_temp_post_id_as_owner_for_manager_post( $post_arg ) {
	    $current_user_id = get_current_user_id();
	    if ($current_user_id && isset($post_arg['post_author']) && absint($post_arg['post_author']) && ($store = StoreFunctions::get_manager_store($current_user_id)) && $post_arg['post_author'] !== $store->owner_id()) {
		    $post_arg['post_author'] = $store->owner_id();
	    }
	    return $post_arg;
    }

    /**
     * @param bool    $user_can
     * @param array   $capability
     * @param Listing $listing
     *
     * @return mixed
     */
    public static function manager_can_manage($user_can, $capability, $listing) {
        if ($user_can || !is_a($listing, Listing::class)) {
            return $user_can;
        }
        $manager_id = absint(get_post_meta($listing->get_id(), '_rtcl_manager_id', true));
        if ($manager_id && $manager_id === get_current_user_id() && in_array($capability, ['edit_rtcl_listing', 'delete_rtcl_listing'])) {
            $user_can = true;
        }
        return $user_can;
    }

    /**
     * @param int     $payment_id
     * @param Payment $payment
     */
    public static function add_payment_membership_item_details($payment_id, $payment) {
        if ($payment && !empty($payment->pricing) && "membership" === $payment->pricing->getType()) { ?>
            <div class="item-title">
                <a href="<?php echo get_the_permalink($payment_id); ?>"><?php echo get_the_title($payment_id); ?></a>
            </div>
            <?php
        }
    }

    /**
     * @param int     $payment_id
     * @param Payment $payment
     */
    public static function add_membership_payment_promotions($payment_id, $payment) {
        if ($payment && !empty($payment->pricing) && "membership" === $payment->pricing->getType()) {
            $promotions = get_post_meta($payment->get_id(), '_rtcl_membership_promotions', true);
            $regular_ads = get_post_meta($payment->pricing->getId(), 'regular_ads', true);
            echo '<div class="membership-promotions rtcl-pricing-promotions">';
            echo '<div class="item"><span class="item-label"></span><span class="listing-count">Ads</span></span><span class="validate">Validate</span></div>';
            echo sprintf('<div class="item"><span class="item-label">%s:</span><span class="listing-count">%d</span><span class="validate">%s</span></div>',
                __("Regular", "classified-listing-store"),
                absint($regular_ads),
                __("--", "classified-listing-store")
            );
            if (!empty($promotions)) {
                $promotion_list = Options::get_listing_promotions();
                foreach ($promotion_list as $promotion_key => $promotion_label) {
                    echo sprintf('<div class="item"><span class="item-label">%s:</span><span class="listing-count">%d</span><span class="validate">%d</span></div>',
                        $promotion_label,
                        !empty($promotions[$promotion_key]['ads']) ? $promotions[$promotion_key]['ads'] : 0,
                        !empty($promotions[$promotion_key]['validate']) ? $promotions[$promotion_key]['validate'] : 0
                    );
                }
            }
            echo "</div>";
        }
    }

    public static function add_membership_pricing_promotions($pricing_id) {
        $pricing_type = get_post_meta($pricing_id, 'pricing_type', true);
        $promotions = get_post_meta($pricing_id, '_rtcl_membership_promotions', true);
        if ("membership" === $pricing_type && !empty($promotions)) {
            $promotion_list = Options::get_listing_promotions();
            echo '<div class="membership-promotions rtcl-pricing-promotions">';
            echo '<div class="item"><span class="item-label"></span><span class="listing-count">Ads</span></span><span class="validate">Validate</span></div>';
            foreach ($promotion_list as $promotion_key => $promotion_label) {
                echo sprintf('<div class="item"><span class="item-label">%s:</span><span class="listing-count">%d</span><span class="validate">%d</span></div>',
                    $promotion_label,
                    !empty($promotions[$promotion_key]['ads']) ? $promotions[$promotion_key]['ads'] : 0,
                    !empty($promotions[$promotion_key]['validate']) ? $promotions[$promotion_key]['validate'] : 0
                );
            }
            echo "</div>";
        }
    }

    static function add_custom_page($pages) {
        $pages['store'] = [
            'title'   => __('Store', 'classified-listing-store'),
            'content' => ''
        ];
        return $pages;
    }

    static function add_public_style($style, $style_options) {
        $primary = !empty($style_options['primary']) ? $style_options['primary'] : null;
        if ($primary) {
            $style .= ".rtcl .rtcl-stores .rtcl-store-item:hover div.item-content{background-color: $primary;}";
        }
        return $style;
    }

    static private function register_store_menu() {

        if (!Functions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox')) {
            return;
        }
        add_filter('rtcl_advanced_settings_options', [__CLASS__, 'add_store_end_point_options']);


        add_filter('rtcl_account_menu_items', [__CLASS__, 'add_store_menu_item_at_account_menu']);
        add_filter('rtcl_my_account_endpoint', [__CLASS__, 'add_my_account_store_end_points']);

    }

    /**
     * @param $features
     * @param $pricing Pricing
     *
     * @return array
     */
    public static function rtcl_get_payment_option_features($features, $pricing) {
        if ('membership' === $pricing->getType()) {
            $ads = get_post_meta($pricing->getId(), 'regular_ads', true);
            $features['regular_ads'] = sprintf('<span class="ads-count">%d</span>%s',
                $ads,
                __("Ads", "classified-listing-store")
            );
        }

        return $features;
    }

    public static function save_pricing_meta($post_id, $post) {

        if (!isset($_POST['post_type'])) {
            return $post_id;
        }

        if (rtcl()->post_type_pricing != $post->post_type) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        if (isset($_POST['pricing_type'])) {
            $pricing_type = Functions::sanitize($_POST['pricing_type']);
            if (!in_array($pricing_type, array_keys(Options::get_pricing_types()))) {
                $pricing_type = 'regular';
            }
            update_post_meta($post_id, 'pricing_type', $pricing_type);
        }

        if (isset($_POST['regular_ads'])) {
            $regular_ads = absint(Functions::sanitize($_POST['regular_ads']));
            update_post_meta($post_id, 'regular_ads', $regular_ads);
        }

        if (isset($_POST['_rtcl_membership_promotions'])) {
            $promotions_list = Options::get_listing_promotions();
            $promotions = [];
            if (!empty($promotions_list)) {
                foreach ($promotions_list as $promotion_key => $promotion_label) {
                    if (!empty($_POST['_rtcl_membership_promotions'][$promotion_key]['ads']) && !empty($_POST['_rtcl_membership_promotions'][$promotion_key]['validate'])) {
                        $ads = absint($_POST['_rtcl_membership_promotions'][$promotion_key]['ads']);
                        $validate = absint($_POST['_rtcl_membership_promotions'][$promotion_key]['validate']);
                        if ($ads && $validate) {
                            $promotions[$promotion_key]['ads'] = $ads;
                            $promotions[$promotion_key]['validate'] = $validate;
                        }
                    }
                }
            }
            if (!empty($promotions)) {
                update_post_meta($post_id, '_rtcl_membership_promotions', $promotions);
            } else {
                delete_post_meta($post_id, '_rtcl_membership_promotions');
            }
        }

        if (isset($_POST['membership_categories']) && is_array($_POST['membership_categories']) && !empty($_POST['membership_categories'])) {

            $cats = array_map(array(
                Functions::class,
                'clean'
            ), array_map('stripslashes', $_POST['membership_categories']));
            update_post_meta($post_id, 'membership_categories', $cats);
        } else {
            delete_post_meta($post_id, 'membership_categories');
        }

    }

    public static function add_payment_pricing_type($types) {
        $types['membership'] = esc_html__("Membership", "classified-listing-store");

        return $types;
    }

    public static function add_pricing_options($data, $post) {
        $pricing_type = get_post_meta($post->ID, "pricing_type", true);
        $hide_allowed = $pricing_type == "membership" ? " style='display:none'" : '';
        $regular_ads = absint(get_post_meta($post->ID, "regular_ads", true));
        $memberCats = (array)get_post_meta($post->ID, "membership_categories", true);
        $promotions = get_post_meta($post->ID, "_rtcl_membership_promotions", true);
        $promotions_list = Options::get_listing_promotions();
        $pricingTypes = Options::get_pricing_types();
        $pOpt = null;
        foreach ($pricingTypes as $key => $value) {
            $slt = $pricing_type == $key ? " selected" : null;
            $pOpt .= "<option value='{$key}' {$slt}>{$value}</option>";
        }
        $promotion_options = '';
        if (!empty($promotions_list)) {
            $promotion_options = sprintf('<div class="rtcl-promotion-item">
                                                    <label> </label>
                                                    <div class="rtcl-promotion-action">
                                                        <span>%s</span>
                                                        <span>%s</span>
                                                    </div>
                                                </div>',
                __('Ads', "classified-listing-store"),
                __('Validate<small>(Days)</small>', "classified-listing-store")
            );
            foreach ($promotions_list as $promotion_key => $promotion_label) {
                $promotion_options .= sprintf('<div class="rtcl-promotion-item">
                                                    <label for="%2$s">%3$s</label>
                                                    <div class="rtcl-promotion-action" id="%2$s">
                                                        <input name="_rtcl_membership_promotions[%1$s][ads]" class="form-control" type="number" step="1" value="%4$d">
                                                        <input name="_rtcl_membership_promotions[%1$s][validate]" class="form-control" type="number" step="1" value="%5$d">
                                                    </div>
                                                </div>',
                    $promotion_key,
                    'promotion-' . $promotion_key,
                    $promotion_label,
                    !empty($promotions[$promotion_key]['ads']) ? absint($promotions[$promotion_key]['ads']) : '',
                    !empty($promotions[$promotion_key]['validate']) ? absint($promotions[$promotion_key]['validate']) : ''
                );
            }
        }

        $data['allowed'] = substr_replace($data['allowed'], " allowed", 15, 0);
        $data['allowed'] = substr_replace($data['allowed'], $hide_allowed, 4, 0);

        $pricing_type_data = array(
            'price_type' => sprintf('<div class="row form-group">
                                                    <label class="col-2 col-form-label"
                                                           for="pricing-type">%s</label>
                                                    <div class="col-10">
                                                        <select class="form-control" id="pricing-type" name="pricing_type" required>%s</select>
                                                    </div>
                                                </div>',
                __("Pricing Type", "classified-listing-store"),
                $pOpt
            )
        );
        $data = array_merge($pricing_type_data, $data);
        $membership_options_data = array(
            'regular_ads' => sprintf('<div class="row form-group regular-listings"%s>
                                            <label class="col-2 col-form-label" for="regular-listings">%s</label>
                                            <div class="col-10">
                                                <input type="number" step="1" name="regular_ads" id="regular-listings" value="%d"
                                                       class="form-control" required>
                                            </div>
                                        </div>',
                $pricing_type == "membership" ? '' : " style='display:none'",
                esc_html__("Total Number of ads", "classified-listing-store"),
                esc_attr($regular_ads)
            ),
            'promotions'  => sprintf('<div class="row form-group rtcl-membership-promotions"%s>
                                            <label class="col-2 col-form-label">%s<small class="hint d-block text-danger">(%s)</small></label>
                                            <div class="col-10 rtcl-promotions-wrap">%s</div>
                                        </div>',
                $pricing_type == "membership" ? '' : " style='display:none'",
                esc_html__("Promotions", "classified-listing-store"),
                esc_html__("Total number of promotions ads must be less then total number of ads", "classified-listing-store"),
                $promotion_options
            )
        );
        $data = Functions::array_insert_after('price', $data, $membership_options_data);
        ob_start();
        ?>
        <div class="row form-group membership-categories"<?php if ($pricing_type != "membership") { ?> style="display:none"<?php } ?>>
            <label class="col-2 col-form-label"><?php esc_html_e("Categories", "classified-listing-store"); ?></label>
            <div class="col-10">
                <div class="checkbox">
                    <?php
                    $cats = StoreFunctions::get_first_level_category_array();
                    if (!empty($cats)):
                        foreach ($cats as $catId => $cat):?>
                            <div class="form-check">
                                <input class="form-check-input" name="membership_categories[]" type="checkbox"
                                       value="<?php echo esc_attr($catId) ?>"
                                    <?php echo in_array($catId, $memberCats) ? ' checked' : null; ?>
                                       id="membership_categories_<?php echo esc_attr($catId); ?>">
                                <label class="form-check-label"
                                       for="membership_categories_<?php echo esc_attr($catId); ?>"><?php echo esc_html($cat) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <span class="description"><?php esc_html_e("If you leave it unchecked, membership will validate for all categories.", "classified-listing-store"); ?></span>
            </div>
        </div>
        <?php
        $mc_html = ob_get_clean();
        $categories_data = array('membership_categories' => $mc_html);

        return Functions::array_insert_after('visible', $data, $categories_data);
    }


    public static function remove_attachment_for_store($excluded_ids) {

        $store_ids = get_posts([
            'post_type'        => rtclStore()->post_type,
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'fields'           => 'ids',
            'suppress_filters' => false
        ]);
        if (!empty($store_ids)) {
            $excluded_ids = array_merge($store_ids, (array)$excluded_ids);
        }

        return $excluded_ids;
    }

    public static function add_store_menu_item_at_account_menu($items) {
        if (Functions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox')) {
            if (Functions::get_option_item('rtcl_membership_settings', 'enable_store_only_membership', false, 'checkbox')) {
                $member = rtclStore()->factory->get_membership();
                if (!$member || !$member->has_membership() || $member->is_expired()) {
                    return $items;
                }
            }

            $item = array('store' => apply_filters('rtcl_store_myaccount_store_title', esc_html__('Store', 'classified-listing-store')));
            Functions::array_insert($items, 1, $item);
        }
        return $items;
    }

    // add membership tab item
    public static function add_membership_tab_item_at_settings_tabs_list($tabs) {
        $tabs['membership'] = esc_html__("Membership", "classified-listing-store");

        return $tabs;
    }

    // Add membership tab options
    public static function add_membership_tab_options($fields, $active_tab) {
        if ('membership' == $active_tab) {
            $fields = [
                'enable'                              => [
                    'title'       => esc_html__('Membership', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'description' => esc_html__('Enable Membership option', 'classified-listing-store'),
                ],
                'enable_store'                        => [
                    'title'       => esc_html__('Store', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'description' => esc_html__('All Store functionality will be active', 'classified-listing-store'),
                ],
                'enable_store_rating'                 => [
                    'title'       => esc_html__('Store rating', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'default'     => 'yes',
                    'description' => esc_html__('Enable Store rating. ', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable_store' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'enable_store_only_membership'        => [
                    'title'       => esc_html__('Store only for membership', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'description' => esc_html__('Store menu at My Account page will visible only for the valid membership users. ', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable_store' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'display_store_only_valid_membership' => [
                    'title'       => esc_html__('Single store only for membership', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'description' => esc_html__('Single store page will display only for valid membership owner. If enable, store single page will be only visible until membership is active.', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable_store' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'enable_store_manager'                => [
                    'title'      => esc_html__('Store Manager', 'classified-listing-store'),
                    'label'      => esc_html__('Enable', 'classified-listing-store'),
                    'type'       => 'checkbox',
                    'dependency' => [
                        'rules' => [
                            '#rtcl_membership_settings-enable_store' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'enable_free_ads'                     => [
                    'title'       => esc_html__('Free ads', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'description' => esc_html__('Enable free ad posting', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'number_of_free_ads'                  => [
                    'title'       => esc_html__('Number of free ads', 'classified-listing-store'),
                    'type'        => 'number',
                    'default'     => 3,
                    'description' => esc_html__('Number of ads to post as free with out membership, if membership is enabled.<br>If this field is blank dy default it will be 3', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable'          => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ],
                            '#rtcl_membership_settings-enable_free_ads' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'renewal_days_for_free_ads'           => [
                    'title'       => esc_html__('Renewal days for Free ads number', 'classified-listing-store'),
                    'type'        => 'number',
                    'default'     => 30,
                    'description' => esc_html__('Free ads number will be renew after this days.<br>If this field is blank it will be 30', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable'          => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ],
                            '#rtcl_membership_settings-enable_free_ads' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ],
                'unlimited_free_ads_membership'       => [
                    'title'       => esc_html__('Unlimited free ads for membership', 'classified-listing-store'),
                    'label'       => esc_html__('Enable', 'classified-listing-store'),
                    'type'        => 'checkbox',
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable'          => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ],
                            '#rtcl_membership_settings-enable_free_ads' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ],
                    'description' => esc_html__('Enable unlimited free ad posting for membership user.', 'classified-listing-store'),
                ],
                'categories_of_free_ads'              => [
                    'title'       => esc_html__('Allowed category for free ads', 'classified-listing-store'),
                    'type'        => 'multi_checkbox',
                    'options'     => StoreFunctions::get_first_level_category_array(),
                    'description' => esc_html__('Select the specific category for free ads, Leave it un select to allow any category.', 'classified-listing-store'),
                    'dependency'  => [
                        'rules' => [
                            '#rtcl_membership_settings-enable'          => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ],
                            '#rtcl_membership_settings-enable_free_ads' => [
                                'type'  => 'equal',
                                'value' => 'yes'
                            ]
                        ]
                    ]
                ]
            ];

            $fields = apply_filters('rtcl_membership_settings_options', $fields);
        }

        return $fields;
    }

    public static function add_misc_options($options) {
        $position = array_search('image_size_thumbnail', array_keys($options));
        if ($position > -1) {
            $option = array(
                'store_banner_size' => array(
                    'title'       => esc_html__('Store Banner', 'classified-listing-store'),
                    'type'        => 'image_size',
                    'default'     => array('width' => 1200, 'height' => 360, 'crop' => 'yes'),
                    'options'     => array(
                        'width'  => esc_html__('Width', 'classified-listing-store'),
                        'height' => esc_html__('Height', 'classified-listing-store'),
                        'crop'   => esc_html__('Hard Crop', 'classified-listing-store'),
                    ),
                    'description' => esc_html__('This image size is being used in banner at the store detail page.', "classified-listing-store")
                ),
                'store_logo_size'   => array(
                    'title'       => esc_html__('Store Logo', 'classified-listing-store'),
                    'type'        => 'image_size',
                    'default'     => array('width' => 200, 'height' => 150, 'crop' => 'yes'),
                    'options'     => array(
                        'width'  => esc_html__('Width', 'classified-listing-store'),
                        'height' => esc_html__('Height', 'classified-listing-store'),
                        'crop'   => esc_html__('Hard Crop', 'classified-listing-store'),
                    ),
                    'description' => esc_html__('This image size is being used at the store detail page and where store link is given.', "classified-listing-store")
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        return $options;
    }

    public static function add_store_end_point_options($options) {
        $position = array_search('location_base', array_keys($options));
        if ($position > -1) {
            $option = array(
                'permalink_store'     => array(
                    'title'       => esc_html__('Store base', 'classified-listing-store'),
                    'type'        => 'text',
                    'default'     => _x('store', 'slug', 'classified-listing-store'),
                    'description' => esc_html__('Store base permalink.', 'classified-listing-store'),
                ),
                'store_category_base' => array(
                    'title'       => esc_html__('Store category base', 'classified-listing-store'),
                    'type'        => 'text',
                    'default'     => _x('store-category', 'slug', 'classified-listing-store'),
                    'description' => esc_html__('Store category base permalink.', 'classified-listing-store'),
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        $position = array_search('checkout', array_keys($options));
        if ($position > -1) {
            $option = array(
                'store' => array(
                    'title'       => esc_html__('Store page', 'classified-listing-store'),
                    'type'        => 'select',
                    'class'       => 'rtcl-select2',
                    'blank_text'  => esc_html__("Select a page", 'classified-listing-store'),
                    'options'     => Functions::get_pages(),
                    'description' => esc_html__('This is the page where all the active store lists are displayed.', 'classified-listing-store'),
                    'css'         => 'min-width:300px;',
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        $position = array_search('myaccount_listings_endpoint', array_keys($options));
        if ($position > -1) {
            $option = array(
                'myaccount_store_endpoint' => array(
                    'title'   => esc_html__('Store', 'classified-listing-store'),
                    'type'    => 'text',
                    'default' => 'store'
                )
            );
            Functions::array_insert($options, $position, $option);
        }

        $position = array_search('checkout_submission_endpoint', array_keys($options));
        $newOptions = array(
            'checkout_membership_endpoint' => array(
                'title'   => esc_html__('Membership', 'classified-listing-store'),
                'type'    => 'text',
                'default' => 'membership'
            )
        );
        if ($position > -1) {
            Functions::array_insert($options, $position, $newOptions);
        } else {
            $options = array_merge($options, $newOptions);
        }

        return $options;
    }

    public static function add_checkout_membership_endpoint($endpoints) {
        if (StoreFunctions::is_membership_enabled()) {
            $endpoints['membership'] = Functions::get_option_item('rtcl_advanced_settings', 'checkout_membership_endpoint', 'membership');
        }
        return $endpoints;
    }

    public static function add_my_account_store_end_points($endpoints) {
        if (Functions::get_option_item('rtcl_membership_settings', 'enable_store_only_membership', false, 'checkbox')) {
            $member = rtclStore()->factory->get_membership();
            if (!$member || !$member->has_membership() || $member->is_expired()) {
                return $endpoints;
            }
        }
        $endpoints['store'] = Functions::get_option_item('rtcl_advanced_settings', 'myaccount_store_endpoint', 'store');

        return $endpoints;
    }

    public static function get_template_part($template, $slug, $name) {
        if (strpos($template, "classified-listing/" . $name) === false && !file_exists($template)) {
            $cache_key = sanitize_key(implode('-', array('template-part', $slug, $name, RTCL_STORE_VERSION)));
            $template = (string)wp_cache_get($cache_key, 'rtcl_store');

            if (!$template) {
                if ($name) {
                    $template = RTCL_STORE_TEMPLATE_DEBUG_MODE ? '' : locate_template(
                        array(
                            rtcl()->get_template_path() . "{$slug}-{$name}.php",
                        )
                    );

                    if (!$template) {
                        $fallback = rtclStore()->plugin_path() . "/templates/{$slug}-{$name}.php";
                        $template = file_exists($fallback) ? $fallback : '';
                    }
                }
                wp_cache_set($cache_key, $template, 'rtcl_store');
            }
        }
        return $template;
    }

    public static function locate_store_template($template_file, $name) {
        if (strpos($template_file, "classified-listing/" . $name) === false && !file_exists($template_file)) {
            $template_file = rtclStore()->plugin_path() . "/templates/$name.php";
        }
        return $template_file;
    }

    public static function add_store_settings_tab($tabs) {
        $tabs['store'] = esc_html__("Store", "classified-listing-store");

        return $tabs;
    }


    /**
     * @param int     $paymentId
     * @param Payment $payment
     */
    public static function payment_receipt_pricing_info($paymentId, $payment) {
        if ('membership' === get_post_meta($paymentId, 'payment_type', true)) {
            Functions::get_template("checkout/membership-pricing-info", compact('payment'), '', rtclStore()->get_plugin_template_path());
        } else {
            Functions::get_template("checkout/pricing-info", compact('payment'));
        }
    }

    public static function add_store_update_admin_email_notification($options) {

        $options['store_update'] = esc_html__('Store Update', 'classified-listing-store');

        return $options;

    }

    public static function add_recaptcha_store_contact_form($list) {
        $list['store_contact'] = esc_html__('Store contact form', 'classified-listing-store');

        return $list;
    }

    public static function add_recaptcha_script_store_contact_form() {
        if (is_singular(rtclStore()->post_type) &&
            Functions::get_option_item('rtcl_misc_settings', 'recaptcha_forms', 'store_contact', 'multi_checkbox')) {
            wp_enqueue_script('rtcl-recaptcha');
        }
    }
}
