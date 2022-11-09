<?php
 
$helper = new Rtrs\Helpers\Functions;
$meta_options = new Rtrs\Controllers\Admin\Meta\MetaOptions;

echo $helper->fieldGenerator($meta_options->sectionSchemaFields(), true);