The Dencoder Class
=========================

The Binders returning a stdClass object. This object can not be used to create an Response. Thats why
the Dencoder exists. If a bound object should be returned, the encode method can be used:

```php
$stdClass = GetMethodBinder::create(false)->bind($user)->execute()
return new Response(Dencoder::encode($stdClass));
```

Data that is given by a POST/PUT request has to be converted into data that can be bound to objects:

```php
$data = Dencoder::decode($this->getRequest()->getContent());
$this->container->get('doctrinebinder')
    ->bind($data)
    ->to($template)
    ->execute();
```