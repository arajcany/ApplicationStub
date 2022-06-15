<?php

namespace App\Controller;

use App\Controller\Component\BackgroundServicesComponent;
use App\Model\Entity\User;
use App\Utility\Install\VersionControl;
use arajcany\ToolBox\Utility\Security\Security;
use arajcany\ToolBox\Utility\TextFormatter;
use arajcany\ToolBox\ZipPackager;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Installers Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Utility\Install\VersionControl $Version
 * @property BackgroundServicesComponent $BackgroundServices
 */
class InstallersController extends AppController
{
    use MailerAwareTrait;

    public $Users;
    public $Version;
    public $Installers;
    public $Settings;
    public $BackgroundServices;


    /**
     * Initialize method
     *
     * @return \Cake\Http\Response|null
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->Version = new VersionControl();
        $this->Users = TableRegistry::getTableLocator()->get('Users');

        $this->loadComponent('BackgroundServices');

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        return $this->redirect(['controller' => '']);
    }


    /**
     * Perform this on the first-run or upgrade of the application
     * This Controller/Action is declared as 'allowed' in auth_allow.ini, therefore it only contains the minimum
     * configuration options. The application will still need to be configured through the 'checks' action.
     * There is a safeguard against running this action once the initial configure is completed. If there is no error
     * with the bare minimum configuration, this action will automatically redirect to the login page.
     *
     * @return \Cake\Http\Response|null
     */
    public function configure()
    {
        if ($this->request->is('post') && isset($this->request->getData()['configure'])) {
            //allow to pass as an update is being called
        } elseif (Cache::read('first_run', 'quick_burn') !== true) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }

        /**
         * @var User $user
         */
        $user = $this->Users->findUsersByRoleNameOrRoleAlias("SuperAdmin")->order('Users.id', true)->first();

        //bare minimum checks. SuperAdmin has a password.
        if (strlen($user->password) >= 40) {
            Cache::delete('first_run', 'quick_burn');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }

        $dbDriver = ($this->Users->getConnection())->getDriver();
        if ($dbDriver instanceof Sqlite) {
            $caseSensitive = true;
        } else {
            $caseSensitive = false;
        }
        $this->set('caseSensitive', $caseSensitive);

        $this->viewBuilder()->setLayout('login');

        if ($this->request->is('post')) {
            if (isset($this->request->getData()['configure'])) {
                $p1 = $this->request->getData()['password'];
                $p2 = $this->request->getData()['password_2'];

                if ($p1 !== $p2) {
                    $this->Flash->error(__('Sorry, passwords do not match, please try again.'));
                }

                $patching = [
                    'password' => $p1,
                    'password_expiry' => $this->Settings->getPasswordExpiryDate(),
                    'activation' => $this->Settings->getAccountActivationDate(),
                    'expiration' => $this->Settings->getAccountExpirationDate(),
                ];
                $this->Users->patchEntity($user, $patching);

                if ($user->hasErrors()) {
                    $msg = __("Sorry there were issues with the password.");
                    $this->Flash->error($msg);
                }

                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Password updated for {0}. Please login.', $user->username));
                    Cache::delete('first_run', 'quick_burn');
                    return $this->redirect(['controller' => 'users', 'action' => 'login']);
                }
            }
        }

        $this->set('user', $user);

        return null;
    }


    /**
     * TODO Perform system checks.
     *
     * This action is behind AUTH/ACL controlled.
     *
     * @return \Cake\Http\Response|null
     */
    public function checks()
    {

    }

    /**
     * Display a list of Updates
     */
    public function updates()
    {
        $hash = $this->Version->_getOnlineVersionHistoryHash();
        if ($hash) {
            $hash = @array_reverse($hash);
        } else {
            $hash = [];
        }
        $this->set('versions', $hash);

        $remote_update_url = TextFormatter::makeEndsWith($this->Settings->getSetting('remote_update_url'), "/");
        $this->set('remote_update_url', $remote_update_url);

        $settingRemoteUpdateUrl = $this->Settings->find('all')->where(['property_key' => 'remote_update_url'])->first();
        $this->set('remote_update_url_id', $settingRemoteUpdateUrl->id);

        $this->set('currentVersion', $this->Version->getCurrentVersionTag());

        return null;
    }


    /**
     * Upgrade to the requested version
     *
     * @param null $upgradeFile
     * @return \Cake\Http\Response|\Cake\Http\Response|null
     */
    public function upgrade($upgradeFile = null)
    {
        $time_start = microtime(true);

        if (strtolower(Configure::read('mode')) !== 'uat' && strtolower(Configure::read('mode')) !== 'prod') {
            $this->Flash->error(__('You are not allowed to Upgrade!'));
            return $this->redirect(['action' => 'updates']);
        } elseif (empty(Configure::read('mode'))) {
            $this->Flash->error(__('Please add a Config value of  ["mode"=>"prod"] to allow upgrading'));
            return $this->redirect(['action' => 'updates']);
        }

        $upgradeFile = Security::decrypt64Url($upgradeFile);
        $versionHistory = $this->Version->_getOnlineVersionHistoryHash();
        $tag = 0;
        foreach ($versionHistory as $version) {
            if (isset($version['installer_url']) && $upgradeFile == $version['installer_url']) {
                $tag = $version['tag'];
            }
        }

        if (!$upgradeFile) {
            $this->Flash->error(__('Sorry, invalid upgrade file.'));
            return $this->redirect(['action' => 'updates']);
        } else {
            $arrContextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $zipFileContents = file_get_contents($upgradeFile, false, stream_context_create($arrContextOptions));

            if (!$zipFileContents) {
                $remote_update_url = TextFormatter::makeEndsWith($this->Settings->getSetting('remote_update_url'), "/");
                $zipFileContents = file_get_contents($remote_update_url . pathinfo($upgradeFile, PATHINFO_BASENAME), false, stream_context_create($arrContextOptions));
            }

            $zipFilePathName = TMP . pathinfo($upgradeFile, PATHINFO_BASENAME);
            if ($zipFileContents) {
                $this->Flash->success(__('Downloaded the upgrade file.'));
                file_put_contents($zipFilePathName, $zipFileContents);
            } else {
                $this->Flash->error(__('Sorry, could not download the upgrade file. {0}', $remote_update_url . pathinfo($upgradeFile, PATHINFO_BASENAME)));
                $this->Flash->error(__('{0}', $zipFileContents));
                return $this->redirect(['action' => 'updates']);
            }
        }

        $baseExtractDir = ROOT . DS;

        $count = $this->BackgroundServices->stop('all', false);
        if ($count > 0) {
            $this->Flash->success(__('Stopped {0} Background Services.', $count));
        }

        $zipPackager = new ZipPackager();
        $result = $zipPackager->extractZipDifference($zipFilePathName, $baseExtractDir, true);

        $diffReport = $zipPackager->getZipFsoDifference($zipFilePathName, $baseExtractDir, true);
        $toRemove = $diffReport['fsoExtra'];
        $toRemove = str_replace($baseExtractDir, "", $toRemove);
        $countRemoved = $this->removeUnusedFiles($toRemove);

        $msg = '';
        if ($result['status']) {
            $msg .= __('Zip update extracted successfully. ');
        } else {
            $msg .= __('Zip update extracted with errors. ');
        }
        $msg .= __('{0} files extracted, {1} files failed to extract. ', count($result['extract_passed']), count($result['extract_failed']));
        $msg .= __('{0} files removed. ', $countRemoved);
        $msg = trim($msg);

        if ($result['status']) {
            $this->Flash->success($msg);
        } else {
            $this->Flash->warning($msg);
        }

        $this->Flash->success(__('Successfully upgraded to version {0}.', $tag));

        //clear the Cache
        try {
            Cache::clearAll();
            $this->Flash->success(__('Cache cleared.'));
        } catch (\Throwable $exception) {
            $this->Flash->success(__('Could not clear the cache.'));
        }

        $count = $this->BackgroundServices->start('all', false);
        if ($count > 0) {
            $this->Flash->success(__('Started {0} Background Services.', $count));
        }

        $time_end = microtime(true);
        $time_total = round($time_end - $time_start);
        $this->Flash->success(__('Upgrade took {0} seconds.', $time_total));

        return $this->redirect(['controller' => 'installers', 'action' => 'updates']);
    }

    /**
     * Remove unused files
     *
     * @param null $removeList
     * @return int
     */
    public function removeUnusedFiles($removeList = null)
    {
        $zipPackager = new ZipPackager();

        $ignoreFilesFolders = [
            "config/app.php",
            "config/config_local.php",
            "config/Stub_DB.sqlite",
            "bin/BackgroundServices/nssm.exe",
            "logs/",
            "tmp/",
            "web.xml",
            "web.config",
        ];

        $removeList = $zipPackager->filterOutFoldersAndFiles($removeList, $ignoreFilesFolders);

        $removedCounter = 0;
        foreach ($removeList as $file) {
            $baseDir = ROOT . DS;
            if (unlink($baseDir . $file)) {
                $removedCounter++;
            }
        }

        return $removedCounter;
    }

    /**
     * To test status and requests-per-second
     *
     * @param string $contentType1
     * @param string $contentType2
     * @param int $statusCode
     * @param string $data
     * @return null
     */
    public function helloWorld($contentType1 = 'text', $contentType2 = 'html', $statusCode = 200, $data = 'hello-world')
    {
        $this->viewBuilder()->setLayout('blank');

        $contentTypeAllowed = [
            'text/html',
            'text/xml',
            'text/json',
        ];

        $contentType = $contentType1 . "/" . $contentType2;
        if (!in_array($contentType, $contentTypeAllowed)) {
            $contentType = 'text/html';
        }

        $this->response = $this->response->withType($contentType)->withStatus($statusCode);
        $this->set('data', $data);

        return null;
    }

}
