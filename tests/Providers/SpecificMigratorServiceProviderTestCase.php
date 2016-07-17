<?php

use IGonics\Migrations\Console\Commands\DBMigrateSpecific;
use IGonics\Migrations\Console\Commands\DBRollbackSpecific;
use IGonics\Migrations\Factories\MigratorFactory;
use IGonics\Migrations\Providers\SpecificMigratorServiceProvider;

class SpecificMigratorServiceProviderTestCase extends Orchestra\Testbench\TestCase
{

    public function setUp()
    {
        parent::setUp();
        // $this->artisan('migrate', [
        //     '--database' => 'testing',
        //     '--realpath' => (__DIR__.'/../../src/database/migrations'),
        // ]);
    }





    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function getPackageProviders($app)
    {
        return [
             SpecificMigratorServiceProvider::class,
        ];
    }
}