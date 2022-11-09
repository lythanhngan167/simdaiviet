<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;

$col_class  = "col-xl-{$data['col_xl']} col-lg-{$data['col_lg']} col-md-{$data['col_md']} col-sm-{$data['col_sm']} col-{$data['col_mobile']} ";

$uniqueid = time().rand( 1, 99 );
$count = 0;

$layout = $data['style'];
$display = array(
	'cat'   => $data['cat_display'] ? true : false,
    'fields'   => $data['field_display']==='yes' ? true : false,
	'type'      => $data['type_display']==='yes' ? true : false,
	'label' => false,
);
?>
<div class="rt-el-listing-isotope rt-el-isotope-container">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="rt-el-isotope-tab rtin-btn">
				<?php foreach ( $data['navs'] as $key => $value ): ?>
					<?php
					$count++;
					$navclass = '';
					if ( $count == 1) {
						$navclass = 'current';
					}
					?>
					<a class= "<?php echo esc_attr( $navclass );?>" href="#" data-filter=".<?php echo esc_attr( $uniqueid.$key );?>"><?php echo esc_html( $value );?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="row rt-el-isotope-wrapper">
		<?php foreach ( $data['queries'] as $key => $query ): ?>
			<?php if ( $query->have_posts() ) :?>
				<?php while ( $query->have_posts() ) : $query->the_post();?>
					<div class="<?php echo esc_attr( $col_class.$uniqueid.$key );?>">
						<?php Helper::get_template_part( 'classified-listing/custom/grid', compact( 'layout', 'display' ) );?>
					</div>
				<?php endwhile;?>
			<?php else: ?>
				<div class="rtin-no-item col-sm-12 col-12 <?php echo esc_attr( $uniqueid.$key );?>"><?php esc_html_e( 'Không có sim nào', 'classima-core' );?></div>
			<?php endif;?>
			<?php wp_reset_postdata();?>
		<?php endforeach; ?>
	</div>
</div>