<?php

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\StripeClient;

class CheckoutSession
{
    const PAYMENT_METHOD_BACS = 'bacs_debit';
    const PAYMENT_METHOD_CARD = 'card';
    const DEFAULT_PAYMENT_METHODS = [
        self::PAYMENT_METHOD_BACS,
        self::PAYMENT_METHOD_CARD,
    ];

    const MODE_SUBSCRIPTION = 'subscription';
    const MODE_PAYMENT = 'payment';

    /**
     * @var StripeClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $customer;

    /**
     * @var bool
     */
    protected $allowsPromotionCodes = false;

    /**
     * @param StripeClient $client
     */
    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @param bool $allowsPromotionCodes
     * @return $this
     */
    public function setAllowsPromotionCodes(bool $allows = true)
    {
        $this->allowsPromotionCodes = $allows;

        return $this;
    }

    /**
     * @param string $customer
     * @return $this
     */
    public function setCustomer(string $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @param array $params
     * @return Session
     */
    public function create(array $params = []): Session
    {
        return Session::create(array_merge($this->defaultParams(), $params));
    }

    /**
     * @return array
     */
    protected function defaultParams(): array
    {
        $params = [
            'payment_method_types' => self::DEFAULT_PAYMENT_METHODS,
            'allow_promotion_codes' => $this->allowsPromotionCodes,
            'mode' => $this->mode,
            'customer' => $this->customer,
        ];

        if ($this->mode === self::MODE_PAYMENT) {
            $params['payment_intent_data'] = [
                'setup_future_usage' => 'off_session',
            ];
        }

        return array_filter($params);
    }
}
