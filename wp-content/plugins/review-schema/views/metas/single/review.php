<?php
 
$helper = new Rtrs\Helpers\Functions;
$meta_options = new Rtrs\Controllers\Admin\Meta\SingleMetaOptions; 

echo $helper->fieldGenerator($meta_options->sectionReviewFields(), true);