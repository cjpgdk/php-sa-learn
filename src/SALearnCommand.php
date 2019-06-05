<?php
namespace CJPGDK\SALearn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use CJPGDK\SALearn\SACommands;

/**
 * Description of SALearnCommand
 *
 * @author Christian M. Jensen
 */
class SALearnCommand extends Command
{
    /**
     * Path to which binary
     * @var string
     */
    private $binWhich = '/usr/bin/which';
    
    public function __construct($name = null) {
        parent::__construct('sa-learn');
    }

    protected function configure()
    {
        $this->addOption('ham', '', InputOption::VALUE_NONE, 'Learn messages as ham (non-spam)', null);
        $this->addOption('spam', '', InputOption::VALUE_NONE, 'Learn messages as spam', null);
        $this->addOption('forget', '', InputOption::VALUE_NONE, 'Forget a message', null);
        $this->addOption('use-ignores', '', InputOption::VALUE_NONE, 'Use bayes_ignore_from and bayes_ignore_to', null);
        $this->addOption('sync', '', InputOption::VALUE_NONE, 'Synchronize the database and the journal if needed', null);
        $this->addOption('force-expire', '', InputOption::VALUE_NONE, 'Force a database sync and expiry run', null);
        $this->addOption('dbpath', '', InputOption::VALUE_REQUIRED, 'Allows commandline override (in bayes_path form) for where to read the Bayes DB from', null);
        $this->addOption('dump', '', InputOption::VALUE_REQUIRED, '[all|data|magic]  Display the contents of the Bayes database, Takes optional argument for what to display', 'all');
        $this->addOption('regexp', '', InputOption::VALUE_REQUIRED, 'For dump only, specifies which tokens to dump based on a regular expression.', null);
        $this->addOption('folders', 'f', InputOption::VALUE_REQUIRED, 'Read list of files/directories from file', null);
        $this->addOption('mbox', '', InputOption::VALUE_NONE, 'Input sources are in mbox format', null);
        $this->addOption('mbx', '', InputOption::VALUE_NONE, 'Input sources are in mbx format', null);
        $this->addOption('max-size', '', InputOption::VALUE_REQUIRED, 'Skip messages larger than max-size in bytes, 0 implies no limit (default: 0)', '0');
        $this->addOption('no-sync', '', InputOption::VALUE_NONE, 'Skip synchronizing the database and journal after learning', null);
        $this->addOption('local', 'L', InputOption::VALUE_NONE, 'Operate locally, no network accesses', null);
        $this->addOption('import', '', InputOption::VALUE_NONE, 'Migrate data from older version/non DB_File based databases', null);
        $this->addOption('clear', '', InputOption::VALUE_NONE, 'Wipe out existing database', null);
        $this->addOption('backup', '', InputOption::VALUE_REQUIRED, 'Backup, to file, existing database', null);
        $this->addOption('restore', '', InputOption::VALUE_REQUIRED, 'Restore a database from filename', null);
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Override username taken from the runtime environment, used with SQL', null);
        $this->addOption('configpath', 'C', InputOption::VALUE_REQUIRED, 'Path to standard configuration dir', null);
        $this->addOption('config-file', '', InputOption::VALUE_REQUIRED, 'Path to standard configuration dir', null);
        $this->addOption('prefspath', 'p', InputOption::VALUE_REQUIRED, 'Path to standard configuration dir', null);
        $this->addOption('prefs-file', '', InputOption::VALUE_REQUIRED, 'Set user preferences file', null);
        $this->addOption('siteconfigpath', '', InputOption::VALUE_REQUIRED, 'Path for site configs (default: /usr/etc/spamassassin)', '/usr/etc/spamassassin');
        $this->addOption('cf', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional line of configuration', null);
        $this->addOption('debug', 'D', InputOption::VALUE_REQUIRED, '[area=n,...] Print debugging messages', null);
        $this->addOption('sa-version', '', InputOption::VALUE_NONE, 'Print version', null);
        
        $this->addArgument('file', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Files and folder to read');
        
        // build in thing.
        $this->addOption('showdots', '', InputOption::VALUE_NONE, 'Show progress using dots', null);
        $this->addOption('progress', '', InputOption::VALUE_NONE, 'Show progress using progress bar', null);
        
        // locate which
        foreach (array(
            '/usr/bin/which',
            '/bin/which'
        ) as $binWhich) {
            if (file_exists($binWhich)) {
                $this->binWhich = $binWhich;
                break;
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        $files = $input->getArgument('file');
        
        list($execOutput, $return_var) = $this->exec($this->binWhich.' sa-learn');
        if (!$this->testReturnStatus($return_var, 0)) {
            die('error');
        }
        
        SACommands::saLearnBin($execOutput[0]);
        SACommands::setInputInterface($input);
        SACommands::setOutputInterface($output);
        
        // options that if pressent just prints end exit.
        $this->_execEcho(SACommands::version(), true);
        
        $output->writeln('DONE');
    }
    
    private function _exec($res, $exitOnOk = false) 
    {
        if (is_callable($res)) {
            SACommands::$output->writeln($res());
            if ($exitOnOk) { exit(0); }
        } else if($res) {
            SACommands::$output->writeln($res);
            if ($exitOnOk) { exit(0); }
        }
    }
    
    /**
     * compare $result and $expect
     * @param mixed $result
     * @param mixed $expect
     * @return boolean
     */
    private function testReturnStatus($result, $expect = 0)
    {
        if ($result <> $expect) {
            return false;
        }
        return true;
    }
    

}
