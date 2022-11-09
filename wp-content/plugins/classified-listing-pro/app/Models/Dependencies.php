<?php

namespace RtclPro\Models;

use Rtcl;

class Dependencies {
	const MIN_RTCL = '2.2.18';

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
						'plugin'    => 'classified-listing-pro',
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
				$sep = ' ' . esc_html__( 'and', 'classified-listing-pro' ) . ' ';
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

		<div class="notice notice-error">
			<p><?php echo wp_kses( sprintf( __( '<strong>Classified Listing Pro</strong> is enabled but not effective. It requires %s in order to work.', 'classified-listing-pro' ), $missing ), [ 'strong' => [],
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
		$message = wp_kses( __( sprintf( '<strong>Classified Listing Pro</strong> is enabled but not effective. It is not compatible with <a class="thickbox open-plugin-details-modal" href="%1$s">Classified Listing</a> versions prior %2$s.',
			$link,
			self::MIN_RTCL
		), 'classified-listing-pro' ), [ 'strong' => [], 'a' => [ 'href' => true, 'class' => true ] ] );

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}
}