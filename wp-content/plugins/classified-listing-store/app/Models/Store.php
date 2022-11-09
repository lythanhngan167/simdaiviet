<?php

namespace RtclStore\Models;

use WP_Query;
use WP_User;
use Rtcl\Helpers\Functions as RtclFunctions;
use RtclStore\Controllers\Hooks\StoreReviews;

class Store
{

    protected $id;
    protected $store;
    protected $categories;

    function __construct($store_id) {
        $store = get_post($store_id);
        if (is_object($store) && $store->post_type == rtclStore()->post_type) {
            $this->store = $store;
            $this->id = $this->store->ID;
            $this->categories = wp_get_object_terms($this->id, rtcl()->category);
        }

    }

    public function exist() {
        return $this->store->post_type === rtclStore()->post_type;
    }


    /**
     * @deprecated
     */
    public function get_the_id() {
        return $this->get_id();
    }


    public function get_id() {
        return $this->id;
    }


    public function get_status() {
        return $this->store->post_status;
    }


    public function created_at() {
        return $this->store->post_date;
    }

    public function created_at_gmt() {
        return $this->store->post_date_gmt;
    }

	/**
	 *
	 * @return string
	 */
	 function get_category() {
		$terms = get_the_terms( $this->get_id(), rtclStore()->category );

		if (!empty($terms)) {
			foreach( $terms as $term ) {
				$children = get_terms(array( 'parent' => $term->term_id ));
				if ( count($children) == 0 ) {
					return '<a href="'.get_term_link($term->slug, rtclStore()->category).'">'.$term->name.'</a>';
				}
			}
		}

		return '';
	}

    /**
     * @return array
     */
    function get_category_ids() {
        if (!empty($this->categories)) {
            return wp_list_pluck($this->categories, 'term_id');
        }

        return [];
    }

    public function get_address() {
        return get_post_meta($this->get_id(), 'address', true);
    }

    public function get_phone() {
        return get_post_meta($this->get_id(), 'phone', true);
    }

    public function get_email() {
        return get_post_meta($this->get_id(), 'email', true);
    }

    public function get_website() {
        return get_post_meta($this->get_id(), 'website', true);
    }

    public function get_the_slogan() {
        return apply_filters('rtcl_store_get_the_slogan', get_post_meta($this->get_id(), 'slogan', true));
    }

    public function the_slogan() {
        echo apply_filters('rtcl_store_the_slogan', $this->get_the_slogan());
    }

    public function get_the_description() {
        return apply_filters('rtcl_store_get_the_description', $this->store->post_content);
    }

    public function get_social_media() {
        return apply_filters('rtcl_store_get_social_media', get_post_meta($this->get_id(), 'social_media', true));
    }


    public function the_description($word_limit = 0) {
        $description = $this->get_the_description();
        if ($word_limit) {
            $description = wp_trim_words($description, $word_limit, '');
            echo apply_filters('rtcl_store_the_description', $description);
        } else {
            echo apply_filters('rtcl_store_the_description', apply_filters('the_content', $this->get_the_description()));
        }
    }

    public function get_banner_id() {
        return apply_filters('rtcl_store_get_banner_id', get_post_meta($this->get_id(), 'banner_id', true));
    }

    public function get_logo_id() {
        return apply_filters('rtcl_store_get_logo_id', get_post_meta($this->get_id(), 'logo_id', true));
    }


    public function get_label_class() {
        return [];
    }


    public function get_open_hour_type() {
        return apply_filters('rtcl_store_get_open_hour_type', get_post_meta($this->get_id(), 'oh_type', true), $this);
    }

    public function get_open_hours() {
        return apply_filters('rtcl_store_get_open_hours', get_post_meta($this->get_id(), 'oh_hours', true), $this);
    }

    public function get_post_type() {
        return $this->store->post_type;
    }

    public function owner_id() {
        return absint(get_post_meta($this->get_id(), 'store_owner_id', true));
    }

    /**
     * @return array
     */
    public function get_manager_invitation_list() {
        $store_manager_user_ids = get_post_meta($this->get_id(), '_rtcl_manager_invitations', true);
        return is_array($store_manager_user_ids) && !empty($store_manager_user_ids) ? $store_manager_user_ids : [];
    }

    /**
     * @return int[]
     */
    public function get_manager_ids() {
        $store_manager_user_ids = get_post_meta($this->get_id(), '_rtcl_manager_ids', true);
        return is_array($store_manager_user_ids) && !empty($store_manager_user_ids) ? $store_manager_user_ids : [];
    }

    /**
     * @param WP_User|integer
     *
     * @return boolean|integer
     */
    public function add_to_manager_invitation_list($user) {
        $user_id = is_a($user, WP_User::class) ? $user->ID : absint($user);
        if ($user_id) {
            $invitation_list = $this->get_manager_invitation_list();
            if (!isset($invitation_list[$user_id])) {
                $key = wp_generate_uuid4();
                $invitation_list[$user_id] = $key;
                update_post_meta($this->get_id(), '_rtcl_manager_invitations', $invitation_list);
                rtcl()->mailer()->emails['StoreManagerInvitation']->trigger($this->get_id(), ['user_id' => $user_id, 'key' => $key]);
                return $user_id;
            }
        }
        return false;
    }

    /**
     * @param int   $manager_id
     * @param array $options
     *
     * @return array
     */
    public function get_manager_listing_ids(int $manager_id, $options = []): array {
        if (!absint($manager_id) && !in_array($manager_id, $this->get_manager_ids())) {
            return [];
        }
        $args = array(
            'post_type'      => rtcl()->post_type,
            'post_status'    => 'publish',
            'posts_per_page' => !empty($options['listings_per_page']) ? absint($options['listings_per_page']) : -1,
            'paged'          => !empty($options['paged']) ? absint($options['paged']) : 1,
            'author'         => $this->owner_id(),
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'   => '_rtcl_manager_id',
                    'value' => $manager_id
                ]
            ]
        );

        $results = new WP_Query(apply_filters('rtcl_store_get_manager_listing_ids', $args, $this));
        if (!empty($results->posts)) {
            return $results->posts;
        }

        return [];
    }

    /**
     * @param WP_User|integer
     *
     * @return boolean|integer
     */
    public function approve_manager($user) {
        $user_id = is_a($user, WP_User::class) ? $user->ID : absint($user);
        $invitation_list = $this->get_manager_invitation_list();
        if ($user_id && !empty($invitation_list) && isset($invitation_list[$user_id])) {
            unset($invitation_list[$user_id]);
            if (count($invitation_list)) {
                update_post_meta($this->get_id(), '_rtcl_manager_invitations', $invitation_list);
            } else {
                delete_post_meta($this->get_id(), '_rtcl_manager_invitations');
            }

            $existing_manager_ids = $this->get_manager_ids();
            array_push($existing_manager_ids, $user_id);
            $existing_manager_ids = array_unique($existing_manager_ids);
            update_post_meta($this->get_id(), '_rtcl_manager_ids', $existing_manager_ids);
            update_user_meta($user_id, '_rtcl_store_id', $this->get_id());
            return $user_id;
        }
        return false;
    }

    /**
     * @param WP_User|integer
     *
     * @return boolean|integer
     */
    public function remove_manager($user) {
        $user_id = is_a($user, WP_User::class) ? $user->ID : absint($user);
        if ($user_id) {
            $invitation_list = $this->get_manager_invitation_list();
            if (!empty($invitation_list) && isset($invitation_list[$user_id])) {
                unset($invitation_list[$user_id]);
                delete_user_meta($user_id, '_rtcl_store_id');
                delete_metadata('post', 0, '_rtcl_manager_id', '', true);
                if (count($invitation_list)) {
                    update_post_meta($this->get_id(), '_rtcl_manager_invitations', $invitation_list);
                } else {
                    delete_post_meta($this->get_id(), '_rtcl_manager_invitations');
                }
            } else {
                $existing_manager_user_ids = $this->get_manager_ids();
                if (($key = array_search($user_id, $existing_manager_user_ids)) !== false) {
                    unset($existing_manager_user_ids[$key]);
                    delete_user_meta($user_id, '_rtcl_store_id');
                    delete_metadata('post', 0, '_rtcl_manager_id', '', true);
                    if (count($existing_manager_user_ids)) {
                        update_post_meta($this->get_id(), '_rtcl_manager_ids', $existing_manager_user_ids);
                    } else {
                        delete_post_meta($this->get_id(), '_rtcl_manager_ids');
                    }
                }
            }

            do_action('rtcl_store_removed_listing_manager', $user_id);

	        return $user_id;

        }
        return false;
    }

    public function owner_name() {
        $user = get_user_by('id', $this->owner_id());
        if (is_a($user, WP_User::class)) {
            return $store_owner_name = $user->first_name . ' ' . $user->last_name;
        }

        return '';
    }

    public function has_logo() {
        return $this->get_logo_id();
    }

    public function has_banner() {
        return $this->get_banner_id();
    }

    public function get_logo_url($size = 'rtcl-store-logo') {
        $logo_url = false;
        if ($logo_id = $this->get_logo_id()) {
            $logo_url = wp_get_attachment_image_url($logo_id, $size);
        }

        return apply_filters('rtcl_store_get_logo_url', $logo_url);
    }

    /**
     * @param string $size
     * @param array  $attr class [], id
     *
     * @return mixed|void
     */
    public function get_the_logo($size = 'rtcl-store-logo', $attr = array()) {
        $logoImage = null;
        if ($logo_id = $this->get_logo_id()) {
            $attrClass = !empty($attr['class']) ? ' ' . $attr['class'] : '';
            $attr['class'] = 'rtcl-thumbnail rtcl-store-thumbnail' . $attrClass;
            $logoImage = wp_get_attachment_image($logo_id, $size, false, $attr);
        }
        return apply_filters('rtcl_store_get_the_logo', $logoImage, $size, $attr);
    }

    public function the_logo($size = 'rtcl-store-logo', $attr = array()) {
        echo apply_filters('rtcl_store_the_logo', $this->get_the_logo($size, $attr));
    }


    public function get_banner_url($size = 'rtcl-store-banner') {
        $banner_url = '';
        if ($banner_id = $this->get_banner_id()) {
            $banner_url = wp_get_attachment_image_url($banner_id, $size);
        }

        return apply_filters('rtcl_store_get_banner_url', $banner_url, $size);
    }

    public function get_the_banner($size = 'rtcl-store-banner', $attr = array()) {
        $bannerImage = null;
        if ($banner_id = $this->get_banner_id()) {
            $attrClass = !empty($attr['class']) ? ' ' . $attr['class'] : '';
            $attr['class'] = 'rtcl-thumbnail' . $attrClass;
            $bannerImage = wp_get_attachment_image($banner_id, $size, false, $attr);
        }

        return apply_filters('rtcl_store_get_the_banner', $bannerImage, $size, $attr);
    }

    public function the_banner($size = 'rtcl-store-banner', $attr = array()) {
        echo apply_filters('rtcl_store_the_banner', $this->get_the_banner($size, $attr));
    }

    /**
     * @return int
     */
    public function get_ad_count() {
        return count(get_posts(array(
            'post_type'        => rtcl()->post_type,
            'post_status'      => 'publish',
            'posts_per_page'   => -1,
            'suppress_filters' => false,
            'fields'           => 'ids',
            'author'           => $this->owner_id()
        )));
    }

    public function get_the_metas() {
        $metas_html = $this->get_ad_count_html();
        $metas_html = apply_filters('rtcl_store_get_the_metas', $metas_html, $this);

        return apply_filters('rtcl_store_get_the_metas_html', sprintf('<div class="rtcl-store-meta">%s</div>', $metas_html), $this);
    }

    public function get_ad_count_html() {
        $count = $this->get_ad_count();
        $count_string = $count <= 0 ? apply_filters('rtcl_store_no_ad_text', esc_html__("No ad", "classified-listing-store"), $this, $count) : sprintf(_n("%s Lượt đăng bán sim", "%s Lượt đăng bán sim", $count, 'classified-listing-store'), number_format_i18n($count));

        return apply_filters('rtcl_store_get_ad_count_html', sprintf('<span class="ads-count">%s</span>', $count_string), $this, $count, $count_string);
    }

    public function the_metas() {
        echo apply_filters('rtcl_store_the_metas', $this->get_the_metas());
    }

    public function the_labels() {

    }

    public function the_excerpt() {
        echo apply_filters('rtcl_store_the_excerpt', esc_html(get_the_excerpt($this->store)));
    }

    public function get_slug() {
        return apply_filters('rtcl_store_get_slug', $this->store->post_name);
    }

    public function get_the_title() {
        return apply_filters('rtcl_store_get_the_title', get_the_title($this->store));
    }

    public function the_title() {
        echo apply_filters('rtcl_store_the_title', $this->get_the_title());
    }

    public function get_the_permalink() {
        return apply_filters('rtcl_store_get_the_permalink', get_the_permalink($this->store->ID));
    }

    public function the_permalink() {
        echo apply_filters('rtcl_store_the_permalink', $this->get_the_permalink());
    }

    public function get_listing_ids() {
        global $wpdb;
        $ids = [];
        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT ID
					FROM {$wpdb->posts}
					WHERE post_type = %s
					AND post_author = %d
					AND post_status = 'publish'
				", rtcl()->post_type, $this->owner_id()), ARRAY_A
        );
        if (count($result)) {
            $ids = wp_list_pluck($result, 'ID');
        }

        return $ids;
    }


    /**
     * Set total rating value. Read only.
     *
     * @param int $rating_total All listings rating count of a store.
     */
    public function set_rating_total($rating_total) {
        update_post_meta($this->get_id(), '_rtcl_rating_total', absint($rating_total));
    }

    /**
     * Set review counts. Read only.
     *
     * @param int $counts Product review counts.
     */
    public function set_review_counts($counts) {
        update_post_meta($this->get_id(), '_rtcl_review_count', absint($counts));
    }


    /**
     * Set average rating. Read only.
     *
     * @param float $average Product average rating.
     */
    public function set_average_rating($average) {
        update_post_meta($this->get_id(), '_rtcl_average_rating', RtclFunctions::format_decimal($average));
    }


    /**
     * Get average rating.
     *
     * @return float
     */
    public function get_average_rating() {
        return get_post_meta($this->get_id(), '_rtcl_average_rating', true);
    }

    /**
     * Get review count.
     *
     * @return int
     */
    public function get_review_counts() {
        return absint(get_post_meta($this->get_id(), '_rtcl_review_count', true));
    }


    /**
     * Get total rating count.
     *
     * @return int
     */
    public function get_rating_total() {
        return absint(get_post_meta($this->get_id(), '_rtcl_rating_total', true));
    }


    public function is_rating_enable() {
        return RtclFunctions::get_option_item('rtcl_membership_settings', 'enable_store', false, 'checkbox') && RtclFunctions::get_option_item('rtcl_membership_settings', 'enable_store_rating', true, 'checkbox');
    }

    public function update_review_rating() {
        if ($this->is_rating_enable() && !RtclFunctions::meta_exist($this->get_id(), '_rtcl_average_rating')) {
            StoreReviews::calculate_store_rating($this);
        }
    }


    /**
     *
     * @return null|string
     */
    function get_social_media_html() {
        return RtclFunctions::get_template_html("store/social-media", [ 'store' => $this ], '', rtclStore()->get_plugin_template_path());
    }

}
