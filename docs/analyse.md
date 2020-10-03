# Analyse

[Retour au sommaire](index.md)

Respect du [PSR-1](https://www.php-fig.org/psr/psr-1) et du [PSR-12](https://www.php-fig.org/psr/psr-12).

Vous pouvez aussi utiliser `PHP Code Sniffer` pour corriger certaines erreurs automatiquement :
```
vendor/bin/phpcbf
```

Et analyser votre projet avec :
```
vendor/bin/phpcs
```

Lancer PHPStan pour analyser le code dans le dossier `src` du projet :
```
vendor/bin/phpstan analyse -c phpstan.neon src --level 7 --no-progress
```

Lancer PHPStan pour analyser le code dans le dossier `tests` du projet :
```
vendor/bin/phpstan analyse -c phpstan-tests.neon tests --level 7 --no-progress
```

Lancer PHPStan pour analyser Doctrine le dossier `src` du projet :
```
vendor/bin/phpstan analyse -c phpstan-doctrine.neon src --level 7 --no-progress
```

Analyser le code avec PHPMetrics :
```
vendor/bin/phpmetrics ./src
```

Lancer l'ensemble des analyses :
```
make analyze
```