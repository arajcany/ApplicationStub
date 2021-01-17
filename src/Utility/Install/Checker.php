<?php

namespace App\Utility\Install;

use App\Utility\Network\Connection as NetworkConnection;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validation;
use Exception;
use phpseclib\Net\SFTP;

/**
 * Class Checker
 *
 * @package App\Utility\Installer
 *
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\InternalOptionsTable $InternalOptions
 */
class Checker
{
    private $Settings;
    private $InternalOptions;
    private $messages = [];

    /**
     * DefaultApplication constructor.
     */
    public function __construct()
    {
        $this->Settings = TableRegistry::getTableLocator()->get('Settings');
        $this->InternalOptions = TableRegistry::getTableLocator()->get('InternalOptions');
    }


    /**
     * Check if the username is styled like a domain
     *  - user@company
     *  - company\user
     *
     * @param string $user
     * @return bool
     */
    public function checkNameIsDomainStyle($user = '')
    {
        if (strpos($user, "@") !== false || strpos($user, "\\") !== false) {
            $isDomainUsername = true;
        } else {
            $isDomainUsername = false;
        }

        return $isDomainUsername;
    }


    /**
     * Check if the passed in $user is a valid Windows Administrator
     * There is no way to test a Username/Password combination
     *
     * @param string $user
     * @return bool
     */
    public function checkNameIsValidWindowsAdmin($user = '')
    {
        $cmdCheckAdmin = "net user {$user} 2>&1";
        $userData = [];
        $ret = '';
        exec($cmdCheckAdmin, $userData, $ret);
        if ($ret == 0) {
            foreach ($userData as $dataChunk) {
                if (substr($dataChunk, 0, strlen('Local Group Memberships')) == 'Local Group Memberships') {
                    $localGroupMemberships = str_replace("Local Group Memberships", "", $dataChunk);
                    $localGroupMemberships = trim($localGroupMemberships, " *");
                    $localGroupMemberships = explode("*", $localGroupMemberships);

                    foreach ($localGroupMemberships as $localGroupMembership) {
                        $localGroupMembership = trim($localGroupMembership);

                        if ($localGroupMembership == 'Administrators') {
                            return true;
                        }
                    }
                    return false;
                }
            }
        } else {
            return false;
        }

    }

    public function checkPhp()
    {
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $return = true;
            $message = __("Your version of PHP is 7.0.0 or higher (detected {0}).", PHP_VERSION);
            $this->addToMessages(['message' => $message, 'key' => 'checkPhp', 'element' => 'success']);
        } else {
            $return = false;
            $message = __("You need PHP 7.0.0 or higher to use CakePHP (detected {0}).", PHP_VERSION);
            $this->addToMessages(['message' => $message, 'key' => 'checkPhp', 'element' => 'error']);
        }

        return $return;
    }


    /**
     * Round trip check of SFTP/URL.
     * Saves a file to SFTP and tries to read it back via HTTP.
     *
     * @param array $settings
     * @return bool
     */
    public function checkSftpSettings($settings)
    {
        //host null
        if (is_null($settings['host']) || empty($settings['host']) || strtolower($settings['host']) == 'null') {
            $message = __("SFTP host is NULL or empty");
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //port null
        if (is_null($settings['port']) || empty($settings['port']) || strtolower($settings['port']) == 'null') {
            $message = __("SFTP port is NULL or empty");
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //timeout null
        if (is_null($settings['timeout']) || empty($settings['timeout']) || strtolower($settings['timeout']) == 'null') {
            $message = __("SFTP timeout is NULL or empty");
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        $rnd = sha1(mt_rand(1000, 9999));
        $rndDirectory = "test_" . $rnd;
        $rndUrlFile = "{$settings['url']}{$rndDirectory}/{$rnd}.txt";
        $rndSftpFile = "{$rnd}.txt";

        //check login to sFTP server
        $SFTP = new SFTP($settings['host'], $settings['port'], $settings['timeout']);
        if (@!$SFTP->login($settings['username'], $settings['password'])) {
            $message = __("Could not login to the sFTP server {0}.", $settings['host']);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //check basic network connectivity
        $isConn = NetworkConnection::checkUrlConnection($settings['url']);
        if (!$isConn) {
            $message = __("Cannot read from the URL path {0}", $settings['url']);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //change directory
        if (!$SFTP->chdir($settings['path'])) {
            $message = __("Could not change to the path {0}.", $settings['path']);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //create test directory
        if (!$SFTP->mkdir($rndDirectory, 0777, true)) {
            $message = __("Could not make directory {0}.", $rndDirectory);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //change to test directory
        if (!$SFTP->chdir($rndDirectory)) {
            $message = __("Could not change to the path {0}.", $rndDirectory);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        } else {
            $pwd = $SFTP->pwd();
        }

        //put a file
        if (!$SFTP->put($rndSftpFile, $rnd)) {
            $message = __("Could not write to path {0}.", $pwd);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //read file via http/s
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $readResult = @file_get_contents($rndUrlFile, false, stream_context_create($arrContextOptions));
        if ($readResult == false) {
            $message = __("Could not read {0}.", $rndUrlFile);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //delete file
        if (!$SFTP->delete($rndSftpFile)) {
            $message = __("Could not delete from path {0}.", $settings['path']);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //delete the directory
        if (!$SFTP->delete($pwd)) {
            $message = __("Could not delete the path {0}.", $pwd);
            $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'error']);
            return false;
        }

        //all passed so return true
        $sftpString = "sftp://" . $settings['host'] . $settings['path'];
        $message = __("Repository sFTP and URL paths confirmed {0} {1}.", $sftpString, $settings['url']);
        $this->addToMessages(['message' => $message, 'key' => 'checkSftpSettings', 'element' => 'success']);
        return true;
    }


    /**
     * Add data to the message stack. Messages can be used as Flash messages for GUI feedback.
     *
     * @param array $messageData
     */
    private function addToMessages(array $messageData = [])
    {
        $this->messages[] = $messageData;
    }


    /**
     * Retrieve messages in the stack.
     *
     * @param bool $clear
     * @return array
     */
    public function getMessages($clear = true)
    {
        $messages = $this->messages;
        if ($clear == true) {
            $this->messages = [];
        }

        return $messages;
    }

}
