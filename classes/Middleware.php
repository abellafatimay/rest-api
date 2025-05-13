<?php

class Middleware {
    // Authenticate the user using JWT
    public static function authenticate($authController, $handler) {
        return function (...$args) use ($authController, $handler) {
            $headers = getallheaders();
            $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
            $userId = $authController->authenticate($token);

            if (isset($userId['error'])) {
                return $userId; // Return error if token is invalid
            }

            // Pass the authenticated user ID and other arguments to the handler
            return $handler($userId, ...$args);
        };
    }

    // Role-based access control (RBAC)
    public static function authorize($authController, $requiredRole, $handler) {
        return function (...$args) use ($authController, $requiredRole, $handler) {
            $headers = getallheaders();
            $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
            $userId = $authController->authenticate($token);

            if (isset($userId['error'])) {
                return $userId; // Return error if token is invalid
            }

            // Check if the user has the required role
            $user = $authController->getUserById($userId); // Assuming this method exists
            if ($user['role'] !== $requiredRole) {
                return ['error' => 'Unauthorized access'];
            }

            // Pass the authenticated user ID and other arguments to the handler
            return $handler($userId, ...$args);
        };
    }

    // Example: Input validation middleware
    public static function validateInput($rules, $handler) {
        return function (...$args) use ($rules, $handler) {
            $data = json_decode(file_get_contents('php://input'), true);

            foreach ($rules as $field => $rule) {
                if (!isset($data[$field]) || !preg_match($rule, $data[$field])) {
                    return ['error' => "Invalid input for field: $field"];
                }
            }

            // Pass the validated data and other arguments to the handler
            return $handler($data, ...$args);
        };
    }
}