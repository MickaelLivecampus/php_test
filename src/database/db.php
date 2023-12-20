<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
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
        die("La connexion    la base de donn  es a   chou   : " . $conn->connect_error);
    }

    return $conn;
}

function getVehicules()
{
    $conn = connectDB();

    $results = $conn->query("SELECT vehicules.id AS vehicule_id, vehicules.marque, vehicules.modele, vehicules.annee, vehicules.client_id, clients.nom FROM vehicules JOIN clients ON vehicules.client_id = clients.id");
    $rows = $results->fetch_all(MYSQLI_ASSOC);
    
    $data = array_map(function ($item) {
        return [
            'id' => $item['vehicule_id'], 
            'marque' => $item['marque'],
            'modele' => $item['modele'],
            'annee' => $item['annee'],
            'client' => [
                'id' => $item['client_id'],
                'nom' => $item['nom']
            ]
        ];
    }, $rows);
    
    return $data;    
}

function getClients(): array
{
    $conn = connectDB();

    $results = $conn->query("SELECT id, nom FROM clients");
    $rows = $results->fetch_all(MYSQLI_ASSOC);

    return $rows;
}

function createVehicule($fields): void
{
    $conn = connectDB();

    $marque = $fields["marque"];
    $modele = $fields["modele"];
    $annee = $fields["annee"];
    $client_id = $fields["client_id"];

    $stmt = $conn?->prepare(
        'INSERT INTO vehicules (marque,modele, annee, client_id) VALUES(?,?,?,?)'
    );
    $stmt->bind_param('ssii', $marque, $modele, $annee, $client_id);
    $stmt->execute();
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
