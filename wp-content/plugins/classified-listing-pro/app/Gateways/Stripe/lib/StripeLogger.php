<?php

namespace RtclPro\Gateways\Stripe\lib;

use Rtcl;

class StripeLogger
{

    public static $logger;
    const rtcl_LOG_FILENAME = 'rtcl-gateway-stripe';

    /**
     * Utilize WC logger class
     *
     * @since   4.0.0
     * @version 4.0.0
     */
    public static function log($message, $start_time = null, $end_time = null) {
        if (!class_exists(Rtcl::class)) {
            return;
        }

        if (apply_filters('rtcl_stripe_logging', true, $message)) {
            if (empty(self::$logger)) {
                self::$logger = rtcl()->logger(['filename'=>self::rtcl_LOG_FILENAME]);
            }

            $settings = get_option('rtcl_payment_stripe');

            if (empty($settings) || isset($settings['logging']) && 'yes' !== $settings['logging']) {
                return;
            }

            if (!is_null($start_time)) {

                $formatted_start_time = date_i18n(get_option('date_format') . ' g:ia', $start_time);
                $end_time = is_null($end_time) ? current_time('timestamp') : $end_time;
                $formatted_end_time = date_i18n(get_option('date_format') . ' g:ia', $end_time);
                $elapsed_time = round(abs($end_time - $start_time) / 60, 2);

                $log_entry = "\n" . '====RTCL Stripe Version: ' . RTCL_PRO_VERSION . '====' . "\n";
                $log_entry .= '====Start Log ' . $formatted_start_time . '====' . "\n" . $message . "\n";
                $log_entry .= '====End Log ' . $formatted_end_time . ' (' . $elapsed_time . ')====' . "\n\n";

            } else {
                $log_entry = "\n" . '====RTCL Stripe Version: ' . RTCL_PRO_VERSION . '====' . "\n";
                $log_entry .= '====Start Log====' . "\n" . $message . "\n" . '====End Log====' . "\n\n";

            }

            self::$logger->debug($log_entry);
        }
    }
}
