<?php

namespace App\Log\Engine;

use App\Utility\Feedback\DebugCapture;
use Cake\Core\Configure;
use Cake\Http\Session;
use Cake\I18n\FrozenTime;
use Cake\Log\Engine\BaseLog;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Exception;

/**
 * Auditor class for logging events into the DB
 * There are 3 tables that are used to hold Auditing Information
 * 1) Audits Table
 * 2) TrackLogins Table
 * 3) TrackHits Table
 *
 * Try to use this Class (as opposed to the Models) to write Auditable events.
 * This is because this Class auto detects the user and automatically logs the
 * Event against the User.
 *
 *
 * @property \App\Model\Table\AuditsTable $Audits
 * @property \App\Model\Table\TrackLoginsTable $TrackLogins
 * @property \App\Model\Table\TrackHitsTable $TrackHits
 * @property Session $Session
 */
class Auditor extends BaseLog
{
    public $Audits;
    public $TrackLogins;
    public $TrackHits;
    private $Session;

    /**
     * Auditor constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->Audits = TableRegistry::getTableLocator()->get('Audits');
        $this->TrackLogins = TableRegistry::getTableLocator()->get('TrackLogins');
        $this->TrackHits = TableRegistry::getTableLocator()->get('TrackHits');

        $this->Session = Router::getRequest()->getSession();
    }

    /**
     * Mandatory implementation
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logEmergency($message = null, $context = 'audit')
    {
        $this->writeLog('emergency', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logAlert($message = null, $context = 'audit')
    {
        $this->writeLog('alert', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logCritical($message = null, $context = 'audit')
    {
        $this->writeLog('critical', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logError($message = null, $context = 'audit')
    {
        $this->writeLog('error', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logWarning($message = null, $context = 'audit')
    {
        $this->writeLog('warning', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logNotice($message = null, $context = 'audit')
    {
        $this->writeLog('notice', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logInfo($message = null, $context = 'audit')
    {
        $this->writeLog('info', $message, $context);
    }

    /**
     * Convenience method
     *
     * @param null $message
     * @param string $context
     */
    public function logDebug($message = null, $context = 'audit')
    {
        $this->writeLog('debug', $message, $context);
    }

    /**
     * Main method of writing to the Audits table.
     * Use wrapper methods for more convenient logging
     *
     * @param null $context
     * @param null $message
     * @param null $level
     * @param null $expiration
     * @param int $user_link
     * @param null $url
     * @return bool
     */
    public function writeLog($level = null, $message = null, $context = null, $expiration = null, $user_link = 0, $url = null)
    {
        $inputDefault = $this->getDefaultData();

        //clean up input data
        $inputData = [];

        if ($level) {
            $inputData['level'] = trim($level);
        }

        if ($message) {
            if (!is_string($message)) {
                $inputData['message'] = DebugCapture::captureDump($message);
            } else {
                $inputData['message'] = $message;
            }
        }

        if ($context) {
            if (is_array($context)) {
                if (isset($context['scope'])) {
                    $context = implode(', ', $context['scope']);
                } elseif (count($context) !== count($context, COUNT_RECURSIVE)) {
                    $context = DebugCapture::captureDump($context);
                } else {
                    $context = implode(', ', $context);
                }
            }

            $inputData['context'] = trim($context);
        }

        if ($expiration) {
            $expiration = new FrozenTime($expiration);
            $inputData['expiration'] = $expiration;
        }

        if ($user_link) {
            $inputData['user_link'] = $user_link;
        }

        if ($url) {
            $inputData['url'] = trim($url);
        }

        //merge the data
        $inputData = array_merge($inputDefault, $inputData);

        //save the data
        $audit = $this->Audits->newEntity();

        $audit = $this->Audits->patchEntity($audit, $inputData);
        if ($this->Audits->save($audit)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Common default data.
     *
     * @return array
     */
    private function getDefaultData()
    {
        //no Session or User in CLI
        if (!is_cli()) {
            //web
            if ($this->Session) {
                $defaultUserId = $this->Session->read('Auth.User.id');
                $defaultUsername = $this->Session->read('Auth.User.username');
            } else {
                $defaultUserId = 0;
                $defaultUsername = '';
            }
            $defaultContext = 'web_application';
            $defaultUrl = Router::url(null, true);
        } else {
            //cli
            $defaultUserId = 0;
            $defaultUsername = '';
            $defaultContext = 'cli_application';
            $defaultUrl = ''; //TODO get the cmd that was called
        }

        try {
            $expiration = new FrozenTime('+ ' . Configure::read('Settings.audit_purge') . ' months');
        } catch (Exception $e) {
            $expiration = new FrozenTime('+ 1 months');
        }

        $inputDefault = [
            'level' => 'info',
            'message' => '',
            'context' => $defaultContext,
            'expiration' => $expiration,
            'user_link' => $defaultUserId,
            'username' => $defaultUsername,
            'url' => $defaultUrl,
        ];

        return $inputDefault;
    }

    /**
     * Track a Users login. Minor impact on performance.
     *
     * @param array $user
     * @return \App\Model\Entity\TrackLogin|bool
     */
    public function trackLogin(array $user)
    {
        $trackLogin = $this->TrackLogins->newEntity();
        $trackLogin->username = $user['username'];

        $tryCounter = 0;
        $tryLimit = 5;
        $isSaved = false;
        while ($tryCounter < $tryLimit & !$isSaved) {
            try {
                $isSaved = $this->TrackLogins->save($trackLogin);
            } catch (\Throwable $exception) {
                //do nothing, non critical error
            }
            usleep(30);

            $tryCounter++;
        }
    }

    /**
     * Track a Hit to the application. Can have a performance impact.
     *
     * @param \Psr\Http\Message\UriInterface $passed
     * @param array $otherData
     * @return \App\Model\Entity\TrackHit|false
     */
    public function trackHit(\Psr\Http\Message\UriInterface $passed, $otherData = [])
    {
        $url = $passed->__toString();

        $scheme = $passed->getScheme();
        $host = $passed->getHost();
        $port = $passed->getPort();
        $path = $passed->getPath();
        $query = $passed->getQuery();

        $headers = getallheaders();

        if (isset($headers['Accept'])) {
            unset($headers['Accept']);
        }

        if (isset($headers['Cookie'])) {
            unset($headers['Cookie']);
        }

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $http_client_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $http_client_ip = '';
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $http_x_forwarded_for = '';
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        } else {
            $remote_addr = '';
        }

        $appUserData = $this->getDefaultData();

        $data = [
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'path' => $path,
            'query' => $query,
            'HTTP_CLIENT_IP' => $http_client_ip,
            'HTTP_X_FORWARDED_FOR' => $http_x_forwarded_for,
            'REMOTE_ADDR' => $remote_addr,
            'headers' => $headers,
            'app_user_id' => $appUserData['user_link'],
            'app_username' => $appUserData['username'],
        ];
        $data = array_merge($data, $otherData);

        $hit = $this->TrackHits->newEntity();
        $hit->url = $url;
        $hit->data = $data;
        $hit->scheme = substr($scheme, 0, 10);
        $hit->host = substr($host, 0, 255);
        $hit->port = substr($port, 0, 10);
        $hit->path = substr($path, 0, 255);
        $hit->query = substr($query, 0, 255);
        $hit->app_execution_time = round($data['app_execution_time'], 10);

        $tryCounter = 0;
        $tryLimit = 5;
        $isSaved = false;
        while ($tryCounter < $tryLimit & !$isSaved) {
            try {
                $isSaved = $this->TrackHits->save($hit);
            } catch (\Throwable $exception) {
                //do nothing, non critical error
            }
            usleep(20);

            $tryCounter++;
        }

        return $isSaved;
    }


}
