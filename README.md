DataTransformation Bundle
=========================

Symfony2 Bundle that can help building REST services.

Example
-------

Providing a REST service that returns a list of contacts linked with companies. The service has to deliver
something like that:

```json
[{
    "id": 1181,
    "address": null,
    "email": "max.mustermann@dtb.com",
    "firstname": "Max",
    "lastname": "Mustermann",
    "company": 7
} , {
    "id" : 1177,
    "address" : null,
    "email" : "uwe.klawitter@dtb.com",
    "firstname" : "Uwe",
    "lastname" : "Klawitter",
    "company": 298
}]
```

To generate this, some Doctrine entities have to be loaded from a repository and converted into json. Performing
an json_encode() to on the entities won't work, because the entity contains proxy objects to the company. To
solve this, the DoctrineBinder can be used:

```php
// loading the entity manager to create the doctrine binder instance
$em = $this->getDoctrine()->getEntityManager();

// create a doctrine binder, bind the models that have been loaded before and execute the binder. The execute
// method will iterate over the models and return an array containing stdClass objects with all values defined
// by getters.
$result = DoctrineBinder::create($em)->bind($models)->execute();

// finally the result has to be converted into json to return it as an response
$json = Dencoder::decode($result);
```

In addition the binder can be used to:
- calculate values before returning them //TODO add a link
- bind request data to entities //TODO add a link
- joining data of referenced models //TODO add a link

Documentation
-------------

- [Installation](DataTransformationBundle/tree/master/Resources/doc/installation.md)
- Binding data from entity [Binder](DataTransformationBundle/tree/master/Resources/doc/binder.md)
- Using the [GetMethodBinder](DataTransformationBundle/tree/master/Resources/doc/get_method_binder.md)
- Using the [DoctrineBinder](DataTransformationBundle/tree/master/Resources/doc/doctrine_binder.md)
- Using the [Dencoder](DataTransformationBundle/tree/master/Resources/doc/dencoder.md)

