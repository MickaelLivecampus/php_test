<?php

use Random\RandomException;

require_once('src/database/db.php');

function checkPath(): bool
{
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    $routes = [
        '/',
        '/index.php',
        '/dashboard.php',
        '/assets/bootstrap/css/bootstrap.min.css',
        '/assets/css/styles.min.css'
    ];

    return in_array($path, $routes, true);
}

/**
 * @throws RandomException
 */
function generateToken(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * @throws RandomException
 */
function loginWithToken($username, $password): false|string
{
    $conn = connectDB();
    $query = "SELECT id, username, password_hash FROM administrateurs WHERE username = ?";
    $stmt = $conn?->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $adminId = $row['id'];
        $hashedPassword = $row['password_hash'];

        if (password_verify($password, $hashedPassword)) {
            $token = generateToken();

            $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day'));
            $insertQuery = "INSERT INTO tokens (user_id, token, expiration_date) VALUES (?, ?, ?)";
            $insertStmt = $conn?->prepare($insertQuery);
            $insertStmt->bind_param("iss", $adminId, $token, $expirationDate);
            $insertStmt->execute();
            return $token;
        }
    }

    return false;
}

function isTokenInDatabase($token): bool
{
    $conn = connectDB();

    $query = "SELECT COUNT(*) AS token_count FROM tokens WHERE token = ? AND expiration_date > NOW()";
    $stmt = $conn?->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $tokenCount = $row['token_count'];

        return $tokenCount > 0;
    }
    return false;
}

function isTokenValid($token): bool
{
    if (!empty($token)) {
        return isTokenInDatabase($token);
    }

    return false;
}