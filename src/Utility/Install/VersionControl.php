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
     * Full path and filename of the version.ini file
     *
     * @return string
     */
    public function getVersionIniFullPath()
    {
        return CONFIG . 'version.ini';
    }

    /**
     * Full path and filename of the version.ini file
     *
     * @return string
     */
    public function getVersionIniFilename()
    {
        return 'version';
    }

    /**
     * Return the contents of version.ini in array format.
     * If version.ini doe not exist, default is created and returned.
     *
     * @return array
     */
    public function getVersionIni()
    {
        $fileToRead = $this->getVersionIniFullPath();
        if (is_file($fileToRead)) {
            $versionIni = $this->IniConfig->read($this->getVersionIniFilename());
        } else {
            $versionIni = $this->getDefaultVersionIni();
            $this->putVersionIni($versionIni);
        }

        $versionIni = Hash::expand($versionIni);

        return $versionIni;
    }

    /**
     * Write the version.ini file
     *
     * Should call $this->validateIni($data) to validate $data first
     *
     * @param array $data
     * @return bool
     */
    public function putVersionIni($data = [])
    {
        $versionIniFormat = $this->getDefaultVersionIni();
        $data = array_merge($versionIniFormat, $data);
        $data = Hash::flatten($data);

        $fileToWrite = $this->getVersionIniFullPath();

        $result = [];
        foreach ($data as $k2 => $v) {
            $result[] = "$k2 = " . $this->toIniValue($v);
        }

        $contents = trim(implode("\n", $result)) . "\n";
        return file_put_contents($fileToWrite, $contents) > 0;
    }

    /**
     * Format of the version.ini
     *
     * @return array
     */
    private function getDefaultVersionIni()
    {
        return [
            'name' => APP_NAME,
            'tag' => '0.0.1',
            'desc' => APP_DESC,
            'codename' => '',
        ];
    }

    /**
     * Validate if the passed in array can be successfully written as an INI file
     *
     * @param array $ini
     * @return bool
     */
    public function validateIni($ini)
    {
        $hashedData = Hash::flatten($ini);

        $values = array_values($hashedData);
        if (!$this->isValidIniValue($values)) {
            return false;
        }

        $keys = array_keys($hashedData);
        if (!$this->isValidIniKey($keys)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a Key passes
     *
     * @param $keyToCheck
     * @return bool
     */
    public function isValidIniKey($keyToCheck)
    {
        $specialCharacters = $this->getIniSpecialCharacters();

        if (is_string($keyToCheck)) {
            $keys = [$keyToCheck];
        } elseif (is_array($keyToCheck)) {
            $keys = $keyToCheck;
        } else {
            return false;
        }

        foreach ($keys as $key) {
            foreach ($specialCharacters as $specialCharacter) {
                //contains
                if ($key !== str_replace($specialCharacter, '', $key)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if a Value passes
     *
     * @param $valueToCheck
     * @return bool
     */
    public function isValidIniValue($valueToCheck)
    {
        $reservedWords = $this->getIniReservedWords();

        if (is_string($valueToCheck)) {
            $values = [$valueToCheck];
        } elseif (is_array($valueToCheck)) {
            $values = $valueToCheck;
        } else {
            return false;
        }

        foreach ($values as $value) {
            foreach ($reservedWords as $reservedWord) {
                //in sentence
                if ($value !== str_replace(" " . $reservedWord . " ", '', $value)) {
                    return false;
                }

                //startsWith
                if (TextFormatter::startsWith($value, $reservedWord . " ")) {
                    return false;
                }

                //endsWith
                if (TextFormatter::endsWith($value, " " . $reservedWord)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Converts a value into the ini equivalent
     *
     * @param mixed $value Value to export.
     * @return string String value for ini file.
     */
    protected function toIniValue($value)
    {
        if ($value === null) {
            return 'null';
        }
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }

        return (string)$value;
    }

    /**
     * Returns an array of reserved words.
     *
     * From php.net
     * Note: There are reserved words which must not be used as keys for ini files.
     * These include: null, yes, no, true, false, on, off, none.
     * Values null, off, no and false result in "",
     * and values on, yes and true result in "1",
     * unless INI_SCANNER_TYPED mode is used (as of PHP 5.6.1).
     * Characters ?{}|&~!()^" must not be used anywhere in the key
     * and have a special meaning in the value.
     *
     *
     * e.g. the following is OK
     * foo = true
     * bar = off
     *
     * e.g. the following is not OK
     * foo = it is true that this will fail
     * bar = turn the kettle off
     *
     * @return array
     */
    public function getIniReservedWords()
    {
        return ["null", "yes", "no", "true", "false", "on", "off", "none"];
    }

    /**
     * Returns an array of special characaters
     *
     * See note above.
     *
     * @return array
     */
    public function getIniSpecialCharacters()
    {
        return str_split('?{}|&~!()^"');
    }

    /**
     * Wrapper function to get the versionHistory.ini as array format
     *
     * @return array|bool|string
     */
    public function _getLocalVersionHistoryIni()
    {
        $versionHistoryIni = $this->_getLocalVersionHistoryHash(false);
        $versionHistoryIni = parse_ini_string($versionHistoryIni, true, INI_SCANNER_RAW);
        $versionHistoryIni = $this->sortVersionHistoryArray($versionHistoryIni);

        return $versionHistoryIni;
    }


    /**
     * Display the versionHistory.ini file in encrypted format
     *
     * This will only return a result in the DEV environment as the
     * versionHistory.ini is removed in the PROD releases.
     *
     * @param bool $encrypted
     * @return bool|string
     */
    public function _getLocalVersionHistoryHash($encrypted = true)
    {
        $key = $this->InternalOptions->getKey();
        $salt = $this->InternalOptions->getSalt();

        $fileToRead = ROOT . DS . "bin" . DS . "installer" . DS . 'versionHistory.ini';
        if (is_file($fileToRead)) {
            $hash = file_get_contents($fileToRead);
            if ($encrypted !== false) {
                $hash = base64_encode(Security::encrypt($hash, $key, $salt));
                $hash = Text::wrap($hash, ['width' => 72, 'wordWrap' => false]);
            }
        } else {
            $hash = '';
        }

        return $hash;
    }


    public function _putLocalVersionHistoryHash(array $data)
    {
        $fileToWrite = ROOT . DS . "bin" . DS . "installer" . DS . 'versionHistory.ini';

        $data = $this->sortVersionHistoryArray($data);

        $result = [];
        foreach ($data as $k => $value) {
            $isSection = false;
            if ($k[0] !== '[') {
                $result[] = "[$k]";
                $isSection = true;
            }
            if (is_array($value)) {
                $kValues = Hash::flatten($value, '.');
                foreach ($kValues as $k2 => $v) {
                    $result[] = "$k2 = " . $this->toIniValue($v);
                }
            }
            if ($isSection) {
                $result[] = '';
            }
        }
        $contents = trim(implode("\n", $result)) . "\r\n";
        return file_put_contents($fileToWrite, $contents) > 0;
    }


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
     * Display the versionHistory.ini file in encrypted format
     *
     * @param bool $encrypted
     * @return array|bool|mixed|string
     */
    public function _getOnlineVersionHistoryHash($encrypted = true)
    {
        $sftp = $this->InternalOptions->getUpdateSftpSession();
        if ($sftp === false) {
            return false;
        }

        $fileList = $sftp->rawlist();
        if (isset($fileList['versionHistory' . APP_NAME . '.hash'])) {

            $key = $this->InternalOptions->getKey();
            $salt = $this->InternalOptions->getSalt();

            $versionHistoryHash = $sftp->get('versionHistory' . APP_NAME . '.hash');

            if ($encrypted == false) {
                $versionHistoryHash = preg_replace("/\r|\n/", "", $versionHistoryHash);
                $versionHistoryHash = base64_decode($versionHistoryHash);
                $versionHistoryHash = Security::decrypt($versionHistoryHash, $key, $salt);
                $versionHistoryHash = parse_ini_string($versionHistoryHash, true, INI_SCANNER_RAW);
            }

            return $versionHistoryHash;

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
