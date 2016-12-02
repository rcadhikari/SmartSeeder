<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use File;
use App;
use Config;

class SeedClientMakeCommand extends Command {

    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:client:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a seed for client';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $client = $this->option('client-name');
        $file_path= client_path(config('smart-seeder.seedFileDir'));
        $file_path = str_replace('{client}', $client, $file_path);
        $data_path = config('smart-seeder.seedDataFileDir');

        $seed_file = $this->argument('seed');

        // Return the error message if no extension found.
        if (strpos($seed_file, '.') === false) {
            pc('Please enter the seed(seed file) extension.', 1);
        }
        // To remove the seed file extension.
        $seed_name = substr($seed_file,0, strpos($seed_file, '.'));
        $model = ucfirst(camel_case($seed_name));
        $path = $this->option('seeder-path');

        $path = (empty($path)) ? $file_path : base_path($path);
        $data_path = $path . $data_path;

        if (!File::exists($data_path)) {
            // mode 0755 is based on the default mode Laravel use.
            File::makeDirectory($data_path, 755, true);
        }
        $created = date('Y_m_d_His');
        $model_filename = snake_case($model);
        $path .= "/{$created}_{$model_filename}_seeder.php";

        $fs = File::get(__DIR__."/stubs/SmartSeeder.stub");

        $namespace = rtrim($this->getAppNamespace(), "\\");
        $stub = str_replace('{{model}}', $model.'Seeder', $fs);
        $stub = str_replace('{{namespace}}', " namespace $namespace;", $stub);
        $stub = str_replace('{{class}}', $model, $stub);
        $stub = str_replace('{{seed_file}}', $seed_file, $stub);

        File::put($path, $stub);

        $message = "Seeder class <info>$model</info> created";
        if (!empty($client)) {
            $message .= " for client: $client";
        }

        $this->line($message);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('seed', InputArgument::REQUIRED, 'The name of the model you wish to seed.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('client-name', null, InputOption::VALUE_OPTIONAL, 'The client name to seed to.', null),
            array('seeder-path', null, InputOption::VALUE_OPTIONAL, 'The relative path to the base path to generate the seed to.', null),
        );
    }
}
