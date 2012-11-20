<?php

$vendorDir = __DIR__.'/../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => $vendorDir.'/symfony/src',
    'Doctrine\\Common' => $vendorDir.'/doctrine-common/lib',
    'Doctrine\\DBAL'   => $vendorDir.'/doctrine-dbal/lib',
    'Doctrine\\Tests' => $vendorDir.'/doctrine/tests',
    'Doctrine' => $vendorDir.'/doctrine/lib'
));
$loader->registerPrefixes(array(
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
));
$loader->register();

spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');
    if (0 === strpos($class, 'RtxLabs\DataTransformationBundle\\')) {
        $file = __DIR__.'/../'.str_replace('\\', '/', substr($class, strlen('RtxLabs\DataTransformationBundle\\'))).'.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
