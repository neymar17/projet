<?php
session_start();
require 'db.php';

if (!isset($_SESSION['tech_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

/* ====== Charger les utilisateurs ====== */
$users = $pdo->query("SELECT usr_id, usr_nom FROM user ORDER BY usr_nom ASC")
             ->fetchAll(PDO::FETCH_ASSOC);

/* ====== Charger les emplacements ====== */
$emplacements = $pdo->query("
    SELECT emp_id, CONCAT(emp_localisation, ' - ', emp_batiment, ' - ', emp_etage, ' - ', emp_num_bureau) AS emp_label
    FROM emplacement
    ORDER BY emp_localisation ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* ====== Définir les catégories et statuts possibles ====== */
$categories = ['Ordinateur', 'PC Portable', 'Écran', 'Imprimante', 'Scanner'];
$statuts = ['Disponible', 'Attribué', 'En panne', 'Réformé', 'Perdu'];

/* ====== Traitement du formulaire ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mat_code   = trim($_POST['mat_code'] ?? '');
    $mat_categ  = trim($_POST['mat_categ'] ?? '');
    $mat_marque = trim($_POST['mat_marque'] ?? '');
    $mat_statut = trim($_POST['mat_statut'] ?? 'Disponible'); // valeur par défaut
    $mat_fin_gar= trim($_POST['mat_fin_gar'] ?? '');
   $usr_id = $_POST['usr_id'] ?? '';

if (empty($usr_id)) {
    die("Erreur: لازم تختار utilisateur !");
}

    $emp_id     = $_POST['emp_id'] ?? null;

    // Si vide, mettre NULL
    $mat_fin_gar = $mat_fin_gar === '' ? null : $mat_fin_gar;
   

    // Vérifier que mat_statut et mat_categ sont valides
    $mat_statut = in_array($mat_statut, $statuts) ? $mat_statut : 'Disponible';
    $mat_categ  = in_array($mat_categ, $categories) ? $mat_categ : null;

    if ($mat_code && $mat_categ && $emp_id) {

        // Vérifier si le mat_code existe déjà
        $check = $pdo->prepare("SELECT COUNT(*) FROM materiel WHERE mat_code = ?");
        $check->execute([$mat_code]);

        if ($check->fetchColumn() > 0) {
            $message = "⚠️ Ce code matériel existe déjà !";
        } else {
            // Insertion du matériel
            $stmt = $pdo->prepare("
                INSERT INTO materiel
                    (mat_code, mat_categ, mat_marque, mat_statut, mat_fin_gar, usr_id, emp_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $mat_code,
                $mat_categ,
                $mat_marque,
                $mat_statut,
                $mat_fin_gar,
                $usr_id,
                $emp_id
            ]);

            $message = "✅ Matériel ajouté avec succès.";
        }

    } else {
        $message = "⚠️ Merci de remplir tous les champs obligatoires (code, catégorie, emplacement).";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard User</title>

    <!-- Fonts et styles SB Admin 2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

    <?php include('fragment/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include('fragment/navbar.php'); ?>

            <div class="container-fluid mt-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-plus"></i> Ajouter Matériel
                                </h6>
                            </div>

                            <div class="card-body">

                                <?php if ($message): ?>
                                    <div class="alert <?= strpos($message, 'succès') !== false ? 'alert-success' : 'alert-danger' ?>">
                                        <?= htmlspecialchars($message) ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST">

                                    <!-- Code matériel -->
                                    <div class="form-group">
                                        <label>Code</label>
                                        <input type="text" name="mat_code" class="form-control" required>
                                    </div>

                                    <!-- Catégorie -->
                                    <div class="form-group">
                                        <label>Catégorie</label>
                                        <select name="mat_categ" class="form-control" required>
                                            <option value="">-- Choisir une catégorie --</option>
                                            <?php foreach ($categories as $c): ?>
                                                <option value="<?= $c ?>"><?= $c ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Marque -->
                                    <div class="form-group">
                                        <label>Marque</label>
                                        <input type="text" name="mat_marque" class="form-control">
                                    </div>

                                    <!-- Statut -->
                                    <div class="form-group">
                                        <label>Statut</label>
                                        <select name="mat_statut" class="form-control" required>
                                            <?php foreach ($statuts as $s): ?>
                                                <option value="<?= $s ?>" <?= $s === 'Disponible' ? 'selected' : '' ?>><?= $s ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Fin de garantie -->
                                    <div class="form-group">
                                        <label>Fin de Garantie (optionnel)</label>
                                        <input type="date" name="mat_fin_gar" class="form-control">
                                    </div>

                                    <!-- Utilisateur -->
                                    <div class="form-group">
                                        <label>Utilisateur (optionnel)</label>
                                       <select name="usr_id" id="usr_id" class="form-control" required>
                                        <option value="">-- Choisir un utilisateur --</option>
                                        <?php foreach($users as $u): ?>
                                            <option value="<?= $u['usr_id'] ?>">
                                                <?= htmlspecialchars($u['usr_nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>


                                    <!-- Emplacement -->
                                    <div class="form-group">
                                        <label>Emplacement</label>
                                        <select name="emp_id" class="form-control" required>
                                            <option value="">-- Choisir un emplacement --</option>
                                            <?php foreach ($emplacements as $e): ?>
                                                <option value="<?= $e['emp_id'] ?>"><?= htmlspecialchars($e['emp_label']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajouter
                                    </button>

                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
