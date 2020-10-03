# Tests

[Retour au sommaire](index.md)

Tout d'abord, faisons le point sur les différentes types de tests que nous implémenterons.

Pour cela, nous allons suivre la pyramide de test :
* **Test d'acceptation** : On valide les tests d'acceptation grâce à Gherkin.
* **Test unitaire** : Tester les cas d'utilisation (et donc seulement les `BusinessRules`.
* **Test d'intégration** : Tester l'interface, en simulant une requête HTTP, en bouchonnant les ports secondaires.
* **Test système** : Tester l'interface, toujours en simulant une requête HTTP, sans bouchonner les ports secondaires.

Lancer tous les tests :
```
make tests
```

Lancer seulement les tests unitaires :
```
make unit-tests
```

Lancer seulement les tests d'intégration :
```
make integration-tests
```

Lancer seulement les tests systèmes :
```
make system-tests
```
