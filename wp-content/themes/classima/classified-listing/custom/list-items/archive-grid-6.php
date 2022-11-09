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

use function PHPSTORM_META\type;

?>
<div class="swiper-slide">
    <div class="listing-grid-each listing-grid-each-6<?php echo esc_attr( $class ); ?>">
        <div class="rtin-item">
            
            <!-- <div class="rtin-thumb">
                <a class="rtin-thumb-inner rtcl-media" href="<?php the_permalink(); ?>"><?php $listing->the_thumbnail(); ?></a>
	            <?php if ( $display['type'] ): ?>
                    <div class="rtin-type">
                        <span><?php echo sprintf( apply_filters( 'classima_ad_type_prefix', __( "For %s", 'classima' ), $type['label'] ), $type['label'] ); ?></span>
                    </div>
	            <?php endif; ?>
            </div> -->
            <div class="rtin-content">
            
                <?php if ( $display['cat'] ): ?>
                    <a class="rtin-cat" href="<?php echo esc_url( the_permalink() ); ?>"><?php echo esc_html( $category->name ); ?></a>
                <?php endif; ?>
                
                <h3 class="rtin-title listing-title" title="<?php the_title(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                <?php
                if ( $display['fields'] ) {
                    TemplateHooks::loop_item_listable_fields();
                }
                ?>

                <!-- <ul class="rtin-meta">
                    <?php if ( $display['date'] ): ?>
                        <li><i class="far fa-fw fa-clock" aria-hidden="true"></i><?php $listing->the_time(); ?></li>
                    <?php endif; ?>
                    <?php if ( $display['location'] && $listing->has_location() ): ?>
                        <li><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i><?php $listing->the_locations( true, false ); ?></li>
                    <?php endif; ?>
                    <?php if ( $display['views'] ): ?>
                        <li><i class="fa fa-fw fa-eye" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%1$s Views', 'classima' ) , number_format_i18n( $listing->get_view_counts() ) ); ?></li>
                    <?php endif; ?>
                </ul> -->
                <?php if ( $display['price'] ):  ?>
                    <a href="<?php echo esc_url( the_permalink() ); ?>" class="rtin-price" >
                        <?php
                       
                        if (method_exists( $listing, 'get_price_html')) {
                            Functions::print_html($listing->get_price_html());
                            
    
                        }
                        
                        ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>