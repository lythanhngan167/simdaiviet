<?php

namespace RtclPro\Controllers;

use DateInterval;
use Rtcl\Helpers\Functions;

class CronController
{

    public static function init() {
        add_action('rtcl_cron_daily_scheduled_events', [__CLASS__, 'remove_inactive_conversation']);
    }

    public static function remove_inactive_conversation() {
        if ($days = Functions::get_option_item('rtcl_chat_settings', 'remove_inactive_conversation_duration', 0, 'number')) {
            try {
                global $wpdb;
                $inactive_date = current_datetime()->sub(new DateInterval(sprintf('P%sD', $days)))->format('Y-m-d H:i:s');
                $ids = $wpdb->get_col($wpdb->prepare(
                    "SELECT rc.con_id FROM {$wpdb->prefix}rtcl_conversations as rc, {$wpdb->prefix}rtcl_conversation_messages as rcm  WHERE rc.con_id = rcm.con_id AND rc.last_message_id = rcm.message_id AND rcm.created_at < %s LIMIT 500",
                    $inactive_date
                ));

                if (!empty($ids)) {
                    $wpdb->query(sprintf('DELETE FROM %s WHERE con_id IN (%s)', $wpdb->prefix . 'rtcl_conversations', implode(',', $ids)));
                }
            } catch (\Exception $e) {
            }

        }
    }
}