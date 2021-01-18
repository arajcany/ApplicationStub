<?php

namespace App\Utility\Install;

use arajcany\ToolBox\Utility\TextFormatter;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Utility\Text;

/**
 * Class Version
 *
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\InternalOptionsTable $InternalOptions
 * @property  IniConfig $IniConfig
 *
 * @package App\Utility\Install
 */
class VersionControl
{
    public $InternalOptions;
    public $Settings;
    public $IniConfig;

    public function __construct()
    {
        $this->InternalOptions = TableRegistry::getTableLocator()->get('InternalOptions');
        $this->Settings = TableRegistry::getTableLocator()->get('Settings');
        $this->IniConfig = new IniConfig();
    }

    /**
     * Full path and filename of the version.json file
     *
     * @return string
     */
    public function getVersionJsnFullPath()
    {
        return CONFIG . 'version.json';
    }

    /**
     * Full path and filename of the version.json file
     *
     * @return string
     */
    public function getVersionIniFilename()
    {
        return pathinfo($this->getVersionJsnFullPath(), PATHINFO_FILENAME);
    }

    /**
     * Full path and filename of the version.json file
     *
     * @return string
     */
    public function getVersionHistoryJsnFullPath()
    {
        return CONFIG . 'version_history.json';
    }

    /**
     * Full path and filename of the version.json file
     *
     * @return string
     */
    public function getVersionHistoryJsnFilename()
    {
        return pathinfo($this->getVersionHistoryJsnFullPath(), PATHINFO_FILENAME);
    }

    /**
     * Return the contents of version.json in array format.
     * If version.json doe not exist, default is created and returned.
     *
     * @return array
     */
    public function getVersionJsn()
    {
        $fileToRead = $this->getVersionJsnFullPath();
        if (is_file($fileToRead)) {
            $versionData = json_decode(file_get_contents($fileToRead), JSON_OBJECT_AS_ARRAY);;
        } else {
            $versionData = $this->getDefaultVersionJsn();
            $this->putVersionJsn($versionData);
        }

        return $versionData;
    }

    /**
     * Write the version.json file
     *
     * Should call $this->validateIni($data) to validate $data first
     *
     * @param array $data
     * @return bool
     */
    public function putVersionJsn($data = [])
    {
        $fileToWrite = $this->getVersionJsnFullPath();
        return file_put_contents($fileToWrite, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Return the contents of version_history.json in array format.
     * If version_history.json doe not exist, default is created and returned.
     *
     * @return array
     */
    public function getVersionHistoryJsn()
    {
        $fileToRead = $this->getVersionHistoryJsnFullPath();
        if (is_file($fileToRead)) {
            $versionData = json_decode(file_get_contents($fileToRead), JSON_OBJECT_AS_ARRAY);;
        } else {
            $versionData = [$this->getDefaultVersionJsn()];
            $this->putVersionHistoryJsn($versionData);
        }

        return $versionData;
    }

    /**
     * Write the version_history.json file
     *
     * Should call $this->validateIni($data) to validate $data first
     *
     * @param array $data
     * @return bool
     */
    public function putVersionHistoryJsn($data = [])
    {
        $fileToWrite = $this->getVersionHistoryJsnFullPath();
        return file_put_contents($fileToWrite, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Return the contents of version_history.json in hashed TXT format.
     *
     * @return string
     */
    public function getVersionHistoryHashtxt()
    {
        $versionData = $this->getVersionHistoryJsn();
        $versionData = json_encode($versionData);
        $versionData = \arajcany\ToolBox\Utility\Security\Security::encrypt64Url($versionData);

        return $versionData;
    }

    /**
     * Format of the version.json
     *
     * @return array
     */
    public function getDefaultVersionJsn()
    {
        return [
            'name' => APP_NAME,
            'tag' => '0.0.0',
            'desc' => APP_DESC,
            'codename' => '',
        ];
    }

    /**
     * Get the current version tag
     *
     * @return string
     */
    public function getCurrentVersionTag()
    {
        $version = $this->getVersionJsn();
        return $version['tag'];
    }


    /**
     * Sort the Version History
     *
     * @param $unsorted
     * @return array
     */
    public function sortVersionHistoryArray($unsorted)
    {
        $keys = array_keys($unsorted);
        natsort($keys);
        $keys = array_reverse($keys);

        $sorted = [];
        foreach ($keys as $key) {
            $sorted[$key] = $unsorted[$key];
        }

        return $sorted;
    }


    /**
     * Increment a classic software version number
     *
     * @param string $number in the format xx.xx.xx
     * @param string $part the part to increment, major | minor | patch
     * @return string
     */
    public function incrementVersion($number, $part = 'patch')
    {
        $numberParts = explode('.', $number);

        if ($part == 'major') {
            $numberParts[0] += 1;
            $numberParts[1] = 0;
            $numberParts[2] = 0;
        }

        if ($part == 'minor') {
            $numberParts[1] += 1;
            $numberParts[2] = 0;
        }

        if ($part == 'patch') {
            $numberParts[2] += 1;
        }

        return implode('.', $numberParts);
    }


    /**
     * Display the versionHistory.json file in encrypted format
     *
     * @return false|array
     */
    public function _getOnlineVersionHistoryHash()
    {

        $remote_update_url = $this->Settings->getSetting('remote_update_url');
        $versionHistoryHash = @file_get_contents($remote_update_url . "version_history_hash.txt");

        if ($versionHistoryHash) {
            $versionHistoryHash = \arajcany\ToolBox\Utility\Security\Security::decrypt64Url($versionHistoryHash);
            $versionHistoryHash = @json_decode($versionHistoryHash, JSON_OBJECT_AS_ARRAY);
            if (is_array($versionHistoryHash)) {
                return $versionHistoryHash;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Publish the version history hash to the sFTP site
     *
     * @return bool
     */
    public function _publishOnlineVersionHistoryHash()
    {
        $sftp = $this->InternalOptions->getUpdateSftpSession();
        if ($sftp === false) {
            return false;
        }

        //publish the hash
        $versionHistoryHash = $this->_getLocalVersionHistoryHash();
        if (strlen($versionHistoryHash) > 0) {
            $result = $sftp->put('versionHistory' . APP_NAME . '.hash', $versionHistoryHash);
            if ($result == 1) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

}
