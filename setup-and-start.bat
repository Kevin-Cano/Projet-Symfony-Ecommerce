@echo off
echo Installation et demarrage de l'application...

echo Demarrage des conteneurs Docker...
docker compose up -d

echo Installation des dependances...
composer install

echo Reinitialisation de la base de donnees...
call reset-db.bat

echo Lancement du serveur Symfony...
start "Symfony Server" symfony server:start

echo Ouverture du navigateur...
timeout /t 5
start http://localhost:8000

echo Installation et demarrage termines avec succes!
echo L'application est accessible sur http://localhost:8000
pause 