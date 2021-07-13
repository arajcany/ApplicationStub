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
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Console;

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

use Cake\Cache\Cache;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class Installer
{
    static $defaultVendorName;
    static $defaultAppName;
    static $defaultAppDescription;
    static $validatedVendorName;
    static $validatedAppName;
    static $validatedAppDescription;

    /**
     * An array of directories to be made writable
     */
    const WRITABLE_DIRS = [
        'logs',
        'tmp',
        'tmp/cache',
        'tmp/cache/models',
        'tmp/cache/persistent',
        'tmp/cache/views',
        'tmp/sessions',
        'tmp/tests'
    ];

    /**
     * Does some routine installation tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @return void
     * @throws \Exception Exception raised by validator.
     */
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();

        $rootDir = dirname(dirname(__DIR__));

        static::createAppConfig($rootDir, $io);
        static::createWritableDirectories($rootDir, $io);
        static::updateNameAndDescription($rootDir, $io);

        // ask if the permissions should be changed
        if ($io->isInteractive()) {
            $validator = function ($arg) {
                if (in_array($arg, ['Y', 'y', 'N', 'n'])) {
                    return $arg;
                }
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
            $setFolderPermissions = $io->askAndValidate(
                '<info>Set Folder Permissions ? (Default to Y)</info> [<comment>Y,n</comment>]? ',
                $validator,
                10,
                'Y'
            );

            if (in_array($setFolderPermissions, ['Y', 'y'])) {
                static::setFolderPermissions($rootDir, $io);
            }
        } else {
            static::setFolderPermissions($rootDir, $io);
        }

        static::setSecuritySalt($rootDir, $io);

        $class = 'Cake\Codeception\Console\Installer';
        if (class_exists($class)) {
            $class::customizeCodeceptionBinary($event);
        }

        static::removeReferencesToApplicationStub($rootDir, $io);

        try {
            Cache::write('first_run', true, 'quick_burn');
        } catch (\Throwable $exception) {

        }
    }

    /**
     * Remove references to 'Application Stub'.
     *
     * @param string $rootDir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     * @throws Exception
     */
    public static function removeReferencesToApplicationStub($rootDir, $io)
    {
        $defaultVendorName = static::$defaultVendorName;
        $defaultAppName = static::$defaultAppName;
        $defaultAppDescription = static::$defaultAppDescription;
        $validatedVendorName = static::$defaultVendorName;
        $validatedAppName = static::$validatedAppName;
        $validatedAppDescription = static::$validatedAppDescription;

        //remove references in the .gitignore file that stop the committing of Version History files
        if ($io->isInteractive()) {
            $validator = function ($arg) {
                if (in_array($arg, ['Y', 'y', 'N', 'n'])) {
                    return $arg;
                }
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
            $question = "<info>It is recommended that you commit the Version Histories when building a Releases. Would you like to automatically commit Version Histories? (Default to N)<info> <comment>[Y,N]</comment>:";
            $commitVersionHistories = $io->askAndValidate(
                $question,
                $validator,
                4,
                'N'
            );

            if (in_array($commitVersionHistories, ['Y', 'y'])) {
                $files = [
                    $rootDir . "/.gitignore"
                ];
                $references = [
                    '/config/version.ini',
                    '/config/version.json',
                    '/config/version_history.json',
                    '/config/version_history_hash.txt',
                ];
                $lineEndings = ["\r\n", "\r", "\n", ""];
                foreach ($files as $file) {
                    $contentsOriginal = file_get_contents($file);
                    $contentsNew = $contentsOriginal;
                    foreach ($references as $reference) {
                        foreach ($lineEndings as $lineEnding) {
                            $referenceWithLineEnding = $reference . $lineEnding;
                            $contentsNew = str_replace($referenceWithLineEnding, "", $contentsNew);
                        }
                    }
                    if ($contentsOriginal != $contentsNew) {
                        $result = file_put_contents($file, $contentsNew);
                        if ($result) {
                            $io->write('The .gitignore file was updated.');
                        }
                    }
                }
            }
        }//.gitignore


        //----remove references to Application Stub------------------------------------------------
        $files = [
            $rootDir . '/src/Template/Element/navbar.ctp',
            $rootDir . '/src/Template/Pages/home.ctp',
            $rootDir . '/config/Migrations/20190702030303_SeedSettingsEmail.php',
        ];
        $referencesIn = [
            'Application Stub',
            'ApplicationStub',
            'application-stub',
            'application_stub',
        ];
        $referencesOut = [
            ucwords(Inflector::humanize((Inflector::underscore($validatedAppName)))),
            Inflector::humanize($validatedAppName),
            Inflector::dasherize($validatedAppName),
            Inflector::underscore($validatedAppName),
        ];
        foreach ($files as $file) {
            $contentsOriginal = file_get_contents($file);
            $contentsNew = $contentsOriginal;
            $contentsNew = str_replace($referencesIn, $referencesOut, $contentsNew);
            if ($contentsOriginal != $contentsNew) {
                $result = file_put_contents($file, $contentsNew);
                if ($result) {
                    $io->write("The file `{$file}` was updated.");
                }
            }
        }
        //---------------------------------------------------------------------------------------


        //----new README.md----------------------------------------------------------------------
        $appName = ucwords(Inflector::humanize((Inflector::underscore($validatedAppName))));
        $readmeFile = $rootDir . "/README.md";
        $readmeContents = "# {$appName}\r\n{$validatedAppDescription} \r\n\r\nFor more information refer to:  \r\nhttps://github.com/{$validatedVendorName}/{$validatedAppName}\r\n";
        $result = file_put_contents($readmeFile, $readmeContents);
        if ($result) {
            $io->write("The README.md file was updated.");
        }
        //---------------------------------------------------------------------------------------

    }

    /**
     * Update the Name and Description of this Application
     *
     * @param string $rootDir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     *
     * @return bool|void
     * @throws Exception
     */
    public static function updateNameAndDescription($rootDir, $io)
    {
        if (!$io->isInteractive()) {
            return true;
        }

        $composerJsonFile = $rootDir . "/composer.json";
        $composerJson = file_get_contents($composerJsonFile);
        $composerJson = json_decode($composerJson, JSON_OBJECT_AS_ARRAY);
        $composerJsonOriginal = $composerJson;

        $defaultVendorName = explode("/", $composerJson['name'])[0];
        $defaultAppName = explode("/", $composerJson['name'])[1];
        $defaultAppDescription = $composerJson['description'];

        static::$defaultVendorName = $defaultVendorName;
        static::$defaultAppName = $defaultAppName;
        static::$defaultAppDescription = $defaultAppDescription;

        //VendorName
        $validator = function ($arg) use ($defaultVendorName) {
            if ($arg == "Y" || $arg == "y") {
                return $defaultVendorName;
            } elseif (strlen($arg) > 0) {
                return Text::slug(strtolower($arg));
            }

            throw new Exception('Please supply your Vendor Name.');
        };

        $validatedVendorName = $io->askAndValidate(
            "<info>What is your Vendor Name? </info><comment>[Defaults to '{$defaultVendorName}']</comment>: ",
            $validator,
            5,
            'Y'
        );

        //AppName
        $validator = function ($arg) use ($defaultAppName) {
            if ($arg == "Y" || $arg == "y") {
                return $defaultAppName;
            } elseif (strlen($arg) > 0) {
                return Text::slug(strtolower($arg));
            }

            throw new Exception('Please supply a name for this application.');
        };

        $validatedAppName = $io->askAndValidate(
            "<info>Provide a Name for this Application? </info><comment>[Defaults to '{$defaultAppName}']</comment>: ",
            $validator,
            5,
            'Y'
        );

        //AppDescription
        $validator = function ($arg) use ($defaultAppDescription) {
            if ($arg == "Y" || $arg == "y") {
                return $defaultAppDescription;
            } elseif (strlen($arg) > 0) {
                return $arg;
            }

            throw new Exception('Please supply a brief description of this application.');
        };

        $validatedAppDescription = $io->askAndValidate(
            "<info>Provide a Description for this Application? </info><comment>[Defaults to '{$defaultAppDescription}']</comment>: ",
            $validator,
            5,
            'Y'
        );

        static::$validatedVendorName = $validatedVendorName;
        static::$validatedAppName = $validatedAppName;
        static::$validatedAppDescription = $validatedAppDescription;

        $io->write("Vendor: " . $validatedVendorName);
        $io->write("App Name: " . $validatedAppName);
        $io->write("App Desc: " . $validatedAppDescription);

        $composerJson['name'] = "{$validatedVendorName}/{$validatedAppName}";
        $composerJson['description'] = $validatedAppDescription;

        //save only if there is a difference
        if (json_encode($composerJsonOriginal) != json_encode($composerJson)) {
            $composerJson = json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
            $result = file_put_contents($composerJsonFile, $composerJson);
        }

        $appConfig = $rootDir . '/config/app.php';
        static::updateValueInFile($appConfig, "__APP_NAME__", Inflector::camelize(Inflector::variable($validatedAppName)), $io);
        static::updateValueInFile($appConfig, "__APP_DESCRIPTION__", $validatedAppDescription, $io);

        return;
    }

    /**
     * Create the config/app.php file if it does not exist.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createAppConfig($dir, $io)
    {
        $appConfig = $dir . '/config/app.php';
        $defaultConfig = $dir . '/config/app.default.php';
        if (!file_exists($appConfig)) {
            copy($defaultConfig, $appConfig);
            $io->write('Created `config/app.php` file');
        }
    }

    /**
     * Create the `logs` and `tmp` directories.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createWritableDirectories($dir, $io)
    {
        foreach (static::WRITABLE_DIRS as $path) {
            $path = $dir . '/' . $path;
            if (!file_exists($path)) {
                mkdir($path);
                $io->write('Created `' . $path . '` directory');
            }
        }
    }

    /**
     * Set globally writable permissions on the "tmp" and "logs" directory.
     *
     * This is not the most secure default, but it gets people up and running quickly.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setFolderPermissions($dir, $io)
    {
        // Change the permissions on a path and output the results.
        $changePerms = function ($path) use ($io) {
            $currentPerms = fileperms($path) & 0777;
            $worldWritable = $currentPerms | 0007;
            if ($worldWritable == $currentPerms) {
                return;
            }

            $res = chmod($path, $worldWritable);
            if ($res) {
                $io->write('Permissions set on ' . $path);
            } else {
                $io->write('Failed to set permissions on ' . $path);
            }
        };

        $walker = function ($dir) use (&$walker, $changePerms) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;

                if (!is_dir($path)) {
                    continue;
                }

                $changePerms($path);
                $walker($path);
            }
        };

        $walker($dir . '/tmp');
        $changePerms($dir . '/tmp');
        $changePerms($dir . '/logs');
    }

    /**
     * Set the security.salt value in the application's config file.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setSecuritySalt($dir, $io)
    {
        $newKey = hash('sha256', Security::randomBytes(64));
        static::setSecuritySaltInFile($dir, $io, $newKey, 'app.php');
    }

    /**
     * Set the security.salt value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $newKey key to set in the file
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setSecuritySaltInFile($dir, $io, $newKey, $file)
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);

        $content = str_replace('__SALT__', $newKey, $content, $count);

        if ($count == 0) {
            $io->write('No Security.salt placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Security.salt value in config/' . $file);

            return;
        }
        $io->write('Unable to update Security.salt value.');
    }

    /**
     * Set the APP_NAME value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $appName app name to set in the file
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setAppNameInFile($dir, $io, $appName, $file)
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);
        $content = str_replace('__APP_NAME__', $appName, $content, $count);

        if ($count == 0) {
            $io->write('No __APP_NAME__ placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated __APP_NAME__ value in config/' . $file);

            return;
        }
        $io->write('Unable to update __APP_NAME__ value.');
    }

    /**
     * Update the given value in a given file
     *
     * @param string $file A path to a file relative to the application's root
     * @param string $oldValue
     * @param string $newValue
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function updateValueInFile($file, $oldValue, $newValue, $io)
    {
        $filename = pathinfo($file, PATHINFO_BASENAME);

        if (!is_file($file)) {
            $io->write("Fail: Could not find the file {$filename}.");

            return;
        }

        $content = file_get_contents($file);
        $content = str_replace($oldValue, $newValue, $content, $count);

        if ($count == 0) {
            $io->write("Warning: The value '{$oldValue}' was not found in {$filename}.");

            return;
        }

        $result = file_put_contents($file, $content);
        if ($result) {
            $io->write("Success: The value '{$oldValue}' was replaced with '{$newValue}' in {$filename}.");

            return;
        }

        $io->write("Fail: Could not write to file {$filename}.");
    }
}
