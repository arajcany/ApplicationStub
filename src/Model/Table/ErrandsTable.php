<?php

namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use PDOException;

/**
 * Errands Model
 *
 * @method \App\Model\Entity\Errand get($primaryKey, $options = [])
 * @method \App\Model\Entity\Errand newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Errand[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Errand|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Errand saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Errand patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Errand[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Errand findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ErrandsTable extends Table
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

        $this->setTable('errands');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * @param TableSchema $schema
     * @return TableSchema
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $jsonFields = $this->getJsonFields();

        foreach ($jsonFields as $jsonField) {
            $schema->setColumnType($jsonField, 'json');
        }

        return $schema;
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
            ->dateTime('activation')
            ->allowEmptyDateTime('activation');

        $validator
            ->dateTime('expiration')
            ->allowEmptyDateTime('expiration');

        $validator
            ->boolean('auto_delete')
            ->allowEmptyString('auto_delete');

        $validator
            ->integer('wait_for_link')
            ->allowEmptyString('wait_for_link');

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
            ->integer('worker_link')
            ->allowEmptyString('worker_link');

        $validator
            ->scalar('worker_name')
            ->maxLength('worker_name', 128)
            ->allowEmptyString('worker_name');

        $validator
            ->scalar('class')
            ->maxLength('class', 255)
            ->allowEmptyString('class');

        $validator
            ->scalar('method')
            ->maxLength('method', 255)
            ->allowEmptyString('method');

        $validator
            ->scalar('parameters')
            ->allowEmptyString('parameters');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->allowEmptyString('status');

        $validator
            ->dateTime('started')
            ->allowEmptyDateTime('started');

        $validator
            ->dateTime('completed')
            ->allowEmptyDateTime('completed');

        $validator
            ->integer('progress_bar')
            ->allowEmptyString('progress_bar');

        $validator
            ->integer('priority')
            ->allowEmptyString('priority');

        $validator
            ->integer('return_value')
            ->allowEmptyString('return_value');

        $validator
            ->scalar('return_message')
            ->allowEmptyString('return_message');

        $validator
            ->scalar('errors_thrown')
            ->allowEmptyString('errors_thrown');

        $validator
            ->integer('errors_retry')
            ->allowEmptyString('errors_retry');

        $validator
            ->integer('errors_retry_limit')
            ->allowEmptyString('errors_retry_limit');

        $validator
            ->scalar('hash_sum')
            ->maxLength('hash_sum', 50)
            ->allowEmptyString('hash_sum');

        return $validator;
    }

    /**
     * List of properties that can be JSON encoded
     *
     * @return array
     */
    public function getJsonFields()
    {
        $jsonFields = [
            'parameters',
            'return_message',
            'errors_thrown',
        ];

        return $jsonFields;
    }


    /**
     * Count how many Errands are ready to run
     *
     * @return int|null
     */
    public function getReadyToRunCount()
    {
        //prevent deadlocks
        try {
            $errandQuery = $this->buildQueryForErrands();
            $count = $errandQuery->count();
        } catch (\Throwable $e) {
            $count = 0;
        }

        return $count;
    }

    /**
     * @return array|bool|\App\Model\Entity\Errand|null
     */
    public function getNextErrand()
    {
        /**
         * @var \App\Model\Entity\Errand $errand
         */

        //prevent deadlocks
        try {
            //find and lock a row in one operation to prevent SQL race condition
            $errandQuery = $this->buildQueryForErrandsRowLock();
            $rnd = sha1(mt_rand(1, mt_getrandmax()));
            $query = $this->query();
            $res = $query->update()
                ->set(['status' => $rnd])
                ->where(['id' => $errandQuery])
                ->rowCountAndClose();
        } catch (\Throwable $e) {
            return false;
        }

        if ($res == 0) {
            //no errands to run
            return false;
        }

        $errandRetryLimit = Configure::read("Settings.errand_retry_limit");
        $errandRetryLimit = max(1, $errandRetryLimit);
        $errandRetry = 0;
        while ($errandRetry < $errandRetryLimit) {
            //prevent deadlocks
            try {
                //now get the locked row
                $errand = $this->find('all')->where(['status' => $rnd])->first();
                if ($errand) {
                    $timeObjCurrent = new FrozenTime();
                    $errand->started = $timeObjCurrent;
                    $errand->status = 'Allocated';
                    $errand->progress_bar = 0;
                    $this->save($errand);
                    return $errand;
                } else {
                    return false;
                }
            } catch (\Throwable $e) {
                $errandRetry++;
            }
        }

        return false;
    }

    /**
     * Returns a query of Errands that can be run
     *
     * @return \Cake\ORM\Query
     */
    public function buildQueryForErrandsRowLock()
    {
        $timeObjCurrent = new FrozenTime();

        $selectList = [
            "Errands.id",
        ];
        $errandQuery = $this->find('all')
            ->join([
                'ErrandsParent' => [
                    'table' => 'errands',
                    'alias' => 'ErrandsParent',
                    'type' => 'LEFT',
                    'conditions' => 'ErrandsParent.id = Errands.wait_for_link'
                ],
            ])
            ->select($selectList)
            ->where(['Errands.status IS NULL'])
            ->where(['Errands.started IS NULL'])
            ->where(['OR' => ['Errands.activation <=' => $timeObjCurrent, 'Errands.activation IS NULL']])
            ->where(['OR' => ['Errands.expiration >=' => $timeObjCurrent, 'Errands.expiration IS NULL']])
            ->where(['OR' => ['Errands.wait_for_link IS NULL', 'ErrandsParent.completed IS NOT NULL']])
            ->orderAsc('Errands.priority')
            ->orderAsc('Errands.id')
            ->limit(1);

        return $errandQuery;
    }

    /**
     * Returns a query of Errands that can be run
     *
     * @return \Cake\ORM\Query
     */
    public function buildQueryForErrands()
    {
        $timeObjCurrent = new FrozenTime();

        $selectList = [
            "Errands.id",
            "Errands.created",
            "Errands.modified",
            "Errands.activation",
            "Errands.expiration",
            "Errands.auto_delete",
            "Errands.wait_for_link",
            "Errands.name",
            "Errands.class",
            "Errands.method",
            "Errands.parameters",
            "Errands.started",
            "Errands.completed",
            "Errands.status",
            "Errands.progress_bar",
            "Errands.priority",
            "Errands.return_value",
            "Errands.return_message",
            "ErrandsParent.id",
            "ErrandsParent.started",
            "ErrandsParent.completed",
        ];
        $errandQuery = $this->find('all')
            ->join([
                'ErrandsParent' => [
                    'table' => 'errands',
                    'alias' => 'ErrandsParent',
                    'type' => 'LEFT',
                    'conditions' => 'ErrandsParent.id = Errands.wait_for_link'
                ],
            ])
            ->select($selectList)
            ->where(['Errands.started IS NULL'])
            ->where(['OR' => ['Errands.activation <=' => $timeObjCurrent, 'Errands.activation IS NULL']])
            ->where(['OR' => ['Errands.expiration >=' => $timeObjCurrent, 'Errands.expiration IS NULL']])
            ->where(['OR' => ['Errands.wait_for_link IS NULL', 'ErrandsParent.completed IS NOT NULL']])
            ->orderAsc('Errands.priority')
            ->orderAsc('Errands.id')
            ->limit(1);

        return $errandQuery;
    }

    /**
     * @param array $options
     * @param bool $preventDuplicateCreation
     * @return \App\Model\Entity\Errand|bool
     */
    public function createErrand(array $options = [], $preventDuplicateCreation = true)
    {
        $activation = new FrozenTime();
        $expiration = new FrozenTime('+ ' . Configure::read('Settings.data_purge') . ' months');
        $errandRetryLimit = Configure::read("Settings.errand_retry_limit");

        $defaultOptions = [
            'activation' => $activation,
            'expiration' => $expiration,
            'auto_delete' => true,
            'wait_for_link' => null,
            'server' => null,
            'domain' => parse_url(Router::url("/", true), PHP_URL_HOST),
            'name' => ' ',
            'worker_link' => null,
            'worker_name' => null,
            'class' => null,
            'method' => null,
            'parameters' => null,
            'status' => null,
            'started' => null,
            'completed' => null,
            'progress_bar' => null,
            'priority' => 5,
            'return_value' => null,
            'return_message' => null,
            'errors_thrown' => null,
            'errors_retry' => 0,
            'errors_retry_limit' => $errandRetryLimit,
            'hash_sum' => null,
        ];

        $options = array_merge($defaultOptions, $options);
        if ($options['parameters']) {
            $parameters = $options['parameters'];
            unset($options['parameters']);
        } else {
            $parameters = null;
        }
        $errand = $this->newEntity($options);
        $errand->parameters = $parameters;

        $hashSumParams = [
            $errand->class,
            $errand->method,
            $errand->parameters,
            $errand->priority,
        ];
        $hashSum = sha1(json_encode($hashSumParams));
        $errand->hash_sum = $hashSum;


        $queryIsHashExistsCount = $this->find('all')
            ->where(['hash_sum' => $hashSum])
            ->where(['activation <=' => $activation])
            ->where(['expiration >=' => $activation])
            ->count();

        if ($preventDuplicateCreation && ($queryIsHashExistsCount > 0)) {
            return false;
        } else {
            return $this->save($errand);
        }

    }


    /**
     * Delete duplicate Errands
     *
     * @return int
     */
    public function deleteDuplicates()
    {
        $queryDelete = $this->deleteDuplicatesQuery();

        $currentTime = time();
        $futureTime = $currentTime + 10;
        $rowCount = false;
        while ($currentTime <= $futureTime && $rowCount === false) {
            try {
                $time_start = microtime(true);
                $rowCount = $queryDelete->rowCountAndClose();
                $time_end = microtime(true);
                $time_total = $time_end - $time_start;
            } catch (PDOException $e) {
            }
            $currentTime = time();
        }

        return $rowCount;
    }


    /**
     * Delete duplicate Errands
     *
     * @return \Cake\Database\Query
     */
    public function deleteDuplicatesQuery()
    {
        $utcDateString = (new FrozenTime())->setTimezone('UTC')->format('Y-m-d H:i:s');

        $conn = ConnectionManager::get('default');

        $subTableToSelectFrom = $this->findSubTableForCompare();

        $queryDistinct = $conn->newQuery();
        $queryDistinct = $queryDistinct
            ->select(['MIN(id)'])
            ->from(['Errands' => $subTableToSelectFrom])
            ->where(['started IS' => null, 'completed IS' => null])
            ->where(['activation <' => $utcDateString, 'expiration >' => $utcDateString])
            ->group(['class', 'method', 'parameters', 'priority']);


        $queryWaitForParent = $conn->newQuery();
        $queryWaitForParent = $queryWaitForParent
            ->select(['id'])
            ->from('errands')
            ->where(['wait_for_link IS NOT' => null]);


        $queryDelete = $conn->newQuery();
        $queryDelete = $queryDelete
            ->delete('errands')
            ->where(["id NOT IN" => $queryDistinct])
            ->where(["id NOT IN" => $queryWaitForParent])
            ->where(['started IS' => null, 'completed IS' => null]);

        return $queryDelete;
    }

    /**
     * Because the 'parameters' column is TEXT it cannot be directly used in a compare for SELECT DISTINCT queries.
     * This creates a sub-table where 'parameters' are converted to nvarchar(1024)
     *
     * @return Query
     */
    public function findSubTableForCompare()
    {
        $selects = [
            'id' => 'id',
            'class' => 'class',
            'method' => 'method',
            'parameters' => 'convert(nvarchar(1024),parameters)',
            'priority' => 'priority',
            'started' => 'started',
            'completed' => 'completed',
            'activation' => 'activation',
            'expiration' => 'expiration',
        ];

        $query = $this->find('all')
            ->select($selects);

        return $query;
    }
}
