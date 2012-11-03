Joining
=======

In many cases it is usefull if a referenced object will also be returned. In this case, the join can be used to
define a binder the will be used to bind the referenced class. Supposted the entity structure
looks like this:

![class diagram](http://yuml.me/diagram/scruffy;/class/[Car|id;name], [Group|id;name], [User|id;isAdmin;username;deletedAt]->drives 0..1[Car], [User]->groups *[Group], [User]->deletedBy 0..1[User])

... and this should be the result returned by the service:

```json
[{
    "id": 1181,
    "isAdmin": false,
    "username": "uklawitter",
    "deletedAt": null
    "car": {
        id: 7,
        name: "Twinto"
    }
}, {
    "id": 1182,
    "isAdmin": true,
    "username": "thaberkern",
    "deletedAt": null
    "car": null
}]
```

Example: Binder
-------------------

```php
$users = $repository->findAll();
$stdClass = Binder::create()
    ->bind($users)
    ->fields("id", "isAdmin", "username", "deletedAt")
    ->join("car", Binder::create()->fields("id", "name"))
    ->execute();
$json = Dencoder::decode($stdClass);
```

Example: GetMethodBinder
----------------------------

```php
$users = $repository->findAll();
$stdClass = GetMethodBinder::create(false)
    ->bind($users)
    ->join("car", GetMethodBinder::create(false))
    ->execute();
$json = Dencoder::decode($stdClass);
```

Example: DoctrineBinder
---------------------------

```php
$users = $repository->findAll();
$stdClass = DoctrineBinder::create(false)
    ->bind($users)
    ->join("car", DoctrineBinder::create(false))
    ->execute();
$json = Dencoder::decode($stdClass);
```