<?php

namespace RtclPro\Api;

use RtclPro\Api\V1\V1_MyApi;
use RtclPro\Api\V1\V1_AuthApi;
use RtclPro\Api\V1\V1_OrderApi;
use RtclPro\Api\V1\V1_ReviewApi;
use RtclPro\Api\V1\V1_CommonApi;
use RtclPro\Api\V1\V1_ListingApi;
use RtclPro\Api\V1\V1_PushNotificationApi;

class RestApi
{

    public function init() {
        add_action('rest_api_init', [$this, 'init_rest_routes'], 99);
        RestActionHooks::init();
    }

    public function init_rest_routes() {
        $auth = new V1_AuthApi();
        $auth->register_routes();

        $common = new V1_CommonApi();
        $common->register_routes();

        $payment = new V1_OrderApi();
        $payment->register_routes();

        $listings = new V1_ListingApi();
        $listings->register_routes();

        $my = new V1_MyApi();
        $my->register_routes();

        $review = new V1_ReviewApi();
        $review->register_routes();

        $review = new V1_PushNotificationApi();
        $review->register_routes();
    }
}