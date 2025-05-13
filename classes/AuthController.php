<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $userRepository;
    private $secretKey = 'your-secret-key'; // Replace with a secure key

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    // User registration
    public function register($data) {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return ['error' => 'All fields are required'];
        }

        // Check if the email already exists
        if ($this->userRepository->getByEmail($data['email'])) {
            return ['error' => 'Email already exists'];
        }

        // Hash the password and save the user
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->userRepository->create($data);

        return ['success' => true, 'message' => 'User registered successfully'];
    }

    // User login and token generation
    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $user = $this->userRepository->getByEmail($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $payload = [
                'iss' => 'your-domain.com',
                'sub' => $user['id'],
                'iat' => time(),
                'exp' => time() + 3600 // Token expires in 1 hour
            ];
            $token = JWT::encode($payload, $this->secretKey, 'HS256');
            return ['token' => $token];
        }

        return ['error' => 'Invalid credentials'];
    }

    // Authenticate requests
    public function authenticate($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->sub; // Return user ID
        } catch (Exception $e) {
            return ['error' => 'Invalid token'];
        }
    }
}
