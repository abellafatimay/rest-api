<?php
header('Access-Control-Allow-Origin: http://localhost:8090');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

return [
    // Public Routes
    [
        'method' => 'POST',
        'path' => '/register',
        'handler' => function () use ($authController) {
            $data = json_decode(file_get_contents('php://input'), true);
            $response = $authController->register($data);

            http_response_code($response->getStatusCode());
            echo json_encode($response->getBody());
            return;
        }
    ],
    [
        'method' => 'POST',
        'path' => '/login',
        'handler' => function () use ($authController) {
            $data = json_decode(file_get_contents('php://input'), true);
            $response = $authController->login($data);

            http_response_code($response->getStatusCode());
            echo json_encode($response->getBody());
            return;
        }
    ],

    // Protected Routes
    [
        'method' => 'GET',
        'path' => '/users',
        'handler' => Middleware::authenticate($authController, function ($userId) use ($controller) {
            return $controller->getAllUsers();
        })
    ],
    [
        'method' => 'GET',
        'path' => '/users/{id}',
        'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
            return $controller->getUserById($id);
        })
    ],
    [
        'method' => 'POST',
        'path' => '/users',
        'handler' => Middleware::authenticate($authController, function ($userId) use ($controller) {
            return $controller->createUser();
        })
    ],
    [
        'method' => 'PUT',
        'path' => '/users/{id}',
        'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
            return $controller->updateUser($id);
        })
    ],
    [
        'method' => 'DELETE',
        'path' => '/users/{id}',
        'handler' => Middleware::authenticate($authController, function ($userId, $id) use ($controller) {
            return $controller->deleteUser($id);
        })
    ],
];


