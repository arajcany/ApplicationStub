<?php

namespace App\Model\Table;

use arajcany\ToolBox\Utility\Security\Security;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use mersenne_twister\twister;
use phpseclib\Net\SFTP;

/**
 * InternalOptions Model
 *
 * @method \App\Model\Entity\InternalOption get($primaryKey, $options = [])
 * @method \App\Model\Entity\InternalOption newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\InternalOption[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\InternalOption|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InternalOption patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\InternalOption[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\InternalOption findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query findById(int $id)
 * @method \Cake\ORM\Query findByName(string $name)
 * @method \Cake\ORM\Query findByOptionKey(string $optionKey)
 * @method \Cake\ORM\Query findByApplyMask(bool $applyMask)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class InternalOptionsTable extends Table
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

        $this->setTable('internal_options');
        $this->setDisplayField('id');
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
            ->scalar('option_key')
            ->requirePresence('option_key', 'create')
            ->notBlank('option_key')
            ->add('option_key', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('option_value')
            ->requirePresence('option_value', 'create')
            ->notBlank('option_value');

        $validator
            ->boolean('is_masked')
            ->allowEmptyString('is_masked');

        $validator
            ->boolean('apply_mask')
            ->allowEmptyString('apply_mask');

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
        $rules->add($rules->isUnique(['option_key']));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'internal';
    }

    /**
     * Create the internal_options table in the internal.db
     * Commonly called when this is a brand new install
     */
    public function buildInternalOptionsTable()
    {
        $hk = mt_rand(111111111, 999999999);
        $hs = mt_rand(111111111, 999999999);

        $sqlStaments[] = "create table internal_options
(
	id INTEGER not null primary key autoincrement,
	created DATETIME,
	modified DATETIME,
	option_key TEXT not null,
	option_value TEXT not null,
	is_masked INT,
	apply_mask INT
)";

        $sqlStaments[] = "create unique index options_key_uindex
	on internal_options (option_key)";

        $connection = $this->getConnection();
        foreach ($sqlStaments as $sqlStatement) {
            $results = $connection->execute($sqlStatement)->fetchAll('assoc');
        }

        $vals = [
            ['option_key' => 'hk', 'option_value' => $hk, 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'hs', 'option_value' => $hs, 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'company_name', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'street', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'suburb', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'state', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'postcode', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'phone', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'web', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
            ['option_key' => 'email', 'option_value' => '-', 'is_masked' => 0, 'apply_mask' => 0,],
        ];

        foreach ($vals as $val) {
            $ent = $this->newEntity($val);
            $this->save($ent);
        }

        Cache::write('first_run', true, 'quick_burn');

        $this->saveOptionsToConfigure();
        $this->encryptOptions();
    }

    /**
     * Push all the Options into Configure.
     * Uses Cache to speed up DB read.
     *
     * @param bool $readFromCache
     * @return void
     */
    public function saveOptionsToConfigure($readFromCache = true)
    {
        $options = false;

        //read from Cache to speed up expensive DB read
        if ($readFromCache) {
            $options = Cache::read('internal_options', 'query_results_app');
        }

        //if Cache has expired read from DB and push back to Cache for next time
        if (!$options) {
            $options = $this->find('all')
                ->select(['id', 'option_key', 'option_value'])
                ->orderAsc('id')
                ->toArray();
            Cache::write('internal_options', $options, 'query_results_app');
        }

        $optionsList = [];

        /**
         * @var \App\Model\Entity\InternalOption $option
         */
        foreach ($options as $option) {
            $optionsList[$option->option_key] = $option->option_value;
        }

        $optionsList['key'] = $this->getKey();
        $optionsList['salt'] = $this->getSalt();

        Configure::write('InternalOptions', $optionsList);
    }

    /**
     * Clear the Cache of the Settings query_results_app
     */
    public function clearCache()
    {
        Cache::delete('option_key', 'query_results_app');
    }

    /**
     * Convenience method
     *
     * @return string
     */
    public function getKey()
    {
        $twst = new twister();
        $int = $this->getOption('hk');
        $sec = '';
        $counter = range(0, 10);
        foreach ($counter as $k => $v) {
            $twst->init_with_integer($int);
            $int = $twst->int31();
            $sec .= $int;
        }

        return $sec;
    }


    /**
     * Convenience method
     *
     * @return string
     */
    public function getSalt()
    {
        $twst = new twister();
        $int = $this->getOption('hs');
        $sec = '';
        $counter = range(0, 10);
        foreach ($counter as $k => $v) {
            $twst->init_with_integer($int);
            $int = $twst->int31();
            $sec .= $int;
        }

        return $sec;
    }


    /**
     * Convenience Method to get a value based on the passed in key
     *
     * @param $optionKey
     * @param $decrypt
     * @return bool|string
     */
    public function getOption($optionKey, $decrypt = false)
    {
        //try to get the value from Configure first
        $configValue = Configure::read("InternalOptions.{$optionKey}");
        if ($configValue !== null) {
            if ($decrypt == true) {
                $configValue = Security::decrypt64($configValue);
            }
            return $configValue;
        }

        //fallback to reading from DB
        $value = $this->findByOptionKey($optionKey)->toArray();

        if (isset($value[0]->option_value)) {
            $value = $value[0]->option_value;
            if ($decrypt == true) {
                $value = Security::decrypt64($value);
            }
        } else {
            $value = false;
        }

        return $value;
    }

    /**
     * Convenience Method to set a value based on the passed in key
     * Can only be used to update.
     *
     * @param $optionKey
     * @param $optionValue
     * @return bool
     */
    public function setOption($optionKey, $optionValue)
    {
        $value = $this->findByOptionKey($optionKey)->toArray();

        //update
        if (isset($value[0]->id)) {
            $ent = $value[0];
            $ent->property_value = $optionValue;
            $result = $this->save($ent);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Encrypt Options where 'apply_mask' == true
     *
     * @return \App\Model\Entity\InternalOption|bool
     */
    public function encryptOptions()
    {
        /**
         * @var \App\Model\Entity\InternalOption $ent
         */

        $rows = $this->findByApplyMask(1)->toArray();

        $result = true;
        foreach ($rows as $ent) {
            $ent->option_value = Security::encrypt64($ent->option_value);
            $ent->is_masked = 1;
            $ent->apply_mask = 0;
            $resultOfSave = $this->save($ent);

            if (!$resultOfSave) {
                $result = false;
            }
        }

        return $result;
    }


    /**
     * Convenience Method to get all the AuthorText details
     *
     * @return array
     */
    public function getAuthorText()
    {
        $fields = [
            'company_name',
            'street',
            'suburb',
            'state',
            'postcode',
            'phone',
            'web',
            'email',
        ];

        $fieldsPopulated = [];
        foreach ($fields as $field) {
            $fieldsPopulated[$field] = Configure::read("InternalOptions.{$field}");
        }

        return $fieldsPopulated;
    }

}
