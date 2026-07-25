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
        // ── Ensure database exists (pull from GitHub if on serverless) ──
        if (app()->environment('production')) {
            try {
                $this->app->make(\App\Services\GitHubDatabaseService::class)->ensureDatabase();
            } catch (\Throwable $e) {
                Log::warning('GitHubDatabaseService: boot pull skipped', ['error' => $e->getMessage()]);
            }

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

        // ── On local/staging, also try to sync but don't fail ──
        if (app()->environment('local')) {
            try {
                $this->app->make(\App\Services\GitHubDatabaseService::class)->ensureDatabase();
            } catch (\Throwable $e) {
                // Silently skip on local
            }
        }
    }

}
