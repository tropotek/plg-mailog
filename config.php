<?php

$config = \App\Config::getInstance();
$routes = $config->getRouteCollection();
if (!$routes) return;

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Tk\\Ml\\', dirname(__FILE__));


$params = array('role' => 'admin');
$routes->add('mailog-settings', new \Tk\Routing\Route('/mailogSettings.html', 'Tk\Ml\Controller\Settings::doDefault', $params));

$routes->add('admin-mailog-manager', new \Tk\Routing\Route('/admin/mailLogManager.html', 'Tk\Ml\Controller\MailLog\Manager::doDefault', $params));
$routes->add('admin-mailog-view', new \Tk\Routing\Route('/admin/mailLogView.html', 'Tk\Ml\Controller\MailLog\View::doDefault', $params));





