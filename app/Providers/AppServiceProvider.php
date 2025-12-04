<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL; // <--- Tambahkan ini

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
        // Fix untuk Error MySQL Key Length (yang sudah ada sebelumnya)
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // --- TAMBAHAN BARU: PAKSA HTTPS ---
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}