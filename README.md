# entity-repository
Trait for Doctrine\ORM\EntityRepository

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/entity-repository
```

## Usage

```php
class MyRepository extends Doctrine\ORM\EntityRepository
{
    use Cyve\EntityRepository\EntityRepositoryTrait;
}
```
