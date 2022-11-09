<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( !$data['rt_results'] ) {
	return;
}
?>
<div class="headerCategories rt-listing-cat-list">
    <?php if ($data['all_cat_btn'] == 'yes'): ?>
        <div class="headerCategoriesMenu">
            <span><?php esc_html_e('See All Categories', 'classima-core'); ?></span>
            <?php if (!empty($data['rt_results'])): ?>
                <ul class="headerCategoriesMenu__dropdown">
                <?php foreach ( $data['rt_results'] as $result ): ?>
                    <li>
                        <a class="rtin-title-area" href="<?php echo esc_attr( $result['permalink'] ); ?>">
                            <?php if ( $result['icon_html'] ): ?>
                                <div class="rtin-icon"><?php echo wp_kses_post( $result['icon_html'] ); ?></div>
                            <?php endif; ?>
                            <?php echo esc_html( $result['name'] );
                            if ( $data['count'] ) {
                                $count_html = sprintf(" (%d)", number_format_i18n( $result['count'] ));
                                echo esc_html( $count_html );
                            }
                            ?>
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
                                        <a href="<?php echo esc_attr( $sub_cat['permalink'] ); ?>">
                                            <?php if ( $sub_cat['icon_html'] ): ?>
                                                <div class="rtin-icon"><?php echo wp_kses_post( $sub_cat['icon_html'] ); ?></div>
                                            <?php endif; ?>
                                            <?php echo esc_html( $sub_cat_html ); ?>
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
    <?php endif; ?>
	<?php
	$args = [
		'parent'     => 0,
		'include'    => $data['top_cats_list'] ? $data['top_cats_list'] : [],
		'hide_empty' => $data['hide_empty'] ? true : false,
		'order'      => 'asc'
	];
	if ( $data['orderby'] == 'custom' ) {
		$args['orderby']  = 'meta_value_num';
		$args['order']    = $data['sortby'] ? $data['sortby'] : 'asc';
		$args['meta_key'] = '_rtcl_order';
	} else {
		$args['orderby'] = $data['orderby'] ? $data['orderby'] : 'name';
		$args['order']   = $data['sortby'] ? $data['sortby'] : 'asc';
	}
	$terms = get_terms( 'rtcl_category', $args );
	if ( $data['top_cats_list'] && ! $terms ) {
		$args['include'] = [];
		$terms           = get_terms( 'rtcl_category', $args );
	}
	if ($data['top_cat'] == 'yes' && !empty($terms)): ?>
        <div class="headerTopCategoriesNav">
            <ul>
                <li><span><?php esc_html_e('Top Categories:', 'classima-core'); ?></span></li>
                <?php
                $html = '';
                foreach ( $terms as $term ) {
                    $html .= '<li><a href="'.Link::get_category_page_link( $term ).'">'.$term->name.'</a></li>';
                }
                Functions::print_html($html);
                ?>
            </ul>
        </div>
    <?php endif; ?>
</div>