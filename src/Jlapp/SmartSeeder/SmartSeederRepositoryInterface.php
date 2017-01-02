<?php namespace Jlapp\SmartSeeder;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;

interface SmartSeederRepositoryInterface extends MigrationRepositoryInterface
{
    const envVar = 'client';

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function setEnv($env);

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function getEnv();

    /**
     * Set the environment to run the seeds against
     *
     * @param $env
     */
    public function setSeedType($seedType);

    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber();

    /**
     * Get a query builder for the migration table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function table();

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver();

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection();

}