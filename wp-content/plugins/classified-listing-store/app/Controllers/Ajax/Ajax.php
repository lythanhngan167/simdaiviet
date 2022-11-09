<?php

namespace RtclStore\Controllers\Ajax;

class Ajax {

    public static function init()
    {
        Admin::init();
        FrontEnd::init();
        Membership::init();
        ApiRequest::init();
        LoadMore::init();
    }

}