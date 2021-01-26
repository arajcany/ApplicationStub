<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\ArtifactsTable;
use App\Model\Table\ErrandsTable;
use App\Model\Table\WorkersTable;
use arajcany\ToolBox\Utility\Security\Security;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;

/**
 * Developers Controller
 *
 * @property ArtifactsTable $Artifacts;
 * @property ErrandsTable $Errands;
 * @property WorkersTable $Workers;
 */
class DevelopersController extends AppController
{
    private $Errands;
    private $Workers;

    public function initialize()
    {
        parent::initialize();

        $this->Artifacts = TableRegistry::getTableLocator()->get('Artifacts');
        $this->Errands = TableRegistry::getTableLocator()->get('Errands');
        $this->Workers = TableRegistry::getTableLocator()->get('Workers');

    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {

    }


    public function errands()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $options = [
            'class' => 'Seeds',
            'method' => 'createSeed',
            'parameters' => [0 => ['token' => sha1(Security::randomBytes(1024))]],
        ];

        $errand = $this->Errands->createErrand($options);
        $toDebug['$errand'] = $errand;


//        $worker = $this->Workers->getWorker('errand');
//        $toDebug['$worker'] = $worker;

        $this->set('toDebug', $toDebug);
    }


    public function artifacts()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $options = [
            'width' => mt_rand(64, 256),
            'height' => mt_rand(64, 256),
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];

        $artifact = $this->Artifacts->createPlaceholderArtifact($options);
        $toDebug['$errand'] = $artifact;

        $this->set('toDebug', $toDebug);
    }


    public function cache()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $toDebug['Cache1'] = Cache::read('first_run');
        $toDebug['Cache2'] = Cache::read('setttings');
        $toDebug['Cache3'] = Cache::read('internal_options');
        $toDebug['clear'] = $this->clearCache();


        $this->set('toDebug', $toDebug);
    }


    public function workers()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        //$toDebug['heartbeats'] = $this->Workers->find('all')->limit(5)->contain(['heartbeats'])->toArray();
        $toDebug['heartbeats'] = $this->Workers->findHeartbeats()->toArray();
        $toDebug['pulses'] = sqld($this->Workers->findPulses(0));

        $this->set('toDebug', $toDebug);
    }


}
