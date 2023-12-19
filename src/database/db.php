<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function connectDB()
{

    $host = $_ENV['MYSQL_HOST'];
    $user = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];
    $database = $_ENV['MYSQL_DATABASE'];
    $port = $_ENV['MYSQL_PORT'];

    $conn = new mysqli($host, $user, $password, $database, $port);

    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    return $conn;
}

function createUser(): void
{
    $conn = connectDB();

    $username = 'alexis';
    $password = password_hash('1234', PASSWORD_DEFAULT);

    $stmt = $conn?->prepare(
        'INSERT INTO administrateurs (username,password_hash) VALUES(?,?)'
    );
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
}
