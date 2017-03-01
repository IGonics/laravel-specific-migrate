<?php

namespace IGonics\Migrations\Providers;

use Illuminate\Support\ServiceProvider;
use IGonics\Migrations\Console\Commands\DBMigrateSpecific;
use IGonics\Migrations\Console\Commands\DBRollbackSpecific;
use IGonics\Migrations\Factories\MigratorFactory;
use IGonics\Migrations\Contracts\ISpecificFilesMigrator;

class SpecificMigratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerMigrator();
    }

    protected function getSpecificMigratorClass(){
        return MigratorFactory::SpecificMigratorClassName();
    }

    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerMigrator()
    {
        $specificFilesMigratorClass = $this->getSpecificMigratorClass();
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $instantiation = function ($app) use($specificFilesMigratorClass) {
            return new $specificFilesMigratorClass($app['migration.repository'], $app['db'], $app['files']);
        };
        $this->app->singleton($specificFilesMigratorClass, $instantiation);

        $this->app->singleton('SpecificFilesMigrator',$instantiation);

        $this->app->singleton(ISpecificFilesMigrator::class,$instantiation);
    }

    /**
     * Register the commands.
     */
    protected function registerCommands()
    {
        $this->commands([
            DBMigrateSpecific::class,
            DBRollbackSpecific::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            DBMigrateSpecific::class,
            DBRollbackSpecific::class,
            $this->getSpecificMigratorClass(),
            'SpecificFilesMigrator',
            ISpecificFilesMigrator::class
        ];
    }
}
