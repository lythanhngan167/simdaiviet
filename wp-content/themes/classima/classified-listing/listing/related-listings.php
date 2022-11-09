<?php
/**
 *
 * @author        RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 */

use Rtcl\Models\Listing;
use radiustheme\Classima\RDTheme;
use radiustheme\Classima\Helper;

$count = $rtcl_related_query->post_count;

if ( !$count ) {
    return;
}

$rand = substr(md5(mt_rand()), 0, 7);

$owl_data = [
	"navigation"        => [
		"nextEl"            => ".rtin-custom-nav-$rand .owl-next",
		"prevEl"            => ".rtin-custom-nav-$rand .owl-prev",
	],
	"loop"              => false,
    "autoplay"          => [
	    "delay" => 3000,
	    "disableOnInteraction"  => false,
	    "pauseOnMouseEnter"     => true
    ],
	"speed"             => 1000,
	"spaceBetween"      => 20,
	"breakpoints"       => [
		0   => [
			"slidesPerView" => 1
		],
		500   => [
			"slidesPerView" => 2
		],
		1200 => [
			"slidesPerView" => 3
		]
	]
];

$owl_data = apply_filters('classima_related_listing_options', $owl_data);

$owl_data = json_encode( $owl_data );

$layout = RDTheme::$options['listing_related_style'];

$display = array(
    'user' => false,
);
?>
<?php if ( $rtcl_related_query->have_posts() ) : ?>
    <div class="content-block-gap"></div>
    <div class="site-content-block classima-single-related owl-wrap">
        <div class="main-title-block">
            <h3 class="main-title"><?php esc_html_e( 'Sim liên quan', 'classima' ); ?></h3>
            <div class="owl-related-nav owl-custom-nav rtin-custom-nav-<?php echo esc_attr( $rand );?>">
                <div class="owl-prev"><i class="fa fa-angle-left"></i></div><div class="owl-next"><i class="fa fa-angle-right"></i></div>
            </div>
        </div>
        <div class="main-content">
            <div class="rtcl-carousel-slider" data-options="<?php echo esc_attr( $owl_data ); ?>">
                <div class="swiper-wrapper">
                    <?php while ( $rtcl_related_query->have_posts() ) : $rtcl_related_query->the_post(); ?>
                        <?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) ); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;