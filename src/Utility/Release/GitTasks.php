<?php


namespace App\Utility\Release;


/**
 * Class GitTasks
 * Helps to do common Git tasks when looking to build a release of the App
 *
 * @package App\Utility\Release
 * @property string|false|null $gitExe
 */
class GitTasks
{
    private $gitExe = null;

    /**
     * DefaultApplication constructor.
     */
    public function __construct()
    {

    }

    /**
     * Get the location of the git.exe
     *
     * @return bool|false|string|null
     */
    public function getGitExe()
    {
        if (!is_null($this->gitExe)) {
            return $this->gitExe;
        }

        $cmd = 'where git.exe';
        $out = null;
        $ret = null;
        exec($cmd, $out, $ret);

        if (isset($out[0]) && is_file($out[0])) {
            $gitExeLocation = $out;
        } elseif (is_file('C:\Program Files\Git\cmd\git.exe')) {
            $gitExeLocation = 'C:\Program Files\Git\cmd\git.exe';
        } elseif (is_file('C:\Program Files\Git\bin\git.exe')) {
            $gitExeLocation = 'C:\Program Files\Git\bin\git.exe';
        } else {
            $gitExeLocation = false;
        }

        $this->gitExe = $gitExeLocation;

        return $this->gitExe;
    }

    /**
     * Return the current GIT branch
     *
     * @return string
     */
    public function getGitBranch()
    {
        $gitBranch = array_reverse(explode("/", trim(file_get_contents(ROOT . DS . ".git" . DS . "HEAD"))))[0];
        return $gitBranch;
    }

    /**
     * Return a list of modified files
     *
     * @return array
     */
    public function getGitModified()
    {
        $gitExeLocation = $this->getGitExe();

        $cmd = __('"{0}" status --porcelain ', $gitExeLocation);
        $out = null;
        $ret = null;
        exec($cmd, $out, $ret);

        $modifiedFiles = [];
        if (is_array($out)) {
            foreach ($out as $line) {
                $modPrefixes = [' M ', 'AM ', 'A  ',];
                foreach ($modPrefixes as $modPrefix) {
                    if (substr($line, 0, strlen($modPrefix)) == $modPrefix) {
                        $line = str_replace($modPrefix, '', $line);
                        $modifiedFiles[] = $line;
                    }
                }
            }
        }

        return $modifiedFiles;
    }

    /**
     * Get the Git commits since the last build
     *
     * @return array
     */
    public function getCommitsSinceLastBuild()
    {
        $commits = [];

        $gitExeLocation = $this->getGitExe();

        $cmd = __('"{0}" log --format=oneline ', $gitExeLocation);
        $out = null;
        $ret = null;
        exec($cmd, $out, $ret);

        if (is_array($out)) {
            foreach ($out as $line) {
                $line = explode(" ", $line, 2);
                $commitSha1 = $line[0];
                $commitMsg = $line[1];

                $commitBuildPrefix = 'Build Release: ';
                if (substr($commitMsg, 0, strlen($commitBuildPrefix)) == $commitBuildPrefix) {
                    break 1;
                }

                $commitVersionHistoryPrefix = 'Commit Version History: ';
                if (substr($commitMsg, 0, strlen($commitVersionHistoryPrefix)) != $commitVersionHistoryPrefix) {
                    $commits[$commitSha1] = $commitMsg;
                }

            }
        }

        return $commits;
    }

}
