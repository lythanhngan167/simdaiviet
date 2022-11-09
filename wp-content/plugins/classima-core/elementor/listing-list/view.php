<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use radiustheme\Classima\Helper;

$layout = $data['style'];
$display = array(
	'cat'           => $data['cat_display'] ? true : false,
	'label'         => false,
    'fields'   => $data['field_display']==='yes' ? true : false,
	'excerpt_limit' => $data['content_limit'],
);

$query = $data['query'];
?>
<div class="rt-el-listing-list rtcl-list-view">
	<?php if ( $query->have_posts() ) :?>
		<?php while ( $query->have_posts() ) : $query->the_post();?>
			<?php Helper::get_template_part( 'classified-listing/custom/list', compact( 'layout', 'display' ) );?>
		<?php endwhile;?>
	<?php endif;?>
	<?php wp_reset_postdata();?>
</div>