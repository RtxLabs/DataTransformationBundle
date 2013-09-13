DataTransformation Bundle
=========================

Symfony2 Bundle that can help building REST services.

[![Build Status](https://secure.travis-ci.org/RtxLabs/DataTransformationBundle.png)](http://travis-ci.org/RtxLabs/DataTransformationBundle)

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
$result = $this->container->get('doctrinebinder')->bind($models)->execute();

// finally the result has to be converted into json to return it as an response
$json = Dencoder::decode($result);
```

Documentation
-------------

- [Installation](DataTransformationBundle/blob/master/Resources/doc/installation.md)
- [Binding objects / entities](DataTransformationBundle/blob/master/Resources/doc/binding_objects.md)
- [Binding collections](DataTransformationBundle/blob/master/Resources/doc/binding_collections.md)
- [Joining referenced entities](DataTransformationBundle/blob/master/Resources/doc/joining.md)
- [Binding data to entities](DataTransformationBundle/blob/master/Resources/doc/binding_to_objects.md)
- [Binding calculated values](DataTransformationBundle/blob/master/Resources/doc/calculation.md)
- [Encode and decode bound values](DataTransformationBundle/blob/master/Resources/doc/dencoder.md)

