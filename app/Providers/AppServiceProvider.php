<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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


    public function boot()
    {
        if (app()->environment('production')) {
            DB::listen(function ($query) {
                if ($query->time > 500) { // ms
                    Log::warning('Slow Query', [
                        'sql' => $query->sql,
                        'time_ms' => $query->time,
                        'bindings' => $query->bindings,
                    ]);
                }
            });
        }
    }

}
