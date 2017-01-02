<?php namespace Jlapp\SmartSeeder;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Schema\Blueprint;
use App;

class SmartSeederRepository implements SmartSeederRepositoryInterface
{
    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    var $resolver;

    /**
     * The name of the migration table.
     *
     * @var string
     */
    var $table;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    var $connection;

    /**
     * The name of the environment to run in
     *
     * @var string
     */
    var $env;

    /**
     * The name of the seeder type to run for
     *
     * @var string
     */
    var $seedType;

    /**
     * Create a new database migration repository instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface $resolver
     * @param  string                                           $table
     */
    public function __construct(Resolver $resolver, $table)
    {
        $this->table = $table;
        $this->resolver = $resolver;
    }

    /**
     * Get the ran migrations.
     *
     * @return array
     */
    public function getRan()
    {
        $env = $this->env;
        if (empty($env)) {
            $env = App::environment();
        }

        return $this->table()
            ->where(self::envVar, '=', $env)
            ->orderBy('batch', 'asc')
            ->orderBy('seed', 'asc')
            ->pluck('seed')->all();
    }

    public function getMigrations($steps)
    {
        $this->getMigrations($steps);
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast()
    {
        $env = $this->env;
        if (empty($env)) {
            $env = App::environment();
        }

        $query = $this->table()->where(self::envVar, '=', $env)->where('batch', $this->getLastBatchNumber());

        return $query->orderBy('seed', 'desc')->get();
    }

    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    public function log($file, $batch)
    {
        $env = $this->env;
        if (empty($env)) {
            $env = App::environment();
        }
        $record = array('seed' => $file, self::envVar => $env, 'batch' => $batch);

        $this->table()->insert($record);
    }

    /**
     * Remove a migration from the log.
     *
     * @param $seed
     *
     * @internal param object $migration
     */
    public function delete($seed)
    {
        $env = $this->env;
        if (empty($env)) {
            $env = App::environment();
        }
        $this->table()->where(self::envVar, '=', $env)->where('seed', $seed->seed)->delete();
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public function createRepository()
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        if ($schema->hasTable($this->table)) {
            return false;
        }

        $schema->create($this->table, function(Blueprint $table)
        {
            // The migrations table is responsible for keeping track of which of the
            // migrations have actually run for the application. We'll create the
            // table to hold the migration file's path as well as the batch ID.
            $table->string('seed');
            $table->string('client');
            $table->integer('batch');
        });

        return true;
    }

    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists()
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }

    /**
     * Set the information source to gather data.
     *
     * @param  string  $name
     * @return void
     */
    public function setSource($name)
    {
        $this->connection = $name;
    }

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function setEnv($env) {
        $this->env = $env;
    }

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function setSeedType($seedType) {
        $this->seedType = $seedType;
    }

    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber()
    {
        $env = $this->env;
        if (empty($env)) {
            $env = App::environment();
        }
        return $this->table()->where('client', '=', $env)->max('batch');
    }

    /**
     * Get a query builder for the migration table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function table()
    {
        return $this->getConnection()->table($this->table);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver()
    {
        return $this->resolver;
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        if (empty($this->connection)) {
            $name = 'cli'; // that's the default system connection
            $name = ( !in_array($name, config('database.connections') ) ) ? $name : config('database.default');

            $this->connection = $name;
        }

        return $this->resolver->connection($this->connection);
    }
}