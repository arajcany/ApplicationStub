<?php

namespace App\Controller\Component;

use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * AppSettingsForUser component
 *
 * The main purpose of this Component is to configure the App with Settings specific to a User
 * e.g. Timezone, locale, language, preferences, GUI interface and so forth
 *
 * @property \TinyAuth\Controller\Component\AuthComponent $Auth
 * @property \TinyAuth\Controller\Component\AuthUserComponent $AuthUser
 * @property UsersTable $Users
 */
class AppSettingsForUserComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $components = ['Auth', 'AuthUser'];
    private $Users;

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }


    /**
     * Set the Session Timeout for the User
     *
     * @return bool
     */
    public function setTimeouts()
    {
        if ($this->Auth->user()) {
            $timeouts = $this->Users->Roles->listByNameAndTimeout();
            if ($this->AuthUser->hasRole('superadmin')) {
                Configure::write('Session.timeout', $timeouts['SuperAdmin']);
                Configure::write('Session.cookie_lifetime', $timeouts['SuperAdmin']);
                $timeout = $timeouts['SuperAdmin'];
            } elseif ($this->AuthUser->hasRole('admin')) {
                Configure::write('Session.timeout', $timeouts['Admin']);
                Configure::write('Session.cookie_lifetime', $timeouts['Admin']);
                $timeout = $timeouts['Admin'];
            } elseif ($this->AuthUser->hasRole('superuser')) {
                Configure::write('Session.timeout', $timeouts['SuperUser']);
                Configure::write('Session.cookie_lifetime', $timeouts['SuperUser']);
                $timeout = $timeouts['SuperUser'];
            } elseif ($this->AuthUser->hasRole('user')) {
                Configure::write('Session.timeout', $timeouts['User']);
                Configure::write('Session.cookie_lifetime', $timeouts['User']);
                $timeout = $timeouts['User'];
            } elseif ($this->AuthUser->hasRole('manager')) {
                Configure::write('Session.timeout', $timeouts['Manager']);
                Configure::write('Session.cookie_lifetime', $timeouts['Manager']);
                $timeout = $timeouts['Manager'];
            } elseif ($this->AuthUser->hasRole('supervisor')) {
                Configure::write('Session.timeout', $timeouts['Supervisor']);
                Configure::write('Session.cookie_lifetime', $timeouts['Supervisor']);
                $timeout = $timeouts['Supervisor'];
            } elseif ($this->AuthUser->hasRole('operator')) {
                Configure::write('Session.timeout', $timeouts['Operator']);
                Configure::write('Session.cookie_lifetime', $timeouts['Operator']);
                $timeout = $timeouts['Operator'];
            } else {
                $timeout = false;
            }

            return $timeout;
        } else {
            return false;
        }

    }


    /**
     * Get the Localization values for the logged in User
     *
     * @return array|\Cake\Datasource\EntityInterface|null
     */
    public function getLocalization()
    {
        /**
         * @var User $user
         */
        $user = $this->Users->find('all')
            ->where(['Users.id' => $this->AuthUser->id()])
            ->contain(['UserLocalizations'])
            ->first();

        if (!$user->user_localization) {
            return [];
        }

        $localizationValues = [
            'location' => $user->user_localization->location,
            'locale' => $user->user_localization->locale,
            'timezone' => $user->user_localization->timezone,
            'time_format' => $user->user_localization->time_format,
            'date_format' => $user->user_localization->date_format,
            'datetime_format' => $user->user_localization->datetime_format,
            'week_start' => $user->user_localization->week_start,
        ];

        foreach ($localizationValues as $k => $localizationValue) {
            if (is_null($localizationValue) || empty($localizationValue)) {
                unset($localizationValues[$k]);
            }
        }

        return $localizationValues;
    }
}
