<?php

// Start the output buffer
ob_start();

// Set the include path
set_include_path( get_include_path() . 
                  ':../library:' .
                  ':../library/Table' .
                  ':../library/Table/Tables' .
                  ':../library/Inflection' .
                  ':../library/Inflection/Inflectors' .
                  ':../library/Inflection/Inflections' .
                  ':../library/Model' .
                  ':../library/Mapping' .
                  ':../library/Access' .
                  ':../library/Controller' .
                  ':../app/models'
                );

// AUTOLOADER 
////////////////////////////////////////////////////////////////////////////////
require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

// SESSION
////////////////////////////////////////////////////////////////////////////////
Zend_Session::start();

// CONFIGURATION
////////////////////////////////////////////////////////////////////////////////
// Current site state (staging or production)
$state = 'staging';
// General configuration
$general_config = new Zend_Config_Xml('../app/config/general.xml', $state, true);
// Paths
$path_config = new Zend_Config_Xml('../app/config/paths.xml', $state, true);
// Routes
$route_config = new Zend_Config_Xml('../app/config/routes.xml', $state, true);
// Model Mappers
$mapper_config = new Zend_Config_Xml('../app/config/mappers.xml', $state, true);
// Layout
$layout_config = $general_config->layout;
// Database
$database_config = $general_config->database;
// Authentication
$authentication_config = $general_config->authentication;
// Authorization
$authorization_config = $general_config->authorization;
// Register config in the registry
Zend_Registry::set('general_config', $general_config);

// COMPONENTS
////////////////////////////////////////////////////////////////////////////////
// Get the front controller
$front_controller = Zend_Controller_Front::getInstance();
// Get the router
$router = $front_controller->getRouter();

// PATHS
////////////////////////////////////////////////////////////////////////////////
// Set controller directories (array of module => path pairs)
$module_paths = $path_config->module_paths->toArray();
$front_controller->setControllerDirectory($module_paths);

// ROUTES
////////////////////////////////////////////////////////////////////////////////
// Load the site routes into the router
$router->addConfig($route_config, 'routes');

// LAYOUT
////////////////////////////////////////////////////////////////////////////////
Zend_Layout::startMvc($layout_config);

// DATABASE
////////////////////////////////////////////////////////////////////////////////
$db = Zend_Db::factory($database_config);
// Register the database adapter in the registry
Zend_Registry::set('database', $db);
// Set the database adapter as the default for all tables
Zend_Db_Table_Abstract::setDefaultAdapter($db);

// MAPPERS
////////////////////////////////////////////////////////////////////////////////
Mapper::setConfig($mapper_config);

// RUN
////////////////////////////////////////////////////////////////////////////////
// Display the site
$front_controller->dispatch();

ob_end_flush();
