<?php
/**
 *  Function description…
 * @project    comply-core-docker
 * @package     ViKon\Utilities
 * @author     Ram Adhikari <ram.adhikari@fticonsulting.com> | 21/11/2016 10:11
 * @copyright  2016 FTI FEDEV Team
 * @license    PHP License 3.01 - http://www.php.net/license/3_01.txt
 * @version    v0.1
 */

namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

trait SeederProgressBarTrait
{
    protected function createProgressBar() {
        //$style = new SymfonyStyle();
        /** @var OutputInterface $output */
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output = $this->getOutput();
        $this->output->writeln('');

        /** @noinspection PhpUndefinedFieldInspection */
        //$dimensions = $style->getApplication()->getTerminalDimensions();
        $progress = new ProgressBar($this->output);
        $progress->setFormat('Seeding: <info>' . get_called_class() . "</info>\n%current:4s%/%max:-4s% [%bar%] %percent:4s%% %elapsed:6s%/%estimated:-6s%");

        return $progress;
    }

    public function getOutput()
    {
        $output = new ConsoleOutput();

        return $output;
    }

    public function line($msg)
    {
        $this->output->writeln($msg);
    }

    public function sampleProgress()
    {
        // use Symfony\Component\Console\Output\ConsoleOutput;
        // use Symfony\Component\Console\Helper\ProgressBar;
        $output = new ConsoleOutput();

        // create a new progress bar (50 units)
        $progress = new ProgressBar($output, 200);

        // start and displays the progress bar
        $progress->start(200);
        $output->writeln('Your message here');
        $progress->advance();
        // ensure that the progress bar is at 100%
        $progress->finish();
    }


}