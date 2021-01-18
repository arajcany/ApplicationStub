::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: BuildReleases is a Windows batch script for building a Application Release
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0


cmd /C composer install --no-dev --no-scripts
cmd /C composer dump-autoload

echo.

php "%lib%cake.php" Releases debug_off
php "%lib%cake.php" Releases build
php "%lib%cake.php" Releases debug_on

echo.

cmd /C composer install --no-scripts
cmd /C composer dump-autoload

echo.

exit /B %ERRORLEVEL%
