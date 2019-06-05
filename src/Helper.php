<?php

namespace CJPGDK\SALearn;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Description of Helper
 *
 * @author Christian M. Jensen
 */
class Helper 
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
     * Get path to sa-learn binary
     * @return string|null
     */
    public static function saLearn() 
    {
        static $saLearnBin;
        if ($saLearnBin) {
            return $saLearnBin;
        }
        
        list($execOutput, $return_var) = static::exec(static::which().' sa-learn');
        return $saLearnBin = $execOutput[0];
    }

    /**
     * Get path to which binary
     * @return string|null
     */
    public static function which() 
    {
        static $whichBin;
        if ($whichBin) {
            return $whichBin;
        }
        // locate which
        foreach (array(
            '/usr/bin/which',
            '/bin/which'
        ) as $binWhich) {
            if (file_exists($binWhich)) {
                return $whichBin = $binWhich;
            }
        }
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
        exec($cmd, $output, $return_var);
        return array($output, $return_var);
    }
    
    /**
     * 
     * Set a function to get live input from the process that runs.
     * 
     * this variable is reset after each run.
     * 
     * Helper::$onRunCallback = function ($type, $buffer) {
     *  if (Process::ERR === $type) {
     *      echo 'ERR > '.$buffer;
     *  } else {
     *      echo 'OUT > '.$buffer;
     *  }
     * };
     * @var \Closure 
     */
    public static $onRunCallback = null;
    /**
     * on run callbacks, use Helper::setOnRun()
     * 
     * @var array
     * @see Helper::setOnRun()
     * @see Helper::run()
     */
    protected static $onRun = array();
    /**
     * on run cmd, function is full override
     * 
     * function($cmd, $cwd, $env, $input, $timeout, $options) { ... }
     */
    const ONRUN_TYPE_OVERRIDE = 'override';
    
    /**
     * on pre create
     * 
     * function(& $cmd, & $cwd, & $env, & $input, & $timeout, & $options) { ... }
     */
    const ONRUN_TYPE_PRE = 'pre';
    
    /**
     * just before run 
     * 
     * function($process, $params = null) { ... }
     */
    const ONRUN_TYPE_PRE_RUN = 'pre-run';
    
    /**
     * just after run 
     * 
     * function($process, $params = null) { ... }
     */
    const ONRUN_TYPE_POST_RUN = 'post-run';


    /**
     * 
     * @param type $cmd
     * @param \Closure $closure
     * @param type $type
     * @return type
     * @see Helper::ONRUN_TYPE_OVERRIDE
     * @see Helper::ONRUN_TYPE_PRE
     * @see Helper::ONRUN_TYPE_PRE_RUN
     * @see Helper::ONRUN_TYPE_POST_RUN
     */
    public static function setOnRun($cmd, \Closure $closure, $type = 'override')
    {
        if ($type == static::ONRUN_TYPE_OVERRIDE) {
            static::$onRun[$cmd] = $closure;
            return;
        }
        static::$onRun[$cmd][$type][] = $closure;
    }

    /**
     * Run a process
     * 
     * @param string $cmd The command line to run
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     * @param array|null $env The environment variables or null to use the same environment as the current PHP process
     * @param mixed|null $input The input as stream resource, scalar or \Traversable, or null for no input
     * @param int|float|null $timeout The timeout in seconds or null to disable
     * @param array $options An array of options for proc_open
     * @return Process
     * @throws ProcessFailedException
     * @throws RuntimeException When proc_open is not installed
     * @see Helper::setOnRun()
     * @see Helper::$onRunCallback
     * @see Helper::$onRun
     */
    public static function run($cmd, $cwd = null, array $env = null, $input = null, $timeout = 60, array $options = array()) 
    {
        // call override
        if (static::callOnRun($cmd, static::ONRUN_TYPE_OVERRIDE, $cmd, $cwd, $env, $input, $timeout, $options)) {
            return;
        }
        
        static::callOnRun($cmd, static::ONRUN_TYPE_PRE, $cmd, $cwd, $env, $input, $timeout, $options);
        
        $process = new Process($cmd, $cwd, $env, $input, $timeout, $options);
        
        static::callOnRun($cmd, static::ONRUN_TYPE_PRE_RUN, $process);
        
        $process->run(static::$onRunCallback);
        static::$onRunCallback = null;
        
        static::callOnRun($cmd, static::ONRUN_TYPE_POST_RUN, $process);
        
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process;
    }
    
    private static function callOnRun($cmd, $type, ...$params)
    {
        if ($type == static::ONRUN_TYPE_OVERRIDE && ($onRun = isset(static::$onRun[$cmd]) ? static::$onRun[$cmd] : null)) {
            if (!is_array($onRun) && is_callable($onRun)) {
                $onRun(...$params);
                return true;;
            }
            return false;
        }
        
        $onRun = isset(static::$onRun[$cmd][$type]) ? static::$onRun[$cmd][$type] : array();
        foreach ($onRun as $function) {
            if (!is_callable($function)) {
                continue;
            }
            $function(...$params);
        }
    }
}
