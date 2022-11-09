<?php

/**
 * Main Blocks ListingAdsSlider Class.
 *
 * The main class that initiates and runs the plugin.
 *
 * @package  Classifid-listing
 *
 * @since 1.0.0
 */

namespace RtclPro\Controllers\Blocks;

use Rtcl\Controllers\Blocks\ListingsAjaxController;
use Rtcl\Helpers\Functions;

class ListingAdsSlider
{
	protected $name = 'rtcl/listing-ads-slider';

	protected $attributes = [];

	public function get_attributes($default = false)
	{
		$attributes = array(
			'blockId'      => array(
				'type'    => 'string',
				'default' => '',
			),
			"cats" => array(
				"type" => "array",
			),
			"locations" => array(
				"type" => "array",
			),
			"listing_type" => array(
				"type" => "string",
				"default" => "all",
			),

			"image_size" => array(
				"type" => "string",
				"default" => "rtcl-thumbnail",
			),
			"custom_image_width" => array(
				"type" => "number",
				"default" => 400,
			),
			"custom_image_height" => array(
				"type" => "number",
				"default" => 280,
			),

			"promotion_in" => array(
				"type" => "array",
			),
			"promotion_not_in" => array(
				"type" => "array",
			),
			"orderby" => array(
				"type" => "string",
				"default" => "date",
			),
			"sortby" => array(
				"type" => "string",
				"default" => "desc",
			),
			"perPage" => array(
				"type" => "number",
				"default" => 8,
			),
			"offset" => array(
				"type" => "number",
				"default" => 0,
			),
			"align" => array(
				"type" => "string",
			),

			"col_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{padding:{{col_padding}};}']
				]
			),
			"content_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content
					{padding:{{content_padding}};}'],
				]
			),
			"col_style" => array(
				"type" => "object",
				"default" => array(
					"style" => "1",
				),
			),
			'colBGColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ background-color:{{colBGColor}} !important; }']
				]
			],
			'colBorderColor'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ border-color:{{colBorderColor}} !important; }']
				]
			],
			'colBorderWith'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{border-width:{{colBorderWith}} !important; }']
				]
			],
			'colBorderStyle'      => [
				'type'    => 'string',
				'default' => 'solid',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item
					{ border-style:{{colBorderStyle}} !important; }']
				]
			],
			'colBorderRadius'      => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item 
					{ border-radius:{{colBorderRadius}}; }']
				]
			],
			'colBoxShadowStyle'      => [
				'type'    => 'string',
				'default' => 'normal',
			],
			'colBoxShadow' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => '', 'inset' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item']
				],
			],
			'colBoxShadowHover' => [
				'type' => 'object',
				'default' => (object)['openShadow' => 1, 'width' => (object)['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1], 'color' => ''],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover']
				],
			],
			'pfeaturedBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured{ border-color:{{pfeaturedBDColor}} !important; }']
				]
			],
			'pfeaturedBGColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured{ background-color:{{pfeaturedBGColor}} !important; }']
				]
			],
			'ptopBDColor' => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-top{ border-color:{{ptopBDColor}} !important; }']
				]
			],
			'ptopBGColor'  => [
				'type'    => 'string',
				'default' => '',
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-top{ background-color:{{ptopBGColor}} !important; }']
				]
			],
			"titleColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"titleColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title a{color:{{titleColor}} !important;}']
				]
			),
			"titleHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title a:hover {color:{{titleHoverColor}} !important;}']
				],
			),
			'titleTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '18', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '700'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title']
				],
			],
			"title_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-title {margin:{{title_margin}};}']
				]
			),
			"bnewBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{background-color:{{bnewBGColor}};}']
				]
			),
			"bnewColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-new{color:{{bnewColor}};}']
				]
			),
			"bfeaturedBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{background-color:{{bfeaturedBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured .listing-thumb:after { background-color:{{bfeaturedBGColor}}; }']
				]
			),
			"bfeaturedColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-featured{color:{{bfeaturedColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item.is-featured .listing-thumb:after { color:{{bfeaturedColor}}; }']
				]
			),
			"btopBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{background-color:{{btopBGColor}};}']
				]
			),
			"btopColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-_top{color:{{btopColor}};}']
				]
			),
			"bbumpBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-grid-view .badge.rtcl-badge-_bump_up{background-color:{{bbumpBGColor}};}']
				]
			),
			"bbumpColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl .rtcl-gb-grid-view .badge.rtcl-badge-_bump_up{color:{{bbumpColor}};}']
				]
			),
			"bpopularBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{background-color:{{bpopularBGColor}};}']
				]
			),
			"bpopularColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge.rtcl-badge-popular.popular-badge.badge-success{color:{{bpopularColor}};}']
				]
			),
			"badge_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge {padding:{{badge_padding}} !important;}']
				]
			),
			"badge_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge {margin:{{badge_margin}};}']
				]
			),
			'badgeTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '13', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '13', 'unit' => 'px'], 'transform' => 'capitalize', 'weight' => '600'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-badge-wrap .badge']
				],
			],
			"soldBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out{background-color:{{soldBGColor}};}']
				]
			),
			"soldColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out{color:{{soldColor}};}']
				]
			),
			'soldTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '14', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '14', 'unit' => 'px'], 'transform' => 'uppercase', 'weight' => '600'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-sold-out']
				],
			],
			"metaColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),
			"metaColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li{color:{{metaColor}};}'],
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .right-content .rtcl-listing-meta-data{color:{{metaColor}};}'],
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-list-style-4 .right-content .rtcl-listing-type{color:{{metaColor}};}'],
				]
			),
			"metaHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover .rtcl-listing-meta-data li{color:{{metaHoverColor}};}']]
			),
			"metaIconColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li .rtcl-icon
					{color:{{metaIconColor}};}']
				]
			),
			"metaIconHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item:hover .rtcl-listing-meta-data li .rtcl-icon{color:{{metaIconHoverColor}};}']]
			),
			"metaCatColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-cat a{color:{{metaCatColor}};}']]
			),
			"metaCatHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-cat a:hover{color:{{metaCatHoverColor}};}']]
			),
			'metaTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '15', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data li'],
				],
			],
			"meta_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-listing-meta-data
				{margin:{{meta_margin}};}']]
			),

			"priceColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount
					{color:{{priceColor}};}']

				]
			),
			"priceBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .item-price
					{background-color:{{priceBGColor}};}']

				]
			),
			"priceFontSize" => array(
				"type" => "string",
				"default" => 22,
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount
				{font-size:{{priceFontSize}}px;}']]
			),
			"priceFontWeight" => array(
				"type" => "string",
				"default" => 600,
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-price-amount,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .listing-thumb .rtcl-price-amount
				{font-weight:{{priceFontWeight}};}']]
			),
			"unitLabelColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .rtcl-price-meta
					{color:{{unitLabelColor}};}']

				]
			),
			"unitLFSize" => array(
				"type" => "string",
				"default" => 15,
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .rtcl-price-meta
				{font-size:{{unitLFSize}}px;}']]
			),
			"unitLFSizeWeight" => array(
				"type" => "string",
				"default" => 500,
				'style' => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .listing-price .rtcl-price-meta,
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .rtcl-price-meta
				{font-weight:{{unitLFSizeWeight}};}']]
			),
			"price_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .item-price
				{margin:{{price_margin}};}']]
			),
			"price_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
			),
			"btnColorStyle" => array(
				"type" => "string",
				"default" => "normal",
			),

			"btnBGColor" => array(
				"type" => "string",
				"default" => "",
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a
					{background-color:{{btnBGColor}};}']
				]
			),
			"btnBGHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover
					{background-color:{{btnBGHoverColor}};}'],
				]
			),
			"btnColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a .rtcl-icon,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a
					{color:{{btnColor}};}'],
				]
			),
			"btnHoverColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [
					(object)['selector' => '
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .rtcl-gb-meta-buttons-wrap .rtcl-gb-button a:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button a:hover .rtcl-icon,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-3 .rtcl-bottom.button-count-4 .rtcl-gb-button .rtcl-gb-phone-reveal:hover,
					{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover
					{color:{{btnHoverColor}};}'],
				]
			),
			"btnBorderColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a{border-color:{{btnBorderColor}};}']]
			),
			"btnHoverBorderColor" => array(
				"type" => "string",
				"default" => '',
				'style' => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view.rtcl-gb-grid-style-5 .listing-item .rtcl-bottom ul .action-btn a:hover{border-color:{{btnHoverBorderColor}};}']]
			),

			'contentTypo' => [
				'type' => 'object',
				'default' => (object)['openTypography' => 1, 'size' => (object)['lg' => '16', 'unit' => 'px'], 'spacing' => (object)['lg' => '0', 'unit' => 'px'], 'height' => (object)['lg' => '26', 'unit' => 'px'], 'transform' => 'none', 'weight' => '400'],
				'style' => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-excerpt']
				],
			],
			"content_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-grid-view .listing-item .item-content .rtcl-excerpt 
				{margin:{{content_margin}};}']]
			),
			"container_padding" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {padding:{{container_padding}};}']]
			),
			"container_margin" => array(
				"type" => "object",
				"default" => array(
					"unit" => "px",
				),
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {margin:{{container_margin}};}']]
			),
			"containerBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}}.rtcl-block-editor,
				{{RTCL}}.rtcl-block-frontend {background-color:{{containerBGColor}};}']]
			),
			"content_visibility" => array(
				"type" => "object",
				"default" => array(
					"badge" => true,
					"location" => true,
					"category" => true,
					"date" => true,
					"price" => true,
					"author" => true,
					"view" => true,
					"content" => true,
					"grid_content" => false,
					"title" => true,
					"thumbnail" => true,
					"listing_type" => true,
					"thumb_position" => "",
					"details_btn" => true,
					"favourit_btn" => true,
					"phone_btn" => true,
					"compare_btn" => true,
					"quick_btn" => true,
					"sold" => true,
					"actionLayout" => "horizontal-layout",
				),
			),
			"content_limit" => array(
				"type" => "number",
				"default" => 20,
			),
			"col_xl" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_lg" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_md" => array(
				"type" => "string",
				"default" => "4",
			),
			"col_sm" => array(
				"type" => "string",
				"default" => "6",
			),
			"col_mobile" => array(
				"type" => "string",
				"default" => "12",
			),
			"slider_options" => array(
				"type" => "object",
				"default" => array(
					"autoHeight" => false,
					"loop" => true,
					"autoPlay" => true,
					"stopOnHover" => true,
					"autoPlayDelay" => 2000,
					"autoPlaySlideSpeed" => 2000,
					"spaceBetween" => 30,
					"arrowNavigation" => true,
					"dotNavigation" => false,
					"dotStyle" => "1",
					"arrowPosition" => "1",
					"sliderLoader" => true
				),
			),
			"arrowBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn
				{background-color:{{arrowBGColor}};}']]
			),
			"arrowBGHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn:focus, 
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn:hover
				{background-color:{{arrowBGHoverColor}};}']]
			),
			"arrowIconColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn
				{color:{{arrowIconColor}};}']]
			),
			"arrowIconHoverColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn:focus, 
				{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-btn:hover
				{color:{{arrowIconHoverColor}};}']]
			),
			"dotBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet
				{background-color:{{dotBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet
				{border-color:{{dotBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet:after
				{background-color:{{dotBGColor}};}']
				]
			),
			"dotActiveBGColor" => array(
				"type" => "string",
				"default" => "",
				'style'   => [
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active
				{background-color:{{dotActiveBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active
				{border-color:{{dotActiveBGColor}};}'],
					(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active:after
				{background-color:{{dotActiveBGColor}};}']
				]
			),
			"dotSpacing" => array(
				"type" => "number",
				"default" => -30,
				'style'   => [(object)['selector' => '{{RTCL}} .rtcl.rtcl-gb-block .rtcl-gb-slider-pagination.swiper-pagination-bullets
				{bottom:{{dotSpacing}}px;}']]
			),

		);

		if ($default) {
			$temp = [];
			foreach ($attributes as $key => $value) {
				if (isset($value['default'])) {
					$temp[$key] = $value['default'];
				}
			}
			return $temp;
		} else {
			return $attributes;
		}
	}
	public function __construct()
	{
		add_action('init', [$this, 'register_listings']);
	}

	public function register_listings()
	{
		if (!function_exists('register_block_type')) {
			return;
		}
		register_block_type(
			'rtcl/listing-ads-slider',
			[
				'render_callback' => [$this, 'render_callback_listings'],
				'attributes' => $this->get_attributes(),
			]
		);
	}

	public function render_callback_listings($attributes)
	{
		$settings  = $attributes;
		$the_loops = ListingsAjaxController::rtcl_gb_listings_query($settings);
		$view = 'grid';
		$style = '1';
		if ('grid' === $view) {
			$style = isset($settings['col_style']['style']) ? $settings['col_style']['style'] : '1';
		}
		$data = array(
			'template'              => 'block/listing-ads-slider/' . $view . '/style-' . $style,
			'view'                  => $view,
			'style'                 => $style,
			'instance'              => $settings,
			'the_loops'             => $the_loops,
			'default_template_path' => rtclPro()->get_plugin_template_path(),
		);
		$data  = apply_filters('rtcl_gb_listing_slider_filter_data', $data);
		ob_start();
		Functions::get_template($data['template'], $data, '', $data['default_template_path']);
		return ob_get_clean();
	}
}
