<?php

namespace Rtrs\Controllers\Admin; 
use Rtrs\Helpers\Functions;
use Rtrs\Models\Schema;

class ScriptLoader {

	private $suffix;
	private $version;
	private $ajaxurl;
	private static $wp_localize_scripts = [];

	function __construct() {

		$this->suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';    
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : rtrs()->version();

		$this->ajaxurl = admin_url( 'admin-ajax.php' ); 

		add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ), 99 );
		add_action( 'admin_init', array( $this, 'register_admin_script' ), 1 ); 
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_script_setting_page' ) );  
 
		add_action( 'wp_head', array( $this, 'google_rich_snippet' ) ); 
		
		add_filter('comment_post_redirect', [$this, 'redirect_after_review']);
	}     
	
	//auto redirect to first review page instead of last
	function redirect_after_review( $location ) {
		return esc_url( $_SERVER["HTTP_REFERER"] . "#comments" );
	}

	function register_script_both_end() {   
		wp_register_style( 'rtrs-app', rtrs()->get_assets_uri( "css/app{$this->suffix}.css" ), array(), $this->version );    
		wp_register_script( 'rtrs-app', rtrs()->get_assets_uri( "js/app{$this->suffix}.js" ), array('jquery'), $this->version, true );  

		// Dynamic Css.
		$upload_dir = wp_upload_dir(); 
		$cssFile = $upload_dir['basedir'] . '/review-schema/sc.css'; 
		if( file_exists( $cssFile ) ) { 
			$version = filemtime( $cssFile ) ;
			wp_register_style( 'rtrs-sc', set_url_scheme( $upload_dir['baseurl'] ) . '/review-schema/sc.css', array('rtrs-app') , $version );
		}
	}

	function register_script() {
		$this->register_script_both_end();   

		if ( is_singular() ) {  

			$p_meta = Functions::getMetaByPostType( get_post_type() );
			if ( !$p_meta ) return;
			
			if ( ! empty( $p_meta['rtrs_support'][0] ) && 'schema' === $p_meta['rtrs_support'][0] ) return;

			wp_enqueue_style( 'rtrs-app' );         
			$recaptcha = ( isset( $p_meta['recaptcha'] ) && $p_meta['recaptcha'][0] == '1' ); 
            $recaptcha_sitekey = rtrs()->get_options('rtrs_review_settings', array( 'recaptcha_sitekey', '' ));  
			if ( $recaptcha && $recaptcha_sitekey ) {
				wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render='.esc_attr( $recaptcha_sitekey ) ); 
			}  
			wp_enqueue_script( 'rtrs-app' );    

			$pros_cons_limit = isset( $p_meta['pros_cons_limit'] ) ? esc_attr( $p_meta['pros_cons_limit'][0] ) : 3; 
			$pro_cons_limit = ( !function_exists('rtrsp') ) ? 3 : $pros_cons_limit;
			$free_version_text = ( !function_exists('rtrsp') ) ? esc_html__( ' for free version.', 'review-schema' ) : '';

			wp_localize_script('rtrs-app', 'rtrs',
				array(
					'pro' => function_exists('rtrsp'),
					'recaptcha' => $recaptcha,
					'recaptcha_sitekey' => $recaptcha_sitekey,
					'highlight' => esc_html__( 'Highlight?', 'review-schema' ),
					'remove_highlight' => esc_html__( 'Remove Highlight?', 'review-schema' ), 
					'loading' => esc_html__( 'Loading...', 'review-schema' ),
					'edit' => esc_html__( 'Edit', 'review-schema' ),
					'upload_img' => esc_html__( 'Upload Image', 'review-schema' ),
					'upload_video' => esc_html__( 'Upload Video', 'review-schema' ),
					'sure_txt' => esc_html__( 'Are you sure to delete?', 'review-schema' ),
					'pro_label' => esc_html__( '[PRO]', 'review-schema' ),
					'pro_cons_limit' => $pro_cons_limit,
					'pros_alt_txt' => $pro_cons_limit . esc_html__( ' pros field are allowed', 'review-schema' ) . $free_version_text,
					'cons_alt_txt' => $pro_cons_limit . esc_html__( ' cons field are allowed', 'review-schema' ) . $free_version_text,
					'write_txt' => esc_html__( 'Write here!', 'review-schema' ),
					// 'nonceID' => rtrs()->getNonceId(),
					// 'nonce'   => rtrs()->getNonceText(),
					'nonce'   => wp_create_nonce( rtrs()->getNonceId() ),
					'ajaxurl' => admin_url('admin-ajax.php'),
					'post_id' => get_the_ID(), 
					'current_page' => get_query_var( 'cpage' ) ? get_query_var( 'cpage' ) : 1, 
				)
			); 
		}   

		wp_enqueue_style( 'rtrs-sc' );
	}

	function register_admin_script() {
		$this->register_script_both_end(); 
		
		wp_register_style( 'select2', rtrs()->get_assets_uri( "vendor/select2/select2.min.css" ), array(), $this->version ); 
		wp_register_style( 'rtrs-admin', rtrs()->get_assets_uri( "css/admin{$this->suffix}.css" ), array(), $this->version );

		wp_register_script( 'select2', rtrs()->get_assets_uri( "vendor/select2/select2.min.js" ), array( 'jquery' ), $this->version, true );  
		wp_register_script( 'rtrs-admin', rtrs()->get_assets_uri( "js/admin{$this->suffix}.js" ), array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ), $this->version, true );  
	}

	function load_admin_script_setting_page() {
		global $pagenow, $post_type;
		
		wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style( 'select2' );  
		wp_enqueue_style( 'rtrs-admin' ); 
		 
		wp_enqueue_script( 'select2' );  
		wp_enqueue_script( 'rtrs-admin' ); 
		wp_enqueue_media(); 

		wp_localize_script('rtrs-admin', 'rtrs',
			array(
				'pro' => function_exists('rtrsp'),
				'nonceID' => rtrs()->getNonceId(),
				'nonce'   => rtrs()->getNonceText(),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'sure_txt' => esc_html__( 'Are you sure to delete?', 'review-schema' ),
				'criteria_alt_txt' => esc_html__( '3 criteria field are allowed for free version.', 'review-schema' ),
				'pros_alt_txt' => esc_html__( '3 pros field are allowed for free version.', 'review-schema' ),
				'cons_alt_txt' => esc_html__( '3 cons field are allowed for free version.', 'review-schema' ),
				'multiple_txt' => esc_html__( 'Multiple schema are not allowed in free version.', 'review-schema' ),
				'write_txt' => esc_html__( 'Write here!', 'review-schema' ),
				'at_least_txt' => esc_html__( 'At least one field require', 'review-schema' ),
				'criteria_rating' => esc_html__( '3 criteria rating field are allowed for free version.', 'review-schema' ),
				'remove_img' => esc_html__( 'Remove Image', 'review-schema' ),
			)
		); 
	}
 
	function google_rich_snippet() { 
		$snippet = new Schema;
		$rich_snippet = $snippet->rich_snippet();
		if ( $rich_snippet ) {  
			echo $rich_snippet; //this data already escaped XSS issues
		}
	}   
}