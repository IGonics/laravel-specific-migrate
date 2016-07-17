<?php

namespace IGonics\Notification\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Symfony\Component\Console\Input\InputOption;
use IGonics\Notification\Services\SpecificFilesMigrator;

class RollbackNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'notifications:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback Notification Schema';

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
        $relativePath = '/../../../../database/migrations';
        $path = __DIR__.$relativePath;
        //var_dump($path);
        $files = array_filter(scandir($path), function ($file) {
            return preg_match("/\.php/", $file) == 1;
        });
        $files = array_map(function ($file) use ($path) {
            return str_replace([$path, '.php'], '', $file);
        }, $files);
        //var_dump($files);

        $this->call('migrate:specific-down', [
            '--force' => true,
            '--path' => str_replace(app_path(), '', $path),
            '--files' => implode(',', $files),
        ]);
    }
}
