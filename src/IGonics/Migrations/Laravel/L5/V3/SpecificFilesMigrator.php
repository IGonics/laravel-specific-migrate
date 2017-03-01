<?php

namespace IGonics\Migrations\Laravel\L5\V3;

use Illuminate\Support\Arr;
use Illuminate\Database\Migrations\Migrator;
use IGonics\Migrations\Contracts\ISpecificFilesMigrator;

class SpecificFilesMigrator extends Migrator implements ISpecificFilesMigrator
{
    protected $filesToMigrate = [];
    protected $ensureFilesInPath = true;
    protected $rollbackPath = null;

    public function setEnsureFilesInPath($ensure = true)
    {
        $this->ensureFilesInPath;
    }

    public function shouldFilesBeInPath()
    {
        return $this->ensureFilesInPath;
    }

    public function setFilesToMigrate($files = [])
    {
        $this->filesToMigrate = $files;
    }

    public function getRollbackPath()
    {
        return $this->rollbackPath ?: database_path('migrations');
    }

    public function setRollbackPath($path)
    {
        $this->rollbackPath = $path;
    }

    public function getFilesToMigrate($files = [])
    {
        $this->filesToMigrate = $files;
    }
    /**
     * Get all of the migration files in a given path and verify whether specifie fles exist.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFilesFromPath($path)
    {
        if (!$this->shouldFilesBeInPath()) {
            return $this->filesToMigrate;
        }
        $files = $this->files->glob($path.'/*_*.php');
        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if ($files === false) {
            return [];
        }
        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);
        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        $actualFiles = $this->filesToMigrate;
        sort($actualFiles);
        $instance = $this;
        // var_dump([$files]);
        $actualFiles = array_filter($actualFiles, function ($file) use ($files, $instance, $path) {
            $inArray = in_array($file, $files);
            if (!$inArray) {
                $instance->note("<error>$file was not found in $path. Skipping $file</error>");
            }

            return $inArray;
        });

        return $actualFiles;
    }

    public function getMigrationFiles($paths){
        $files=[];
        foreach ($paths as $path) {
            $files = array_merge($files, $this->getMigrationFilesFromPath($path));
        }
        return $files;
    }

    /**
     * Run the outstanding migrations at a given path.
     *
     * @param  string  $path
     * @param  array  $options
     * @param  array  $files 
     * @return void
     */
    public function run($paths=[], array $options = [])
    {
        parent::run($paths, $options);
    }

    /**
     * Rollback the last migration operation.
     *
     * @param  bool  $pretend
     * @param  array  $files 
     * @return int
     */
    public function rollback($paths=[], array $options = [])
    {
        $this->notes = [];

        $rolledBack = [];

        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
        if (($steps = Arr::get($options, 'step', 0)) > 0) {
            $migrations = $this->repository->getMigrations($steps);
        } else {
            $migrations = $this->repository->getLast();
        }

        $count = count($migrations);

        $files = $this->getMigrationFiles($paths);

        if ($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            // Next we will run through all of the migrations and call the "down" method
            // which will reverse each migration in order. This getLast method on the
            // repository already returns these migration's names in reverse order.
            $this->requireFiles($files);

            foreach ($migrations as $migration) {
                $rolledBack[] = $files[$migration->migration];

                $this->runDown(
                    $files[$migration->migration],
                    (object) $migration, Arr::get($options, 'pretend', false)
                );
            }
        }

        return $rolledBack;
    }
}
