<?php

namespace Rtrs\Controllers\Admin;

class Notifications {

    public function __construct() { 
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
    } 

    public function plugin_row_meta($links, $file) {
        if ( $file == plugin_basename(RTRS_PLUGIN_FILE) ) { 
            $report_url = 'https://www.radiustheme.com/contact/';
            $row_meta['issues'] = sprintf('%2$s <a target="_blank" href="%1$s">%3$s</a>', esc_url($report_url), esc_html__('Facing issue?', 'review-schema'), '<span style="color: red">' . esc_html__('Please open a support ticket.', 'review-schema') . '</span>'); 
            return array_merge($links, $row_meta);
        }

        return (array)$links;
    } 
}