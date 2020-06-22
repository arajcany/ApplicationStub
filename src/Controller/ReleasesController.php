<?php

namespace App\Controller;

use App\Utility\Install\VersionControl;
use App\Utility\Release\BuildTasks;
use App\Utility\Release\GitTasks;
use Cake\Event\Event;

/**
 * Class ReleasesController
 * Used to build a release of the application
 *
 * @package App\Controller
 * @property VersionControl $VersionControl
 * @property GitTasks $GitTasks
 * @property BuildTasks $BuildTasks
 */
class ReleasesController extends AppController
{
    private $VersionControl;
    private $GitTasks;
    private $BuildTasks;

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

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $versionIni = $this->VersionControl->getVersionIni();
        $this->set('versionIni', $versionIni);

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

        return null;
    }


    /**
     * Build a release package
     */
    public function build()
    {

        if ($this->request->is('post')) {
            $this->BuildTasks = new BuildTasks();

        }//end POST


        $drive = explode("\\", ROOT);
        $drive = str_replace(" ", "_", $drive[0] . DS . APP_NAME . "_Builds" . DS);
        $this->set('drive', $drive);

        $this->set('gitBranch', $this->GitTasks->getGitBranch());
        $this->set('gitModified', $this->GitTasks->getGitModified());

        return null;
    }

}
