::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: BuildReleases is a Windows batch script for building a Application Release
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0

php "%lib%cake.php" Releases debug_off

cmd /C composer install --no-dev --no-scripts --ignore-platform-reqs
cmd /C composer dump-autoload --ignore-platform-reqs

echo.

php "%lib%cake.php" Releases build

echo.

cmd /C composer install --no-scripts --ignore-platform-reqs
cmd /C composer dump-autoload --ignore-platform-reqs

php "%lib%cake.php" Releases debug_on

echo.

exit /B %ERRORLEVEL%
