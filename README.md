# symfony-project

Firstly run this command to install the dependencies:
```composer install```

Then run this command to create the database:
```php bin/console doctrine:database:create```

Then run this command to create the tables:
```php bin/console doctrine:schema:update --force```

Then run this command to load the fixtures:
```php bin/console doctrine:fixtures:load```

Then run this command to start the server:
```symfony serve```