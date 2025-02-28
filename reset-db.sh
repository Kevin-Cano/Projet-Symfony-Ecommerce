#!/bin/bash

# Supprimer la base de données
php bin/console doctrine:database:drop --force --if-exists

# Créer la base de données
php bin/console doctrine:database:create

# Créer le schéma
php bin/console doctrine:schema:create

# Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction

echo "Base de données réinitialisée avec succès!" 