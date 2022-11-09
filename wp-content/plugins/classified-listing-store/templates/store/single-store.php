<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 * @deprecated 1.2.31
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

Functions::get_template('single-store', '', '', rtclStore()->get_plugin_template_path());
