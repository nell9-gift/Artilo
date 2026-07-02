<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {}

    public function boot(): void {
        // Injecte $settings dans TOUTES les vues automatiquement
        view()->composer('*', function ($view) {
           $settings = Setting::all()->pluck('value', 'key')->toArray();
            $view->with('settings', $settings);
        });
    }
}