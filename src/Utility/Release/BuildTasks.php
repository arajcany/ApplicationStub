<?php


namespace App\Utility\Release;

use arajcany\ToolBox\Utility\TextFormatter;
use arajcany\ToolBox\Utility\ZipMaker;

/**
 * Class BuildTasks
 *
 * Builds a release ZIP file. Requires the location of a build file.
 * This way, the ZIP can be built via CLI or Web request.
 *
 * @property string $buildFile
 *
 * @package App\Utility\Release
 */
class BuildTasks
{
    private $buildFile = null;

    /**
     * DefaultApplication constructor.
     *
     * @param null $buildFile
     */
    public function __construct($buildFile = null)
    {
        $this->setBuildFile($buildFile);
    }

    /**
     * @return string
     */
    public function getBuildFile(): string
    {
        return $this->buildFile;
    }

    /**
     * @param string $buildFile
     */
    public function setBuildFile(string $buildFile)
    {
        $this->buildFile = $buildFile;
    }

    /**
     * Builds the release ZIP file according to the parameters specified in $this->buildFile
     *
     * @return mixed
     */
    public function build()
    {
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
            "config\\internal.sqlite",
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
        $fileList[] = [
            'external' => CONFIG . "empty",
            'internal' => "$app_name\\tmp\\cache\\clear_all.txt"
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
    }

}
