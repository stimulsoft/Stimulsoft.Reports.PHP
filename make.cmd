@echo off
cls
echo.

set build_version=%1
set build_date=%date:~-4%.%date:~3,2%.%date:~0,2%
set build_pack=%2
cd D:\Builder

if "%build_version%"=="" ( 
	echo Please specify the release version!
	echo.
	echo Usage: make version [product]
	goto done
)

echo Release %build_version% from %build_date%

:clean
echo.
echo -- Clean
rd make /s /q
md make\Build_%build_version%_%build_date%
cd make
echo OK

if not "%build_pack%"=="" goto %build_pack%

:java
echo.
echo -- Java
xcopy ..\files\java\components\* JAVA_%build_date%\Components\* /e /q
xcopy ..\files\java\external\* JAVA_%build_date%\Components\External\* /e /q
xcopy ..\files\js\reports\components\Designers\* JAVA_%build_date%\Designers\* /e /q
..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%\JAVA_%build_date%.zip" "JAVA_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"
echo | set /p="%build_version%" > Build_%build_version%_%build_date%\JAVA_%build_date%.txt
if not "%build_pack%"=="" goto done

:php
echo.
echo -- PHP
xcopy ..\files\php\* PHP_%build_date%\JS\* /e /q
xcopy ..\files\js\dbs\components\Css\* PHP_%build_date%\JS\css\* /e /q
xcopy ..\files\js\dbs\components\Scripts\* PHP_%build_date%\JS\scripts\* /e /q
xcopy ..\files\js\dbs\components\Designers\* PHP_%build_date%\Designers\* /e /q
..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%\PHP_%build_date%.zip" "PHP_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"
echo | set /p="%build_version%" > Build_%build_version%_%build_date%\PHP_%build_date%.txt
if not "%build_pack%"=="" goto done

:js
echo.
echo -- JS
xcopy ..\files\js\reports\components\* JS_%build_date%\* /e /q
..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%\JS_%build_date%.zip" "JS_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"
echo | set /p="%build_version%" > Build_%build_version%_%build_date%\JS_%build_date%.txt
if not "%build_pack%"=="" goto done

:dbsjs
echo.
echo -- DBS-JS
xcopy ..\files\js\dbs\components\* DBS_JS_%build_date%\* /e /q
..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%\DBS_JS_%build_date%.zip" "DBS_JS_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"
echo | set /p="%build_version%" > Build_%build_version%_%build_date%\DBS_JS_%build_date%.txt
if not "%build_pack%"=="" goto done

:sources
echo.
echo -- Sources
xcopy ..\files\java\sources\* Sources_%build_date%\Java\* /e /q
xcopy ..\files\js\reports\src\* Sources_%build_date%\JS\* /e /q
xcopy ..\files\js\dbs\src\* Sources_%build_date%\DBS-JS\* /e /q
..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%\Sources_%build_date%.zip" "Sources_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"
if not "%build_pack%"=="" goto done

:build
REM echo.
REM echo -- Build
REM ..\7z a -tzip -mx=7 -mmt8 -bsp2 "Build_%build_version%_%build_date%.zip" "Build_%build_version%_%build_date%" | findstr /i "Build_%build_version%_%build_date% everything warning error"

:done
cd D:\Builder
echo.
set /p key="Press Enter to exit "
