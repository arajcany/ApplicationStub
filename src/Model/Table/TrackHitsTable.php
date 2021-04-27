<?php

namespace App\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TrackHits Model
 *
 * @method \App\Model\Entity\TrackHit get($primaryKey, $options = [])
 * @method \App\Model\Entity\TrackHit newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TrackHit[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TrackHit|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TrackHit saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TrackHit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TrackHit[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TrackHit findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TrackHitsTable extends AppTable
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

        $this->setTable('track_hits');
        $this->setDisplayField('id');
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
            ->allowEmptyString('id', null, 'create')
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('url')
            ->maxLength('url', 1024)
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->scalar('scheme')
            ->maxLength('scheme', 10)
            ->allowEmptyString('scheme');

        $validator
            ->scalar('host')
            ->maxLength('host', 255)
            ->allowEmptyString('host');

        $validator
            ->scalar('port')
            ->maxLength('port', 10)
            ->allowEmptyString('port');

        $validator
            ->scalar('path')
            ->maxLength('path', 255)
            ->allowEmptyString('path');

        $validator
            ->scalar('query')
            ->maxLength('query', 255)
            ->allowEmptyString('query');

        $validator
            ->decimal('app_execution_time')
            ->allowEmptyString('app_execution_time');

        $validator
            ->scalar('data')
            ->maxLength('data', 2048)
            ->requirePresence('data', 'create')
            ->notEmptyString('data');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['id']));

        return $rules;
    }

    /**
     * List of properties that can be JSON encoded
     *
     * @return array
     */
    public function getJsonFields()
    {
        $jsonFields = [
            'data',
        ];

        return $jsonFields;
    }

    /**
     * Find the Remote User data such as IP address and Agent
     * @param null $timeStart
     * @param null $timeEnd
     *
     * @return Query
     */
    public function findRemoteUserData($timeStart = null, $timeEnd = null)
    {
        $timeFilteredSubQuery = $this->find('all');
        $selectSub = [
            'id' => 'id',
            'created' => 'created',
            'url' => 'url',
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 'port',
            'path' => 'path',
            'query' => 'query',
            'app_execution_time' => 'app_execution_time',
            'data' => 'data',
        ];
        $timeFilteredSubQuery = $timeFilteredSubQuery->select($selectSub, true);
        if ($timeStart) {
            $timeFilteredSubQuery = $timeFilteredSubQuery->where(['created >=' => $timeStart]);
        }
        if ($timeEnd) {
            $timeFilteredSubQuery = $timeFilteredSubQuery->where(['created <=' => $timeEnd]);
        }

        $select = [
            'id',
            'created',
            'url',
            'scheme',
            'host',
            'port',
            'path',
            'query',
            'app_execution_time',
            'client_ip' => "json_extract(TrackHits.data, '$.HTTP_CLIENT_IP')",
            'remote_address' => "json_extract(TrackHits.data, '$.REMOTE_ADDR')",
            'user_agent' => "json_extract(TrackHits.data, '$.headers.User-Agent')",
            'app_user_id' => "json_extract(TrackHits.data, '$.app_user_id')",
            'app_username' => "json_extract(TrackHits.data, '$.app_username')",
        ];
        $query = $this->find('all')->select($select, false)->from(['TrackHits' => $timeFilteredSubQuery], true);

        return $query;
    }
}
