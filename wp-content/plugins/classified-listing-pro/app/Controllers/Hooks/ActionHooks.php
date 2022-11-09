<?php

namespace RtclPro\Controllers\Hooks;


use Rtcl\Models\Payment;
use Rtcl\Models\Roles;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclPro\Gateways\WooPayment\WooPayment;

class ActionHooks
{
    public static function init() {
        add_action('init', [__CLASS__, "wc_payment_support"]);
        add_action('rtcl_admin_settings_before_saved_account_settings', [__CLASS__, 'apply_user_role_at_account_settings'], 10, 2);

        add_action('rtcl_listing_overwrite_change', [__CLASS__, 'update_promotions_at_save_post'], 10, 2);

        add_action('rtcl_save_pricing_meta_data', [__CLASS__, 'save_pricing_meta_data'], 10, 2);

        add_action('rtcl_cron_move_listing_publish_to_expired', [__CLASS__, 'remove_data_move_listing_publish_to_expired']);
        add_action('rtcl_cron_hourly_scheduled_events', [__CLASS__, 'cron_hourly_scheduled_actions']);

        add_action('rtcl_rest_checkout_process_success', [__CLASS__, 'rest_checkout_process_mail']);

        add_action('clear_auth_cookie', [__CLASS__, 'set_user_status_offline']);

        add_action('rtcl_shortcode_before_listings_loop', [__CLASS__, 'add_map_data_support']);
        add_action('rtcl_shortcode_after_listings_loop', [__CLASS__, 'remove_map_data_support']);

		add_action('rtcl_listing_submit_box_misc_actions__bump_up', [__CLASS__, 'add_bump_up_expired_date'], 10,2);
    }


    public static function add_map_data_support($attributes) {
        if (!empty($attributes['map'])) {
            global $rtcl_has_map_data;
            $rtcl_has_map_data = 1;
        }
    }

    public static function remove_map_data_support($attributes) {
        if (isset($attributes['map'])) {
            global $rtcl_has_map_data;
            $rtcl_has_map_data = null;
        }
    }


    public static function set_user_status_offline() {
        update_user_meta(get_current_user_id(), 'online_status', 0);
        delete_user_meta(get_current_user_id(), '_rtcl_conversation_status');
    }


    /**
     * @param Payment $payment
     */
    static function rest_checkout_process_mail($payment) {
        if ($payment && $payment->exists()) {
            if (Functions::get_option_item('rtcl_email_settings', 'notify_admin', 'order_created', 'multi_checkbox')) {
                rtcl()->mailer()->emails['Order_Created_Email_To_Admin']->trigger($payment->get_id(), $payment);
            }

            if (Functions::get_option_item('rtcl_email_settings', 'notify_users', 'order_created', 'multi_checkbox')) {
                rtcl()->mailer()->emails['Order_Created_Email_To_Customer']->trigger($payment->get_id(), $payment);
            }
        }
    }

    /**
     * @param integer $post_id
     * @param array   $request
     */
    public static function save_pricing_meta_data($post_id, $request) {
        if (isset($request['_top'])) {
            update_post_meta($post_id, '_top', 1);
        } else {
            delete_post_meta($post_id, '_top');
        	delete_post_meta($post_id, '_top_expiry_date');
        }
        if (isset($request['_bump_up'])) {
            update_post_meta($post_id, '_bump_up', 1);
        } else {
            delete_post_meta($post_id, '_bump_up');
			delete_post_meta($post_id, '_bump_up_expiry_date');
        }
    }

    public static function cron_hourly_scheduled_actions() {
        self::remove_expired_bump_up();
        self::remove_expired_top_listing();
        self::do_hourly_bump_up();
    }

    public static function remove_data_move_listing_publish_to_expired($post_id) {
        delete_post_meta($post_id, '_top');
        delete_post_meta($post_id, '_top_expiry_date');
        delete_post_meta($post_id, '_bump_up');
        delete_post_meta($post_id, '_bump_up_expiry_date');
    }

    private static function do_hourly_bump_up() {
        // Define the query
        $args = apply_filters('rtcl_cron_do_hourly_bump_up_query_args', [
            'post_type'      => rtcl()->post_type,
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'date_query'     => [
                'before' => current_time('Y-m-d')
            ],
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => '_bump_up_expiry_date',
                    'value'   => current_time('mysql'),
                    'compare' => '>',
                    'type'    => 'DATETIME'
                ],
                [
                    'key'     => '_bump_up',
                    'compare' => '=',
                    'value'   => 1,
                ]
            ]
        ]);

        $rtcl_query = new \WP_Query($args);
        if (!empty($rtcl_query->posts)) {
            foreach ($rtcl_query->posts as $post_id) {
                wp_update_post(
                    array(
                        'ID'            => $post_id,
                        'post_date'     => current_time('mysql'),
                        'post_date_gmt' => get_gmt_from_date(current_time('mysql'))
                    )
                );
                do_action("rtcl_cron_do_hourly_bump_up_listing", $post_id);
            }
        }
    }

    private static function remove_expired_bump_up() {

        // Define the query
        $args = array(
            'post_type'      => rtcl()->post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_bump_up_expiry_date',
                    'value'   => current_time('mysql'),
                    'compare' => '<',
                    'type'    => 'DATETIME'
                ),
                array(
                    'key'     => '_bump_up',
                    'compare' => '=',
                    'value'   => 1,
                )
            )
        );


        $rtcl_query = new \WP_Query(apply_filters('rtcl_cron_remove_expired_bump_up_query_args', $args));

        if (!empty($rtcl_query->posts)) {

            foreach ($rtcl_query->posts as $post_id) {
                delete_post_meta($post_id, '_bump_up');
                delete_post_meta($post_id, '_bump_up_expiry_date');
                do_action("rtcl_cron_remove_expired_bump_up_listing", $post_id); // TODO : make task
            }
        }


    }

    private static function remove_expired_top_listing() {
        // Define the query
        $args = array(
            'post_type'      => rtcl()->post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_top_expiry_date',
                    'value'   => current_time('mysql'),
                    'compare' => '<',
                    'type'    => 'DATETIME'
                ),
                array(
                    'key'     => '_top',
                    'compare' => '=',
                    'value'   => 1,
                )
            )
        );


        $rtcl_query = new \WP_Query(apply_filters('rtcl_cron_remove_expired_top_listing_query_args', $args));

        if (!empty($rtcl_query->posts)) {

            foreach ($rtcl_query->posts as $post_id) {
                delete_post_meta($post_id, '_top');
                delete_post_meta($post_id, '_top_expiry_date');
                do_action("rtcl_cron_remove_expired_top_listing", $post_id);
            }
        }
    }

    /**
     * @param integer $post_id
     * @param array   $request
     */
    public static function update_promotions_at_save_post($post_id, $request) {

        // Top
        if (isset($request['_top'])) {
            update_post_meta($post_id, '_top', 1);
        } else {
            delete_post_meta($post_id, '_top');
            delete_post_meta($post_id, '_top_expiry_date');
        }

        // Bump up
        if (isset($request['_bump_up'])) {
            update_post_meta($post_id, '_bump_up', 1);
			
			if(isset($request['_bump_up_expiry_date'])){
				$current_date = new \DateTime(current_time('mysql'));
				$bumpUp_expiry_date = new \DateTime(Functions::datetime('mysql', trim($request['_bump_up_expiry_date'])));
				
				$oldBumpUpExpireDate = get_post_meta($post_id, '_bump_up_expiry_date', true);
				$oldBumpUpExpireDate = $oldBumpUpExpireDate ? new \DateTime(Functions::datetime('mysql', trim($oldBumpUpExpireDate))) : null;
				if ($bumpUp_expiry_date && $bumpUp_expiry_date > $current_date) {
					
					if($oldBumpUpExpireDate){
						if($bumpUp_expiry_date > $oldBumpUpExpireDate){
							update_post_meta($post_id, '_bump_up_expiry_date',$bumpUp_expiry_date->format('Y-m-d H:i:s'));
						}
					}else{
						update_post_meta($post_id, '_bump_up_expiry_date', $bumpUp_expiry_date->format('Y-m-d H:i:s'));
					}
				}
			}else{
				delete_post_meta($post_id, '_bump_up_expiry_date');
			}
        } else {
            delete_post_meta($post_id, '_bump_up');
            delete_post_meta($post_id, '_bump_up_expiry_date');
        }
    }

	/**
	 * @param int $isBumpUp
	 * @param Post $post
	 */
	public static function add_bump_up_expired_date($isBumpUp, $post){
			$expiredDate = get_post_meta($post->ID, '_bump_up_expiry_date', true);
			?>
			<div class="misc-pub-section rtcl-overwrite-sub-item rtcl-overwrite-sub-item__bump_up">
				<label for="_bump_up_expiry_date"><?php _e("Bump Up Expired at:", 'classified-listing') ?></label>
				<input disabled type="text" class="rtcl-date" name="_bump_up_expiry_date" value="<?php echo $expiredDate; ?>" data-options="<?php echo htmlspecialchars(wp_json_encode([
					'singleDatePicker'=> true,
					'minDate'=>	date('Y-m-d h:i:s'),
					'timePicker'=> true,
					"timePicker24Hour"=> true,
    				'showDropdowns'=> true,
					'autoUpdateInput'=> false,
					'locale'           => [
						'format' => 'YYYY-MM-DD hh:mm:ss'
					]
				])) ?>" />
			</div>
			<?php
	}


    public static function wc_payment_support() {
        if (Fns::is_wc_payment_enabled()) {
            new WooPayment();
        }
    }


    public static function apply_user_role_at_account_settings($new_options, $old_options) {

        if (!empty($new_options['allowed_core_permission_roles']) || !empty($old_options['allowed_core_permission_roles'])) {

            $new_roles = isset($new_options['allowed_core_permission_roles']) && is_array($new_options['allowed_core_permission_roles']) ? $new_options['allowed_core_permission_roles'] : [];
            $old_roles = isset($old_options['allowed_core_permission_roles']) && is_array($old_options['allowed_core_permission_roles']) ? $old_options['allowed_core_permission_roles'] : [];
            $add_roles = array_diff($new_roles, $old_roles);
            $remove_roles = array_diff($old_roles, $new_roles);
            if (!empty($add_roles)) {
                Roles::add_core_caps($add_roles);
            }
            if (!empty($remove_roles)) {
                Roles::remove_code_caps($remove_roles);
            }
        }

    }
}
