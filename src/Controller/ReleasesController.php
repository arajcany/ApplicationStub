<?php

namespace App\Controller;

use App\Model\Table\InternalOptionsTable;
use App\Utility\Install\VersionControl;
use App\Utility\Release\BuildTasks;
use App\Utility\Release\GitTasks;
use arajcany\ToolBox\Utility\TextFormatter;
use Cake\Event\Event;
use Cake\Utility\Text;
use phpseclib\Net\SFTP;
use App\Utility\Install\Checker;

/**
 * Class ReleasesController
 * Used to build a release of the application
 *
 * @package App\Controller
 * @property VersionControl $VersionControl
 * @property GitTasks $GitTasks
 * @property BuildTasks $BuildTasks
 * @property InternalOptionsTable $InternalOptions
 */
class ReleasesController extends AppController
{
    private $VersionControl;
    private $GitTasks;
    private $BuildTasks;
    public $InternalOptions;

    /**
     * Initialize method
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->GitTasks = new GitTasks();
        $this->VersionControl = new VersionControl();

        $this->loadModel('InternalOptions');

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        return null;
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|\Cake\Http\Response|null
     */
    public function index()
    {
        $gitBranch = $this->GitTasks->getGitBranch();
        $gitCommits = $this->GitTasks->getCommitsSinceLastBuild();
        $gitModified = $this->GitTasks->getGitModified();
        $this->set('gitBranch', $gitBranch);
        $this->set('gitCommits', $gitCommits);
        $this->set('gitModified', $gitModified);

        $remote_update_unc = $this->InternalOptions->getOption('remote_update_unc');
        $remote_update_sftp_host = $this->InternalOptions->getOption('remote_update_sftp_host');
        $remote_update_sftp_port = $this->InternalOptions->getOption('remote_update_sftp_port');
        $remote_update_sftp_username = $this->InternalOptions->getOption('remote_update_sftp_username');
        $remote_update_sftp_password = $this->InternalOptions->getOption('remote_update_sftp_password', true);
        $remote_update_sftp_timeout = $this->InternalOptions->getOption('remote_update_sftp_timeout');
        $remote_update_sftp_path = $this->InternalOptions->getOption('remote_update_sftp_path');

        $this->set(compact('remote_update_unc'));
        $this->set(compact('remote_update_sftp_host', 'remote_update_sftp_port', 'remote_update_sftp_username'));
        $this->set(compact('remote_update_sftp_password', 'remote_update_sftp_timeout', 'remote_update_sftp_path'));

        $remote_update_url = $this->Settings->findByPropertyKey('remote_update_url')->first();
        $remote_update_url = $remote_update_url['property_value'];
        $this->set('remote_update_url', $remote_update_url);

        $sftpRoundTripSettings = [
            'url' => $remote_update_url,
            'host' => $remote_update_sftp_host,
            'port' => $remote_update_sftp_port,
            'username' => $remote_update_sftp_username,
            'password' => $remote_update_sftp_password,
            'timeout' => $remote_update_sftp_timeout,
            'path' => $remote_update_sftp_path,
        ];

        $uncRoundTripSettings = [
            'url' => $remote_update_url,
            'unc' => $remote_update_unc,
        ];

        $urlSettings = [
            'url' => $remote_update_url,
        ];

        $Checker = new Checker();
        $isUrl = $Checker->checkUrlSettings($urlSettings);
        $this->set('isUrl', $isUrl);
        $isSFTP = $Checker->checkSftpSettings($sftpRoundTripSettings);
        $this->set('isSFTP', $isSFTP);
        $isUNC = $Checker->checkUncSettings($uncRoundTripSettings);
        $this->set('isUNC', $isUNC);

        $this->set('remoteUpdateDebug', $Checker->getMessages());

        return null;
    }

    public function configureRemoteUpdate()
    {
        $setting = $this->Settings->findByPropertyKey('remote_update_url')->first();

        $remote_update_unc = $this->InternalOptions->getOption('remote_update_unc');
        $remote_update_sftp_host = $this->InternalOptions->getOption('remote_update_sftp_host');
        $remote_update_sftp_port = $this->InternalOptions->getOption('remote_update_sftp_port');
        $remote_update_sftp_username = $this->InternalOptions->getOption('remote_update_sftp_username');
        $remote_update_sftp_password = $this->InternalOptions->getOption('remote_update_sftp_password', true);
        $remote_update_sftp_timeout = $this->InternalOptions->getOption('remote_update_sftp_timeout');
        $remote_update_sftp_path = $this->InternalOptions->getOption('remote_update_sftp_path');

        $this->set(compact('remote_update_unc'));
        $this->set(compact('remote_update_sftp_host', 'remote_update_sftp_port', 'remote_update_sftp_username'));
        $this->set(compact('remote_update_sftp_password', 'remote_update_sftp_timeout', 'remote_update_sftp_path'));


        if ($this->request->is(['patch', 'post', 'put'])) {
            $dataToSave = $this->request->getData();
            $dataToSave['property_value'] = TextFormatter::makeEndsWith($dataToSave['property_value'], "/");

            $setting = $this->Settings->patchEntity($setting, $dataToSave);
            if ($this->Settings->save($setting)) {
                $this->Flash->success(__('The application remote update URL has been saved.'));
                $isSuccessRemoteUpdateUrl = true;

                //update Configure
                $this->Settings->saveSettingsToConfigure(false);
            } else {
                $isSuccessRemoteUpdateUrl = false;
                $this->Flash->error(__('The application remote update URL could not be saved. Please, try again.'));
            }

            $password_is_masked = 0;
            $password_apply_mask = 1;

            if (strlen($dataToSave['remote_update_sftp_password']) > 64) {
                $password_is_masked = 1;
                $password_apply_mask = 0;
            }

            $dataToSave['remote_update_unc'] = TextFormatter::makeEndsWith($dataToSave['remote_update_unc'], "\\");

            $values = [
                ['option_key' => 'remote_update_unc', 'option_value' => $dataToSave['remote_update_unc'], 'is_masked' => 0, 'apply_mask' => 0,],
                ['option_key' => 'remote_update_sftp_host', 'option_value' => $dataToSave['remote_update_sftp_host'], 'is_masked' => 0, 'apply_mask' => 0,],
                ['option_key' => 'remote_update_sftp_port', 'option_value' => $dataToSave['remote_update_sftp_port'], 'is_masked' => 0, 'apply_mask' => 0,],
                ['option_key' => 'remote_update_sftp_username', 'option_value' => $dataToSave['remote_update_sftp_username'], 'is_masked' => 0, 'apply_mask' => 0,],
                ['option_key' => 'remote_update_sftp_password', 'option_value' => $dataToSave['remote_update_sftp_password'], 'is_masked' => $password_is_masked, 'apply_mask' => $password_apply_mask,],
                ['option_key' => 'remote_update_sftp_timeout', 'option_value' => $dataToSave['remote_update_sftp_timeout'], 'is_masked' => 0, 'apply_mask' => 0,],
                ['option_key' => 'remote_update_sftp_path', 'option_value' => $dataToSave['remote_update_sftp_path'], 'is_masked' => 0, 'apply_mask' => 0,],
            ];

            if ($this->InternalOptions->saveRemoteUpdate($values)) {
                $this->Flash->success(__('The application remote update SFTP and UNC Settings has been saved.'));
                $isRemoteUpdateSftp = true;
            } else {
                $this->Flash->error(__('The application remote update SFTP and UNC Settings could not be saved. Please try again.'));
                $isRemoteUpdateSftp = false;
            }

            if ($isSuccessRemoteUpdateUrl && $isRemoteUpdateSftp) {
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set(compact('setting'));
        $this->set('_serialize', ['remoteUpdateUrl']);

        return null;
    }


}
