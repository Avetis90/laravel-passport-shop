<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Cart\Payments\Gateway;
use App\Cart\Payments\Gateways\StripeGateway;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Cart::class, function ($app) {
            if ($app->auth->user()) {
                $app->auth->user()->load([
                    'cart.stock'
                ]);
            }

            return new Cart($app->auth->user());
        });

        $this->app->singleton(Gateway::class, function () {
            return new StripeGateway();
        });
    }
}
