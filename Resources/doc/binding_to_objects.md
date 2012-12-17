Binding to Objects
==================

To update date of a given object (for example Doctrine entity) the "to" method can be used.
Supposted the entity structure looks like this:

![class diagram](http://yuml.me/diagram/scruffy;/class/[Car|id;name], [Group|id;name], [User|id;isAdmin;username;deletedAt]->drives 0..1[Car], [User]->groups *[Group], [User]->deletedBy 0..1[User])

... and this should is what we get from an put action or something else:

```json
{
    "id": 1181,
    "isAdmin": false,
    "username": "uklawitter",
    "deletedAt": null,
    "car": 7
}
```

Example: Binder
-------------------

```php
$data = Dencoder::decode($this->getRequest()->getContent());
$user = $userRepository->find($data->id);
Binder::create()
    ->bind($data)
    ->to($user)
    ->fields("id", "isAdmin", "username", "deletedAt")
    ->field("car", function($carId) use ($user, $carRepository) {
        return $carRepository->find($carId);
    })
    ->execute();
```

Example: GetMethodBinder
----------------------------

```php
$data = Dencoder::decode($this->getRequest()->getContent());
$user = $userRepository->find($data->id);
$this->container->get('getmethodbinder')
    ->bind($data)
    ->to($user)
    ->field("car", function($carId) use ($user, $carRepository) {
        return $carRepository->find($carId);
    })
    ->execute();
```

Example: DoctrineBinder
---------------------------

```php
$data = Dencoder::decode($this->getRequest()->getContent());
$user = $userRepository->find($data->id);
$this->container->get('doctrinebinder')
    ->bind($data)
    ->to($user)
    ->execute();
```