<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        view()->composer('app', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth.login', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('member.cetak', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('header', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('components.sidebar', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('components.layouts.admin', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('components.authentication-card-logo', function ($view) {
            $view->with('setting', Setting::first());
        });

        $this->app->singleton(ChartOfAccountService::class, function ($app) {
            return new ChartOfAccountService();
        });

        Relation::morphMap([
            'journal' => 'App\Models\Journal',
            'payment' => 'App\Models\Payment',
            'receipt' => 'App\Models\Receipt', 
            'invoice' => 'App\Models\Invoice',
            'purchase' => 'App\Models\Purchase',
            'pembelian' => 'App\Models\Pembelian',
            'penjualan' => 'App\Models\Penjualan',
            'payroll' => 'App\Models\Payroll',
            'inventory' => 'App\Models\Inventori'
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) {
            URL::forceScheme('https');
        }
        
        // Atau cara lebih sederhana jika di belakang Cloudflare
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('investor.layouts.app', function ($view) {
            $view->with('investor', Auth::guard('investor')->user());
        });

        // Register @hasPermission Blade directive
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });
    }
}
