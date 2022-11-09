<?php

namespace Rtrs\Controllers\Ajax;

class AjaxController {

    public function __construct() { 
        new Shortcode(); 
        new Review(); 
        new Migration(); 
    }
}