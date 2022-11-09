<?php

namespace RtclStore\Controllers\Hooks;

class Hooks
{

    public static function init() {
        Init::init();
        CustomHooks::init();
        StoreReviews::init();
        GatewayHooks::init();
        MembershipHook::init();
        StatusChange::init();
        TemplateRedirect::init();
        NotificationHooks::init();
        StoreEmailHooks::init();
        FilterHooks::init();
        ActionHooks::init();
    }

}