<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use RtclPro\Controllers\Hooks\TemplateHooks;

?>
<div class="swiper-slide">
    <div class="listing-grid-each listing-grid-each-5<?php echo esc_attr( $class ); ?>">
        <a class="rtin-item" href="<?php the_permalink(); ?>">
            <div class="rtin-thumb"><?php $listing->the_thumbnail(); ?></div>
            <div class="rtin-content">

                <h3 class="rtin-title listing-title" title="<?php the_title(); ?>"><?php the_title(); ?></h3>

                <?php
                if ( $display['fields'] ) {
                    TemplateHooks::loop_item_listable_fields();
                }
                ?>

                <div class="rtin-meta-area">
                    <div class="rtin-meta"><?php $listing->the_time();?> / <?php $listing->the_locations( true, false ); ?></div>

                    <?php if ( $display['price'] ): ?>
                        <div class="rtin-price">
                            <?php
                            if (method_exists( $listing, 'get_price_html')) {
                                Functions::print_html($listing->get_price_html());
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </a>
    </div>
</div>