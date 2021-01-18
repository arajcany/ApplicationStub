<?php

namespace App\Controller;

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
 */
class InstallersController extends AppController
{
    use MailerAwareTrait;

    public $Users;
    public $Version;
    public $Installers;
    public $Settings;


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
        return null;
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
     * Perform system checks. Essentially the same as Configure but with more Settings exposed.
     * This action is behind AUTH/ACL controlled.
     *
     * @return \Cake\Http\Response|null
     */
    public function checks()
    {
        $this->Users = $this->loadModel('Users');

        $Checker = new Checker();
        $Builder = new Builder();

        //encrypt Internal Options
        $this->InternalOptions->encryptOptions();

        //build Database if required
        $dbVersion = $Builder->buildDatabase();
        if (is_numeric($dbVersion) && DB_VERSION != $dbVersion) {
            $this->Flash->success(__("Database automatically upgraded to Version {0}", $dbVersion));
            return $this->redirect([]);
        }
        $isConnected = $Checker->checkDatabase();
        $this->set('isConnected', $isConnected);

        //load Form contexts
        $settingsEmailForm = new SettingsEmailForm();
        $settingsSuperAdminForm = new SettingsSuperAdminForm();
        $settingsEmergencyEmailForm = new SettingsEmergencyEmailForm();
        $settingsCompanyDetailsForm = new SettingsCompanyDetailsForm();
        $settingsScheduledTasksForm = new SettingsScheduledTasksForm();
        $settingsRepositoryForm = new SettingsRepositoryForm();

        //update the configuration if requested
        if ($this->request->is(['patch', 'post', 'put'])) {

            if ($this->request->getData('config_email') == true) {
                if ($settingsEmailForm->validate($this->request->getData())) {
                    $this->Installers->handleEmailForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsEmailForm']);
                }
            }

            if ($this->request->getData('config_super_admin') == true) {
                $settingsSuperAdminForm->password_1 = $this->request->getData('password_1');
                $settingsSuperAdminForm->password_2 = $this->request->getData('password_2');

                if ($settingsSuperAdminForm->validate($this->request->getData())) {
                    $this->Installers->handleSuperAdminForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsSuperAdminForm']);
                }
            }

            if ($this->request->getData('config_emergency_email') == true) {
                if ($settingsEmergencyEmailForm->validate($this->request->getData())) {
                    $this->Installers->handleEmergencyEmailForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsEmergencyEmailForm']);
                }
            }

            if ($this->request->getData('config_company_details') == true) {
                if ($settingsCompanyDetailsForm->validate($this->request->getData())) {
                    $this->Installers->handleCompanyDetailsForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsCompanyDetailsForm']);
                }
            }

            if ($this->request->getData('config_scheduled_tasks') == true) {
                if ($settingsScheduledTasksForm->validate($this->request->getData())) {
                    $this->Installers->handleScheduledTasksForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsScheduledTasksForm']);
                }
            }

            if ($this->request->getData('config_repo_sftp') == true) {
                if ($settingsRepositoryForm->validate($this->request->getData())) {
                    $this->Installers->handleRepositoryForm();
                } else {
                    $this->Flash->error(__('Sorry we encountered an error, please check the data you entered'),
                        ['key' => 'settingsRepositoryForm']);
                }
            }

        }//POST

        $emailDetails = $this->Settings->getEmailDetails();
        $emailDetails['email_password'] = "****************";
        $this->set('emailDetails', $emailDetails);
        $this->set('settingsEmailForm', $settingsEmailForm);
        $isEmail = $Checker->checkEmailServer();
        $this->set('isEmail', $isEmail);

        $superAdminDetails = $this->Users
            ->find('all')->matching(
                'Roles', function ($q) {
                return $q->where(['Roles.alias' => 'superadmin']);
            }
            )
            ->toArray();
        $this->set('superAdminDetails', $superAdminDetails);
        $this->set('settingsSuperAdminForm', $settingsSuperAdminForm);
        $isSuperAdmin = $Checker->checkSuperAdmin();;
        $this->set('isSuperAdmin', $isSuperAdmin);

        $emergencyEmailDetails = $this->Settings->getSetting('emergency_email');
        $this->set('emergencyEmailDetails', $emergencyEmailDetails);
        $this->set('settingsEmergencyEmailForm', $settingsEmergencyEmailForm);
        $isEmergencyEmail = $Checker->checkEmergencyEmail();
        $this->set('isEmergencyEmail', $isEmergencyEmail);

        $companyDetails = $this->Settings->getCompanyDetails();
        $this->set('companyDetails', $companyDetails);
        $this->set('settingsCompanyDetailsForm', $settingsCompanyDetailsForm);
        $isCompanyDetails = $Checker->checkCompanyDetails();
        $this->set('isCompanyDetails', $isCompanyDetails);

        $this->set('settingsScheduledTasksForm', $settingsScheduledTasksForm);
        $isScheduledTasks = $Checker->checkScheduledTasks();
        $this->set('isScheduledTasks', $isScheduledTasks);

        $repoSftpDetails = $this->Settings->getRepoDetails();
        $repoSftpDetails['repo_sftp_password'] = "****************";
        $this->set('repoSftpDetails', $repoSftpDetails);
        $this->set('optionsMode', $this->Settings->getSettingSelections('repo_mode'));
        $this->set('optionsPurge', $this->Settings->getSettingSelections('repo_purge'));
        $this->set('settingsRepositoryForm', $settingsRepositoryForm);
        $isRepositoryForm = $Checker->checkRepoSftpSettings();
        $this->set('isRepositoryForm', $isRepositoryForm);

        $generalWorkerDetails = $this->Users->getGeneralWorkerUser();
        $mailWorkerDetails = $this->Users->getMailWorkerUser();
        $this->set('generalWorkerDetails', $generalWorkerDetails);
        $this->set('mailWorkerDetails', $mailWorkerDetails);

        //set Flash Messages from the Checker and Builder
        $this->Flash->setMultiple($Checker->getMessages());
        $this->Flash->setMultiple($Builder->getMessages());

        return null;
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
        if (strtolower(Configure::read('mode')) !== 'uat' && strtolower(Configure::read('mode')) !== 'prod') {
            //$this->Flash->error(__('You are not allowed to Upgrade!'));
            //return $this->redirect(['action' => 'updates']);
        }

        $upgradeFile = Security::decrypt64Url($upgradeFile);
        $versionHistory = $this->Version->getVersionHistoryJsn();
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

            //clear the Cache
            //Cache::clearAll();

            $msg = '';
            $msg .= __('{0} files extracted, {1} files failed to extract. ', $countExtracted, $countNotExtracted);
            $msg .= __('{0} files upgraded, {1} files failed to upgrade. ', $countUpgraded, $countNotUpgraded);
            $msg .= __('{0} files removed. ', $countRemoved);
            $msg = trim($msg);
            $this->Flash->success($msg);
            $this->Flash->success(__('Successfully upgraded to version {0}.', $tag));
            return $this->redirect(['controller' => 'installers', 'action' => 'updates']);
        } else {
            $this->Flash->error(__('Could not read the update package. Please try again.'));
            return $this->redirect(['controller' => 'installers', 'action' => 'updates']);
        }

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
