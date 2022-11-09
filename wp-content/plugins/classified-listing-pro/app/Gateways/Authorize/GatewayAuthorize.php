<?php

namespace RtclPro\Gateways\Authorize;


use Rtcl\Log\Logger;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;
use RtclPro\Gateways\Authorize\lib\AuthorizeNetAIM;

class GatewayAuthorize extends PaymentGateway
{

    /**
     * @var string
     */
    private $authorizenet_cardtypes;

    public function __construct() {
        $this->id = 'authorizenet';
        $this->option = $this->option . $this->id;
        $this->icon = plugins_url('images/authorizenet.png', __FILE__);
        $this->has_fields = true;
        $this->method_title = 'Authorize.Net Cards Settings';
        $this->init_form_fields();
        $this->init_settings();
        $this->supports = array('products', 'refunds');
        $this->authorizenet_description = $this->get_option('authorizenet_description');

        $this->title = $this->get_option('authorizenet_title');
        $this->authorizenet_apilogin = $this->get_option('authorizenet_apilogin'); // "43j733Z8wKz";//
        $this->authorizenet_transactionkey = $this->get_option('authorizenet_transactionkey'); // 5329wuCMF2FDY8ga
        $this->authorizenet_sandbox = $this->get_option('authorizenet_sandbox');
        $this->authorizenet_authorize_only = $this->get_option('authorizenet_authorize_only');
        $this->authorizenet_cardtypes = $this->get_option('authorizenet_cardtypes');
        $this->authorizenet_meta_cartspan = $this->get_option('authorizenet_meta_cartspan');

        if (!defined("AUTHORIZE_NET_SANDBOX")) {
            define("AUTHORIZE_NET_SANDBOX", $this->authorizenet_sandbox == 'yes');
        }
        if (!defined("AUTHORIZENET_TRANSACTION_MODE")) {
            define("AUTHORIZENET_TRANSACTION_MODE",
                $this->authorizenet_authorize_only == 'yes');
        }


        if ('yes' == AUTHORIZE_NET_SANDBOX) {
            if (!defined("AUTHORIZENET_API_LOGIN_ID")) {
                define("AUTHORIZENET_API_LOGIN_ID", $this->authorizenet_apilogin);
            }
            if (!defined("AUTHORIZENET_TRANSACTION_KEY")) {
                define("AUTHORIZENET_TRANSACTION_KEY", $this->authorizenet_transactionkey);
            }
            if (!defined("AUTHORIZENET_SANDBOX")) {
                define("AUTHORIZENET_SANDBOX", true);
            }

        } else {
            if (!defined("AUTHORIZENET_API_LOGIN_ID")) {
                define("AUTHORIZENET_API_LOGIN_ID", $this->authorizenet_apilogin);
            }
            if (!defined("AUTHORIZENET_TRANSACTION_KEY")) {
                define("AUTHORIZENET_TRANSACTION_KEY", $this->authorizenet_transactionkey);
            }
            if (!defined("AUTHORIZENET_SANDBOX")) {
                define("AUTHORIZENET_SANDBOX", false);
            }
        }
    }

    public function init_form_fields() {

        $this->form_fields = [
            'enabled'                     => [
                'title' => esc_html__('Enable/Disable', 'classified-listing-pro'),
                'type'  => 'checkbox',
                'label' => esc_html__('Enable Authorize.Net', 'classified-listing-pro'),
            ],
            'authorizenet_title'          => [
                'title'       => esc_html__('Title', 'classified-listing-pro'),
                'type'        => 'text',
                'description' => esc_html__('This controls the title which the buyer sees during checkout.', 'classified-listing-pro'),
                'default'     => esc_html__('Authorize.Net', 'classified-listing-pro'),
            ],
            'authorizenet_description'    => [
                'title'       => esc_html__('Description', 'classified-listing-pro'),
                'type'        => 'textarea',
                'description' => esc_html__('This controls the description which the user sees during checkout.', 'classified-listing-pro'),
                'default'     => esc_html__('All cards are charged by &copy;Authorize.Net &#174;&#8482; servers.', 'classified-listing-pro'),
            ],
            'authorizenet_apilogin'       => [
                'title'       => esc_html__('API Login ID', 'classified-listing-pro'),
                'type'        => 'text',
                'description' => esc_html__('This is the API Login ID Authorize.net.', 'classified-listing-pro'),
                'default'     => '',
                'placeholder' => esc_html__('Authorize.Net API Login ID', 'classified-listing-pro')
            ],
            'authorizenet_transactionkey' => [
                'title'       => esc_html__('Transaction Key', 'classified-listing-pro'),
                'type'        => 'text',
                'description' => esc_html__('This is the Transaction Key of Authorize.Net.', 'classified-listing-pro'),
                'default'     => '',
                'placeholder' => esc_html__('Authorize.Net Transaction Key', 'classified-listing-pro')
            ],
            'authorizenet_sandbox'        => [
                'title'       => esc_html__('Authorize.Net sandbox', 'classified-listing-pro'),
                'type'        => 'checkbox',
                'label'       => esc_html__('Enable Authorize.Net sandbox (Live Mode if Unchecked)', 'classified-listing-pro'),
                'description' => esc_html__('If checked its in sanbox mode and if unchecked its in live mode', 'classified-listing-pro'),
                'default'     => 'no'
            ],
            'authorizenet_authorize_only' => [
                'title'       => esc_html__('Authorize Only', 'classified-listing-pro'),
                'type'        => 'checkbox',
                'label'       => __('Enable Authorize Only Mode (Authorize & Capture If Unchecked).<span style="color:red;">Make sure to keep <b>Unchecked</b> if your Address Verification Service (AVS) is set to hold transaction for review.</span>', 'classified-listing-pro'),
                'description' => esc_html__('If checked will only authorize the credit card only upon checkout.', 'classified-listing-pro'),
                'default'     => 'no',
            ],
            'authorizenet_meta_cartspan'  => [
                'title'       => esc_html__('Authorize.Net + Cartspan', 'classified-listing-pro'),
                'type'        => 'checkbox',
                'label'       => esc_html__('Enable Authorize.Net Metas for Cartspan', 'classified-listing-pro'),
                'description' => esc_html__('If checked will store last4 and card brand in local db from Transaction response', 'classified-listing-pro'),
                'default'     => 'no',
            ],
            'authorizenet_cardtypes'      => [
                'title'    => esc_html__('Accepted Cards', 'classified-listing-pro'),
                'type'     => 'multiselect',
                'class'    => 'rtcl-select2',
                'css'      => 'width: 350px;',
                'desc_tip' => esc_html__('Select the card types to accept.', 'classified-listing-pro'),
                'options'  => [
                    'mastercard' => 'MasterCard',
                    'visa'       => 'Visa',
                    'discover'   => 'Discover',
                    'amex'       => 'American Express',
                    'jcb'        => 'JCB',
                    'dinersclub' => 'Dinners Club',
                ],
                'default'  => ['mastercard', 'visa', 'discover', 'amex'],
            ]
        ];
    }

    public function payment_fields() {
        $html = null;
        $html .= apply_filters('rtcl_authorizenet_description', wpautop(wp_kses_post(wptexturize(trim($this->authorizenet_description)))));
        $html .= $this->form();

        return $html;
    }

    public function field_name($name) {
        return $this->supports('tokenization') ? '' : ' name="' . esc_attr($this->id . '-' . $name) . '" ';
    }

    public function form() {
        $this->load_stripe_scripts();

        ob_start();
        ?>

        <fieldset id="wc-<?php echo esc_attr($this->id); ?>-cc-form" class='rtcl-credit-card-form rtcl-payment-form'>
            <?php do_action('rtcl_credit_card_form_start', $this->id); ?>
            <div class="form-group">
                <label for="<?php esc_attr($this->id) ?>-card-number"><?php esc_html_e('Card Number', 'classified-listing-pro') ?>
                    <span class="required">*</span></label>
                <input id="<?php esc_attr($this->id) ?>-card-number"
                       class="input-text rtcl-credit-card-number form-control" type="text" maxlength="20"
                       autocomplete="off"
                       placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" <?php echo $this->field_name('card-number') ?> />
            </div>
            <div class="form-row">
                <div class="col form-group">
                    <label for="<?php esc_attr($this->id) ?>-card-expiry"><?php esc_html_e('Expiry (MM/YY)', 'classified-listing-pro') ?>
                        <span class="required">*</span></label>
                    <input id="<?php esc_attr($this->id) ?>-card-expiry"
                           class="input-text rtcl-credit-card-expiry form-control" type="text" autocomplete="off"
                           placeholder="<?php esc_attr_e('MM / YY', 'classified-listing-pro') ?>" <?php echo $this->field_name('card-expiry') ?> />
                </div>
                <div class="col form-group">
                    <label for="<?php esc_attr($this->id) ?>-card-cvc"><?php esc_html_e('Card Code', 'classified-listing-pro') ?>
                        <span class="required">*</span></label>
                    <input id="<?php esc_attr($this->id) ?>-card-cvc"
                           class="input-text rtcl-credit-card-cvc form-control" type="text" autocomplete="off"
                           placeholder="<?php esc_attr_e('CVC', 'classified-listing-pro') ?>" <?php echo $this->field_name('card-cvc') ?> />
                </div>
            </div>
            <?php do_action('rtcl_credit_card_form_end', $this->id); ?>
            <div class="clear"></div>
        </fieldset>
        <?php
        return ob_get_clean();
    }

    /**
     * @param Payment $order
     * @param array   $data
     *
     * @return array
     */
    public function process_payment($order, $data = []) {
        $message = null;
        $result = 'error';
        $redirect = null;
        if (!$order instanceof Payment) {
            return [
                'result'   => $result,
                'message'  => esc_html__('Payment not found', 'classified-listing-pro'),
                'redirect' => $redirect,
            ];
        }
        $log = new Logger();
        $log->info("redirect url" . $redirect . " " . $order->get_listing_id());
        $card_num = '';
        if (!empty($data['number'])) {
            $card_num = sanitize_text_field(str_replace(' ', '', $data['number']));
        } else if (!empty($_POST['authorizenet-card-number'])) {
            $card_num = sanitize_text_field(str_replace(' ', '', $_POST['authorizenet-card-number']));
        }
        $cardtype = $this->get_card_type($card_num);

        if (!in_array($cardtype, $this->authorizenet_cardtypes)) {
            $log->info('Merchant do not support accepting in ' . $cardtype);
            $message = sprintf(esc_html__('Merchant do not support accepting in %s', 'classified-listing-pro'), $cardtype);

            return [
                'result'   => $result,
                'message'  => $message,
                'redirect' => $redirect,
            ];
        }

        $exp_month = $exp_year = null;
        if (isset($_POST['authorizenet-card-expiry'])) {
            $exp_date = explode("/", sanitize_text_field($_POST['authorizenet-card-expiry']));
            $exp_month = str_replace(' ', '', !empty($exp_date[0]) ? $exp_date[0] : null);
            $exp_year = str_replace(' ', '', !empty($exp_date[1]) ? $exp_date[1] : null);
        }

        $exp_year = !empty($data['exp_year']) ? sanitize_text_field($data['exp_year']) : $exp_year;
        $exp_month = !empty($data['exp_month']) ? sanitize_text_field($data['exp_month']) : $exp_month;
        if ($exp_year && strlen($exp_year) == 2) {
            $exp_year += 2000;
        }
        $cvc = !empty($data['cvc']) ? sanitize_text_field($data['cvc']) : (isset($_POST['authorizenet-card-cvc']) ? sanitize_text_field($_POST['authorizenet-card-cvc']) : '');

        if (!$exp_month || !$exp_year || !$cvc) {
            return [
                'result'   => $result,
                'message'  => esc_html__('Card expired month or year / CVC is not defined.', 'classified-listing-pro'),
                'redirect' => $redirect,
            ];
        }

        $sale = new AuthorizeNetAIM;
        $sale->amount = $order->get_total();
        $sale->card_num = $card_num;
        $sale->exp_date = $exp_year . '/' . $exp_month;
        $sale->card_code = $cvc;
        $sale->addLineItem($order);


        if ('yes' == AUTHORIZENET_TRANSACTION_MODE) {
            $response = $sale->authorizeOnly();
        } else {
            $response = $sale->authorizeAndCapture();
        }


        if ($response) {

            if ((1 == $response->approved) || (1 == $response->held)) {

                $log->info($response->response_reason_text . ' on ' . date("d-M-Y h:i:s e") . ' with Transaction ID = ' . $response->transaction_id . ' using ' . strtoupper($response->transaction_type) . ' and authorization code ' . $response->authorization_code);
                $order->payment_complete(Functions::clean($response->transaction_id));
                $transactionmetas = array(
                    'approved'             => $response->approved,
                    'declined'             => $response->declined,
                    'error'                => $response->error,
                    'held'                 => $response->held,
                    'response_code'        => $response->response_code,
                    'response_subcode'     => $response->response_subcode,
                    'response_reason_code' => $response->response_reason_code,
                    'authorization_code'   => $response->authorization_code,
                    'card_type'            => $response->card_type,
                    'transaction_type'     => $response->transaction_type,
                    'account_number'       => $response->account_number,
                    'cavv_response'        => $response->cavv_response,
                    'card_code_response'   => $response->card_code_response
                );

                add_post_meta($order->get_id(), '_' . $order->get_id() . '_' . $response->transaction_id . '_metas',
                    $transactionmetas);

                if ('yes' == $this->authorizenet_meta_cartspan) {
                    $authorizenet_metas_for_cartspan = [
                        'cc_type'     => $response->card_type,
                        'cc_last4'    => $response->account_number,
                        'cc_trans_id' => $response->transaction_id
                    ];
                    add_post_meta($order->get_id(), '_authorizenet_metas_for_cartspan', $authorizenet_metas_for_cartspan);
                }

                if (1 == $response->approved && "auth_capture" == $response->transaction_type) {
                    add_post_meta($order->get_id(), '_authorizenet_charge_status', 'charge_auth_captured');
                }

                if (1 == $response->approved && "auth_only" == $response->transaction_type) {
                    add_post_meta($order->get_id(), '_authorizenet_charge_status', 'charge_auth_only');
                }

                if (1 == $response->held) {
                    add_post_meta($order->get_id(), '_authorizenet_charge_status', 'charge_auth_only');
                }
                $result = 'success';
                $redirect = $this->get_return_url($order);
            } else {
                $message = $response->response_reason_text;
                $log->info($response->response_reason_text . '---' . $response->error_message . ' on ' . date("d-M-Y h:i:s e") . ' using ' . strtoupper($response->transaction_type));
            }


        } else {
            $log->info($response->response_reason_text . '---' . $response->error_message . ' on ' . date("d-M-Y h:i:s e") . ' using ' . strtoupper($response->transaction_type));
            $message = $response->response_reason_text;
        }

        return [
            'result'   => $result,
            'message'  => $message,
            'redirect' => $redirect,
        ];
    }

    /**
     * Get Icon
     */
    public function get_icon() {
        $icon = '';
        if (is_array($this->authorizenet_cardtypes)) {
            foreach ($this->authorizenet_cardtypes as $card_type) {

                if ($url = $this->get_payment_method_image_url($card_type)) {

                    $icon .= '<img width="45" src="' . esc_url($url) . '" alt="' . esc_attr(strtolower($card_type)) . '" />';
                }
            }
        } else {
            $icon .= '<img src="' . esc_url(plugins_url('images/authorizenet.png', __FILE__)) . '" alt="Authorize.Net Payment Gateway" />';
        }

        return apply_filters('rtcl_authorizenet_icon', $icon, $this->id);
    }

    public function get_payment_method_image_url($type) {

        $image_type = strtolower($type);

        return plugins_url('images/' . $image_type . '.png', __FILE__);
    }

    /**
     * Get Icon
     */
    public function load_stripe_scripts() {
        wp_enqueue_script('rtcl-credit-card-form');
    }

    /**
     * Get Card Types
     */
    function get_card_type($number) {

        $number = preg_replace('/[^\d]/', '', $number);
        if (preg_match('/^3[47][0-9]{13}$/', $number)) {
            return 'amex';
        } elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $number)) {
            return 'dinersclub';
        } elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number)) {
            return 'discover';
        } elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/', $number)) {
            return 'jcb';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $number)) {
            return 'mastercard';
        } elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) {
            return 'visa';
        } else {
            return 'unknown card';
        }
    }

    /**
     * Function to check IP
     *
     * @return array|false|string
     */
    function get_client_ip() {
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = '0.0.0.0';
        }

        return $ipaddress;
    }

}