<?php

namespace App\Services;

use App\Models\User;
use Arr;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class Stripe
{
    protected $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function loadSession(string $session)
    {
        return $this->stripeClient->checkout->sessions->retrieve($session);
    }

    public function loadPaymentIntent(string $paymentIntent)
    {
        return $this->stripeClient->paymentIntents->retrieve($paymentIntent);
    }

    public function paymentIntentForSession(string $session)
    {
        return $this->loadPaymentIntent(
            $this->loadSession($session)->payment_intent
        );
    }

    public function loadPaymentMethod(string $method)
    {
        return $this->stripeClient->paymentMethods->retrieve($method);
    }

    /**
     * Load the customer object from Stripe based on the ID.
     *
     * @param None
     *
     * @return Object
     */
    public function loadCustomerById($id)
    {
        $customer = $this->stripeClient->customers->retrieve($id);

        return $customer;
    }

    public function loadCustomerByEmail(string $email): ?Customer
    {
        $customers = $this->stripeClient->customers->all([
            'email' => $email,
        ]);

        return Arr::first($customers->data);
    }

    public function findOrCreateCustomer(User $user): ?Customer
    {
        if (empty($user->email)) {
            return null;
        }

        if ($customer = $this->loadCustomerByEmail($user->email)) {
            return $customer;
        }

        return $this->stripeClient->customers->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * @param string $stripeProductId
     *
     * @return Collection
     * @throws ApiErrorException
     */
    public function getPricesForProduct(string $stripeProductId): Collection
    {
        return $this->stripeClient->prices->all([
            'product' => $stripeProductId,
        ]);
    }
}
