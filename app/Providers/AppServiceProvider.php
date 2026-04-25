<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate manage-product: hanya admin yang bisa akses menu/halaman produk
        Gate::define('manage-product', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate export-product: hanya admin yang bisa export data produk
        Gate::define('export-product', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate manage-category: hanya admin yang bisa akses menu/halaman kategori
        Gate::define('manage-category', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
