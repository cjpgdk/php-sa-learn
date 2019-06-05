<?php

namespace CJPGDK\SALearn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use CJPGDK\SALearn\Helper;
use CJPGDK\SALearn\SACommands;
use Symfony\Component\Process\Process;

/**
 * Description of VersionCommand
 *
 * @author Christian M. Jensen
 */
class HamCommand extends Command
{

    protected function configure()
    {
        $this->setProcessTitle('Learn messages as ham (non-spam)');
        $this->setDescription('Learn messages as ham (non-spam)');
        $this->addOption('use-ignores', '', InputOption::VALUE_NONE, 'Use bayes_ignore_from and bayes_ignore_to', null);
        $this->addOption('folders', 'f', InputOption::VALUE_REQUIRED, 'Read list of files/directories from file', null);
        $this->addOption('mbox', '', InputOption::VALUE_NONE, 'Input sources are in mbox format', null);
        $this->addOption('mbx', '', InputOption::VALUE_NONE, 'Input sources are in mbx format', null);
        $this->addOption('max-size', '', InputOption::VALUE_REQUIRED, 'Skip messages larger than max-size in bytes, 0 implies no limit', '0');
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Override username taken from the runtime environment, used with SQL', null);
        $this->addArgument('file', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folder to read');
    }

    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        // sa-learn --ham --use-ignores? (-f file, --folders=file)? --mbox? --mbx? --max-size? (-u username, --username=username)?
        
        Helper::$onRunCallback = function($type, $buffer) use (& $output){
            if (Process::ERR !== $type) {
                $output->write($buffer);
            }
        };
        
        
        
        SACommands::learnHam(
                $input->getArgument('file'),
                $input->getOption('username'),
                $input->getOption('max-size'),
                $input->getOption('mbx'),
                $input->getOption('mbox'),
                $input->getOption('folders'),
                $input->getOption('use-ignores'));
        
        $output->writeln('');
    }
}
