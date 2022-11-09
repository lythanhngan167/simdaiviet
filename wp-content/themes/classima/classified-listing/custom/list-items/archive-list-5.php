<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclPro\Controllers\Hooks\TemplateHooks;

?>
<div class="listing-list-each listing-item listing-list-each-5<?php echo esc_attr( $class ); ?>">
	<div class="rtin-item">
        <div class="rtin-thumb">
            <a class="rtin-thumb-inner rtcl-media" href="<?php the_permalink(); ?>"><?php $listing->the_thumbnail(); ?></a>
        </div>
		<div class="rtin-content">
			
			<h3 class="rtin-title listing-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

            <?php
            if ( $display['fields'] ) {
                TemplateHooks::loop_item_listable_fields();
            }
            ?>

			<ul class="rtin-meta">
				<?php if ( $display['user'] && method_exists($listing, 'get_the_author_url') ): ?>
					<li class="rtin-usermeta"><i class="far fa-user" aria-hidden="true"></i>
						<?php if ($listing->can_add_user_link() && !is_author()) : ?>
                            <a href="<?php echo esc_url($listing->get_the_author_url()); ?>"><?php $listing->the_author(); ?></a>
						<?php else: ?>
							<?php $listing->the_author(); ?>
						<?php endif; ?>
						<?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
                    </li>
				<?php endif; ?>
				<?php if ( $display['location'] && $listing->has_location() ): ?>
					<li><i class="fa fa-map-marker" aria-hidden="true"></i><?php $listing->the_locations( true, false ); ?></li>
				<?php endif; ?>
				<?php if ( $display['cat'] ): ?>
					<li><i class="fa fa-tag" aria-hidden="true"></i><a class="rtin-cat" href="<?php echo esc_url( Link::get_category_page_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
				<?php endif; ?>
				<?php if ( $display['date'] ): ?>
					<li><i class="far fa-clock" aria-hidden="true"></i><?php $listing->the_time();?></li>
				<?php endif; ?>
				<?php if ( $display['views'] ): ?>
					<li><i class="fa fa-eye" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%1$s Views', 'classima' ) , number_format_i18n( $listing->get_view_counts() ) ); ?></li>
				<?php endif; ?>
			</ul>

			<?php if ( $display['excerpt'] ): ?>
				<?php 
				$excerpt = Helper::get_current_post_content( $listing_post );
				$excerpt = wp_trim_words( $excerpt, $display['excerpt_limit'] );
				?>
				<p class="rtin-excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $display['price'] ): ?>
				<div class="rtin-price">
                    <?php
                    if (method_exists( $listing, 'get_price_html')) {
                        Functions::print_html($listing->get_price_html());
                    }
                    ?>
                </div>
			<?php endif; ?>	

			<?php do_action( 'classima_listing_list_view_after_content', $listing );?>
		</div>
	</div>
</div>