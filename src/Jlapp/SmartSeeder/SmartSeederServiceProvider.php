<?php namespace Jlapp\SmartSeeder;

use Illuminate\Support\ServiceProvider;
use App;

class SmartSeederServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot() {

        $this->publishes([
            __DIR__.'/../../config/smart-seeder.php' => config_path('smart-seeder.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/smart-seeder.php', 'smart-seeder'
        );

        App::bindShared('seed.repository', function($app) {
            return new SmartSeederRepository(
                $app['db'],
                config('smart-seeder.seedTable') // for seeder table
            );
        });

        // For creating the seed_file table.
        /*App::bindShared('seed.repository_files', function($app) {
            return new SmartSeederFilesRepository(
                $app['db'],
                config('smart-seeder.ClientSeedFileTable') // for seeder files table;
            );
        });*/

        App::bindShared('seed.migrator', function($app)
        {
            return new SeedMigrator($app['seed.repository'], $app['db'], $app['files']);
        });

        /*App::bindShared('seed.default-migrator', function($app)
        {
            return new SeedMigrator($app['seed.repository'], $app['db'], $app['files']);
        });*/

        /*$this->app->bind('command.seed', function($app)
        {
            return new SeedOverrideCommand($app['seed.migrator']);
        });*/

        $this->app->bind('seed.run', function($app)
        {
            return new SeedCommand($app['seed.migrator']);
        });

        $this->app->bind('seed.install', function($app)
        {
            return new SeedInstallCommand(
                $app['seed.repository']
                //$app['seed.repository_files']
            );
        });

        $this->app->bind('seed.make', function()
        {
            return new SeedMakeCommand();
        });

        $this->app->bind('seed.reset', function($app)
        {
            return new SeedResetCommand($app['seed.migrator']);
        });

        $this->app->bind('seed.rollback', function($app)
        {
            return new SeedRollbackCommand($app['seed.migrator']);
        });

        $this->app->bind('seed.refresh', function()
        {
            return new SeedRefreshCommand();
        });

        App::bindShared('seed.smart_migrator', function($app)
        {
            return new SmartSeedMigrator($app['seed.repository'], $app['db'], $app['files']);
        });

        $this->app->bind('seed:core:make', function($app)
        {
            return new SeedCoreMakeCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed:core:run', function($app)
        {
            return new SeedCoreCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed.client.make', function($app)
        {
            return new SeedClientMakeCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed.client.run', function($app)
        {
            return new SeedClientCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed:client:rollback', function($app)
        {
            return new SeedClientRollbackCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed:master:make', function($app)
        {
            return new SeedMasterMakeCommand($app['seed.smart_migrator']);
        });

        $this->app->bind('seed:master:run', function($app)
        {
            return new SeedMasterCommand($app['seed.smart_migrator']);
        });

        $this->commands([
            'seed.install',

            /*'seed.run',
            'seed.install',
            'seed.make',
            'seed.reset',
            'seed.rollback',
            'seed.refresh',*/
            'seed:core:make',
            'seed:core:run',
            'seed.client.make',
            'seed.client.run',
            'seed:client:rollback',

            'seed:master:make',
            'seed:master:run'
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
            'seed.repository',
            'seed.migrator',
            /*'command.seed',
            'seed.run',
            'seed.install',
            'seed.make',
            'seed.reset',
            'seed.rollback',
            'seed.refresh',*/

            /*'seed.repository_files',
            */
            'seed:core:make',
            'seed:core:run',

            'seed.smart_migrator',
            'seed.client.make',
            'seed.client.run',
            'seed:client:rollback',

            'seed:master:make',
            'seed:master:run'
        ];
    }

}
