<?php

namespace App\Providers;

use App\Services\Properties\PropertiesService;
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
        $this->app->bind(PropertiesService::class, function () {
            return (new PropertiesService)->setAllProperties()
                                          ->setZapProperties()
                                          ->setVivaRealProperties();
        });
    }
}
