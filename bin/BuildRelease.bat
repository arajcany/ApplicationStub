::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: BuildReleases is a Windows batch script for building a Application Release
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0


cmd /C composer install --no-dev

echo.

php "%lib%cake.php" Releases build

echo.

cmd /C composer install

echo.

exit /B %ERRORLEVEL%
