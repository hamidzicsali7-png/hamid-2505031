<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        View::composer('layouts.dashboard', function (ViewInstance $view): void {
            $user = auth()->user();
            $notifikasi = $user?->unreadNotifications()
                ->latest()
                ->limit(5)
                ->get() ?? collect();

            $view->with([
                'notifikasiBelumDibaca' => $notifikasi,
                'jumlahNotifikasiBelumDibaca' => $user?->unreadNotifications()->count() ?? 0,
            ]);
        });
    }
}
