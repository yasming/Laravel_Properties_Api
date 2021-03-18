<?php

namespace App\Providers;

use App\Services\Properties\PropertiesService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

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
        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate',
                function ($perPage = 20, $page = null, $options = []) {
                $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                return (new LengthAwarePaginator(
                    $this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))
                    ->withPath('');
            });
        }
    }
}
