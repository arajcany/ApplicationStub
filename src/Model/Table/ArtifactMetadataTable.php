<?php

namespace App\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ArtifactMetadata Model
 *
 * @property \App\Model\Table\ArtifactsTable&\Cake\ORM\Association\BelongsTo $Artifacts
 *
 * @method \App\Model\Entity\ArtifactMetadata get($primaryKey, $options = [])
 * @method \App\Model\Entity\ArtifactMetadata newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ArtifactMetadata[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ArtifactMetadata|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArtifactMetadata saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArtifactMetadata patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ArtifactMetadata[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ArtifactMetadata findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArtifactMetadataTable extends AppTable
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

        $this->setTable('artifact_metadata');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Artifacts', [
            'foreignKey' => 'artifact_id',
        ]);
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
            ->integer('width')
            ->allowEmptyString('width');

        $validator
            ->integer('height')
            ->allowEmptyString('height');

        $validator
            ->scalar('exif')
            ->allowEmptyString('exif');

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
        $rules->add($rules->existsIn(['artifact_id'], 'Artifacts'));

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
            'exif',
        ];

        return $jsonFields;
    }
}
