<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsToMany $Users
 *
 * @method \App\Model\Entity\Role get($primaryKey, $options = [])
 * @method \App\Model\Entity\Role newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Role|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Role[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Role findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query findById(int $id)
 * @method \Cake\ORM\Query findByName(string $name)
 * @method \Cake\ORM\Query findByAlias(string $alias)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
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

        $this->setTable('roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Users', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'roles_users'
        ]);
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
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notBlank('name');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notBlank('description');

        $validator
            ->scalar('alias')
            ->requirePresence('alias', 'create')
            ->notBlank('alias');

        return $validator;
    }

    /**
     * Return a list of IDs that use the given name or alias
     *
     * @param string $nameOrAlias
     * @return array
     */
    public function getRoleIdListByNameOrAlias($nameOrAlias = '')
    {
        $results = $this->findByNameOrAlias($nameOrAlias)
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        return $results;
    }

    /**
     * Return a list of IDs that use the given name or alias
     *
     * @param string|array $nameOrAlias
     * @return \Cake\ORM\Query
     */
    public function findByNameOrAlias($nameOrAlias = '')
    {
        if (is_string($nameOrAlias)) {
            $nameOrAlias = [$nameOrAlias];
        }

        $query = $this->find('all')
            ->where(['OR' => ['name IN' => $nameOrAlias, 'alias IN' => $nameOrAlias]]);

        return $query;
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByIdAndName($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'id', 'valueField' => 'name']);

        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByIdAndName";
            $query = $query->cache($cacheName, 'query_results_general');
        }

        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByIdAndAlias($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'id', 'valueField' => 'alias']);

        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByIdAndAlias";
            $query = $query->cache($cacheName, 'query_results_general');
        }

        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByAliasAndName($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'alias', 'valueField' => 'name']);

        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByAliasAndName";
            $query = $query->cache($cacheName, 'query_results_general');
        }

        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByNameAndAlias($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'name', 'valueField' => 'alias']);

        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByNameAndAlias";
            $query = $query->cache($cacheName, 'query_results_general');
        }

        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByIdAndId($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'id', 'valueField' => 'id']);

        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByIdAndId";
            $query = $query->cache($cacheName, 'query_results_general');
        }

        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByNameAndTimeout($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'name', 'valueField' => 'session_timeout']);
        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByNameAndTimeout";
            $query = $query->cache($cacheName, 'query_results_general');
        }
        return $query->toArray();
    }

    /**
     * Return a list
     *
     * @param bool $useCache
     * @return array
     */
    public function listByAliasAndTimeout($useCache = true)
    {
        $query = $this->find('list', ['keyField' => 'alias', 'valueField' => 'session_timeout']);
        //cache the query
        if ($useCache) {
            $cacheName = "roles-listByAliasAndTimeout";
            $query = $query->cache($cacheName, 'query_results_general');
        }
        return $query->toArray();
    }
}
