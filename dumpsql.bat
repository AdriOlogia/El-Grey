@echo off
For /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c-%%b-%%a)
For /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)

IF EXIST "c:\wamp64\bin\mysql\mysql5.7.26\bin\mysqldump.exe" (
	c:\wamp64\bin\mysql\mysql5.7.26\bin\mysqldump.exe -u root -p --skip-lock-tables --skip-add-locks --dump-date --extended-insert --complete-insert --routines test_ombutech > base\dump\ombutech-%mydate%_%mytime%.sql
)
IF EXIST "c:\wamp\bin\mysql\mysql5.7.26\bin\mysqldump.exe" (
	c:\wamp\bin\mysql\mysql5.7.26\bin\mysqldump.exe -u root -p --skip-lock-tables --skip-add-locks --dump-date --extended-insert --complete-insert --routines test_ombutech > base\dump\ombutech-%mydate%_%mytime%.sql
)