<?php

namespace Haroun\DistanceMatrix;

use Illuminate\Support\ServiceProvider;

class DistanceMatrixServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/distance-matrix.php', 'distance-matrix'
        );

        $this->app->singleton('distance-matrix', function ($app) {
            return new DistanceMatrixManager($app);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/distance-matrix.php' => config_path('distance-matrix.php'),
            ], 'distance-matrix-config');
        }

        if (file_exists(__DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }
}
