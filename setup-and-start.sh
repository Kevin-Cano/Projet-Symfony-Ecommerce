#!/bin/bash

echo "Installation et démarrage de l'application..."

echo "Démarrage des conteneurs Docker..."
docker compose up -d

echo "Installation des dépendances..."
composer install

echo "Réinitialisation de la base de données..."
bash ./reset-db.sh

echo "Lancement du serveur Symfony..."
symfony server:start -d

php bin/console sass:build

echo "Attente du démarrage du serveur..."
sleep 5

echo "Ouverture du navigateur..."
if command -v xdg-open &> /dev/null; then
    xdg-open http://localhost:8000
elif command -v open &> /dev/null; then
    open http://localhost:8000
fi

echo "Installation et démarrage terminés avec succès!"
echo "L'application est accessible sur http://localhost:8000" 