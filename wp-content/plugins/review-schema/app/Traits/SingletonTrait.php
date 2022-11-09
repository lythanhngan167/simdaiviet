<?php 

namespace Rtrs\Traits; 

trait SingletonTrait
{
    /**
     * Store the singleton object.
     */
    private static $singleton = false;

    /**
     * Create an inaccessible constructor.
     */
    private function __construct() {
        $this->__instance();
    }

    private function __instance() {
    }

    /**
     * Fetch an instance of the class.
     */
    public static function getInstance() {
        if (self::$singleton === false) {
            self::$singleton = new self();
        }

        return self::$singleton;
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {
    }

    /**
     * Prevent unserializing.
     */
    public function __wakeup() {
    }
}