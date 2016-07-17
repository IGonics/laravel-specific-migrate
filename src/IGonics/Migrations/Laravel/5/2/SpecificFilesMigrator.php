<?php

namespace IGonics\Notification\Services;

use Illuminate\Database\Migrations\Migrator;

class SpecificFilesMigrator extends Migrator
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
    public function getMigrationFiles($path)
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

    /**
     * Run the outstanding migrations at a given path.
     *
     * @param  string  $path
     * @param  array  $options
     * @param  array  $files 
     * @return void
     */
    public function run($path, array $options = [], $files = [])
    {
        if (is_array($files) && !empty($files)) {
            $this->setFilesToMigrate($files);
        }
        parent::run($path, $options);
    }

    /**
     * Rollback the last migration operation.
     *
     * @param  bool  $pretend
     * @param  array  $files 
     * @return int
     */
    public function rollback($pretend = false)
    {
        $files = $this->getMigrationFiles();
        $path = $this->getRollbackPath();
        $this->notes = [];
        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
        if ($path == null) {
            $this->note('<error>Please specify the base paths where these migrations exists.</error>');

            return 0;
        }
        $files = array_filter($files, function ($file) use ($path) {
            $fullName = "$path/$file.php";
            if (!file_exists($fullName)) {
                $this->note("<error>$fullName does not exist...Skipping</error>");

                return false;
            }
            require_once $fullName;

            return true;
        });
        sort($files);
        $files = array_reverse($files);
        $migrations = array_map(function ($filename) {
            $migration = new \stdClass;
            $migration->migration = $filename;

            return $migration;
        }, $files);
        $count = count($migrations);
        if ($count === 0) {
            $this->note('<info>Nothing to rollback. Please specify the files you would like to rollback</info>');
        } else {
            // We need to reverse these migrations so that they are "downed" in reverse
            // to what they run on "up". It lets us backtrack through the migrations
            // and properly reverse the entire database schema operation that ran.
            foreach ($migrations as $migration) {
                $this->runDown((object) $migration, $pretend);
            }
        }

        return $count;
    }
}
