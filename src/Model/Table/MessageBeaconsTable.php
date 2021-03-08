<?php

namespace App\Model\Table;

use App\Model\Entity\MessageBeacon;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MessageBeacons Model
 *
 * @method \App\Model\Entity\MessageBeacon get($primaryKey, $options = [])
 * @method \App\Model\Entity\MessageBeacon newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MessageBeacon[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MessageBeacon|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MessageBeacon saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MessageBeacon patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MessageBeacon[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MessageBeacon findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MessageBeaconsTable extends AppTable
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

        $this->setTable('message_beacons');
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('beacon_hash')
            ->maxLength('beacon_hash', 50)
            ->allowEmptyString('beacon_hash');

        $validator
            ->scalar('beacon_url')
            ->maxLength('beacon_url', 255)
            ->allowEmptyString('beacon_url');

        $validator
            ->scalar('beacon_data')
            ->maxLength('beacon_data', 2048)
            ->allowEmptyString('beacon_data');

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
            'beacon_data',
        ];

        return $jsonFields;
    }

    public function logBeacon(\Psr\Http\Message\UriInterface $passed, array $options)
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
            'port' => $port,
            'path' => $path,
            'query' => $query,
            'HTTP_CLIENT_IP' => $http_client_ip,
            'HTTP_X_FORWARDED_FOR' => $http_x_forwarded_for,
            'REMOTE_ADDR' => $remote_addr,
            'headers' => $headers,
        ];

        if (isset($options[0])) {
            if (strlen($options[0]) == 40) {
                $chars = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'a', 'b', 'c', 'd', 'e', 'f'];
                if (str_replace($chars, '', $options[0]) === '') {
                    $beaconHash = $options[0];
                } else {
                    $beaconHash = null;
                }
            } else {
                $beaconHash = null;
            }
        } else {
            $beaconHash = null;
        }

        $messageBeacon = $this->newEntity();
        $messageBeacon->beacon_data = $data;
        $messageBeacon->beacon_url = $url;
        $messageBeacon->beacon_hash = $beaconHash;

        $tryCounter = 0;
        $tryLimit = 5;
        $isSaved = false;
        while ($tryCounter < $tryLimit & !$isSaved) {
            try {
                $isSaved = $this->save($messageBeacon);
            } catch (\Throwable $exception) {
                //do nothing, non critical error
            }
            usleep(30);

            $tryCounter++;
        }

        return $isSaved;
    }
}
