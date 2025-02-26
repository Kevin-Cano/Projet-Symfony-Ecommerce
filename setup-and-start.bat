@echo off
setlocal ENABLEDELAYEDEXPANSION

cd /d "%~dp0"

echo Installation et demarrage de l'application...

echo Demarrage des conteneurs Docker...
docker compose up -d

echo Installation des dependances...
cmd /c "composer install --no-interaction"
if %ERRORLEVEL% NEQ 0 (
    echo Erreur lors de l'installation des dependances.
    exit /b
)
echo Installation terminee

echo Reinitialisation de la base de donnees...
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction

echo Base de donnees reinitialisee avec succes!

echo Lancement du serveur Symfony...
start "Symfony Server" symfony server:start --no-tls --port=8000
timeout /t 5

echo Ouverture du navigateur...
start http://localhost:8000

echo Installation et demarrage termines avec succes!
echo L'application est accessible sur http://localhost:8000

pause