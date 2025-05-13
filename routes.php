<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

return [
    // Public Routes
    ['method' => 'POST', 'path' => '/register', 'handler' => function () use ($authController) {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $authController->register($data);
        echo json_encode($result);
        return;
    }],
    ['method' => 'POST', 'path' => '/login', 'handler' => function () use ($authController) {
        header('Content-Type: application/json'); // Ensure JSON response
        $data = json_decode(file_get_contents('php://input'), true);
        error_log('Login Request Data: ' . print_r($data, true)); // Log the request data

        $result = $authController->login($data);
        error_log('Login Response: ' . print_r($result, true)); // Log the response

        echo json_encode($result); // Return JSON response
        return;
    }],

    // Protected Routes
    ['method' => 'GET', 'path' => '/users', 'handler' => Middleware::authenticate($authController, function ($userId) use ($controller) {
        return $controller->getAllUsers();
    })],
    ['method' => 'GET', 'path' => '/users/{id}', 'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
        return $controller->getUserById($id);
    })],
    ['method' => 'POST', 'path' => '/users', 'handler' => Middleware::authenticate($authController, function ($userId) use ($controller) {
        return $controller->createUser();
    })],
    ['method' => 'PUT', 'path' => '/users/{id}', 'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
        return $controller->updateUser($id);
    })],
    ['method' => 'DELETE', 'path' => '/users/{id}', 'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
        return $controller->deleteUser($id);
    })],
];


