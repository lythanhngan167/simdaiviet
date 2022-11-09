<?php

namespace RtclStore\Models;

use Rtcl;
use RtclPro;

final class Dependencies {
	const MIN_RTCL = '2.2.18';
	const MIN_RTCL_PRO = '2.0.21';

	private static $singleton = false;
	private $missing = [];
	/**
	 * @var bool
	 */
	private $allOk = true;

	private final function __construct() {
	}


	/**
	 * Fetch an instance of the class.
	 */
	public static function getInstance() {
		if ( self::$singleton === false ) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * @return bool
	 */
	public function check() {

		if ( ! class_exists( Rtcl::class ) ) {
			$link                                = esc_url(
				add_query_arg(
					[
						'tab'       => 'plugin-information',
						'plugin'    => 'classified-listing',
						'TB_iframe' => 'true',
						'width'     => '640',
						'height'    => '500',
					], admin_url( 'plugin-install.php' )
				)
			);
			$this->missing['Classified Listing'] = $link;
			$this->allOk                         = false;
		} elseif ( defined( 'RTCL_VERSION' ) && version_compare( RTCL_VERSION, self::MIN_RTCL, '<' ) ) {
			add_action( 'admin_notices', [ $this, '_old_rtcl_warning' ] );
			$this->allOk = false;
		}

		if ( ! class_exists( RtclPro::class ) ) {
			$this->missing['Classified Listing Pro'] = 'https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/';
			$this->allOk                             = false;
		} elseif ( defined( 'RTCL_PRO_VERSION' ) && version_compare( RTCL_PRO_VERSION, self::MIN_RTCL_PRO, '<' ) ) {
			add_action( 'admin_notices', [ $this, '_old_rtcl_pro_warning' ] );
			$this->allOk = false;
		}

		if ( ! empty( $this->missing ) ) {
			add_action( 'admin_notices', [ $this, '_missing_plugins_warning' ] );
		}

		return $this->allOk;
	}


	/**
	 * Adds admin notice.
	 */
	public function _missing_plugins_warning() {

		$missing = '';
		$counter = 0;
		foreach ( $this->missing as $title => $url ) {
			$counter ++;
			if ( $counter == sizeof( $this->missing ) ) {
				$sep = '';
			} elseif ( $counter == sizeof( $this->missing ) - 1 ) {
				$sep = ' ' . __( 'and', 'classified-listing-store' ) . ' ';
			} else {
				$sep = ', ';
			}
			if ( $title === "Classified Listing" ) {
				$missing .= '<a class="thickbox open-plugin-details-modal" href="' . $url . '">' . $title . '</a>' . $sep;
			} else {
				$missing .= '<a href="' . $url . '">' . $title . '</a>' . $sep;
			}
		}
		?>

        <div class="message error">
            <p><?php echo wp_kses( sprintf( __( '<strong>Classified Listing Store</strong> is enabled but not effective. It requires %s in order to work.', 'classified-listing-store' ), $missing ), [ 'strong' => [],
			                                                                                                                                                                                            'a'      => [
				                                                                                                                                                                                            'href'  => true,
				                                                                                                                                                                                            'class' => true
			                                                                                                                                                                                            ]
				] ); ?></p>
        </div>
		<?php
	}

	public function _old_rtcl_warning() {
		$link    = esc_url(
			add_query_arg(
				[
					'tab'       => 'plugin-information',
					'plugin'    => 'classified-listing',
					'TB_iframe' => 'true',
					'width'     => '640',
					'height'    => '500',
				], admin_url( 'plugin-install.php' )
			)
		);
		$message = wp_kses( __( sprintf( '<strong>Classified Listing Store</strong> is enabled but not effective. It is not compatible with <a class="thickbox open-plugin-details-modal" href="%1$s">Classified Listing</a> versions prior %2$s.',
			$link,
			self::MIN_RTCL
		), 'classified-listing-store' ), [ 'strong' => [], 'a' => [ 'href' => true, 'class' => true ] ] );

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}

	public function _old_rtcl_pro_warning() {
		$message = wp_kses( __( sprintf( '<strong>Classified Listing Store</strong> is enabled but not effective. It is not compatible with <a href="%1$s">Classified Listing pro</a> versions prior %2$s.',
			esc_url( 'https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/' ),
			self::MIN_RTCL_PRO
		), 'classified-listing-store' ), [ 'strong' => [], 'a' => [ 'href' => true ] ] );

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}
}