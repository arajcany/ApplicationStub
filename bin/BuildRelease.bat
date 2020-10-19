::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: ResetPassword is a Windows batch script for invoking CakePHP shell commands
::
:: CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
:: Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
::
:: Licensed under The MIT License
:: Redistributions of files must retain the above copyright notice.
::
:: @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
:: @link          https://cakephp.org CakePHP(tm) Project
:: @since         2.0.0
:: @license       https://opensource.org/licenses/mit-license.php MIT License
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0


REM cmd /C composer -V

echo.

php "%lib%cake.php" Releases build

echo.

REM cmd /C composer -V

echo.

exit /B %ERRORLEVEL%
