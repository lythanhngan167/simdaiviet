<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
?>
<?php if ( RDTheme::$has_banner ): ?>
	<div class="theme-banner">
		<div class="container">
			<div class="banner-content">
				<h1 class="entry-title"><?php Helper::the_title();?></h1>
				<?php if ( RDTheme::$has_breadcrumb ): ?>
					<div class="main-breadcrumb"><?php Helper::the_breadcrumb(); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if ( RDTheme::$has_banner_search && class_exists( 'RtclPro' ) ): ?>
		<div class="banner-search">
			<div class="container">
				<div class="rtcl classima-listing-search">
					<?php Helper::get_custom_listing_template( 'listing-search' ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ( RDTheme::$has_banner != 'on' && RDTheme::$has_breadcrumb && is_single() ): ?>
    <div class="theme-banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="main-breadcrumb"><?php Helper::the_breadcrumb();?></div>
            </div>
        </div>
    </div>
<?php endif; ?>
