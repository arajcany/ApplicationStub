<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TrackUploads Model
 *
 * @method \App\Model\Entity\TrackUpload get($primaryKey, $options = [])
 * @method \App\Model\Entity\TrackUpload newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TrackUpload[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TrackUpload|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TrackUpload saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TrackUpload patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TrackUpload[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TrackUpload findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TrackUploadsTable extends AppTable
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

        $this->setTable('track_uploads');
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->allowEmptyString('type');

        $validator
            ->scalar('tmp_name')
            ->maxLength('tmp_name', 255)
            ->allowEmptyString('tmp_name');

        $validator
            ->scalar('size')
            ->maxLength('size', 255)
            ->allowEmptyString('size');

        $validator
            ->scalar('error')
            ->maxLength('error', 255)
            ->allowEmptyString('error');

        $validator
            ->scalar('finfo_mime_type')
            ->maxLength('finfo_mime_type', 255)
            ->allowEmptyString('finfo_mime_type');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->allowEmptyString('username');

        $validator
            ->scalar('rnd_hash')
            ->maxLength('rnd_hash', 50)
            ->allowEmptyString('rnd_hash');

        $validator
            ->integer('batch_reference')
            ->allowEmptyString('batch_reference');

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
        //$rules->add($rules->isUnique(['username']));

        return $rules;
    }
}
