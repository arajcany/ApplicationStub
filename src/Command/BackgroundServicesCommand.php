<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Network\Exception\SocketException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
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
 * @property ConsoleIo $io
 */
class BackgroundServicesCommand extends Command
{
    public $Settings;
    public $Errands;
    public $Workers;
    public $Messages;
    public $MessageBeacons;
    public $Heartbeats;
    private $io;

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
        $this->Messages = TableRegistry::getTableLocator()->get('Messages');
        $this->MessageBeacons = TableRegistry::getTableLocator()->get('MessageBeacons');
        $this->Heartbeats = TableRegistry::getTableLocator()->get('Heartbeats');

        $serviceType = $args->getArgumentAt(0);
        $this->io = $io;

        if (in_array($serviceType, ['errands', 'errand'])) {
            return $this->runErrandsService($args, $io);
        }

        if (in_array($serviceType, ['messages', 'message'])) {
            return $this->runMessagesService($args, $io);
        }

        $io->abort(__("Please run an 'errand' or 'message' BackgroundService"), 1);
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
        return $this->_runService('message', $args, $io);
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
        return $this->_runService('errand', $args, $io);
    }

    /**
     * Run a Background Service
     *
     * @param $name
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int
     */
    private function _runService($name, Arguments $args, ConsoleIo $io)
    {
        $namePluralUpper = ucwords(Inflector::pluralize($name));
        $namePluralLower = strtolower(Inflector::pluralize($name));
        $nameSingleUpper = ucwords(Inflector::singularize($name));
        $nameSingleLower = strtolower(Inflector::singularize($name));
        $runNext = 'runNext' . $nameSingleUpper;

        $io->out(__("Initiating an {0} Worker...", $nameSingleUpper));

        $heartbeatContext = $args->getOption('heartbeat-context');

        //====Create a Worker=============================================================
        $workerOptions = ['background_services_link' => $heartbeatContext];
        $worker = $this->Workers->createWorker($nameSingleLower, $workerOptions);
        if (!$worker) {
            $io->abort(__("Failed to initiate a Worker...Bye!"), 2);
        }
        $msg = __("Created {0} Worker {1} under PID {2}.", $nameSingleUpper, $worker->name, $worker->pid);
        $io->info($msg, 3);

        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => __("Started {0} Service", $nameSingleUpper),
            'description' => $msg,
        ];
        $this->Workers->createHeartbeat($worker, $hbOptions);
        //================================================================================


        //====Start Daily Grind=============================================================
        $counter = 1;
        $sleep = 0;
        $timeObjCurrent = new FrozenTime();
        while ($timeObjCurrent->lt($worker->retirement_date)) {

            $runCount = $this->$namePluralUpper->getReadyToRunCount();

            if ($runCount > 0) {
                $sleep = 0;

                $io->out(__("====Start of {0}============================================", $nameSingleUpper));

                /**
                 * @var array|bool|null|\App\Model\Entity\Errand|\App\Model\Entity\Message $task
                 */
                $task = $this->$runNext($worker);
                if ($task) {
                    $msg = __("Completed {0} {1}:{2}.", $nameSingleUpper, $task->id, $task->name);

                    $io->info($msg);

                    $hbOptions = [
                        'context' => $heartbeatContext,
                        'name' => $msg,
                    ];
                    $this->Workers->createPulse(null, $hbOptions);
                }

                $io->out(__("====End of {0}==============================================", $nameSingleUpper), 3);
            } else {
                //go to sleep for a bit because there is nothing to do
                $sleepTimeout = Configure::read("Settings.{$nameSingleLower}_worker_sleep");
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
                $msg = __("PID {0} was deleted in the GUI. Shutting down the {1} Service", getmypid(), $nameSingleUpper);
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => __("Deleted {0} Service", $nameSingleUpper),
                    'description' => $msg,
                ];
                $this->Workers->createHeartbeat(null, $hbOptions);

                $this->Heartbeats->purgePulses();
                $io->abort($msg, 3);
            } elseif ($worker->force_retirement) {
                $msg = __("Forced retirement for {0} Worker {1} under PID {2}.", $nameSingleUpper, $worker->name, $worker->pid);
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => __("Forced Retirement of {0} Service", $nameSingleUpper),
                    'description' => $msg,
                ];
                $this->Workers->createHeartbeat($worker, $hbOptions);

                $this->Heartbeats->purgePulses();
                $this->Workers->delete($worker);
                $io->abort($msg, 4);
            } elseif ($worker->force_shutdown) {
                $msg = __("Forced shutdown for {0} Worker {1} under PID {2}.", $nameSingleUpper, $worker->name, $worker->pid);
                $hbOptions = [
                    'context' => $heartbeatContext,
                    'name' => __("Forced Shutdown of {0} Service", $nameSingleUpper),
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
        $msg = __("Retiring {0} Worker {1} under PID {2}.", $nameSingleUpper, $worker->name, $worker->pid);
        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => __("Retiring {0} Service", $nameSingleUpper),
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
            $returnValue = null;
            $returnMessage = null;

            //Switch between a Model and Fully Qualified class
            if (strpos($class, "Table") !== false) {
                $class = str_replace("Table", "", $class);
                $Model = $this->loadModel($class);
                if (empty($parameters)) {
                    $result = $Model->$method();
                } else {
                    $result = $Model->$method(...$parameters);
                }

                if (method_exists($Model, 'getReturnValue')) {
                    $returnValue = $Model->getReturnValue();
                }
                if (method_exists($Model, 'getReturnMessage')) {
                    $returnMessage = $Model->getReturnMessage();
                }
            } else {
                $Object = new $class();
                if (empty($parameters)) {
                    $result = $Object->$method();
                } else {
                    $result = $Object->$method(...$parameters);
                }

                if (method_exists($Object, 'getReturnValue')) {
                    $returnValue = $Object->getReturnValue();
                }
                if (method_exists($Object, 'getReturnMessage')) {
                    $returnMessage = $Object->getReturnMessage();
                }
            }

            $errand->return_value = $returnValue;
            $errand->return_message = $returnMessage;
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
     * Run the Message
     *
     * @param \App\Model\Entity\Worker $worker
     */
    private function runNextMessage(\App\Model\Entity\Worker $worker)
    {
        $email = new Email();

        $message = $this->Messages->getNextMessage();
        if (!$message) {
            return false;
        }

        $worker->errand_name = $message->id . ":" . $message->name;
        $worker->errand_link = $message->id;
        $this->Workers->save($worker);

        $this->io->out($message->id . ":" . $message->name);

        //domain, profile and transport
        if ($message->domain) {
            $email->setDomain($message->domain);
        }
        if ($message->transport) {
            $email->setTransport($message->transport);
        }
        if ($message->profile) {
            $email->setProfile($message->profile);
        }


        //templating
        if ($message->layout) {
            $email->viewBuilder()->setLayout($message->layout);
        }
        if ($message->template) {
            $email->viewBuilder()->setTemplate($message->template);
        }
        if ($message->email_format) {
            $email->setEmailFormat($message->email_format);
        }


        //to and from
        if ($message->sender) {
            $email->setSender($message->sender);
        }
        if ($message->email_from) {
            $email->setFrom($message->email_from);
        }
        if ($message->email_to) {
            $email->setTo($message->email_to);
        }
        if ($message->email_cc) {
            $email->setCc($message->email_cc);
        }
        if ($message->email_bcc) {
            $email->setBcc($message->email_bcc);
        }
        if ($message->reply_to) {
            $email->setReplyTo($message->reply_to);
        }
        if ($message->read_receipt) {
            $email->setReadReceipt($message->read_receipt);
        }


        //subject and body
        if ($message->subject) {
            $email->setSubject($message->subject);
        }

        //view vars
        $additionalViewVars = [
            'domain' => $message->domain,
            'beacon_hash' => $message->beacon_hash,
        ];
        if ($message->view_vars) {
            $viewVars = $message->view_vars;

            if (isset($viewVars['entities'])) {
                $viewVars['entities'] = $this->Messages->expandEntities($viewVars['entities']);
            }

            $email->setViewVars(array_merge($additionalViewVars, $viewVars));
        } else {
            $email->setViewVars($additionalViewVars);
        }


        //headers
        if ($message->headers) {
            $email->setHeaders($message->headers);
        }
        if ($message->priority) {
            $email->setPriority($message->priority);
        }


        //send the message
        try {
            $sendResult = $email->send();
            if ($sendResult) {
                $message->smtp_code = 1;
                $message->smtp_message = __("Email Sent.");

                $message->completed = new FrozenTime();
                $message->errors_thrown = null;
            } else {
                $message->smtp_code = 0;
                $message->smtp_message = __("Email Failed.");

                $message->completed = null;
                $message->errors_thrown = __("Email Failed.");

                if ($message->errors_retry < $message->errors_retry_limit) {
                    $message->errors_retry = $message->errors_retry + 1;
                    $message->started = null;
                    $message->completed = null;
                    $message->lock_code = null;
                }
            }
        } catch (\Throwable $e) {
            $errorsThrown = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];

            $message->smtp_code = 0;
            $message->smtp_message = __("Fatal Error.");

            $message->completed = null;
            $message->errors_thrown = $errorsThrown;

            if ($message->errors_retry < $message->errors_retry_limit) {
                $message->errors_retry = $message->errors_retry + 1;
                $message->started = null;
                $message->completed = null;
                $message->lock_code = null;
            }

        }

        $this->Messages->save($message);

        return $message;
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
