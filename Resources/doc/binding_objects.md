Binding Objects
===============

Supposted the entity structure looks like this:

![class diagram](http://yuml.me/diagram/scruffy;/class/[Car|id;name], [Group|id;name], [User|id;isAdmin;username;deletedAt]->drives 0..1[Car], [User]->groups *[Group], [User]->deletedBy 0..1[User])

... and this should be the result returned by the service:

```json
{
    "id": 1181,
    "isAdmin": false,
    "username": "uklawitter",
    "deletedAt": null
    "car": 7
}
```

Binding with Binder
-------------------

```php
$user = $repository->findOneById(1181);
$stdClass = Binder::create()
    ->bind($user)
    ->fields("id", "isAdmin", "username", "deletedAt")
    ->field("car", function($user) {
        $car = $user->getCar();
        if ($car) {
            return $car->getId();
        }
        else {
            return null;
        }
    }))->execute();
$json = Dencoder::decode($stdClass);
```

Binding with GetMethodBinder
----------------------------

```php
$user = $repository->findOneById(1181);
$stdClass = GetMethodBinder::create(false)
    ->bind($user)
    ->field("car", function($user) {
        $car = $user->getCar();
        if ($car) {
            return $car->getId();
        }
        else {
            return null;
        }
    }))->execute();
$json = Dencoder::decode($stdClass);
```

Binding with DoctrineBinder
---------------------------

```php
$user = $repository->findOneById(1181);
$stdClass = DoctrineBinder::create(false)->bind($user)->execute();
$json = Dencoder::decode($stdClass);
```