<?php
/**
 * Login Form Information
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var Listing $listing
 * @var int $title_limit
 * @var array $hidden_fields
 * @var string $selected_type
 * @var string $title
 * @var string $listing_pricing
 * @var string $price_type
 * @var string $price
 * @var string $post_content
 * @var string $editor
 * @var int $category_id
 * @var int $post_id
 * @var int $description_limit
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Resources\Options;

?>

<div class="rtcl-post-details rtcl-post-section rtcl-post-section-info">
    <div class="classified-listing-form-title">
        <i class="fa fa-folder-open" aria-hidden="true"></i>
        <h3><?php esc_html_e( 'Product Information', 'classima' ); ?></h3>
    </div>
    <div class="row classima-form-title-row">
        <div class="col-sm-3 col-12">
            <label class="control-label"><?php esc_html_e( 'Title', 'classima' ); ?><span> *</span></label>
        </div>
        <div class="col-sm-9 col-12">
            <div class="form-group">
                <input type="text"
					<?php echo esc_attr( $title_limit ) ? 'data-max-length="3" maxlength="' . esc_attr( $title_limit ) . '"' : ''; ?>
                       class="form-control"
                       value="<?php echo esc_attr( $title ); ?>"
                       id="rtcl-title"
                       name="title"
                       required/>
				<?php
				if ( $title_limit ) {
					$dtext = sprintf( "<span class='target-limit'>%s</span>", $title_limit );
					$dtext = sprintf( esc_html__( "Character limit %s", 'classima' ), $dtext );
					$html  = '<div class="rtcl-hints">' . $dtext . '</div>';
					echo wp_kses_post( $html );
				}
				?>
            </div>
        </div>
    </div>
	<?php if ( ! in_array( 'price', $hidden_fields ) ):
		$listingPricingTypes = Options::get_listing_pricing_types();
		?>
        <div id="rtcl-pricing-wrap">
			<?php if ( ! Functions::is_pricing_disabled() ) { ?>
                <div class="row" id="rtcl-form-pricing-type-wrap">
                    <div class="col-sm-3 col-12">
                        <label class="control-label"><?php esc_html_e( "Pricing", "classima" ); ?></label>
                    </div>
                    <div class="col-sm-9 col-12">
						<?php
						foreach ( $listingPricingTypes as $type_id => $type ) {
							?>
                            <div class="rtcl-checkbox rtcl-listing-pricing-type">
                                <input type="radio" name="_rtcl_listing_pricing"
                                       id="_rtcl_listing_pricing_<?php echo esc_attr( $type_id ) ?>"
									<?php echo $listing_pricing === $type_id ? 'checked' : '' ?>
                                       value="<?php echo esc_attr( $type_id ) ?>">
                                <label for="_rtcl_listing_pricing_<?php echo esc_attr( $type_id ) ?>">
									<?php echo esc_html( $type ); ?>
                                </label>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
			<?php } ?>
            <div id="rtcl-pricing-items" class="<?php echo esc_attr( 'rtcl-pricing-' . $listing_pricing ) ?>">
				<?php if ( ! in_array( 'price_type', $hidden_fields ) && ! Functions::is_price_type_disabled() ): ?>
                    <div class="row" id="rtcl-form-price-wrap">
                        <div class="col-sm-3 col-12">
                            <label class="control-label"><?php esc_html_e( 'Price Type', 'classima' ); ?><span> *</span></label>
                        </div>
                        <div class="col-sm-9 col-12">
                            <div class="form-group">
                                <select class="form-control rtcl-select2" id="rtcl-price-type" name="price_type">
									<?php
									$price_types = Options::get_price_types();
									foreach ( $price_types as $key => $type ) {
										$slt = $price_type == $key ? " selected" : null;
										echo "<option value='{$key}'{$slt}>{$type}</option>";
									}
									?>
                                </select>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>
				<?php do_action( 'rtcl_listing_form_price_items', $listing ); ?>
                <div id="rtcl-price-items"
                     class="rtcl-pricing-item<?php echo ! Functions::is_price_type_disabled() ? ( ' rtcl-price-type-' . esc_attr( $price_type ) ) : '' ?>">
                    <div class="rtcl-price-item" id="rtcl-price-wrap">
                        <div class="price-wrap">
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <label class="control-label">
	                                    <?php echo sprintf( '<span class="price-label">%s [<span class="rtcl-currency-symbol">%s</span>]</span>',
		                                    esc_html__( "Price", 'classima' ),
		                                    apply_filters( 'rtcl_listing_price_currency_symbol', Functions::get_currency_symbol(), $listing )
	                                    ); ?>
                                        <span> *</span>
                                    </label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               value="<?php echo $listing ? esc_attr( $listing->get_price() ) : ''; ?>"
                                               name="price"
                                               id="rtcl-price"<?php echo esc_attr( ! $price_type || $price_type == 'fixed' ? " required" : '' ) ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="price-wrap rtcl-max-price rtcl-hide">
                            <div class="row">
                                <div class="col-lg-4 col-md-3 col-12">
                                    <label class="control-label" for="rtcl-max-price">
	                                    <?php echo sprintf( '<span class="price-label">%s [<span class="rtcl-currency-symbol">%s</span>]</span>',
		                                    esc_html__( "Max Price", 'classima' ),
		                                    apply_filters( 'rtcl_listing_price_currency_symbol', Functions::get_currency_symbol(), $listing )
	                                    ); ?>
                                        <span class="require-star">*</span>
                                    </label>
                                </div>
                                <div class="col-lg-8 col-md-9 col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control rtcl-price"
                                               value="<?php echo $listing ? esc_attr( $listing->get_max_price() ) : ''; ?>"
                                               name="_rtcl_max_price"
                                               id="rtcl-max-price"<?php echo esc_attr( ! $price_type || $price_type == 'fixed' ? " required" : '' ) ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php do_action( 'rtcl_listing_form_price_unit', $listing, $category_id ); ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

    <div id="rtcl-custom-fields-list" data-post_id="<?php echo esc_attr( $post_id ); ?>">
		<?php do_action( 'wp_ajax_rtcl_custom_fields_listings', $post_id, $category_id ); ?>
    </div>

	<?php if ( ! in_array( 'description', $hidden_fields ) ): ?>
        <div class="row classima-form-des-row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Description', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
					<?php

					if ( 'textarea' == $editor ) { ?>
                        <textarea
                                id="description"
                                name="description"
                                class="form-control"
						<?php echo esc_attr( $description_limit ) ? 'maxlength="' . esc_attr( $description_limit ) . '"' : ''; ?>
						rows="8"><?php Functions::print_html( $post_content ); ?></textarea>
						<?php
					} else {
						wp_editor(
							$post_content,
							'description',
							[
								'media_buttons' => false,
								'editor_height' => 200
							]
						);
					}


					if ( $description_limit ) {
						$dtext = sprintf( "<span class='target-limit'>%s</span>", $description_limit );
						$dtext = sprintf( esc_html__( "Character limit %s", 'classima' ), $dtext );
						$html  = '<div class="rtcl-hints">' . $dtext . '</div>';
						echo wp_kses_post( $html );
					}

					?>
                </div>
            </div>
        </div>
	<?php endif; ?>

</div>

<?php
$spec_textarea = '';
if ( $post_id ) {
	$specs         = get_post_meta( $post_id, 'classima_spec_info', true );
	$spec_textarea = ! empty( $specs['specs'] ) ? $specs['specs'] : '';
}
?>
<?php if ( ! in_array( 'features', $hidden_fields ) ): ?>
    <div class="rtcl-post-details rtcl-post-section rtcl-post-section-features">
        <div class="classified-listing-form-title">
            <i class="fa fa-list-ul" aria-hidden="true"></i>
            <h3><?php esc_html_e( 'Features', 'classima' ); ?></h3>
        </div>
        <div class="row">
            <div class="col-sm-3 col-12">
                <label class="control-label"><?php esc_html_e( 'Features List', 'classima' ); ?></label>
            </div>
            <div class="col-sm-9 col-12">
                <div class="form-group">
                    <textarea class="form-control" name="classima_spec_info[specs]"
                              rows="5"><?php echo esc_textarea( $spec_textarea ); ?></textarea>
                    <div class="help-block-2">
						<?php printf( esc_html__( 'Write a feature in each line eg. %1$sFeature 1%1$sFeature 2%1$s...', 'classima' ), '<br/>' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
