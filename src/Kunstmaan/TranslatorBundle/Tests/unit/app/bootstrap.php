<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!is_file($loaderFile = __DIR__.'/../../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $loaderFile;

// Needed to load entities from the cli
AnnotationRegistry::registerFile(__DIR__.'/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
AnnotationRegistry::registerAutoloadNamespace('Symfony\Component\Validator\Constraint', __DIR__.'/../../vendor/symfony/symfony/src');
