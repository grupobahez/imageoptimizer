<?php

namespace Grupobahez\Imageoptimizer\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Grupobahez\Imageoptimizer\ImageOptimizerServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ImageOptimizerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filesystems.disks.test', [
            'driver' => 'local',
            'root' => __DIR__ . '/temp',
        ]);
    }
}
