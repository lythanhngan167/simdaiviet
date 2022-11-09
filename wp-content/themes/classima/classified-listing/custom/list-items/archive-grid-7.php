<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.7
 */

namespace radiustheme\Classima;

use RtclPro\Controllers\Hooks\TemplateHooks;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;

$phone = get_post_meta( $listing_post->ID, 'phone', true );
?>
<div class="listing-grid-each listing-grid-each-7<?php echo esc_attr( $class ); ?>">
	<div class="rtin-item">
        <div class="rtin-thumb">
            <a class="rtin-thumb-inner rtcl-media" href="<?php the_permalink(); ?>"><?php $listing->the_thumbnail(); ?></a>
            <?php if ( $display['price'] ): ?>
                <div class="rtin-price">
                    <?php
                        if (method_exists( $listing, 'get_price_html')) {
                            Functions::print_html($listing->get_price_html());
                        }
                    ?>
                </div>
            <?php endif; ?>
            <?php TemplateHooks::sold_out_banner(); ?>
        </div>
		<div class="rtin-content">

			<?php if ( $display['cat'] ): ?>
				<a class="rtin-cat" href="<?php echo esc_url( Link::get_category_page_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endif; ?>

			<h3 class="rtin-title listing-title" title="<?php the_title(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

			<?php
			if ( $display['label'] ) {
				$listing->the_badges();
			}
			?>

            <?php
            if ( $display['fields'] ) {
                TemplateHooks::loop_item_listable_fields();
            }
            ?>

			<ul class="rtin-meta">
				<?php if ( $display['type'] && $type ): ?>
					<li><i class="fa fa-fw <?php echo esc_attr( $type['icon'] );?>" aria-hidden="true"></i><?php echo esc_html( $type['label'] ); ?></li>
				<?php endif; ?>
				<?php if ( $display['date'] ): ?>
					<li><i class="far fa-fw fa-clock" aria-hidden="true"></i><?php $listing->the_time();?></li>
				<?php endif; ?>
				<?php if ( $display['location'] && $listing->has_location() ): ?>
					<li><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i><?php $listing->the_locations( true, false ); ?></li>
				<?php endif; ?>
				<?php if ( $display['views'] ): ?>
					<li><i class="fa fa-fw fa-eye" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%1$s Views', 'classima' ) , number_format_i18n( $listing->get_view_counts() ) ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
        <?php if ( Fns::is_enable_quick_view() || Fns::is_enable_compare() || Functions::is_enable_favourite() || $display['user'] ): ?>
            <div class="rtin-bottom">
                <ul>
                    <?php if ( $display['user'] ): ?>
                        <li class="item-author">
                            <div class="item-img">
                                <?php
                                $pp_id = absint( get_user_meta( $listing->get_owner_id(), '_rtcl_pp_id', true ) );
                                if ( $listing->can_add_user_link() ): ?>
                                    <a href="<?php echo esc_url($listing->get_the_author_url()); ?>"><?php echo( $pp_id ? wp_get_attachment_image( $pp_id, [40, 40]) : get_avatar( $listing->get_author_id(), 40 ) ); ?></a>
                                <?php else:
	                                echo( $pp_id ? wp_get_attachment_image( $pp_id, [40, 40]) : get_avatar( $listing->get_author_id(), 40 ) );
                                endif;
                                ?>
                                <?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php if ( Fns::is_enable_quick_view() || Fns::is_enable_compare() || Functions::is_enable_favourite() ): ?>
                        <li class="action-btn">
                            <?php
                                if ( Functions::is_enable_favourite() ) {
                                    echo Functions::get_favourites_link( $listing_post->ID );
                                }
                            ?>
                            <?php if (Fns::is_enable_quick_view()) { ?>
                                <a class="rtcl-quick-view" href="#" title="<?php esc_attr_e("Quick View", "classima") ?>" data-listing_id="<?php echo absint($listing->get_id()) ?>">
                                    <i class="fas fa-search-plus"></i>
                                </a>
                            <?php } ?>
                            <?php if (Fns::is_enable_compare()) {
                                $compare_ids = !empty($_SESSION['rtcl_compare_ids']) ? $_SESSION['rtcl_compare_ids'] : [];
                                $selected_class = '';
                                if (is_array($compare_ids) && in_array($listing->get_id(), $compare_ids)) {
                                    $selected_class = ' selected';
                                }
                                ?>
                                <a class="rtcl-compare <?php echo esc_attr($selected_class); ?>" href="#" title="<?php esc_attr_e("Compare", "classima") ?>" data-listing_id="<?php echo absint($listing->get_id()) ?>">
                                    <i class="fa fa-retweet"></i>
                                </a>
                            <?php } ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
	</div>
	<?php if ( $map ) $listing->the_map_lat_long();?>
</div>