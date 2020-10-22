<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * LoadTests Controller
 *
 */
class LoadTestsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
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

        $this->set('$hits', $hits);

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
