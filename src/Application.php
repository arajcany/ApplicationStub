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
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use Composer\Cache;
use Migrations\Migrations;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            try {
                $this->addPlugin('Bake');
            } catch (MissingPluginException $e) {
                // Do not halt if the plugin is missing
            }

            $this->addPlugin('TinyAuth');
            $this->addPlugin('Migrations');
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        Configure::write('DebugKit.safeTld', ['localhost.applicationstub', 'localhost',]);
        if (Configure::read('debug')) {
            $this->addPlugin(\DebugKit\Plugin::class);
        }

        $this->buildDatabase();
        $this->clearCache();

    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middlewareQueue)
    {
        $securityCsrf = boolval(Configure::read('Settings.security_csrf'));
        $applyCsrf = $this->applyCsrfToCurrentUrl();

        //is csrf required
        if ($securityCsrf) {
            if ($applyCsrf) {
                $csrfOptions = [];
                $csrf = new CsrfProtectionMiddleware($csrfOptions);
                $middlewareQueue->add($csrf);
            }
        }

        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(null, Configure::read('Error')))
            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime')
            ]))
            // Add routing middleware.
            // Routes collection cache enabled by default, to disable route caching
            // pass null as cacheConfig, example: `new RoutingMiddleware($this)`
            // you might want to disable this cache in case your routing is extremely simple
            ->add(new RoutingMiddleware($this, '_cake_routes_'));

        return $middlewareQueue;
    }


    /**
     * Auto build DB and perform Migrations
     *
     * @return bool
     */
    public function buildDatabase()
    {
        $migrate = false;
        $performMigrationFlag = false;

        //connect to the DB
        $Conn = ConnectionManager::get('default');

        //TODO build DB if not available
        //$result = $Conn->query("if not exists(select * from sys.databases where name = 'ApplicationStub') create database ApplicationStub");

        if (!$Conn) {
            return false;
        }

        $migrations = new Migrations();
        $status = $migrations->status();

        if (!empty($status)) {
            foreach ($status as $state) {
                if ($state['status'] == 'down') {
                    $performMigrationFlag = true;
                }
            }
        }

        if ($performMigrationFlag) {
            $migrate = $migrations->migrate();
        }

        return $migrate;
    }

    /**
     * Clear CACHE based on a signal file - useful after an update or install.
     */
    private function clearCache()
    {
        $clearCacheSignalFile = CACHE . "clear.txt";
        if (is_file($clearCacheSignalFile)) {
            try {
                \Cake\Cache\Cache::clear();
                unlink($clearCacheSignalFile);
            } catch (\Throwable $exception) {

            }
        }

        $clearCacheSignalFile = CACHE . "clear_all.txt";
        if (is_file($clearCacheSignalFile)) {
            try {
                \Cake\Cache\Cache::clearAll();
                unlink($clearCacheSignalFile);
            } catch (\Throwable $exception) {

            }
        }
    }

    /**
     * @return bool
     */
    private function applyCsrfToCurrentUrl()
    {
        $Request = new ServerRequest();
        $requestData = Router::parseRequest($Request);

        $currentUrl = Router::url(['controller' => $requestData['controller'], 'action' => $requestData['action']]);

        if (in_array($currentUrl, $this->getIgnoreCsrfUrls($Request))) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * @param ServerRequest $Request
     * @return array
     */
    private function getIgnoreCsrfUrls(ServerRequest $Request)
    {
        $ignore = Configure::read('Csrf.ignore');

        $ignoreCompiled = [];
        foreach ($ignore as $k => $v) {
            if (isset($v['controller']) && isset($v['action'])) {

                if (isset($v['requestType'])) {
                    $requestType = $v['requestType'];
                    if (!$Request->is($requestType)) {
                        continue;
                    }
                }

                $c = $v['controller'];
                $actions = $v['action'];

                if (is_string($actions)) {
                    $actions = [$actions];
                }

                foreach ($actions as $a) {
                    $ignoreCompiled[] = Router::url(['controller' => $c, 'action' => $a], false);
                }
            }
        }

        return $ignoreCompiled;
    }
}
