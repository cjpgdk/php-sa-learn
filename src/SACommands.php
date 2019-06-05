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
    /**
     * Call: sa-learn --spam...
     * @param array $files
     * @param string $username
     * @param boolean $useIgnores
     * @param int $maxSize
     * @param boolean $mbx
     * @param boolean $mbox
     * @param string $folders
     * @return string
     */
    public static function learnSpam(array $files, $username = null, $useIgnores = false, $maxSize = 0, $mbx = false, $mbox = false, $folders = null)
    {
        $cmd  = Helper::saLearn();
        $cmd .= ' --spam';
        $cmd .= ($username ? ' --username='.$username : '');
        $cmd .= ' --max-size '.(intval($maxSize)>0 ? intval($maxSize) : 0);
        $cmd .= ($useIgnores ? ' --use-ignores' : '');
        $cmd .= ($mbx ? ' --mbx' : '');
        $cmd .= ($mbox ? ' --mbox' : '');
        $cmd .= ($folders ? ' --folders='.$folders : '');
        $cmd .= ' '.implode(' ', $files);
        return Helper::run($cmd);
    }
    
    /**
     * Call: sa-learn --ham...
     * @param array $files
     * @param string $username
     * @param boolean $useIgnores
     * @param int $maxSize
     * @param boolean $mbx
     * @param boolean $mbox
     * @param string $folders
     * @return string
     */
    public static function learnHam(array $files, $username = null, $useIgnores = false, $maxSize = 0, $mbx = false, $mbox = false, $folders = null)
    {
        $cmd  = Helper::saLearn();
        $cmd .= ' --ham';
        $cmd .= ($username ? ' --username='.$username : '');
        $cmd .= ' --max-size '.(intval($maxSize)>0 ? intval($maxSize) : 0);
        $cmd .= ($useIgnores ? ' --use-ignores' : '');
        $cmd .= ($mbx ? ' --mbx' : '');
        $cmd .= ($mbox ? ' --mbox' : '');
        $cmd .= ($folders ? ' --folders='.$folders : '');
        $cmd .= ' '.implode(' ', $files);
        return Helper::run($cmd);
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
