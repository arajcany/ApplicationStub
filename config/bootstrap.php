<?php
ini_set('memory_limit', '2048M');

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use App\Model\Table\InternalOptionsTable;
use App\Model\Table\SettingsTable;
use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\ORM\Locator\TableLocator;
use Cake\Utility\Inflector;
use Cake\Utility\Security;

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 */
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    if (!is_file(CONFIG . 'app.php')) {
        $tmpConfig = file_get_contents(CONFIG . 'app.default.php');
        file_put_contents(CONFIG . 'app.php', $tmpConfig);
        exit("Please configure the DB credentials in \config\app.php" . "\n");
    }

    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

//replace __SALT__
if (Configure::read('Security.salt') == '__SALT__') {
    $newSalt = sha1(Security::randomBytes(1024)) . sha1(Security::randomBytes(1024));
    Configure::write('Security.salt', $newSalt);
    $appConfigFilepath = CONFIG . 'app.php';
    $appConfigContents = file_get_contents($appConfigFilepath);
    $appConfigContents = str_replace('__SALT__', $newSalt, $appConfigContents);
    file_put_contents($appConfigFilepath, $appConfigContents);
}

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */

//build a config_local file from default if missing
if (!is_file(CONFIG . 'config_local.php')) {
    $tmpConfig = file_get_contents(CONFIG . 'config_local.default.php');
    file_put_contents(CONFIG . 'config_local.php', $tmpConfig);
}
Configure::load('config_local', 'default', false);
Configure::load('config_stub');
Configure::load('config_seed');
Configure::load('config_cache');
Configure::load('config_logs_errors');
Configure::load('config_csrf', 'default');

require CONFIG . 'global_functions.php';

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Email::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Try to connect to the default DB. If not, exit with error message.
 */
if (!Cache::read('default_is_online', 'table_list')) {
    try {
        /**
         * @var Cake\Database\Connection $conn
         */
        $conn = ConnectionManager::get('default');
        $schema = $conn->getSchemaCollection();
        if ($schema) {
            Cache::write('default_is_online', true, 'table_list');
        }
    } catch (\PDOException $e) {
        $msg = __("Sorry, could not connect to the default DB.<br><strong>{0}</strong><br>Please check the DB configuration in \config\app.php", $e->getMessage());
        exit($msg);
    }
}

/**
 * @var SettingsTable $SettingsTable
 * @var InternalOptionsTable $InternalOptionsTable
 */

$tableLocator = new TableLocator();

//load Settings from DB
try {
    $tablesDefault = getConnectionTableList('default');
    if (isset($tablesDefault['settings'])) {
        $SettingsTable = $tableLocator->get('Settings');
        $SettingsTable->saveSettingsToConfigure();
    } else {
        $SettingsTable = false;
    }
} catch (\Exception $e) {
    $SettingsTable = false;
}

//load InternalOptions from DB
$tablesInternal = getConnectionTableList('internal');
if (isset($tablesInternal['internal_options'])) {
    $InternalOptionsTable = $tableLocator->get('InternalOptions');
    $InternalOptionsTable->saveOptionsToConfigure();
} else {
    $InternalOptionsTable = $tableLocator->get('InternalOptions');
    $InternalOptionsTable->buildInternalOptionsTable();
}

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
Type::build('time')
    ->useImmutable();
Type::build('date')
    ->useImmutable();
Type::build('datetime')
    ->useImmutable();
Type::build('timestamp')
    ->useImmutable();


function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}

//----------------------------------------------------------------------------------------------------------------------
/**
 * This function is purely to use Cache when getting a list of tables
 *
 * @param string $connectionName
 * @param bool $readFromCache
 * @return array|mixed|null
 */
function getConnectionTableList($connectionName = '', $readFromCache = true)
{
    if ($readFromCache) {
        $list = Cache::read($connectionName, 'table_list');
        if ($list) {
            return $list;
        }
    }

    $list = ConnectionManager::get($connectionName)->getSchemaCollection()->listTables();
    $list = array_flip($list);
    Cache::write($connectionName, $list, 'table_list');
    return $list;
}
