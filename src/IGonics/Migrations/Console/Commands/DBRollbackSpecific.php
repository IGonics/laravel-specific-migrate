<?php

namespace IGonics\Notification\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Symfony\Component\Console\Input\InputOption;
use IGonics\Notification\Services\SpecificFilesMigrator;

class DBRollbackSpecific extends MigrateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'migrate:specific-down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Down Specific Migration Files';

    /**
     * Create a new command instance.
     */
    public function __construct(SpecificFilesMigrator $migrator)
    {
        parent::__construct($migrator);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        $this->prepareDatabase();
        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $pretend = $this->input->getOption('pretend');
        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if (!is_null($path = $this->input->getOption('path'))) {
            $path = $this->input->getOption('prefix-with-base-path') ? $this->laravel->basePath().'/'.$path : $path;
        } else {
            $path = $this->getMigrationPath();
        }

        $files = $this->input->getOption('files') ?: '';
        $files = array_map(function ($file) {
            return trim($file);
        }, explode(',', trim($files)));

        $files = array_filter($files, function ($file) {
            return strlen($file) != 0;
        });

        $this->migrator->setFilesToMigrate($files);
        $this->migrator->setRollbackPath($path);

        $count = $this->migrator->rollback($pretend);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }

        $this->output->writeln("<info>$count files rollbacked successfully</info>");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['files', null, InputOption::VALUE_OPTIONAL, 'Comma delimited list of php files without extensions to migrate.'],
            ['prefix-with-base-path', null, InputOption::VALUE_NONE, 'Prefix --path with app base bath'],
        ]);
    }
}
