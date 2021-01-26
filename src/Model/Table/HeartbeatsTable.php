<?php

namespace App\Model\Table;

use App\Model\Entity\Heartbeat;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Validation\Validator;

/**
 * Heartbeats Model
 *
 * @method \App\Model\Entity\Heartbeat get($primaryKey, $options = [])
 * @method \App\Model\Entity\Heartbeat newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Heartbeat[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Heartbeat|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Heartbeat saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Heartbeat patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Heartbeat[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Heartbeat findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HeartbeatsTable extends Table
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

        $this->setTable('heartbeats');
        $this->setDisplayField('id');
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
            ->dateTime('expiration')
            ->allowEmptyDateTime('expiration');

        $validator
            ->boolean('auto_delete')
            ->allowEmptyString('auto_delete');

        $validator
            ->scalar('server')
            ->maxLength('server', 128)
            ->allowEmptyString('server');

        $validator
            ->scalar('domain')
            ->maxLength('domain', 128)
            ->allowEmptyString('domain');

        $validator
            ->scalar('type')
            ->maxLength('type', 128)
            ->allowEmptyString('type');

        $validator
            ->scalar('context')
            ->maxLength('context', 128)
            ->allowEmptyString('context');

        $validator
            ->integer('pid')
            ->allowEmptyString('pid');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 128)
            ->allowEmptyString('description');

        return $validator;
    }

    /**
     * Log a heartbeat
     *
     * @param array $options
     * @return \App\Model\Entity\Heartbeat|false
     * @deprecatd Use createPulse() and createHeartbeat() instead
     */
    public function create(array $options)
    {
        $expiration = new FrozenTime('+ ' . Configure::read('Settings.audit_purge') . ' months');

        $defaultOptions = [
            'expiration' => $expiration,
            'auto_delete' => true,
            'server' => gethostname(),
            'domain' => parse_url(Router::url("/", true), PHP_URL_HOST),
            'type' => '',
            'context' => '',
            'pid' => getmypid(),
        ];

        $options = array_merge($defaultOptions, $options);

        $options['type'] = substr($options['type'], 0, 128);
        $options['context'] = substr($options['context'], 0, 128);
        $options['domain'] = substr($options['domain'], 0, 128);

        $heartbeat = $this->newEntity($options);
        return $this->save($heartbeat);
    }

    /**
     * Wrapper function
     *
     * @param array $options
     * @return mixed
     */
    public function createHeartbeat(array $options)
    {
        $options['type'] = 'heartbeat';
        return $this->_createHeartbeatOrPulse($options);
    }

    /**
     * Wrapper function
     *
     * @param array $options
     * @return mixed
     */
    public function createPulse(array $options)
    {
        $options['type'] = 'pulse';
        return $this->_createHeartbeatOrPulse($options);
    }

    /**
     * Create a heartbeat or pulse.
     *
     * @param array $options
     * @return \App\Model\Entity\Heartbeat|false
     */
    public function _createHeartbeatOrPulse(array $options)
    {
        $expiration = new FrozenTime('+ ' . Configure::read('Settings.audit_purge') . ' months');

        $defaultOptions = [
            'expiration' => $expiration,
            'auto_delete' => true,
            'server' => gethostname(),
            'pid' => getmypid(),
            'type' => '',
            'context' => '',
            'domain' => parse_url(Router::url("/", true), PHP_URL_HOST),
            'name' => '',
            'description' => '',
        ];

        $options = array_merge($defaultOptions, $options);

        $options['type'] = substr($options['type'], 0, 128);
        $options['context'] = substr($options['context'], 0, 128);
        $options['domain'] = substr($options['domain'], 0, 128);
        $options['name'] = substr($options['name'], 0, 128);
        $options['description'] = substr($options['description'], 0, 128);

        $heartbeat = $this->newEntity($options);
        return $this->save($heartbeat);
    }

    /**
     * Find Heartbeats
     *
     * @param null $pid
     * @param null $limit
     * @return Query
     */
    public function findHeartbeats($pid = null, $limit = null)
    {
        if (!$pid) {
            $pid = getmypid();
        }

        $query = $this->find('all')
            ->where(['pid' => $pid])
            ->where(['type IN' => ['heartbeats']])
            ->orderAsc('id')
            ->limit($limit);

        return $query;
    }

    /**
     * Find Pulses
     *
     * @param null $pid
     * @param null $limit
     * @return Query
     */
    public function findPulses($pid = null, $limit = null)
    {
        if (!$pid) {
            $pid = getmypid();
        }

        $query = $this->find('all')
            ->where(['pid' => $pid])
            ->where(['type IN' => ['pulses']])
            ->orderAsc('id')
            ->limit($limit);

        return $query;
    }

    /**
     * Find Heartbeats and Pulses
     *
     * @param null $pid
     * @param null $limit
     * @return Query
     */
    public function findHeartbeatsAndPulses($pid = null, $limit = null)
    {
        if (!$pid) {
            $pid = getmypid();
        }

        $query = $this->find('all')
            ->where(['pid' => $pid])
            ->where(['type IN' => ['heartbeats', 'pulses']])
            ->orderAsc('id')
            ->limit($limit);

        return $query;
    }

    /**
     * Purge Heartbeats based on the System PID.
     * This will also purge Pulses that belong to the Heartbeat
     *
     * @return int
     */
    public function purgeHeartbeats()
    {
        $pid = getmypid();
        $result = $this->deleteAll(['pid' => $pid, ['OR' => ['type /*is pulse*/' => 'pulse', 'type /*is heartbeat*/' => 'heartbeat']]]);

        return $result;
    }

    /**
     * Purge Pulses based on the System PID.
     *
     * @return int
     */
    public function purgePulses()
    {
        $pid = getmypid();
        $result = $this->deleteAll(['pid' => $pid, 'type' => 'pulse']);

        return $result;
    }

    /**
     * Find the last Heartbeats
     *
     * @param int $limit
     * @return
     */
    public function findLastHeartbeats()
    {
        $cte = ";WITH cte AS ( SELECT *, ROW_NUMBER() OVER (PARTITION BY context ORDER BY created DESC) AS rn FROM Heartbeats Where type = 'heartbeat' )SELECT * FROM cte WHERE rn = 1";

        $conn = $this->getConnection();
        $results = $conn->execute($cte)->fetchAll('assoc');
        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }

        $selectList = [
            'created',
            'context',
            'pid',
        ];

        if (empty($ids)) {
            //return a Query that will give an empty result
            return $this->find('all')->where(['id' => 0]);
        }

        $contexts = $this->getHeartbeatContexts();

        $query = $this->find('all')
            ->select($selectList, true)
            ->where(['context IN' => $contexts])
            ->where(['id IN' => $ids])
            ->orderAsc('context');

        return $query;
    }

    /**
     * Find the last pulse for the given Heartbeat
     *
     * @param Heartbeat $heartbeat
     * @param int $limit
     * @return Query
     */
    public function findPulsesForHeartbeat(Heartbeat $heartbeat, $limit = 10)
    {
        $query = $this->find('all')
            ->where(['pid' => $heartbeat->pid])
            ->where(['type' => 'pulse'])
            ->limit($limit)
            ->orderDesc('id');

        return $query;
    }

    /**
     * List out the Heartbeats
     *
     * @return array
     */
    public function getHeartbeatTypes()
    {
        $selectList = [
            'type'
        ];
        $query = $this->find('list', ['keyField' => 'id', 'valueField' => 'type'])
            ->select($selectList, true)
            ->distinct(['type'])
            ->where(['type !=' => 'pulse'])
            ->group(['type'])
            ->orderAsc('type');

        return $query->toArray();
    }

    /**
     * List out the Heartbeats
     *
     * @return array
     */
    public function getHeartbeatContexts()
    {
        $selectList = [
            'context'
        ];
        $query = $this->find('list', ['keyField' => 'id', 'valueField' => 'context'])
            ->select($selectList, true)
            ->distinct(['context'])
            ->where(['type !=' => 'pulse'])
            ->group(['context'])
            ->orderAsc('context');

        return $query->toArray();
    }
}
