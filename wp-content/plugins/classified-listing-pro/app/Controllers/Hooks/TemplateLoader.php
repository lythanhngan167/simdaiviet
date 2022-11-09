<?php

namespace RtclPro\Controllers\Hooks;


class TemplateLoader
{
    /**
     * Is ClassifiedListing support defined?
     *
     * @var boolean
     */
    private static $theme_support = false;

    static function init() {
        self::$theme_support = current_theme_supports('rtcl');
        if (self::$theme_support) {
            add_filter('comments_template', [__CLASS__, 'comments_template_loader']);
        }
    }

    /**
     * Load comments template.
     *
     * @param string $template template to load.
     *
     * @return string
     */
    public static function comments_template_loader($template) {

        if (get_post_type() !== rtcl()->post_type) {
            return $template;
        }
        $file = 'single-rtcl_listing-reviews.php';
        $checkFiles = array(
            $file,
            "classified-listing/$file"
        );

        if ($template_file = locate_template($checkFiles)) {
            return $template_file;
        } else {
            $file = trailingslashit(rtclPro()->plugin_path()) . 'templates/single-rtcl_listing-reviews.php';
            if (file_exists($file)) {
                return $file;
            }
            return $template;
        }
    }

}