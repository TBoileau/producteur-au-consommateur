# Installation

[Retour au sommaire](index.md)

## Récupérer les sources du projet
Pensez à `fork` le projet, pensez à lire le [guide de contribution](/CONTRIBUTING.md).
```
git clone https://github.com/<your-username>/<repo-name>
```

## Pré-requis
* PHP >= 7.4.4
* Extensions PHP :
    * ctype
    * iconv
    * json
    * xml
    * intl
* composer
* MySQL >= 8.0.0
* NodeJS >= 14.4.0
* npm >= 6.14.5

## Installer les dépendances
Dans un premier temps, positionnez vous dans le dossier du projet :
```
cd <repo-name>
```

Installez les dépendances de **composer** :
```
composer install
```

Ainsi que les dépendances de **npm** :
```
npm install
```

## Environnements
Pour faire fonctionner le projet sur votre machine, pensez à configurer les différentes environnements. Une documentation sur ce sujet est présent [ici](environnements.md).

## Initialiser les base de données
En commençant par l'environnement `dev`
```
make prepare-dev
```

Puis l'environnement `test`:
```
make preapre-test
```

## Lancer le serveur en local
Il est nécessaire d'avoir installé le [binaire de symfony](https://symfony.com/download).
```
symfony serve
```

## Gestion des ressources externes (css, js)
Compilez une seule fois les fichiers en environnement de développement :
```
npm run dev
```

Activez la compilation automatique :
```
npm run watch
```

Compilez les fichiers pour la production :
```
npm run build
```
