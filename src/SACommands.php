<?php
namespace CJPGDK\SALearn;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of SACommands
 *
 * @author Christian M. Jensen
 */
class SACommands 
{
    /**
     * Command input
     * @var InputInterface
     */
    public static $input;
    /**
     * Command output
     * @var OutputInterface
     */
    public static $output;

    public static function version() 
    {
        if (static::$input && static::$input->getOption('sa-version')) {
            list($execOutput, $return_var) = static::exec('--version');
            return $execOutput[0];
        }
        return static::$input->getOption('sa-version');
    }

    /**
     * Ececute an command with php exec
     * @param string $cmd
     * @return array $output, $return_var
     */
    public static function exec($cmd) 
    {
        $output     = null;
        $return_var = null;
        exec(escapeshellcmd(static::saLearnBin().' '.$cmd), $output, $return_var);
        return array($output, $return_var);
    }

    /**
     * Set the Command output
     * @param OutputInterface $output
     */
    public static function setOutputInterface(OutputInterface & $output)
    {
        static::$output = $output;
    }

    /**
     * Set the Command input
     * @param InputInterface $input
     */
    public static function setInputInterface(InputInterface & $input)
    {
        static::$input = $input;
    }

    /**
     * Get/Set the path to sa binary
     * @staticvar string|null $saLearnBin
     * @param string|null $bin
     * @return string|null
     */
    public static function saLearnBin($bin = null) 
    {
        static $saLearnBin;
        if (!is_null($bin)) {
            $saLearnBin = trim($bin);
        }
        return $saLearnBin ?:$bin;
    }
}
