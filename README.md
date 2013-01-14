This package implements the unique and exists [Laravel 4](http://laravel.com/) validation rules for [MongoDB](http://www.mongodb.org/).

Installation
============

Add `navruzm/mongo-validation` as a requirement to composer.json:

```json
{
    "require": {
        "navruzm/mongo-validation": "*"
    }
}
```
And then run `composer update`

Once Composer has updated your packages open up `app/config/app.php` and change `Illuminate\Validation\ValidationServiceProvider` to `MongoValidation\MongoValidationServiceProvider`
