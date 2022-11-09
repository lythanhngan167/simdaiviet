<?php
namespace RtclPro\Gateways\Stripe\lib;

use Exception;

class StripeException extends Exception
{

    /**
     * String sanitized/localized error message.
     *
     * @var string
     */
    protected $localized_message;

    /**
     * Setup exception
     *
     * @param string $error_message     Full response
     * @param string $localized_message user-friendly translated error message
     *
     * @since 4.0.2
     */
    public function __construct($error_message = '', $localized_message = '') {
        $this->localized_message = $localized_message;
        parent::__construct($error_message);
    }

    /**
     * Returns the localized message.
     *
     * @return string
     * @since 4.0.2
     */
    public function getLocalizedMessage() {
        return $this->localized_message;
    }
}
