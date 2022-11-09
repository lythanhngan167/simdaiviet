<?php

namespace Rtrs\Models;

use Rtrs\Helpers\Functions;

class Schema {
	public function __construct() {
		add_action('wp_footer', [$this, 'footer'], 999);
	}

	/**
	 * Get social links array.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_social() {
		$social_links = rtrs()->get_options('rtrs_schema_social_profiles_settings', ['social_profiles', []]);

		// Remove empty fields
		$social = [];
		foreach ($social_links as $profile) {
			if ($profile['url']) {
				$social[] = $profile['url'];
			}
		}

		return $social;
	}

	/**
	 * Get Get corporate contacts types array.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function corporate_contacts_types() {
		$corporate_contacts	= [];

		$corporate_contacts = rtrs()->get_options('rtrs_schema_corporate_contacts_settings');

		$contact_type = isset($corporate_contacts['type']) ? esc_attr($corporate_contacts['type']) : '';

		if ($contact_type) {
			// Remove dashes and replace it with a space
			$contact_type = str_replace('_', ' ', $contact_type);

			$corporate_contacts = [
				'@type'			          => 'ContactPoint',	// default required value
				'contactType'	      => $contact_type,
				'telephone'		       => ($corporate_contacts['telephone']) ? $corporate_contacts['telephone'] : '',
				'url'			            => ($corporate_contacts['url']) ? esc_url($corporate_contacts['url']) : '',
				'email'			          => ($corporate_contacts['email']) ? esc_html($corporate_contacts['email']) : '',
				'contactOption'	    => ($corporate_contacts['contactOption']) ? esc_html($corporate_contacts['contactOption']) : '',
				'areaServed'	       => ($corporate_contacts['areaServed']) ? $corporate_contacts['areaServed'] : '',
				'availableLanguage'	=> ($corporate_contacts['availableLanguage']) ? $corporate_contacts['availableLanguage'] : '',
			];
		}

		return $corporate_contacts;
	}

	/**
	 * Google sitelink searchbox.
	 *
	 * @return mixed
	 */
	public function footer() {
		$settings = get_option('rtrs_schema_settings');

		// site schema
		$enable_site_schema = false;

		$site_schema = ! empty($settings['site_schema']) ? $settings['site_schema'] : 'home_page';
		if ($site_schema !== 'off') {
			if ($site_schema == 'home_page') {
				if (is_home() || is_front_page()) {
					$enable_site_schema = true;
				}
			} elseif ($site_schema == 'all') {
				$enable_site_schema = true;
			}
		}

		if ($enable_site_schema) {
			$helper = new Functions();

			//sitelink searchbox
			$settings_sitelink = get_option('rtrs_schema_sitelink_settings');
			if (isset($settings_sitelink['sitelink_searchbox']) && $settings_sitelink['sitelink_searchbox'] == 'yes') {
				$html                        = null;
				$metaData                    = [];
				$metaData['@context']        = 'http://schema.org/';
				$metaData['@type']           = 'WebSite';
				$metaData['url']             = trailingslashit(get_home_url());
				$metaData['potentialAction'] = [
					'@type'       => 'SearchAction',
					'target'      => trailingslashit(get_home_url()) . '?s={query}',
					'query-input' => 'required name=query',
				];
				echo $this->getJsonEncode(apply_filters('rtseo_sitelink_searchbox', $metaData));
			}

			$metaData       = $settings;
			$category       = ! empty($metaData['category']) ? $helper->sanitizeOutPut($metaData['category']) : 'LocalBusiness';
			$local_business = [
				'@context' => 'https://schema.org',
				'@type'    => $category,
			];

			if (! empty($metaData['url'])) {
				$local_business['@id'] = $helper->sanitizeOutPut($metaData['url'], 'url');
			}

			if (! empty($metaData['name'])) {
				$local_business['name'] = $helper->sanitizeOutPut($metaData['name']);
			}

			if (! empty($metaData['alternateName'])) {
				$local_business['alternateName'] = $helper->sanitizeOutPut($metaData['alternateName']);
			}

			if (! empty($metaData['description'])) {
				$local_business['description'] = $helper->sanitizeOutPut($metaData['description'], 'textarea');
			}

			if (! empty($metaData['image'])) {
				$img                       = $helper->imageInfo(absint($metaData['image']));
				$local_business['image'][] = $helper->sanitizeOutPut($img['url'], 'url');
			}

			if (! empty($metaData['logo'])) {
				$img                    = $helper->imageInfo(absint($metaData['logo']));
				$local_business['logo'] = $helper->sanitizeOutPut($img['url'], 'url');
			}

			if (! empty($metaData['priceRange'])) {
				$local_business['priceRange'] = $helper->sanitizeOutPut($metaData['priceRange']);
			}

			if ($this->get_social()) {
				$local_business['sameAs'] = $this->get_social();
			}

			if (! empty($metaData['sameAs'])) {
				$aType = explode("\r\n", $metaData['sameAs']);

				if ($local_business['sameAs']) {
					$aType = array_merge($aType, $local_business['sameAs']);
				}

				if (! empty($aType) && is_array($aType)) {
					if (count($aType) == 1) {
						$local_business['sameAs'] = $aType[0];
					} elseif (count($aType) > 1) {
						$local_business['sameAs'] = $aType;
					}
				}
			}

			if ($this->corporate_contacts_types()) {
				$local_business['contactPoint'] = $this->corporate_contacts_types();
			}

			if ($category == 'Restaurant') {
				if (! empty($metaData['servesCuisine'])) {
					$local_business['servesCuisine'] = $helper->sanitizeOutPut($metaData['servesCuisine']);
				}

				if (! empty($metaData['menu'])) {
					$local_business['menu'] = $helper->sanitizeOutPut($metaData['menu'], 'url');
					if (isset($metaData['acceptsReservations']) && $metaData['acceptsReservations'] == 'yes') {
						$local_business['acceptsReservations'] = 'True';
					}
				}
			}

			if (! empty($metaData['addresses'])) {
				$addresses = [];
				$address_i = 1;
				foreach ($metaData['addresses'] as $address) {
					if (! empty($address['addressLocality']) || ! empty($address['addressRegion'])
				|| ! empty($address['postalCode']) || ! empty($address['streetAddress'])) {
						if (! function_exists('rtrsp') && $address_i > 1) {
							break;
						}

						$addresses[] = [
							'@type'           => 'PostalAddress',
							'addressLocality' => $helper->sanitizeOutPut($address['addressLocality']),
							'addressRegion'   => $helper->sanitizeOutPut($address['addressRegion']),
							'postalCode'      => $helper->sanitizeOutPut($address['postalCode']),
							'streetAddress'   => $helper->sanitizeOutPut($address['streetAddress']),
							'addressCountry'  => $helper->sanitizeOutPut($address['addressCountry']),
						];
						$address_i++;
					}
				}

				if ($addresses) {
					$local_business['address'] = $addresses;
				}
			}

			$metaDataSubOrg = get_option('rtrs_schema_sub_organization_settings');
			if (function_exists('rtrsp') && ! empty($metaDataSubOrg['sub_organization'])) {
				$sub_organization = [];
				foreach ($metaDataSubOrg['sub_organization'] as $sub_org) {
					if (! empty($sub_org['name']) || ! empty($sub_org['url'])) {
						$sub_organization[] = [
							'@type'           => 'Organization',
							'name'            => $helper->sanitizeOutPut($sub_org['name']),
							'url'             => $helper->sanitizeOutPut($sub_org['url']),
						];
					}
				}

				if ($sub_organization) {
					$local_business['subOrganization'] = $sub_organization;
				}
			}

			if (! empty($metaData['latitude']) || ! empty($metaData['longitude'])) {
				$local_business['geo'] = [
					'@type'       => 'GeoCircle',
					'geoMidpoint' => [
						'@type'     => 'GeoCoordinates',
						'latitude'  => $helper->sanitizeOutPut($metaData['latitude']),
						'longitude' => $helper->sanitizeOutPut($metaData['longitude']),
					],
					'geoRadius'   => ! empty($metaData['radius']) ? $helper->sanitizeOutPut($metaData['radius']) : 50,
				];
			}

			if (! empty($metaData['telephone'])) {
				$local_business['telephone'] = $helper->sanitizeOutPut($metaData['telephone']);
			}

			if (! empty($metaData['url'])) {
				$local_business['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
			}

			if (! empty($metaData['openingHours'])) {
				$opening_day = explode("\r\n", $metaData['openingHours']);
				if (! empty($opening_day) && is_array($opening_day)) {
					$local_business_opening_hours = [];
					foreach ($opening_day as $value) {
						$opening_day_time = explode(' ', $value);
						$opening_day      = isset($opening_day_time[0]) ? $opening_day_time[0] : '';

						$opening_time = isset($opening_day_time[1]) ? $opening_day_time[1] : '';
						$opening_time = explode('-', $opening_time);

						$open_time   = isset($opening_time[0]) ? $opening_time[0] : '';
						$closes_time = isset($opening_time[1]) ? $opening_time[1] : '';

						$opening_hours_single_schema = [
							'@type'          => 'OpeningHoursSpecification',
							'dayOfWeek'      => $opening_day ? $helper->sanitizeOutPut($opening_day) : '',
							'opens'          => $open_time ? $helper->sanitizeOutPut($open_time) : '',
							'closes'         => $closes_time ? $helper->sanitizeOutPut($closes_time) : '',
						];
						array_push($local_business_opening_hours, $opening_hours_single_schema);
					}
					$local_business['openingHoursSpecification'] = $local_business_opening_hours;
				}
			}

			echo $this->getJsonEncode(apply_filters('rtseo_site_schema', $local_business, $metaData));

			// Generate SiteNavigationElement Schema
			$sitelink_settings = get_option('rtrs_schema_sitelink_settings');

			if (! empty($sitelink_settings['site_nav'])) {
				$items = wp_get_nav_menu_items(absint($sitelink_settings['site_nav']));
				if (! empty($items)) {
					$navData             = [];
					$navData['@context'] = 'https://schema.org/';
					$itemData            = [];
					foreach ($items as $item) {
						$itemData[] = [
							'@context' => 'https://schema.org',
							'@type'    => 'SiteNavigationElement',
							'@id'      => '#table-of-contents',
							'name'     => esc_html($item->title),
							'url'      => esc_url($item->url),
						];
					}
					$navData['@graph'] = $itemData;
					echo $this->getJsonEncode(apply_filters('rtseo_sitelink_nav_menu', $navData));
				}
			}
		}
	}

	/**
	 * rich snippet generator.
	 *
	 * @return object
	 */
	public function rich_snippet() {
		if (! is_singular()) {
			return;
		}

		global $post;
		$post_type        = $post->post_type;
		$p_meta           = Functions::getMetaByPostType($post_type);
		$rich_snippet     = (isset($p_meta['rich_snippet']) && $p_meta['rich_snippet'][0] == '1');
		$rich_snippet_cat = isset($p_meta['rich_snippet_cat']) ? $p_meta['rich_snippet_cat'][0] : null;

		$prefix         = 'rtrs_';
		$output         = null;
		$post_id        = get_the_ID();
		$custom_snippet = get_post_meta($post_id, '_rtrs_custom_rich_snippet', true);

		if ($custom_snippet) {
			$schemaCat = get_post_meta($post_id, '_rtrs_rich_snippet_cat', false);
			foreach ($schemaCat as $singleCat) {
				$metaData = get_post_meta($post_id, $prefix . $singleCat . '_schema', true);
				if( ! empty( $metaData ) ){
					foreach ($metaData as $meta) {
						if ($meta['status'] == 'show') {
							$output .= $this->schemaOutput($singleCat, $meta);
						}
					}
				}

			}
		} else { //auto generate 
			if ( ! $rich_snippet && empty( $p_meta ) ) {
				$support_cat = $this->post_type_schema_supported( $post_type );
				if( isset( $support_cat['schema_type'] ) && ! empty( $support_cat['schema_type'] ) ){
					$rich_snippet = true;
					$rich_snippet_cat = $support_cat['schema_type'];
				}
			}
			if ($rich_snippet) {
				$output = $this->autoSchemaOutput($rich_snippet_cat, $post_id);
				
			}
		}
		$generated_type = ($custom_snippet) ? 'manually' : 'auto';

		return "\n" . '<!-- This Google structured data (Rich Snippet) ' . $generated_type . ' generated by RadiusTheme Review Schema plugin version ' . RTRS_VERSION . ' -->' . "\n" . $output . "\n";
	}
	/**
	 * Is post type Supported from settings
	 */
	public function post_type_schema_supported( $post_type = null ){
		$post_supported = [] ;
		if( ! $post_type ){
			return $post_supported;
		}
		$schema_settings = get_option('rtrs_schema_settings');
		if( isset( $schema_settings['post_type'] ) && is_array( $schema_settings['post_type'] ) && !empty( $schema_settings['post_type'] ) ){
			$match = array_filter(
				$schema_settings['post_type'],
				function( $ar ) use ( $post_type ) {
					if ( isset( $ar['post_type'] ) ) {
						return $post_type === $ar['post_type'];
					}
					return false;
				}
			);
			
			if( ! empty( $match ) && ! empty( array_keys( $match ) )  ){
				$array_keys =  array_keys($match)[0];
				$post_supported = ! empty( $match[$array_keys] ) ? $match[$array_keys] : [] ;
			}
		}
		return $post_supported;

	}
	// private static $generated_text = false;

	public function getJsonEncode($data = []) {
		$html = null;
		if (! empty($data) && is_array($data)) {
			//show only one time
			/* if ( self::$generated_text === false ) {
				$html .= '<!-- This Google structured data (Rich Snippet) generated by RadiusTheme Review Schema plugin version '.RTRS_VERSION.' -->';
				self::$generated_text = true;
			}  */

			$html .= '<script type="application/ld+json">' . json_encode(
				$data,
				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			) . '</script>';
		}

		return $html;
	}

	/**
	 * schema output.
	 *
	 * @return object
	 */
	public function schemaOutput($schemaCat, $metaData = null, $without_script = false) {
		$html = null;
		if ($schemaCat) {
			$helper = new Functions();
			switch ($schemaCat) {
				case 'article':
					$article = [
						'@context' => 'https://schema.org',
						'@type'    => 'Article',
					];
					if (! empty($metaData['headline'])) {
						$article['headline'] = $helper->sanitizeOutPut($metaData['headline']);
					}
					if (! empty($metaData['mainEntityOfPage'])) {
						$article['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($metaData['mainEntityOfPage']),
						];
					}
					if (! empty($metaData['author'])) {
						$article['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];

						if (! empty($metaData['author_url'])) {
							$article['author']['url'] =  $helper->sanitizeOutPut($metaData['author_url'], 'url');
						}
					}
					if (! empty($metaData['publisher'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$article['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
							'logo'  => $plA,
						];
					}
					if (! empty($metaData['alternativeHeadline'])) {
						$article['alternativeHeadline'] = $helper->sanitizeOutPut($metaData['alternativeHeadline']);
					}
					if (! empty($metaData['image'])) {
						$img              = $helper->imageInfo(absint($metaData['image']));
						$article['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['datePublished'])) {
						$article['datePublished'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['dateModified'])) {
						$article['dateModified'] = $helper->sanitizeOutPut($metaData['dateModified']);
					}
					if (! empty($metaData['description'])) {
						$article['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['articleBody'])) {
						$article['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($metaData['articleBody'], 500), 'textarea');
					}

					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$article_video = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['embedUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$article_video = $video_single_schema;
							}
						}
						if ($article_video) {
							$article['video'] = $article_video;
						}
					}

					if (isset($metaData['audio']) && is_array($metaData['audio'])) {
						$article_audio = [];
						foreach ($metaData['audio'] as $audio_single) {
							if ($audio_single['name'] && $audio_single['contentUrl']) {
								$audio_single_schema = [
									'@type'             => 'AudioObject',
									'name'              => $audio_single['name'] ? $helper->sanitizeOutPut($audio_single['name']) : null,
									'description'       => $audio_single['description'] ? $helper->sanitizeOutPut($audio_single['description']) : null,
									'duration'          => $audio_single['duration'] ? $helper->sanitizeOutPut($audio_single['duration']) : null,
									'contentUrl'        => $audio_single['contentUrl'] ? $helper->sanitizeOutPut($audio_single['contentUrl']) : null,
									'encodingFormat'    => $audio_single['encodingFormat'] ? $helper->sanitizeOutPut($audio_single['encodingFormat']) : null,
								];

								$article_audio = $audio_single_schema;
							}
						}
						if ($article_audio) {
							$article['audio'] = $article_audio;
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_article', $article, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_article', $article, $metaData));
					}

					break;

				case 'news_article':
					$newsArticle = [
						'@context' => 'https://schema.org',
						'@type'    => 'NewsArticle',
					];
					if (! empty($metaData['headline'])) {
						$newsArticle['headline'] = $helper->sanitizeOutPut($metaData['headline']);
					}
					if (! empty($metaData['mainEntityOfPage'])) {
						$newsArticle['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($metaData['mainEntityOfPage']),
						];
					}
					if (! empty($metaData['author'])) {
						$newsArticle['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];

						if (! empty($metaData['author_url'])) {
							$newsArticle['author']['url'] =  $helper->sanitizeOutPut($metaData['author_url'], 'url');
						}
					}
					if (! empty($metaData['image'])) {
						$img                  = $helper->imageInfo(absint($metaData['image']));
						$newsArticle['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['datePublished'])) {
						$newsArticle['datePublished'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['dateModified'])) {
						$newsArticle['dateModified'] = $helper->sanitizeOutPut($metaData['dateModified']);
					}
					if (! empty($metaData['publisher'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$newsArticle['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
							'logo'  => $plA,
						];
					}
					if (! empty($metaData['description'])) {
						$newsArticle['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['articleBody'])) {
						$newsArticle['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($metaData['articleBody'], 500), 'textarea');
					}

					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$newsArticle_video = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['embedUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$newsArticle_video = $video_single_schema;
							}
						}
						if ($newsArticle_video) {
							$newsArticle['video'] = $newsArticle_video;
						}
					}

					if (isset($metaData['audio']) && is_array($metaData['audio'])) {
						$newsArticle_audio = [];
						foreach ($metaData['audio'] as $audio_single) {
							if ($audio_single['name'] && $audio_single['contentUrl']) {
								$audio_single_schema = [
									'@type'             => 'AudioObject',
									'name'              => $audio_single['name'] ? $helper->sanitizeOutPut($audio_single['name']) : null,
									'description'       => $audio_single['description'] ? $helper->sanitizeOutPut($audio_single['description']) : null,
									'duration'          => $audio_single['duration'] ? $helper->sanitizeOutPut($audio_single['duration']) : null,
									'contentUrl'        => $audio_single['contentUrl'] ? $helper->sanitizeOutPut($audio_single['contentUrl']) : null,
									'encodingFormat'    => $audio_single['encodingFormat'] ? $helper->sanitizeOutPut($audio_single['encodingFormat']) : null,
								];

								$newsArticle_audio = $audio_single_schema;
							}
						}
						if ($newsArticle_audio) {
							$newsArticle['audio'] = $newsArticle_audio;
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_news_article', $newsArticle, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_news_article', $newsArticle, $metaData));
					}

					break;

				case 'blog_posting':
					$blogPosting = [
						'@context' => 'https://schema.org',
						'@type'    => 'BlogPosting',
					];
					if (! empty($metaData['headline'])) {
						$blogPosting['headline'] = $helper->sanitizeOutPut($metaData['headline']);
					}
					if (! empty($metaData['mainEntityOfPage'])) {
						$blogPosting['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($metaData['mainEntityOfPage']),
						];
					}
					if (! empty($metaData['author'])) {
						$blogPosting['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];

						if (! empty($metaData['author_url'])) {
							$blogPosting['author']['url'] =  $helper->sanitizeOutPut($metaData['author_url'], 'url');
						}
					}
					if (! empty($metaData['image'])) {
						$img                  = $helper->imageInfo(absint($metaData['image']));
						$blogPosting['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['datePublished'])) {
						$blogPosting['datePublished'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['dateModified'])) {
						$blogPosting['dateModified'] = $helper->sanitizeOutPut($metaData['dateModified']);
					}
					if (! empty($metaData['publisher'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$blogPosting['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
							'logo'  => $plA,
						];
					}
					if (! empty($metaData['description'])) {
						$blogPosting['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['articleBody'])) {
						$blogPosting['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($metaData['articleBody'], 500), 'textarea');
					}

					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$blogPosting_video = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['embedUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$blogPosting_video = $video_single_schema;
							}
						}
						if ($blogPosting_video) {
							$blogPosting['video'] = $blogPosting_video;
						}
					}

					if (isset($metaData['audio']) && is_array($metaData['audio'])) {
						$blogPosting_audio = [];
						foreach ($metaData['audio'] as $audio_single) {
							if ($audio_single['name'] && $audio_single['contentUrl']) {
								$audio_single_schema = [
									'@type'             => 'AudioObject',
									'name'              => $audio_single['name'] ? $helper->sanitizeOutPut($audio_single['name']) : null,
									'description'       => $audio_single['description'] ? $helper->sanitizeOutPut($audio_single['description']) : null,
									'duration'          => $audio_single['duration'] ? $helper->sanitizeOutPut($audio_single['duration']) : null,
									'contentUrl'        => $audio_single['contentUrl'] ? $helper->sanitizeOutPut($audio_single['contentUrl']) : null,
									'encodingFormat'    => $audio_single['encodingFormat'] ? $helper->sanitizeOutPut($audio_single['encodingFormat']) : null,
								];

								$blogPosting_audio = $audio_single_schema;
							}
						}
						if ($blogPosting_audio) {
							$blogPosting['audio'] = $blogPosting_audio;
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_blog_posting', $blogPosting, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_blog_posting', $blogPosting, $metaData));
					}

					break;

				case 'book':
					$book = array();
					$book["@context"] = "http://schema.org";
					$book["@type"] = "Book";
					if (!empty($metaData['name'])) {
						$book["name"] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (!empty($metaData['author'])) {
						$book["author"] = array(
							"@type" => "Person",
							"name"  => $helper->sanitizeOutPut($metaData['author'])
						);

						if (isset($metaData['author_sameAs']) && !empty($metaData['author_sameAs'])) {
							$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['author_sameAs'], "textarea"));
							if (!empty($sameAs)) {
								$book["author"]["sameAs"] = $sameAs;
							}
						}
					}
					if (!empty($metaData['bookFormat'])) {
						$book["bookFormat"] = $helper->sanitizeOutPut($metaData['bookFormat']);
					}
					if (!empty($metaData['isbn'])) {
						$book["isbn"] = $helper->sanitizeOutPut($metaData['isbn']);
					}
					if (!empty($metaData['workExample'])) {
						$book["workExample"] = array(
							"@type" => "CreativeWork",
							"name"  => $helper->sanitizeOutPut($metaData['workExample'])
						);
					}
					if (!empty($metaData['url'])) {
						$book["url"] = $helper->sanitizeOutPut($metaData['url']);
					}
					if (!empty($metaData['sameAs'])) {
						$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['sameAs'], "textarea"));
						if (!empty($sameAs)) {
							$book["sameAs"] = $sameAs;
						}
					}
					if (!empty($metaData['publisher'])) {
						$book["publisher"] = array(
							"@type" => "Organization",
							"name"  => $helper->sanitizeOutPut($metaData['publisher'])
						);
					}
					if (!empty($metaData['numberOfPages'])) {
						$book["numberOfPages"] = $helper->sanitizeOutPut($metaData['numberOfPages']);
					}
					if (!empty($metaData['copyrightHolder'])) {
						$book["copyrightHolder"] = array(
							"@type" => "Organization",
							"name"  => $helper->sanitizeOutPut($metaData['copyrightHolder'])
						);
					}
					if (!empty($metaData['copyrightYear'])) {
						$book["copyrightYear"] = $helper->sanitizeOutPut($metaData['copyrightYear']);
					}
					if (!empty($metaData['description'])) {
						$book["description"] = $helper->sanitizeOutPut($metaData['description']);
					}
					if (!empty($metaData['genre'])) {
						$book["genre"] = $helper->sanitizeOutPut($metaData['genre']);
					}
					if (!empty($metaData['inLanguage'])) {
						$book["inLanguage"] = $helper->sanitizeOutPut($metaData['inLanguage']);
					} 

					if ( $without_script ) {
						$html = apply_filters('rtseo_snippet_book', $book, $metaData);
					} else { 
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_book', $book, $metaData));
					}

					if (isset($metaData['review_active'])) {
						$book_review = array(
							"@context"     => "http://schema.org",
							"@type"        => "Review",
							"itemReviewed" => array(
								"@type" => "Book",
							)
						);

						if (isset($metaData['review_datePublished']) && !empty($metaData['review_datePublished'])) {
							$book_review["datePublished"] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && !empty($metaData['review_body'])) {
							$book_review["reviewBody"] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}
						if (isset($book["url"])) {
							$book_review["url"] = $book["url"];
						}
						if (isset($book["description"])) {
							$book_review["description"] = Functions::filter_content($book["description"], 200);
						}
						if (isset($book['author'])) {
							$book_review['author'] = $book_review["itemReviewed"]["author"] = $book["author"];
						}
						if (isset($book["publisher"])) {
							$book_review['publisher'] = $book["publisher"];
						}
						if (isset($book["name"])) {
							$book_review["itemReviewed"]['name'] = $book["name"];
						}
						if (isset($book["isbn"])) {
							$book_review["itemReviewed"]['isbn'] = $book["isbn"];
						}
						if (!empty($metaData['review_author'])) {
							$book_review["author"] = array(
								"@type" => "Person",
								"name"  => $helper->sanitizeOutPut($metaData['review_author'])
							);

							if (isset($metaData['review_author_sameAs']) && !empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], "textarea"));
								if (!empty($sameAs)) {
									$book_review["author"]["sameAs"] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$book_review["reviewRating"] = array(
								"@type"       => "Rating",
								"ratingValue" => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number')
							);
							if (isset($metaData['review_bestRating'])) {
								$book_review["reviewRating"]["bestRating"] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$book_review["reviewRating"]["worstRating"] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						} 

						if ( $without_script ) {
							$html = apply_filters('rtseo_snippet_book_review', $book_review, $metaData); 
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_book_review', $book_review, $metaData));
						}

					}
					break;
				
				case 'real_state_listing':	
					$real_state = [
						'@context' => 'https://schema.org',
						'@type'    => 'RealEstateListing',
					];
					if (! empty($metaData['headline'])) {
						$real_state['headline'] = $helper->sanitizeOutPut($metaData['headline']);
					}
					if (! empty($metaData['description'])) {
						$real_state['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					} 
					if (! empty($metaData['mainEntityOfPage'])) {
						$real_state['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($metaData['mainEntityOfPage']),
						];
					}
					if (! empty($metaData['author'])) {
						$real_state['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];

						if (! empty($metaData['author_url'])) {
							$real_state['author']['url'] =  $helper->sanitizeOutPut($metaData['author_url'], 'url');
						}
					}
					if (! empty($metaData['publisher'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$real_state['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
							'logo'  => $plA,
						];
					} 
					if (! empty($metaData['image'])) {
						$img              = $helper->imageInfo(absint($metaData['image']));
						$real_state['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['datePosted'])) {
						$real_state['datePosted'] = $helper->sanitizeOutPut($metaData['datePosted']);
					} 
					if (! empty($metaData['award'])) {
						$real_state['award'] = $helper->sanitizeOutPut($metaData['award']);
					}
					if (! empty($metaData['price'])) {
						$real_state['offers'] = [
							'@type' => 'Offer',
							'price' => $helper->sanitizeOutPut($metaData['price']),
						];
						if (! empty($metaData['priceCurrency'])) {
							$real_state['offers']['priceCurrency'] = $helper->sanitizeOutPut($metaData['priceCurrency']);
						}
						if (! empty($metaData['mainEntityOfPage'])) {
							$real_state['offers']['mainEntityOfPage'] = $helper->sanitizeOutPut($metaData['mainEntityOfPage'], 'url');
						}
						if (! empty($metaData['availability'])) {
							$real_state['offers']['availability'] = $helper->sanitizeOutPut($metaData['availability']);
						} 
					}
					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$real_state_video = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['embedUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$real_state_video = $video_single_schema;
							}
						}
						if ($real_state_video) {
							$real_state['video'] = $real_state_video;
						}
					}

					if (isset($metaData['audio']) && is_array($metaData['audio'])) {
						$real_state_audio = [];
						foreach ($metaData['audio'] as $audio_single) {
							if ($audio_single['name'] && $audio_single['contentUrl']) {
								$audio_single_schema = [
									'@type'             => 'AudioObject',
									'name'              => $audio_single['name'] ? $helper->sanitizeOutPut($audio_single['name']) : null,
									'description'       => $audio_single['description'] ? $helper->sanitizeOutPut($audio_single['description']) : null,
									'duration'          => $audio_single['duration'] ? $helper->sanitizeOutPut($audio_single['duration']) : null,
									'contentUrl'        => $audio_single['contentUrl'] ? $helper->sanitizeOutPut($audio_single['contentUrl']) : null,
									'encodingFormat'    => $audio_single['encodingFormat'] ? $helper->sanitizeOutPut($audio_single['encodingFormat']) : null,
								];

								$real_state_audio = $audio_single_schema;
							}
						}
						if ($real_state_audio) {
							$real_state['audio'] = $real_state_audio;
						}
					}

					if ( $without_script ) {
						$html = apply_filters('rtseo_snippet_real_state', $real_state, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_real_state', $real_state, $metaData));
					}
					break;

				case 'course':
					$course                      = [];
					$course['@context']          = 'http://schema.org';
					$course['@type']             = 'Course';
					$course['hasCourseInstance'] = [
						'@type' => 'CourseInstance',
					];
					if (! empty($metaData['name'])) {
						$course['name'] = $course['hasCourseInstance']['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$course['description'] = $course['hasCourseInstance']['description'] = $helper->sanitizeOutPut($metaData['description']);
					}
					if (! empty($metaData['provider'])) {
						$course['provider'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['provider']),
						];
					}

					if (! empty($metaData['courseMode'])) {
						$course['hasCourseInstance']['courseMode'] = explode(
							"\r\n",
							$helper->sanitizeOutPut($metaData['courseMode'], 'textarea')
						);
					}
					if (! empty($metaData['endDate'])) {
						$course['hasCourseInstance']['endDate'] = $helper->sanitizeOutPut($metaData['endDate']);
					}
					if (! empty($metaData['startDate'])) {
						$course['hasCourseInstance']['startDate'] = $helper->sanitizeOutPut($metaData['startDate']);
					}
					if (! empty($metaData['locationName']) && ! empty($metaData['locationAddress'])) {
						$course['hasCourseInstance']['location'] = [
							'@type'   => 'Place',
							'name'    => $helper->sanitizeOutPut($metaData['locationName']),
							'address' => $helper->sanitizeOutPut($metaData['locationAddress']),
						];
					}
					if (! empty($metaData['image'])) {
						$img                                  = $helper->imageInfo(absint($metaData['image']));
						$course['hasCourseInstance']['image'] = $helper->sanitizeOutPut(
							$img['url'],
							'url'
						);
					}
					if (! empty($metaData['price']) && ! empty($metaData['priceCurrency'])) {
						$course['hasCourseInstance']['offers'] = [
							'@type'         => 'Offer',
							'price'         => $helper->sanitizeOutPut($metaData['price']),
							'priceCurrency' => $helper->sanitizeOutPut($metaData['priceCurrency']),
						];
						if (! empty($metaData['availability'])) {
							$course['hasCourseInstance']['offers']['availability'] = $helper->sanitizeOutPut($metaData['availability']);
						}
						if (! empty($metaData['url'])) {
							$course['hasCourseInstance']['offers']['url'] = $helper->sanitizeOutPut($metaData['url']);
						}
						if (! empty($metaData['validFrom'])) {
							$course['hasCourseInstance']['offers']['validFrom'] = $helper->sanitizeOutPut($metaData['validFrom']);
						}
					}
					if (! empty($metaData['performerType']) && ! empty($metaData['performerName'])) {
						$course['hasCourseInstance']['performer'] = [
							'@type' => $helper->sanitizeOutPut($metaData['performerType']),
							'name'  => $helper->sanitizeOutPut($metaData['performerName']),
						];
					}
					$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_course', $course, $metaData));
					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$course_review = [
							'@context'     => 'http://schema.org',
							'@type'        => 'Review',
							'itemReviewed' => [
								'@type' => 'Course',
							],
						];

						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$course_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$course_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}
						if (isset($course['name'])) {
							$course_review['itemReviewed']['name'] = $course['name'];
						}

						if (isset($course['description'])) {
							$course_review['itemReviewed']['description'] = Functions::filter_content($course['description'], 200);
						}
						if (isset($course['provider'])) {
							$course_review['itemReviewed']['provider'] = $course['provider'];
						}
						if (! empty($metaData['review_author'])) {
							$course_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$course_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$course_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$course_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$course_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_course_review', $course_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_course_review', $course_review, $metaData));
						}
					}
					break;

				case 'event':
					$event = [
						'@context' => 'https://schema.org',
						'@type'    => 'Event',
					];
					if (! empty($metaData['name'])) {
						$event['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['startDate'])) {
						$event['startDate'] = $helper->sanitizeOutPut($metaData['startDate']);
					}
					if (! empty($metaData['endDate'])) {
						$event['endDate'] = $helper->sanitizeOutPut($metaData['endDate']);
					}
					if (! empty($metaData['description'])) {
						$event['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['performerName'])) {
						$event['performer'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['performerName']),
						];
					}
					/* if (!empty($metaData['image'])) {
						$event["image"] = $helper->sanitizeOutPut($metaData['image'], 'url');
					} */
					if (! empty($metaData['image'])) {
						$img            = $helper->imageInfo(absint($metaData['image']));
						if ($img) {
							$event['image'] = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						}
					}
					if (! empty($metaData['locationName'])) {
						$event['location'] = [
							'@type'   => 'Place',
							'name'    => $helper->sanitizeOutPut($metaData['locationName']),
							'address' => $helper->sanitizeOutPut($metaData['locationAddress']),
						];
					}
					if (! empty($metaData['price'])) {
						$event['offers'] = [
							'@type' => 'Offer',
							'price' => $helper->sanitizeOutPut($metaData['price']),
						];
						if (! empty($metaData['priceCurrency'])) {
							$event['offers']['priceCurrency'] = $helper->sanitizeOutPut($metaData['priceCurrency']);
						}
						if (! empty($metaData['url'])) {
							$event['offers']['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
						}
						if (! empty($metaData['availability'])) {
							$event['offers']['availability'] = $helper->sanitizeOutPut($metaData['availability']);
						}
						if (! empty($metaData['validFrom'])) {
							$event['offers']['validFrom'] = $helper->sanitizeOutPut($metaData['validFrom']);
						}
					}
					if (! empty($metaData['eventStatus'])) {
						$event['eventStatus'] = $helper->sanitizeOutPut($metaData['eventStatus']);
					}
					if (! empty($metaData['eventAttendanceMode'])) {
						$event['eventAttendanceMode'] = $helper->sanitizeOutPut($metaData['eventAttendanceMode']);
					}
					if (! empty($metaData['url'])) {
						$event['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}
					if (! empty($metaData['organizerName'])) {
						$event['organizer'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['organizerName']),
							'url'   => $helper->sanitizeOutPut($metaData['url'], 'url'),
						];
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_event', $event, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_event', $event, $metaData));
					}

					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$event_review = [
							'@context' => 'https://schema.org',
							'@type'    => 'Review',
						];

						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$event_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$event_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}
						unset($event['@context']);
						$event_review['itemReviewed'] = $event;
						if (! empty($metaData['review_author'])) {
							$event_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$event_review['author']['sameAs'] = $sameAs;
								}
							}
						}

						if (isset($metaData['review_ratingValue'])) {
							$event_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$event_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$event_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_event_review', $event_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_event_review', $event_review, $metaData));
						}
					}
					break;

				case 'product':
					if (! function_exists('rtrsp')) {
						break;
					}

					$product = [
						'@context' => 'https://schema.org',
						'@type'    => 'Product',
					];
					if (! empty($metaData['name'])) {
						$product['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['image'])) {
						$img              = $helper->imageInfo(absint($metaData['image']));
						$product['image'] = $helper->sanitizeOutPut($img['url'], 'url');
					}
					if (! empty($metaData['description'])) {
						$product['description'] = $helper->sanitizeOutPut($metaData['description']);
					}
					/* product identifier */
					if (! empty($metaData['sku'])) {
						$product['sku'] = $helper->sanitizeOutPut($metaData['sku']);
					}
					if (! empty($metaData['brand'])) {
						$product['brand'] = $helper->sanitizeOutPut($metaData['brand']);
					}
					if (! empty($metaData['identifier_type']) && ! empty($metaData['identifier'])) {
						$product[$metaData['identifier_type']] = $helper->sanitizeOutPut($metaData['identifier']);
					}

					if (! empty($metaData['ratingValue'])) {
						$product['aggregateRating'] = [
							'@type'       => 'AggregateRating',
							'ratingValue' => ! empty($metaData['ratingValue']) ? $helper->sanitizeOutPut($metaData['ratingValue']) : null,
							'reviewCount' => ! empty($metaData['reviewCount']) ? $helper->sanitizeOutPut($metaData['reviewCount']) : null,
						];
					}

					if (! empty($metaData['reviewRatingValue']) || ! empty($metaData['reviewBestRating']) || ! empty($metaData['reviewWorstRating'])) {
						$product['review'] = [
							'@type'        => 'Review',
							'reviewRating' => [
								'@type'       => 'Rating',
								'ratingValue' => ! empty($metaData['reviewRatingValue']) ? $helper->sanitizeOutPut($metaData['reviewRatingValue']) : null,
								'bestRating'  => ! empty($metaData['reviewBestRating']) ? $helper->sanitizeOutPut($metaData['reviewBestRating']) : null,
								'worstRating' => ! empty($metaData['reviewWorstRating']) ? $helper->sanitizeOutPut($metaData['reviewWorstRating']) : null,
							],
							'author'  => [
								'@type' => 'Person',
								'name'  => ! empty($metaData['reviewAuthor']) ? $helper->sanitizeOutPut($metaData['reviewAuthor']) : null,
							],
						];
					}

					if (! empty($metaData['price'])) {
						$product['offers'] = [
							'@type'           => 'Offer',
							'price'           => $helper->sanitizeOutPut($metaData['price']),
							'priceValidUntil' => $helper->sanitizeOutPut($metaData['priceValidUntil']),
							'priceCurrency'   => ! empty($metaData['priceCurrency']) ? $helper->sanitizeOutPut($metaData['priceCurrency']) : null,
							'itemCondition'   => ! empty($metaData['itemCondition']) ? $helper->sanitizeOutPut($metaData['itemCondition']) : null,
							'availability'    => ! empty($metaData['availability']) ? $helper->sanitizeOutPut($metaData['availability']) : null,
							'url'             => ! empty($metaData['url']) ? $helper->sanitizeOutPut($metaData['url']) : null,
						];
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_product', $product, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_product', $product, $metaData));
					}

					break;

				case 'recipe':
					$recipe = [
						'@context' => 'http://schema.org',
						'@type'    => 'Recipe',
					];
					if (! empty($metaData['name'])) {
						$recipe['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['author'])) {
						$recipe['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];
					}
					if (! empty($metaData['datePublished'])) {
						$recipe['datePublished'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['keywords'])) {
						$recipe['keywords'] = $helper->sanitizeOutPut($metaData['keywords']);
					}
					if (! empty($metaData['recipeCategory'])) {
						$recipe['recipeCategory'] = $helper->sanitizeOutPut($metaData['recipeCategory']);
					}
					if (! empty($metaData['recipeCuisine'])) {
						$recipe['recipeCuisine'] = $helper->sanitizeOutPut($metaData['recipeCuisine']);
					}
					if (! empty($metaData['prepTime'])) {
						$recipe['prepTime'] = $helper->sanitizeOutPut($metaData['prepTime']);
					}
					if (! empty($metaData['cookTime'])) {
						$recipe['cookTime'] = $helper->sanitizeOutPut($metaData['cookTime']);
					}
					if (! empty($metaData['description'])) {
						$recipe['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['image'])) {
						$img             = $helper->imageInfo(absint($metaData['image']));
						$recipe['image'] = $helper->sanitizeOutPut($img['url'], 'url');
					}

					if (isset($metaData['ingredient']) && is_array($metaData['ingredient'])) {
						$recipeIngredient = [];
						foreach ($metaData['ingredient'] as $recipeIngredient_single) {
							if (! empty($recipeIngredient_single['name'])) {
								array_push($recipeIngredient, $helper->sanitizeOutPut($recipeIngredient_single['name']));
							}
						}
						$recipe['recipeIngredient'] = $recipeIngredient;
					}

					if (isset($metaData['instructions']) && is_array($metaData['instructions'])) {
						$recipeinstructions = [];
						foreach ($metaData['instructions'] as $instructions_single) {
							$instructions_single_schema = [
								'@type'   => 'HowToStep',
							];

							if (! empty($instructions_single['name'])) {
								$instructions_single_schema['name'] = $helper->sanitizeOutPut($instructions_single['name']);
							}

							if (! empty($instructions_single['text'])) {
								$instructions_single_schema['text'] =  $helper->sanitizeOutPut($instructions_single['text'], 'textarea');
							}

							if (! empty($instructions_single['url'])) {
								$instructions_single_schema['url'] = $helper->sanitizeOutPut($instructions_single['url'], 'url');
							}

							if (! empty($instructions_single['image'])) {
								$img                                 = $helper->imageInfo(absint($instructions_single['image']));
								$instructions_single_schema['image'] = $helper->sanitizeOutPut($img['url'], 'url');
							}

							array_push($recipeinstructions, $instructions_single_schema);
						}
						$recipe['recipeInstructions'] = $recipeinstructions;
					}

					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$recipevideo = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['contentUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$recipevideo = $video_single_schema;
							}
						}
						if ($recipevideo) {
							$recipe['video'] = $recipevideo;
						}
					}

					if (! empty($metaData['userInteractionCount'])) {
						$recipe['interactionStatistic'] = [
							'@type'                => 'InteractionCounter',
							'interactionType'      => 'http://schema.org/Comment',
							'userInteractionCount' => $helper->sanitizeOutPut($metaData['userInteractionCount']),
						];
					}
					if (! empty($metaData['ratingValue']) || ! empty($metaData['reviewCount'])) {
						$recipe['aggregateRating'] = [
							'@type'       => 'AggregateRating',
							'ratingValue' => $helper->sanitizeOutPut($metaData['ratingValue'], 'number'),
							'reviewCount' => $helper->sanitizeOutPut($metaData['reviewCount'], 'number'),
							'bestRating'  => $helper->sanitizeOutPut($metaData['bestRating'], 'number'),
							'worstRating' => $helper->sanitizeOutPut($metaData['worstRating'], 'number'),
						];
					}
					if (! empty($metaData['calories']) || ! empty($metaData['fatContent'])) {
						$recipe['nutrition'] = [
							'@type'      => 'NutritionInformation',
							'calories'   => $helper->sanitizeOutPut($metaData['calories']),
							'fatContent' => $helper->sanitizeOutPut($metaData['fatContent']),
						];
					}
					if (! empty($metaData['recipeYield'])) {
						$recipe['recipeYield'] = $helper->sanitizeOutPut($metaData['recipeYield']);
					}
					if (! empty($metaData['suitableForDiet'])) {
						$recipe['suitableForDiet'] = $helper->sanitizeOutPut($metaData['suitableForDiet']);
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_recipe', $recipe, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_recipe', $recipe, $metaData));
					}
					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$recipe_review = [
							'@context'     => 'http://schema.org',
							'@type'        => 'Review',
							'itemReviewed' => [
								'@type' => 'Recipe',
							],
						];

						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$recipe_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						} elseif (isset($recipe['datePublished'])) {
							$recipe_review['datePublished'] = $recipe['datePublished'];
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$recipe_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}
						unset($recipe['@context']);
						unset($recipe['@context']);
						$recipe_review['itemReviewed'] = $recipe;
						if (! empty($metaData['review_author'])) {
							$recipe_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$recipe_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$recipe_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$recipe_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$recipe_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_recipe_review', $recipe_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_recipe_review', $recipe_review, $metaData));
						}
					}
					break;

				case 'audio':
					$audio = [
						'@context' => 'https://schema.org',
						'@type'    => 'AudioObject',
					];
					if (! empty($metaData['name'])) {
						$audio['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$audio['description'] = $helper->sanitizeOutPut($metaData['description'], 'textarea');
					}
					if (! empty($metaData['duration'])) {
						$audio['duration'] = $helper->sanitizeOutPut($metaData['duration']);
					}
					if (! empty($metaData['contentUrl'])) {
						$audio['contentUrl'] = $helper->sanitizeOutPut($metaData['contentUrl'], 'url');
					}
					if (! empty($metaData['encodingFormat'])) {
						$audio['encodingFormat'] = $helper->sanitizeOutPut($metaData['encodingFormat']);
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_audio', $audio, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_audio', $audio, $metaData));
					}

					break;

				case 'video':
					$video = [
						'@context' => 'https://schema.org',
						'@type'    => 'VideoObject',
					];
					if (! empty($metaData['name'])) {
						$video['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$video['description'] = $helper->sanitizeOutPut($metaData['description'], 'textarea');
					}
					if (! empty($metaData['thumbnailUrl'])) {
						$video['thumbnailUrl'] = $helper->sanitizeOutPut($metaData['thumbnailUrl'], 'url');
					}
					if (! empty($metaData['uploadDate'])) {
						$video['uploadDate'] = $helper->sanitizeOutPut($metaData['uploadDate']);
					}
					if (! empty($metaData['duration'])) {
						$video['duration'] = $helper->sanitizeOutPut($metaData['duration']);
					}
					if (! empty($metaData['contentUrl'])) {
						$video['contentUrl'] = $helper->sanitizeOutPut($metaData['contentUrl'], 'url');
					}
					if (! empty($metaData['embedUrl'])) {
						$video['embedUrl'] = $helper->sanitizeOutPut($metaData['embedUrl'], 'url');
					}
					if (! empty($metaData['interactionCount'])) {
						$video['interactionCount'] = $helper->sanitizeOutPut($metaData['interactionCount']);
					}
					if (! empty($metaData['expires'])) {
						$video['expires'] = $helper->sanitizeOutPut($metaData['expires']);
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_video', $video, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_video', $video, $metaData));
					}

					break;

				case 'image_license':
					$image_license = [
						'@context' => 'https://schema.org',
						'@type'    => 'ImageObject',
					];

					if (! empty($metaData['contentUrl'])) {
						$image_license['contentUrl'] = $helper->sanitizeOutPut($metaData['contentUrl'], 'url');
					}
					if (! empty($metaData['license'])) {
						$image_license['license'] = $helper->sanitizeOutPut($metaData['license'], 'url');
					}
					if (! empty($metaData['acquireLicensePage'])) {
						$image_license['acquireLicensePage'] = $helper->sanitizeOutPut($metaData['acquireLicensePage'], 'url');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_image_license', $image_license, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_image_license', $image_license, $metaData));
					}
					break;

				case 'movie':
					$movie             = [];
					$movie['@context'] = 'http://schema.org';
					$movie['@type']    = 'Movie';
					if (! empty($metaData['name'])) {
						$movie['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$movie['description'] = $helper->sanitizeOutPut($metaData['description']);
					}
					if (! empty($metaData['duration'])) {
						$movie['duration'] = $helper->sanitizeOutPut($metaData['duration']);
					}
					if (! empty($metaData['dateCreated'])) {
						$movie['dateCreated'] = $helper->sanitizeOutPut($metaData['dateCreated']);
					}
					if (! empty($metaData['image'])) {
						$img            = $helper->imageInfo(absint($metaData['image']));
						$movie['image'] = $helper->sanitizeOutPut($img['url'], 'url');
					}
					if (! empty($metaData['director'])) {
						$movie['director'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['director']),
						];
					}
					if (! empty($metaData['author'])) {
						$authorArray = explode(
							"\r\n",
							$helper->sanitizeOutPut($metaData['author'], 'textarea')
						);
						$author = [];
						if (! empty($authorArray) && is_array($authorArray) && count($authorArray)) {
							foreach ($authorArray as $authorName) {
								$author[] = [
									'@type' => 'Person',
									'name'  => $authorName,
								];
							}
						}
						$movie['author'] = $author;
					}
					if (! empty($metaData['actor'])) {
						$actorArray = explode(
							"\r\n",
							$helper->sanitizeOutPut($metaData['actor'], 'textarea')
						);
						$actor = [];
						if (! empty($actorArray) && is_array($actorArray) && count($actorArray)) {
							foreach ($actorArray as $actorName) {
								$actor[] = [
									'@type' => 'Person',
									'name'  => $actorName,
								];
							}
						}
						$movie['actor'] = $actor;
					}

					if (! empty($metaData['image'])) {
						$img            = $helper->imageInfo(absint($metaData['image']));
						$movie['image'] = $helper->sanitizeOutPut($img['url'], 'url');
					}

					$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_movie', $movie, $metaData));
					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$movie_review = [
							'@context' => 'http://schema.org',
							'@type'    => 'Review',
						];
						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$movie_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$movie_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}

						unset($movie['@context']);
						$movie['@type'] = 'Movie';
						if (isset($movie['description'])) {
							$movie_review['description'] = Functions::filter_content($movie['description'], 200);
							unset($movie['description']);
						}
						if (isset($metaData['review_sameAs']) && ! empty($metaData['review_sameAs'])) {
							$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_sameAs'], 'textarea'));
							if (! empty($sameAs)) {
								$movie['sameAs'] = $sameAs;
							}
						}

						$movie_review['itemReviewed'] = $movie;
						if (! empty($metaData['review_author'])) {
							$movie_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$movie_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_publisher']) && ! empty($metaData['review_publisher'])) {
							$movie_review['publisher'] = [
								'@type' => 'Organization',
								'name'  => $helper->sanitizeOutPut($metaData['review_publisher']),
							];
							if (isset($metaData['review_publisherImage']) && ! empty($metaData['review_publisherImage'])) {
								$img                                      = $helper->imageInfo(absint($metaData['review_publisherImage']));
								$movie_review['review_publisher']['logo'] = [
									'@type'  => 'ImageObject',
									'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
									'height' => $img['height'],
									'width'  => $img['width'],
								];
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$movie_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$movie_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$movie_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_movie_review', $movie_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_movie_review', $movie_review, $metaData));
						}
					}
					break;

				case 'music':
					$music             = [];
					$music['@context'] = 'https://schema.org';
					$music['@type']    = $helper->sanitizeOutPut($metaData['musicType']);
					if (! empty($metaData['name'])) {
						$music['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$music['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['image'])) {
						$img            = $helper->imageInfo(absint($metaData['image']));
						$movie['image'] = $helper->sanitizeOutPut($img['url'], 'url');
					}
					if (! empty($metaData['sameAs'])) {
						$music['sameAs'] = $helper->sanitizeOutPut($metaData['sameAs'], 'url');
					}
					if (! empty($metaData['url'])) {
						$music['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_music', $music, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_music', $music, $metaData));
					}

					break;

				case 'service':
					$service = [
						'@context' => 'https://schema.org',
						'@type'    => 'Service',
					];
					if (! empty($metaData['name'])) {
						$service['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['serviceType'])) {
						$service['serviceType'] = $helper->sanitizeOutPut($metaData['serviceType']);
					}
					if (! empty($metaData['award'])) {
						$service['award'] = $helper->sanitizeOutPut($metaData['award']);
					}
					if (! empty($metaData['category'])) {
						$service['category'] = $helper->sanitizeOutPut($metaData['category']);
					}
					if (! empty($metaData['providerMobility'])) {
						$service['providerMobility'] = $helper->sanitizeOutPut($metaData['providerMobility']);
					}
					if (! empty($metaData['additionalType'])) {
						$service['additionalType'] = $helper->sanitizeOutPut($metaData['additionalType']);
					}
					if (! empty($metaData['alternateName'])) {
						$service['alternateName'] = $helper->sanitizeOutPut($metaData['alternateName']);
					}
					/* if (! empty($metaData['image'])) {
						$service['image'] = $helper->sanitizeOutPut($metaData['image']);
					} */
					if (! empty($metaData['image'])) {
						$img = $helper->imageInfo(absint($metaData['image']));
						if ($img) {
							$service['image'] = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						}
					}
					if (! empty($metaData['mainEntityOfPage'])) {
						$service['mainEntityOfPage'] = $helper->sanitizeOutPut($metaData['mainEntityOfPage']);
					}
					if (! empty($metaData['sameAs'])) {
						$service['sameAs'] = $helper->sanitizeOutPut($metaData['sameAs']);
					}
					if (! empty($metaData['url'])) {
						$service['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_service', $service, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_service', $service, $metaData));
					}
					break;

				case 'review':
					$review = [
						'@context' => 'https://schema.org',
						'@type'    => 'Review',
					];
					if (! empty($metaData['itemName'])) {
						$review['itemReviewed'] = [
							'@type' => 'product',
							'name'  => $helper->sanitizeOutPut($metaData['itemName']),
						];
					}
					if (! empty($metaData['ratingValue'])) {
						$review['reviewRating'] = [
							'@type'       => 'Rating',
							'ratingValue' => $helper->sanitizeOutPut($metaData['ratingValue']),
							'bestRating'  => $helper->sanitizeOutPut($metaData['bestRating']),
							'worstRating' => $helper->sanitizeOutPut($metaData['worstRating']),
						];
					}
					if (! empty($metaData['name'])) {
						$review['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['author'])) {
						$review['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];
					}
					if (! empty($metaData['reviewBody'])) {
						$review['reviewBody'] = $helper->sanitizeOutPut($metaData['reviewBody']);
					}
					if (! empty($metaData['datePublished'])) {
						$review['datePublished'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['publisher'])) {
						$review['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
						];
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_review', $review, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_review', $review, $metaData));
					}
					break;

				case 'local_business':
					$category       = ! empty($metaData['category']) ? $helper->sanitizeOutPut($metaData['category']) : 'LocalBusiness';
					$local_business = [
						'@context' => 'https://schema.org',
						'@type'    => $category,
					];

					if (! empty($metaData['name'])) {
						$local_business['name'] = $helper->sanitizeOutPut($metaData['name']);
					}

					if (! empty($metaData['description'])) {
						$local_business['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['image'])) {
						$imges = [];
						foreach ($metaData['image'] as $img_single) {
							if (! $img_single) {
								continue;
							}
							$img     = $helper->imageInfo(absint($img_single));
							$imges[] = $helper->sanitizeOutPut($img['url'], 'url');
						}
						$local_business['image'] = $imges;
					}

					if ($category == 'Organization' && ! empty($metaData['logo'])) {
						$img                    = $helper->imageInfo(absint($metaData['logo']));
						$local_business['logo'] = $helper->sanitizeOutPut($img['url'], 'url');
					}

					if (! empty($metaData['priceRange'])) {
						$local_business['priceRange'] = $helper->sanitizeOutPut($metaData['priceRange']);
					}

					if ($category == 'Restaurant') {
						if (! empty($metaData['servesCuisine'])) {
							$local_business['servesCuisine'] = $helper->sanitizeOutPut($metaData['servesCuisine']);
						}
						if (isset($metaData['menu_sections']) && is_array($metaData['menu_sections'])) {
							$local_business_menu_sections = [];
							foreach ($metaData['menu_sections'] as $menu_sections_single) {
								$menu_sections_single_schema = [
									'@type'   => 'MenuSection',
								];

								if (! empty($menu_sections_single['name'])) {
									$menu_sections_single_schema['name'] = $helper->sanitizeOutPut($menu_sections_single['name']);
								}

								if (! empty($menu_sections_single['desc'])) {
									$menu_sections_single_schema['description'] =  $helper->sanitizeOutPut($menu_sections_single['desc'], 'textarea');
								}

								if (! empty($menu_sections_single['url'])) {
									$menu_sections_single_schema['url'] = $helper->sanitizeOutPut($menu_sections_single['url'], 'url');
								}

								if (! empty($menu_sections_single['images'])) {
									$menu_section_images = [];
									foreach ($menu_sections_single['images'] as $image) {
										$img                   = $helper->imageInfo(absint($image));
										$menu_section_images[] = $helper->sanitizeOutPut($img['url'], 'url');
									}

									if ($menu_section_images) {
										$menu_sections_single_schema['image'] = $menu_section_images;
									}
								}

								if (! empty($menu_sections_single['availabilityStarts']) || ! empty($menu_sections_single['availabilityEnds'])) {
									$menu_sections_single_schema['offers'] = [
										'@type'               => 'Offer',
										'availabilityStarts'  => $helper->sanitizeOutPut($menu_sections_single['availabilityStarts']),
										'availabilityEnds'    => $helper->sanitizeOutPut($menu_sections_single['availabilityEnds']),
									];
								}

								if (isset($menu_sections_single['menu_items']) && is_array($menu_sections_single['menu_items'])) {
									$local_business_menu_sections_menu_items = [];
									foreach ($menu_sections_single['menu_items'] as $menu_items_single) {
										$menu_items_single_schema = [
											'@type'         => 'MenuItem',
											'name'          => $menu_items_single['name'] ? $helper->sanitizeOutPut($menu_items_single['name']) : null,
											'description'   => $menu_items_single['desc'] ? $helper->sanitizeOutPut($menu_items_single['desc']) : null,
										];
										$menu_items_single_schema['nutrition'] = [
											'@type' => 'NutritionInformation',
										];

										if ($menu_items_single['calories']) {
											$menu_items_single_schema['nutrition']['calories'] = $helper->sanitizeOutPut($menu_items_single['calories']);
										}
										if ($menu_items_single['fatContent']) {
											$menu_items_single_schema['nutrition']['fatContent'] = $helper->sanitizeOutPut($menu_items_single['fatContent']);
										}
										if ($menu_items_single['fiberContent']) {
											$menu_items_single_schema['nutrition']['fiberContent'] = $helper->sanitizeOutPut($menu_items_single['fiberContent']);
										}
										if ($menu_items_single['proteinContent']) {
											$menu_items_single_schema['nutrition']['proteinContent'] = $helper->sanitizeOutPut($menu_items_single['proteinContent']);
										}

										if ($menu_items_single['suitableForDiet']) {
											$menu_items_single_schema['suitableForDiet'] = $helper->sanitizeOutPut($menu_items_single['suitableForDiet']);
										}

										array_push($local_business_menu_sections_menu_items, $menu_items_single_schema);
									}
									$menu_sections_single_schema['hasMenuItem'] = $local_business_menu_sections_menu_items;
								}

								array_push($local_business_menu_sections, $menu_sections_single_schema);
							}
							$local_business['hasMenu']['@type']          = 'Menu';
							$local_business['hasMenu']['hasMenuSection'] = $local_business_menu_sections;
						}
					}

					if (! empty($metaData['address'][0]['addressLocality']) || ! empty($metaData['address'][0]['addressRegion'])
						|| ! empty($metaData['address'][0]['postalCode']) || ! empty($metaData['address'][0]['streetAddress'])) {
						$local_business['address'] = [
							'@type'           => 'PostalAddress',
							'addressLocality' => $helper->sanitizeOutPut($metaData['address'][0]['addressLocality']),
							'addressRegion'   => $helper->sanitizeOutPut($metaData['address'][0]['addressRegion']),
							'postalCode'      => $helper->sanitizeOutPut($metaData['address'][0]['postalCode']),
							'streetAddress'   => $helper->sanitizeOutPut($metaData['address'][0]['streetAddress']),
							'addressCountry'  => $helper->sanitizeOutPut($metaData['address'][0]['addressCountry']),
						];
					}

					if (! empty($metaData['geo'][0]['latitude']) || ! empty($metaData['geo'][0]['longitude'])) {
						$local_business['geo'] = [
							'@type'           => 'GeoCoordinates',
							'latitude'        => $helper->sanitizeOutPut($metaData['geo'][0]['latitude']),
							'longitude'       => $helper->sanitizeOutPut($metaData['geo'][0]['longitude']),
						];
					}

					if (! empty($metaData['telephone'])) {
						$local_business['telephone'] = $helper->sanitizeOutPut($metaData['telephone']);
					}

					if (! empty($metaData['url'])) {
						$local_business['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}

					if (isset($metaData['opening_hours']) && is_array($metaData['opening_hours'])) {
						$local_business_opening_hours = [];
						foreach ($metaData['opening_hours'] as $opening_hours_single) {
							$opening_hours_single_schema = [
								'@type'               => 'OpeningHoursSpecification',
								'dayOfWeek'           => $opening_hours_single['day'] ? $helper->sanitizeOutPut($opening_hours_single['day']) : '',
								'opens'               => $opening_hours_single['opens'] ? $helper->sanitizeOutPut($opening_hours_single['opens']) : '',
								'closes'              => $opening_hours_single['closes'] ? $helper->sanitizeOutPut($opening_hours_single['closes']) : '',
							];
							array_push($local_business_opening_hours, $opening_hours_single_schema);
						}
						$local_business['openingHoursSpecification'] = $local_business_opening_hours;
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_local_business', $local_business, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_local_business', $local_business, $metaData));
					}
					
					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$local_business_review = [
							'@context' => 'https://schema.org',
							'@type'    => 'Review',
						];
						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$local_business_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$local_business_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}

						unset($local_business['@context']);
						if (isset($local_business['description'])) {
							$local_business_review['description'] = Functions::filter_content($local_business['description'], 200);
							unset($local_business['description']);
						}
						if (isset($metaData['review_sameAs']) && ! empty($metaData['review_sameAs'])) {
							$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_sameAs'], 'textarea'));
							if (! empty($sameAs)) {
								$local_business['sameAs'] = $sameAs;
							}
						}

						$local_business_review['itemReviewed'] = $local_business;
						if (! empty($metaData['review_author'])) {
							$local_business_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$local_business_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$local_business_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$local_business_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$local_business_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_local_business_review', $local_business_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_local_business_review', $local_business_review, $metaData));
						}
					}
					break;

				case 'software_app':
					$app = [
						'@context' => 'http://schema.org',
						'@type'    => 'SoftwareApplication',
					];
					if (! empty($metaData['name'])) {
						$app['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$app['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['applicationCategory'])) {
						$app['applicationCategory'] = $helper->sanitizeOutPut($metaData['applicationCategory']);
					}

					if (! empty($metaData['operatingSystem'])) {
						$app['operatingSystem'] = $helper->sanitizeOutPut($metaData['operatingSystem']);
					}

					if (! empty($metaData['price'])) {
						$app['offers'] = [
							'@type' => 'Offer',
							'price' => $helper->sanitizeOutPut($metaData['price']),
						];
						if (! empty($metaData['priceCurrency'])) {
							$app['offers']['priceCurrency'] = $helper->sanitizeOutPut($metaData['priceCurrency']);
						}
					}
					if (isset($metaData['aggregate_ratingValue'])) {
						$app['aggregateRating'] = [
							'@type'       => 'AggregateRating',
							'ratingValue' => $helper->sanitizeOutPut($metaData['aggregate_ratingValue'], 'number'),
						];
						if (isset($metaData['aggregate_bestRating'])) {
							$app['aggregateRating']['bestRating'] = $helper->sanitizeOutPut($metaData['aggregate_bestRating'], 'number');
						}
						if (isset($metaData['aggregate_worstRating'])) {
							$app['aggregateRating']['worstRating'] = $helper->sanitizeOutPut($metaData['aggregate_worstRating'], 'number');
						}
						if (isset($metaData['aggregate_ratingCount'])) {
							$app['aggregateRating']['reviewCount'] = $helper->sanitizeOutPut($metaData['aggregate_ratingCount'], 'number');
						}
					}
					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_software_application', $app, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_software_application', $app, $metaData));
					}

					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$app_review = [
							'@context' => 'http://schema.org',
							'@type'    => 'Review',
						];
						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$app_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$app_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}

						unset($app['@context']);
						if (isset($app['description'])) {
							$app_review['description'] = Functions::filter_content($app['description'], 200);
							unset($app['description']);
						}
						if (isset($metaData['review_sameAs']) && ! empty($metaData['review_sameAs'])) {
							$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_sameAs'], 'textarea'));
							if (! empty($sameAs)) {
								$app['sameAs'] = $sameAs;
							}
						}

						$app_review['itemReviewed'] = $app;
						if (! empty($metaData['review_author'])) {
							$app_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$app_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$app_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$app_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$app_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}

						if ($without_script) {
							$html = apply_filters('rtseo_snippet_software_application_review', $app_review, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_software_application_review', $app_review, $metaData));
						}
					}
					break;

				case 'job_posting':
					$jobPosting             = [];
					$jobPosting['@context'] = 'http://schema.org';
					$jobPosting['@type']    = 'JobPosting';
					if (! empty($metaData['title'])) {
						$jobPosting['title'] = $helper->sanitizeOutPut($metaData['title']);
					}
					if (! empty($metaData['workHours'])) {
						$jobPosting['workHours'] = $helper->sanitizeOutPut($metaData['workHours']);
					}
					if (! empty($metaData['salaryAmount']) || ! empty($metaData['currency']) || ! empty($metaData['salaryAt'])) {
						$jobPosting['baseSalary'] = [
							'@type'    => 'MonetaryAmount',
							'currency' => $helper->sanitizeOutPut($metaData['currency']),
							'value'    => [
								'@type'    => 'QuantitativeValue',
								'value'    => $helper->sanitizeOutPut($metaData['salaryAmount']),
								'unitText' => $helper->sanitizeOutPut($metaData['salaryAt']),
							],
						];
					}
					if (! empty($metaData['jobBenefits'])) {
						$jobPosting['jobBenefits'] = $helper->sanitizeOutPut($metaData['jobBenefits']);
					}
					if (! empty($metaData['datePosted'])) {
						$jobPosting['datePosted'] = $helper->sanitizeOutPut($metaData['datePosted']);
					}
					if (! empty($metaData['validThrough'])) {
						$jobPosting['validThrough'] = $helper->sanitizeOutPut($metaData['validThrough']);
					}
					if (! empty($metaData['description'])) {
						$jobPosting['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['educationRequirements'])) {
						$jobPosting['educationRequirements'] = [
							'@type'              => 'EducationalOccupationalCredential',
							'credentialCategory' => $helper->sanitizeOutPut($metaData['educationRequirements']),
						];
					}
					if (! empty($metaData['employmentType'])) {
						$jobPosting['employmentType'] = $helper->sanitizeOutPut($metaData['employmentType']);
					}
					if (! empty($metaData['experienceRequirements'])) {
						$jobPosting['experienceRequirements'] = [
							'@type'              => 'OccupationalExperienceRequirements',
							'monthsOfExperience' => $helper->sanitizeOutPut($metaData['experienceRequirements']),
						];
					}
					if (! empty($metaData['incentiveCompensation'])) {
						$jobPosting['incentiveCompensation'] = $helper->sanitizeOutPut($metaData['incentiveCompensation']);
					}
					if (! empty($metaData['hiringOrganization'])) {
						$jobPosting['hiringOrganization'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['hiringOrganization']),
						];
					}
					if (! empty($metaData['industry'])) {
						$jobPosting['industry'] = $helper->sanitizeOutPut($metaData['industry']);
					}
					if (! empty($metaData['addressLocality']) || ! empty($metaData['addressRegion'])) {
						$jobPosting['jobLocation'] = [
							'@type'   => 'Place',
							'address' => [
								'@type'           => 'PostalAddress',
								'addressLocality' => $helper->sanitizeOutPut($metaData['addressLocality']),
								'addressRegion'   => $helper->sanitizeOutPut($metaData['addressRegion']),
								'postalCode'      => $helper->sanitizeOutPut($metaData['postalCode']),
								'streetAddress'   => $helper->sanitizeOutPut($metaData['streetAddress']),
							],
						];
					}
					if (! empty($metaData['occupationalCategory'])) {
						$jobPosting['occupationalCategory'] = $helper->sanitizeOutPut($metaData['occupationalCategory']);
					}
					if (! empty($metaData['qualifications'])) {
						$jobPosting['qualifications'] = $helper->sanitizeOutPut(
							$metaData['qualifications'],
							'textarea'
						);
					}
					if (! empty($metaData['responsibilities'])) {
						$jobPosting['responsibilities'] = $helper->sanitizeOutPut(
							$metaData['responsibilities'],
							'textarea'
						);
					}
					if (! empty($metaData['salaryCurrency'])) {
						$jobPosting['salaryCurrency'] = $helper->sanitizeOutPut($metaData['salaryCurrency']);
					}
					if (! empty($metaData['skills'])) {
						$jobPosting['skills'] = $helper->sanitizeOutPut($metaData['skills'], 'textarea');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_job_posting', $jobPosting, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_job_posting', $jobPosting, $metaData));
					}

					break;

				case 'faq':
					$faqSchema = [
						'@context' => 'http://schema.org',
						'@type'    => 'FAQPage',
					];

					if (isset($metaData['faqs']) && is_array($metaData['faqs'])) {
						$faqs_schema = [];
						foreach ($metaData['faqs'] as $position => $faq_item) {
							$faq_item_schema = [
								'@type'          => 'Question',
								'name'           => $faq_item['ques'] ? $helper->sanitizeOutPut($faq_item['ques']) : null,
								'acceptedAnswer' => [
									'@type' => 'Answer',
									'text'  => isset($faq_item['ans']) ? $helper->sanitizeOutPut($faq_item['ans'], 'textarea') : null,
								],
							];
							array_push($faqs_schema, $faq_item_schema);
						}
						if (count($faqs_schema) == 1) {
							$faqSchema['mainEntity'] = $faqs_schema[0];
						} else {
							$faqSchema['mainEntity'] = $faqs_schema;
						}
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_faq', $faqSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_faq', $faqSchema, $metaData));
					}

					break;

				case 'service':
					$service = [
						'@context' => 'https://schema.org',
						'@type'    => 'Service',
					];
					if (! empty($metaData['name'])) {
						$service['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['serviceType'])) {
						$service['serviceType'] = $helper->sanitizeOutPut($metaData['serviceType']);
					}
					if (! empty($metaData['award'])) {
						$service['award'] = $helper->sanitizeOutPut($metaData['award']);
					}
					if (! empty($metaData['category'])) {
						$service['category'] = $helper->sanitizeOutPut($metaData['category']);
					}
					if (! empty($metaData['providerMobility'])) {
						$service['providerMobility'] = $helper->sanitizeOutPut($metaData['providerMobility']);
					}
					if (! empty($metaData['additionalType'])) {
						$service['additionalType'] = $helper->sanitizeOutPut($metaData['additionalType']);
					}
					if (! empty($metaData['alternateName'])) {
						$service['alternateName'] = $helper->sanitizeOutPut($metaData['alternateName']);
					}
					if (! empty($metaData['image'])) {
						$service['image'] = $helper->sanitizeOutPut($metaData['image']);
					}
					if (! empty($metaData['mainEntityOfPage'])) {
						$service['mainEntityOfPage'] = $helper->sanitizeOutPut($metaData['mainEntityOfPage']);
					}
					if (! empty($metaData['sameAs'])) {
						$service['sameAs'] = $helper->sanitizeOutPut($metaData['sameAs']);
					}
					if (! empty($metaData['url'])) {
						$service['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_service', $service, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_service', $service, $metaData));
					}

					break;

				case 'question_answer':
					$questionAnswerSchema = [
						'@context' => 'https://schema.org',
						'@type'    => 'QAPage',
					];

					$question = [
						'@type'    => 'Question',
					];
					if (! empty($metaData['name'])) {
						$question['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['text'])) {
						$question['text'] = $helper->sanitizeOutPut(
							$metaData['text'],
							'textarea'
						);
					}
					if (! empty($metaData['answerCount'])) {
						$question['answerCount'] = $helper->sanitizeOutPut($metaData['answerCount']);
					}
					if (! empty($metaData['upvoteCount'])) {
						$question['upvoteCount'] = $helper->sanitizeOutPut($metaData['upvoteCount']);
					}
					if (! empty($metaData['dateCreated'])) {
						$question['dateCreated'] = $helper->sanitizeOutPut($metaData['dateCreated']);
					}
					if (! empty($metaData['author'])) {
						$question['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($metaData['author']),
						];
					}

					if (isset($metaData['answers']) && is_array($metaData['answers'])) {
						$acceptedAnswer = $suggestedAnswer = [];
						foreach ($metaData['answers'] as $position => $answer_item) {
							$answer_item_schema = [
								'@type'          => 'Answer',
								'text'           => $answer_item['text'] ? $helper->sanitizeOutPut($answer_item['text']) : null,

								'dateCreated'   => $answer_item['dateCreated'] ? $helper->sanitizeOutPut($answer_item['dateCreated']) : null,

								'upvoteCount'   => $answer_item['upvoteCount'] ? $helper->sanitizeOutPut($answer_item['upvoteCount']) : 0,

								'url'           => $answer_item['url'] ? $helper->sanitizeOutPut($answer_item['url'], 'url') : null,

								'author' => [
									'@type'   => 'Person',
									'author'  => isset($answer_item['author']) ? $helper->sanitizeOutPut($answer_item['author']) : null,
								],
							];

							if ($answer_item['answerType'] == 'normal') {
								array_push($suggestedAnswer, $answer_item_schema);
							} else {
								$acceptedAnswer = $answer_item_schema;
							}

							if ($acceptedAnswer) {
								$question['acceptedAnswer'] = $acceptedAnswer;
							}

							if ($suggestedAnswer) {
								$question['suggestedAnswer'] = $suggestedAnswer;
							}
						}
					}

					$questionAnswerSchema['mainEntity'] = $question;

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_question_answer', $questionAnswerSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_question_answer', $questionAnswerSchema, $metaData));
					}

					break;

				case 'how_to':
					$howToSchema = [
						'@context' => 'https://schema.org',
						'@type'    => 'HowTo',
					];

					if (! empty($metaData['name'])) {
						$howToSchema['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$howToSchema['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}

					if (! empty($metaData['image'])) {
						$img                  = $helper->imageInfo(absint($metaData['image']));
						$howToSchema['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}

					if (! empty($metaData['price'])) {
						$howToSchema['estimatedCost'] = [
							'@type' => 'MonetaryAmount',
							'value' => $helper->sanitizeOutPut($metaData['price']),
						];
						if (! empty($metaData['priceCurrency'])) {
							$howToSchema['estimatedCost']['currency'] = $helper->sanitizeOutPut($metaData['priceCurrency']);
						}
					}

					if (isset($metaData['supply']) && is_array($metaData['supply'])) {
						$how_to_supply = [];
						foreach ($metaData['supply'] as $supply_single) {
							$supply_single_schema = [
								'@type'          => 'HowToSupply',
								'name'           => $supply_single['name'] ? $helper->sanitizeOutPut($supply_single['name']) : null,
							];
							array_push($how_to_supply, $supply_single_schema);
						}
						$howToSchema['supply'] = $how_to_supply;
					}

					if (isset($metaData['tool']) && is_array($metaData['tool'])) {
						$how_to_tool = [];
						foreach ($metaData['tool'] as $tool_single) {
							$tool_single_schema = [
								'@type'   => 'HowToTool',
								'name'    => $tool_single['name'] ? $helper->sanitizeOutPut($tool_single['name']) : null,
							];
							array_push($how_to_tool, $tool_single_schema);
						}
						$howToSchema['tool'] = $how_to_tool;
					}

					if (isset($metaData['step']) && is_array($metaData['step'])) {
						$how_to_step = [];
						foreach ($metaData['step'] as $step_single) {
							$step_single_schema = [
								'@type'   => 'HowToStep',
							];

							if (! empty($step_single['name'])) {
								$step_single_schema['name'] = $helper->sanitizeOutPut($step_single['name']);
							}

							if (! empty($step_single['text'])) {
								$step_single_schema['text'] =  $helper->sanitizeOutPut($step_single['text'], 'textarea');
							}

							if (! empty($step_single['url'])) {
								$step_single_schema['url'] = $helper->sanitizeOutPut($step_single['url'], 'url');
							}

							if (! empty($step_single['image'])) {
								$img                         = $helper->imageInfo(absint($step_single['image']));
								$step_single_schema['image'] = [
									'@type'  => 'ImageObject',
									'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
									'height' => $img['height'],
									'width'  => $img['width'],
								];
							}

							if (! empty($step_single['clipId'])) {
								$step_single_schema['video'] = [
									'@id'  => $helper->sanitizeOutPut($step_single['clipId']),
								];
							}

							if (isset($step_single['direction']) && is_array($step_single['direction'])) {
								$how_to_step_direction = [];
								foreach ($step_single['direction'] as $direction_single) {
									$direction_single_schema = [
										'@type'          => 'HowToDirection',
										'text'           => $direction_single['text'] ? $helper->sanitizeOutPut($direction_single['text']) : null,
									];
									array_push($how_to_step_direction, $direction_single_schema);
								}
								$step_single_schema['itemListElement'] = $how_to_step_direction;
							}

							array_push($how_to_step, $step_single_schema);
						}
						$howToSchema['step'] = $how_to_step;
					}

					if (isset($metaData['video']) && is_array($metaData['video'])) {
						$how_to_video = [];
						foreach ($metaData['video'] as $video_single) {
							if ($video_single['name'] && $video_single['contentUrl']) {
								$video_single_schema = [
									'@type'         => 'VideoObject',
									'name'          => $video_single['name'] ? $helper->sanitizeOutPut($video_single['name']) : null,
									'description'   => $video_single['description'] ? $helper->sanitizeOutPut($video_single['description']) : null,
									'contentUrl'    => $video_single['contentUrl'] ? $helper->sanitizeOutPut($video_single['contentUrl']) : null,
									'embedUrl'      => $video_single['embedUrl'] ? $helper->sanitizeOutPut($video_single['embedUrl']) : null,
									'uploadDate'    => $video_single['uploadDate'] ? $helper->sanitizeOutPut($video_single['uploadDate']) : null,
									'duration'      => $video_single['duration'] ? $helper->sanitizeOutPut($video_single['duration']) : null,
								];
								if (! empty($video_single['thumbnailUrl'])) {
									$img                                 = $helper->imageInfo(absint($video_single['thumbnailUrl']));
									$video_single_schema['thumbnailUrl'] = $helper->sanitizeOutPut($img['url'], 'url');
								}

								$how_to_video = $video_single_schema;

								if (isset($video_single['clip']) && is_array($video_single['clip'])) {
									$how_to_video_clip = [];
									foreach ($video_single['clip'] as $clip_single) {
										$clip_single_schema = [
											'@type'         => 'Clip',
											'@id'           => $clip_single['id'] ? $helper->sanitizeOutPut($clip_single['id']) : '',
											'name'          => $clip_single['name'] ? $helper->sanitizeOutPut($clip_single['name']) : null,
											'startOffset'   => $clip_single['startOffset'] ? $helper->sanitizeOutPut($clip_single['startOffset']) : null,
											'endOffset'     => $clip_single['endOffset'] ? $helper->sanitizeOutPut($clip_single['endOffset']) : null,
											'endOffset'     => $clip_single['endOffset'] ? $helper->sanitizeOutPut($clip_single['endOffset']) : null,
											'url'           => $clip_single['url'] ? $helper->sanitizeOutPut($clip_single['url'], 'url') : null,
										];
										array_push($how_to_video_clip, $clip_single_schema);
									}
									$how_to_video['hasPart'] = $how_to_video_clip;
								}
							}
						}
						if ($how_to_video) {
							$howToSchema['video'] = $how_to_video;
						}
					}

					if (! empty($metaData['totalTime'])) {
						$howToSchema['totalTime'] = $helper->sanitizeOutPut($metaData['totalTime']);
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_how_to', $howToSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_how_to', $howToSchema, $metaData));
					}

					break;

				case 'about':
					$aboutSchema = [
						'@context' => 'https://schema.org',
						'@type'    => 'AboutPage',
					];

					if (! empty($metaData['name'])) {
						$aboutSchema['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$aboutSchema['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['image'])) {
						$img                  = $helper->imageInfo(absint($metaData['image']));
						$aboutSchema['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['url'])) {
						$aboutSchema['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}

					if (isset($metaData['sameAs']) && ! empty($metaData['sameAs'])) {
						$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['sameAs'], 'textarea'));
						if (! empty($sameAs)) {
							$aboutSchema['sameAs'] = $sameAs;
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_about', $aboutSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_about', $aboutSchema, $metaData));
					}
					
					break;

				case 'contact':
					$contactSchema = [
						'@context' => 'https://schema.org',
						'@type'    => 'ContactPage',
					];

					if (! empty($metaData['name'])) {
						$contactSchema['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['description'])) {
						$contactSchema['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['image'])) {
						$img                    = $helper->imageInfo(absint($metaData['image']));
						$contactSchema['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['url'])) {
						$contactSchema['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}

					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_contact', $contactSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_contact', $contactSchema, $metaData));
					}
					break;

				case 'person':
					$personSchema = [
						'@context' => 'https://schema.org',
						'@type'    => 'Person',
					];

					if (! empty($metaData['name'])) {
						$personSchema['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['image'])) {
						$img                   = $helper->imageInfo(absint($metaData['image']));
						$personSchema['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($metaData['telephone'])) {
						$personSchema['telephone'] = $helper->sanitizeOutPut($metaData['telephone']);
					}
					if (! empty($metaData['email'])) {
						$personSchema['email'] = $helper->sanitizeOutPut($metaData['email']);
					}
					if (! empty($metaData['url'])) {
						$personSchema['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}
					if (! empty($metaData['jobTitle'])) {
						$personSchema['jobTitle'] = $helper->sanitizeOutPut($metaData['jobTitle']);
					}
					if (! empty($metaData['description'])) {
						$personSchema['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if (! empty($metaData['birthPlace'])) {
						$personSchema['birthPlace'] = $helper->sanitizeOutPut($metaData['birthPlace']);
					}
					if (! empty($metaData['birthDate'])) {
						$personSchema['birthDate'] = $helper->sanitizeOutPut($metaData['birthDate']);
					}
					if (! empty($metaData['height'])) {
						$personSchema['height'] = $helper->sanitizeOutPut($metaData['height']);
					}
					if (! empty($metaData['gender'])) {
						$personSchema['gender'] = $helper->sanitizeOutPut($metaData['gender']);
					}
					if (! empty($metaData['nationality'])) {
						$personSchema['nationality'] = $helper->sanitizeOutPut($metaData['nationality']);
					}
					if (isset($metaData['sameAs']) && ! empty($metaData['sameAs'])) {
						$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['sameAs'], 'textarea'));
						if (! empty($sameAs)) {
							$personSchema['sameAs'] = $sameAs;
						}
					}
					if (! empty($metaData['addresses'])) {
						$addresses = [];
						$address_i = 1;
						foreach ($metaData['addresses'] as $address) {
							if (! empty($address['addressLocality']) || ! empty($address['addressRegion'])
						|| ! empty($address['postalCode']) || ! empty($address['streetAddress'])) {
								if (! function_exists('rtrsp') && $address_i > 1) {
									break;
								}

								$addresses[] = [
									'@type'           => 'PostalAddress',
									'addressLocality' => $helper->sanitizeOutPut($address['addressLocality']),
									'addressRegion'   => $helper->sanitizeOutPut($address['addressRegion']),
									'postalCode'      => $helper->sanitizeOutPut($address['postalCode']),
									'streetAddress'   => $helper->sanitizeOutPut($address['streetAddress']),
									'addressCountry'  => $helper->sanitizeOutPut($address['addressCountry']),
								];
								$address_i++;
							}
						}

						if ($addresses) {
							$personSchema['address'] = $addresses;
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_person', $personSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_person', $personSchema, $metaData));
					}

					break;

				case 'breadcrumb':
					$breadcrumbSchema = [
						'@context' => 'http://schema.org',
						'@type'    => 'BreadcrumbList',
					];

					if (isset($metaData['items']) && is_array($metaData['items'])) {
						$breadcrumbs_schema = [];
						foreach ($metaData['items'] as $position => $breadcrumb_item) {
							$breadcrumb_item_schema = [
								'@type'          => 'ListItem',
								'position'       => $position + 1,
								'name'           => $breadcrumb_item['name'] ? $helper->sanitizeOutPut($breadcrumb_item['name']) : '',
								'item'           => $breadcrumb_item['item'] ? $helper->sanitizeOutPut($breadcrumb_item['item']) : '',

							];
							array_push($breadcrumbs_schema, $breadcrumb_item_schema);
						}

						$breadcrumbSchema['itemListElement'] = $breadcrumbs_schema;
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_breadcrumb', $breadcrumbSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_breadcrumb', $breadcrumbSchema, $metaData));
					}
					break;

				case 'itemlist':
					$itemlistSchema = [
						'@context' => 'http://schema.org',
						'@type'    => 'ItemList',
					];

					if (isset($metaData['items']) && is_array($metaData['items'])) {
						$itemlists_schema = [];
						foreach ($metaData['items'] as $position => $itemlist_item) {
							$itemlist_item_schema = [
								'@type'          => 'ListItem',
								'position'       => $position + 1,
								'url'            => $itemlist_item['url'] ? $helper->sanitizeOutPut($itemlist_item['url']) : '',

							];
							array_push($itemlists_schema, $itemlist_item_schema);
						}

						$itemlistSchema['itemListElement'] = $itemlists_schema;
					}

					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_itemlist', $itemlistSchema, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_itemlist', $itemlistSchema, $metaData));
					}
					break;

				case 'special_announcement':
					$announcement = [
						'@context' => 'https://schema.org',
						'@type'    => 'SpecialAnnouncement',
						'category' => 'https://www.wikidata.org/wiki/Q81068910',
					];
					if (! empty($metaData['name'])) {
						$announcement['name'] = $helper->sanitizeOutPut($metaData['name']);
					}
					if (! empty($metaData['datePublished'])) {
						$announcement['datePosted'] = $helper->sanitizeOutPut($metaData['datePublished']);
					}
					if (! empty($metaData['expires'])) {
						$announcement['expires'] = $helper->sanitizeOutPut($metaData['expires']);
					}
					if (! empty($metaData['text'])) {
						$announcement['text'] = $helper->sanitizeOutPut($metaData['text'], 'textarea');
					}
					if (! empty($metaData['url'])) {
						$announcement['url'] = $helper->sanitizeOutPut($metaData['url'], 'url');
					}
					if (isset($metaData['locations']) && is_array($metaData['locations']) && ! empty($metaData['locations'])) {
						$locations_schema = [];
						foreach ($metaData['locations'] as $position => $location) {
							if ($location['type']) {
								$location_schema = [
									'@type'   => $helper->sanitizeOutPut($location['type']),
									'name'    => ! empty($location['name']) ? $helper->sanitizeOutPut($location['name']) : '',
									'url'     => ! empty($location['url']) ? $helper->sanitizeOutPut($location['url'], 'url') : '',
									'address' => [
										'@type' => 'PostalAddress',
									],
								];
								if (! empty($location['id'])) {
									$location_schema['@id'] = $helper->sanitizeOutPut($location['id']);
								}
								if (! empty($location['image'])) {
									$img                      = $helper->imageInfo(absint($location['image']));
									$location_schema['image'] = $helper->sanitizeOutPut($img['url'], 'url');
								}
								if (! empty($location['url'])) {
									$location_schema['url'] = $helper->sanitizeOutPut($location['url'], 'url');
								}
								if (! empty($location['address_street'])) {
									$location_schema['address']['streetAddress'] = $helper->sanitizeOutPut($location['address_street']);
								}
								if (! empty($location['address_locality'])) {
									$location_schema['address']['addressLocality'] = $helper->sanitizeOutPut($location['address_locality']);
								}
								if (! empty($location['address_post_code'])) {
									$location_schema['address']['postalCode'] = $helper->sanitizeOutPut($location['address_post_code']);
								}
								if (! empty($location['address_region'])) {
									$location_schema['address']['addressRegion'] = $helper->sanitizeOutPut($location['address_region']);
								}
								if (! empty($location['address_country'])) {
									$location_schema['address']['addressCountry'] = $helper->sanitizeOutPut($location['address_country']);
								}
								if (! empty($location['priceRange'])) {
									$location_schema['priceRange'] = $helper->sanitizeOutPut($location['priceRange']);
								}
								if (! empty($location['telephone'])) {
									$location_schema['telephone'] = $helper->sanitizeOutPut($location['telephone']);
								}
								array_push($locations_schema, $location_schema);
							}
						}
						if (count($locations_schema) === 1) {
							$announcement['announcementLocation'] = $locations_schema[0];
						} else {
							$announcement['announcementLocation'] = $locations_schema;
						}
					}
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_item_list', $announcement, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_item_list', $announcement, $metaData));
					}
					
					break;
				case 'mosque':
					$output             = [];
					$output['@context'] = 'https://schema.org';
					$output['@type']    = 'Mosque';
					if ( ! empty( $metaData['name'] ) ) {
						$output['name'] = esc_html( $metaData['name'] );
					}
					if ( ! empty( $metaData['description'] ) ) {
						$output['description'] = esc_html( $metaData['description'] );
					}
					if ( ! empty( $metaData['url'] ) ) {
						$output['url'] = esc_html( $metaData['url'] );
					}
					if ( ! empty( $metaData['capacity'] ) ) {
						$output['maximumAttendeeCapacity'] = esc_html( $metaData['capacity'] );
					}
					if ( ! empty( $metaData['image'] ) ) {
						$image_id = absint( $metaData['image'] );
						$image_src = wp_get_attachment_image_src( $image_id );
						if( $image_src ){
							$output['image']['@type'] = 'ImageObject';
							if( ! empty( $image_src[0] ) ){
								$output['image']['url'] = esc_url( $image_src[0] ) ;
							}
							if( ! empty( $image_src[1] ) ){
								$output['image']['height'] = esc_html( $image_src[1] ) ;
							}
							if( ! empty( $image_src[2] ) ){
								$output['image']['width'] = esc_html( $image_src[2] ) ;
							}
						}
					}
					if ( ! empty( $metaData['address'] ) || ! empty( $metaData['address'][0] ) ) {
						$output['address']['@type'] = 'PostalAddress';
						$mosque_address             = $metaData['address'][0];
						if ( ! empty( $mosque_address['address-country'] ) ) {
							$output['address']['addressCountry'] = esc_html( $mosque_address['address-country'] );
						}
						if ( ! empty( $mosque_address['address-locality'] ) ) {
							$output['address']['addressLocality'] = esc_html( $mosque_address['address-locality'] );
						}
						if ( ! empty( $mosque_address['address-region'] ) ) {
							$output['address']['addressRegion'] = esc_html( $mosque_address['address-region'] );
						}
						if ( ! empty( $mosque_address['postal-code'] ) ) {
							$output['address']['PostalCode'] = esc_html( $mosque_address['postal-code'] );
						}
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_tv_series', $output, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_tv_series', $output, $metaData ) );
					}
					break;

				case 'church':
					$output             = [];
					$output['@context'] = 'https://schema.org';
					$output['@type']    = 'Church';
					if ( ! empty( $metaData['name'] ) ) {
						$output['name'] = esc_html( $metaData['name'] );
					}
					if ( ! empty( $metaData['description'] ) ) {
						$output['description'] = esc_html( $metaData['description'] );
					}
					if ( ! empty( $metaData['url'] ) ) {
						$output['url'] = esc_html( $metaData['url'] );
					}
					if ( ! empty( $metaData['capacity'] ) ) {
						$output['maximumAttendeeCapacity'] = esc_html( $metaData['capacity'] );
					}
					if ( ! empty( $metaData['image'] ) ) {
						$image_id = absint( $metaData['image'] );
						$image_src = wp_get_attachment_image_src( $image_id );
						if( $image_src ){
							$output['image']['@type'] = 'ImageObject';
							if( ! empty( $image_src[0] ) ){
								$output['image']['url'] = esc_url( $image_src[0] ) ;
							}
							if( ! empty( $image_src[1] ) ){
								$output['image']['height'] = esc_html( $image_src[1] ) ;
							}
							if( ! empty( $image_src[2] ) ){
								$output['image']['width'] = esc_html( $image_src[2] ) ;
							}
						}
					}
					if ( ! empty( $metaData['address'] ) || ! empty( $metaData['address'][0] ) ) {
						$output['address']['@type'] = 'PostalAddress';
						$mosque_address             = $metaData['address'][0];
						if ( ! empty( $mosque_address['address-country'] ) ) {
							$output['address']['addressCountry'] = esc_html( $mosque_address['address-country'] );
						}
						if ( ! empty( $mosque_address['address-locality'] ) ) {
							$output['address']['addressLocality'] = esc_html( $mosque_address['address-locality'] );
						}
						if ( ! empty( $mosque_address['address-region'] ) ) {
							$output['address']['addressRegion'] = esc_html( $mosque_address['address-region'] );
						}
						if ( ! empty( $mosque_address['postal-code'] ) ) {
							$output['address']['PostalCode'] = esc_html( $mosque_address['postal-code'] );
						}
					}
					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_church', $output, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_church', $output, $metaData ) );
					}
					break;
				case 'hindutemple':
					$output             = [];
					$output['@context'] = 'https://schema.org';
					$output['@type']    = 'HinduTemple';
					if ( ! empty( $metaData['name'] ) ) {
						$output['name'] = esc_html( $metaData['name'] );
					}
					if ( ! empty( $metaData['description'] ) ) {
						$output['description'] = esc_html( $metaData['description'] );
					}
					if ( ! empty( $metaData['url'] ) ) {
						$output['url'] = esc_html( $metaData['url'] );
					}
					if ( ! empty( $metaData['capacity'] ) ) {
						$output['maximumAttendeeCapacity'] = esc_html( $metaData['capacity'] );
					}
					if ( ! empty( $metaData['image'] ) ) {
						$image_id = absint( $metaData['image'] );
						$image_src = wp_get_attachment_image_src( $image_id );
						if( $image_src ){
							$output['image']['@type'] = 'ImageObject';
							if( ! empty( $image_src[0] ) ){
								$output['image']['url'] = esc_url( $image_src[0] ) ;
							}
							if( ! empty( $image_src[1] ) ){
								$output['image']['height'] = esc_html( $image_src[1] ) ;
							}
							if( ! empty( $image_src[2] ) ){
								$output['image']['width'] = esc_html( $image_src[2] ) ;
							}
						}
					}
					if ( ! empty( $metaData['address'] ) || ! empty( $metaData['address'][0] ) ) {
						$output['address']['@type'] = 'PostalAddress';
						$mosque_address             = $metaData['address'][0];
						if ( ! empty( $mosque_address['address-country'] ) ) {
							$output['address']['addressCountry'] = esc_html( $mosque_address['address-country'] );
						}
						if ( ! empty( $mosque_address['address-locality'] ) ) {
							$output['address']['addressLocality'] = esc_html( $mosque_address['address-locality'] );
						}
						if ( ! empty( $mosque_address['address-region'] ) ) {
							$output['address']['addressRegion'] = esc_html( $mosque_address['address-region'] );
						}
						if ( ! empty( $mosque_address['postal-code'] ) ) {
							$output['address']['PostalCode'] = esc_html( $mosque_address['postal-code'] );
						}
					}
					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_hindutemple', $output, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_hindutemple', $output, $metaData ) );
					}

					break;
				
				case 'buddhisttemple':
					$output             = [];
					$output['@context'] = 'https://schema.org';
					$output['@type']    = 'BuddhistTemple';
					if ( ! empty( $metaData['name'] ) ) {
						$output['name'] = esc_html( $metaData['name'] );
					}
					if ( ! empty( $metaData['description'] ) ) {
						$output['description'] = esc_html( $metaData['description'] );
					}
					if ( ! empty( $metaData['url'] ) ) {
						$output['url'] = esc_html( $metaData['url'] );
					}
					if ( ! empty( $metaData['image'] ) ) {
						$image_id = absint( $metaData['image'] );
						$image_src = wp_get_attachment_image_src( $image_id );
						if( $image_src ){
							$output['image']['@type'] = 'ImageObject';
							if( ! empty( $image_src[0] ) ){
								$output['image']['url'] = esc_url( $image_src[0] ) ;
							}
							if( ! empty( $image_src[1] ) ){
								$output['image']['height'] = esc_html( $image_src[1] ) ;
							}
							if( ! empty( $image_src[2] ) ){
								$output['image']['width'] = esc_html( $image_src[2] ) ;
							}
						}
					}

					if ( ! empty( $metaData['capacity'] ) ) {
						$output['maximumAttendeeCapacity'] = esc_html( $metaData['capacity'] );
					}
					if ( ! empty( $metaData['address'] ) || ! empty( $metaData['address'][0] ) ) {
						$output['address']['@type'] = 'PostalAddress';
						$mosque_address             = $metaData['address'][0];
						if ( ! empty( $mosque_address['address-country'] ) ) {
							$output['address']['addressCountry'] = esc_html( $mosque_address['address-country'] );
						}
						if ( ! empty( $mosque_address['address-locality'] ) ) {
							$output['address']['addressLocality'] = esc_html( $mosque_address['address-locality'] );
						}
						if ( ! empty( $mosque_address['address-region'] ) ) {
							$output['address']['addressRegion'] = esc_html( $mosque_address['address-region'] );
						}
						if ( ! empty( $mosque_address['postal-code'] ) ) {
							$output['address']['PostalCode'] = esc_html( $mosque_address['postal-code'] );
						}
					}
					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_buddhisttemple', $output, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_buddhisttemple', $output, $metaData ) );
					}
					break;

				case 'tech_article':
					$techarticle = [
						'@context' => 'https://schema.org',
						'@type'    => 'TechArticle',
					];
					if ( ! empty( $metaData['name'] ) ) {
						$techarticle['headline'] = $helper->sanitizeOutPut( $metaData['name'] );
					}
					if ( ! empty( $metaData['mainEntityOfPage'] ) ) {
						$techarticle['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut( $metaData['mainEntityOfPage'] ),
						];
					}
					if ( ! empty( $metaData['author'] ) ) {
						$techarticle['author']['name']  = $helper->sanitizeOutPut( $metaData['author'] );
						if ( ! empty( $metaData['author_type'] ) ) {
							$techarticle['author']['@type'] = $helper->sanitizeOutPut( $metaData['author_type'] );
						}
						if ( ! empty( $metaData['author_url'] ) ) {
							$techarticle['author']['url'] = $helper->sanitizeOutPut( $metaData['author_url'], 'url' );
						}
						if ( ! empty( $metaData['auth_description'] ) ) {
							$techarticle['author']['description'] = $helper->sanitizeOutPut( $metaData['auth_description'] );
						}
					}

					if ( ! empty( $metaData['image'] ) ) {
						$img                  = $helper->imageInfo( absint( $metaData['image'] ) );
						$techarticle['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut( $img['url'], 'url' ),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if ( ! empty( $metaData['datePublished'] ) ) {
						$techarticle['datePublished'] = $helper->sanitizeOutPut( $metaData['datePublished'] );
					}
					if ( ! empty( $metaData['dateModified'] ) ) {
						$techarticle['dateModified'] = $helper->sanitizeOutPut( $metaData['dateModified'] );
					}

					if ( ! empty( $metaData['publisher'] ) ) {
						if ( ! empty( $metaData['publisherImage'] ) ) {
							$img = $helper->imageInfo( absint( $metaData['publisherImage'] ) );
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut( $img['url'], 'url' ),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$techarticle['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut( $metaData['publisher'] ),
							'logo'  => $plA,
						];
					}

					if ( ! empty( $metaData['description'] ) ) {
						$techarticle['description'] = $helper->sanitizeOutPut(
							$metaData['description'],
							'textarea'
						);
					}
					if ( ! empty( $metaData['articleBody'] ) ) {
						$techarticle['articleBody'] = $helper->sanitizeOutPut(
							$metaData['articleBody'],
							'textarea'
						);
					}
					if ( ! empty( $metaData['keywords'] ) ) {
						$techarticle['keywords'] = $helper->sanitizeOutPut( $metaData['keywords'] );
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_tech_article', $techarticle, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_tech_article', $techarticle, $metaData ) );
					}
					break;
				case 'medical_webpage':
					$medical_webpage = [
						'@context' => 'https://schema.org',
						'@type'    => 'MedicalWebPage',
					];
					if ( ! empty( $metaData['headline'] ) ) {
						$medical_webpage['headline'] = $helper->sanitizeOutPut( $metaData['headline'] );
					}
					if ( ! empty( $metaData['webpage_url'] ) ) {
						$medical_webpage['url'] = $helper->sanitizeOutPut( $metaData['webpage_url'], 'url' );
					}
					if ( ! empty( $metaData['specialty_url'] ) ) {
						$medical_webpage['specialty'] = $helper->sanitizeOutPut( $metaData['specialty_url'], 'url' );
					}
					if ( ! empty( $metaData['image'] ) ) {
						$img                  = $helper->imageInfo( absint( $metaData['image'] ) );
						$medical_webpage['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut( $img['url'], 'url' ),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					
					if ( ! empty( $metaData['publisher'] ) ) {
						if ( ! empty( $metaData['publisherImage'] ) ) {
							$img = $helper->imageInfo( absint( $metaData['publisherImage'] ) );
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut( $img['url'], 'url' ),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$medical_webpage['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut( $metaData['publisher'] ),
							'logo'  => $plA,
						];
					}
					if ( ! empty( $metaData['datePublished'] ) ) {
						$medical_webpage['datePublished'] = $helper->sanitizeOutPut( $metaData['datePublished'] );
					}
					if ( ! empty( $metaData['dateModified'] ) ) {
						$medical_webpage['dateModified'] = $helper->sanitizeOutPut( $metaData['dateModified'] );
					}
					
					if ( ! empty( $metaData['lastreviewed'] ) ) {
						$medical_webpage['lastReviewed'] = $helper->sanitizeOutPut( $metaData['lastreviewed'] );
					}
					
					if ( ! empty( $metaData['maincontentofpage'] ) ) {
						$medical_webpage['mainContentOfPage'] = $helper->sanitizeOutPut( $metaData['maincontentofpage'] );
					}
					if ( ! empty( $metaData['about'] ) ) {
						$medical_webpage['about']['@type'] = "MedicalCondition";
						$medical_webpage['about']['name'] = $helper->sanitizeOutPut( $metaData['about'] );
					}
					if ( ! empty( $metaData['description'] ) ) {
						$medical_webpage['description'] = $helper->sanitizeOutPut( $metaData['description'] );
					}
					if ( ! empty( $metaData['keywords'] ) ) {
						$medical_webpage['keywords'] = $helper->sanitizeOutPut( $metaData['keywords'] );
					}
					
					if ($without_script) {
						$html = apply_filters('rtseo_snippet_medical_webpage', $medical_webpage, $metaData);
					} else {
						$html .= $this->getJsonEncode( apply_filters( 'rtseo_snippet_medical_webpage', $medical_webpage, $metaData ) );
					}
					break;
				
				default:
			}
			$html .=  do_action('rtseo_snippet_others_schema_output', $schemaCat, $metaData, $without_script, $this );
		}

		return $html;
	}

	/**
	 * schema output.
	 *
	 * @return object
	 */
	public function autoSchemaOutput($schemaCat, $post_id = null, $without_script = false) {
		$html     = null;
		$metaData = [];
		if ($schemaCat) {
			$helper = new Functions();
			$post   = get_post($post_id);

			$publisher = rtrs()->get_options('rtrs_schema_publisher_settings', 'publisher_name');
			if ($publisher) {
				$metaData['publisher'] = $publisher;
			}

			$publisher_logo = rtrs()->get_options('rtrs_schema_publisher_settings', 'publisher_logo');
			if ($publisher_logo) {
				$metaData['publisherImage'] = $publisher_logo;
			}

			switch ($schemaCat) {
				case 'article':
					$article = [
						'@context' => 'https://schema.org',
						'@type'    => 'Article',
					];
					if (! empty($post->post_title)) {
						$article['headline'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post_url = get_the_permalink($post_id))) {
						$article['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($post_url),
						];
					}
					if (! empty($author = get_the_author_meta('display_name', $post->post_author))) {
						$article['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($author),
						];

						if (! empty($url = get_the_author_meta('url', $post->post_author))) {
							$article['author']['url'] = $helper->sanitizeOutPut($url, 'url');
						}
					}
					if (! empty($metaData['publisher'])) {
						if (! empty($metaData['publisherImage'])) {
							$img = $helper->imageInfo(absint($metaData['publisherImage']));
							$plA = [
								'@type'  => 'ImageObject',
								'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
								'height' => $img['height'],
								'width'  => $img['width'],
							];
						} else {
							$plA = [];
						}
						$article['publisher'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
							'logo'  => $plA,
						];
					}
					if (! empty($image_id = get_post_thumbnail_id($post->ID))) {
						$img              = $helper->imageInfo(absint($image_id));
						$article['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($post->post_date)) {
						$article['datePublished'] = $helper->sanitizeOutPut($post->post_date);
					}
					if (! empty($post->post_modified)) {
						$article['dateModified'] = $helper->sanitizeOutPut($post->post_modified);
					}
					if (! empty($post->post_excerpt)) {
						$article['description'] = $helper->sanitizeOutPut(
							$post->post_excerpt,
							'textarea'
						);
					}
					if (! empty($post->post_content)) {
						$article['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($post->post_content, 200), 'textarea');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_article', $article, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_article', $article, $metaData));
					}

					break;

				case 'news_article':
					$newsArticle = [
						'@context' => 'https://schema.org',
						'@type'    => 'NewsArticle',
					];
					if (! empty($post->post_title)) {
						$newsArticle['headline'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post_url = get_the_permalink($post_id))) {
						$newsArticle['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($post_url),
						];
					}
					if (! empty($author = get_the_author_meta('display_name', $post->post_author))) {
						$newsArticle['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($author),
						];

						if (! empty($url = get_the_author_meta('url', $post->post_author))) {
							$newsArticle['author']['url'] = $helper->sanitizeOutPut($url, 'url');
						}
					}
					if (! empty($image_id = get_post_thumbnail_id($post->ID))) {
						$img                  = $helper->imageInfo(absint($image_id));
						$newsArticle['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($post->post_date)) {
						$newsArticle['datePublished'] = $helper->sanitizeOutPut($post->post_date);
					}
					if (! empty($post->post_modified)) {
						$newsArticle['dateModified'] = $helper->sanitizeOutPut($post->post_modified);
					}
					if (! empty($post->post_excerpt)) {
						$newsArticle['description'] = $helper->sanitizeOutPut(
							$post->post_excerpt,
							'textarea'
						);
					}
					if (! empty($post->post_content)) {
						$newsArticle['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($post->post_content, 200), 'textarea');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_news_article', $newsArticle, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_news_article', $newsArticle, $metaData));
					}

					break;

				case 'blog_posting':
					$blogPosting = [
						'@context' => 'https://schema.org',
						'@type'    => 'BlogPosting',
					];
					if (! empty($post->post_title)) {
						$blogPosting['headline'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post_url = get_the_permalink($post_id))) {
						$blogPosting['mainEntityOfPage'] = [
							'@type' => 'WebPage',
							'@id'   => $helper->sanitizeOutPut($post_url),
						];
					}
					if (! empty($author = get_the_author_meta('display_name', $post->post_author))) {
						$blogPosting['author'] = [
							'@type' => 'Person',
							'name'  => $helper->sanitizeOutPut($author),
						];

						if (! empty($url = get_the_author_meta('url', $post->post_author))) {
							$blogPosting['author']['url'] = $helper->sanitizeOutPut($url, 'url');
						}
					}
					if (! empty($image_id = get_post_thumbnail_id($post->ID))) {
						$img                  = $helper->imageInfo(absint($image_id));
						$blogPosting['image'] = [
							'@type'  => 'ImageObject',
							'url'    => $helper->sanitizeOutPut($img['url'], 'url'),
							'height' => $img['height'],
							'width'  => $img['width'],
						];
					}
					if (! empty($post->post_date)) {
						$blogPosting['datePublished'] = $helper->sanitizeOutPut($post->post_date);
					}
					if (! empty($post->post_modified)) {
						$blogPosting['dateModified'] = $helper->sanitizeOutPut($post->post_modified);
					}
					if (! empty($post->post_excerpt)) {
						$blogPosting['description'] = $helper->sanitizeOutPut(
							$post->post_excerpt,
							'textarea'
						);
					}
					if (! empty($post->post_content)) {
						$blogPosting['articleBody'] = $helper->sanitizeOutPut(Functions::filter_content($post->post_content, 200), 'textarea');
					}

					if ($without_script) {
						$html = apply_filters('rtseo_snippet_blog_posting', $blogPosting, $metaData);
					} else {
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_blog_posting', $blogPosting, $metaData));
					}

					break;

				case 'course':

					if (! function_exists('rtrsp')) {
						break;
					}
					if ($post->post_type != 'lp_course' || ! in_array('learnpress/learnpress.php', apply_filters('active_plugins', get_option('active_plugins')))) {
						break;
					}

					$post_meta  = get_post_meta($post->ID);
					$price      = isset($post_meta['_lp_price']) ? $post_meta['_lp_price'][0] : '';
					$sale_price = isset($post_meta['_lp_sale_price']) ? $post_meta['_lp_sale_price'][0] : '';

					if ($sale_price) {
						$price = $sale_price;
					}

					if ($price) {
						$metaData['price']         = $price;
						$metaData['priceCurrency'] = rtrs()->get_options('learn_press_currency');
						$metaData['availability']  = 'http://schema.org/InStock';
						$metaData['url']           = get_the_permalink($post->ID);
					}
					$metaData['courseMode'] = 'online';

					$course                      = [];
					$course['@context']          = 'http://schema.org';
					$course['@type']             = 'Course';
					$course['hasCourseInstance'] = [
						'@type' => 'CourseInstance',
					];

					if (! empty($post->post_title)) {
						$course['name'] = $course['hasCourseInstance']['name'] = $helper->sanitizeOutPut($post->post_title);
					}
					if (! empty($post->post_content)) {
						$course['description'] = $course['hasCourseInstance']['description'] = $helper->sanitizeOutPut($post->post_content);
					}
					if (! empty($metaData['publisher'])) {
						$course['provider'] = [
							'@type' => 'Organization',
							'name'  => $helper->sanitizeOutPut($metaData['publisher']),
						];
					}

					if (! empty($metaData['courseMode'])) {
						$course['hasCourseInstance']['courseMode'] = explode(
							"\r\n",
							$helper->sanitizeOutPut($metaData['courseMode'], 'textarea')
						);
					}
					if (! empty($metaData['endDate'])) {
						$course['hasCourseInstance']['endDate'] = $helper->sanitizeOutPut($metaData['endDate']);
					}
					if (! empty($metaData['startDate'])) {
						$course['hasCourseInstance']['startDate'] = $helper->sanitizeOutPut($metaData['startDate']);
					}
					if (! empty($metaData['locationName']) && ! empty($metaData['locationAddress'])) {
						$course['hasCourseInstance']['location'] = [
							'@type'   => 'Place',
							'name'    => $helper->sanitizeOutPut($metaData['locationName']),
							'address' => $helper->sanitizeOutPut($metaData['locationAddress']),
						];
					}
					if ($img_url = get_the_post_thumbnail_url($post->ID)) {
						$course['hasCourseInstance']['image'] = $helper->sanitizeOutPut(
							$img_url,
							'url'
						);
					}
					if (! empty($metaData['price']) && ! empty($metaData['priceCurrency'])) {
						$course['hasCourseInstance']['offers'] = [
							'@type'         => 'Offer',
							'price'         => $helper->sanitizeOutPut($metaData['price']),
							'priceCurrency' => $helper->sanitizeOutPut($metaData['priceCurrency']),
						];
						if (! empty($metaData['availability'])) {
							$course['hasCourseInstance']['offers']['availability'] = $helper->sanitizeOutPut($metaData['availability']);
						}
						if (! empty($metaData['url'])) {
							$course['hasCourseInstance']['offers']['url'] = $helper->sanitizeOutPut($metaData['url']);
						}
						if (! empty($metaData['validFrom'])) {
							$course['hasCourseInstance']['offers']['validFrom'] = $helper->sanitizeOutPut($metaData['validFrom']);
						}
					}
					if (! empty($metaData['performerType']) && ! empty($metaData['performerName'])) {
						$course['hasCourseInstance']['performer'] = [
							'@type' => $helper->sanitizeOutPut($metaData['performerType']),
							'name'  => $helper->sanitizeOutPut($metaData['performerName']),
						];
					}
					$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_course', $course, $metaData));
					if (isset($metaData['review_active']) && $metaData['review_active'] == 'show') {
						$course_review = [
							'@context'     => 'http://schema.org',
							'@type'        => 'Review',
							'itemReviewed' => [
								'@type' => 'Course',
							],
						];

						if (isset($metaData['review_datePublished']) && ! empty($metaData['review_datePublished'])) {
							$course_review['datePublished'] = $helper->sanitizeOutPut($metaData['review_datePublished']);
						}
						if (isset($metaData['review_body']) && ! empty($metaData['review_body'])) {
							$course_review['reviewBody'] = $helper->sanitizeOutPut($metaData['review_body'], 'textarea');
						}
						if (isset($course['name'])) {
							$course_review['itemReviewed']['name'] = $course['name'];
						}

						if (isset($course['description'])) {
							$course_review['itemReviewed']['description'] = Functions::filter_content($course['description'], 200);
						}
						if (isset($course['provider'])) {
							$course_review['itemReviewed']['provider'] = $course['provider'];
						}
						if (! empty($metaData['review_author'])) {
							$course_review['author'] = [
								'@type' => 'Person',
								'name'  => $helper->sanitizeOutPut($metaData['review_author']),
							];

							if (isset($metaData['review_author_sameAs']) && ! empty($metaData['review_author_sameAs'])) {
								$sameAs = Functions::get_same_as($helper->sanitizeOutPut($metaData['review_author_sameAs'], 'textarea'));
								if (! empty($sameAs)) {
									$course_review['author']['sameAs'] = $sameAs;
								}
							}
						}
						if (isset($metaData['review_ratingValue'])) {
							$course_review['reviewRating'] = [
								'@type'       => 'Rating',
								'ratingValue' => $helper->sanitizeOutPut($metaData['review_ratingValue'], 'number'),
							];
							if (isset($metaData['review_bestRating'])) {
								$course_review['reviewRating']['bestRating'] = $helper->sanitizeOutPut($metaData['review_bestRating'], 'number');
							}
							if (isset($metaData['review_worstRating'])) {
								$course_review['reviewRating']['worstRating'] = $helper->sanitizeOutPut($metaData['review_worstRating'], 'number');
							}
						}
						$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_course_review', $course_review, $metaData));
					}
					break;

				case 'product':

					if (! function_exists('rtrsp')) {
						break;
					}
					$markup = [];

					//if edd
					// var_dump($post);
					if ($post->post_type == 'download' && function_exists('EDD')) {
						$permalink = get_the_permalink($post->ID);
						$markup    = [
							'@context'    => 'https://schema.org',
							'@type'       => 'Product',
							'@id'         => $permalink . '#comments', // Append '#comments' to differentiate between this @id and the @id generated for the Breadcrumblist.
							'name'        => wp_kses_post($post->post_title),
							'url'         => $permalink,
							'description' => wp_strip_all_tags(do_shortcode($post->post_excerpt ? $post->post_excerpt : $post->post_content)),
						];

						$image = get_the_post_thumbnail_url($post->ID);
						if ($image) {
							$markup['image'] = $image;
						}

						$markup['sku'] = $post->ID;
						$brand_name    = rtrs()->get_options('rtrs_woocommerce_settings', 'brand_name');
						if ($brand_name) {
							$markup['brand'] = [
								'@type' => 'Brand',
								'name'  => esc_html($brand_name),
							];
						}

						$identifier_type = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier_type');
						$identifier      = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier');
						if ($identifier_type && $identifier) {
							$markup[$identifier_type] = esc_html($identifier);
						}

						$price             = edd_get_download_price($post->ID, false);
						$price_valid_until = gmdate('Y-12-31', time() + YEAR_IN_SECONDS);
						$markup['offers']  = [
							'@type'             => 'Offer',
							'url'               => $permalink,
							'price'             => $price,
							'priceValidUntil'   => $price_valid_until,
							'priceCurrency'     => 'USD',
							'itemCondition'     => 'https://schema.org/NewCondition',
							'availability'      => 'https://schema.org/InStock',
						];

						$download_rating = true;
						if ($download_rating) {
							$total_rating = Review::getTotalRatings($post->ID);
							if ($total_rating) {
								$markup['aggregateRating'] = [
									'@type'       => 'AggregateRating',
									'ratingValue' => Review::getAvgRatings($post->ID),
									'reviewCount' => $total_rating,
								];
							}

							// Markup 5 most recent rating/review.
							$comments = get_comments(
								[
									'number'      => 5,
									'post_id'     => $post->ID,
									'status'      => 'approve',
									'post_status' => 'publish',
									'post_type'   => 'download',
									'parent'      => 0,
									'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
										[
											'key'     => 'rating',
											'type'    => 'NUMERIC',
											'compare' => '>',
											'value'   => 0,
										],
									],
								]
							);

							if ($comments) {
								$markup['review'] = [];
								foreach ($comments as $comment) {
									$markup['review'][] = [
										'@type'         => 'Review',
										'reviewRating'  => [
											'@type'       => 'Rating',
											'bestRating'  => '5',
											'ratingValue' => get_comment_meta($comment->comment_ID, 'rating', true),
											'worstRating' => '1',
										],
										'author'        => [
											'@type' => 'Person',
											'name'  => get_comment_author($comment),
										],
										'reviewBody'    => get_comment_text($comment),
										'datePublished' => get_comment_date('c', $comment),
									];
								}
							}
						}
					} elseif ($post->post_type == 'rtcl_listing' && in_array('classified-listing/classified-listing.php', apply_filters('active_plugins', get_option('active_plugins')))) {
						$permalink = get_the_permalink($post->ID);
						$markup    = [
							'@context'    => 'https://schema.org',
							'@type'       => 'Product',
							'@id'         => $permalink . '#comments', // Append '#comments' to differentiate between this @id and the @id generated for the Breadcrumblist.
							'name'        => wp_kses_post($post->post_title),
							'url'         => $permalink,
							'description' => wp_strip_all_tags(do_shortcode($post->post_excerpt ? $post->post_excerpt : $post->post_content)),
						];

						global $listing;

						$image = $listing->get_images();
						if ($image) {
							$markup['image'] = $image[0]->url;
						}

						$markup['sku'] = $post->ID;
						$brand_name    = rtrs()->get_options('rtrs_woocommerce_settings', 'brand_name');
						if ($brand_name) {
							$markup['brand'] = [
								'@type' => 'Brand',
								'name'  => esc_html($brand_name),
							];
						}

						$identifier_type = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier_type');
						$identifier      = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier');
						if ($identifier_type && $identifier) {
							$markup[$identifier_type] = esc_html($identifier);
						}

						$price = $listing->get_price();

						$price_valid_until = gmdate('Y-12-31', time() + YEAR_IN_SECONDS);
						$markup['offers']  = [
							'@type'             => 'Offer',
							'url'               => $permalink,
							'price'             => $price,
							'priceValidUntil'   => $price_valid_until,
							'priceCurrency'     => 'USD',
							'itemCondition'     => 'https://schema.org/NewCondition',
							'availability'      => 'https://schema.org/InStock',
						];

						$download_rating = true;
						if ($download_rating) {
							$total_rating = Review::getTotalRatings($post->ID);
							if ($total_rating) {
								$markup['aggregateRating'] = [
									'@type'       => 'AggregateRating',
									'ratingValue' => Review::getAvgRatings($post->ID),
									'reviewCount' => $total_rating,
								];
							}

							// Markup 5 most recent rating/review.
							$comments = get_comments(
								[
									'number'      => 5,
									'post_id'     => $post->ID,
									'status'      => 'approve',
									'post_status' => 'publish',
									'post_type'   => 'rtcl_listing',
									'parent'      => 0,
									'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
										[
											'key'     => 'rating',
											'type'    => 'NUMERIC',
											'compare' => '>',
											'value'   => 0,
										],
									],
								]
							);

							if ($comments) {
								$markup['review'] = [];
								foreach ($comments as $comment) {
									$markup['review'][] = [
										'@type'         => 'Review',
										'reviewRating'  => [
											'@type'       => 'Rating',
											'bestRating'  => '5',
											'ratingValue' => get_comment_meta($comment->comment_ID, 'rating', true),
											'worstRating' => '1',
										],
										'author'        => [
											'@type' => 'Person',
											'name'  => get_comment_author($comment),
										],
										'reviewBody'    => get_comment_text($comment),
										'datePublished' => get_comment_date('c', $comment),
									];
								}
							}
						}
					} else {
						//woocommerce
						if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
							if ($without_script) {
								$product = new \WC_Product($post_id);
							} else {
								global $product;
							}

							if (is_a($product, 'WC_Product')) {
								$shop_name = get_bloginfo('name');
								$shop_url  = home_url();
								$currency  = get_woocommerce_currency();
								$permalink = get_permalink($product->get_id());

								$markup = [
									'@context'    => 'https://schema.org',
									'@type'       => 'Product',
									'@id'         => $permalink . '#product', // Append '#product' to differentiate between this @id and the @id generated for the Breadcrumblist.
									'name'        => wp_kses_post($product->get_name()),
									'url'         => $permalink,
									'description' => wp_strip_all_tags(do_shortcode($product->get_short_description() ? $product->get_short_description() : $product->get_description())),
								];

								$image = wp_get_attachment_url($product->get_image_id());
								if ($image) {
									$markup['image'] = $image;
								}

								// Declare SKU or fallback to ID.
								if ($product->get_sku()) {
									$markup['sku'] = $product->get_sku();
								} else {
									$markup['sku'] = $product->get_id();
								}

								$brand_name = rtrs()->get_options('rtrs_woocommerce_settings', 'brand_name');
								if ($brand_name) {
									$markup['brand'] = [
										'@type' => 'Brand',
										'name'  => esc_html($brand_name),
									];
								}

								$identifier_type = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier_type');
								$identifier      = rtrs()->get_options('rtrs_woocommerce_settings', 'identifier');
								if ($identifier_type && $identifier) {
									$markup[$identifier_type] = esc_html($identifier);
								}

								if ('' !== $product->get_price()) {
									// Assume prices will be valid until the end of next year, unless on sale and there is an end date.
									$price_valid_until = gmdate('Y-12-31', time() + YEAR_IN_SECONDS);

									if ($product->is_type('variable')) {
										$lowest  = $product->get_variation_price('min', false);
										$highest = $product->get_variation_price('max', false);

										if ($lowest === $highest) {
											$markup_offer = [
												'@type'              => 'Offer',
												'price'              => wc_format_decimal($lowest, wc_get_price_decimals()),
												'priceValidUntil'    => $price_valid_until,
												'priceSpecification' => [
													'price'                 => wc_format_decimal($lowest, wc_get_price_decimals()),
													'priceCurrency'         => $currency,
													'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
												],
											];
										} else {
											$markup_offer = [
												'@type'      => 'AggregateOffer',
												'lowPrice'   => wc_format_decimal($lowest, wc_get_price_decimals()),
												'highPrice'  => wc_format_decimal($highest, wc_get_price_decimals()),
												'offerCount' => count($product->get_children()),
											];
										}
									} else {
										if ($product->is_on_sale() && $product->get_date_on_sale_to()) {
											$price_valid_until = gmdate('Y-m-d', $product->get_date_on_sale_to()->getTimestamp());
										}
										$markup_offer = [
											'@type'              => 'Offer',
											'price'              => wc_format_decimal($product->get_price(), wc_get_price_decimals()),
											'priceValidUntil'    => $price_valid_until,
											'priceSpecification' => [
												'price'                 => wc_format_decimal($product->get_price(), wc_get_price_decimals()),
												'priceCurrency'         => $currency,
												'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
											],
										];
									}

									$markup_offer += [
										'priceCurrency' => $currency,
										'availability'  => 'https://schema.org/' . ($product->is_in_stock() ? 'InStock' : 'OutOfStock'),
										'url'           => $permalink,
										'seller'        => [
											'@type' => 'Organization',
											'name'  => $shop_name,
											'url'   => $shop_url,
										],
									];

									$markup['offers'] = [apply_filters('woocommerce_structured_data_product_offer', $markup_offer, $product)];
								}

								if ($product->get_rating_count() && wc_review_ratings_enabled()) {
									$markup['aggregateRating'] = [
										'@type'       => 'AggregateRating',
										'ratingValue' => $product->get_average_rating(),
										'reviewCount' => $product->get_review_count(),
									];

									// Markup 5 most recent rating/review.
									$comments = get_comments(
										[
											'number'      => 5,
											'post_id'     => $product->get_id(),
											'status'      => 'approve',
											'post_status' => 'publish',
											'post_type'   => 'product',
											'parent'      => 0,
											'meta_query'  => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
												[
													'key'     => 'rating',
													'type'    => 'NUMERIC',
													'compare' => '>',
													'value'   => 0,
												],
											],
										]
									);

									if ($comments) {
										$markup['review'] = [];
										foreach ($comments as $comment) {
											$markup['review'][] = [
												'@type'         => 'Review',
												'reviewRating'  => [
													'@type'       => 'Rating',
													'bestRating'  => '5',
													'ratingValue' => get_comment_meta($comment->comment_ID, 'rating', true),
													'worstRating' => '1',
												],
												'author'        => [
													'@type' => 'Person',
													'name'  => get_comment_author($comment),
												],
												'reviewBody'    => get_comment_text($comment),
												'datePublished' => get_comment_date('c', $comment),
											];
										}
									}
								}
							}
						}
					}

					if ($markup) {
						if ($without_script) {
							$html = apply_filters('rtseo_snippet_product', $markup, $metaData);
						} else {
							$html .= $this->getJsonEncode(apply_filters('rtseo_snippet_product', $markup, $metaData));
						}
					}
					break;

				default:
					break;
			}
		}

		return $html;
	}
}
