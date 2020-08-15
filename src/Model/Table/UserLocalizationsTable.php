<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserLocalizations Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserLocalization get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserLocalization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserLocalization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserLocalization|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserLocalization saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserLocalization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserLocalization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserLocalization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserLocalizationsTable extends Table
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

        $this->setTable('user_localizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('location')
            ->maxLength('location', 50)
            ->allowEmptyString('location');

        $validator
            ->scalar('locale')
            ->maxLength('locale', 10)
            ->allowEmptyString('locale');

        $validator
            ->scalar('timezone')
            ->maxLength('timezone', 50)
            ->allowEmptyString('timezone');

        $validator
            ->scalar('time_format')
            ->maxLength('time_format', 50)
            ->allowEmptyString('time_format');

        $validator
            ->scalar('date_format')
            ->maxLength('date_format', 50)
            ->allowEmptyString('date_format');

        $validator
            ->scalar('datetime_format')
            ->maxLength('datetime_format', 50)
            ->allowEmptyString('datetime_format');

        $validator
            ->scalar('week_start')
            ->maxLength('week_start', 50)
            ->allowEmptyString('week_start');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
