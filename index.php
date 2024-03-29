<?php

require_once 'bootstrap.php';
error_reporting(E_ALL);
ini_set('error_reporting', "1");

// Set up router
$router = new \Bramus\Router\Router();

// Set up database
$dsn = $config['db']['driver'] . ':host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname']
       . ';user=' . $config['db']['user'] . ';password=' . $config['db']['password'] . ';charset=utf8';

$db = new \PDO($dsn, $config['db']['user'], $config['db']['password']);

if ($db) {
    $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    if ($config['db']['driver'] === 'mysql') $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
} else {
    die('Could not connect to database');
}

// Define routes

// Example code

$router->get('/users/unusedburgers/(\d+)', function($limit) use($db) {
    $res = new JSONResponse();
    $user = new User($db);

    $res->setData($user->getUsersWithFreeBurgers($limit));
    $res->finish();
});

$router->get('/users/(\d+)', function($id) use ($db) {
    $res = new JSONResponse();
    $user = new User($db);

    $res->setData($user->get($id));
    $res->finish();
});

$router->get('/users/(\d+)/hasfree', function($id) use ($db) {
    $res = new JSONResponse();
    $user = new User($db);

    $res->setData($user->hasFree($id));
    $res->finish();
});

$router->post('/users', function () use ($db) {
    $res = new JSONResponse();
    $user = new User($db);

    if (empty($_POST)) {
        $_POST = (array) json_decode(@file_get_contents('php://input'));
    }

    $res->setData($user->add($_POST));
    $res->finish();
});

$router->get('/events', function() use ($db) {
    $res = new JSONResponse();
    $event = new Event($db);

    $res->setData($event->getAll());
    $res->finish();
});

$router->get('/events/(\d+)', function($id) use ($db) {
    $res = new JSONResponse();
    $event = new Event($db);

    $res->setData($event->get($id));
    $res->finish();
});

$router->get('/events/now', function() use ($db) {
    $res = new JSONResponse();
    $event = new Event($db);

    $res->setData($event->getCurrentEvent());
    $res->finish();
});

$router->get('/burgers', function() use($db){
    $res = new JSONResponse();
    $burger = new Burger($db);

    $res->setData($burger->getAll());
    $res->finish();
});

$router->post('/burgers', function() use ($db) {
    $res = new JSONResponse();
    $burger = new Burger($db);

    if (empty($_POST)) {
        $_POST = (array) json_decode(@file_get_contents('http://input'));
    }

    $res->setData($burger->add($_POST));
    $res->finish();
});

$router->post('/creations', function() use ($db) {
    $res = new JSONResponse();
    $creation = new Creation($db);

    if (empty($_POST)) {
        $_POST = (array) json_decode(@file_get_contents('http://input'));
    }

    $res->setData($creation->add($_POST));
    $res->finish();
});

$router->get('/creations/pay/(\d+)/(\d+)', function($user_id, $burger_id) use ($db) {
    $res = new JSONResponse();
    $creation = new Creation($db);

    $res->setData($creation->pay($user_id, $burger_id));
    $res->finish();
});

$router->get('/burgers/(\d+)', function($id) use ($db){
    $res = new JSONResponse();
    $burger = new Burger($db);

    $res->setData($burger->get($id));
    $res->finish();
});

$router->post('/rate', function() use ($db) {
    $res = new JSONResponse();
    $rating = new Rating($db);

    $res->setData($rating->rate($_POST));
    $res->finish();
});

$router->get('/getRatings/(\d+)', function($id) use ($db) {
    $res = new JSONResponse();
    $rating = new Rating($db);

    $res->setData($rating->getRatings($id));
    $res->finish();
});

$router->run();