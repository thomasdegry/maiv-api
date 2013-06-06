<?php

// Require Composer autoloader
// This includes the router
require_once 'vendor/autoload.php';

// Require the response object
require_once 'rest/Response.php';

// Require any other dependencies here
require_once 'model/User.php';
require_once 'model/Event.php';
require_once 'model/Burger.php';
require_once 'model/Creation.php';

$config = array();
$config['db']['driver'] = 'mysql';
$config['db']['host'] = 'localhost';
$config['db']['user'] = 'root';
$config['db']['password'] = 'root';
$config['db']['dbname'] = 'maiv_mrburger';