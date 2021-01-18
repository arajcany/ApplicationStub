<?php


namespace App\Utility\Release;

use App\Model\Table\InternalOptionsTable;
use arajcany\ToolBox\Utility\Security\Security;
use arajcany\ToolBox\Utility\TextFormatter;
use arajcany\ToolBox\Utility\ZipMaker;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Console\ConsoleIo;
use Cake\Console\Arguments;
use Composer\Config;
use phpseclib\Net\SFTP;
use App\Utility\Install\VersionControl;

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

        if (is_cli()) {
            if ($this->io) {
                $this->io->out($data);
            }
        }

        $this->log[] = $data;
    }

    /**
     * @return int
     */
    public function debugOff()
    {
        $contents = file_get_contents(CONFIG . 'app.php');
        $contents = str_replace("filter_var(env('DEBUG', true)", "filter_var(env('DEBUG', false)", $contents);
        $result = file_put_contents(CONFIG . 'app.php', $contents);
        if ($result) {
            $this->io->out("Debugging turned off.");
            return 0;
        } else {
            $this->io->out("Failed to turn Debugging turned off.");
            return 1;
        }
    }

    /**
     * @return int
     */
    public function debugOn()
    {
        $contents = file_get_contents(CONFIG . 'app.php');
        $contents = str_replace("filter_var(env('DEBUG', false)", "filter_var(env('DEBUG', true)", $contents);
        $result = file_put_contents(CONFIG . 'app.php', $contents);
        if ($result) {
            $this->io->out("Debugging turned on.");
            return 0;
        } else {
            $this->io->out("Failed to turn Debugging turned on.");
            return 1;
        }
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

        $VC = new VersionControl();
        $versionHistoryData = $VC->getVersionHistoryJsn();
        $newVersionData = $VC->getDefaultVersionJsn();
        $currentTag = $VC->getCurrentVersionTag();

        $sampleMajor = $VC->incrementVersion($currentTag, 'major');
        $sampleMinor = $VC->incrementVersion($currentTag, 'minor');
        $samplePatch = $VC->incrementVersion($currentTag, 'patch');

        $this->io->out(__('Major Build => {0}', $sampleMajor));
        $this->io->out(__('Minor Build => {0}', $sampleMinor));
        $this->io->out(__('Patch Build => {0}', $samplePatch));

        $tagUpgrade = $this->io->askChoice('Is this a Major, Minor or Patch Build?', ['Major', 'Minor', 'Patch'], 'Patch');
        $desc = $this->io->ask('Please type out a description for this release.');
        $codename = $this->io->ask('Please type out a codename for this release (optional).');

        $newVersionData['tag'] = $VC->incrementVersion($currentTag, strtolower($tagUpgrade));
        $newVersionData['desc'] = $desc;
        $newVersionData['codename'] = $codename;

        $VC->putVersionJsn($newVersionData);

        $this->writeToLog(__("Building {0} version {1}.", APP_NAME, $newVersionData['tag']));

        $drive = explode(DS, ROOT);
        array_pop($drive);
        $drive = implode(DS, $drive);
        $drive = TextFormatter::makeEndsWith($drive, DS);

        $zm = new ZipMaker();

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
            'external' => CONFIG . "internal.sqlite",
            'internal' => "$app_name\\config\\internal.sqlite"
        ];
        $fileList[] = [
            'external' => CONFIG . "version.json",
            'internal' => "$app_name\\config\\version.json"
        ];
        $fileList[] = [
            'external' => CONFIG . "version_history.json",
            'internal' => "$app_name\\config\\version_history.json"
        ];
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
        $this->writeToLog(__("Compiled a list of {0} files to Zip.", count($fileList)));


        //----backup Internal DB---------------------------------
        /**
         * @var InternalOptionsTable $InternalOptions
         */
        copy(CONFIG . "internal.sqlite", CONFIG . "internal.bak.sqlite");
        $InternalOptions = TableRegistry::getTableLocator()->get('InternalOptions');
        $InternalOptions->deleteAll(["option_key LIKE 'remote_update_%'"]);
        //------------------------------------------------------------------------


        //----create the required zip files---------------------------------
        $this->writeToLog(__('Zipping files to {0}', $drive));
        $date = date('Ymd_His');
        $zipFileName = str_replace(" ", "_", "{$date}_{$app_name}_v{$newVersionData['tag']}.zip");
        $zipResult = $zm->makeZipFromFileList($fileList, "{$drive}{$zipFileName}", $baseDir, $app_name);
        if ($zipResult) {
            $this->writeToLog(__('Created {0}', "{$drive}{$zipFileName}"));
            $return = true;
        } else {
            $this->writeToLog(__('Could not create {0}', "{$drive}{$zipFileName}"));
            $return = false;
        }
        //------------------------------------------------------------------------


        //----restore Internal DB---------------------------------
        /**
         * @var InternalOptionsTable $InternalOptions
         */
        copy(CONFIG . "internal.bak.sqlite", CONFIG . "internal.sqlite");
        unlink(CONFIG . "internal.bak.sqlite");
        //------------------------------------------------------------------------


        //----automatic upload to remote update site---------------------------------
        if ($options['remoteUpdateUnc']) {
            $this->writeToLog(__("Uploading Zip to Remote Update site via UNC"));
            $copyResult = copy("{$drive}{$zipFileName}", "{$options['remoteUpdateUnc']['unc']}{$zipFileName}");
            if ($copyResult) {
                $this->writeToLog(__('Copied Zip to {0}', "{$options['remoteUpdateUnc']['unc']}{$zipFileName}"));
                $newVersionData['installer_url'] = $options['remoteUpdateUnc']['url'] . $zipFileName;
                $newVersionData['release_date'] = $date;
            } else {
                $this->writeToLog(__('Failed to copy Zip to {0}', "{$options['remoteUpdateUnc']['unc']}{$zipFileName}"));
            }
        } elseif ($options['remoteUpdateSftp']) {
            $this->writeToLog(__("Uploading Zip to Remote Update site via SFTP"));
            $SFTP = new SFTP($options['remoteUpdateSftp']['host'], $options['remoteUpdateSftp']['port'], $options['remoteUpdateSftp']['timeout']);
            $SFTP->login($options['remoteUpdateSftp']['username'], $options['remoteUpdateSftp']['password']);
            $SFTP->chdir($options['remoteUpdateSftp']['path']);

            $copyResult = $SFTP->put($zipFileName, file_get_contents("{$drive}{$zipFileName}"));
            if ($copyResult) {
                $this->writeToLog(__('Uploaded Zip to sftp://{0}', "{$options['remoteUpdateSftp']['host']}"));
                $newVersionData['installer_url'] = $options['remoteUpdateSftp']['url'] . $zipFileName;
                $newVersionData['release_date'] = $date;
            } else {
                $this->writeToLog(__('Failed to upload Zip to sftp://{0}', "{$options['remoteUpdateSftp']['host']}"));
            }
        } else {
            $this->writeToLog(__("No automatic upload as UNC or SFTP are not configured."));
        }
        //------------------------------------------------------------------------


        //----save version history---------------------------------
        $versionHistoryData[] = $newVersionData;
        $VC->putVersionHistoryJsn($versionHistoryData);
        $versionHistoryHashData = $VC->getVersionHistoryHashtxt();

        if ($options['remoteUpdateUnc']) {
            $this->writeToLog(__("Uploading Version History Hash to Remote Update site via UNC"));
            file_put_contents("{$options['remoteUpdateUnc']['unc']}version_history_hash.txt", $versionHistoryHashData);
        } elseif ($options['remoteUpdateSftp']) {
            $this->writeToLog(__("Uploading Version History Hash to Remote Update site via SFTP"));
            $SFTP = new SFTP($options['remoteUpdateSftp']['host'], $options['remoteUpdateSftp']['port'], $options['remoteUpdateSftp']['timeout']);
            $SFTP->login($options['remoteUpdateSftp']['username'], $options['remoteUpdateSftp']['password']);
            $SFTP->chdir($options['remoteUpdateSftp']['path']);
            $SFTP->put('version_history_hash.txt', $versionHistoryHashData);
        } else {
            $this->writeToLog(__("No automatic uploading of Version History Hash as UNC or SFTP are not configured."));
        }
        //------------------------------------------------------------------------

        return $return;
    }

}
