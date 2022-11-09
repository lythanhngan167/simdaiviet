<?php 
// Security check
defined('ABSPATH') || die();

interface RTOptionFramework{

    /**
     * @description: Get option from id: string
    */
    public function get_option(string $id);

}