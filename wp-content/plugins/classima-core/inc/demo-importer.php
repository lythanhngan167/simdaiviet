<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.1
 */

namespace radiustheme\Classima_Core;

use \FW_Ext_Backups_Demo;
use \WPCF7_ContactFormTemplate;

if ( ! defined( 'ABSPATH' ) ) exit;

class Demo_Importer {

	public function __construct() {
		add_filter( 'plugin_action_links_rt-demo-importer/rt-demo-importer.php', array( $this, 'add_action_links' ) ); // Link from plugins page 
		add_filter( 'rt_demo_installer_warning', array( $this, 'data_loss_warning' ) );
		add_filter( 'fw:ext:backups-demo:demos', array( $this, 'demo_config' ) );
		add_action( 'fw:ext:backups:tasks:success:id:demo-content-install', array( $this, 'after_demo_install' ) );
		//add_filter( 'fw:ext:backups:add-restore-task:image-sizes-restore', '__return_false' ); // Enable it to skip image restore step
	}

	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . esc_url( admin_url( 'tools.php?page=fw-backups-demo-content' ) ) . '">'.__( 'Install Demo Contents', 'classima-core' ).'</a>',
		);
		return array_merge( $links, $mylinks );
	}

	public function data_loss_warning( $links ) {
		$html  = '<div style="margin-top:20px;color:#f00;font-size:20px;line-height:1.3;font-weight:600;margin-bottom:40px;border-color: #f00;border-style: dashed;border-width: 1px 0;padding:10px 0;">';
		$html .= __( 'Warning: All your old data will be lost if you install One Click demo data from here, so it is suitable only for a new website.', 'classima-core');
		$html .= '</div>';
		return $html;
	}

	public function demo_config( $demos ) {
		$demos_array = array(
			'demo1' => array(
				'title' => __( 'Home 1', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot1.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/',
			),
			'demo2' => array(
				'title' => __( 'Home 2', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot2.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-2/',
			),
			'demo3' => array(
				'title' => __( 'Home 3', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot3.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-3/',
			),
			'demo4' => array(
				'title' => __( 'Home 4', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot4.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-4/',
			),
			'demo5' => array(
				'title' => __( 'Home 5', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot5.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-5/',
			),
			'demo6' => array(
				'title' => __( 'Home 6', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot6.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-6/',
			),
			'demo7' => array(
				'title' => __( 'Home 7', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot7.png', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-7/',
			),
			'demo8' => array(
				'title' => __( 'Home 8', 'classima-core' ),
				'screenshot' => plugins_url( 'screenshots/screenshot8.jpg', dirname(__FILE__) ),
				'preview_link' => 'https://www.radiustheme.com/demo/wordpress/themes/classima/home-8/',
			),
		);

		$download_url = 'http://demo.radiustheme.com/wordpress/demo-content/classima/';

		foreach ($demos_array as $id => $data) {
			$demo = new FW_Ext_Backups_Demo($id, 'piecemeal', array(
				'url' => $download_url,
				'file_id' => $id,
			));
			$demo->set_title($data['title']);
			$demo->set_screenshot($data['screenshot']);
			$demo->set_preview_link($data['preview_link']);

			$demos[ $demo->get_id() ] = $demo;

			unset($demo);
		}

		return $demos;
	}

	public function after_demo_install( $collection ){
		// Update front page id
		$demos = array(
			'demo1'  => 767,
			'demo2'  => 802,
			'demo3'  => 826,
			'demo4'  => 1725,
			'demo5'  => 1793,
			'demo6'  => 1897,
			'demo7'  => 2247,
			'demo8'  => 2404,
		);

		$data = $collection->to_array();

		foreach( $data['tasks'] as $task ) {
			if( $task['id'] == 'demo:demo-download' ){
				$demo_id = $task['args']['demo_id'];
				$page_id = $demos[$demo_id];
				update_option( 'page_on_front', $page_id );
				flush_rewrite_rules();
				break;
			}
		}

		// Update contact form 7 From email
		$cf7ids = array( 339 );
		foreach ( $cf7ids as $cf7id ) {
			$mail = get_post_meta( $cf7id, '_mail', true );
			if ( class_exists( 'WPCF7_ContactFormTemplate' ) ) {
				$pattern = "/<[^@\s]*@[^@\s]*\.[^@\s]*>/"; // <email@email.com>
				$replacement = '<'. WPCF7_ContactFormTemplate::from_email().'>';
				$mail['sender'] = preg_replace($pattern, $replacement, $mail['sender']);
			}
			update_post_meta( $cf7id, '_mail', $mail );		
		}

		// Update post author id
		global $wpdb;
		$id = get_current_user_id();
		$query = "UPDATE $wpdb->posts SET post_author = $id";
		$wpdb->query($query);
	}
}

new Demo_Importer;