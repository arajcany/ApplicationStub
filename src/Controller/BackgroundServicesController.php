<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Utility\Install\VersionControl;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * BackgroundServices Controller
 *
 */
class BackgroundServicesController extends AppController
{

    /**
     * Initialize method
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

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
        $services = $this->_getServices();
        $this->set('services', $services);
    }

    public function create()
    {
        $nssm = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS . 'nssm.exe';
        $serviceInstallFile = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS . 'services_install.bat';
        $serviceRemoveFile = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS . 'services_remove.bat';
        if (is_file($nssm)) {
            $isNssm = true;
        } else {
            $isNssm = false;
        }

        $this->set('isNssm', $isNssm);


        if ($this->request->is(['post'])) {

            $username = $this->request->getData('username');
            $password = $this->request->getData('password');

            $errand_worker_limit = $this->Settings->getSetting('errand_worker_limit');
            $message_worker_limit = $this->Settings->getSetting('message_worker_limit');

            exec('where  php', $phpExe, $ret);
            asort($phpExe);
            $phpExe = array_reverse($phpExe);
            $phpExe = $phpExe[0];
            $phpLocation = pathinfo($phpExe, PATHINFO_DIRNAME);
            $binDirectory = ROOT . DS . 'bin' . DS;

            $commands = [];
            $commandsRemove = [];
            foreach (range(1, $errand_worker_limit) as $counter) {
                $serviceName = Inflector::camelize(APP_NAME) . "_ErrandWorker" . $counter;
                $serviceDescription = "Errand Worker for " . APP_NAME;
                $parameters = __("-f \"{0}cake.php\" BackgroundServices errand", $binDirectory);
                $startMode = "SERVICE_AUTO_START";

                $commands[] = __("\"{0}\" install \"{1}\" \"{2}\"", $nssm, $serviceName, $phpExe);

                if (strlen($username) > 0 && strlen($password) > 0) {
                    $commands[] = __("\"{0}\" set \"{1}\" ObjectName {2} {3}", $nssm, $serviceName, $username, $password);
                }

                $commands[] = __("\"{0}\" set \"{1}\" Application \"{2}\"", $nssm, $serviceName, $phpExe);
                $commands[] = __("\"{0}\" set \"{1}\" AppDirectory \"{2}\"", $nssm, $serviceName, $phpLocation);
                $commands[] = __("\"{0}\" set \"{1}\" AppParameters {2}", $nssm, $serviceName, $parameters);

                $commands[] = __("\"{0}\" set \"{1}\" DisplayName \"{2}\"", $nssm, $serviceName, $serviceName);
                $commands[] = __("\"{0}\" set \"{1}\" Description \"{2}\"", $nssm, $serviceName, $serviceDescription);
                $commands[] = __("\"{0}\" set \"{1}\" Start {2}", $nssm, $serviceName, $startMode);

                $commands[] = __("net start \"{0}\"", $serviceName);


                $commandsRemove[] = __("net stop \"{0}\"", $serviceName);
                $commandsRemove[] = __("\"{0}\" remove \"{1}\" confirm", $nssm, $serviceName);
            }

            $commands = implode("\r\n", $commands) . "\r\npause\r\n";
            file_put_contents($serviceInstallFile, $commands);

            $commandsRemove = implode("\r\n", $commandsRemove) . "\r\npause\r\n";
            file_put_contents($serviceRemoveFile, $commandsRemove);

            $this->Flash->success(__('Batch files have been created to Install and Remove the Windows Services.'));
            return $this->redirect(['action' => 'index']);

        }

    }

    public function stop($serviceName)
    {
        $services = $this->_getServices();

        $serviceNamesCompiled = [];
        foreach ($services as $service) {
            $serviceNamesCompiled[] = $service['name'];
        }

        if (!in_array($serviceName, $serviceNamesCompiled)) {
            $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
            return $this->redirect(['action' => 'index']);
        }

        $cmd = __("net stop \"{0}\"", $serviceName);
        exec($cmd, $out, $ret);

        $out = implode(" ", $out);
        $this->Flash->success(__('{0}', $out));
        return $this->redirect(['action' => 'index']);

    }

    public function start($serviceName)
    {
        $services = $this->_getServices();

        $serviceNamesCompiled = [];
        foreach ($services as $service) {
            $serviceNamesCompiled[] = $service['name'];
        }

        if (!in_array($serviceName, $serviceNamesCompiled)) {
            $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
            return $this->redirect(['action' => 'index']);
        }

        $cmd = __("net start \"{0}\"", $serviceName);
        exec($cmd, $out, $ret);

        $out = implode(" ", $out);
        $this->Flash->success(__('{0}', $out));
        return $this->redirect(['action' => 'index']);

    }

    private function _getServices()
    {
        $appName = Inflector::camelize(APP_NAME);
        $cmd = __("sc.exe query state= all | find \"SERVICE_NAME: {0}_\"", $appName);
        exec($cmd, $foundServices, $ret);

        $services = [];
        foreach ($foundServices as $service) {
            $service = str_replace("SERVICE_NAME: ", "", $service);
            $cmd = __("sc.exe query {0} | find \"STATE\"", $service);
            exec($cmd, $outServiceState, $ret2);
            $outServiceState = explode(" ", $outServiceState[0]);
            $outServiceState = array_pop($outServiceState);

            $services[] =
                [
                    'name' => $service,
                    'state' => $outServiceState,
                ];

            unset($outServiceState);
            unset($ret2);
        }

        return $services;
    }


}
