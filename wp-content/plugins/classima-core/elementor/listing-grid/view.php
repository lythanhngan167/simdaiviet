<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;

$col_class = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']}";

$layout  = $data['style'];
$display = [
	'cat'      => $data['cat_display'] ? true : false,
	'views'    => $data['views_display'] === 'yes' ? true : false,
	'fields'   => $data['field_display'] === 'yes' ? true : false,
	'type'     => $data['type_display'] === 'yes' ? true : false,
	'date'     => $data['date_display'] === 'yes' ? true : false,
	'location' => $data['location_display'] === 'yes' ? true : false,
	'label'    => false,
];

if ( $data['style'] == 1 ) {
	$display['views'] = false;
}

$query         = $data['query'];
$selectedQuery = [
	'cat'           => $data['cat'],
	'type'          => $data['type'],
	'orderby'       => $data['orderby'],
	'order'         => $data['order'],
	'ids'           => $data['ids'],
	'random'        => $data['random'],
	'post_per_page' => $data['number']
];
?>
<div class="rt-el-listing-grid">
	<?php if ( $query->have_posts() ): ?>
        <div class="row auto-clear">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <div class="<?php echo esc_attr( $col_class ); ?>">
					<?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) ); ?>
                </div>
			<?php endwhile; ?>
        </div>
		<?php if ( ( $layout == '8' || $layout == '9' ) && $data['load_more_btn_display'] == 'yes' ): ?>
            <div class="text-center load-more-wrapper layout-<?php echo esc_attr( $layout ); ?>"
                 data-col-class="<?php echo esc_attr( $col_class ); ?>"
                 data-total-pages="<?php echo esc_attr( $query->max_num_pages ); ?>"
                 data-layout="<?php echo esc_attr( $layout ); ?>"
                 data-options='<?php echo json_encode( $display ); ?>' data-page="1"
                 data-query='<?php echo json_encode( $selectedQuery ); ?>'
                 data-posts-per-page="<?php echo esc_attr( $data['number'] ); ?>">
                <button id="el_load_more" class="btn load-more-btn">
                    <i class="fas fa-sync-alt"></i>
					<?php esc_html_e( 'Load More', 'classima-core' ); ?>
                </button>
            </div>
		<?php endif; ?>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
</div>