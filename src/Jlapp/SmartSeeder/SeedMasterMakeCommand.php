<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use File;
use App;
use Config;

class SeedMasterMakeCommand extends Command {

    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:master:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a seed for master';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $file_path= client_path(config('smart-seeder.seedMasterDir'));
        $data_path = config('smart-seeder.dataFileDir');

        $seed_file = $this->argument('seed');

        // Return the error message if no extension found.
        if (strpos($seed_file, '.') === false) {
            $this->line("\nPlease enter the seed(seed file) <info>extension</info>.");dd();
        }
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
        $table = snake_case($model);
        $path .= DIRECTORY_SEPARATOR."{$created}_{$table}_seeder.php";

        $eloquentModel = substr($model, 0,-1);
        $seedDataFile = $seed_name;

        $fs = File::get(__DIR__."/stubs/MasterSeeder.stub");

        $eloquentModel = substr($model, 0,-1);
        $model = "{$model}Seeder_{$created}";

        $namespace = rtrim($this->getAppNamespace(), "\\");
        $stub = str_replace('{{model}}', $model, $fs);
        $stub = str_replace('{{namespace}}', " namespace $namespace;", $stub);
        $stub = str_replace('{{eloquentModel}}', $eloquentModel, $stub);
        $stub = str_replace('{{table}}', $table, $stub);
        $stub = str_replace('{{seed_file}}', $seed_file, $stub);

        File::put($path, $stub);

        $message = "Master seeder class <info>$model</info> is created";

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
            array('seeder-path', null, InputOption::VALUE_OPTIONAL, 'The relative path to the base path to generate the seed to.', null),
        );
    }
}
