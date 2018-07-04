<?php
$config = \Tk\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Ems\\', dirname(__FILE__));

/** @var \Tk\Routing\RouteCollection $routes */
$routes = $config['site.routes'];

$params = array('role' => 'admin');
$routes->add('Sample Admin Settings', new \Tk\Routing\Route('/sample/adminSettings.html', 'Ems\Controller\SystemSettings::doDefault', $params));

$params = array('role' => array('admin', 'client'));
$routes->add('Sample Institution Settings', new \Tk\Routing\Route('/sample/institutionSettings.html', 'Ems\Controller\InstitutionSettings::doDefault', $params));

$params = array('role' => array('client', 'staff'));
$routes->add('Sample Course Profile Settings', new \Tk\Routing\Route('/sample/courseProfileSettings.html', 'Ems\Controller\CourseProfileSettings::doDefault', $params));

$params = array('role' => array('client', 'staff'));
$routes->add('Sample Course Settings', new \Tk\Routing\Route('/sample/courseSettings.html', 'Ems\Controller\CourseSettings::doDefault', $params));


