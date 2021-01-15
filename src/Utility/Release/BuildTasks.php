<?php


namespace App\Utility\Release;

use arajcany\ToolBox\Utility\TextFormatter;
use arajcany\ToolBox\Utility\ZipMaker;
use Cake\Utility\Inflector;
use Cake\Console\ConsoleIo;
use Cake\Console\Arguments;

/**
 * Class BuildTasks
 *
 * Builds a release ZIP file. Requires the location of a build file.
 * This way, the ZIP can be built via CLI or Web request.
 *
 * @property string $buildFile
 * @property ConsoleIo $io
 * @property Arguments $args
 *
 * @package App\Utility\Release
 */
class BuildTasks
{
    private $buildFile = null;
    private $log = [];
    private $args = null;
    private $io = null;

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
     * @param mixed $args
     */
    public function setArgs(Arguments $args)
    {
        $this->args = $args;
    }

    /**
     * @param mixed $io
     */
    public function setIo(ConsoleIo $io)
    {
        $this->io = $io;
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

        if (is_cli()) {
            if ($this->io) {
                $this->io->out($data);
            }
        }

        $this->log[] = $data;
    }

    /**
     * Builds the release ZIP file according to the parameters specified in $this->buildFile
     *
     * @param array $options
     * @return bool
     */
    public function build($options = [])
    {
        $app_name = Inflector::underscore(APP_NAME);

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
            "bin\\BuildRelease.bat",
            "src\\Controller\\DevelopersController.php",
            "src\\Template\\Developers\\",
        ];

        if (isset($options['gitIgnored'])) {
            $ignoreFilesFolders = array_merge($ignoreFilesFolders, $options['gitIgnored']);
        }

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
            $this->writeToLog(__('Zip Directory: {0}', $drive));
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
