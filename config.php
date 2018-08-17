<?php

$config = \App\Config::getInstance();
$routes = $config->getRouteCollection();
if (!$routes) return;

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Tk\\Ml\\', dirname(__FILE__));



//$routes->add('mailog-admin-settings', new \Tk\Routing\Route('/admin/mailogSettings.html', 'Tk\Ml\Controller\Settings::doDefault'));
//$routes->add('mailog-admin-manager', new \Tk\Routing\Route('/admin/mailLogManager.html', 'Tk\Ml\Controller\MailLog\Manager::doDefault'));
//$routes->add('mailog-admin-view', new \Tk\Routing\Route('/admin/mailLogView.html', 'Tk\Ml\Controller\MailLog\View::doDefault'));

$routes->add('mailog-role-settings', new \Tk\Routing\Route('/{roleType}/mailogSettings.html', 'Tk\Ml\Controller\Settings::doDefault'));
$routes->add('mailog-role-manager', new \Tk\Routing\Route('/{roleType}/mailLogManager.html', 'Tk\Ml\Controller\MailLog\Manager::doDefault'));
$routes->add('mailog-role-view', new \Tk\Routing\Route('/{roleType}/mailLogView.html', 'Tk\Ml\Controller\MailLog\View::doDefault'));

