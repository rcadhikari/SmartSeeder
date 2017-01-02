<?php namespace Jlapp\SmartSeeder;

trait SmartSeederRepositoryTrait
{
    private $envVar = 'client';

    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    private $resolver;

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

    /**
     * The name of the seeder type to run for
     *
     * @var string
     */
    public $seedType = "";

}