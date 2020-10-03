# Apache

[Retour au sommaire](index.md)

Désactiver le mode **php7.4** :
```
sudo a2dismod php7.4 
```

Désactiver le mode **mpm_prefork**:
```
sudo a2dismod mpm_prefork
```

Activer les modes **mpm_event**, **proxy_fcgi** & **setenvif** :
```
sudo a2enmod mpm_event proxy_fcgi setenvif
```

Installer **PHP-FPM**:
```
sudo apt install php7.4-fpm 
sudo systemctl start php7.4-fpm
sudo systemctl enable php7.4-fpm
```

Activer le mode **PHP-FPM**:
```
sudo a2enconf php7.4-fpm
```

Activer le mode **http2**:
```
sudo a2enmod http2
```

Redémarrer apache:
```
sudo systemctl restart apache2
```
