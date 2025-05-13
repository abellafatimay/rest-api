<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'init.php';

// Initialize the database connection
$db = new Database('localhost', 'root', 'root', 'rest_api');

// Initialize the ORM
$orm = new ORM($db);

// Initialize the user repository
$userRepository = new UserRepository($orm);

// Initialize the auth controller
$authController = new AuthController($userRepository);

// Initialize the request object
$request = new Request();

// Initialize the user controller with dependencies
$controller = new UserController($userRepository, $request);

// Load routes and pass $authController and $controller
$routes = include __DIR__ . '/routes.php';

// Initialize the router
$router = new Router($request, new RouteMatcher());

// Register routes
foreach ($routes as $route) {
    $router->addRoute($route['method'], $route['path'], $route['handler']);
}

// Dispatch the request
$response = $router->dispatch();

// Send the response
echo json_encode($response);
