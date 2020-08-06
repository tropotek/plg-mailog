<?php

use Tk\Routing\Route;

$config = \App\Config::getInstance();
$routes = $config->getRouteCollection();
if (!$routes) return;

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Tk\\Ml\\', dirname(__FILE__));



$routes->add('mailLog-settings', new Route('/admin/mailLog/{fkey}/{fid}/settings.html', 'Tk\Ml\Controller\Settings::doDefault'));

$routes->add('mailLog-manager', new Route('{type}/mailLog/{fkey}/{fid}/manager.html', 'Tk\Ml\Controller\MailLog\Manager::doDefault'));
$routes->add('mailLog-view', new Route('{type}/mailLog/{fkey}/{fid}/view.html', 'Tk\Ml\Controller\MailLog\View::doDefault'));

