<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * BackgroundServices component
 *
 * @property FlashComponent $Flash
 */
class BackgroundServicesComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $components = ['Flash'];

    private function getAppNameCamelized()
    {
        return Inflector::camelize(APP_NAME);
    }

    public function createBackgroundServicesBatchFiles()
    {
        $appName = $this->getAppNameCamelized();

        $batchLocation = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS;
        $nssm = $batchLocation . 'nssm.exe';

        $serviceInstallFile = $batchLocation . 'services_install.bat';
        $serviceRemoveFile = $batchLocation . 'services_remove.bat';
        $serviceStartFile = $batchLocation . 'services_start.bat';
        $serviceStopFile = $batchLocation . 'services_stop.bat';
        $serviceCommandsTestFile = $batchLocation . 'services_test_commands.bat';

        $username = $this->getController()->request->getData('username');
        $password = $this->getController()->request->getData('password');
        $serviceStartMode = $this->getController()->request->getData('service_start');

        exec('where  php', $phpExe, $ret);
        asort($phpExe);
        $phpExe = array_reverse($phpExe);
        $phpExe = $phpExe[0];
        $phpLocation = pathinfo($phpExe, PATHINFO_DIRNAME);
        $binDirectory = ROOT . DS . 'bin' . DS;

        $commands = [];
        $commandsRemove = [];
        $commandsStart = [];
        $commandsStop = [];
        $commandsTest = [];


        //------START Errand and Messages workers-------------------------------------------------------------
        $workerTypes = ['Errand', 'Message'];
        foreach ($workerTypes as $workerType) {
            $workerTypeLowerCased = strtolower($workerType);
            $workerLimit = Configure::read("Settings.{$workerTypeLowerCased}_worker_limit");
            foreach (range(1, $workerLimit) as $counter) {
                $counterPadded = str_pad($counter, 2, '0', STR_PAD_LEFT);
                $serviceName = $appName . "_{$workerType}Worker_" . $counterPadded;
                $serviceDescription = "{$workerType} Worker for " . APP_NAME;
                $parameters = __("-f \"{0}cake.php\" BackgroundServices {2} -h {1}", $binDirectory, $serviceName, $workerTypeLowerCased);
                $commands[] = __("\"{0}\" install \"{1}\" \"{2}\"", $nssm, $serviceName, $phpExe);
                if (strlen($username) > 0 && strlen($password) > 0) {
                    $commands[] = __("\"{0}\" set \"{1}\" ObjectName {2} {3}", $nssm, $serviceName, $username, $password);
                }
                $commands[] = __("\"{0}\" set \"{1}\" Application \"{2}\"", $nssm, $serviceName, $phpExe);
                $commands[] = __("\"{0}\" set \"{1}\" AppDirectory \"{2}\"", $nssm, $serviceName, $phpLocation);
                $commands[] = __("\"{0}\" set \"{1}\" AppParameters {2}", $nssm, $serviceName, $parameters);
                $commands[] = __("\"{0}\" set \"{1}\" DisplayName \"{2}\"", $nssm, $serviceName, $serviceName);
                $commands[] = __("\"{0}\" set \"{1}\" Description \"{2}\"", $nssm, $serviceName, $serviceDescription);
                $commands[] = __("\"{0}\" set \"{1}\" Start {2}", $nssm, $serviceName, $serviceStartMode);
                if ($serviceStartMode == "SERVICE_AUTO_START" || $serviceStartMode == "SERVICE_DELAYED_START") {
                    $commands[] = __("net start \"{0}\"", $serviceName);
                }
                $commandsRemove[] = __("net stop \"{0}\"", $serviceName);
                $commandsRemove[] = __("\"{0}\" remove \"{1}\" confirm", $nssm, $serviceName);
                $commandsStart[] = __("net start \"{0}\"", $serviceName);
                $commandsStop[] = __("net stop \"{0}\"", $serviceName);
                $commandsTest[] = __("rem \"{0}\" {1}", $phpExe, $parameters);
            }
        }
        //------END Errand and Messages workers-------------------------------------------------------------


        //------START Artifacts cleanup-------------------------------------------------------------
        $repoPurgeLimit = Configure::read("Settings.repo_purge_limit");
        $serviceCountInterval = Configure::read("Settings.repo_purge_interval");
        $offset = ($serviceCountInterval / $repoPurgeLimit) * 60;
        foreach (range(1, $repoPurgeLimit) as $counter) {
            $counterPadded = str_pad($counter, 2, '0', STR_PAD_LEFT);
            $delay = intval($offset * ($counter - 1));
            $serviceName = $appName . "_ArtifactsDeleter_" . $counterPadded;
            $serviceDescription = "Artifacts Cleanup for " . APP_NAME;
            $parameters = __("-f \"{0}cake.php\" ArtifactsDeleter -d {1} -h {2}", $binDirectory, $delay, $serviceName);
            $commands[] = __("\"{0}\" install \"{1}\" \"{2}\"", $nssm, $serviceName, $phpExe);
            if (strlen($username) > 0 && strlen($password) > 0) {
                $commands[] = __("\"{0}\" set \"{1}\" ObjectName {2} {3}", $nssm, $serviceName, $username, $password);
            }
            $commands[] = __("\"{0}\" set \"{1}\" Application \"{2}\"", $nssm, $serviceName, $phpExe);
            $commands[] = __("\"{0}\" set \"{1}\" AppDirectory \"{2}\"", $nssm, $serviceName, $phpLocation);
            $commands[] = __("\"{0}\" set \"{1}\" AppParameters {2}", $nssm, $serviceName, $parameters);
            $commands[] = __("\"{0}\" set \"{1}\" DisplayName \"{2}\"", $nssm, $serviceName, $serviceName);
            $commands[] = __("\"{0}\" set \"{1}\" Description \"{2}\"", $nssm, $serviceName, $serviceDescription);
            $commands[] = __("\"{0}\" set \"{1}\" Start {2}", $nssm, $serviceName, $serviceStartMode);
            if ($serviceStartMode == "SERVICE_AUTO_START" || $serviceStartMode == "SERVICE_DELAYED_START") {
                $commands[] = __("net start \"{0}\"", $serviceName);
            }
            $commandsRemove[] = __("net stop \"{0}\"", $serviceName);
            $commandsRemove[] = __("\"{0}\" remove \"{1}\" confirm", $nssm, $serviceName);
            $commandsStart[] = __("net start \"{0}\"", $serviceName);
            $commandsStop[] = __("net stop \"{0}\"", $serviceName);
            $commandsTest[] = __("rem \"{0}\" {1}", $phpExe, $parameters);
        }
        //------END Artifacts cleanup-------------------------------------------------------------

        $commands = implode("\r\n", $commands) . "\r\npause\r\n";
        $saveResultInstall = file_put_contents($serviceInstallFile, $commands);

        $commandsRemove = implode("\r\n", $commandsRemove) . "\r\npause\r\n";
        $saveResultRemove = file_put_contents($serviceRemoveFile, $commandsRemove);

        $commandsStart = implode("\r\n", $commandsStart) . "\r\npause\r\n";
        $saveResultStart = file_put_contents($serviceStartFile, $commandsStart);

        $commandsStop = implode("\r\n", $commandsStop) . "\r\npause\r\n";
        $saveResultStop = file_put_contents($serviceStopFile, $commandsStop);

        $commandsTest = implode("\r\n", $commandsTest) . "\r\npause\r\n";
        $saveResultTest = file_put_contents($serviceCommandsTestFile, $commandsTest);

        if ($saveResultInstall && $saveResultRemove && $saveResultStart && $saveResultStop) {
            return true;
        } else {
            return false;
        }

    }

    public function _getServices()
    {
        $appName = $this->getAppNameCamelized();

        $cmd = __("sc.exe query state= all | find \"SERVICE_NAME: {0}_\"", $appName);
        exec($cmd, $foundServices, $ret);

        $services = [];
        foreach ($foundServices as $service) {
            $service = str_replace("SERVICE_NAME: ", "", $service);

            $cmd2 = __("sc.exe query {0} | find \"STATE\"", $service);
            exec($cmd2, $outServiceState, $ret2);
            $outServiceState = explode(" ", $outServiceState[0]);
            $outServiceState = array_pop($outServiceState);

            $cmd3 = __("sc.exe qc {0} | find \"START_TYPE\"", $service);
            exec($cmd3, $outServiceStartType, $ret3);
            $outServiceStartType = explode(" ", $outServiceStartType[0]);
            $outServiceStartType = array_pop($outServiceStartType);

            $services[] =
                [
                    'name' => $service,
                    'state' => $outServiceState,
                    'start_type' => $outServiceStartType,
                ];

            unset($outServiceState);
            unset($ret2);
            unset($outServiceStartType);
            unset($ret3);
        }

        return $services;
    }

    /**
     * @param $serviceName
     * @param bool $verbose
     * @return mixed
     */
    public function stop($serviceName, $verbose = true)
    {
        $services = $this->_getServices();

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
            $servicesToActOn = $servicesRunning;
        } elseif (in_array($serviceName, $serviceNamesCompiled)) {
            $servicesToActOn = [$serviceName];
        } else {
            $servicesToActOn = [];
            if ($verbose) {
                $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
            }
        }

        $counter = 0;
        foreach ($servicesToActOn as $service) {
            /**
             * @var array $out
             */
            $cmd = __("net stop \"{0}\" 2>&1", $service);
            exec($cmd, $out, $ret);

            $out = implode(" ", $out);
            if ($verbose) {
                $this->Flash->smartFlash(__('{0}', $out));
            }

            if (strpos(strtolower($out), 'success') !== false) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * @param $serviceName
     * @param bool $verbose
     * @return mixed
     */
    public function start($serviceName, $verbose = true)
    {
        $services = $this->_getServices();

        $serviceNamesCompiled = [];
        $servicesRunning = [];
        $servicesStopped = [];
        foreach ($services as $service) {
            $serviceNamesCompiled[] = $service['name'];

            if ($service['state'] == 'RUNNING' || $service['state'] == 'PAUSED') {
                $servicesRunning[] = $service['name'];
            } elseif ($service['state'] == 'STOPPED' && $service['start_type'] != 'DISABLED') {
                $servicesStopped[] = $service['name'];
            }
        }

        if (strtolower($serviceName) == 'all') {
            $servicesToActOn = $servicesStopped;
        } elseif (in_array($serviceName, $serviceNamesCompiled)) {
            $servicesToActOn = [$serviceName];
        } else {
            $servicesToActOn = [];
            if ($verbose) {
                $this->Flash->error(__('Sorry, could not find service {0}', $serviceName));
            }
        }

        $counter = 0;
        foreach ($servicesToActOn as $service) {
            /**
             * @var array $out
             */
            $cmd = __("net start \"{0}\" 2>&1", $service);
            exec($cmd, $out, $ret);

            $out = implode(" ", $out);
            if ($verbose) {
                $this->Flash->smartFlash(__('{0}', $out));
            }

            if (strpos(strtolower($out), 'success') !== false) {
                $counter++;
            }
        }

        return $counter;
    }

}
