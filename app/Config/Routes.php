<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Contact Management Routes
 * --------------------------------------------------------------------
 */
// Contact list and search
$routes->get('/contacts', 'Contacts::index');

// Create new contact
$routes->get('/contacts/create', 'Contacts::create');
$routes->post('/contacts/store', 'Contacts::store');

// Edit existing contact
$routes->get('/contacts/edit/(:num)', 'Contacts::edit/$1');
$routes->post('/contacts/update/(:num)', 'Contacts::update/$1');

// Delete contact
$routes->get('/contacts/delete/(:num)', 'Contacts::delete/$1');

// Import contacts
$routes->get('/contacts/import', 'Contacts::importForm');
$routes->post('/contacts/import', 'Contacts::import');

// Export contacts
$routes->get('/contacts/export-csv', 'Contacts::exportCsv');
$routes->get('/contacts/export-vcard/(:num)', 'Contacts::exportVcard/$1');

// Default route redirect to contacts
$routes->get('/', 'Contacts::index');
