# Environnements

[Retour au sommaire](index.md)

Il existe plusieurs environnements différents :
* `dev`: environnement de développement
* `test`: environnement de test
* `prod`: environnement de production

Pour chaque environnement, il sera nécessaire de créer un fichier contenant les variables d'environnement.

## Environnement de développement
Il n'est pas nécessaire de créer un fichier, si c'est le cas, il doit se nommer `.env.dev.local`.

Par défaut, aucun service tier (mail, base de données, ...) n'est utilisé. 

Par exemple pour l'accès à la base de données, il suffit de créer un port secondaire généralement appelé `InMemoryRepository`.

## Environnement de test
Il est indispensable de créer le fichier `.env.test.local` pour assurer le bon fonctionnement des tests, vous pouvez vous baser sur cet exemple :
```dotenv
# Nécessaire si vous souhaitez faire fonctionner les tests systèmes
DATABASE_URL=mysql://root:password@127.0.0.1:3306/productauconsommateur
```

## Environnement de production
Vous avez 2 possibilité, créer le fichier `.env.prod.local`ou ajouter vos variables d'environnement dans la configuration de votre hôte virtuel :
```dotenv
DATABASE_URL=mysql://root:password@127.0.0.1:3306/productauconsommateur
```

N'oubliez pas de configurer les autres variables d'environnement si besoin, comme `MAILER_DSN`.
