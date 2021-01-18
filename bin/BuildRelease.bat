::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: BuildReleases is a Windows batch script for building a Application Release
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0

php "%lib%cake.php" Releases debug_off

cmd /C composer install --no-dev --no-scripts
cmd /C composer dump-autoload

echo.

php "%lib%cake.php" Releases build

echo.

cmd /C composer install --no-scripts
cmd /C composer dump-autoload

php "%lib%cake.php" Releases debug_on

echo.

exit /B %ERRORLEVEL%
