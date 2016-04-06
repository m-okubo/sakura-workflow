rem Set autocrlf to false based on PSR-2.
cd /d %~dp0
cd ..\.git
git config core.autocrlf false
