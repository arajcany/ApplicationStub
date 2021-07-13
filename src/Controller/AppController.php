<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Controller\Component\AppSettingsForUserComponent;
use App\Log\Engine\Auditor;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Controller\Exception\SecurityException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;
use Exception;
use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\SeedsTable $Seeds
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\InternalOptionsTable $InternalOptions
 *
 * @property \App\Controller\Component\FlashComponent $Flash
 * @property \TinyAuth\Controller\Component\AuthComponent $Auth
 * @property \TinyAuth\Controller\Component\AuthUserComponent $AuthUser
 * @property AppSettingsForUserComponent $AppSettingsForUser
 *
 * @property Auditor $Auditor
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 *
 */
class AppController extends Controller
{
    public $Settings;
    public $Seeds;
    public $Roles;
    public $InternalOptions;
    public $timeStartup;
    public $timeShutdown;
    public $Auditor;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return Response
     * @throws \Exception
     */
    public function initialize()
    {
        $this->timeStartup = microtime(true);

        parent::initialize();

        $this->Auditor = new Auditor();

        $this->loadModel('Settings');
        $this->loadModel('Seeds');
        $this->loadModel('Roles');
        $this->loadModel('InternalOptions');

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        $securityHttps = boolval(Configure::read('Settings.security_https'));

        //load SecurityComponent if required
        if ($securityHttps) {
            $securityConfig = [
                'blackHoleCallback' => 'blackHoleHandler',
            ];
            $this->loadComponent('Security', $securityConfig);
            $this->Security->requireSecure();
        }


        $this->set('basePath', Router::url(null, false));

        /**
         * Authentication is the process of identifying users by provided credentials and ensuring
         * that users are who they say they are. Generally, this is done through a username and password,
         * that are checked against a known list of users.
         *
         * Authorization is the process of ensuring that an identified/authenticated user is
         * allowed to access the resources they are requesting.
         */
        $tinyAuthorizeConfig = [
            'autoClearCache' => false,
            'multiRole' => true,
            'pivotTable ' => 'roles_users',
            'roleColumn ' => 'roles',
        ];
        $this->loadComponent(
            'TinyAuth.Auth',
            [
                'loginRedirect' => [
                    'controller' => '/',
                    'action' => '',
                ],
                'logoutRedirect' => [
                    'controller' => 'login',
                    'action' => '',
                ],
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'username', 'password' => 'password'],
                        'finder' => 'auth',
                    ],
                ],
                'autoClearCache' => false,
                'authorize' => [
                    'TinyAuth.Tiny' => $tinyAuthorizeConfig
                ],
                'checkAuthIn' => 'Controller.initialize',
                'authError' => __('Sorry, you are not authorised to access that location.'),
                'flash' => [
                    'element' => 'error',
                    'key' => 'flash',
                    'params' => ['class' => 'error this']
                ],
            ]
        );
        //debug($this->Auth->getConfig());
        //debug($this->Auth->user());

        $this->loadComponent('TinyAuth.AuthUser', $tinyAuthorizeConfig);
        //debug($this->AuthUser->getConfig());

        //if User is logged in, configure the App for the User
        if ($this->Auth->user()) {
            $this->loadComponent('AppSettingsForUser');
            $this->AppSettingsForUser->setTimeouts();
            $userLocalizations = $this->AppSettingsForUser->getLocalization();
        } else {
            $userLocalizations = [];
        }

        //localizations from DB
        if (Configure::check('SettingsGrouped.localization')) {
            $appLocalizations = Configure::read('SettingsGrouped.localization');
        } else {
            $appLocalizations = [];
        }

        //localizations from bootstrap
        $defaultLocalisations =
            [
                'locale' => Configure::read("App.defaultLocale"),
                'timezone' => Configure::read("App.defaultTimezone"),
                'location' => '',
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'HH:mm:ss',
                'datetime_format' => 'yyyy-MM-dd HH:mm:ss',
                'week_start' => 'Sunday'
            ];

        $compiledLocalisations = array_merge($defaultLocalisations, $appLocalizations, $userLocalizations);

        //set some localizations as constants
        if (!defined('LCL')) {
            define('LCL', $compiledLocalisations);
        }
        if (!defined('TZ')) {
            define('TZ', $compiledLocalisations['timezone']);
        }
        if (!defined('TF')) {
            define('TF', $compiledLocalisations['time_format']);
        }
        if (!defined('DF')) {
            define('DF', $compiledLocalisations['date_format']);
        }
        if (!defined('DTF')) {
            define('DTF', $compiledLocalisations['datetime_format']);
        }

        //kill flash messages if User has been redirected to /login from /
        $currentPath = $this->request->getPath();
        if ($currentPath == '/login') {
            $refererPath = $this->request->referer();
            if ($refererPath == '/') {
                $msgs = $this->request->getSession()->read("Flash.flash");
                if (is_array($msgs)) {
                    foreach ($msgs as $k => $msg) {
                        if ($msg) {
                            //debug($this->Auth->getConfig('authError'));
                            if ($msg['message'] == $this->Auth->getConfig('authError')) {
                                $this->request->getSession()->delete("Flash.flash.{$k}");
                            };
                        }
                    }
                }
            }
        }

        if (is_file(CONFIG . 'version.json')) {
            $version = file_get_contents(CONFIG . 'version.json');
            $version = json_decode($version, JSON_OBJECT_AS_ARRAY);
        } else {
            $version = [
                "name" => "ApplicationStub",
                "tag" => "0.0.0",
                "desc" => "",
                "codename" => ""
            ];
        }

        $this->set('version', $version);

    }


    /**
     * Handle black-holed connections
     *
     * @param string $type passed in by the callback as either 'secure' || 'auth'
     * @param SecurityException|null $exception
     * @return Response|null
     */
    public function blackHoleHandler($type = '', SecurityException $exception = null)
    {
        //http/s
        if ($type == 'secure') {
            if (!$this->request->is('ssl')) {
                return $this->redirect('https://' . env('SERVER_NAME') . Router::url($this->request->getRequestTarget()));
            }
        }

        return null;
    }

    /**
     * Clear the Cache
     */
    public function clearCache()
    {
        try {
            $result = Cache::clearAll();
        } catch (\Throwable $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * @return Response|void|null
     */
    public function shutdownProcess()
    {
        parent::shutdownProcess();

        $this->timeShutdown = microtime(true);
        $execution_time = ($this->timeShutdown - $this->timeStartup);

        if (Configure::read('Settings.hit_tracking_enabled')) {
            $passed = $this->request->getUri();
            $otherData = [
                'app_execution_time' => $execution_time,
            ];
            $this->Auditor->trackHit($passed, $otherData);
        }
    }
}
