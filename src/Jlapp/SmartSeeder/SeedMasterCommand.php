<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Config;
use Jlapp\SmartSeeder\SmartSeedMigrator;

class SeedMasterCommand extends Command {

    use ConfirmableTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:master:run';

    private $migrator;

    protected $command;

    protected $output;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the master data files';

    public function __construct(SmartSeedMigrator $migrator) {
        parent::__construct();
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire1()
    {
        if ( ! $this->confirmToProceed()) return;

        $this->prepareDatabase();

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $pretend = $this->input->getOption('pretend');

        $path = config('smart-seeder.seedMasterDir');
        $file = config('smart-seeder.seedMasterFile');

        $this->migrator->runSingleFile(client_path($path . $file), $pretend);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note)
        {
            $this->output->writeln($note);
        }
    }

    public function fire()
    {
        if ( ! $this->confirmToProceed()) return;

        $this->prepareDatabase();

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $pretend = $this->input->getOption('pretend');

        $file_path= client_path(config('smart-seeder.masterSeedFileDir'));

        // Set the Seed Type as 'master';
        $this->migrator->setSeedType('master');

        $single = $this->option('file');

        if ($single) {
            $this->migrator->runSingleFile("$file_path/$single", $pretend);
        }
        else {
            $this->migrator->run($file_path, $pretend);
        }
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->input->getOption('database'));

        if ( ! $this->migrator->repositoryExists())
        {
            $options = array('--database' => $this->input->getOption('database'));

            $this->call('seed:install', $options);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'),
            array('file', null, InputOption::VALUE_OPTIONAL, 'Allows individual seed files to be run.', null),

            array('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'),
            array('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
        );
    }
}