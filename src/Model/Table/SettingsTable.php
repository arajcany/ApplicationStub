<?php

namespace App\Model\Table;

use App\Model\Entity\Setting;
use arajcany\ToolBox\Utility\Security\Security;
use arajcany\ToolBox\Utility\TextFormatter;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
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
class SettingsTable extends AppTable
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', null, false);

        $validator
            ->scalar('description')
            ->maxLength('description', 1024)
            ->allowEmptyString('description');

        $validator
            ->scalar('property_group')
            ->maxLength('property_group', 50)
            ->requirePresence('property_group', 'create')
            ->allowEmptyString('property_group', null, false);

        $validator
            ->scalar('property_key')
            ->maxLength('property_key', 50)
            ->requirePresence('property_key', 'create')
            ->allowEmptyString('property_key', null, false);

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
                ->select(['id', 'property_key', 'property_value', 'property_group', 'is_masked'])
                ->orderAsc('property_group')
                ->orderAsc('id')
                ->toArray();
            try {
                Cache::write('settings', $settings, 'query_results_app');
            } catch (\Throwable $e) {
                // Do not halt - not critical
            }
        }

        $settingsList = [];
        $settingsGrouped = [];
        $settingsEncrypted = [];

        /**
         * @var \App\Model\Entity\Setting $setting
         */
        foreach ($settings as $setting) {
            $value = $setting->property_value;

            if ($value === 'false') {
                $value = false;
            }

            if ($value === 'true') {
                $value = true;
            }

            if ($value === 'null') {
                $value = null;
            }

            $settingsList[$setting->property_key] = $value;
            $settingsGrouped[$setting->property_group][$setting->property_key] = $value;
            if ($setting->is_masked == true) {
                $settingsEncrypted[$setting->property_key] = true;
            } else {
                $settingsEncrypted[$setting->property_key] = false;
            }
        }

        Configure::write('Settings', $settingsList);
        Configure::write('SettingsGrouped', $settingsGrouped);
        Configure::write('SettingsMasked', $settingsEncrypted);
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
     * @param string $propertyKey
     * @param bool $autoDecrypt
     * @return bool
     */
    public function getSetting($propertyKey, $autoDecrypt = true)
    {
        /**
         * @var Setting $setting
         */

        //try to get the value from Configure first
        $configValue = Configure::read("Settings.{$propertyKey}");
        $configIsMasked = Configure::read("SettingsMasked.{$propertyKey}");
        if ($configValue !== null) {
            if ($autoDecrypt == true) {
                if ($configIsMasked == true) {
                    if (strlen($configValue) > 0) {
                        $configValue = Security::decrypt64($configValue);
                    }
                }
            }
            return $configValue;
        }

        //update the Configure values
        $this->saveSettingsToConfigure(false);

        //fallback to reading from DB
        $setting = $this->findByPropertyKey($propertyKey)->first();
        debug($setting);

        if (!$setting) {
            return false;
        }

        if (isset($setting->property_value)) {
            $configValue = $setting->property_value;
            if ($autoDecrypt == true) {
                if ($setting->is_masked == true) {
                    if (strlen($configValue) > 0) {
                        $configValue = Security::decrypt64($configValue);
                    }
                }
            }
        } else {
            $configValue = false;
        }

        if ($configValue === 'false') {
            $configValue = false;
        }

        if ($configValue === 'true') {
            $configValue = true;
        }

        if ($configValue === 'null') {
            $configValue = null;
        }

        return $configValue;
    }

    /**
     * Convenience Method to set a value based on the passed in key
     * Can only be used to update.
     *
     * @param string|Setting $propertyKeyOrEntity
     * @param string $propertyValue
     * @param bool $autoEncrypt
     * @return bool
     */
    public function setSetting($propertyKeyOrEntity, $propertyValue, $autoEncrypt = true)
    {
        /**
         * @var Setting $setting
         */
        if ($propertyKeyOrEntity instanceof Setting) {
            $setting = $propertyKeyOrEntity;
        } else {
            $setting = $this->findByPropertyKey($propertyKeyOrEntity)->first();
        }

        if ($setting) {
            //if no change save hitting DB and exit
            if ($setting->property_value == $propertyValue) {
                return true;
            }

            if ($setting->html_select_type == 'multiple') {
                if (is_array($propertyValue)) {
                    $propertyValue = implode(',', $propertyValue);
                }
            }

            if ($autoEncrypt == true) {
                if ($setting->is_masked == true) {
                    if (strlen($propertyValue) > 0) {
                        $propertyValue = Security::encrypt64($propertyValue);
                    }
                }
            }

            $setting->property_value = $propertyValue;
            $result = $this->save($setting);

            //update the Configure values
            $this->saveSettingsToConfigure(false);

            return $result;
        } else {
            return false;
        }
    }

    /**
     * Convenience Method to get all the Email details
     *
     * @return array
     */
    public function getEmailDetails()
    {
        //try to read from Configure First
        $results = Configure::read("SettingsGrouped.email_server");
        if ($results) {
            $results['email_password'] = Security::decrypt64($results['email_password']);
            return $results;
        }

        //fall back to read from DB
        $results = $this->find('list', ['keyField' => 'property_key', 'valueField' => 'property_value'])
            ->where(['property_group' => 'email_server'])
            ->toArray();
        $results['email_password'] = Security::decrypt64($results['email_password']);
        return $results;
    }

    /**
     * Return a FrozenTime object for a password expiry date.
     * Date is based on the password_expiry setting.
     *
     * @return Time|FrozenTime
     */
    public function getPasswordExpiryDate()
    {
        $days = $this->getSetting('password_reset_days');

        //fallback
        if ($days <= 0) {
            $days = 365;
        }

        $frozenTimeObj = (new FrozenTime('+' . $days . ' days'))->endOfDay();

        return $frozenTimeObj;
    }

    /**
     * Return a FrozenTime object for a password expiry date that has passed.
     * Useful for when a password needs to have a force reset.
     *
     * @return Time|FrozenTime
     */
    public function getExpiredPasswordExpiryDate()
    {
        $frozenTimeObj = (new FrozenTime('-10 days'))->startOfDay();

        return $frozenTimeObj;
    }

    /**
     * Return a FrozenTime object for a user account activation date.
     * Date is start of today.
     *
     * @return Time|FrozenTime
     */
    public function getAccountActivationDate()
    {
        $frozenTimeObj = (new FrozenTime())->startOfDay();

        return $frozenTimeObj;
    }

    /**
     * Return a FrozenTime object for a user account expiration date.
     * Date is based on the account_expiry setting.
     *
     * @return Time|FrozenTime
     */
    public function getAccountExpirationDate()
    {
        $days = $this->getSetting('account_expiry');

        //fallback
        if ($days <= 0) {
            $days = 365;
        }

        $frozenTimeObj = (new FrozenTime('+' . $days . ' days'))->endOfDay();

        return $frozenTimeObj;
    }

    /**
     * Return a FrozenTime object for the default expiration date.
     * Date is based on the data_purge setting.
     *
     * @return Time|FrozenTime
     */
    public function getDefaultActivationDate()
    {
        $frozenTimeObj = (new FrozenTime())->startOfDay();

        return $frozenTimeObj;
    }

    /**
     * Return a FrozenTime object for the default expiration date.
     * Date is based on the data_purge setting.
     *
     * @return Time|FrozenTime
     */
    public function getDefaultExpirationDate()
    {
        $days = $this->getSetting('data_purge');

        //fallback
        if ($days <= 0) {
            $days = 365 * 10;
        }

        $frozenTimeObj = (new FrozenTime('+' . $days . ' days'))->endOfDay();

        return $frozenTimeObj;
    }

    /**
     * Check if the passed in domain is in the whitelist.
     * Whitelist filtering will not be employed if whitelist is empty.
     *
     * @param $domain
     * @return bool
     */
    public function isDomainWhitelisted($domain)
    {
        $loginDomainWhitelist = $this->getSetting('login_domain_whitelist');
        $loginDomainWhitelist = trim($loginDomainWhitelist);

        if (empty($loginDomainWhitelist)) {
            return true;
        }

        $loginDomainWhitelist = str_replace(["\r\n", "\r", "\n"], ",", $loginDomainWhitelist);
        $loginDomainWhitelist = explode(",", $loginDomainWhitelist);

        if (in_array($domain, $loginDomainWhitelist)) {
            return true;
        }

        return false;
    }


    /**
     * Convenience function to set the Repository settings.
     * Included some basic validation.
     *
     * @param $repoSettings
     * @return bool
     */
    public function setRepositoryDetails($repoSettings)
    {
        if (isset($repoSettings['repo_unc'])) {
            $s = TextFormatter::makeEndsWith($repoSettings['repo_unc'], "\\");
            $this->setSetting('repo_unc', $s);
        }

        if (isset($repoSettings['repo_url'])) {
            $s = TextFormatter::makeEndsWith($repoSettings['repo_url'], "/");
            $this->setSetting('repo_url', $s);
        }

        if (isset($repoSettings['repo_mode'])) {
            $s = strtolower($repoSettings['repo_mode']);
            if ($s !== 'dynamic' && $s !== 'static') {
                $s = 'static';
            }
            $this->setSetting('repo_mode', $s);
        }

        if (isset($repoSettings['repo_purge'])) {
            $s = intval($repoSettings['repo_purge']);
            if ($s < 3 || $s > 600) {
                $s = 12;
            }
            $this->setSetting('repo_purge', $s);
        }

        if (isset($repoSettings['repo_purge_interval'])) {
            $s = intval($repoSettings['repo_purge_interval']);
            if ($s < 5 || $s > 30) {
                $s = 10;
            }
            $this->setSetting('repo_purge_interval', $s);
        }

        if (isset($repoSettings['repo_purge_limit'])) {
            $s = intval($repoSettings['repo_purge_limit']);
            if ($s < 1 || $s > 5) {
                $s = 1;
            }
            $this->setSetting('repo_purge_limit', $s);
        }

        if (isset($repoSettings['repo_sftp_host'])) {
            $s = $repoSettings['repo_sftp_host'];
            $this->setSetting('repo_sftp_host', $s);
        }

        if (isset($repoSettings['repo_sftp_port'])) {
            $s = intval($repoSettings['repo_sftp_port']);
            if ($s < 1 || $s > 64000) {
                $s = 22;
            }
            $this->setSetting('repo_sftp_port', $s);
        }

        if (isset($repoSettings['repo_sftp_username'])) {
            $s = $repoSettings['repo_sftp_username'];
            $this->setSetting('repo_sftp_username', $s);
        }

        if (isset($repoSettings['repo_sftp_password'])) {
            $s = $repoSettings['repo_sftp_password'];
            $this->setSetting('repo_sftp_password', $s);
        }

        if (isset($repoSettings['repo_sftp_timeout'])) {
            $s = intval($repoSettings['repo_sftp_timeout']);
            if ($s < 1 || $s > 10) {
                $s = 2;
            }
            $this->setSetting('repo_sftp_timeout', $s);
        }

        if (isset($repoSettings['repo_sftp_path'])) {
            $s = $repoSettings['repo_sftp_path'];
            $this->setSetting('repo_sftp_path', $s);
        }

        if (isset($repoSettings['repo_size_icon'])) {
            $s = intval($repoSettings['repo_size_icon']);
            if ($s < 32 || $s > 256) {
                $s = 128;
            }
            $this->setSetting('repo_size_icon', $s);
        }

        if (isset($repoSettings['repo_size_thumbnail'])) {
            $s = intval($repoSettings['repo_size_thumbnail']);
            if ($s < 256 || $s > 512) {
                $s = 256;
            }
            $this->setSetting('repo_size_thumbnail', $s);
        }

        if (isset($repoSettings['repo_size_preview'])) {
            $s = intval($repoSettings['repo_size_preview']);
            if ($s < 512 || $s > 1024) {
                $s = 512;
            }
            $this->setSetting('repo_size_preview', $s);
        }

        if (isset($repoSettings['repo_size_lr'])) {
            $s = intval($repoSettings['repo_size_lr']);
            if ($s < 1024 || $s > 1600) {
                $s = 1024;
            }
            $this->setSetting('repo_size_lr', $s);
        }

        if (isset($repoSettings['repo_size_mr'])) {
            $s = intval($repoSettings['repo_size_mr']);
            if ($s < 1600 || $s > 2400) {
                $s = 1600;
            }
            $this->setSetting('repo_size_mr', $s);
        }

        if (isset($repoSettings['repo_size_hr'])) {
            $s = intval($repoSettings['repo_size_hr']);
            if ($s < 2400 || $s > 4800) {
                $s = 2400;
            }
            $this->setSetting('repo_size_hr', $s);
        }

        return true;
    }

}
