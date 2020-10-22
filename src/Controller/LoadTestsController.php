<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\LoadTestsUrlMakerComponent;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;

/**
 * LoadTests Controller
 * @property LoadTestsUrlMakerComponent $LoadTestsUrlMaker
 *
 */
class LoadTestsController extends AppController
{

    /**
     * Initialize method
     *
     * @return Response|null
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('LoadTestsUrlMaker');

        return null;
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return Response|null
     */
    public function index()
    {
        $loadTests = [
            [
                'name' => 'Raw',
                'description' => 'Raw performance to the framework.',
                'controller' => 'load-tests',
                'action' => 'raw',
            ],
        ];

        $this->set(compact('loadTests'));
    }

    /**
     * Index method
     *
     * @param null $timespan
     * @param null $hits
     * @return void
     */
    public function cakePerformance($timespan = null, $hits = null)
    {
        $timespanDefault = 5;
        $hitsDefault = 100;

        if ($this->request->is('post')) {
            if ($this->request->getData('timespan')) {
                $timespan = $this->request->getData('timespan');
            } else {
                $timespan = $timespanDefault;
            }

            if ($this->request->getData('hits')) {
                $hits = $this->request->getData('hits');
            } else {
                $hits = $hitsDefault;
            }
        } else {
            $timespan = $timespanDefault;
            $hits = $hitsDefault;
        }

        $urlRoot = str_replace(Router::url(null, false), '', Router::url(null, true));

        $urlFolder = "/load-tests/splat";

        $finalUrls = [];
        $delayMatrix = [];
        $arrayKeys = [];
        foreach (range(1, $hits) as $counter) {
            $finalUrls[$counter] = $urlRoot . $urlFolder . "/" . sha1(Security::randomBytes(1024));
            $delayMatrix[$counter] = mt_rand(1, $timespan * 1000);
            $arrayKeys[] = $counter;
        }
        asort($delayMatrix);
        $delayMatrix = array_combine($arrayKeys, $delayMatrix);

        $this->set('hits', $hits);
        $this->set('finalUrls', $finalUrls);
        $this->set('timespan', $timespan);
        $this->set('delayMatrix', $delayMatrix);
    }

    /**
     * Index method
     *
     * @param null $timespan
     * @param null $hits
     * @param null $url
     * @return void
     */
    public function urlPerformance($timespan = null, $hits = null, $url = null)
    {
        $timespanDefault = 5;
        $hitsDefault = 100;
        $urlDefault = "https://www.example.com/{rnd_int:1-20}/{rnd_word:1-5}";

        if ($this->request->is('post')) {
            if ($this->request->getData('timespan')) {
                $timespan = $this->request->getData('timespan');
            } else {
                $timespan = $timespanDefault;
            }

            if ($this->request->getData('hits')) {
                $hits = $this->request->getData('hits');
            } else {
                $hits = $hitsDefault;
            }
            if ($this->request->getData('url')) {
                $url = $this->request->getData('url');
            } else {
                $url = $urlDefault;
            }
        } else {
            $timespan = $timespanDefault;
            $hits = $hitsDefault;
            $url = $urlDefault;
        }

        $finalUrls = [];
        $delayMatrix = [];
        $arrayKeys = [];
        foreach (range(1, $hits) as $counter) {
            $urlRoot = $this->LoadTestsUrlMaker->insertVariables($url);

            if (strpos($url, "?") !== false) {
                $qs = '';
            } else {
                $qs = "?r=" . sha1(Security::randomBytes(1024));
            }

            $finalUrls[$counter] = $urlRoot . $qs;
            $delayMatrix[$counter] = mt_rand(1, $timespan * 1000);
            $arrayKeys[] = $counter;
        }
        asort($delayMatrix);
        $delayMatrix = array_combine($arrayKeys, $delayMatrix);

        $this->set('hits', $hits);
        $this->set('timespan', $timespan);
        $this->set('url', $url);
        $this->set('finalUrls', $finalUrls);
        $this->set('delayMatrix', $delayMatrix);
    }


    /**
     * Special method to test raw speed of framework.
     * Provides a URL with (virtually) unlimited number of parameters.
     *
     * @param mixed ...$options
     * @return Response|null
     */
    public function splat(...$options)
    {
        $this->viewBuilder()->setLayout('blank');

        $responseData = Router::parseRequest($this->request);

        $responseData = json_encode($responseData, JSON_PRETTY_PRINT);

        $this->response = $this->response->withType('json');
        $this->response = $this->response->withStringBody($responseData);

        return $this->response;
    }

}
