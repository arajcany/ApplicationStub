<?php

namespace App\Model\Table;

use App\Model\Entity\Worker;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use PDOException;

/**
 * Workers Model
 *
 * @property \App\Model\Table\HeartbeatsTable&\Cake\ORM\Association\HasMany $Heartbeats
 *
 * @method Worker get($primaryKey, $options = [])
 * @method Worker newEntity($data = null, array $options = [])
 * @method Worker[] newEntities(array $data, array $options = [])
 * @method Worker|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Worker saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Worker patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Worker[] patchEntities($entities, array $data, array $options = [])
 * @method Worker findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WorkersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('workers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Heartbeats')
            ->setProperty('heartbeats')
            ->setConditions(['type' => 'heartbeat'])
            ->setForeignKey('pid')
            ->setBindingKey('pid');

        $this->hasMany('Pulses', ['className' => 'Heartbeats'])
            ->setProperty('pulses')
            ->setConditions(['type' => 'pulse'])
            ->setForeignKey('pid')
            ->setBindingKey('pid');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('server')
            ->maxLength('server', 128)
            ->allowEmptyString('server');

        $validator
            ->scalar('domain')
            ->maxLength('domain', 128)
            ->allowEmptyString('domain');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->allowEmptyString('name');

        $validator
            ->scalar('type')
            ->maxLength('type', 128)
            ->allowEmptyString('type');

        $validator
            ->integer('errand_link')
            ->allowEmptyString('errand_link');

        $validator
            ->scalar('errand_name')
            ->maxLength('errand_name', 128)
            ->allowEmptyString('errand_name');

        $validator
            ->dateTime('appointment_date')
            ->allowEmptyDateTime('appointment_date');

        $validator
            ->dateTime('retirement_date')
            ->allowEmptyDateTime('retirement_date');

        $validator
            ->dateTime('termination_date')
            ->allowEmptyDateTime('termination_date');

        $validator
            ->boolean('force_retirement')
            ->allowEmptyString('force_retirement');

        $validator
            ->boolean('force_shutdown')
            ->allowEmptyString('force_shutdown');

        $validator
            ->integer('pid')
            ->allowEmptyString('pid');

        $validator
            ->scalar('background_services_link')
            ->maxLength('background_services_link', 128)
            ->allowEmptyString('background_services_link');

        return $validator;
    }

    /**
     * Find a Worker based on passed in Entity/ID or default to current System ProcessID
     *
     * @param null|int|Worker $idOrEntity
     * @param int|bool $containHeartbeats
     * @param int|bool $containPulses
     * @return array|Query
     */
    public function findWorker($idOrEntity = null, $containHeartbeats = null, $containPulses = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
        } else {
            $key = 'pid';
            $value = getmypid();
        }

        //base Query
        $query = $this->find('all')
            ->limit(1)
            ->orderDesc('id')
            ->where([$key => $value]);

        //contain Heartbeats
        if ($containHeartbeats === true) {
            $query = $query
                ->contain(['heartbeats' => function ($q) use ($containHeartbeats) {
                    return $q->orderAsc('id');
                }]);
        } elseif (is_numeric($containHeartbeats)) {
            $query = $query
                ->contain(['heartbeats' => function ($q) use ($containHeartbeats) {
                    return $q->orderAsc('id');
                }]);
        }

        //contain Pulses
        if ($containPulses === true) {
            $query = $query
                ->contain(['pulses' => function ($q) use ($containPulses) {
                    return $q->orderAsc('id')->limit($containPulses);
                }]);
        } elseif (is_numeric($containPulses)) {
            $query = $query
                ->contain(['pulses' => function ($q) use ($containPulses) {
                    return $q->orderAsc('id')->limit($containPulses);
                }]);
        }

        return $query;
    }

    /**
     * Create a Worker and return the Entity
     *
     * @param string $type
     * @param array $options
     * @return Worker|bool
     */
    public function createWorker($type = 'errand', $options = [])
    {
        $allowed = ['errand', 'message'];

        if (!in_array($type, $allowed)) {
            return false;
        }

        //create some defaults based on Settings
        $workerLifeExpectancy = Configure::read("Settings.{$type}_worker_life_expectancy");
        $workerGracePeriod = Configure::read("Settings.{$type}_worker_grace_period");

        $timeObjCurrent = new FrozenTime('now');

        $timeObjRetirement = new FrozenTime('now');
        $timeObjRetirement = $timeObjRetirement->addMinutes($workerLifeExpectancy);

        $timeObjTermination = new FrozenTime('now');
        $timeObjTermination = $timeObjTermination->addMinutes($workerLifeExpectancy + $workerGracePeriod);

        //create the Worker
        $worker = $this->newEntity();
        $worker->name = $this->makeRandomName();
        $worker->type = $type;
        $worker->errand_link = null;
        $worker->errand_name = null;
        $worker->appointment_date = $timeObjCurrent;
        $worker->retirement_date = $timeObjRetirement;
        $worker->termination_date = $timeObjTermination;
        $worker->force_retirement = false;
        $worker->force_shutdown = false;
        $worker->pid = getmypid();
        $worker->background_services_link = '';
        $worker->server = gethostname();
        $worker->domain = parse_url(Router::url("/", true), PHP_URL_HOST);

        $this->patchEntity($worker, $options);

        $worker = $this->save($worker);

        return $worker;
    }

    /**
     * Refresh picks up on changes to a Worker Entity.
     * Outside processes can alter Worker properties such as retirement/termination date.
     *
     * @param Worker|int|null $idOrEntity
     * @return Worker|bool
     */
    public function refreshWorker($idOrEntity = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
        } else {
            $key = 'pid';
            $value = getmypid();
        }

        //base Query
        /**
         * @var Worker|array|null $worker
         */
        $worker = $this->find('all')
            ->limit(1)
            ->orderDesc('id')
            ->where([$key => $value])
            ->first();

        if ($worker) {
            return $worker;
        } else {
            return false;
        }
    }

    /**
     * Wrapper function
     *
     * @param Worker|int|null $idOrEntity
     * @param array $options
     * @return \App\Model\Entity\Heartbeat|false
     */
    public function createHeartbeat($idOrEntity = null, $options = null)
    {
        $options['type'] = 'heartbeat';
        return $this->_createHeartbeatOrPulse($idOrEntity, $options);
    }

    /**
     * Wrapper function
     *
     * @param Worker|int|null $idOrEntity
     * @param array $options
     * @return \App\Model\Entity\Heartbeat|false
     */
    public function createPulse($idOrEntity = null, $options = null)
    {
        $options['type'] = 'pulse';
        return $this->_createHeartbeatOrPulse($idOrEntity, $options);
    }

    /**
     * Refresh picks up on changes to a Worker Entity.
     * Outside processes can alter Worker properties such as retirement/termination date.
     *
     * @param Worker|int|null $idOrEntity
     * @param array $options
     * @return \App\Model\Entity\Heartbeat|false
     */
    public function _createHeartbeatOrPulse($idOrEntity = null, $options = [])
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
            $pid = null;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
            $pid = $idOrEntity->pid;
        } else {
            $key = 'pid';
            $value = getmypid();
            $pid = getmypid();
        }

        if (!$pid) {
            /**
             * @var Worker|array|null $worker
             */
            $worker = $this->find('all')
                ->limit(1)
                ->orderDesc('id')
                ->where([$key => $value])
                ->first();

            if ($worker) {
                $pid = $worker->pid;
            } else {
                $pid = -1;
            }
        }

        $options['pid'] = $pid;
        return $this->Heartbeats->_createHeartbeatOrPulse($options);
    }


    /**
     * Flag a Worker to retire
     *
     * @param Worker|int|null $idOrEntity
     * @return int
     */
    public function retireWorker($idOrEntity = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
        } else {
            $key = 'pid';
            $value = getmypid();
        }

        $query = $this->getConnection()->newQuery();
        $query->update($this->getTable())
            ->set(['force_retirement' => 1])
            ->where([$key => $value]);

        return $query->rowCountAndClose();
    }

    /**
     * Flag a Worker to retire
     *
     * @param Worker|int|null $idOrEntity
     * @return int
     */
    public function shutdownWorker($idOrEntity = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
        } else {
            $key = 'pid';
            $value = getmypid();
        }

        $query = $this->getConnection()->newQuery();
        $query->update($this->getTable())
            ->set(['force_shutdown' => 1])
            ->where([$key => $value]);

        return $query->rowCountAndClose();
    }

    /**
     * Find Heartbeats
     *
     * @param Worker|int|null $idOrEntity
     * @return Query
     */
    public function findHeartbeats($idOrEntity = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
            $pid = null;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
            $pid = $idOrEntity->pid;
        } else {
            $key = 'pid';
            $value = getmypid();
            $pid = getmypid();
        }

        if (!$pid) {
            /**
             * @var Worker|array|null $worker
             */
            $worker = $this->find('all')
                ->limit(1)
                ->orderDesc('id')
                ->where([$key => $value])
                ->first();

            if ($worker) {
                $pid = $worker->pid;
            } else {
                $pid = -1;
            }
        }

        return $this->Heartbeats->findHeartbeats($pid);
    }

    /**
     * Find Heartbeats
     *
     * @param Worker|int|null $idOrEntity
     * @return Query
     */
    public function findPulses($idOrEntity = null)
    {
        //find based on passed in Entity/ID or default to current System ProcessID
        if (is_numeric($idOrEntity)) {
            $key = 'id';
            $value = $idOrEntity;
            $pid = null;
        } elseif ($idOrEntity instanceof Worker) {
            $key = 'id';
            $value = $idOrEntity->id;
            $pid = $idOrEntity->pid;
        } else {
            $key = 'pid';
            $value = getmypid();
            $pid = getmypid();
        }

        if (!$pid) {
            /**
             * @var Worker|array|null $worker
             */
            $worker = $this->find('all')
                ->limit(1)
                ->orderDesc('id')
                ->where([$key => $value])
                ->first();

            if ($worker) {
                $pid = $worker->pid;
            } else {
                $pid = -1;
            }
        }

        return $this->Heartbeats->findPulses($pid);
    }

    /**
     * Flag all Workers to retire
     *
     * @return int
     */
    public function retireAllWorkers()
    {
        $query = $this->getConnection()->newQuery();
        $query = $query->update($this->getTable())
            ->set(['force_retirement' => 1]);

        try {
            $result = $query->rowCountAndClose();
        } catch (\Throwable $exception) {
            $result = 0;
        }

        return $result;
    }

    /**
     * Flag all Workers to shutdown
     *
     * @return int
     */
    public function shutdownAllWorkers()
    {
        $query = $this->getConnection()->newQuery();
        $query = $query->update($this->getTable())
            ->set(['force_shutdown' => 1]);

        try {
            $result = $query->rowCountAndClose();
        } catch (\Throwable $exception) {
            $result = 0;
        }

        return $result;
    }

    /**
     * Clean out DB of Workers that are past termination date
     *
     * @return int
     */
    public function purge()
    {
        //modify query
        $query = $this->getConnection()->newQuery();
        $query = $query->delete($this->getTable());

        //these are the potential workers that exist, base on name of PHP executable.
        $phpExeName = "php.exe";
        $cmd = __("tasklist | find \"{0}\" 2>&1", $phpExeName);
        exec($cmd, $out, $ret);
        $string = implode(" ", $out);
        $string = str_replace(" 0 ", "", $string);
        preg_match_all('!\d+\.*\d*!', $string, $phpCleanedList);
        $phpCleanedList = $phpCleanedList[0];

        //clean out workers that are past termination date
        $timeObjCurrent = new FrozenTime();

        //create query
        $query = $query->where(
            [
                'OR' => [
                    ['termination_date <=' => $timeObjCurrent->format("Y-m-d H:i:s")],
                    ['pid NOT IN' => $phpCleanedList]
                ]
            ]
        );

        try {
            $result = $query->rowCountAndClose();
        } catch (\Throwable $exception) {
            $result = 0;
        }

        return $result;
    }

    /**
     * Make a random name
     *
     * @return string
     */
    public function makeRandomName()
    {
        $names = file_get_contents(ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS . 'names.txt');
        $names = str_replace(["\r\n", "\r"], "\n", $names);
        $names = explode("\n", $names);
        $firstNames = [];
        $lastNames = [];

        foreach ($names as $name) {
            $name = explode(" ", $name);

            if (isset($name[0])) {
                $firstNames[] = $name[0];
            }

            if (isset($name[1])) {
                $lastNames[] = $name[1];
            }
        }

        $firstNames = array_values(array_unique($firstNames));
        $lastNames = array_values(array_unique($lastNames));

        $f = $firstNames[mt_rand(0, count($firstNames) - 1)];
        $l = $lastNames[mt_rand(0, count($lastNames) - 1)];

        $name = trim($f . " " . $l);
        return $name;
    }
}
