<?php

namespace App\Providers;

use App\Services\Properties\Properties;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(Properties::class, function () {
            return (new Properties)->setAllProperties()->setZapProperties();
        });
    }
}
