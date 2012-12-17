Installation
============

## Step 1) Get the bundle

First, grab the RtxLabsDataTransformationBundle. There are two different ways
to do this:

### Method a) Using the `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```
[RtxLabsDataTransformationBundle]
    git=https://github.com/RtxLabs/DataTransformationBundle.git
    target=bundles/RtxLabs/DataTransformationBundle
```

### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/RtxLabs/DataTransformationBundle.git vendor/bundles/RtxLabs/DataTransformationBundle
```

### Method c) Using Composer

Add the following lines in your `composer.json` file:

``` js
"require": {
    "rtxlabs/datatransformation-bundle": "1.0.*"
}
```

Run Composer to download and install the bundle:

    $ php composer.phar update rtxlabs/datatransformation-bundle

## Step 2) Register the namespaces

Add the following namespace entry to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'RtxLabs' => __DIR__.'/../vendor/bundles',
    // ...
));
```

## Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new RtxLabs\DataTransformationBundle\RtxLabsDataTransformationBundle(),
    );
    // ...
)
```

## Next Steps

- Using the [Binder](DataTransformationBundle/tree/master/Resources/doc/binder.md)
