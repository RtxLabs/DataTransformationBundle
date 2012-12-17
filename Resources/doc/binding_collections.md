Binding Collections
===================

Binding collections works exactly like binding objects with the difference that collections have to be given to
the "bind" method instead of an singel object.

![class diagram](http://yuml.me/diagram/scruffy;/class/[Car|id;name], [Group|id;name], [User|id;isAdmin;username;deletedAt]->drives 0..1[Car], [User]->groups *[Group], [User]->deletedBy 0..1[User])

... and this should be the result returned by the service:

```json
[{
    "id": 1181,
    "isAdmin": false,
    "username": "uklawitter",
    "deletedAt": null,
    "car": 7
}, {
    "id": 1182,
    "isAdmin": true,
    "username": "thaberkern",
    "deletedAt": null,
    "car": null
}]
```

Binding with Binder
-------------------

```php
$users = $repository->findAll();
$stdClass = Binder::create()
    ->bind($users)
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
$users = $repository->findAll();
$stdClass = $this->container->get('getmethodbinder')
    ->bind($users)
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
$users = $repository->findAll();
$stdClass = $this->container->get('doctrinebinder')->bind($users)->execute();
$json = Dencoder::decode($stdClass);
```