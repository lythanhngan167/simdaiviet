<?php
/**
 * Login Form Gallery
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var string $post_id
 */

use Rtcl\Resources\Gallery;

?>
<div class="rtcl-post-gallery rtcl-post-section">
    <div class="classified-listing-form-title">
        <i class="fa fa-image" aria-hidden="true"></i><h3><?php esc_html_e( "Images", 'classima' ); ?></h3>
    </div>
	<?php Gallery::rtcl_gallery_content( get_post( $post_id ), array( 'post_id_input' => '#_post_id' ) ); ?>
</div>