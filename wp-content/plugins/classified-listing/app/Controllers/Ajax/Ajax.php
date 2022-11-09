<?php

namespace Rtcl\Controllers\Ajax;

class Ajax {
	public function __construct() {
		new ListingAdminAjax();
		new AjaxGallery();
		new Checkout();
		new AjaxCFG();
		new PublicUser();
		new Import();
		new AjaxListingType();
		InlineSearchAjax::init();
	}
}