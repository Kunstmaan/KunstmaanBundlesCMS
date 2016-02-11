<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../app/bootstrap.php.cache';

if (extension_loaded('apc') && ini_get('apc.enabled')) {
    // Enable APC for autoloading to improve performance.
    // You should change the ApcClassLoader first argument to a unique prefix
    // in order to prevent cache key conflicts with other applications
    // also using APC.
    $apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
    $loader->unregister();
    $apcLoader->register(true);
}

if (getenv('APP_ENV') === 'dev') {
    umask(0000);
    Debug::enable();
    $kernel = new AppKernel('dev', true);
} else {
    $kernel = new AppKernel('prod', false);
}

$kernel->loadClassCache();

if (getenv('APP_ENV') !== 'dev') {
    if (!isset($_SERVER['HTTP_SURROGATE_CAPABILITY']) || false === strpos($_SERVER['HTTP_SURROGATE_CAPABILITY'], 'ESI/1.0')) {
        $kernel = new AppCache($kernel);
    }
}

Request::enableHttpMethodParameterOverride();
Request::setTrustedProxies(array('127.0.0.1'));
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
