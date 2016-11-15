<?php
/**
 * Created by PhpStorm.
 * User: Jordan
 * Date: 2014-11-07
 * Time: 1:46 PM
 */

namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\App;

class SeedInstallCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the seed repository';

    /**
     * The repository instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    protected $repository_files;


    public function __construct(
        SmartSeederRepository $repository,
        SmartSeederFilesRepository $repository_files
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->repository_files = $repository_files;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->repository->setSource($this->input->getOption('database'));
        $result = $this->repository->createRepository();

        $message = (!$result) ? 'already exist' : 'created successfully';
        $this->info("Seeds table $message.");

        $this->repository_files->setSource($this->input->getOption('database'));
        $result = $this->repository_files->createRepository();
        $message = (!$result) ? 'already exist' : 'created successfully';
        $this->info("Seeds files table $message.");
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
        );
    }
}