<?php

namespace RtclPro\Gateways\Stripe\lib;

use Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;
use stdClass;

class StripePaymentGateway extends PaymentGateway
{


    /**
     * Get payment source. This can be a new token/source or existing WC token.
     * If user is logged in and/or has WC account, create an account on Stripe.
     * This way we can attribute the payment to the user to better fight fraud.
     *
     * @param integer $user_id
     * @param bool    $force_save_source Should we force save payment source.
     *
     * @return object
     * @throws Exception When card was not added or for and invalid card.
     */
    public function prepare_source($user_id, $force_save_source = false, $existing_customer_id = null) {
        $customer = new StripeCustomer($user_id);
        if (!empty($existing_customer_id)) {
            $customer->set_id($existing_customer_id);
        }

        $force_save_source = apply_filters('rtcl_stripe_force_save_source', $force_save_source, $customer);
        $source_object = '';
        $source_id = '';
        $payment_method = isset($_POST['payment_method']) ? Functions::clean(wp_unslash($_POST['payment_method'])) : 'stripe';

        // New CC info was entered, and we have a new source to process.
        if (!empty($_POST['stripe_source'])) {
            $source_object = self::get_source_object(Functions::clean(wp_unslash($_POST['stripe_source'])));
            $source_id = $source_object->id;

            // This checks to see if customer opted to save the payment method to file.
            $maybe_saved_card = isset($_POST['rtcl-' . $payment_method . '-new-payment-method']) && !empty($_POST['rtcl-' . $payment_method . '-new-payment-method']);

            /**
             * This is true if the user wants to store the card to their account.
             * Criteria to save to file is they are logged in, they opted to save or product requirements and the source is
             * actually reusable. Either that or force_save_source is true.
             */
            if (($user_id && $this->saved_cards && $maybe_saved_card && 'reusable' === $source_object->usage) || $force_save_source) {
                $response = $customer->attach_source($source_object->id);

                if (!empty($response->error)) {
                    throw new StripeException(print_r($response, true), $this->get_localized_error_message_from_response($response));
                }
                if (is_wp_error($response)) {
                    throw new StripeException($response->get_error_message(), $response->get_error_message());
                }
            }
        }

        $customer_id = $customer->get_id();
        if (!$customer_id) {
            $customer->set_id($customer->create_customer());
            $customer_id = $customer->get_id();
        } else {
            $customer_id = $customer->update_customer();
        }

        if (empty($source_object)) {
            $source_object = self::get_source_object($source_id);
        }

        return (object)[
            'customer'      => $customer_id,
            'source'        => $source_id,
            'source_object' => $source_object,
        ];
    }


    /**
     * Get payment source. This can be a new token/source or existing WC token.
     * If user is logged in and/or has WC account, create an account on Stripe.
     * This way we can attribute the payment to the user to better fight fraud.
     *
     * @param integer $user_id
     * @param bool    $force_save_source Should we force save payment source.
     * @param null    $existing_customer_id
     * @param array   $extraData
     *
     * @return object
     * @throws StripeException
     */
    public function prepare_payment_method($user_id, $force_save_source = false, $existing_customer_id = null, $extraData = []) {
        $customer = new StripeCustomer($user_id);
        if (!empty($existing_customer_id)) {
            $customer->set_id($existing_customer_id);
        }

        $force_save_source = apply_filters('rtcl_stripe_force_save_source', $force_save_source, $customer);
        $pm_object = '';
        $pm_id = '';
        $payment_method = !empty($extraData['payment_method']) ? Functions::clean(wp_unslash($extraData['payment_method'])) : (isset($_POST['payment_method']) ? Functions::clean(wp_unslash($_POST['payment_method'])) : 'stripe');
        $paymentMethodId = !empty($extraData['stripe_payment_method']) ? Functions::clean(wp_unslash($extraData['stripe_payment_method'])) : (isset($_POST['stripe_payment_method']) ? Functions::clean(wp_unslash($_POST['stripe_payment_method'])) : '');

        // New CC info was entered, and we have a new source to process.
        if (!empty($paymentMethodId)) {
            $pm_object = self::get_pm_object($paymentMethodId);
            $pm_id = $pm_object->id;

            // This checks to see if customer opted to save the payment method to file.
            $maybe_saved_card = isset($_POST['rtcl-' . $payment_method . '-new-payment-method']) && !empty($_POST['rtcl-' . $payment_method . '-new-payment-method']);

            /**
             * This is true if the user wants to store the card to their account.
             * Criteria to save to file is they are logged in, they opted to save or product requirements and the source is
             * actually reusable. Either that or force_save_source is true.
             */
            if (($user_id && $this->saved_cards && $maybe_saved_card && 'reusable' === $pm_object->usage) || $force_save_source) {
                $response = $customer->attach_payment_method($pm_object->id);

                if (!empty($response->error)) {
                    throw new StripeException(print_r($response, true), $this->get_localized_error_message_from_response($response));
                }
                if (is_wp_error($response)) {
                    throw new StripeException($response->get_error_message(), $response->get_error_message());
                }
            }
        }

        $customer_id = $customer->get_id();
        if (!$customer_id) {
            $customer->set_id($customer->create_customer());
            $customer_id = $customer->get_id();
        } else {
            $customer_id = $customer->update_customer();
        }

        if (empty($pm_object)) {
            $pm_object = self::get_pm_object($pm_id);
        }

        return (object)[
            'customer'  => $customer_id,
            'pm_id'     => $pm_id,
            'pm_object' => $pm_object,
        ];
    }


    /**
     * Get payment source from an order. This could be used in the future for
     * a subscription as an example, therefore using the current user ID would
     * not work - the customer won't be logged in :)
     *
     * Not using 2.6 tokens for this part since we need a customer AND a card
     * token, and not just one.
     *
     * @param Payment $order
     *
     * @return object
     * @since   3.1.0
     * @version 4.0.0
     */
    public function prepare_order_pm_obj($order = null) {
        $stripe_customer = new StripeCustomer();
        $stripe_source = false;
        $source_object = false;

        if ($order) {

            $stripe_customer_id = $this->get_stripe_customer_id($order);

            if ($stripe_customer_id) {
                $stripe_customer->set_id($stripe_customer_id);
            }

            $source_id = get_post_meta($order->get_id(), '_stripe_source_id', true);

            // Since 4.0.0, we changed card to source so we need to account for that.
            if (empty($source_id)) {
                $source_id = get_post_meta($order->get_id(), '_stripe_card_id', true);

                // Take this opportunity to update the key name.
                update_post_meta($order->get_id(), '_stripe_source_id', $source_id);
            }

            if ($source_id) {
                $stripe_source = $source_id;
                $source_object = StripeAPI::retrieve('sources/' . $source_id);
            } elseif (apply_filters('rtcl_stripe_use_default_customer_source', true)) {
                /*
                 * We can attempt to charge the customer's default source
                 * by sending empty source id.
                 */
                $stripe_source = '';
            }
        }

        return (object)[
            'customer'  => $stripe_customer ? $stripe_customer->get_id() : false,
            'pm_id'     => $stripe_source,
            'pm_object' => $source_object,
        ];
    }


    /**
     * Save source to order.
     *
     * @param Payment  $order  For to which the source applies.
     * @param stdClass $source Source information.
     *
     * @since   3.1.0
     * @version 4.0.0
     */
    public function save_source_to_order($order, $source) {
        // Store source in the order.
        if ($source->customer) {
            update_post_meta($order->get_id(), '_stripe_customer_id', $source->customer);
        }

        if ($source->source) {
            update_post_meta($order->get_id(), '_stripe_source_id', $source->source);
        }
    }


    /**
     * Save source to order.
     *
     * @param Payment  $order  For to which the payment method applies.
     * @param stdClass $pm_obj Source information.
     */
    public function save_pm_to_order($order, $pm_obj) {
        // Store source in the order.
        if (!empty($pm_obj->customer)) {
            update_post_meta($order->get_id(), '_stripe_customer_id', $pm_obj->customer);
        }

        if (!empty($pm_obj->source)) {
            update_post_meta($order->get_id(), '_stripe_pm_id', $pm_obj->source);
        }
    }


    /**
     * Get source object by source id.
     *
     * @param string $source_id The source ID to get source object for.
     *
     * @throws StripeException
     */
    public function get_source_object($source_id = '') {
        if (empty($source_id)) {
            return '';
        }

        $source_object = StripeAPI::retrieve('sources/' . $source_id);

        if (!empty($source_object->error)) {
            throw new StripeException(print_r($source_object, true), $source_object->error->message);
        }

        return $source_object;
    }

    /**
     * Get source object by source id.
     *
     * @param string $paymentMethodId The source ID to get source object for.
     *
     * @throws StripeException
     */
    public function get_pm_object($paymentMethodId = '') {
        if (empty($paymentMethodId)) {
            return '';
        }

        $pm_object = StripeAPI::retrieve('payment_methods/' . $paymentMethodId);

        if (!empty($pm_object->error)) {
            throw new StripeException(print_r($pm_object, true), $pm_object->error->message);
        }

        return $pm_object;
    }


    /**
     * Creates a SetupIntent for future payments, and saves it to the order.
     *
     * @param Payment $order           The ID of the (free/pre- order).
     * @param object  $prepared_source The source, entered/chosen by the customer.
     *
     * @return string                   The client secret of the intent, used for confirmation in JS.
     * @throws StripeException
     */
    public function setup_intent($order, $prepared_source) {
        $order_id = $order->get_id();
        $setup_intent = StripeAPI::request(
            [
                'payment_method' => $prepared_source->source,
                'customer'       => $prepared_source->customer,
                'confirm'        => 'true',
            ],
            'setup_intents'
        );

        if (is_wp_error($setup_intent)) {
            StripeLogger::log("Unable to create SetupIntent for Order #$order_id: " . print_r($setup_intent, true));
        } elseif ('requires_action' === $setup_intent->status) {
            update_post_meta($order->get_id(), '_stripe_setup_intent', $setup_intent->id);

            return $setup_intent->client_secret;
        }
    }


    /**
     * Validates that the order meets the minimum order amount
     * set by Stripe.
     *
     * @param object $order
     *
     * @throws StripeException=
     */
    public function validate_minimum_order_amount($order) {
        if ($order->get_total() * 100 < StripeHelper::get_minimum_amount()) {
            /* translators: 1) amount (including currency symbol) */
            throw new StripeException('Did not meet minimum amount', sprintf(__('Sorry, the minimum allowed order total is %1$s to use this payment method.', 'classified-listing-pro'), wc_price(WC_Stripe_Helper::get_minimum_amount() / 100)));
        }
    }


    /**
     * Gets the saved customer id if exists.
     *
     * @param Payment $order
     *
     * @return false|mixed
     * @since   4.0.0
     * @version 4.0.0
     */
    public function get_stripe_customer_id($order) {
        // Try to get it via the order first.
        $customer = get_post_meta($order->get_id(), '_stripe_customer_id', true);

        if (empty($customer)) {
            $customer = get_user_option('_stripe_customer_id', $order->get_customer_id());
        }

        return $customer;
    }

    /**
     * Retrieves the payment intent, associated with an order.
     *
     * @param Payment $order The order to retrieve an intent for.
     *
     * @return object|bool     Either the intent object or `false`.
     * @throws Exception
     * @since 4.2
     */
    public function get_intent_from_order($order) {
        $intent_id = get_post_meta($order->get_id(), '_stripe_intent_id', true);

        if ($intent_id) {
            return $this->get_intent('payment_intents', $intent_id);
        }

        // The order doesn't have a payment intent, but it may have a setup intent.
        $intent_id = get_post_meta($order->get_id(), '_stripe_setup_intent', true);

        if ($intent_id) {
            return $this->get_intent('setup_intents', $intent_id);
        }

        return false;
    }


    /**
     * Retrieves intent from Stripe API by intent id.
     *
     * @param string $intent_type Either 'payment_intents' or 'setup_intents'.
     * @param string $intent_id   Intent id.
     *
     * @return object|bool          Either the intent object or `false`.
     * @throws Exception|StripeException            Throws exception for unknown $intent_type.
     */
    private function get_intent($intent_type, $intent_id) {
        if (!in_array($intent_type, ['payment_intents', 'setup_intents'])) {
            throw new Exception("Failed to get intent of type $intent_type. Type is not allowed");
        }

        $response = StripeAPI::request([], "$intent_type/$intent_id", 'GET');

        if ($response && isset($response->{'error'})) {
            $error_response_message = print_r($response, true);
            StripeLogger::log("Failed to get Stripe intent $intent_type/$intent_id.");
            StripeLogger::log("Response: $error_response_message");

            return false;
        }

        return $response;
    }

    /**
     * Create the level 3 data array to send to Stripe when making a purchase.
     *
     * @param Payment $order The order that is being paid for.
     *
     * @return array          The level 3 data to send to Stripe.
     */
    public function get_level3_data_from_order($order) {
        $currency = $order->get_currency();
        $stripe_line_items = [
            'pricing_id'          => (string)$order->pricing->getId(),
            // Up to 12 characters that uniquely identify the product.
            'pricing_description' => substr($order->pricing->getDescription(), 0, 26),
            // Up to 26 characters long describing the product.
            'cost'                => StripeHelper::get_stripe_amount($order->get_total(), $currency),
            // Cost of the product, in cents, as a non-negative integer.
            'pricing_type'        => $order->pricing->getType()
        ];

        return [
            'merchant_reference' => $order->get_id(),
            // An alphanumeric string of up to  characters in length. This unique value is assigned by the merchant to identify the order. Also known as an “Order ID”.
            'line_items'         => $stripe_line_items,
        ];
    }

    /**
     * Create a new PaymentIntent.
     *
     * @param Payment $order        The order that is being paid for.
     * @param object  $prepared_obj The source that is used for the payment.
     *
     * @return object                   An intent or an error.
     * @throws StripeException
     */
    public function create_intent($order, $prepared_obj) {
        $request = $this->generate_create_intent_request($order, $prepared_obj);
        // Create an intent that awaits an action.
        $intent = StripeAPI::request($request, 'payment_intents');
        if (!empty($intent->error)) {
            return $intent;
        }

        $order_id = $order->get_id();
        StripeLogger::log("Stripe PaymentIntent $intent->id initiated for order $order_id");

        // Save the intent ID to the order.
        $this->save_intent_to_order($order, $intent);

        return $intent;
    }

    /**
     * Saves intent to order.
     *
     * @param Payment  $order  For to which the source applies.
     * @param stdClass $intent Payment intent information.
     *
     * @since 3.2.0
     */
    public function save_intent_to_order($order, $intent) {
        update_post_meta($order->get_id(), '_stripe_intent_id', $intent->id);
    }


    /**
     * Generates the request when creating a new payment intent.
     *
     * @param Payment $order        The order that is being paid for.
     * @param object  $prepared_obj The source that is used for the payment.
     *
     * @return array                    The arguments for the request.
     */
    public function generate_create_intent_request($order, $prepared_obj) {
        // The request for a charge contains metadata for the intent.
        $full_request = $this->generate_payment_request($order, $prepared_obj);

        $request = [
            'amount'               => StripeHelper::get_stripe_amount($order->get_total()),
            'currency'             => strtolower($order->get_currency()),
            'description'          => $full_request['description'],
            'metadata'             => $full_request['metadata'],
            'capture_method'       => ('true' === $full_request['capture']) ? 'automatic' : 'manual',
            'payment_method_types' => [
                'card',
            ],
        ];
        if (!empty($prepared_obj->pm_id)) {
            $request['payment_method'] = $prepared_obj->pm_id;
        } elseif (!empty($prepared_obj->source)) {
            $request['source'] = $prepared_obj->source;
        }

        $force_save_pm = apply_filters('rtcl_stripe_force_save_payment_method', false, $prepared_obj->pm_id);

        if ($this->save_payment_method_requested() || $force_save_pm) {
            $request['setup_future_usage'] = 'off_session';
            $request['metadata']['save_payment_method'] = 'true';
        }

        if ($prepared_obj->customer) {
            $request['customer'] = $prepared_obj->customer;
        }

        if (isset($full_request['statement_descriptor'])) {
            $request['statement_descriptor'] = $full_request['statement_descriptor'];
        }

        if (isset($full_request['payment_method_options']['card']['request_three_d_secure'])) {
            $request['payment_method_options']['card']['request_three_d_secure'] = $full_request['payment_method_options']['card']['request_three_d_secure'];
        }

        if (isset($full_request['receipt_email'])) {
            $request['receipt_email'] = $full_request['receipt_email'];
        }

        /**
         *
         * @param array   $request
         * @param Payment $order
         * @param object  $source
         *
         * @since 3.1.0
         */
        return apply_filters('rtcl_stripe_generate_create_intent_request', $request, $order, $prepared_obj);
    }


    /**
     * Generate the request for the payment.
     *
     * @param Payment $order
     * @param object  $prepared_source
     *
     * @return array
     */
    public function generate_payment_request($order, $prepared_source) {
        $settings = get_option('rtcl_payment_stripe', []);
        $statement_descriptor = !empty($settings['statement_descriptor']) ? str_replace("'", '', $settings['statement_descriptor']) : '';
        $capture = !empty($settings['capture']) && 'yes' === $settings['capture'];
        $post_data = [
            'currency' => $order->get_currency(),
            'amount'   => StripeHelper::get_stripe_amount($order->get_total(), $order->get_currency())
        ];
        /* translators: 1) blog name 2) order number */
        $post_data['description'] = sprintf(__('%1$s - Order %2$s', 'classified-listing-pro'), wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES), $order->get_order_number());
        $billing_email = $order->get_customer_email();
        $billing_full_name = $order->get_customer_full_name();

        if ((!empty($settings['force_3d_secure']) && 'yes' === $settings['force_3d_secure']) || apply_filters('rtcl_stripe_force_3d_secure', false)) {
            $post_data['payment_method_options']['card']['request_three_d_secure'] = 'any';
        }

        if (!empty($billing_email) && apply_filters('rtcl_stripe_send_stripe_receipt', false)) {
            $post_data['receipt_email'] = $billing_email;
        }

        if (!empty($statement_descriptor)) {
            $post_data['statement_descriptor'] = StripeHelper::clean_statement_descriptor($statement_descriptor);
        }
        if ('stripe' === $order->get_payment_method()) {
            $post_data['capture'] = $capture ? 'true' : 'false';
        }

        $post_data['expand[]'] = 'balance_transaction';

        $metadata = [
            __('customer_name', 'classified-listing-pro')  => sanitize_text_field($billing_full_name),
            __('customer_email', 'classified-listing-pro') => sanitize_email($billing_email),
            'site_url'                                     => esc_url(get_site_url()),
        ];

        $post_data['metadata'] = apply_filters('rtcl_stripe_payment_metadata', $metadata, $order, $prepared_source);
        $metadata['metadata']['rtcl_order_id'] = $order->get_order_number();
        if ($prepared_source->customer) {
            $post_data['customer'] = $prepared_source->customer;
        }

        if (!empty($prepared_source->source)) {
            $post_data['source'] = $prepared_source->source;
        }

        /**
         * Filter the return value of the
         *
         * @param array   $post_data
         * @param Payment $order
         * @param object  $source
         *
         * @since 3.1.0
         */
        return apply_filters('rtcl_stripe_generate_payment_request', $post_data, $order, $prepared_source);
    }

    /**
     *
     * Store extra metadata for an order from a Stripe Response.
     *
     * @param         $response
     * @param Payment $order
     *
     * @return mixed
     * @throws StripeException
     */
    public function process_response($response, $order) {
        StripeLogger::log('Processing response: ' . print_r($response, true));

        $captured = (isset($response->captured) && $response->captured) ? 'yes' : 'no';

        // Store charge data.
        $order->update_meta('_stripe_charge_captured', $captured);

        if (isset($response->balance_transaction)) {
            $this->update_fees($order, is_string($response->balance_transaction) ? $response->balance_transaction : $response->balance_transaction->id);
        }

        if ('yes' === $captured) {
            /**
             * Charge can be captured but in a pending state. Payment methods
             * that are asynchronous may take couple days to clear. Webhook will
             * take care of the status changes.
             */
            if ('pending' === $response->status) {

                $order->set_transaction_id($response->id);
                /* translators: transaction id */
                $order->update_status('on-hold', sprintf(__('Stripe charge awaiting payment: %s.', 'classified-listing-pro'), $response->id));
            }

            if ('succeeded' === $response->status) {
                $order->payment_complete($response->id);
                delete_post_meta($order->get_id(), '_stripe_requires_action');
                /* translators: transaction id */
                $message = sprintf(__('Stripe charge complete (Charge ID: %s)', 'classified-listing-pro'), $response->id);
                $order->add_note($message);
            }

            if ('failed' === $response->status) {
                $localized_message = __('Payment processing failed. Please retry.', 'classified-listing-pro');

                $order->add_note($localized_message);
                throw new StripeException(print_r($response, true), $localized_message);
            }
        } else {
            $order->set_transaction_id($response->id);

            /* translators: transaction id */
            $order->update_status('on-hold', sprintf(__('Stripe charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization. Attempting to refund the order in part or in full will release the authorization and cancel the payment.', 'classified-listing-pro'), $response->id));
        }

        do_action('rtcl_gateway_stripe_process_response', $response, $order);

        return $response;
    }


    /**
     * Updates Stripe fees/net.
     * e.g usage would be after a refund.
     *
     * @param object $order The order object
     * @param int    $balance_transaction_id
     *
     * @since   4.0.0
     * @version 4.0.6
     */
    public function update_fees($order, $balance_transaction_id) {
        $balance_transaction = StripeAPI::retrieve('balance/history/' . $balance_transaction_id);

        if (empty($balance_transaction->error)) {
            if (isset($balance_transaction) && isset($balance_transaction->fee)) {
                // Fees and Net needs to both come from Stripe to be accurate as the returned
                // values are in the local currency of the Stripe account, not from WC.
                $fee_refund = !empty($balance_transaction->fee) ? StripeHelper::format_balance_fee($balance_transaction) : 0;
                $net_refund = !empty($balance_transaction->net) ? StripeHelper::format_balance_fee($balance_transaction, 'net') : 0;

                // Current data fee & net.
                $fee_current = StripeHelper::get_stripe_fee($order);
                $net_current = StripeHelper::get_stripe_net($order);

                // Calculation.
                $fee = (float)$fee_current + (float)$fee_refund;
                $net = (float)$net_current + (float)$net_refund;

                StripeHelper::update_stripe_fee($order, $fee);
                StripeHelper::update_stripe_net($order, $net);

                $currency = !empty($balance_transaction->currency) ? strtoupper($balance_transaction->currency) : null;
                StripeHelper::update_stripe_currency($order, $currency);
            }
        } else {
            StripeLogger::log('Unable to update fees/net meta for order: ' . $order->get_id());
        }
    }


    /**
     * @return bool
     */
    public function save_payment_method_requested() {
        $payment_method = isset($_POST['payment_method']) ? Functions::clean(wp_unslash($_POST['payment_method'])) : 'stripe';

        return isset($_POST['rtcl-' . $payment_method . '-new-payment-method']) && !empty($_POST['rtcl-' . $payment_method . '-new-payment-method']);
    }


    /**
     * Locks an order for payment intent processing for 5 minutes.
     *
     * @param Payment  $order  The order that is being paid.
     * @param stdClass $intent The intent that is being processed.
     *
     * @return bool            A flag that indicates whether the order is already locked.
     * @since 4.2
     */
    public function lock_order_payment($order, $intent = null) {
        $order_id = $order->get_id();
        $transient_name = 'rtcl_stripe_processing_intent_' . $order_id;
        $processing = get_transient($transient_name);

        // Block the process if the same intent is already being handled.
        if ('-1' === $processing || (isset($intent->id) && $processing === $intent->id)) {
            return true;
        }

        // Save the new intent as a transient, eventually overwriting another one.
        set_transient($transient_name, empty($intent) ? '-1' : $intent->id, 5 * MINUTE_IN_SECONDS);

        return false;
    }

    /**
     * Unlocks an order for processing by payment intents.
     *
     * @param Payment $order The order that is being unlocked.
     *
     * @since 4.2
     */
    public function unlock_order_payment($order) {
        $order_id = $order->get_id();
        delete_transient('rtcl_stripe_processing_intent_' . $order_id);
    }


    /**
     * Updates an existing intent with updated amount, source, and customer.
     *
     * @param object  $intent          The existing intent object.
     * @param Payment $order           The order.
     * @param object  $prepared_source Currently selected source.
     *
     * @return object                   An updated intent.
     * @throws StripeException
     */
    public function update_existing_intent($intent, $order, $prepared_source) {
        $request = [];

        if ($prepared_source->source !== $intent->source) {
            $request['source'] = $prepared_source->source;
        }

        $new_amount = StripeHelper::get_stripe_amount($order->get_total());
        if ($intent->amount !== $new_amount) {
            $request['amount'] = $new_amount;
        }

        if ($prepared_source->customer && $intent->customer !== $prepared_source->customer) {
            $request['customer'] = $prepared_source->customer;
        }

        if (empty($request)) {
            return $intent;
        }

        $level3_data = $this->get_level3_data_from_order($order);

        return StripeAPI::request_with_level3_data(
            $request,
            "payment_intents/$intent->id",
            $level3_data,
            $order
        );
    }

    /**
     * Confirms an intent if it is the `requires_confirmation` state.
     *
     * @param object  $intent          The intent to confirm.
     * @param Payment $order           The order that the intent is associated with.
     * @param object  $prepared_source The source that is being charged.
     *
     * @return object                   Either an error or the updated intent.
     * @throws StripeException
     * @since 4.2.1
     */
    public function confirm_intent($intent, $order, $prepared_source) {
        if ('requires_confirmation' !== $intent->status) {
            return $intent;
        }

        // Try to confirm the intent & capture the charge (if 3DS is not required).
        $confirm_request = [
            'payment_method' => $prepared_source->pm_id,
        ];

        $level3_data = $this->get_level3_data_from_order($order);
        $confirmed_intent = StripeAPI::request_with_level3_data(
            $confirm_request,
            "payment_intents/$intent->id/confirm",
            $level3_data,
            $order
        );

        if (!empty($confirmed_intent->error)) {
            return $confirmed_intent;
        }

        // Save a note about the status of the intent.
        $order_id = $order->get_id();
        if ('succeeded' === $confirmed_intent->status) {
            StripeLogger::log("Stripe PaymentIntent $intent->id succeeded for order $order_id");
        } elseif ('requires_action' === $confirmed_intent->status) {
            StripeLogger::log("Stripe PaymentIntent $intent->id requires authentication for order $order_id");
        }

        return $confirmed_intent;
    }


    /**
     * Checks to see if request is invalid and that
     * they are worth retrying.
     *
     * @param object $error
     *
     * @since 4.0.5
     */
    public function is_retryable_error($error) {
        return (
            'invalid_request_error' === $error->type ||
            'idempotency_error' === $error->type ||
            'rate_limit_error' === $error->type ||
            'api_connection_error' === $error->type ||
            'api_error' === $error->type
        );
    }


    /**
     * Retries the payment process once an error occured.
     *
     * @param object  $response The response from the Stripe API.
     * @param Payment $order    An order that is being paid for.
     * @param array   $data
     *                          bool    $retry             A flag that indicates whether another retry should be attempted.
     *                          bool    $force_save_source Force save the payment source.
     *                          mixed   $previous_error    Any error message from previous request.
     *                          bool    $use_order_source  Whether to use the source, which should already be attached to the order.
     *
     * @return array
     * @throws StripeException        If the payment is not accepted.
     * @since 4.2.0
     */
    public function retry_after_error($response, $order, $data) {
        if (!empty($data['retry'])) {
            $localized_message = __('Sorry, we are unable to process your payment at this time. Please retry later.', 'classified-listing-pro');
            $order->add_note($localized_message);
            throw new StripeException(print_r($response, true), $localized_message); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.
        }

        // Don't do anymore retries after this.
        if (5 <= $this->retry_interval) {
            $data['retry'] = false;

            return $this->process_payment($order, $data);
        }

        sleep($this->retry_interval);
        $this->retry_interval++;
        $data['retry'] = true;

        return $this->process_payment($order, $data);
    }


    /**
     * Checks if payment is via saved payment source.
     *
     * @return bool
     */
    public function is_using_saved_payment_method() {
        $payment_method = isset($_POST['payment_method']) ? Functions::clean(wp_unslash($_POST['payment_method'])) : 'stripe';

        return (isset($_POST['rtcl-' . $payment_method . '-payment-token']) && 'new' !== $_POST['rtcl-' . $payment_method . '-payment-token']);
    }


    /**
     * Checks to see if error is of invalid request
     * error, and it is no such customer.
     *
     * @param array $error
     */
    public function is_no_such_customer_error($error) {
        return (
            $error &&
            'invalid_request_error' === $error->type &&
            preg_match('/No such customer/i', $error->message)
        );
    }


    /**
     * Checks if card is a prepaid card.
     *
     * @param object $source_object
     *
     * @return bool
     */
    public function is_prepaid_card($source_object) {
        return (
            $source_object
            && ('token' === $source_object->object || 'source' === $source_object->object)
            && 'prepaid' === $source_object->card->funding
        );
    }


    /**
     * Sends the failed order email to admin.
     *
     * @param Payment $order
     *
     * @return void
     */
    public function send_failed_order_email($order) {
        // TODO : need to add order failed mail
//		$emails = rtcl()->mailer();
//		if ( ! empty( $emails ) && ! empty( $order_id ) ) {
//
//		}
    }
}
