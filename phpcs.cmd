@echo off
%~dp0vendor\bin\phpcs.bat --warning-severity=1 --ignore-annotations %*
: %~dp0vendor\bin\phpcs.bat --standard=./phpcs.xml --warning-severity=1 --ignore-annotations --report-full --extensions=php,html,inc --no-colors --ignore=*Test.php %*
