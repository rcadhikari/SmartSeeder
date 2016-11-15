<?php namespace Jlapp\SmartSeeder;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Schema\Blueprint;
use App;

class SmartSeederFilesRepository implements MigrationRepositoryInterface {

    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the migration table.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * The name of the environment to run in
     *
     * @var string
     */
    public $env;

    const envVar = 'client';

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
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function setEnv($env) {
        $this->env = $env;
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
        return $this->table()->where(self::envVar, '=', $env)->lists('seed');
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

        return $query->orderBy('file', 'desc')->get();
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
        $record = array(
            'file' => $file,
            self::envVar => $env,
            'batch' => $batch,
            'created_at' => date('Y-m-d H:i:s')
        );

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
            $table->string('file');
            $table->string('client');
            $table->integer('batch');
            $table->timestamp('created_at');
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
     * Get a query builder for the migration table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
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
        return $this->resolver->connection($this->connection);
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

}
