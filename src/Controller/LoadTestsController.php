<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\LoadTestsUrlMakerComponent;
use App\Model\Table\ArtifactsTable;
use App\Model\Table\SettingsTable;
use arajcany\ToolBox\Utility\TextFormatter;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\I18n\Number;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;
use Intervention\Image\ImageManager;

/**
 * LoadTests Controller
 * @property LoadTestsUrlMakerComponent $LoadTestsUrlMaker
 * @property SettingsTable $Settings
 * @property ArtifactsTable $Artifacts
 *
 */
class LoadTestsController extends AppController
{
    public $Artifacts;

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('LoadTestsUrlMaker');
        $this->loadModel('Artifacts');
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
            $urlVariable = $this->LoadTestsUrlMaker->insertVariables($url);

            if (strpos($url, "?") !== false) {
                $qs = '';
            } else {
                $qs = "?r=" . substr(sha1(Security::randomBytes(1024)), 0, 8);
            }

            $finalUrls[$counter] = $urlVariable . $qs;
            $delayMatrix[$counter] = mt_rand(1, $timespan * 1000);
            $arrayKeys[] = $counter;
        }
        $finalUrls = array_values($finalUrls);
        asort($delayMatrix);
        $delayMatrix = array_combine($arrayKeys, $delayMatrix);

        $this->set('hits', $hits);
        $this->set('timespan', $timespan);
        $this->set('urlRoot', $urlRoot);
        $this->set('url', $url);
        $this->set('finalUrls', $finalUrls);
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
    public function repositoryPerformance($cycles = null, $sizeMb = null)
    {
        //safety limiting
        $cyclesMax = 500;
        $sizeMbMax = 10;
        $cycles = min($cycles, $cyclesMax);
        $sizeMb = min($sizeMb, $sizeMbMax);

        $cyclesMin = 10;
        $sizeMbMin = 0.1;
        $cycles = max($cycles, $cyclesMin);
        $sizeMb = max($sizeMb, $sizeMbMin);

        $this->set('cycles', $cycles);
        $this->set('sizeMb', $sizeMb);
        $this->set('localPerformance', 0);
        $this->set('uncPerformance', 0);


        if ($this->request->is('post')) {
            $cycles = $this->request->getData('cycles');
            $sizeMb = $this->request->getData('sizeMb');
            $this->set('cycles', $cycles);
            $this->set('sizeMb', $sizeMb);

            $cycleTicker = range(1, $cycles);

            $basePaths = [
                'appTmpPath' => TMP,
                'uncRepo' => TextFormatter::makeEndsWith($this->Settings->getSetting('repo_unc'), '\\'),
            ];
            $performance = [];
            foreach ($basePaths as $name => $path) {
                $time_total_w = 0;
                $time_total_r = 0;
                $byteCounter = 0;
                foreach ($cycleTicker as $cycle) {
                    $filename = $path . sha1(Security::randomBytes(1024)) . ".txt";
                    $sizeBytes = 1024 * 1024 * $sizeMb;
                    $data = Security::randomBytes($sizeBytes);

                    //write
                    $time_start_w = microtime(true);
                    file_put_contents($filename, $data);
                    $time_end_w = microtime(true);

                    //read
                    $time_start_r = microtime(true);
                    file_put_contents($filename, $data);
                    $time_end_r = microtime(true);

                    //delete
                    unlink($filename);

                    $time_total_w += ($time_end_w - $time_start_w);
                    $time_total_r += ($time_end_r - $time_start_r);

                    $byteCounter = $byteCounter + $sizeBytes;
                }
                $performance[$name] = [
                    'path' => $path,
                    'size_bytes' => $sizeBytes,
                    'cycles' => $cycles,
                    'total_time_write' => $time_total_w,
                    'total_time_read' => $time_total_r,
                    'size_bytes_total' => $byteCounter,
                    'write_speed_bytes_per_second' => $byteCounter / $time_total_w,
                    'write_speed_human' => Number::toReadableSize($byteCounter / $time_total_w) . "/sec",
                    'read_speed_bytes_per_second' => $byteCounter / $time_total_r,
                    'read_speed_human' => Number::toReadableSize($byteCounter / $time_total_r) . "/sec",
                ];
            }

            $this->set('performance', $performance);

        }
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

        $postData = $this->request->getData();
        $responseData['post'] = $postData;
        $responseData['method'] = $this->request->getMethod();

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
