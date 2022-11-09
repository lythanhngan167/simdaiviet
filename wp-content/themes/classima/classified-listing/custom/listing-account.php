<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;
use Rtcl\Helpers\Functions;

// Layout class
RDTheme::$layout = ( RDTheme::$layout != 'right-sidebar' ) ? 'left-sidebar' : 'right-sidebar';
$layout_class    = 'col-lg-9 col-md-8 col-sm-12 col-12';
if ( ! is_user_logged_in()){
    $layout_class    = 'col-lg-12 col-md-12 col-sm-12 col-12';
}
?>
<?php get_header(); ?>
<div id="primary" class="content-area classima-myaccount">
	<div class="container">
		<div class="row">
			<?php
			if ( RDTheme::$layout == 'left-sidebar' ) {
			    if (is_user_logged_in()){
                    Helper::get_custom_listing_template( 'sidebar-account' );
                }
			}
			?>
			<div class="<?php echo esc_attr( $layout_class );?>">
				<?php if ( Functions::is_account_page( 'listings' ) || Functions::is_account_page( 'favourites' ) ): ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php Helper::get_custom_listing_template( 'listing-account-content' ); ?>
					<?php endwhile; ?>
				<?php else: ?>
					<main id="main" class="site-content-block">
						<div class="main-content">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php Helper::get_custom_listing_template( 'listing-account-content' ); ?>
							<?php endwhile; ?>
						</div>
					</main>
				<?php endif; ?>

				<?php Helper::get_template_part( 'template-parts/pagination', array( 'max_num_pages' => RDTheme::$listing_max_page_num ) );?>
			</div>
			<?php
			if ( RDTheme::$layout == 'right-sidebar' ) {
                if (is_user_logged_in()) {
                    Helper::get_custom_listing_template('sidebar-account');
                }
			}
			?>
		</div>
	</div>
</div>
<?php get_footer(); ?>