<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\BackgroundServicesComponent;
use App\Utility\Install\VersionControl;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use App\Model\Table\HeartbeatsTable;

/**
 * BackgroundServices Controller
 *
 * @property BackgroundServicesComponent $BackgroundServices
 * @property HeartbeatsTable $Heartbeats
 */
class BackgroundServicesController extends AppController
{
    public $BackgroundServices;
    public $Heartbeats;

    /**
     * Initialize method
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('BackgroundServices');
        $this->loadModel('Heartbeats');

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
        $services = $this->BackgroundServices->_getServices();
        $this->set('services', $services);

        $heartbeats = $this->Heartbeats->findLastHeartbeats();
        $this->set('heartbeats', $heartbeats);
    }

    /**
     * Create Batch files that aid with Install/Remove of the Windows Serevice
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function batch()
    {
        $batchLocation = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS;
        $nssm = $batchLocation . 'nssm.exe';

        if (is_file($nssm)) {
            $isNssm = true;
        } else {
            $isNssm = false;
        }

        $this->set('isNssm', $isNssm);

        if ($this->request->is(['post'])) {

            $result = $this->BackgroundServices->createBackgroundServicesBatchFiles();

            if ($result) {
                $this->Flash->success(__('Batch files created in {0}. Run as Administrator to install Windows Services.', $batchLocation));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->success(__('Failed to create the batch files to install Windows Services.', $batchLocation));
            }
        }

    }

    /**
     * @param $serviceName
     * @return \Cake\Http\Response|null
     */
    public function stop($serviceName)
    {
        $services = $this->BackgroundServices->_getServices();

        $serviceNamesCompiled = [];
        $servicesRunning = [];
        $servicesStopped = [];
        foreach ($services as $service) {
            $serviceNamesCompiled[] = $service['name'];

            if ($service['state'] == 'RUNNING') {
                $servicesRunning[] = $service['name'];
            } elseif ($service['state'] == 'STOPPED') {
                $servicesStopped[] = $service['name'];
            }
        }

        if (strtolower($serviceName) == 'all') {
            $servicesToActOn = $servicesRunning;
        } elseif (in_array($serviceName, $serviceNamesCompiled)) {
            $servicesToActOn = [$serviceName];
        } else {
            $servicesToActOn = [];
            $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
        }

        foreach ($servicesToActOn as $service) {
            $cmd = __("net stop \"{0}\" 2>&1", $service);
            exec($cmd, $out, $ret);

            $out = implode(" ", $out);
            $this->Flash->success(__('{0}', $out));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param $serviceName
     * @return \Cake\Http\Response|null
     */
    public function start($serviceName)
    {
        $services = $this->BackgroundServices->_getServices();

        $serviceNamesCompiled = [];
        $servicesRunning = [];
        $servicesStopped = [];
        foreach ($services as $service) {
            $serviceNamesCompiled[] = $service['name'];

            if ($service['state'] == 'RUNNING' || $service['state'] == 'PAUSED') {
                $servicesRunning[] = $service['name'];
            } elseif ($service['state'] == 'STOPPED') {
                $servicesStopped[] = $service['name'];
            }
        }

        if (strtolower($serviceName) == 'all') {
            $servicesToActOn = $servicesStopped;
        } elseif (in_array($serviceName, $serviceNamesCompiled)) {
            $servicesToActOn = [$serviceName];
        } else {
            $servicesToActOn = [];
            $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
        }

        foreach ($servicesToActOn as $service) {
            $cmd = __("net start \"{0}\" 2>&1", $service);
            exec($cmd, $out, $ret);

            $out = implode(" ", $out);
            $this->Flash->success(__('{0}', $out));
        }

        return $this->redirect(['action' => 'index']);
    }


}
