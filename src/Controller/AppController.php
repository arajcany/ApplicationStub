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
use Cake\Controller\Controller;
use Cake\Http\Exception\UnauthorizedException;
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
 * @property array $BasicAuthUser
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\SeedsTable $Seeds
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\InternalOptionsTable $InternalOptions
 * @property \App\Model\Table\TrackLoginsTable $TrackLogins
 *
 * @property \TinyAuth\Controller\Component\AuthComponent $Auth
 * @property \TinyAuth\Controller\Component\AuthUserComponent $AuthUser
 * @property AppSettingsForUserComponent $AppSettingsForUser
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 *
 * TODO implement HTTPS
 * TODO implement CSRF
 * TODO implement blackHoleHandler
 * TODO implement User locale -> fallback to App locale -> fallback to default locale
 */
class AppController extends Controller
{
    public $BasicAuthUser = [];
    public $Settings;
    public $Seeds;
    public $Roles;
    public $InternalOptions;
    public $TrackLogins;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return \Cake\Http\Response
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Settings');
        $this->loadModel('Seeds');
        $this->loadModel('Roles');
        $this->loadModel('InternalOptions');
        $this->loadModel('TrackLogins');

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');


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

    }
}
