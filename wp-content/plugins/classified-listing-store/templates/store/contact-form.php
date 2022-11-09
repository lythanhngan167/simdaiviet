<?php
/**
 * Store contact form
 *
 * @author     RadiusTheme
 * @package    classified-listing-store/templates
 * @version    1.2.31
 */
?>
<div id="store-email-area">
    <?php do_action('rtcl_store_before_contact_form'); ?>
    <form class="form" id="store-email-form">
        <div class="form-group">
            <input type="text" name="name" id="sc-name"
                   placeholder="<?php esc_html_e("Your name", "classified-listing-store"); ?>"
                   class="form-control"
                   required>
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <input type="email" name="email" id="sc-email"
                   placeholder="<?php esc_html_e("Your email", "classified-listing-store"); ?>"
                   class="form-control" required>
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <input type="text" name="phone"
                   placeholder="<?php esc_html_e("Phone number", "classified-listing-store"); ?>"
                   id="sc-phone"
                   class="form-control">
            <div class="help-block"></div>
        </div>
        <div class="form-group">
                                <textarea rows="5" name="message" id="sc-message"
                                          placeholder="<?php esc_html_e("Message", "classified-listing-store"); ?>"
                                          class="form-control" required></textarea>
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <div class="rtcl-g-recaptcha-wrap"></div>
        </div>
        <?php do_action('rtcl_store_contact_form'); ?>
        <button class="btn btn-primary sc-submit">
            <?php esc_html_e("Send Message", "classified-listing-store"); ?>
        </button>
        <div class="rtcl-response"></div>
    </form>
    <?php do_action('rtcl_store_after_contact_form'); ?>
</div>