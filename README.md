# shopping-cart
Sample shopping cart application with an API

Installation process
--------------------

1. invoke in shell:

```
$ git clone git@github.com:mkurosz/shopping-cart.git
```

2. copy .env.dist file to .env:

```
$ cp .env.dist .env
```

3. edit .env file.

In this file you'll find following variable:

```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

Fill this line by your db connection data.

4. install dependencies:

```
$ composer install
```

5. create database structure:

```
$ php bin/console doctrine:schema:update --force
```

6. load fixtures:

```
$ php bin/console doctrine:fixtures:load
```
