# Installation Instructions

## Drive

https://drive.google.com/drive/u/0/folders/1Ary2UftZ4BBb6VVavVbGXZr_QENKDXlh

## Install Git

```
$ su root
$ apt install git
```

## Install Postgres

```
$ sudo apt install postgresql
$ service postgresql start
$ service postgresql status

$ su root
$ sudo -i -u postgres
$ psql

$$ CREATE ROLE spring_financial WITH LOGIN PASSWORD ‘spring_financial’;
$$ CREATE DATABASE spring_leaderboards;
$$ GRANT ALL PRIVILEGES ON DATABASE spring_leaderboards TO spring_financial;
```

## Start the App

```
$ git clone https://github.com/eduardocgarza/app-spring-leaderboards-server
$ cd app-spring-leaderboards-server
app-spring-leaderboards-server $ composer install # composer install installs the dependencies for the project 
app-spring-leaderboards-server $ php artisan serve # artisan is the CLI for Laravel
```

## Install PHP, Composer, Laravel

```
$ su root
$ apt install php

```

## Composer

```
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```

