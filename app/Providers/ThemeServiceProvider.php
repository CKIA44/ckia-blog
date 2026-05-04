<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * View composers in app/View/Composers/ are auto-discovered by Acorn.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
