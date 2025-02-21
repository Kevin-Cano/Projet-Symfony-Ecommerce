#!/bin/bash

php bin/console doctrine:database:drop --force

php bin/console doctrine:database:create

php bin/console doctrine:schema:create

php bin/console doctrine:fixtures:load -n

echo "Base de données réinitialisée avec succès!" 