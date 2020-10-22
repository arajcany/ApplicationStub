<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\ErrandsTable;
use App\Model\Table\WorkersTable;
use arajcany\ToolBox\Utility\Security\Security;
use Cake\ORM\TableRegistry;

/**
 * Developers Controller
 *
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


}
