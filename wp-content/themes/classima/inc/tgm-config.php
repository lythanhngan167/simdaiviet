<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.7.8
 */

namespace radiustheme\Classima;

class TGM_Config {
	
	public $base;
	public $path;

	public function __construct() {
		$this->base = 'classima';
		$this->path = Constants::$theme_plugins_dir;

		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
	}

	public function register_required_plugins(){
		$plugins = array(
			// Bundled
			array(
				'name'         => 'Classima Core',
				'slug'         => 'classima-core',
				'source'       => 'classima-core.zip',
				'required'     =>  true,
				'version'      => '1.11'
			),
			array(
				'name'         => 'RT Framework',
				'slug'         => 'rt-framework',
				'source'       => 'rt-framework.zip',
				'required'     =>  true,
				'version'      => '2.8'
			),
			array(
				'name'         => 'RT Demo Importer',
				'slug'         => 'rt-demo-importer',
				'source'       => 'rt-demo-importer.zip',
				'required'     =>  false,
				'version'      => '4.3.1'
			),
			array(
				'name'         => 'Classified Listing Pro',
				'slug'         => 'classified-listing-pro',
				'source'       => 'classified-listing-pro.zip',
				'required'     =>  true,
				'version'      => '2.0.21'
			),
			array(
				'name'         => 'Classified Listing Store',
				'slug'         => 'classified-listing-store',
				'source'       => 'classified-listing-store.zip',
				'required'     =>  false,
				'version'      => '1.4.21'
			),
			array(
				'name'         => 'Review Schema Pro',
				'slug'         => 'review-schema-pro',
				'source'       => 'review-schema-pro.zip',
				'required'     =>  false,
				'version'      => '1.0.4'
			),

			// Repository
            array(
                'name'     => 'Classified Listing',
                'slug'     => 'classified-listing',
                'required' => true,
            ),
			array(
				'name'     => 'Redux Framework',
				'slug'     => 'redux-framework',
				'required' => true,
			),
			array(
				'name'     => 'Elementor Page Builder',
				'slug'     => 'elementor',
				'required' => true,
			),
			array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false,
			),
			array(
				'name'     => 'Review Schema',
				'slug'     => 'review-schema',
				'required' => false,
			),
		);

		$config = array(
			'id'           => $this->base,            // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => $this->path,              // Default absolute path to bundled plugins.
			'menu'         => $this->base . '-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                    // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}
}

new TGM_Config;