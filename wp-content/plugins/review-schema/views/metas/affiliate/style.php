<?php
 
$helper = new Rtrs\Helpers\Functions;
$meta_options = new Rtrs\Controllers\Admin\Meta\AffiliateOptions;

echo $helper->fieldGenerator($meta_options->sectionStyleFields(), true);