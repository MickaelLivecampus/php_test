<?php

session_start();

require_once('src/security/connexion.php');
require_once('src/database/db.php');
require_once('src/utils/form.php');

if (!isset($_SESSION['token']) || !isTokenValid($_SESSION['token'])) {
    header("Location: /");
}

$vehicules = getVehicules();
$client_id_options = getClients();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $marque = strip_tags($_POST['marque']);
    $modele = strip_tags($_POST['modele']);
    $annee = strip_tags($_POST['annee']);
    $client_id = strip_tags($_POST['client_id']);

    try {
        checkFields(['marque' => $marque, 'modele' => $modele, 'annee' => $annee]);
        createVehicule(['marque' => $marque, 'modele' => $modele, 'annee' => $annee, 'client_id' => $client_id]);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Garage train</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/css/styles.min.css">
</head>

<body>
    <!-- GET LIST -->
    <div class="container mt-4">
        <h2>Tableau des Véhicules</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Marque</th>
                    <th scope="col">Modèle</th>
                    <th scope="col">Année</th>
                    <th scope="col">Client</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicules as $vehicle): ?>
                    <tr>
                        <td><?= $vehicle["marque"] ?></td>
                        <td><?= $vehicle["modele"] ?></td>
                        <td><?= $vehicle["annee"] ?></td>
                        <td><?= $vehicle["client"]["nom"] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- ADD FORM -->
    <div id="vehicule-form" class="container mt-5">
        <h1 class="mb-4">Tableau des Véhicules</h1>
        <form action="vehicule.php" method="post">
            <div class="mb-3">
                <label for="marque" class="form-label">Marque :</label>
                <input type="text" class="form-control" id="marque" name="marque">
            </div>

            <div class="mb-3">
                <label for="modele" class="form-label">Modèle :</label>
                <input type="text" class="form-control" id="modele" name="modele">
            </div>

            <div class="mb-3">
                <label for="annee" class="form-label">Année :</label>
                <input type="number" class="form-control" id="annee" name="annee" min="1900" max="2023">
            </div>

            <div class="mb-3">
                <label for="clientSelect" class="form-label">Client :</label>
                <select name="client_id" id="clientSelect" class="form-select">
                    <?php foreach ($client_id_options as $option): ?>
                        <option value="<?php echo $option["id"]; ?>">
                            <?php echo $option["nom"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Créer Voiture</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>


</script>

</html>