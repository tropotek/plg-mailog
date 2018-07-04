<?php
$config = \Tk\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Ml\\', dirname(__FILE__));

/** @var \Tk\Routing\RouteCollection $routes */
$routes = $config['site.routes'];

$params = array('role' => 'admin');
$routes->add('mailog-settings', new \Tk\Routing\Route('/mailog/adminSettings.html', 'Ml\Controller\SystemSettings::doDefault', $params));

$routes->add('mailog-admin-manager', new \Tk\Routing\Route('/admin/mailLogManager.html', 'Ml\Controller\SystemSettings::doDefault', $params));





