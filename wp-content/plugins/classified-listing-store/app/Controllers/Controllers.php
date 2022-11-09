<?php

namespace RtclStore\Controllers;

use RtclStore\Controllers\Ajax\Ajax;
use RtclStore\Controllers\Hooks\Hooks;

class Controllers {

	public function __construct() {
		Hooks::init();
		Ajax::init();
		new Script();
	}

}
