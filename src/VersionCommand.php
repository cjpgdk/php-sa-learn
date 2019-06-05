<?php

namespace CJPGDK\SALearn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CJPGDK\SALearn\Helper;
use CJPGDK\SALearn\SACommands;
use Symfony\Component\Process\Process;

/**
 * Description of VersionCommand
 *
 * @author Christian M. Jensen
 */
class VersionCommand extends Command
{

    protected function configure()
    {
        $this->setProcessTitle('Show version of sa-learn binary');
        $this->setDescription('Show version of sa-learn binary');
    }

    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        Helper::$onRunCallback = function($type, $buffer) use (& $output){
            if (Process::ERR !== $type) {
                $output->write($buffer);
            }
        };
        SACommands::version();
        $output->writeln('');
    }
}
