<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';
//echo __DIR__.'/../app/bootstrap.php.cache'; die;
include_once __DIR__.'/../app/bootstrap.php.cache';

//include_once __DIR__.'/../app/Resources/TwigBundle/views/Exception/technical_works.html.twig';
//die;

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new Symfony\Component\ClassLoader\ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/

//require_once __DIR__.'/../app/AppCache.php';

if (strpos($_SERVER['REQUEST_URI'], 'admin-portal/instance') !== false) {
    $arrUri = explode('/', $_SERVER['REQUEST_URI']);
    if ( !empty($arrUri) && isset($arrUri[1]) && $arrUri[1] == 'admin-portal' &&
            isset($arrUri[2]) && $arrUri[2] == 'instance' &&
            isset($arrUri[3]) && trim($arrUri[3]) &&
            file_exists($_SERVER['DOCUMENT_ROOT'].'/../app/sites/'.$arrUri[3].'/config/config.yml')) {
        $kernel = new AppKernel('prod', false, $arrUri[3]);
    } else {
        $kernel = new AppKernel('prod', false);
    }
/*} elseif ($_SERVER['HTTP_HOST'] !== $_SERVER['SERVER_NAME']) {
    $arrHttpHost = explode('.', $_SERVER['HTTP_HOST']);
    $kernel = new AppKernel('prod', false, $arrHttpHost[0]);*/
} else {
    $kernel = new AppKernel('prod', false);
}
$kernel->boot();
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();

//Request::setTrustedProxies(['192.0.0.1', '10.0.0.0/8'], Request::HEADER_X_FORWARDED_ALL);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
