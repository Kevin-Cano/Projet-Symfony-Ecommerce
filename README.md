```
docker compose up -d
```

```
composer install
```

```
symfony server:start
```

a mettre dans un script pour le start du projet
```
php bin/console doctrine:migrations:migrate
```
a mettre dans un script pour le start du projet
```
php bin/console doctrine:fixtures:load --no-interaction
```

```
php bin/console doctrine:schema:update --force
```

Voir toutes les montres avec leur stock :
GET http://localhost:8000/api/watches


Ajouter du stock à une montre :
POST http://localhost:8000/api/stock/add/{id}
Body: { "quantity": 5 }

Retirer du stock d'une montre :
POST http://localhost:8000/api/stock/remove/{id}
Body: { "quantity": 2 }

Définir un niveau de stock spécifique :
POST http://localhost:8000/api/stock/set/{id}
Body: { "quantity": 10 }


Voir les montres avec un stock bas (moins de 5 pièces) :
GET http://localhost:8000/api/stock/low-stock