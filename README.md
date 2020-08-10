# shopping-cart
Sample shopping cart application with API

Installation process
--------------------

Invoke in shell:

```
$ git clone git@github.com:mkurosz/shopping-cart.git
```

Copy .env.dist file to .env:

```
$ cp .env.dist .env
```

edit .env file.

In this file you'll find following variable:

```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

Fill this line by your db connection data.

Install dependencies:

```
$ composer install
```

Create database structure:

```
$ php bin/console doctrine:schema:update --force
```

Load fixtures:

```
$ php bin/console doctrine:fixtures:load
```

Running tests
--------------------

Drop previous test db:

```
$ php bin/console doctrine:schema:drop --env=test --force
```

Create again test db:

```
$ php bin/console doctrine:schema:create --env=test
```

Create database structure:

```
$ php bin/console doctrine:schema:update --force
```

Load fixtures:

```
$ php bin/console doctrine:fixtures:load --env=test
```

Run tests:

```
$ php bin/phpunit
```

