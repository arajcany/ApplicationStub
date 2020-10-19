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
    private $log = [];

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
     * @param $buildFile
     */
    public function setBuildFile($buildFile)
    {
        $this->buildFile = $buildFile;
    }

    /**
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * Write to the log variable
     *
     * @param $data
     */
    public function writeToLog($data)
    {
        if (is_object($data)) {
            $data = json_decode(json_encode($data));
        }

        $this->log[] = $data;
    }

    /**
     * Builds the release ZIP file according to the parameters specified in $this->buildFile
     *
     * @return bool
     */
    public function build()
    {
        $app_name = APP_NAME;

        $drive = explode(DS, ROOT);
        array_pop($drive);
        $drive = implode(DS, $drive);
        $drive = TextFormatter::makeEndsWith($drive, DS);

        $zm = new ZipMaker();
        $this->writeToLog(__("Building a release of {0}.", $app_name));

        //----create a file list to zip---------------------------------
        $baseDir = ROOT;

        //ignore files and folders relative to the ROOT
        $ignoreFilesFolders = [
            "config\\app.php",
            "config\\config_local.php",
            "config\\Stub_DB.sqlite",
            "config\\internal.sqlite",
            "bin\\installer\\",
            ".git\\",
            ".idea\\",
            "logs\\",
            "tmp\\",
            "tests\\",
            "src\\Controller\\ReleasesController.php",
            "src\\Command\\ReleasesCommand.php",
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
        $zipFileName = str_replace(" ", "_", "{$date}_{$app_name}.zip");
        $zipResult = $zm->makeZipFromFileList($fileList, "{$drive}{$zipFileName}", $baseDir, $app_name);
        if ($zipResult) {
            $this->writeToLog(__('Created {0}', $zipFileName));
            $return = true;
        } else {
            $this->writeToLog(__('Could not create {0}', $zipFileName));
            $return = false;
        }
        //------------------------------------------------------------------------

        return $return;
    }

}
