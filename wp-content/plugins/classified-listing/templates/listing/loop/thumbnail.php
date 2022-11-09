<?php

global $listing;

?>
<div class="listing-thumb">
    <div class="listing-thumb-inner">
        <a href="<?php $listing->the_permalink(); ?>" class="rtcl-media"><?php $listing->the_thumbnail(); ?></a>
        <?php
        /**
         * Hook: rtcl_after_listing_thumbnail.
         *
         * @hooked loop_item_meta_buttons - 10
         */
        do_action('rtcl_after_listing_thumbnail');
        ?>
    </div>
</div>