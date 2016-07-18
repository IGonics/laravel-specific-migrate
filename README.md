# Laravel Specific File Migrator

Migrate Specific Migrations

## Installation

Step 1. Install using composer
```
composer require igonics/laravel-specific-migrate
```

Step 2. Add Service Provider to config/app.php
```
return [
     ...
     "providers":[
        ...
        IGonics\Migrations\Providers\SpecificMigratorServiceProvider::class,
     ]
];
```
## Usage

Migrating specific files
```
php artisan migrate:specific --files="filename_1, filename_2"
```

Rollback specific files
```
php artisan migrate:specific-rollback --files="filename_1, filename_2"
```


### Using the specific migrator 

Migrating specific files
```
class MigrateComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'component:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Component Schema';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $relativePath = database_path('migrations');
        $path = __DIR__.$relativePath;
        //var_dump($path);
        $files = array_filter(scandir($path), function ($file) {
            return preg_match("/\.php/", $file) == 1;
        });
        $files = array_map(function ($file) use ($path) {
            return str_replace([$path, '.php'], '', $file);
        }, $files);
        //var_dump($files);

        $this->call('migrate:specific', [
            '--force' => true,
            '--path' => str_replace(app_path(), '', $path),
            '--files' => implode(',', $files),
        ]);
    }
}

```

Rolling back specific files
```
class RollbackComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'component:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback Component Schema';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $relativePath = database_path('migrations');
        $path = __DIR__.$relativePath;
        //var_dump($path);
        $files = array_filter(scandir($path), function ($file) {
            return preg_match("/\.php/", $file) == 1;
        });
        $files = array_map(function ($file) use ($path) {
            return str_replace([$path, '.php'], '', $file);
        }, $files);
        //var_dump($files);

        $this->call('migrate:specific-rollback', [
            '--force' => true,
            '--path' => str_replace(app_path(), '', $path),
            '--files' => implode(',', $files),
        ]);
    }
}

```

#MIT License
Copyright (c) 2016 IGonics

# Maintained by
[IGonics](http://igonics.com)