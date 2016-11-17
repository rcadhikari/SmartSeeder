<?php

namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

class SmartSeeder extends Seeder
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /*public function __construct()
    {
        $this->command = parent::setCommand();
    }*/

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    //protected $output;

    public $seedFilePath;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    }

    public function setSeedFilePath($seedFilePath)
    {
        $this->seedFilePath = $seedFilePath;
    }

    public function getSeedFilePath()
    {
        return $this->seedFilePath;
    }

    /**
     * Seed the given connection from the given path.
     *
     * @param  string  $class
     * @return void
     */
    public function call($class)
    {
        $this->resolve($class)->run();

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeded:</info> $class");
        }
    }

    public function callSeeder($class, $params = null)
    {
        $this->resolve($class)->run($params);

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeded:</info> $class");
        }
    }

    /**
     * Resolve an instance of the given seeder class.
     *
     * @param  string  $class
     * @return \Illuminate\Database\Seeder
     */
    protected function resolve($class)
    {
        if (isset($this->container)) {
            $instance = $this->container->make($class);

            $instance->setContainer($this->container);
        } else {
            $instance = new $class;
        }

        if (isset($this->command)) {
            $instance->setCommand($this->command);
        }

        return $instance;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Set the console command instance.
     *
     * @param  \Illuminate\Console\Command  $command
     * @return $this
     */
    public function setCommand1(Command $command)
    {
        $this->command = $command;

        return $this;
    }
}
