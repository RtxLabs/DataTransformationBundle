The Binder Class
================

The Binder is the most basic DataTransformationBundle and is used by the GetMethodBinder and the DoctrineBinder.

## Example: Binding an object to stdClass object
´´´php
$contact = new Contact();
$contact->setFirstname("Max");
$contact->setLastname("Muster");

$this->assertBound($entity->getId(), "id", $entity);
´´´

## Next Step

- Using the [GetMethodBinder](DataTransformationBundle/tree/master/Resources/doc/get_method_binder.md)