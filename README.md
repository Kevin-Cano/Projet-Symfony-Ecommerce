# Comment run notre projet LuxWatch

## Les prérequis

### 1. Windows

- Docker desktop, que vous pourrez télécharger sur leur site [ici](https://www.docker.com/products/docker-desktop/).

- PHP, que vous pourrez également télécharger sur leur site [ici](https://www.php.net/downloads.php).

- Symfony, que vous trouverez [ici](https://symfony.com/doc/current/setup.html).

### 2. Linux

- Docker desktop, que vous pourrez télécharger sur leur site [ici](https://docs.docker.com/desktop/setup/install/linux/).

- PHP, que vous pourrez également télécharger sur leur site [ici](https://www.php.net/manual/fr/install.unix.php).

- Symfony, que vous trouverez [ici](https://symfony.com/doc/current/setup.html). (Identique a l'installation sous windows)  
  
N'oubliez pas de lire correctement les documentations afin de tout installer sans encombre.

Pensez également a créer ces 2 fichiers, ``.env.dev`` dans lequel vous devrez y mettre ceci ``APP_SECRET=4d68cecbcc634ee27ca944ad8fb44877`` et ``.env.local`` dans lequel vous copierez le contenu du fichier ``.env.example`` en ajoutant les informations nécessaires. 

Maintenant que nos prérequis sont installé, il n'y a plus qu'a démarrer notre site !

## Lancement du projet

### 1. Windows

Dans un premier temps, il vous faut lancer votre Docker desktop via le logiciel installé au préalable.  
Puis, dans un second temps, lancer un Powershell ou une invite de commande. Déplacez vous dans le dossier contenant le projet en faisant ``cd \chemin\vers\le\projet\Projet-Symfony-Ecommerce\``

Puis taper cette commande :

```powershell
PS C: ...\...\Projet-Symfony-Ecommerce> .\setup-and-start.bat
```

### 2. Linux

Sous linux, le lancement de Docker est un peu différent.  
Ouvrez directement une invite de commande et taper cette commande :
```bash
sudo systemctl start docker
```

Déplacez vous dans le bon dossier ``cd ...\...\Projet-Symfony-Ecommerce\``

Puis taper cette commande : 

```bash
.\setup-and-start.sh
```
<div style="text-align: right;">

## Crédit
### Maxime CHORT
### Kevin CANO
### Brendan VISINE
</div>
