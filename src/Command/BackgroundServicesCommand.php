<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Exception;

/**
 * BackgroundServices command.
 *
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\ErrandsTable $Errands
 * @property \App\Model\Table\WorkersTable $Workers
 * @property \App\Model\Table\MessagesTable $Messages
 * @property \App\Model\Table\MessageBeaconsTable $MessageBeacons
 * @property \App\Model\Table\HeartbeatsTable $Heartbeats
 */
class BackgroundServicesCommand extends Command
{
    public $Settings;
    public $Errands;
    public $Workers;
    //public $Messages;
    //public $MessageBeacons;
    public $Heartbeats;

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser = parent::buildOptionParser($parser);

        $parser
            ->addOption('heartbeat-context', [
                'short' => 'h',
                'help' => 'Context when logging a Heartbeat',
                'default' => 'BackgroundServices',
            ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->Settings = TableRegistry::getTableLocator()->get('Settings');
        $this->Errands = TableRegistry::getTableLocator()->get('Errands');
        $this->Workers = TableRegistry::getTableLocator()->get('Workers');
        //$this->Messages = TableRegistry::getTableLocator()->get('Messages');
        //$this->MessageBeacons = TableRegistry::getTableLocator()->get('MessageBeacons');
        $this->Heartbeats = TableRegistry::getTableLocator()->get('Heartbeats');

        $serviceType = $args->getArgumentAt(0);

        if (in_array($serviceType, ['errands', 'errand'])) {
            return $this->runErrandsService($args, $io);
        }

        if (in_array($serviceType, ['messages', 'message'])) {
            return $this->runMessagesService($args, $io);
        }

        $io->abort(__("Please run an 'errand' or 'message' BackgroundService"), 1);
    }

    /**
     * Run the Errands Background Service
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int
     */
    private function runErrandsService(Arguments $args, ConsoleIo $io)
    {
        $io->out('Initiating an Errand Worker...');

        $heartbeatContext = $args->getOption('heartbeat-context');

        //====Create a Worker=============================================================
        $workerOptions = ['background_services_link' => $heartbeatContext];
        $worker = $this->Workers->createWorker('errand', $workerOptions);
        if (!$worker) {
            $io->abort(__("Failed to initiate a Worker...Bye!"), 2);
        }
        $msg = __("Created Errand Worker {0} under PID {1}.", $worker->name, $worker->pid);
        $io->info($msg, 3);

        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => 'Started Errand Service',
            'description' => $msg,
        ];
        $this->Workers->createHeartbeat($worker, $hbOptions);
        //================================================================================


        //====Start Daily Grind=============================================================
        $counter = 1;
        $sleep = 0;
        $timeObjCurrent = new FrozenTime();
        while ($timeObjCurrent->lt($worker->retirement_date)) {

            $errandCount = $this->Errands->getReadyToRunCount();

            if ($errandCount > 0) {
                $sleep = 0;

                $io->out(__("====Start of Errand============================================"));

                $errand = $this->runNextErrand($worker);
                if ($errand) {
                    $msg = __("Completed Errand {0}:{1}.", $errand->id, $errand->name);

                    $io->info($msg);

                    $hbOptions = [
                        'context' => $heartbeatContext,
                        'name' => $msg,
                    ];
                    $this->Workers->createPulse(null, $hbOptions);
                }

                $io->out(__("====End of Errand=============================================="), 3);
            } else {
                //go to sleep for a bit because there is nothing to do
                $sleepTimeout = Configure::read('Settings.errand_worker_sleep');
                $sleep = $this->getSleepLength($sleep, $sleepTimeout);

                $msg = __("Sleeping for {0} seconds.", round($sleep, 1));
                $io->info($msg);

                $worker->errand_name = $msg;
                $worker->errand_link = null;
                $this->Workers->save($worker);

                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => $msg,
                ];
                $this->Workers->createPulse(null, $hbOptions);

                sleep($sleep);
            }

            //refresh worker and check if deleted or been forced into retirement
            $worker = $this->Workers->refreshWorker($worker);
            if ($worker === false) {
                $msg = __("PID {0} was deleted in the GUI. Shutting down the Errand Service", getmypid());
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => 'Deleted Errand Service',
                    'description' => $msg,
                ];
                $this->Workers->createHeartbeat(null, $hbOptions);

                $this->Heartbeats->purgePulses();
                $io->abort($msg, 3);
            } elseif ($worker->force_retirement) {
                $msg = __("Forced retirement for Errand Worker {0} under PID {1}.", $worker->name, $worker->pid);
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => 'Forced Retirement of Errand Service',
                    'description' => $msg,
                ];
                $this->Workers->createHeartbeat($worker, $hbOptions);

                $this->Heartbeats->purgePulses();
                $this->Workers->delete($worker);
                $io->abort($msg, 4);
            } elseif ($worker->force_shutdown) {
                $msg = __("Forced shutdown for Errand Worker {0} under PID {1}.", $worker->name, $worker->pid);
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => 'Forced Shutdown of Errand Service',
                    'description' => $msg,
                ];
                $this->Workers->createHeartbeat($worker, $hbOptions);

                $this->Heartbeats->purgePulses();
                $backgroundServiceName = $worker->background_services_link;
                $this->Workers->delete($worker);
                $this->shutdownBackgroundServiceByName($backgroundServiceName);
                $io->abort($msg, 4);
            }

            //update the current time object and counter
            $timeObjCurrent = new FrozenTime();
            $counter++;
        }
        //====End Daily Grind===============================================================


        //====Retire Worker===============================================================
        $msg = __("Retiring Errand Worker {0} under PID {1}.", $worker->name, $worker->pid);
        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => 'Retiring Errand Service',
            'description' => $msg,
        ];
        $this->Workers->createHeartbeat($worker, $hbOptions);

        $this->Heartbeats->purgePulses();
        $this->Workers->delete($worker);
        $io->abort($msg, 4);
        //================================================================================

        return 0;
    }

    /**
     * Run the Errand
     *
     * @param \App\Model\Entity\Worker $worker
     * @return array|bool|\App\Model\Entity\Errand|null
     */
    private function runNextErrand(\App\Model\Entity\Worker $worker)
    {
        $errand = $this->Errands->getNextErrand();

        if ($errand === false) {
            $worker->errand_name = __("No errands to run...");
            $worker->errand_link = null;
            $this->Workers->save($worker);
            return false;
        }

        $worker->errand_name = Text::truncate($errand->id . ":" . $errand->name, 128);
        $worker->errand_link = $errand->id;
        $this->Workers->save($worker);

        $errand->status = 'Started';
        $errand->progress_bar = 0;
        $errand->worker_link = $worker->id;
        $errand->worker_name = $worker->name;
        $errand->server = $worker->server;
        $this->Errands->save($errand);

        $class = $errand->class;
        $method = $errand->method;
        $parameters = $errand->parameters;

        try {
            //Switch between a Model and Fully Qualified class
            if (strpos($class, "Table") !== false) {
                $class = str_replace("Table", "", $class);
                $Model = $this->loadModel($class);
                $result = $Model->$method(...$parameters);
            } else {
                $Object = new $class();
                $result = $Object->$method(...$parameters);
            }

            $errand->completed = new FrozenTime('now');
            $errand->status = 'Completed';
            $errand->progress_bar = 100;

        } catch (\Throwable $e) {
            $errorsThrown = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];

            $errand->completed = null;
            $errand->status = 'Errored';
            $errand->progress_bar = 0;
            $errand->errors_thrown = $errorsThrown;

            if ($errand->errors_retry < $errand->errors_retry_limit) {
                $errand->errors_retry = $errand->errors_retry + 1;
                $errand->started = null;
                $errand->completed = null;
                $errand->status = null;
                $errand->progress_bar = 0;
            }

        }

        $this->Errands->save($errand);

        return $errand;
    }

    /**
     * Run the Messages Background Service
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int
     */
    private function runMessagesService(Arguments $args, ConsoleIo $io)
    {
        return 0;
    }

    /**
     * Run the Message
     *
     * @param \App\Model\Entity\Worker $worker
     */
    private function runNextMessage(\App\Model\Entity\Worker $worker)
    {
        return 0;
    }

    /**
     * Get an ever increasing $sleepLength till $cap is reached
     *
     * @param int $currentSleepLength
     * @param int $cap
     * @param float|int $rate
     * @return mixed
     */
    public function getSleepLength($currentSleepLength = 1, $cap = 8, $rate = 1.1)
    {
        if ($currentSleepLength <= 0) {
            $currentSleepLength = 1;
        }

        //make sure the $rate >1 otherwise would never sleep
        $rate = max($rate, 1.1);

        $newSleepLength = $currentSleepLength * $rate;
        return min($newSleepLength, $cap);
    }

    /**
     * Shutdown a Background Service.
     *
     * @param $serviceName
     * @return bool
     */
    private function shutdownBackgroundServiceByName($serviceName)
    {
        $cmd = __("net stop \"{0}\" 2>&1", $serviceName);
        exec($cmd, $out, $ret);

        $out = implode(" ", $out);

        if (strpos(strtolower($out), 'success') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
