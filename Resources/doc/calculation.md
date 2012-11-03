Calculation
===========

Sometimes it is needed to return values that are calculated and not persistet in any entities. To do this, the
field method can be used.

Supposted the entity structure looks like this:

![class diagram](http://yuml.me/diagram/scruffy;/class/[Car|id;name], [Group|id;name], [User|id;isAdmin;username;deletedAt]->drives 0..1[Car], [User]->groups *[Group], [User]->deletedBy 0..1[User])

... and this should is what we get from an put action or something else:

```json
[{
    "id": 1181,
    "isAdmin": false,
    "username": "uklawitter",
    "deletedAt": null
    "groupCount": 3
}, {
    "id": 1182,
    "isAdmin": true,
    "username": "thaberkern",
    "deletedAt": null
    "groupCount": 6
}]
```

Binding with Binder
-------------------

```php
$users = $repository->findAll();
$stdClass = Binder::create()
    ->bind($users)
    ->fields("id", "isAdmin", "username", "deletedAt")
    ->field("groupCount", function($user) {
        return count($user->getGroups());
    })
    ->execute();
$json = Dencoder::decode($stdClass);
```

Binding with GetMethodBinder
----------------------------

```php
$users = $repository->findAll();
$stdClass = GetMethodBinder::create(false)
    ->bind($users)
    ->field("groupCount", function($user) {
        return count($user->getGroups());
    })
    ->execute();
$json = Dencoder::decode($stdClass);
```

Binding with DoctrineBinder
---------------------------

```php
$users = $repository->findAll();
$stdClass = DoctrineBinder::create(false)
    ->bind($users)
    ->field("groupCount", function($user) {
        return count($user->getGroups());
    })
    ->execute();
$json = Dencoder::decode($stdClass);
```