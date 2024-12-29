<?php

namespace Grupobahez\Imageoptimizer;

use Illuminate\Support\ServiceProvider;

class ImageOptimizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/imageoptimizer.php' => config_path('imageoptimizer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/imageoptimizer.php', 'imageoptimizer'
        );

        $this->app->singleton('imageoptimizer', function ($app) {
            return new ImageOptimizerManager($app['config']->get('imageoptimizer'));
        });
    }
}
