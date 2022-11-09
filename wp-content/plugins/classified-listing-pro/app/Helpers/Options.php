<?php

namespace RtclPro\Helpers;

use Rtcl\Helpers\Link;
use Rtcl\Resources\Options as RtclOptions;

class Options
{


    public static function get_registered_only_options() {
        $options = [
            'listing_seller_information' => esc_html__('Listing seller information', 'classified-listing-pro')
        ];
        return apply_filters('rtcl_registered_only_options', $options);
    }

    /**
     * @return array|object
     * @deprecated
     * @use Rtcl\Resources\Options::is_enable_map()
     */
    static function get_radius_search_options() {
        _deprecated_function(__METHOD__, '2.0.9', 'Rtcl\Resources\Options::is_enable_map()');
        return RtclOptions::radius_search_options();
    }

    static function widget_search_style_options() {
        $options = [
            'popup'      => esc_html__('Popup', 'classified-listing-pro'),
            'suggestion' => esc_html__('Auto Suggestion', 'classified-listing-pro'),
            'dependency' => esc_html__('Dependency Selection', 'classified-listing-pro'),
            'standard'   => esc_html__('Standard', 'classified-listing-pro')
        ];

        return apply_filters('rtcl_pro_widget_search_style_options', $options);
    }

    static function get_listings_view_options() {
        $options = [
            'list' => esc_html__("List", 'classified-listing-pro'),
            'grid' => esc_html__("Grid", 'classified-listing-pro')
        ];

        return apply_filters('rtcl_pro_listings_view_options', $options);
    }

    public static function chat_admin_settings() {
        $options = array(
            'ls_section'                            => array(
                'title'       => esc_html__('Chat settings', 'classified-listing-pro'),
                'type'        => 'title',
                'description' => wp_kses(sprintf(__('Regenerate Chat Table <a href="%s" onClick="return confirm(\'Do you really want to Confirm this booking\')">Click Here.</a> <span style="color:red">This will remove all chat history.</span>', 'classified-listing-pro'), add_query_arg([
                    rtcl()->nonceId              => wp_create_nonce(rtcl()->nonceText),
                    'rtcl_regenerate_chat_table' => ''
                ], Link::get_current_url())), [
                    'a'    => [
                        'href'    => [],
                        'onClick' => []
                    ],
                    'span' => [
                        'style' => ['color']
                    ]
                ]),
            ),
            'enable'                                => array(
                'title'       => esc_html__('Chat', 'classified-listing-pro'),
                'label'       => esc_html__('Enable', 'classified-listing-pro'),
                'type'        => 'checkbox',
                'description' => esc_html__('Enable Chat option', 'classified-listing-pro'),
            ),
            'unread_message_email'                  => array(
                'title'       => esc_html__('Unread Message Email', 'classified-listing-pro'),
                'label'       => esc_html__('Enable', 'classified-listing-pro'),
                'type'        => 'checkbox',
                'description' => wp_kses(
                    __('Enable email for unread message trace to receiver, if receiver at offline <span style="color: red">(Only for the first message)</span>.', 'classified-listing-pro'),
                    [
                        'span' => [
                            'style' => true
                        ]
                    ]
                )
            ),
            'remove_inactive_conversation_duration' => array(
                'title'       => esc_html__('Delete inactive conversation (in days)', 'classified-listing-pro'),
                'type'        => 'number',
                'default'     => 30,
                'description' => wp_kses(
                    __('Auto remove inactive conversation which are last active in given days ago <span style="color: red">(Leave it blank to alive conversation forever)</span>.', 'classified-listing-pro'),
                    [
                        'span' => [
                            'style' => true
                        ]
                    ]
                )
            )
        );

        return apply_filters('rtcl_chat_settings_options', $options);
    }

}