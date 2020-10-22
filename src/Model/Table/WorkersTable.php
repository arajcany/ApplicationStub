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
            ->integer('pid')
            ->allowEmptyString('pid');

        return $validator;
    }

    /**
     * Create a Worker and return the Entity
     *
     * @param string $type
     * @return Worker|bool
     */
    public function getWorker($type = 'errand')
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
        $worker->pid = getmypid();
        $worker->domain = parse_url(Router::url("/", true), PHP_URL_HOST);
        $worker = $this->save($worker);

        return $worker;
    }

    /**
     * Refresh picks up on changes to a Worker Entity.
     * Outside processes can alter Worker properties such as retirement/termination date.
     *
     * @param Worker $worker
     * @return Worker|bool
     */
    public function refreshWorker(Worker $worker)
    {
        //in case the Worker has been deleted in the GUI.
        $check = $this->exists(['id' => $worker->id]);

        if ($check) {
            /**
             * @var Worker $worker
             */
            $worker = $this->find('all')->where(['id' => $worker->id])->first();
            return $worker;
        } else {
            return false;
        }
    }

    /**
     * Flag all Workers to retire
     */
    public function retireAll()
    {
        $query = $this->getConnection()->newQuery();
        $query->update($this->getTable())
            ->set(['force_retirement' => 1]);

        return $query->rowCountAndClose();
    }

    /**
     * Flag a Worker to retire
     */
    public function retire($id)
    {
        $query = $this->getConnection()->newQuery();
        $query->update($this->getTable())
            ->set(['force_retirement' => 1])
            ->where(['id' => $id]);

        return $query->rowCountAndClose();
    }


    /**
     * Clean out DB of Workers that are dead AND past termination date
     *
     * @return bool|int
     */
    public function clean()
    {
        //clean out workers that are past termination date
        $timeObjCurrent = new FrozenTime();
        $query = $this->getConnection()->newQuery();
        $query->delete($this->getTable());

        $query->where(
            [
                'AND' => [
                    ['termination_date <=' => $timeObjCurrent->format("Y-m-d H:i:s")]
                ]
            ]
        );

        return  $query->rowCountAndClose();
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
