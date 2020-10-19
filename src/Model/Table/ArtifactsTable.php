<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Artifacts Model
 *
 * @property \App\Model\Table\ArtifactMetadataTable&\Cake\ORM\Association\HasMany $ArtifactMetadata
 *
 * @method \App\Model\Entity\Artifact get($primaryKey, $options = [])
 * @method \App\Model\Entity\Artifact newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Artifact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Artifact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Artifact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Artifact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Artifact[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Artifact findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArtifactsTable extends Table
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

        $this->setTable('artifacts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('ArtifactMetadata', [
            'foreignKey' => 'artifact_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 1024)
            ->allowEmptyString('description');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        $validator
            ->scalar('mime_type')
            ->maxLength('mime_type', 50)
            ->allowEmptyString('mime_type');

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
            ->scalar('token')
            ->maxLength('token', 50)
            ->allowEmptyString('token');

        $validator
            ->scalar('url')
            ->maxLength('url', 2048)
            ->allowEmptyString('url');

        $validator
            ->scalar('unc')
            ->maxLength('unc', 2048)
            ->allowEmptyString('unc');

        return $validator;
    }
}
