<?php
/**
 * @author  RadiusTheme
 * @since   5.0
 * @version 5.0.8
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Traits\Addons\ListingItem;
use \RtclPro\Traits\ELTempleateBuilderTraits as ELTempleateBuilderTraits;
use \Elementor\Utils;

do_action( 'rtcl_builder_before_header' );

?>
	<div class="rtcl el-single-addon header-inner-wrapper header-<?php echo ! empty( $instance['rtcl_header_style'] ) ? esc_attr( $instance['rtcl_header_style'] ) : ''; ?>">
		<?php if ( ! empty( $instance['rtcl_show_page_title'] ) ) { ?>
		<header class="rtcl-listing-header">
			<?php
			$title_text = '';
			if ( ELTempleateBuilderTraits::is_builder_page_archive() ) {
				$title_text = Functions::page_title( false );
			} elseif ( ELTempleateBuilderTraits::is_builder_page_single() ) {
				$_id        = ListingItem::get_prepared_listing_id();
				$listing    = new Listing( $_id );
				$title_text = $listing->get_the_title();
			}
			printf( '<%1$s class="rtcl-listings-header-title page-title %3$s" >%2$s</%1$s>', esc_html( Utils::validate_html_tag( $instance['header_size'] ) ), esc_html( $title_text ), 'text-' . esc_attr( $instance['title_alignment'] ) );
			?>
			<?php
			if ( ELTempleateBuilderTraits::is_builder_page_archive() ) {
				/**
				 * Hook: rtcl_archive_description.
				 *
				 * @hooked TemplateHooks::taxonomy_archive_description - 10
				 * @hooked TemplateHooks::listing_archive_description - 10
				 */
				do_action( 'rtcl_archive_description' );
			}
			?>
		</header>
			<?php
		}
		?>
		<div class="breadcrumb-section text-<?php echo esc_attr( $instance['breadcrumb_alignment'] ); ?>">
			<?php
			if ( ! empty( $instance['rtcl_show_breadcrumb'] ) ) {
				Functions::breadcrumb();
			}
			?>
		</div>
	</div>
<?php
do_action( 'rtcl_builder_after_header' );
