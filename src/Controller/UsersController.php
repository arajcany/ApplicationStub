<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Database\Driver\Sqlite;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;
use Cake\Validation\Validation;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\SettingsTable $Settings
 * @property \App\Model\Table\SeedsTable $Seeds
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public $Settings;
    public $Seeds;

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Settings');
        $this->loadModel('Seeds');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['UserStatuses', 'Roles']
        ];
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $userStatuses = $this->Users->UserStatuses->find('list', ['limit' => 200]);
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'userStatuses', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $userStatuses = $this->Users->UserStatuses->find('list', ['limit' => 200]);
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'userStatuses', 'roles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Update method - Users can self-update their details
     *
     * @return \Cake\Http\Response|null
     */
    public function profile()
    {
        $id = $this->Auth->user('id');
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            //avoid mass-assignment attack
            $options = [
                'fieldList' => [
                    'email',
                    'username',
                    'password',
                    'first_name',
                    'last_name',
                    'address_1',
                    'address_2',
                    'suburb',
                    'state',
                    'post_code',
                    'mobile',
                    'phone',
                ]
            ];

            $user = $this->Users->patchEntity($user, $this->request->getData(), $options);
            if ($this->Users->save($user)) {

                $this->Flash->success(__('Your profile has been updated.'));
                return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Your profile could not be updated. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);

        return null;
    }

    /**
     * Login to the application
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        //die if application login is requested outside of authorised domains
        $domain = str_replace(['http://', 'https://'], "", Router::fullBaseUrl());
        $isAllowed = $this->Settings->isDomainWhitelisted($domain);
        if (!$isAllowed) {
            $this->response = $this->response->withType('text/plain');
            $this->response = $this->response->withStringBody('Not Allowed!');
            return $this->response;
        }

        if (Cache::read('first_run', 'quick_burn') === true) {
            return $this->redirect(['controller' => 'installers', 'action' => 'configure']);
        }

        $dbDriver = ($this->Users->getConnection())->getDriver();
        if ($dbDriver instanceof Sqlite) {
            $caseSensitive = true;
        } else {
            $caseSensitive = false;
        }
        $this->set('caseSensitive', $caseSensitive);

        $this->viewBuilder()->setLayout('login');

        //see if they are already logged in
        if ($this->Auth->user()) {
            return $this->redirect($this->Auth->redirectUrl());
        }

        $user = $this->Users->newEntity();

        if ($this->request->is('post')) {

            //allow switching between email and username for authentication
            if (Validation::email($this->request->getData('username'))) {
                $this->Auth->setConfig('authenticate', [
                    'Form' => [
                        'fields' => ['username' => 'email', 'password' => 'password']
                    ]
                ]);
                $this->Auth->constructAuthenticate();
            } else {
                $this->Auth->setConfig('authenticate', [
                    'Form' => [
                        'fields' => ['username' => 'username', 'password' => 'password']
                    ]
                ]);
                $this->Auth->constructAuthenticate();
            }

            $userDetails = $this->Auth->identify();

            //check if need to reset password
            if ($userDetails) {
                if ($this->Users->isPasswordExpired($userDetails)) {
                    $this->Flash->warning(__('Your password has expired, please enter a new password.'));
                    $options = [
                        'expiration' => new FrozenTime('+ 1 hour'),
                        'user_link' => $userDetails['id'],
                    ];
                    $autoLoginToken = $this->Seeds->createSeedReturnToken($options);

                    $options = [
                        'url' => ['controller' => 'users', 'action' => 'reset', '{token}', $autoLoginToken],
                        'expiration' => new FrozenTime('+ 1 hour'),
                        'user_link' => $userDetails['id'],
                    ];
                    $token = $this->Seeds->createSeedReturnToken($options);

                    return $this->redirect(['controller' => 'users', 'action' => 'reset', $token, $autoLoginToken]);
                }
            }

            //login process
            if ($userDetails) {
                $accountStatus = $this->Users->validateAccountStatus($userDetails);
                if ($accountStatus) {
                    $this->Auth->setUser($userDetails);
                    $this->TrackLogins->logUser($userDetails);
                    return $this->redirect($this->Auth->redirectUrl());
                } else {
                    $messages = $this->Users->getAuthError();
                    foreach ($messages as $message) {
                        $this->Flash->error($message);
                    }
                }
            } else {
                $this->Flash->error(__('Invalid username or password, try again.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);

        return null;
    }

    /**
     * Logout of the application
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        $this->Auth->logout();
        $this->request->getSession()->destroy();
        $this->Flash->success(__('Successfully logged out'));
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Reset the password
     *
     * @param bool $token
     * @param bool $autoLoginToken
     * @return \Cake\Http\Response|null
     */
    public function reset($token = false, $autoLoginToken = false)
    {
        $this->viewBuilder()->setLayout('login');
        $this->set('token', $token);

        if ($token == false) {
            return $this->redirect(['controller' => 'login']);
        }

        $isTokenValid = $this->Seeds->validateSeed($token);

        if ($isTokenValid == false) {
            $this->viewBuilder()->setTemplate('reset_fail');
            $header = "Ooops!";
            $message = "Sorry, the link to reset your password is no longer valid.";
            $this->set('header', $header);
            $this->set('message', $message);
            return null;
        }

        $user = $this->Users->newEntity();

        if ($this->request->is('post')) {
            $seed = $this->Seeds->getSeed($token);
            $user = $this->Users->get($seed->user_link);

            //avoid mass-assignment attack
            $options = [
                'fieldList' => [
                    'password',
                    'password_2',
                    'is_confirmed'
                ]
            ];

            $patchData = $this->request->getData();
            $patchData['is_confirmed'] = true;
            $user = $this->Users->patchEntity($user, $patchData, $options);

            $user->password_expiry = $this->Settings->getPasswordExpiryDate();

            if ($this->Users->save($user)) {
                //increase bid on the token
                $this->Seeds->increaseBid($token);

                if ($autoLoginToken) {
                    $isAutoLoginTokenValid = $this->Seeds->validateSeed($autoLoginToken);
                    if ($isAutoLoginTokenValid) {
                        $this->Flash->success(__('Your password has been updated.'));

                        $this->Seeds->increaseBid($autoLoginToken);
                        $autoLoginTokenDetails = $this->Seeds->getSeed($autoLoginToken);

                        $options = [
                            'url' => ['controller' => 'users', 'action' => 'entry', '{token}'],
                            'expiration' => new FrozenTime('+ 1 hour'),
                            'user_link' => $autoLoginTokenDetails->user_link,
                        ];
                        $newAutoLoginToken = $this->Seeds->createSeedReturnToken($options);

                        return $this->redirect(['controller' => 'users', 'action' => 'entry', $newAutoLoginToken]);
                    }
                }

                $this->Flash->success(__('Password updated, please sign in.'));
                return $this->redirect(['action' => 'login']);
            } else {
                $this->Flash->error(__('Error updating password. Please, try again.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);

        return null;
    }

    /**
     * Auto-login entry point.
     * Used for one-time entries and after password expiry reset.
     *
     * @param bool $autoLoginToken
     * @return \Cake\Http\Response|null
     */
    public function entry($autoLoginToken = false)
    {
        if ($autoLoginToken == false) {
            return $this->redirect(['controller' => 'login']);
        }

        if ($autoLoginToken) {
            $isAutoLoginTokenValid = $this->Seeds->validateSeed($autoLoginToken);
            if ($isAutoLoginTokenValid) {
                $this->Seeds->increaseBid($autoLoginToken);
                $autoLoginTokenDetails = $this->Seeds->getSeed($autoLoginToken);
                $userId = $autoLoginTokenDetails->user_link;
                $userDetails = $this->Users->get($userId)->toArray();

                //check if need to reset password
                if ($userDetails) {
                    if ($this->Users->isPasswordExpired($userDetails)) {
                        $this->Flash->warning(__('Your password has expired, please enter a new password.'));
                        $options = [
                            'expiration' => new FrozenTime('+ 1 hour'),
                            'user_link' => $userDetails['id'],
                        ];
                        $autoLoginToken = $this->Seeds->createSeedReturnToken($options);

                        $options = [
                            'url' => ['controller' => 'users', 'action' => 'reset', '{token}', $autoLoginToken],
                            'expiration' => new FrozenTime('+ 1 hour'),
                            'user_link' => $userDetails['id'],
                        ];
                        $token = $this->Seeds->createSeedReturnToken($options);

                        return $this->redirect(['controller' => 'users', 'action' => 'reset', $token, $autoLoginToken]);
                    }
                }

                //login process
                if ($userDetails) {
                    $accountStatus = $this->Users->validateAccountStatus($userDetails);
                    if ($accountStatus) {
                        $this->Auth->setUser($userDetails);
                        $this->TrackLogins->logUser($userDetails);
                        return $this->redirect($this->Auth->redirectUrl());
                    } else {
                        $messages = $this->Users->getAuthError();
                        foreach ($messages as $message) {
                            $this->Flash->error($message);
                        }
                    }
                } else {
                    return $this->redirect(['controller' => 'login']);
                }
            } else {
                $this->Flash->warning(__('Sorry the one time entry key has expired. Please login with your Username/Email and Password.'));
            }
        }

        return $this->redirect(['controller' => 'login']);
    }

}
