<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\ArtifactsTable;
use App\Model\Table\ErrandsTable;
use App\Model\Table\MessagesTable;
use App\Model\Table\WorkersTable;
use arajcany\ToolBox\Utility\Security\Security;
use Cake\Cache\Cache;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;
use Cake\Network\Exception\SocketException;
use Cake\ORM\TableRegistry;

/**
 * Developers Controller
 *
 * @property ArtifactsTable $Artifacts;
 * @property ErrandsTable $Errands;
 * @property WorkersTable $Workers;
 * @property MessagesTable $Messages;
 */
class DevelopersController extends AppController
{
    private $Errands;
    private $Workers;
    private $Messages;

    public function initialize()
    {
        parent::initialize();

        $this->Artifacts = TableRegistry::getTableLocator()->get('Artifacts');
        $this->Errands = TableRegistry::getTableLocator()->get('Errands');
        $this->Workers = TableRegistry::getTableLocator()->get('Workers');
        $this->Messages = TableRegistry::getTableLocator()->get('Messages');
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {

    }


    public function errand()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $options = [
            'class' => 'Seeds',
            'method' => 'createSeed',
            //'parameters' => [0 => ['token' => sha1(Security::randomBytes(1024))]],
            'parameters' => [0 => ['token' => sha1('abc123')]],
        ];

        $errand = $this->Errands->createErrand($options);
        $toDebug['$errand'] = $errand;


//        $worker = $this->Workers->getWorker('errand');
//        $toDebug['$worker'] = $worker;

        $this->set('toDebug', $toDebug);
    }


    public function message()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $data = [
            'name' => 'Developer Test',
            'description' => 'Send mail called from the Developers Controllers',
            'view_vars' => [
                'entities' => [
                    'user' => 1,
                    'artifacts' => [1, 2, 3, 4],
                    'pings' => ['table' => 'users', 'id' => 2],
                    'pongs' => ['table' => 'users', 'id' => [3, 4, 5]],
                ],
                'foo' => 'This is a foo',
                'bar' => 'This is a bar',
            ],
            'email_to' => ["abc@example.com" => "ABC"],
            'subject' => 'Some Random Number: ' . mt_rand(11111, 99999),
            //'subject' => 'Some Random Number: ' . 000,
            'body' => '<p>Some HTML Text</p>',
            //'template' => 'transactional/Invitation/invite_franchisee',
            //'layout' => 'transactional/process',
        ];

        $message = $this->Messages->createMessage($data);
        $toDebug['$message'] = $message;

        //$expandedEntities = $this->Messages->expandEntities($data['view_vars']['entities'], false);
        //$toDebug['expandedEntities'] = $expandedEntities;


//        try {
//            $email = new Email('default');
//            $emailResult = $email->setFrom(['me@example.com' => 'My Site'])
//                ->setTo('you@example.com')
//                ->setSubject('About')
//                ->send('My message');
//            $toDebug['$emailResult'] = $emailResult;
//        } catch (\Throwable $exception) {
//            $toDebug['$emailResult'] = $exception;
//        }

        $this->set('toDebug', $toDebug);
    }


    public function artifacts()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $options = [
            'width' => mt_rand(64, 256),
            'height' => mt_rand(64, 256),
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];

        //$artifact = $this->Artifacts->createPlaceholderArtifact($options);
        //$toDebug['$errand'] = $artifact;

        $deleted = $this->Artifacts->deleteTopExpired();
        $toDebug['$deleted'] = $deleted;

        $artifact = $this->Artifacts->createArtifactFromUrl('24pp.pdf', 'http://genericrepository.local/24pp.pdf');
        $toDebug['$artifact'] = $artifact->full_url;

        $this->set('toDebug', $toDebug);
    }


    public function trackUploads()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

//        $options = [
//            'width' => mt_rand(64, 256),
//            'height' => mt_rand(64, 256),
//            'background' => '#808080',
//            'format' => 'png',
//            'quality' => '90',
//        ];

//        $artifact = $this->Artifacts->createPlaceholderArtifact($options);
//        $toDebug['$errand'] = $artifact;

        $data = [
            [
                'name' => 'the jpg',
                'modified' => new FrozenTime(),
                'type' => 'jpg',
                'tmp_name' => '/some/jpg',
                'finfo_mime_type' => 'image/jpeg',
                //'error' => false,
                'username' => 'some_user',
                'created' => new FrozenTime(),
                'rnd_hash' => 5432,
                'batch_reference' => 1,
                'dud_field' => 'foo',
                'size' => 12345,
            ],
            [
                'created' => new FrozenTime(),
                'modified' => new FrozenTime(),
                'name' => 'the png',
                //'type' => 'png',
                'tmp_name' => '/some/png',
                'size' => 12345,
                //'error' => false,
                //'finfo_mime_type' => 'image/png',
                //'username' => 'some_user',
                'rnd_hash' => 9876,
                'batch_reference' => 1,
                'dud_field' => 'bar',
            ],
        ];
        //$this->TrackUploads->massInsert($data);

        $frozenTime = new FrozenTime();
        $dataMaster = [
            'name' => 'the jpg ',
            'created' => $frozenTime,
            'modified' => $frozenTime,
            'type' => 'jpg',
            'tmp_name' => '/some/jpg',
            'finfo_mime_type' => 'image/jpeg',
            'error' => false,
            'username' => 'some_user',
            'rnd_hash' => '',
            'batch_reference' => 1,
            'dud_field' => 'foo',
            'size' => mt_rand(111, 999),
        ];
        $range = range(1, 1000000);
        $data = [];
        foreach ($range as $number) {
            $dataTmp = $dataMaster;
            $dataTmp['name'] = $dataTmp['name'] . mt_rand(11111, 99999);
            $dataTmp['rnd_hash'] = sha1(mt_rand());
            $data[] = $dataTmp;
        }

        $dataChunked = partition($data, 1200);
        $dataSub = $dataChunked[0];
        debug($dataSub);


        $start = new FrozenTime();
        //$this->TrackUploads->massInsert($data);
        $end = new FrozenTime();
        $toDebug['time'] = "Completed in " . $start->diffInSeconds($end) . " seconds";

        $this->set('toDebug', $toDebug);
    }


    public function trackHits()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $start = (new FrozenTime())->subMinutes(5);
        $end = (new FrozenTime())->addMinutes(5);

        $query = $this->TrackHits->findRemoteUserData();
        //sqld($query);
        $toDebug['$query'] = $query->toArray();


        $this->set('toDebug', $toDebug);
    }


    public function cache()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        $toDebug['Cache1'] = Cache::read('first_run');
        $toDebug['Cache2'] = Cache::read('setttings');
        $toDebug['Cache3'] = Cache::read('internal_options');
        $toDebug['clear'] = $this->clearCache();


        $this->set('toDebug', $toDebug);
    }


    public function workers()
    {
        $this->viewBuilder()->setTemplate('to_debug');
        $toDebug = [];

        //$toDebug['heartbeats'] = $this->Workers->find('all')->limit(5)->contain(['heartbeats'])->toArray();
        $toDebug['heartbeats'] = $this->Workers->findHeartbeats()->toArray();
        $toDebug['pulses'] = sqld($this->Workers->findPulses(0));

        $this->set('toDebug', $toDebug);
    }


}
