::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
:: ResetPassword is a Windows batch script for resetting a User's Password
::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

@echo off

SET app=%0
SET lib=%~dp0

php "%lib%cake.php" ResetPassword

echo.

exit /B %ERRORLEVEL%
