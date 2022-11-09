<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Models\Listing;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;
use Rtcl\Helpers\Functions;

global $post;

$listing = new Listing( $post->ID );
$category = $listing->get_categories();
$category = end( $category );
$thumb_size = 'rtcl-thumbnail';

if ( get_post_meta( $post->ID, 'never_expires', true ) ) {
	$expiry_date = esc_html__( 'Never Expires', 'classima' );
}
else {
	$expiry_date = get_post_meta( $post->ID, 'expiry_date', true );
	$expiry_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $expiry_date ) );
}
?>
<div class="classified-list-box listing-item">
	<div class="rtin-item d-flex">
		<div class="rtin-thumb">
			<a class="rtin-thumb-inner" href="<?php the_permalink(); ?>">
				<?php $listing->the_thumbnail(); ?>
			</a>
		</div>
		<div class="rtin-content flex-grow-1">
			<h3 class="rtin-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php if ( $listing->is_new() || $listing->is_popular() || $listing->is_top() || $listing->is_featured() ): ?>
				<div class='rtcl-listing-badge-wrap'>
					<?php if ( $listing->is_new() ) : ?>
						<span class="badge new-badge badge-primary"><?php echo esc_html( $listing->get_new_label_text() ); ?></span>
					<?php endif; ?>
					<?php if ( $listing->is_popular() ) : ?>
						<span class="badge popular-badge badge-success"><?php echo esc_html( $listing->get_popular_label_text() ); ?></span>
					<?php endif; ?>
					<?php if ( $listing->is_top() ) : ?>
						<span class="badge top-badge badge-warning"><?php echo esc_html( $listing->get_top_label_text() ); ?></span>
					<?php endif; ?>
					<?php if ( $listing->is_featured() ) : ?>
						<span class="badge feature-badge badge-warning"><?php echo esc_html( $listing->get_featured_label_text() ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<ul class="rtin-meta">
				<li><i class="far fa-clock" aria-hidden="true"></i><?php $listing->the_time();?></li>
				<?php if ( $listing->can_show_location() ): ?>
					<li><i class="fa fa-map-marker" aria-hidden="true"></i><?php $listing->the_locations( true, false ); ?></li>
				<?php endif; ?>
				<?php if ( $listing->can_show_category() && $category ): ?>
					<li><i class="fa fa-tag" aria-hidden="true"></i><a href="<?php echo esc_url( Link::get_category_page_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
				<?php endif; ?>
			</ul>
			<div class="rtcl-listable">
				<div class="rtcl-listable-item">
					<span class="listable-label"><?php esc_html_e( 'Status', 'classima' ); ?></span>
					<span class="listable-value"><?php echo esc_html( Functions::get_status_i18n( $post->post_status ) ); ?></span>
				</div>
                <?php if ($listing->get_status() !== 'pending') { ?>
                    <div class="rtcl-listable-item">
                        <span class="listable-label"><?php esc_html_e( 'Expires on', 'classima' ); ?></span>
                        <span class="listable-value"><?php echo esc_html( $expiry_date ); ?></span>
                    </div>
                <?php } ?>
			</div>
            <div class="btn-group btn-group-justified rtin-action-btn">
            		<a href="<?php echo esc_url( Link::get_checkout_endpoint_url( 'submission', $post->ID ) ); ?>" class="btn btn-primary btn-sm btn-block"><?php esc_html_e( 'Promote', 'classima' ); ?></a>

            	<?php if ( $listing->can_edit() ): ?>
            		<a href="<?php echo esc_url( Link::get_listing_edit_page_link( $post->ID ) ); ?>" class="btn btn-default btn-sm rtcl-edit-listing" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Edit', 'classima' ); ?></a>
            	<?php endif; ?>

            	<?php if ( $listing->can_delete() ): ?>
            		<a href="#" class="btn btn-danger btn-sm rtcl-delete-listing" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Delete', 'classima' ); ?></a>
            	<?php endif; ?>
                <?php if ( method_exists('RtclPro\Helpers\Fns', 'is_enable_mark_as_sold') && Fns::is_enable_mark_as_sold()): ?>
                    <a data-id="<?php echo absint($listing->get_id()) ?>"
                       class="btn btn-default btn-sm mark-as-sold"><?php method_exists('RtclPro\Helpers\Fns', 'is_mark_as_sold') && Fns::is_mark_as_sold($listing->get_id()) ? esc_html_e("Mark as unsold", "classima") : esc_html_e("Mark as sold", "classima"); ?></a>
                <?php endif; ?>
            </div>
		</div>
		<div class="rtin-price-wrap">
            <?php
            if (method_exists( $listing, 'get_price_html')) {
                Functions::print_html($listing->get_price_html());
            }
            ?>
        </div>
	</div>
</div>