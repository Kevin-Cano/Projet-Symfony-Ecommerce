@echo off

php bin/console doctrine:database:drop --force --if-exists

php bin/console doctrine:database:create

php bin/console doctrine:schema:create

php bin/console doctrine:fixtures:load --no-interaction

echo "Base de donnees reinitialisee avec succes!"
pause 