<?php

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Settings Model
 *
 * @method \App\Model\Entity\Setting get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Setting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setting findOrCreate($search, callable $callback = null, $options = [])
 *
 * @method \Cake\ORM\Query findByPropertyKey(string $propertyKey) Find based on property_key
 * @method \Cake\ORM\Query findByPropertyGroup(string $propertyGroup) Find based on property_group
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SettingsTable extends Table
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

        $this->setTable('settings');
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
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false);

        $validator
            ->scalar('description')
            ->maxLength('description', 1024)
            ->allowEmptyString('description');

        $validator
            ->scalar('property_group')
            ->maxLength('property_group', 50)
            ->requirePresence('property_group', 'create')
            ->allowEmptyString('property_group', false);

        $validator
            ->scalar('property_key')
            ->maxLength('property_key', 50)
            ->requirePresence('property_key', 'create')
            ->allowEmptyString('property_key', false);

        $validator
            ->scalar('property_value')
            ->maxLength('property_value', 1024)
            ->allowEmptyString('property_value');

        $validator
            ->scalar('selections')
            ->allowEmptyString('selections');

        $validator
            ->scalar('html_select_type')
            ->maxLength('html_select_type', 50)
            ->allowEmptyString('html_select_type');

        $validator
            ->scalar('match_pattern')
            ->maxLength('match_pattern', 50)
            ->allowEmptyString('match_pattern');

        $validator
            ->boolean('is_masked')
            ->allowEmptyString('is_masked');

        return $validator;
    }

    /**
     * Push all the Settings into Configure.
     * Uses Cache to speed up DB read.
     *
     * @param bool $readFromCache
     * @return void
     */
    public function saveSettingsToConfigure($readFromCache = true)
    {
        $settings = false;

        //read from Cache to speed up expensive DB read
        if ($readFromCache) {
            $settings = Cache::read('settings', 'query_results_app');
        }

        //if Cache has expired read from DB and push back to Cache for next time
        if (!$settings) {
            $settings = $this->find('all')
                ->select(['id', 'property_key', 'property_value', 'property_group'])
                ->orderAsc('property_group')
                ->orderAsc('id')
                ->toArray();
            Cache::write('settings', $settings, 'query_results_app');
        }

        $settingsList = [];
        $settingsGrouped = [];

        /**
         * @var \App\Model\Entity\Setting $setting
         */
        foreach ($settings as $setting) {
            $settingsList[$setting->property_key] = $setting->property_value;
            $settingsGrouped[$setting->property_group][$setting->property_key] = $setting->property_value;
        }

        Configure::write('Settings', $settingsList);
        Configure::write('SettingsGrouped', $settingsGrouped);
    }

    /**
     * Clear the Cache of the Settings query_results_app
     */
    public function clearCache()
    {
        Cache::delete('settings', 'query_results_app');
    }


    /**
     * Convenience Method to get a value based on the passed in key
     *
     * @param $settingKey
     * @return bool
     */
    public function getSetting($settingKey)
    {
        //try to get the value from Configure first
        $configValue = Configure::read("Settings.{$settingKey}");
        if ($configValue !== null) {
            return $configValue;
        }

        //fallback to reading from DB
        $value = $this->findByPropertyKey($settingKey)->toArray();

        if (isset($value[0]->property_value)) {
            $value = $value[0]->property_value;
        } else {
            $value = false;
        }

        if ($value === 'false') {
            $value = false;
        }

        if ($value === 'true') {
            $value = true;
        }

        if ($value === 'null') {
            $value = null;
        }

        return $value;
    }

    /**
     * Convenience Method to set a value based on the passed in key
     * Can only be used to update.
     *
     * @param $settingKey
     * @param $settingValue
     * @return bool
     */
    public function setSetting($settingKey, $settingValue)
    {
        $value = $this->findByPropertyKey($settingKey)->toArray();

        //update
        if (isset($value[0]->id)) {
            $ent = $value[0];
            $ent->property_value = $settingValue;
            $result = $this->save($ent);

            //update the Configure values
            $this->saveSettingsToConfigure(false);

            return $result;
        } else {
            return false;
        }
    }

}
