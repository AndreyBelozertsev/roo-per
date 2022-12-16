<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;


//include_once __DIR__.'/../app/Resources/TwigBundle/views/Exception/technical_works.html.twig';
//var_dump($_SERVER);
//die;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
/*if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}*/

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

if (strpos($_SERVER['REQUEST_URI'], 'admin-portal/instance') !== false) {
    $arrUri = explode('/', $_SERVER['REQUEST_URI']);
    if ( !empty($arrUri) && isset($arrUri[2]) && $arrUri[2] == 'admin-portal' &&
            isset($arrUri[3]) && $arrUri[3] == 'instance' &&
            isset($arrUri[4]) && trim($arrUri[4]) &&
            file_exists($_SERVER['DOCUMENT_ROOT'].'/../app/sites/'.$arrUri[4].'/config/config.yml')) {
        $kernel = new AppKernel('dev', true, $arrUri[4]);
    } else {
        $kernel = new AppKernel('dev', true);
    }
/*} elseif ($_SERVER['HTTP_HOST'] !== $_SERVER['SERVER_NAME']) {
    $arrHttpHost = explode('.', $_SERVER['HTTP_HOST']);
    $kernel = new AppKernel('dev', true, $arrHttpHost[0]);*/
} else {
    $kernel = new AppKernel('dev', true);
}
$kernel->boot();
//$kernel->loadClassCache();
//Request::setTrustedProxies(['192.0.0.1', '10.0.0.0/8'], Request::HEADER_X_FORWARDED_ALL);
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
