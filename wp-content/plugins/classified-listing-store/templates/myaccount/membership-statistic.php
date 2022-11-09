<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.0.0
 *
 * @var \WP_User $current_user
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Resources\Options;

$member = rtclStore()->factory->get_membership();

?>

<div class="membership-statistic-report-wrap">
    <h4><?php esc_html_e("Báo cáo thành viên", "classified-listing-store") ?></h4>
    <div class="statistic-report">
        <?php
        if ($member->has_membership()):?>
            <div class="reports">
                <div class="report-item rtcl-membership-status">
                    <label><?php esc_html_e('Trạng thái', 'classified-listing-store') ?></label>
                    <div class="value">
                        <?php if ($member->is_expired()): ?>
                            <span class="expired"><?php esc_html_e("Chưa kích hoạt", "classified-listing-store") ?></span>
                        <?php else: ?>
                            <span class="active"><?php esc_html_e("Đã kích hoạt", "classified-listing-store") ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="report-item rtcl-membership-validity">
                    <label><?php esc_html_e('Hiệu lực', 'classified-listing-store') ?></label>
                    <div class="value">
                        <?php
                        printf('<strong>%s:</strong> %s',
                            $member->is_expired() ? __("Đã hết hạn vào", "classified-listing-store") : __("Cho đến khi", "classified-listing-store"),
                            Functions::datetime('rtcl', $member->get_expiry_date())
                        );
                        ?>
                    </div>
                </div>
                <div class="report-item rtcl-membership-remaining-ads">
                    <label><?php esc_html_e('Bài viết Còn lại', 'classified-listing-store') ?></label>
                    <div class="value"><?php echo esc_html($member->get_remaining_ads()); ?></div>
                </div>
                <div class="report-item rtcl-membership-posted-ads">
                    <label><?php esc_html_e('Bài viết đã Đăng', 'classified-listing-store') ?></label>
                    <div class="value"><?php echo esc_html($member->get_posted_ads()); ?></div>
                </div>
                <?php if (!empty($promotions = $member->get_promotions())): ?>
                    <div class="report-item rtcl-membership-promotions">
                        <table class="rtcl-responsive-table table table-hover table-stripped table-bordered">
                            <tr class="promotion-item">
                                <th class="promotion-label"><?php esc_html_e("Khuyến mại", "classified-listing-store"); ?></th>
                                <th class="promotion-ads"><?php esc_html_e("Các bài viết còn lại", "classified-listing-store"); ?></th>
                                <th class="promotion-validity"><?php _e("Thời hạn xác thực<small>(ngày)</small>", "classified-listing-store"); ?></th>
                            </tr>
                            <?php foreach ($promotions as $promotion_key => $promotion): ?>
                                <tr class="promotion-item">
                                    <th class="promotion-label"
                                        data-label="<?php esc_html_e("Khuyến mại:", "classified-listing-store"); ?>"><?php esc_html_e(Options::get_listing_promotions()[$promotion_key]); ?></th>
                                    <td class="promotion-ads"
                                        data-label="<?php esc_html_e("Các bài viết còn lại:", "classified-listing-store"); ?>"><?php esc_html_e($promotion['ads']); ?></td>
                                    <td class="promotion-validate"
                                        data-label="<?php esc_html_e('Thời hạn xác thực:', 'classified-listing-store') ?>"><?php esc_html_e($promotion['validate']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p><?php esc_html_e("Bạn không có thành viên.", "classified-listing-store") ?></p>
        <?php endif ?>
        <p><?php printf(__("Bạn có thể mua một thành viên từ <a href='%s'>đây</a>.", "classified-listing-store"), Link::get_checkout_endpoint_url('membership')) ?></p>
    </div>
</div>