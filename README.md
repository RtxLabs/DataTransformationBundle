About
=====

This bundle contains "Binders" that can be used to convert for example Doctrine2 entities into stdClass objects.
Sounds not very spectacular, but if you want to provide some REST services to read or manipulate your Doctrine
entities, this bundle can help you a lot.

## Example:

You want to provide a REST service that returns a list of contacts linked with companies. The service has to deliver
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
$em = $this->getDoctrine()->getEntityManager();
$result = DoctrineBinder::create($em)->bind($models)->execute();
$json = Dencoder::deno
var_dump($result);
```

In this example we simply bind

Its cool, isn't it? The code is shorter, easier to understand an less error prone. We declared
a DoctrineBinder and joined the address, the company and the address of the company.
The joined entity will also be bound by a Binder, defined by the second join parameter.

The result of DoctrineBinder->execute() will be an object of type stdClass that can be transformed into
json very easily.

Its also possible to modify some data or calculate something before returning it, but I think that has to be explained
in the documentation. Another cool feature is the ability to bind the data given by a request and
bind it to an existing or new entity. So if the bundle drew your interest, I would recommend that you install the
bundle and try to bind some data ;)

- [Installation](DataTransformationBundle/tree/master/Resources/doc/installation.md)
- Using the [Binder](DataTransformationBundle/tree/master/Resources/doc/binder.md)
- Using the [GetMethodBinder](DataTransformationBundle/tree/master/Resources/doc/get_method_binder.md)
- Using the [DoctrineBinder](DataTransformationBundle/tree/master/Resources/doc/doctrine_binder.md)

Changelog
=========
