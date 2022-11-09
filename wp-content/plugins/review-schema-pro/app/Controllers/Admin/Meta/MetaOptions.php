<?php
/**
 * Main MetaOptions  Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  review-schema-pro
 *
 * @since    1.0.0
 */

namespace Rtrsp\Controllers\Admin\Meta;

use Rtrsp\Traits\SingletonTrait;

/**
 * MetaOptions class
 */
class MetaOptions {
	/**
	 *
	 */
	use SingletonTrait;
	/**
	 * Init function.
	 *
	 * @return void
	 */
	public function init() {
		// Tab option filter .
		add_filter( 'rtrs_section_schema_fields', [ $this, 'section_schema_fields' ] );
	}
	/**
	 * Undocumented function
	 *
	 * @param array $schema_fields schema fields.
	 * @return array
	 */
	public function section_schema_fields( $schema_fields ) {
		$schema_fields[] = $this->tv_series_schema_fields();
		return $schema_fields;
	}
	/**
	 * Undocumented function
	 *
	 * @return array
	 */
	public function tv_series_schema_fields() {
		$prefix        = 'rtrs_';
		$schema_fields = [
			'type'        => 'group',
			'name'        => $prefix . 'tv_series_schema',
			'id'          => 'rtrs-tv_series_schema',
			'holderClass' => 'rtrs-hidden rtrs-schema-field',
			'label'       => esc_html__( 'TV Series schema', 'review-schema-pro' ),
			'fields'      => [
				[
					'id'     => 'tv_series',
					'type'   => 'auto-fill',
					'is_pro' => true,
					'label'  => esc_html__( 'Auto Fill', 'review-schema-pro' ),
				],
				[
					'name'    => 'status',
					'type'    => 'tab',
					'label'   => esc_html__( 'Status', 'review-schema-pro' ),
					'default' => 'show',
					'options' => [
						'show' => esc_html__( 'Show', 'review-schema-pro' ),
						'hide' => esc_html__( 'Hide', 'review-schema-pro' ),
					],
				],
				[
					'name'     => 'name',
					'type'     => 'text',
					'label'    => esc_html__( 'Name', 'review-schema-pro' ),
					'required' => true,
				],
				[
					'name'  => 'image',
					'type'  => 'image',
					'label' => esc_html__( 'Image', 'review-schema' ),
				],
				[
					'name'    => 'author-type',
					'type'    => 'select',
					'label'   => esc_html__( 'Author Type', 'review-schema-pro' ),
					'empty'   => esc_html__( 'Select one', 'review-schema-pro' ),
					'options' => [
						'Person'       => esc_html__( 'Person', 'review-schema-pro' ),
						'Organization' => esc_html__( 'Organization', 'review-schema-pro' ),
					],
				],
				[
					'name'        => 'author',
					'type'        => 'text',
					'label'       => esc_html__( 'Author', 'review-schema-pro' ),
					'placeholder' => esc_html__( 'Author Name', 'review-schema-pro' ),
				],
				[
					'type'   => 'group',
					'name'   => 'actor',
					'label'  => esc_html__( 'Actor\'s', 'review-schema-pro' ),
					'fields' => [
						[
							'name'  => 'actor-name',
							'type'  => 'text',
							'label' => esc_html__( 'Actor Name', 'review-schema-pro' ),
						],
					],
				],
				[
					'name'     => 'description',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Description', 'review-schema-pro' ),
					'required' => true,
				],
				[
					'type'   => 'group',
					'name'   => 'season',
					'label'  => esc_html__( 'Season', 'review-schema-pro' ),
					'fields' => [
						[
							'name'  => 'season-name',
							'type'  => 'text',
							'label' => esc_html__( 'Season Name', 'review-schema-pro' ),
						],
						[
							'name'  => 'date-published',
							'type'  => 'text',
							'label' => esc_html__( 'Published Date', 'review-schema-pro' ),
							'class' => 'rtrs-date',
							'desc'  => esc_html__( 'Like this: 2021-08-25 14:20:00', 'review-schema-pro' ),
						],
						[
							'name'  => 'number-of-episodes',
							'type'  => 'number',
							'label' => esc_html__( 'Number Of Episodes', 'review-schema-pro' ),
						],
						[
							'name'  => 'episode-name',
							'type'  => 'text',
							'label' => esc_html__( 'Episode Name', 'review-schema-pro' ),
						],
						[
							'name'  => 'episode-number',
							'type'  => 'number',
							'label' => esc_html__( 'Episode Number', 'review-schema-pro' ),
						],
					],
				],
			],
		];
		return $schema_fields;
	}


}
