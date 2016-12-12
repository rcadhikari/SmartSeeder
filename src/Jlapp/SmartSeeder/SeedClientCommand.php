<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\App;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Config;
use Jlapp\SmartSeeder\SmartSeedMigrator;

class SeedClientCommand extends Command {

    use ConfirmableTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:client:run';

    private $migrator;

    protected $command;

    protected $output;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the client data files';

    public function __construct(SmartSeedMigrator $migrator) {
        parent::__construct();
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if ( ! $this->confirmToProceed()) return;

        $this->prepareDatabase();

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $pretend = $this->input->getOption('pretend');

        $path = client_path(config('smart-seeder.clientSeedDir'));
        $file_path= client_path(config('smart-seeder.clientSeedFileDir'));
        $client = $this->option('client-name');
        $file_path = str_replace('{client}', $client, $file_path);

        $env = $client;
        $this->migrator->setEnv($env);
        // Set the Seed Type as 'client';
        $this->migrator->setSeedType('client');

        $single = $this->option('file');
        if ($single) {
            $this->migrator->runSingleFile("$file_path/$single", $pretend);
        }
        else {
            $this->migrator->run($file_path, $pretend);
        }

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note)
        {
            $this->output->writeln($note);
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
            array('client-name', null, InputOption::VALUE_OPTIONAL, 'The client for which to run the seeds.', null),
            array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'),
            array('file', null, InputOption::VALUE_OPTIONAL, 'Allows individual seed files to be run.', null),

            array('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'),
            array('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
        );
    }
}