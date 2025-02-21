@echo off
echo Installation et demarrage de l'application...

echo Demarrage des conteneurs Docker...
docker compose up -d

echo Installation des dependances...
composer install && echo "Installation terminee" && echo Reinitialisation de la base de donnees... && cmd /c call reset-db.bat

echo Lancement du serveur Symfony...
start "Symfony Server" symfony server:start --no-tls --port=8000

echo Ouverture du navigateur...
timeout /t 5
start http://localhost:8000

echo Installation et demarrage termines avec succes!
echo L'application est accessible sur http://localhost:8000
pause 