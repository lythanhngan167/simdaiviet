<?php
/**
 *
 * @author        RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 *
 * @var array $fields
 * @var int $listing_id
 */


use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;


if ( count( $fields ) ) :
	ob_start();
	foreach ( $fields as $field ) :
		$field = new RtclCFGField( $field->ID );
		$value = $field->getFormattedCustomFieldValue( $listing_id );
		if ( $value ) :
			?>
            <div class='rtcl-listable-item'>
                <span class='listable-label'><?php echo esc_html( $field->getLabel() ) ?></span>
                <span class='listable-value'><?php Functions::print_html( $value ); ?></span>
            </div>
		<?php endif;
	endforeach;

	$fields_html = ob_get_clean();
	if ( $fields_html ) {
		printf( '<div class="rtcl-listable">%s</div>', $fields_html );
	}
endif;
