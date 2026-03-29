@echo off
set PHP_BIN=C:\Users\Eyasu\Desktop\tools\php\php.exe
set COMPOSER_BIN=C:\Users\Eyasu\Desktop\tools\composer.phar
echo Starting Composer create-project...
"%PHP_BIN%" "%COMPOSER_BIN%" create-project laravel/laravel laravel_temp
if %ERRORLEVEL% neq 0 (
    echo Composer failed with error %ERRORLEVEL%
    exit /b %ERRORLEVEL%
)
echo Moving files...
xcopy /E /Q /H /Y laravel_temp\* .
rmdir /S /Q laravel_temp
echo Done!
