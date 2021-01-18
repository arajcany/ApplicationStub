<?php

namespace App\Controller;

use App\Controller\Component\BackgroundServicesComponent;
use App\Model\Entity\User;
use App\Utility\Install\VersionControl;
use arajcany\ToolBox\Utility\Security\Security;
use arajcany\ToolBox\Utility\ZipMaker;
use Cake\Cache\Cache;
use Cake\Core\Configure;
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
     * with the bare minimum configuration, this action will automatically redirect to the dashboard.
     *
     * @return \Cake\Http\Response|null
     */
    public function configure()
    {
        if (Cache::read('first_run', 'quick_burn') !== true) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }

        /**
         * @var User $user
         */
        $user = $this->Users->findUsersByRoleNameOrRoleAlias("SuperAdmin")->order('Users.id', true)->first();

        if (strlen($user->password) >= 40) {
            Cache::delete('first_run', 'quick_burn');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }


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
        $hash = array_reverse($hash);
        $this->set('versions', $hash);

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
            $zipFilePathName = TMP . pathinfo($upgradeFile, PATHINFO_BASENAME);
            $zipFileContents = @file_get_contents($upgradeFile);
            if ($zipFileContents) {
                $this->Flash->success(__('Downloaded the upgrade file.'));
                file_put_contents($zipFilePathName, $zipFileContents);
            } else {
                $this->Flash->error(__('Sorry, could not download the upgrade file.'));
                return $this->redirect(['action' => 'updates']);
            }
        }

        $baseExtractDir = ROOT . DS;

        $count = $this->BackgroundServices->stop('all', false);
        if ($count > 0) {
            $this->Flash->success(__('Stopped {0} Background Services.', $count));
        }

        $zip = zip_open($zipFilePathName);
        if ($zip) {
            $countUpgraded = 0;
            $countNotUpgraded = 0;
            $countExtracted = 0;
            $countNotExtracted = 0;
            $safeList = [];
            $notUpgradedList = [];
            while ($zip_entry = zip_read($zip)) {
                $currentFilenameInZip = zip_entry_name($zip_entry);
                $currentFilenameInZipAsParts = explode("\\", $currentFilenameInZip);
                $zipStartOfPath = $currentFilenameInZipAsParts[0] . "\\";

                if (zip_entry_open($zip, $zip_entry)) {
                    $countExtracted++;
                    $contents = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    if (strlen($contents) >= 0) {
                        if (count($currentFilenameInZipAsParts) >= 2) {
                            $currentFileNameInZipTrimmed = str_replace($zipStartOfPath, "", $currentFilenameInZip);
                            $putResult = (new File($baseExtractDir . $currentFileNameInZipTrimmed, true))->write($contents);

                            if ($putResult !== false) {
                                $countUpgraded++;
                            } else {
                                $countNotUpgraded++;
                                $notUpgradedList[] = $currentFileNameInZipTrimmed;
                            }

                            $safeList[] = $baseExtractDir . $currentFileNameInZipTrimmed;
                        } else {
                            $countNotUpgraded++;
                        }
                    }
                    zip_entry_close($zip_entry);
                } else {
                    $countNotExtracted++;
                }
            }
            zip_close($zip);

            $countRemoved = $this->removeUnusedFiles($safeList);

            $msg = '';
            $msg .= __('{0} files extracted, {1} files failed to extract. ', $countExtracted, $countNotExtracted);
            $msg .= __('{0} files upgraded, {1} files failed to upgrade. ', $countUpgraded, $countNotUpgraded);
            $msg .= __('{0} files removed. ', $countRemoved);
            $msg = trim($msg);
            $this->Flash->success($msg);
            $this->Flash->success(__('Successfully upgraded to version {0}.', $tag));

            $time_end = microtime(true);
            $time_total = round($time_end - $time_start);
            $this->Flash->success(__('Upgrade took {0} seconds.', $time_total));

        } else {
            $this->Flash->error(__('Could not read the update package. Please try again.'));
        }

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

        return $this->redirect(['controller' => 'installers', 'action' => 'updates']);
    }

    /**
     * Remove unused files
     *
     * @param null $safeList
     * @return int
     */
    public function removeUnusedFiles($safeList = null)
    {
        $zm = new ZipMaker();

        $baseDir = ROOT;
        $ignoreFilesFolders = [
            "config\\app.php",
            "bin\\BackgroundServices\\nssm.exe",
            "logs\\",
            "tmp\\",
        ];

        $fileListToCheck = $zm->makeFileList($baseDir, $ignoreFilesFolders);
        $fileListToRemove = array_diff($fileListToCheck, $safeList);

        $removedCounter = 0;
        foreach ($fileListToRemove as $file) {
            if (unlink($file)) {
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
