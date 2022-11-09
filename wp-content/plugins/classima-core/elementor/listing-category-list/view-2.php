<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( !$data['rt_results'] ) {
	return;
}
?>
<div class="rt-listing-cat-list-2">
	<div class="listing-category-list">
		<?php if (!empty($data['rt_results'])): ?>
			<ul>
				<?php foreach ( $data['rt_results'] as $result ): ?>
					<li>
						<a class="sidebar-el-category__link" href="<?php echo esc_attr( $result['permalink'] ); ?>">
                            <div class="sidebar-el-category-block">
                                <?php if ( $result['icon_html'] ): ?>
                                    <div class="sidebar-el-category-block__icon"><?php echo wp_kses_post( $result['icon_html'] ); ?></div>
                                <?php endif; ?>
                                <div class="sidebar-el-category-block__content">
                                    <h5 class="sidebar-el-category-block__heading"><?php echo esc_html( $result['name'] ); ?></h5>
                                    <?php
                                    if ( $data['count'] ) {
                                        $count_html = sprintf(" ( %d )", number_format_i18n( $result['count'] ));
                                        ?>
                                        <span class="sidebar-el-category-block__count"><?php echo esc_html( $count_html ); ?></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
							<?php if (!empty($result['sub_cats'])): ?>
                                <div class="sidebar-el-category__arrow">
                                    <img src="<?php echo Helper::get_img('icon-angel-arrow-right.svg') ?>" alt="Arrow icon">
                                </div>
                            <?php endif; ?>
						</a>
						<?php if (!empty($result['sub_cats'])): ?>
							<ul>
								<?php foreach ( $result['sub_cats'] as $sub_cat ): ?>
									<?php
									if ( $data['count'] ) {
										$sub_cat_html = sprintf( '%s (%s)' , $sub_cat['name'], number_format_i18n( $sub_cat['count'] ) );
									} else {
										$sub_cat_html = $sub_cat['name'];
									}
									?>
									<li>
										<a class="sidebar-el-category__link" href="<?php echo esc_attr( $sub_cat['permalink'] ); ?>">
                                            <div class="sidebar-el-category-block">
                                                <?php if ( $sub_cat['icon_html'] ): ?>
                                                    <div class="sidebar-el-category-block__icon"><?php echo wp_kses_post( $sub_cat['icon_html'] ); ?></div>
                                                <?php endif; ?>
                                                <div class="sidebar-el-category-block__content">
                                                    <h5 class="sidebar-el-category-block__heading"><?php echo esc_html( $sub_cat_html ); ?></h5>
                                                </div>
                                            </div>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>