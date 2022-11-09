<?php
/**
 * @package ClassifiedListing/Templates
 * @version 1.4.0
 */

use Rtcl\Helpers\Functions as RtclFunctions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

RtclFunctions::get_template('archive-store', '', '', rtclStore()->get_plugin_template_path());
