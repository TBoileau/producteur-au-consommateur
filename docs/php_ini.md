# php.ini

[Retour au sommaire](index.md)

```
opcache.preload_user=www-data
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.preload=/path/to/project/src/.preload.php
opcache.validate_timestamps=0
realpath_cache_size=4096K
realpath_cache_ttl=600
```
