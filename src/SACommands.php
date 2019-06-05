<?php
namespace CJPGDK\SALearn;

use CJPGDK\SALearn\Helper;

/**
 * Description of SACommands
 *
 * @author Christian M. Jensen
 */
class SACommands 
{
    public static function learnHam(array $files, $username = null, $useIgnores = false, $maxSize = 0, $mbx = false, $mbox = false, $folders = null)
    {
        $cmd  = Helper::saLearn();
        $cmd .= ' --ham';
        $cmd .= ($username ? ' --username='.$username : '');
        $cmd .= ' --max-size '.(intval($maxSize)>0 ? intval($maxSize) : 0);
        $cmd .= ($useIgnores ? ' --use-ignores' : '');
        $cmd .= ($mbx ? ' --mbx' : '');
        $cmd .= ($mbox ? ' --mbox' : '');
        $cmd .= ($mbox ? ' --folders='.$folders : '');
        $cmd .= ' '.implode(' ', $files);
        echo $cmd;
//        return Helper::run($cmd);
    }

    /**
     * Call: sa-learn --version
     * @return string
     */
    public static function version()
    {
        return Helper::run(Helper::saLearn().' --version');
    }
}
