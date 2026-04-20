<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use A17\Twill\Http\Controllers\Admin\UserController as TwillUserController;
use App\Http\Controllers\Twill\UserController as CMSUserController;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(TwillUserController::class, CMSUserController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
         View::composer('twill::*', function ($view) {
            if (!config()->has('twill-navigation-set')) {
                config(['twill-navigation' => adminMenuHelper()]);
                config(['twill-navigation-set' => true]);
            }
        });
    }
}
