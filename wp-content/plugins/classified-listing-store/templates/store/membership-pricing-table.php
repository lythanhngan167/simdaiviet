<?php
/**
 * Membership pricing table
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} ?>

<?php if (!empty($payment_options)) : ?>
    <div class="rtcl-pricing-table row">
        <?php foreach ($payment_options as $option) :
            $pricing = rtcl()->factory->get_pricing($option->ID);
            $item = 12 / $settings['item_per_row'];
            $class = array(
                'rtcl-price-item-' . $pricing->getId(),
                'price-item-wrapper',
                'col-xs-12',
                'col-sm-6',
                'col-md-' . $item,
                'col-lg-' . $item
            );
            if (!empty($settings['class']) && is_array($settings['class']) && in_array($pricing->getId(), array_keys($settings['class']))) {
                $class = array_merge($class, $settings['class'][$pricing->getId()]);
            }
            $class = implode(' ', $class);
            ?>
            <div class="<?php echo esc_attr($class); ?>">
                <div class="card price-item text-center">
                    <div class="card-header">
                        <h5 class="card-title rtcl-po-price-title"><?php $pricing->the_title(); ?></h5>
                    </div>
                    <div class="rtcl-po-price">
                        <span class="payment-option-price">
                            <?php Functions::print_html(Functions::get_payment_formatted_price_html($pricing->getPrice())) ?>
                        </span>
                        <span class="visible">
                            <span class="day"><?php echo absint($pricing->getVisible()); ?></span>
                            <span class="day-unit"><?php esc_html_e("Days", 'classified-listing-store') ?></span>
                        </span>
                    </div>
                    <?php if ($features = $pricing->getFeatures()): ?>
                        <div class="pricing-features">
                            <?php Functions::print_html($features); ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if ($description = $pricing->getDescription()): ?>
                            <div class="pricing-description">
                                <?php Functions::print_html($description); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer mtp-action">
                        <a href="<?php echo esc_url(add_query_arg('option', $pricing->getId(), Link::get_checkout_endpoint_url('membership'))); ?>"
                           class="btn btn-lg btn-block btn-danger">
                            <?php esc_html_e("Sign Up", 'classified-listing-store'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>