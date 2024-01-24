<?php

namespace App\Providers;

use App\Models\SettingWebsite;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
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
        // $this->app->bind('path.public', function() {
        //     return realpath(base_path().'/../admin.iorsel.com');
        // });
        
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $appSettings = SettingWebsite::first();

        Config::set('app.name', $appSettings->app_name);
        Config::set('app.logo', $appSettings->logo);
        // Config::set('app.name', $appSettings->app_name);
    }
}
