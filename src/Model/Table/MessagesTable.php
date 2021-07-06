<?php

namespace App\Model\Table;

use arajcany\ToolBox\Utility\Security\Security;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\I18n\FrozenTime;
use Cake\Network\Session;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use App\Model\Entity\Message;

/**
 * Messages Model
 *
 * @method \App\Model\Entity\Message get($primaryKey, $options = [])
 * @method \App\Model\Entity\Message newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Message[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Message|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Message saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Message patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Message[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Message findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \App\Model\Table\MessageBeaconsTable $MessageBeacons
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MessagesTable extends AppTable
{
    private $MessageBeacons;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('messages');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->MessageBeacons = TableRegistry::getTableLocator()->get("MessageBeacons");
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
            ->scalar('type')
            ->maxLength('type', 50)
            ->allowEmptyString('type');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 1024)
            ->allowEmptyString('description');

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
            ->dateTime('started')
            ->allowEmptyDateTime('started');

        $validator
            ->dateTime('completed')
            ->allowEmptyDateTime('completed');

        $validator
            ->scalar('server')
            ->maxLength('server', 128)
            ->allowEmptyString('server');

        $validator
            ->scalar('domain')
            ->maxLength('domain', 255)
            ->allowEmptyString('domain');

        $validator
            ->scalar('transport')
            ->maxLength('transport', 50)
            ->allowEmptyString('transport');

        $validator
            ->scalar('profile')
            ->maxLength('profile', 50)
            ->allowEmptyFile('profile');

        $validator
            ->scalar('layout')
            ->maxLength('layout', 255)
            ->allowEmptyString('layout');

        $validator
            ->scalar('template')
            ->maxLength('template', 255)
            ->allowEmptyString('template');

        $validator
            ->scalar('email_format')
            ->maxLength('email_format', 50)
            ->allowEmptyString('email_format');

        $validator
            ->scalar('sender')
            ->maxLength('sender', 1024)
            ->allowEmptyString('sender');

        $validator
            ->scalar('email_from')
            ->maxLength('email_from', 1024)
            ->allowEmptyString('email_from');

        $validator
            ->scalar('email_to')
            ->maxLength('email_to', 1024)
            ->allowEmptyString('email_to');

        $validator
            ->scalar('email_cc')
            ->maxLength('email_cc', 1024)
            ->allowEmptyString('email_cc');

        $validator
            ->scalar('email_bcc')
            ->maxLength('email_bcc', 1024)
            ->allowEmptyString('email_bcc');

        $validator
            ->scalar('reply_to')
            ->maxLength('reply_to', 1024)
            ->allowEmptyString('reply_to');

        $validator
            ->scalar('read_receipt')
            ->maxLength('read_receipt', 1024)
            ->allowEmptyString('read_receipt');

        $validator
            ->scalar('subject')
            ->maxLength('subject', 1024)
            ->allowEmptyString('subject');

        $validator
            ->scalar('view_vars')
            ->allowEmptyString('view_vars');

        $validator
            ->integer('priority')
            ->allowEmptyString('priority');

        $validator
            ->scalar('headers')
            ->maxLength('headers', 2048)
            ->allowEmptyString('headers');

        $validator
            ->integer('smtp_code')
            ->allowEmptyString('smtp_code');

        $validator
            ->scalar('smtp_message')
            ->maxLength('smtp_message', 2048)
            ->allowEmptyString('smtp_message');

        $validator
            ->integer('lock_code')
            ->allowEmptyString('lock_code');

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
            ->scalar('beacon_hash')
            ->maxLength('beacon_hash', 50)
            ->allowEmptyString('beacon_hash');

        $validator
            ->scalar('hash_sum')
            ->maxLength('hash_sum', 50)
            ->allowEmptyString('hash_sum');

        return $validator;
    }

    /**
     * List of email properties that can be JSON encoded
     *
     * @return array
     */
    public function getJsonFields()
    {
        $jsonFields = [
            'sender',
            'email_from',
            'email_to',
            'email_cc',
            'email_bcc',
            'reply_to',
            'read_receipt',
            'view_vars',
            'headers',
            'errors_thrown',
        ];

        return $jsonFields;
    }

    /**
     * Unset JSON fields when you need to query as the JSON fields as text.
     */
    private function unsetJsonFields()
    {
        $jsonFields = $this->getJsonFields();

        $schema = $this->getSchema();

        foreach ($jsonFields as $jsonField) {
            $schema = $schema->setColumnType($jsonField, 'text');
        }

        $this->setSchema($schema);
    }

    /**
     * Set JSON fields when you have completed the query of JSON fields as text.
     */
    private function setJsonFields()
    {
        $jsonFields = $this->getJsonFields();

        $schema = $this->getSchema();

        foreach ($jsonFields as $jsonField) {
            $schema = $schema->setColumnType($jsonField, 'json');
        }

        $this->setSchema($schema);
    }


    /**
     * Count how many Messages are ready to send
     *
     * @return int|null
     */
    public function getReadyToRunCount()
    {
        $messageQuery = $this->buildQueryForMessages();
        $count = $messageQuery->count();

        return $count;
    }

    /**
     * @param null|string $typeLimit
     * @return array|bool|\App\Model\Entity\Message|null
     */
    public function getNextMessage($typeLimit = null)
    {
        //generate RND number and double check not in use
        $rnd = mt_rand(1, mt_getrandmax());
        $count = $this->find('all')->where(['lock_code' => $rnd])->count();
        if ($count > 0) {
            return false;
        }

        //prevent deadlocks
        try {
            //lock the row first with the RND number
            $messageRowLockSubQuery = $this->buildQueryForMessagesRowLock();
            if ($typeLimit) {
                $messageRowLockSubQuery = $messageRowLockSubQuery->where(['Messages.type' => $typeLimit]);
            }
            $query = $this->query();
            $res = $query->update()
                ->set(['lock_code' => $rnd])
                ->where(['id' => $messageRowLockSubQuery])
                ->rowCountAndClose();
        } catch (\Throwable $e) {
            return false;
        }

        if ($res == 0) {
            //no messages to send
            return false;
        }

        $messageRetryLimit = Configure::read("Settings.message_retry_limit");
        $messageRetryLimit = max(1, $messageRetryLimit);
        $messageRetry = 0;
        while ($messageRetry < $messageRetryLimit) {
            //prevent deadlocks
            try {
                //now get the locked row based on the RND number
                /**
                 * @var \App\Model\Entity\Message $message
                 */
                $message = $this->find('all')->where(['lock_code' => $rnd])->first();

                if ($message) {
                    $timeObjCurrent = new FrozenTime();
                    $message->started = $timeObjCurrent;
                    $this->save($message);
                    return $message;
                } else {
                    return false;
                }
            } catch (\Throwable $e) {
                $messageRetry++;
            }
        }

        return false;
    }

    /**
     * Returns a query of Messages that can be run
     *
     * @return \Cake\ORM\Query
     */
    public function buildQueryForMessagesRowLock()
    {
        $timeObjCurrent = new FrozenTime();

        $selectList = [
            "Messages.id",
        ];
        $messageQuery = $this->find('all')
            ->select($selectList)
            ->where(['Messages.lock_code IS NULL'])
            ->where(['Messages.started IS NULL'])
            ->where(['OR' => ['Messages.activation <=' => $timeObjCurrent, 'Messages.activation IS NULL']])
            ->where(['OR' => ['Messages.expiration >=' => $timeObjCurrent, 'Messages.expiration IS NULL']])
            ->orderAsc('Messages.priority')
            ->orderAsc('Messages.id')
            ->limit(1);

        return $messageQuery;
    }

    /**
     * Returns a query of Messages that can be run
     *
     * @return \Cake\ORM\Query
     */
    public function buildQueryForMessages()
    {
        $timeObjCurrent = new FrozenTime();

        $messageQuery = $this->find('all')
            ->where(['Messages.started IS NULL'])
            ->where(['OR' => ['Messages.activation <=' => $timeObjCurrent, 'Messages.activation IS NULL']])
            ->where(['OR' => ['Messages.expiration >=' => $timeObjCurrent, 'Messages.expiration IS NULL']])
            ->orderAsc('Messages.priority')
            ->orderAsc('Messages.id');

        return $messageQuery;
    }

    /**
     * Default properties for creating a Message
     *
     * @return array
     */
    public function getDefaultMessageProperties()
    {
        $session = new Session();
        $fromEmail = $session->read('Auth.User.email');
        $fromName = $session->read('Auth.User.first_name') . " " . $session->read('Auth.User.last_name');

        $activation = new FrozenTime();
        $expiration = (new FrozenTime())->addHour();
        $messageRetryLimit = Configure::read("Settings.message_retry_limit");

        $default = [
            'type' => 'email',
            'name' => null,
            'description' => null,
            'activation' => $activation,
            'expiration' => $expiration,
            'auto_delete' => 1,
            'started' => null,
            'completed' => null,
            'server' => null,
            'domain' => parse_url(Router::url("/", true), PHP_URL_HOST),
            'transport' => 'default',
            'profile' => 'default',
            'layout' => 'default',
            'template' => 'default',
            'email_format' => 'html',
            'sender' => [Configure::read('Settings.email_from_address') => Configure::read('Settings.email_from_name')],
            'email_from' => [$fromEmail => $fromName],
            'email_to' => null,
            'email_cc' => null,
            'email_bcc' => null,
            'reply_to' => null,
            'read_receipt' => null,
            'subject' => null,
            'view_vars' => null,
            'priority' => 3,
            'headers' => null,
            'smtp_code' => null,
            'smtp_message' => null,
            'lock_code' => null,
            'errors_thrown' => null,
            'errors_retry' => 0,
            'errors_retry_limit' => $messageRetryLimit,
            'beacon_hash' => sha1(Security::randomString(1024)),
            'hash_sum' => null,
        ];

        return $default;
    }

    /**
     * Simple way to create a Message for sending later.
     * Aids with the JSON type conversion as newEntity() and patchEntity() do not do JSON conversions.
     *
     * See also $this->expandEntities() to find out how you can access records by passing in IDs
     *
     * Built in trap to prevent sending of duplicate emails. Duplicate emails are based on:
     *  - email_to +  subject + view_vars
     *  - to be sent within X hours of an existing email
     *
     * @param array $dataToSave
     * @return \App\Model\Entity\Message|bool
     */
    public function createMessage($dataToSave = [])
    {
        /**
         * @var Message $ent
         * @var Message $existingMessage
         */
        $defaultData = $this->getDefaultMessageProperties();
        $dataToSave = array_merge($defaultData, $dataToSave);
        $ent = $this->newEntity();

        //use direct assignment as opposed to patchEntity()
        foreach ($dataToSave as $k => $data) {
            $ent->$k = $data;
        }

        if (empty($ent->hash_sum)) {
            $findEmailTo = $ent->email_to;
            $findSubject = $ent->subject;
            $findViewVars = json_decode(json_encode($ent->view_vars), JSON_OBJECT_AS_ARRAY);
            $ent->hash_sum = sha1(json_encode([$findEmailTo, $findSubject, $findViewVars]));
        }

        //====trap email base on email_to + subject + view_vars========================================
        $trapTimeInHours = 36;
        $activationLow = (new FrozenTime($ent->activation))->subHour($trapTimeInHours);
        $activationHigh = (new FrozenTime($ent->activation))->addHours($trapTimeInHours);

        $existingMessage = $this->find('all')
            ->select(['id'], true)
            ->where(['activation >=' => $activationLow->format("Y-m-d H:i:s"), 'activation <=' => $activationHigh->format("Y-m-d H:i:s")])
            ->where(['hash_sum' => $ent->hash_sum])
            ->where([1 => 1])
            ->first();

        if ($existingMessage) {
            //modify the record so that it appears already sent.
            $ent->lock_code = mt_rand(1, mt_getrandmax());
            $ent->started = new FrozenTime();
            $ent->completed = new FrozenTime();
            $ent->smtp_code = 99;
            $ent->smtp_message = 'Email Trapped.';
        }
        //=============================================================================================

        $return = $this->save($ent);
        return $return;
    }


    /**
     * Simple mechanism for resending a Message.
     * Reset the DB flags as not sent, hence Message Worker will pickup and resend.
     * Will roll forward Seed and Message Activation and Expiration dates.
     *
     * @param $id
     * @return bool
     */
    public function resendMessage($id)
    {

        /**
         * @var Message $message
         * @var SeedsTable $Seeds
         */
        $message = $this->find('all')->where(['id' => $id])->first();

        if (empty($message) || is_null($message)) {
            return false;
        }

        if (isset($message->view_vars)) {
            if (isset($message->view_vars['seed']['id'])) {
                $Seeds = TableRegistry::getTableLocator()->get('Seeds');
                $Seeds->rollForwardActivationExpiration($message->view_vars['seed']['id']);
                $Seeds->decreaseBid($message->view_vars['seed']['id']);
            }
        }

        $this->rollForwardActivationExpiration($message);

        $patch = [
            'started' => null,
            'completed' => null,
            'smtp_code' => null,
            'smtp_message' => null,
            'lock_code' => null,
        ];
        $message = $this->patchEntity($message, $patch);

        $result = $this->save($message);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Rolls the Activation and Expiration date forward for the given ID.
     * Only rolls forward if the both dates are in the past.
     * Keeps the same spread between the Activation and Expiration
     *
     * @param $idOrMessage
     * @return bool
     */
    public function rollForwardActivationExpiration($idOrMessage)
    {
        /**
         * @var Message $message
         */

        if (is_numeric($idOrMessage)) {
            $message = $this->find('all')->where(['id' => $idOrMessage])->first();
            if (empty($message) || is_null($message)) {
                return false;
            }
        } elseif ($idOrMessage instanceof Message) {
            $message = $idOrMessage;
        } else {
            return false;
        }

        $timeObjCurrent = new FrozenTime('now');

        $activation = $message->activation;
        $expiration = $message->expiration;

        if ($timeObjCurrent->gte($activation) && $timeObjCurrent->gte($expiration)) {
            $differenceSeconds = $timeObjCurrent->diffInSeconds($activation);
            $activation = $activation->addSeconds($differenceSeconds);
            $expiration = $expiration->addSeconds($differenceSeconds);

            $patch = [
                'activation' => $activation,
                'expiration' => $expiration,
            ];
            $message = $this->patchEntity($message, $patch);
            $result = $this->save($message);

            if ($result) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Expands out Entity IDs into fully blown Entities
     *
     * Example $entities:
     * [
     *      'user' => 1,
     *      'artifacts' => [1, 2, 3, 4],
     *      'pings' => ['table' => 'users', 'id' => 2],
     *      'pongs' => ['table' => 'users', 'id' => [3, 4, 5]],
     * ]
     *
     * As you can see from above
     *  - you can use the short form of just the IDs (int|int[])
     *  - you can use the long form where you define the table and ids
     *
     * Tables
     * - if you use the short form, we inflect the table name into its plural form (e.g. 'user' to 'users' above)
     * - if you use the long form, the table name is NOT inflected into it plural form. May lead to errors.
     *
     * The function will always deliver back Entities based in how you passed in the IDs
     *  - int 1 will deliver back $query->first()
     *  - array [1] will deliver back $query->toArray()
     *  - array [1,2,3,4] will deliver back $query->toArray()
     *
     * @param array $entities
     * @param bool $hydrate
     * @return array
     */
    public function expandEntities(array $entities, $hydrate = true)
    {
        $expandedEntities = [];
        foreach ($entities as $name => $inputsToExpand) {

            $queryFirst = false;

            if (isset($inputsToExpand['table'])) {
                $table = $inputsToExpand['table'];
                unset($inputsToExpand['table']);
            } else {
                $table = Inflector::pluralize($name);
            }

            $ids = [0];
            //extract from long form array
            if (isset($inputsToExpand['id'])) {
                $ids = $inputsToExpand['id'];
                unset($inputsToExpand['id']);
                if (is_int($ids)) {
                    $ids = [$ids];
                    $queryFirst = true;
                }
            }

            //extract from short form integer
            if (is_int($inputsToExpand)) {
                $ids = [$inputsToExpand];
                $queryFirst = true;
            }

            //extract from short form integer[]
            if (is_array($inputsToExpand)) {
                $inputsToExpand = array_values($inputsToExpand);
                if (isSeqArr($inputsToExpand)) {
                    $ids = $inputsToExpand;
                }
            }

            $Table = TableRegistry::getTableLocator()->get($table);
            $query = $Table->find('all')->where(['id IN' => $ids]);

            if ($hydrate) {
                $query = $query->enableHydration();
            } else {
                $query = $query->disableHydration();
            }

            if ($queryFirst) {
                $expandedEntities[$name] = $query->first();
            } else {
                $expandedEntities[$name] = $query->toArray();
            }

        }

        return $expandedEntities;
    }

}
