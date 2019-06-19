<?php

namespace App\Controller;

use arajcany\ToolBox\Utility\TextFormatter;
use arajcany\ToolBox\Utility\ZipMaker;
use Cake\Event\Event;

/**
 * Releases Controller
 * Used to build a release of the application
 *
 */
class ReleasesController extends AppController
{

    /**
     * Initialize method
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        return null;
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|\Cake\Http\Response|null
     */
    public function index()
    {
        $gitBranch = $this->_getGitBranch();
        $gitCommits = $this->_getCommitsSinceLastBuild();
        $gitModified = $this->_getGitModified();
        $this->set('gitBranch', $gitBranch);
        $this->set('gitCommits', $gitCommits);
        $this->set('gitModified', $gitModified);

        return null;
    }


    /**
     * Build a release package
     */
    public function build()
    {

        if ($this->request->is('post')) {
            $log = [];
            $app_name = APP_NAME;

            $drive = explode(DS, ROOT);
            $drive = $drive[0] . DS;

            if ($this->request->getData('place_in_drive')) {
                if (strlen($this->request->getData('place_in_drive')) > 0) {
                    $drive = $this->request->getData('place_in_drive');
                    $drive = TextFormatter::makeEndsWith($drive, DS);
                }
            }

            $zm = new ZipMaker();
            $log[] = json_encode($this->request->getData(), JSON_PRETTY_PRINT);
            $log[] = __("Building a release of {0}.", APP_NAME);

            //----create a file list to zip---------------------------------
            $baseDir = ROOT;

            //ignore files and folders relative to the ROOT
            $ignoreFilesFolders = [
                "config\\app.php",
                "config\\config_local.php",
                "config\\CompareProjects_DB.sqlite",
                "bin\\installer\\",
                ".git\\",
                ".idea\\",
                "logs\\",
                "tmp\\",
                "tests\\",
                "src\\Controller\\ReleasesController.php",
                "src\\Template\\Releases\\",
            ];

            $fileList = $zm->makeFileList($baseDir, $ignoreFilesFolders);

            $fileList[] = [
                'external' => CONFIG . "empty",
                'internal' => "$app_name\\logs\\empty"
            ];
            $fileList[] = [
                'external' => CONFIG . "empty",
                'internal' => "$app_name\\tmp\\empty"
            ];
            $fileList[] = [
                'external' => CONFIG . "empty",
                'internal' => "$app_name\\tmp\\sessions\\empty"
            ];
            $fileList[] = [
                'external' => CONFIG . "empty",
                'internal' => "$app_name\\tmp\\cache\\empty"
            ];
            $fileList[] = [
                'external' => CONFIG . "empty",
                'internal' => "$app_name\\tmp\\configure.txt"
            ];
            //------------------------------------------------------------------------


            //----create the required zip files---------------------------------
            $date = date('Ymd_His');
            $zipUpdater = str_replace(" ", "_", "{$date}_{$app_name}.zip");
            $zipResult = $zm->makeZipFromFileList($fileList, "{$drive}{$zipUpdater}", $baseDir, $app_name);
            if ($zipResult) {
                $log[] = __('Created {0}', $zipUpdater);
            } else {
                $log[] = __('Could not create {0}', $zipUpdater);
            }
            //------------------------------------------------------------------------

            //save messages
            $this->request->getSession()->write('releasesBuildLog', implode(LS, $log));
            return $this->redirect(['action' => 'index']);

        }//end POST


        $drive = explode("\\", ROOT);
        $drive = str_replace(" ", "_", $drive[0] . DS . APP_NAME . "_Builds" . DS);
        $this->set('drive', $drive);

        $this->set('gitBranch', $this->_getGitBranch());
        $this->set('gitModified', $this->_getGitModified());

        return null;
    }


    /**
     * Return the current GIT branch
     *
     * @return string
     */
    private function _getGitBranch()
    {
        $gitBranch = array_reverse(explode("/", trim(file_get_contents(ROOT . DS . ".git" . DS . "HEAD"))))[0];
        return $gitBranch;
    }

    /**
     * Return a list of modified files
     *
     * @return array
     */
    private function _getGitModified()
    {
        $cmd = '"C:\Program Files\Git\bin\git.exe" status --porcelain ';
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


    private function _getCommitsSinceLastBuild()
    {
        $commits = [];

        if (is_file('C:\Program Files\Git\cmd\git.exe')) {
            $gitExeLocation = 'C:\Program Files\Git\cmd\git.exe';
        } elseif (is_file('C:\Program Files\Git\bin\git.exe')) {
            $gitExeLocation = 'C:\Program Files\Git\bin\git.exe';
        } else {
            return $commits;
        }

        $cmd = '"' . $gitExeLocation . '" log --format=oneline ';
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
