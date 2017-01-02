<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use File;
use App;
use Config;

class SeedCoreMakeCommand extends Command {

    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:core:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a seed for core via smart seeder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $seed_type = $this->option('type');

        if ($seed_type == "csv") {
            $this->create_seeder_for_csv($seed_type);
        } else {
            $this->create_seeder();
        }
    }

    private function create_seeder_for_csv($seed_type)
    {
        $file_path= client_path(config('smart-seeder.coreSeedFileDir'));
        $data_path = config('smart-seeder.dataFileDir');
        $seed_file = $this->argument('seed');
        $extension = $seed_type;

        $model = ucfirst(camel_case($seed_file));

        $path = $file_path;
        $data_path = $path . $data_path;

        if (!File::exists($data_path)) {
            // mode 0755 is based on the default mode Laravel use.
            File::makeDirectory($data_path, 755, true);
        }

        $created = date('Y_m_d_His');
        $table = snake_case($model);
        $path .= DIRECTORY_SEPARATOR."{$created}_{$table}_seeder.php";
        $seedDataFile = "{$table}.$extension";

        $data_path .= DIRECTORY_SEPARATOR.$seedDataFile;

        // Creating Seeder file
        $fs = File::get(__DIR__.DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."CoreSeederCsv.stub");

        $eloquentModel = substr($model, 0,-1);
        $model = "{$model}Seeder_{$created}";

        $namespace = rtrim($this->getAppNamespace(), "\\");
        $stub = str_replace('{{model}}', $model, $fs);
        $stub = str_replace('{{eloquentModel}}', $eloquentModel, $stub);
        $stub = str_replace('{{seedDataFile}}', $seedDataFile, $stub);
        File::put($path, $stub);

        // Creating Seeder Data file
        $stub = "";
        File::put($data_path, $stub);

        $this->line("Seeder class <info>$model</info> and data file <info>$seedDataFile</info> created for a Core");
    }

    private function create_seeder()
    {
        $file_path= client_path(config('smart-seeder.coreSeedFileDir'));
        $data_path = config('smart-seeder.dataFileDir');
        $seed_file = $this->argument('seed');

        $model = ucfirst(camel_case($seed_file));

        $path = $file_path;
        $data_path = $path . $data_path;

        if (!File::exists($data_path)) {
            // mode 0755 is based on the default mode Laravel use.
            File::makeDirectory($data_path, 755, true);
        }

        $created = date('Y_m_d_His');
        $table = snake_case($model);
        $path .= DIRECTORY_SEPARATOR."{$created}_{$table}_seeder.php";
        $seedDataFile = "{$table}_table_data.php";

        $data_path .= DIRECTORY_SEPARATOR.$seedDataFile;

        // Creating Seeder file
        $fs = File::get(__DIR__.DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR."CoreSeeder.stub");

        $eloquentModel = substr($model, 0,-1);
        $model = "{$model}Seeder_{$created}";

        $namespace = rtrim($this->getAppNamespace(), "\\");
        $stub = str_replace('{{model}}', $model, $fs);
        $stub = str_replace('{{namespace}}', " namespace $namespace;", $stub);
        $stub = str_replace('{{eloquentModel}}', $eloquentModel, $stub);
        $stub = str_replace('{{table}}', $table, $stub);
        $stub = str_replace('{{seedDataFile}}', $seedDataFile, $stub);

        File::put($path, $stub);

        // Creating Seeder Data file
        $stub = File::get(__DIR__."/stubs/CoreSeederDataFile.stub");
        File::put($data_path, $stub);

        $this->line("Seeder class <info>$model</info> and data file <info>$seedDataFile</info> created for a Core");
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
            //array('calls', InputArgument::REQUIRED, 'The name of the existing client seeder class(/database/seeds/client) you wish to call (use for parsing the seed file for seeding).'),
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
            array('seed', null, InputOption::VALUE_REQUIRED, 'The name of the model you wish to seed.', null),
            array('type', null, InputOption::VALUE_OPTIONAL, 'Type of seeder file to be imported', null),
            //array('seeder-path', null, InputOption::VALUE_OPTIONAL, 'The relative path to the base path to generate the seed to.', null),
        );
    }
}
