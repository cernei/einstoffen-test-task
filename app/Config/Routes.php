<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('api/invoices', 'Invoices::index');
$routes->post('api/invoices', 'Invoices::store');

$routes->get('api/shipment-job-test/(:segment)', 'JobTest::shipment/$1');