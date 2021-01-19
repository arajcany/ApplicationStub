<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\LoadTestsUrlMakerComponent;
use App\Model\Table\ArtifactsTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;
use Intervention\Image\ImageManager;

/**
 * LoadTests Controller
 * @property LoadTestsUrlMakerComponent $LoadTestsUrlMaker
 * @property ArtifactsTable $Artifacts
 *
 */
class LoadTestsController extends AppController
{
    public $Artifacts;

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
        $this->loadModel('Artifacts');

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
    public function applicationPerformance($timespan = null, $hits = null)
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
        $finalUrls = array_values($finalUrls);
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

        $urlRoot = str_replace(Router::url(null, false), '', Router::url(null, true));
        $urlFolder = "/load-tests/image";

        $urlDefault = "{$urlRoot}{$urlFolder}/RandomInteger-{rnd_int:1-20}/RandomIntegerPadded-{rnd_pad_int:56-5600}/{rnd_word:1-5}";
        $urlDefault = "{$urlRoot}{$urlFolder}/{rnd_int:400-500}/auto/auto/{rnd_word:1}.jpg";

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
                $qs = "?r=" . substr(sha1(Security::randomBytes(1024)), 0, 8);
            }

            $finalUrls[$counter] = $urlRoot . $qs;
            $delayMatrix[$counter] = mt_rand(1, $timespan * 1000);
            $arrayKeys[] = $counter;
        }
        $finalUrls = array_values($finalUrls);
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

    public function image($size = 'auto', $format = 'auto', $quality = 'auto', $namePlaceholder = null)
    {
        //----Size----------------------------------------------
        $allowedSizes = [
            'icon',
            'thumbnail',
            'preview',
            'lr',
            'mr',
            'hr',
        ];

        $size = strtolower($size);

        if ($size == 'auto') {
            $size = 'preview';
        }

        if (is_numeric($size)) {
            $sizePixels = intval($size);
            $sizePixels = min($sizePixels, Configure::read('Settings.repo_size_hr'));
        } else {
            if (!in_array($size, $allowedSizes)) {
                $size = 'preview';
            }
            $sizePixels = Configure::read('Settings.repo_size_' . $size);
        }

        $settings = [
            'width' => 64,
            'height' => 64,
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];


        //----Format----------------------------------------------
        $allowedFormats = [
            'jpeg',
            'jpg',
            'png',
        ];

        $format = strtolower($format);

        if ($format == 'auto') {
            $format = 'jpg';
        }

        if (!in_array($format, $allowedFormats)) {
            $format = 'jpg';
        }

        if ($format == 'jpeg') {
            $format = 'jpg';
        }


        //----Quality----------------------------------------------
        $quality = strtolower($quality);

        if ($quality == 'auto') {
            $quality = 90;
        } elseif (!is_numeric($quality)) {
            $quality = 90;
        } else {
            $quality = intval($quality);
        }

        //----Generate Image----------------------------------------------
        if ($sizePixels) {
            $settings['width'] = $sizePixels;
        }
        if ($sizePixels) {
            $settings['height'] = $sizePixels;
        }
        if ($format) {
            $settings['format'] = $format;
        }
        if ($quality) {
            $settings['quality'] = $quality;
        }

        $imgRes = $this->Artifacts->getImageResource($settings);
        $this->response = $this->response->withType($imgRes->mime());
        $this->response = $this->response->withStringBody($imgRes->stream());

        return $this->response;
    }


}
