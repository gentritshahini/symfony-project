# symfony-project

Firstly run this command to install the dependencies:
```composer install```

on the env file, set the database connection string to your database. For example:
```DATABASE_URL="mysql://root@localhost:3306/symfonydb?serverVersion=5.7"```

Then run this command to create the database:
```php bin/console doctrine:database:create```

Then run this command to create the tables:
```php bin/console doctrine:schema:update --force```

Then run this command to load the fixtures:
```php bin/console doctrine:fixtures:load```

Then run this command to start the server:
```symfony serve```