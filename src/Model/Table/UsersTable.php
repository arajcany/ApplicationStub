<?php

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\I18n\FrozenTime;
use Cake\Network\Session;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\UserStatusesTable|\Cake\ORM\Association\BelongsTo $UserStatuses
 * @property \App\Model\Table\UserLocalizationsTable|\Cake\ORM\Association\HasMany $UserLocalizations
 * @property \App\Model\Table\RolesTable|\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query findById(int $id) get a User by ID
 * @method \Cake\ORM\Query findByName(string $name)
 * @method \Cake\ORM\Query findByEmail(string $email)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    private $authError = [];
    private $Settings;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('UserStatuses', [
            'foreignKey' => 'user_statuses_id'
        ]);
        $this->hasOne('UserLocalizations', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'roles_users'
        ]);

        $this->Settings = TableRegistry::getTableLocator()->get('Settings');

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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notBlank('email');

        $validator
            ->scalar('username')
            ->requirePresence('username', 'create')
            ->notBlank('username');

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create')
            ->notBlank('password');

        //check if strong passwords are required
        $password_strong_bool = $this->Settings->getSetting('password_strong_bool');
        if ($password_strong_bool == true) {
            $this->validationStrongPassword($validator);
        }

        $validator
            ->allowEmptyString('password_2', null, 'create')
            ->allowEmptyString('password_2', null, 'update')
            ->add('password_2', 'compareWith', [
                'rule' => ['compareWith', 'password'],
                'message' => 'Passwords do not match.'
            ]);

        $validator
            ->scalar('first_name')
            ->requirePresence('first_name', 'create');

        $validator
            ->scalar('last_name')
            ->requirePresence('last_name', 'create');

        $validator
            ->scalar('address_1')
            ->allowEmptyString('address_1');

        $validator
            ->scalar('address_2')
            ->allowEmptyString('address_2');

        $validator
            ->scalar('suburb')
            ->allowEmptyString('suburb');

        $validator
            ->scalar('state')
            ->allowEmptyString('state');

        $validator
            ->scalar('post_code')
            ->allowEmptyString('post_code');

        $validator
            ->scalar('country')
            ->allowEmptyString('country');

        $validator
            ->scalar('mobile')
            ->allowEmptyString('mobile');

        $validator
            ->scalar('phone')
            ->allowEmptyString('phone');

        $validator
            ->dateTime('activation')
            ->allowEmptyDateTime('activation');

        $validator
            ->dateTime('expiration')
            ->allowEmptyDateTime('expiration');

        $validator
            ->boolean('is_confirmed')
            ->allowEmptyString('is_confirmed');

        $validator
            ->dateTime('password_expiry')
            ->allowEmptyDateTime('password_expiry');

        return $validator;
    }


    /**
     * Strong password validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationStrongPassword(Validator $validator)
    {

        $password_strong_length = $this->Settings->getSetting('password_strong_length');
        $password_strong_lower = $this->Settings->getSetting('password_strong_lower');
        $password_strong_upper = $this->Settings->getSetting('password_strong_upper');
        $password_strong_number = $this->Settings->getSetting('password_strong_number');
        $password_strong_special = $this->Settings->getSetting('password_strong_special');

        $validator
            ->add('password', [
                'length' => [
                    'rule' => ['minLength', $password_strong_length],
                    'message' => __('The password must be at least {0} characters long.', $password_strong_length)
                ]
            ]);

        if ($password_strong_lower == true) {
            $validator
                ->add('password', 'containsLower', [
                    'rule' => [$this, 'passwordStrongLower'],
                    'message' => 'The password must contain a lowercase character'
                ]);
        }

        if ($password_strong_upper == true) {
            $validator
                ->add('password', 'containsUpper', [
                    'rule' => [$this, 'passwordStrongUpper'],
                    'message' => 'The password must contain an uppercase character'
                ]);
        }

        if ($password_strong_number == true) {
            $validator
                ->add('password', 'containsNumber', [
                    'rule' => [$this, 'passwordStrongNumber'],
                    'message' => 'The password must contain a number'
                ]);
        }

        if ($password_strong_special == true) {
            $validator
                ->add('password', 'containsSpecial', [
                    'rule' => [$this, 'passwordStrongSpecial'],
                    'message' => 'The password must contain a special character'
                ]);
        }


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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['user_statuses_id'], 'UserStatuses'));

        return $rules;
    }

    /**
     * Custom finder for the Auth component.
     * Roles table needs to be joined for the TinyAuth plugin
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return Query
     */
    public function findAuth(Query $query, array $options)
    {
        $query = $query
            ->contain('Roles')
            ->contain('UserStatuses');

        return $query;
    }

    /**
     * Checks for a strong password lowercase character:
     *
     * @param string $password
     * @param array $context
     * @return boolean
     */
    public function passwordStrongLower($password, array $context)
    {
        $return = true;
        // lowercase
        if (!preg_match("#[a-z]#", $password)) {
            $return = false;
        }
        return $return;
    }

    /**
     * Checks for a strong password uppercase character:
     *
     * @param string $password
     * @param array $context
     * @return boolean
     */
    public function passwordStrongUpper($password, array $context)
    {
        $return = true;
        // uppercase
        if (!preg_match("#[A-Z]#", $password)) {
            $return = false;
        }
        return $return;
    }

    /**
     * Checks for a strong password number character:
     *
     * @param string $password
     * @param array $context
     * @return boolean
     */
    public function passwordStrongNumber($password, array $context)
    {
        $return = true;
        // number
        if (!preg_match("#[0-9]#", $password)) {
            $return = false;
        }
        return $return;
    }

    /**
     * Checks for a strong password special character:
     *
     * @param string $password
     * @param array $context
     * @return boolean
     */
    public function passwordStrongSpecial($password, array $context)
    {
        $return = true;
        // special characters
        if (!preg_match("#\W+#", $password)) {
            $return = false;
        }
        return $return;
    }


    /**
     * Check if the password has expired
     *
     * @param $userDetails
     * @return bool
     */
    public function isPasswordExpired($userDetails)
    {
        if (is_null($userDetails['password_expiry'])) {
            return true;
        }

        $frozenTimeObj = new FrozenTime('now');
        $passwordExpiry = $userDetails['password_expiry'];

        if ($frozenTimeObj->gte($passwordExpiry) === true) {
            return true;
        }

        return false;
    }

    /**
     * Check if the account status is in order
     *
     * @param array $userDetails
     * @return bool
     */
    public function validateAccountStatus($userDetails)
    {
        /**
         * @var \Cake\I18n\FrozenTime $activation
         * @var \Cake\I18n\FrozenTime $expiration
         */

        $return = true;

        //Check if account is 'Active'
        $user_statuses_id = $userDetails['user_statuses_id'];
        $activeStatusIds = $this->UserStatuses->getActiveStatusIds();
        if (!in_array($user_statuses_id, $activeStatusIds)) {
            if ($userDetails['user_status']['alias'] == 'banned') {
                $this->setAuthError('Sorry, your account has been banned.');
            } elseif ($userDetails['user_status']['alias'] == 'disabled') {
                $this->setAuthError('Sorry, your account has been disabled.');
            } elseif ($userDetails['user_status']['alias'] == 'pending') {
                $this->setAuthError('Sorry, your account is pending approval by an Administrator.');
            } elseif ($userDetails['user_status']['alias'] == 'rejected') {
                $this->setAuthError('Sorry, your account has been rejected by an Administrator.');
            } else {
                $this->setAuthError('Sorry, your account has been suspended');
            }
            $return = false;

            //return immediately as there is no point in moving forward
            return $return;
        }

        //Check if email address has been confirmed
        $is_confirmed = $userDetails['is_confirmed'];
        if ($is_confirmed != true) {
            $this->setAuthError('Sorry, your email address has not been confirmed. Please check your inbox for a confirmation link.');
            $return = false;
        }

        $frozenTimeObj = new FrozenTime('now');

        //Check if account has expiry limits
        $activation = $userDetails['activation'];
        $expiration = $userDetails['expiration'];
        $activationReadable = (!is_null($activation) ? $activation->i18nFormat("EEEE, MMMM d, yyyy @ h:mm a", TZ) : '');
        $expirationReadable = (!is_null($expiration) ? $expiration->i18nFormat("EEEE, MMMM d, yyyy @ h:mm a", TZ) : '');

        //activation and expiration set so check in between
        if ($activation && $expiration) {
            if ($frozenTimeObj->gte($activation) === false || $frozenTimeObj->lte($expiration) === false) {
                if ($frozenTimeObj->gte($activation) === false) {
                    $this->setAuthError(
                        __('Sorry, your account will activate on {0}',
                            $activationReadable)
                    );
                    $return = false;
                }

                if ($frozenTimeObj->lte($expiration) === false) {
                    $this->setAuthError(
                        __('Sorry, your account expired on {0}',
                            $expirationReadable)
                    );
                    $return = false;
                }
            }
        }

        //activation set so check if greater
        if ($activation && is_null($expiration)) {
            if ($frozenTimeObj->gte($activation) === false) {
                $this->setAuthError(
                    __('Sorry, your account will activate on {0}',
                        $activationReadable)
                );
                $return = false;
            }
        }

        //expiration set so check if less
        if (is_null($activation) && $expiration) {
            if ($frozenTimeObj->lte($expiration) === false) {
                $this->setAuthError(
                    __('Sorry, your account expired on {0}',
                        $expirationReadable)
                );
                $return = false;
            }
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function getAuthError()
    {
        return $this->authError;
    }

    /**
     * @param mixed $authError
     */
    public function setAuthError($authError)
    {
        if (!is_array($authError)) {
            $authError = [$authError];
        }

        if (is_array($authError)) {
            $this->authError = array_merge($this->authError, $authError);
        }
    }

    public function getDefaultUserProperties()
    {
        $default = [
            'email' => '',
            'username' => '',
            'password' => sha1(mt_rand()) . "abcABC123!@#$%^&*_",
            'first_name' => '',
            'last_name' => '',
            'address_1' => '',
            'address_2' => '',
            'suburb' => '',
            'state' => '',
            'post_code' => '',
            'country' => '',
            'mobile' => '',
            'phone' => '',
            'activation' => new FrozenTime(),
            'expiration' => $this->Settings->getAccountExpirationDate(),
            'is_confirmed' => '0',
            'user_statuses_id' => $this->UserStatuses->getStatusIdByNameOrAlias('disabled'),
            'roles' => [
                '_ids' => $this->Roles->getRoleIdListByNameOrAlias('user')
            ],
            'password_expiry' => $this->Settings->getPasswordExpiryDate(),
        ];

        return $default;
    }

    public function getUsersMap($case = '')
    {
        $users = $this->find('list')->toArray();

        if ($case == 'lower') {
            foreach ($users as $id => $user) {
                $users[$id] = strtolower($user);
            }
        } elseif ($case == 'upper') {
            foreach ($users as $id => $user) {
                $users[$id] = strtoupper($user);
            }
        }

        $userMap = array_flip($users);
        return $userMap;
    }

    public function idToName($id = null)
    {
        if (is_null($id) || empty($id)) {
            return false;
        }

        $users = $this->findById($id)->toArray();

        if ($users) {
            return $users[0]->full_name;
        } else {
            return '';
        }
    }

    public function nameToId($name = null)
    {
        if (is_null($name) || empty($name)) {
            return false;
        }

        $nameParts = explode(' ', $name);

        $users = false;
        if (count($nameParts) == 1) {
            $users = $this->find('all');
            $users = $users->orderAsc('id');
            $users = $users->orWhere(['first_name LIKE' => '%' . $nameParts[0] . '%',]);
            $users = $users->orWhere(['last_name LIKE' => '%' . $nameParts[0] . '%',]);
            $users = $users->toArray();
        } elseif (count($nameParts) == 2) {
            $users = $this->find('all');
            $users = $users->orderAsc('id');
            $users = $users->where(['first_name LIKE' => '%' . $nameParts[0] . '%',]);
            $users = $users->where(['last_name LIKE' => '%' . $nameParts[1] . '%',]);
            $users = $users->toArray();
        } else {
            $users = $this->find('all');
            $users = $users->orderAsc('id');
            $users = $users->where(["first_name + ' ' + last_name LIKE '%{$name}%' "]);
            $users = $users->toArray();
        }

        if ($users) {
            return $users[0]->id;
        } else {
            return false;
        }
    }

    /**
     * Find Users by their Role
     *
     * @param string|array $roleNameOrAlias
     * @return Query
     */
    public function findUsersByRoleNameOrRoleAlias($roleNameOrAlias = '')
    {
        if (!is_array($roleNameOrAlias)) {
            $roleNameOrAlias = [$roleNameOrAlias];
        }

        $query = $this->find('all')
            ->matching('Roles', function ($q) use (&$roleNameOrAlias) {
                return $q->where(['OR' => ['Roles.name IN' => $roleNameOrAlias, 'Roles.alias IN' => $roleNameOrAlias]]);
            });

        return $query;
    }

    /**
     * Wrapper function
     *
     * @param string $roleNameOrAlias
     * @return array
     */
    public function listUsersByRoleNameOrRoleAlias($roleNameOrAlias = '')
    {
        return $this->findUsersByRoleNameOrRoleAlias($roleNameOrAlias)
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();
    }

    /**
     * Generic identity for logging Shell Tasks against
     *
     * @return \App\Model\Entity\User
     */
    public function getGeneralWorkerUser()
    {
        $email = 'general_worker@localhost';
        $worker = $this->find('all')
            ->where(['email' => $email])
            ->first();

        if (!$worker) {
            $worker = $this->newEntity();
            $worker->username = $email;
            $worker->email = $email;
            $worker->first_name = "General";
            $worker->last_name = "Worker";
            $worker->password = sha1(mt_rand());
            $worker->user_statuses_id = $this->UserStatuses->getStatusIdByNameOrAlias('disabled');

            $this->save($worker);
        }

        return $worker;
    }

    /**
     * Generic identity for logging Shell Tasks against
     *
     * @return \App\Model\Entity\User
     */
    public function getMailWorkerUser()
    {
        $email = 'mail_worker@localhost';
        $worker = $this->find('all')
            ->where(['email' => $email])
            ->first();

        if (!$worker) {
            $worker = $this->newEntity();
            $worker->username = $email;
            $worker->email = $email;
            $worker->first_name = "Mail";
            $worker->last_name = "Worker";
            $worker->password = sha1(mt_rand());
            $worker->user_statuses_id = $this->UserStatuses->getStatusIdByNameOrAlias('disabled');

            $this->save($worker);
        }

        return $worker;
    }

    public function getUserSessionDataForCache($id = null)
    {
        if ($id === null || $id === false) {
            $data = (new Session())->read("Auth.User");
        } elseif (is_numeric($id)) {
            $exists = $this->exists(['id' => $id]);
            if ($exists) {
                $data = $this->find('all')
                    ->where(['id' => $id])
                    ->contain(['Roles'])
                    ->first()
                    ->toArray();
            } else {
                $data = [
                    'id' => 0,
                    'roles' => []
                ];
            }
        } else {
            $data = [
                'id' => 0,
                'roles' => []
            ];
        }

        if ($data == null) {
            $data = [
                'id' => 0,
                'roles' => []
            ];
        }

        $cacheConfigName = "query_results_my_{$data['id']}";
        $data['cacheConfigName'] = $cacheConfigName;

        if (Cache::getConfig($cacheConfigName) == null) {
            Cache::setConfig($cacheConfigName, [
                'className' => 'File',
                'prefix' => 'mine_',
                'path' => CACHE . 'queries/my/' . $data['id'],
                'duration' => '+1 minute',
                'url' => env('CACHE_DEFAULT_URL', null),
            ]);
        }

        return $data;
    }
}
