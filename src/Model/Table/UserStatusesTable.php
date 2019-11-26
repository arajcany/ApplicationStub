<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserStatuses Model
 *
 * @method \App\Model\Entity\UserStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserStatus newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query findById(int $id)
 * @method \Cake\ORM\Query findByName(string $name)
 * @method \Cake\ORM\Query findByAlias(string $alias)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserStatusesTable extends Table
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

        $this->setTable('user_statuses');
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
            ->allowEmpty('id', 'create');

        $validator
            ->integer('rank')
            ->allowEmpty('rank');

        $validator
            ->scalar('name')
            ->allowEmpty('name');

        $validator
            ->scalar('description')
            ->allowEmpty('description');

        $validator
            ->scalar('alias')
            ->allowEmpty('alias');

        $validator
            ->scalar('name_status_icon')
            ->allowEmpty('name_status_icon');

        return $validator;
    }


    public function getActiveStatusIds()
    {
        $results = $this->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->where(['alias IN' => ['active', 'approved']])
            ->toArray();

        return $results;
    }

    public function getStatusIdByNameOrAlias($nameOrAlias = '')
    {
        $results = $this->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->where(['OR' => ['name' => $nameOrAlias, 'alias' => $nameOrAlias]])
            ->first();

        return $results;
    }


}
