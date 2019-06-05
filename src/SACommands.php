<?php
namespace CJPGDK\SALearn;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CJPGDK\SALearn\Helper;

/**
 * Description of SACommands
 *
 * @author Christian M. Jensen
 */
class SACommands 
{

    public static function version() 
    {
        return Helper::run([
            Helper::saLearn(),
            ' --version'
        ]);
    }
}
