<?php

namespace Rtrs\Controllers\Admin; 

use Rtrs\Controllers\Admin\Meta\MetaController; 

class AdminController {
    public function __construct() {  
        new RegisterPostType;  
        new MetaController();
        new ScriptLoader(); 
        new AdminSettings();
        new Notifications();
        ReviewSettings::get_instance();  
    } 
}