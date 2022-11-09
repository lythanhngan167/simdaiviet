<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 *
 * @var Store $store
 */

use RtclStore\Models\Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div class='rtcl-member-store-info list-group-item'>
    <span class="title"><?php esc_html_e( "Visit member's page", "classified-listing-store" ); ?></span>
    <div class='media mt-3'>
		<?php if ( $store->has_logo() ): ?>
            <a class="mr-3" href="<?php $store->the_permalink(); ?>">
				<?php $store->the_logo() ?>
            </a>
		<?php endif; ?>
        <div class='media-body'>
            <h5 class="mt-0">
                <a href="<?php $store->the_permalink(); ?>"><?php $store->the_title() ?></a>
            </h5>
			<?php $store->the_slogan(); ?>
        </div>
    </div>
	<?php ?>
</div>
