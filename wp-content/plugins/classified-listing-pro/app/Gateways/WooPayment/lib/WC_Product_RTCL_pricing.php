<?php


namespace RtclPro\Gateways\WooPayment\lib;

use WC_Product;


class WC_Product_RTCL_pricing extends WC_Product
{

    /**
     * @var array|bool|null|\WP_Post
     */
    public $post = false;

    public function __construct($product = 0) {
        if (is_numeric($product) && $product > 0) {
            $this->set_id($product);
        } elseif ($product instanceof self) {
            $this->set_id(absint($product->get_id()));
        } elseif (!empty($product->ID)) {
            $this->set_id(absint($product->ID));
        }
        $this->post = get_post($this->id);
    }

    public function __get($key) {
        if ($key === 'id') {
            return $this->get_id();
        } else if ($key === 'post') {
            return get_post($this->get_id());
        }

        return parent::__get($key);
    }

    /**
     * Get Price Description
     *
     * @param string $context
     *
     * @return int
     */
    public function get_price($context = 'view') {
        $pricing = rtcl()->factory->get_pricing($this->post->ID);

        return $pricing->exists() ? $pricing->getPrice() : 0;
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_name($context = 'view') {
        return get_the_title($this->id);
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public function exists($context = 'view') {
        return $this->post && (get_post_type($this->post->ID) == rtcl()->post_type_pricing) && ($this->post->post_status == 'publish');
    }

    /**
     * Check if a product is purchasable
     */
    public function is_purchasable() {
        return rtcl()->factory->get_pricing($this->post->ID);
    }

    public function is_sold_individually() {
        return true;
    }

    /**
     *
     * @return bool
     */
    public function is_virtual() {
        return apply_filters('rtcl_wc_product_pricing_is_virtual', true, $this);
    }

    /**
     * @return bool
     */
    public function is_downloadable() {
        return apply_filters('rtcl_wc_product_pricing_is_downloadable', true, $this);
    }

}