<?php

namespace App\Providers;

use App\Http\View\Composers\PublicMenuComposer;
use App\Http\View\Composers\WebsiteSettingComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        $this->configureRateLimiting();

        View::composer('public.*', PublicMenuComposer::class);
        View::composer(['public.*', 'admin.*', 'layouts.auth', 'auth.*'], WebsiteSettingComposer::class);

        Paginator::defaultView('vendor.pagination.optilux');
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('public-form', function (Request $request) {
            return Limit::perMinute(8)->by($request->ip());
        });

        // Feedback pengaduan lebih ketat: pelapor sah hanya butuh 1 pesan
        // per giliran (menunggu jawaban admin), sisanya adalah spam/probing.
        RateLimiter::for('pengaduan-feedback', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
