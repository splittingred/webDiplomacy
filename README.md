Diplomacy is a popular turn based strategy game in which you battle to control Europe; to win you must be diplomatic 
and strategic. webDiplomacy lets you play Diplomacy online.

[![CircleCI](https://circleci.com/gh/splittingred/webDiplomacy/tree/production.svg?style=svg)](https://circleci.com/gh/splittingred/webDiplomacy/tree/production)


## Installation

README.txt - Installation information. This is legacy and hopefully will be vastly improved in the future.

Then run (you must have composer + yarn installed):

```
yarn install
composer install
```

### nginx setup

Run on PHP 7.3+. Add the nginx config:

```
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name webdiplomacy.net;

    charset utf-8;

    root /path/to/webDiplomacy;

    location / {
        index index.html index.htm index.php;
        if (!-e $request_filename) {
          rewrite ^/(.*)$ /index.php?q=$1 last;
        }
    }

    location ~ /\.ht {
      deny  all;
    }

    include /etc/nginx/php7.conf;
}
```

## Goals

This is a fork of https://github.com/kestasjk/webDiplomacy/, with vastly modernized code. The goal is to get
 webDiplomacy to PSR-4 standards and fully using [Twig](https://twig.symfony.com/) for templating, separating
 domain logic from rendering.
 
Doing so will eventually allow the more rapid expansion of features and scalability improvements.

This repository is therefore not stable and usable for a webdip server at this time.

### Other Links

http://webdiplomacy.net/ - The official webDiplomacy server, run on https://github.com/kestasjk/webDiplomacy/