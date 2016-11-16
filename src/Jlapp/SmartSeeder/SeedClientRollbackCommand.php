<?php namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

use File;
use Config;

class SeedClientRollbackCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:client:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a seed rollback for client';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct(SmartSeedMigrator $migrator)
    {
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
        if (!$this->confirmToProceed()) return;

        $client = $this->option('client-name');

        $this->migrator->setClient($client);
        $this->migrator->setConnection('cli');

        $file_path= client_path(config('smart-seeder.seedFileDir'));
        $file_path = str_replace('{client}', $client, $file_path);

        //if (File::exists(database_path(config('smart-seeder.seedsDir')))) {
        if (File::exists($file_path)) {
            $this->migrator->setEnv($client);
        }

        $pretend = $this->input->getOption('pretend');

        $this->migrator->rollback($pretend);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    /*protected function getArguments()
    {
        return array(
            array('from', InputArgument::REQUIRED, 'The from date in the format dd-mm-yyyy.'),
            array('to', InputArgument::REQUIRED, 'The to date in the format dd-mm-yyyy.'),
        );
    }*/

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('client-name', null, InputOption::VALUE_OPTIONAL, 'The client in which to rollback the seeds.', null),
            array('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
            array('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'),
        );
    }

}