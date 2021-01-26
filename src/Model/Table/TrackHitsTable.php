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
class TrackHitsTable extends Table
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
            ->decimal('response_time')
            ->allowEmptyString('response_time');

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

    public function trackHit(\Psr\Http\Message\UriInterface $passed, $otherData = [])
    {
        $url = $passed->__toString();

        $scheme = $passed->getScheme();
        $host = $passed->getHost();
        $port = $passed->getPort();
        $path = $passed->getPath();
        $query = $passed->getQuery();

        $headers = getallheaders();

        if (isset($headers['Accept'])) {
            unset($headers['Accept']);
        }

        if (isset($headers['Cookie'])) {
            unset($headers['Cookie']);
        }

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $http_client_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $http_client_ip = '';
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $http_x_forwarded_for = '';
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        } else {
            $remote_addr = '';
        }

        $data = [
            'scheme' => $scheme,
            'host' => $host,
            'path' => $path,
            'query' => $query,
            'HTTP_CLIENT_IP' => $http_client_ip,
            'HTTP_X_FORWARDED_FOR' => $http_x_forwarded_for,
            'REMOTE_ADDR' => $remote_addr,
            'headers' => $headers,
        ];
        $data = array_merge($data, $otherData);

        $hit = $this->newEntity();
        $hit->url = $url;
        $hit->data = $data;
        $hit->scheme = substr($scheme, 0, 10);
        $hit->host = substr($host, 0, 255);
        $hit->port = substr($port, 0, 10);
        $hit->path = substr($path, 0, 255);
        $hit->query = substr($query, 0, 255);
        $hit->response_time = round($data['app_execution_time'], 10);

        $tryCounter = 0;
        $tryLimit = 5;
        $isSaved = false;
        while ($tryCounter < $tryLimit & !$isSaved) {
            try {
                $isSaved = $this->save($hit);
            } catch (\Throwable $exception) {
                //do nothing, non critical error
            }

            $tryCounter++;
        }
    }
}
