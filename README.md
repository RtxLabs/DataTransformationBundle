About
============

Bundle that integrates the Liquibase Database migration tool in Symfony projects. The bundle comes with the latest
LiquiBase-Version, different JDBC drivers and some Commandline tools

[![Build Status](https://secure.travis-ci.org/RtxLabs/LiquibaseBundle.png)](http://travis-ci.org/RtxLabs/LiquibaseBundle)

Installation
============

## Installation

### Step 1) Get the bundle

First, grab the RtxLabsLiquibaseBundle. There are two different ways
to do this:

#### Method a) Using the `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```
[RtxLabsLiquibaseBundle]
    git=https://github.com/RtxLabs/LiquibaseBundle.git
    target=bundles/RtxLabs/LiquibaseBundle
```

#### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/RtxLabs/LiquibaseBundle.git vendor/bundles/RtxLabs/LiquibaseBundle
```

### Step 2) Register the namespaces

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

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new RtxLabs\LiquibaseBundle\RtxLabsLiquibaseBundle(),
    );
    // ...
)
```

Usage
============

At the moment the Bundle comes with two commands

```bash
php app/console liquibase:generate:changelog [--with-changeset] BundleName:ChangelogName
php app/console liquibase:update:run BundleName
```

TODO
============

* Write a decent documentation
* Add a rollback commandline task
* Add unit tests
