<?php
/**
 * Main FrontendHook  Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  review-schema-pro
 *
 * @since    1.0.0
 */

namespace Rtrsp\Controllers\Frontend;

use Rtrs\Helpers\Functions;
use Rtrsp\Traits\SingletonTrait;
/**
 * FrontendHook class
 */
class FrontendHook {
	/**
	 * Singletone.
	 */
	use SingletonTrait;
	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'rtseo_snippet_others_schema_output', [ &$this, 'schema_output' ], 10, 4 );
	}
	/**
	 * Schema output function
	 *
	 * @param [type] $schemaCat schemaCat.
	 * @param [type] $metaData metaData.
	 * @param [type] $without_script without_script.
	 * @param [type] $schema_obj schema_obj.
	 * @return void
	 */
	public function schema_output( $schemaCat, $metaData, $without_script, $schema_obj ) {

		$html = null;
		switch ( $schemaCat ) {
			case 'tv_series':
				$output             = [];
				$output['@context'] = 'https://schema.org';
				$output['@type']    = 'TVSeries';
				if ( ! empty( $metaData['name'] ) ) {
					$output['name'] = esc_html( $metaData['name'] );
				}
				$author_type = esc_html( $metaData['author-type'] ) ?? 'Person';
				if ( ! empty( $metaData['author'] ) ) {
					$output['author'] = [
						'@type' => $author_type,
						'name'  => esc_html( $metaData['author'] ),
					];
				}

				if ( ! empty( $metaData['actor'] ) ) {
					foreach ( $metaData['actor'] as $value ) {
						if ( ! empty( $value['actor-name'] ) ) {
							$output['actor'][] = [
								'@type' => 'Person',
								'name'  => esc_html( $value['actor-name'] ),
							];
						}
					}
				}
				if ( ! empty( $metaData['description'] ) ) {
					$output['description'] = esc_html( $metaData['description'] );
				}
				if ( ! empty( $metaData['season'] ) ) {
					foreach ( $metaData['season'] as $value ) {
						$tvsession = [
							'@type'            => 'TVSeason',
							'datePublished'    => esc_html( $value['date-published'] ),
							'name'             => esc_html( $value['season-name'] ),
							'numberOfEpisodes' => esc_html( $value['number-of-episodes'] ),
						];
						$episode   = [];
						if ( ! empty( $value['episode-name'] ) ) {
							$episode['name'] = esc_html( $value['episode-name'] );
						}
						if ( ! empty( $value['episode-number'] ) ) {
							$episode['episodeNumber'] = esc_html( $value['episode-number'] );
						}
						if ( ! empty( $episode ) ) {
							$tvsession['episode'] = array_merge(
								[
									'@type' => 'TVEpisode',
								],
								$episode
							);
						}
						$output['containsSeason'][] = $tvsession;
					}
				}
				$html .= $schema_obj->getJsonEncode( apply_filters( 'rtseo_snippet_tv_series', $output, $metaData ) );

			default:
				break;
		}
		echo wp_kses(
			$html,
			[
				'script' => [
					'type' => [],
				],
			]
		);
	}

}
