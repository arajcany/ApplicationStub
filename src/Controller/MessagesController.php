<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Routing\Router;
use Exception;

/**
 * Messages Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 * @property \App\Model\Table\MessageBeaconsTable $MessageBeacons
 *
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MessagesController extends AppController
{
    public $MessageBeacons;

    /**
     * Initialize method
     *
     * @return Response|null
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('MessageBeacons');

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $messages = $this->paginate($this->Messages);

        $this->set(compact('messages'));
    }

    /**
     * View method
     *
     * @param string|null $id Message id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);

        $this->set('message', $message);
    }

    /**
     * Special method to receive beacons.
     * Provides a URL with (virtually) unlimited number of parameters.
     *
     * @param mixed ...$options
     * @return Response|null
     */
    public function beacons(...$options)
    {
        $passed = $this->request->getUri();
        $result = $this->MessageBeacons->logBeacon($passed, $options);

        $responseData = "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";

        $this->response = $this->response->withType('gif');
        $this->response = $this->response->withStringBody($responseData);

        return $this->response;
    }
}
