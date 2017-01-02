<?php

namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

use Symfony\Component\Console\Output\ConsoleOutputInterface;

class SmartSeeder extends Seeder
{
    public $connection;

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

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
     protected $output;

    public function a__construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    }

    /**
     * Seed the given connection from the given path.
     *
     * @param  string  $class
     * @return void
     */
    public function call($class, $params = null)
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
   /* public function setCommand(Command $command)
    {
        $this->command = parent::setCommand($this);

        return $this;
    }*/

    public function getCommand()
    {
        return $this->command;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        if( $output instanceof ConsoleOutputInterface )
        {
            // If it's available, get stdErr output
            $this->output = $output->getErrorOutput();
        }
    }

    public function slugify($text)
    {
        // replace non letter or digits by _
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '_');

        // remove duplicate -
        $text = preg_replace('~-+~', '_', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n_a';
        }

        return $text;
    }

    /**
     * @return \Illuminate\Console\OutputStyle
     */
    public function getConnection()
    {
        return $this->connection;
    }


}
