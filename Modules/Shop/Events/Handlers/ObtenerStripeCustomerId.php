<?php

namespace Modules\Shop\Events\Handlers;

use Illuminate\Support\Facades\Log;
use Modules\User\Events\UserHasActivatedAccount;
use Stripe\Customer;
use Stripe\Stripe;

class ObtenerStripeCustomerId
{
    public function handle(UserHasActivatedAccount $event)
    {
        Log::info('Estsamos en ObtenerStripeCustomerID@handle');

        $user = $event->user;

        Stripe::setApiKey(env('STRIPE_SK'));

        // Create a new Stripe customer
        $customer = Customer::create([
            'email' => $user->email,
            'metadata' => [
                "First Name" => $user->first_name,
                "Last Name" => $user->last_name
            ]
        ]);

        $user->stripe_customer_id = $customer->id;
        $user->save();
    }
}