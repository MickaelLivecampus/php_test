<?php

function connectDB()
{
    $env = parse_ini_file('.env.local');

    $host = $env['MYSQL_HOST'];
    $user = $env['MYSQL_USER'];
    $password = $env['MYSQL_PASSWORD'];
    $database = $env['MYSQL_DATABASE'];
    $port = $env['MYSQL_PORT'];

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
