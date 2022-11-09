<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Link;
use \WP_Query;
use Rtcl\Widgets\Filter;
use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) exit;

if (class_exists(Filter::class)) {
    class Listing_Filter_Functions extends Filter {

        public function __construct($data) {

            global $wp;
            $queried_object = get_queried_object();

            $this->instance = array(
                'search_by_category'           => 1,
                'show_icon_image_for_category' => isset($data['show_icon_image_for_category']) && "yes" === $data['show_icon_image_for_category'],
                'search_by_location'           => 1,
                'search_by_ad_type'            => '',
                'search_by_custom_fields'      => '',
                'search_by_price'              => '',
                'hide_empty'                   => isset($data['hide_empty']) && "yes" === $data['hide_empty'],
                'show_count'                   => isset($data['show_count']) && "yes" === $data['show_count'],
                'ajax_load'                    => isset($data['ajax_load']) && "yes" === $data['ajax_load'],
            );

            foreach ([rtcl()->location, rtcl()->category] as $taxonomy) {
                if (is_a($queried_object, \WP_Term::class) && $queried_object->taxonomy === $taxonomy) {
                    $this->instance['current_taxonomy'][$taxonomy] = $queried_object;
                } else {
                    $q_term = '';
                    if (isset($wp->query_vars[$taxonomy])) {
                        $q_term = explode('/', $wp->query_vars[$taxonomy]);
                        $q_term = end($q_term);
                    }
                    $this->instance['current_taxonomy'][$taxonomy] = $q_term ? get_term_by('slug', $q_term, $taxonomy) : '';
                }
            }
        }
    }
}

class Listing_Term_List extends Custom_Widget_Base
{

    public function __construct($data = [], $args = null) {
        $this->rt_name = __('Listing Term List', 'classima-core');
        $this->rt_base = 'rt-listing-term-list';
        parent::__construct($data, $args);
    }

    public function rt_fields() {
        $fields = array(
            array(
                'mode'  => 'section_start',
                'id'    => 'sec_general',
                'label' => __('General', 'classima-core'),
            ),
            array(
                'type'    => Controls_Manager::TEXTAREA,
                'id'      => 'title',
                'label'   => __('Title', 'classima-core'),
                'default' => 'Lorem Ipsum',
            ),
            array(
                'type'    => Controls_Manager::SELECT2,
                'id'      => 'term_type',
                'label'   => __('Term Type', 'classima-core'),
                'options' => array(
                    'cat' => __('Category', 'classima-core'),
                    'loc' => __('Location', 'classima-core'),
                ),
                'default' => 'cat',
            ),
            array(
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'show_icon_image_for_category',
                'label'     => __('Display Category image/icon', 'classima-core'),
                'label_on'  => __('On', 'classima-core'),
                'label_off' => __('Off', 'classima-core'),
                'default'   => 'yes',
                'condition' => array('term_type' => array('cat')),
            ),
            array(
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'hide_empty',
                'label'     => __('Hide empty Category/Location', 'classima-core'),
                'label_on'  => __('On', 'classima-core'),
                'label_off' => __('Off', 'classima-core'),
                'default'   => '',
            ),
            array(
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'show_count',
                'label'     => __('Display count for Category/Location', 'classima-core'),
                'label_on'  => __('On', 'classima-core'),
                'label_off' => __('Off', 'classima-core'),
                'default'   => 'yes',
            ),
            array(
                'type'      => Controls_Manager::SWITCHER,
                'id'        => 'ajax_load',
                'label'     => __('Ajax load for Category / Location<br> to increase PageSpeed.', 'classima-core'),
                'label_on'  => __('On', 'classima-core'),
                'label_off' => __('Off', 'classima-core'),
                'default'   => 'yes',
            ),
            array(
                'mode' => 'section_end',
            ),
        );
        return $fields;
    }

    protected function render() {
        $data = $this->get_settings();

        if (class_exists(Filter::class)) {
            $filter = new Listing_Filter_Functions($data);

            if ($data['term_type'] == 'loc') {
                $data['filter'] = $filter->get_location_filter();
            } else {
                $data['filter'] = $filter->get_category_filter();
            }
        }

        $template = 'view';

        return $this->rt_template($template, $data);
    }
}