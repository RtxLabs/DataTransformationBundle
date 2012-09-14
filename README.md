About
=====

This bundle contains some "Binders" that can be used to convert for example Doctrine2 entities into stdClass objects.
Sounds not very spectacular, but if you want to provide some REST services to read or manipulate your Doctrine
entities, this bundle can help you a lot.

## Example 1:

You want to provide a REST service that returns a list of contacts linked with companies. The service has to deliver
something like that:

```json
{
    "models":[{
        "id":1181,"
        "address":null,
        "email":"mail7@sbpdemo.de",
        "firstname":"Firstname7",
        "lastname":"Lastname7",
        "company":{
            "id":298,
            "name":"Company 0",
            "address":{
                "id":458,
                "city":"City1",
                "street":"Street 1"
            }
        }
    },{
        "id":1177,
        "address":null,
        "email":"mail3@sbpdemo.de",
        "firstname":"Firstname3",
        "lastname":"Lastname3",
        "company":{
            "id":298,
            "name":"Company 0",
            "address":{
                "id":458,
                "city":"City1",
                "street":"Street 1"
            }
        }
    }]
    ,"count":"2"}
```

Thats how I would do it without the DataTransformationBundle:

```php
class ContactController extends Controller
{
    /**
     * @Route("/contact", requirements={"_method"="GET"}, name="md_contact_list")
     */
    public function listAction()
    {
        // ... the models are already fetched

        $data = array(
            "models" => array(),
            "count" => $count
        );

        foreach ($contacts as $contact) {
            $address = $contact->getAddress();
            $addressData = null;

            if ($address != null) {
                $addressData = array(
                    "id" => $address->getId(),
                    "city" => $address->getCity(),
                    "street" => $address->getStreet()
                );
            }

            $company = $contact->getCompany();
            $companyData = null;

            if ($company != null) {
                $companyAddress = $company->getAddress();
                $companyAddressData = null;

                if ($companyAddress != null) {
                    $companyAddressData = array(
                        "id" => $companyAddress->getId(),
                        "city" => $companyAddress->getCity(),
                        "street" => $companyAddress->getStreet()
                    );
                }

                $companyData = array(
                    "id" => $company->getId(),
                    "name" => $company->getName(),
                    "address" => $companyAddressData
                );
            }

            $contactData = array(
                "id"=>$contact->getId(),
                "address" => $addressData,
                "firstname" => $contact->getFirstname(),
                "lastname" => $contact->getLastname(),
                "company" => $companyData
            );

            $data["models"][] = $contactData;
        }

        return new Response(json_encode($data));
    }
}
```

And now the same service implemented with the DoctrineBinder (included in this bundle):

```php
class ContactController extends Controller
{
    /**
     * @Route("/contact", requirements={"_method"="GET"}, name="md_contact_list")
     */
    public function listAction()
    {
        // ... the models are already fetched

        $em = $this->getDoctrine()->getEntityManager();

        $binder = DoctrineBinder::create($em)
            ->join("address", DoctrineBinder::create($em, false))
            ->join("company", DoctrineBinder::create($em, false)
                ->join("address", DoctrineBinder::create($em, false)));

        $data = array(
            "models"=>$binder->bind($models)->execute(),
            "count"=>$count
        );

        return new Response(json_encode($data));
    }
}
```

Its cool, isn't it? The code is shorter, easier to understand an less error prone. We declared
a DoctrineBinder and joined the address, the company and the address of the company.
The joined entity will also be bound by a Binder, defined by the second join parameter.

The result of DoctrineBinder->execute() will be an object of type stdClass that can be transformed into
json very easily.

Its also possible to modify some data or calculate something before returning it, but I think that has to be explained
in the documentation. Another cool feature is the ability to bind the data given by a request and
bind it to an existing or new entity. So if I drew your interest, I would recommend that you install the
bundle and try to bind some Data.

- [Installation](tree/master/Resources/doc/installation.md)
- Using the [Binder](tree/master/Resources/doc/installation.md)
- Using the [GetMethodBinder](DataTransformationBundle/tree/master/Resources/doc/get_method_binder.md)
- Using the [DoctrineBinder](DataTransformationBundle/tree/master/Resources/doc/doctrine_binder.md)

Changelog
=========
