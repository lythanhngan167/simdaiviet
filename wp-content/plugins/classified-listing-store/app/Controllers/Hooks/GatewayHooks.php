<?php

namespace RtclStore\Controllers\Hooks;

use Rtcl\Models\Payment;
use RtclStore\Helpers\Functions;

class GatewayHooks
{

    public static function init() {
        add_filter('rtcl_authorizenet_line_item', array(__CLASS__, 'rtcl_authorizenet_line_item'), 10, 2);
        add_filter('rtcl_paypal_item_info', array(__CLASS__, 'rtcl_paypal_item_info'), 10, 2);
    }

    /**
     * @param                      $lineItemData
     * @param \Rtcl\Models\payment $payment
     * @return mixed
     */
    public static function rtcl_authorizenet_line_item($lineItemData, $payment) {
        if ($payment->is_membership()) {
            $lineItemData['id'] = $payment->pricing->getId();
            $lineItemData['name'] = $payment->pricing->getTitle();
            $lineItemData['description'] = $payment->pricing->getDescription();
        }

        return $lineItemData;
    }

    /**
     * @param $item
     * @param $payment Payment
     * @return mixed
     */
    public static function rtcl_paypal_item_info($item, $payment) {
        if ($payment->is_membership()) {
            $item['item_name_1'] = Functions::limit_length($payment->pricing->getTitle(), 127);
        }
        return $item;
    }

}