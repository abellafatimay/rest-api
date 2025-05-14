<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $userRepository;
    private $secretKey = 'your-secret-key';

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register($data) {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return new Response(400, ['error' => 'All fields are required']);
        }

        // Check if the email already exists
        if ($this->userRepository->getByEmail($data['email'])) {
            return new Response(409, ['error' => 'Email already exists']);
        }

        // Hash the password and save the user
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->userRepository->create($data);

        return new Response(201, ['success' => true, 'message' => 'User registered successfully']);
    }

    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            return new Response(400, ['error' => 'Email and password are required']);
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
            return new Response(200, ['token' => $token]);
        }

        return new Response(401, ['error' => 'Invalid credentials']);
    }

    public function authenticate($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->sub; // Return user ID
        } catch (Exception $e) {
            return new Response(401, ['error' => 'Invalid token']);
        }
    }
}
