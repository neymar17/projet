<?php
session_start();

if (!isset($_SESSION['usr_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$user_id = $_SESSION['usr_id'];
$message = '';

// ---------------------------
// Récupérer les structures
// ---------------------------
$stmtStr = $pdo->query("SELECT STR_ID, STR_NAME FROM structure ORDER BY STR_NAME ASC");
$structures = $stmtStr->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Récupérer le matériel de l'utilisateur
// ---------------------------
$stmtMat = $pdo->prepare("
    SELECT mat_code, mat_categ, mat_marque
    FROM materiel
    WHERE usr_id = :usr_id
    ORDER BY mat_categ ASC, mat_marque ASC
");
$stmtMat->execute([':usr_id' => $user_id]);
$materiels = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Gestion de la création du ticket
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tic_demandeur   = trim($_POST['tic_demandeur'] ?? '');
    $tic_type        = trim($_POST['tic_type'] ?? '');
    $tic_description = trim($_POST['tic_description'] ?? null);
    $tic_urgence     = (int)($_POST['tic_urgence'] ?? 0);
    $mat_code        = trim($_POST['mat_code'] ?? null);

    if (!empty($tic_demandeur) && !empty($tic_type) && $tic_urgence > 0) {

        $stmt = $pdo->prepare("
            INSERT INTO ticket
            (tic_demandeur, tic_type, tic_statut, tic_description, tic_urgence, tic_date, usr_id, mat_code, tech_id)
            VALUES
            (:tic_demandeur, :tic_type, 'nouveau', :tic_description, :tic_urgence, NOW(), :usr_id, :mat_code, NULL)
        ");

        $stmt->execute([
            ':tic_demandeur'   => $tic_demandeur,
            ':tic_type'        => $tic_type,
            ':tic_description' => $tic_description,
            ':tic_urgence'     => $tic_urgence,
            ':usr_id'          => $_SESSION['usr_id'], // utilisateur connecté
            ':mat_code'        => $mat_code
        ]);

        $message = "✅ Ticket créé avec succès.";
    } else {
        $message = "⚠️ Merci de remplir tous les champs obligatoires.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Ticket</title>

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

            <div class="container-fluid">
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-ticket-alt"></i> Créer un Ticket
                            </div>
                            <div class="card-body">

                                <?php if (!empty($message)): ?>
                                    <div class="alert <?= strpos($message, 'succès') !== false ? 'alert-success' : 'alert-danger' ?>">
                                        <?= htmlspecialchars($message) ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST">

                                    <!-- Structure -->
                                    <div class="form-group">
                                        <label for="str_id">Structure</label>
                                        <select class="form-control" id="str_id" name="str_id" required>
                                            <option value="" selected disabled>-- Choisir une structure --</option>
                                            <?php foreach($structures as $str): ?>
                                                <option value="<?= $str['STR_ID'] ?>"><?= htmlspecialchars($str['STR_NAME']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Demandeur -->
                                    <div class="form-group">
                                        <label for="tic_demandeur">Demandeur</label>
                                        <select class="form-control" id="tic_demandeur" name="tic_demandeur" required>
                                            <option value="" selected disabled>-- Choisir un demandeur --</option>
                                            <!-- Options remplies via AJAX -->
                                        </select>
                                    </div>

                                    <!-- Type de ticket -->
                                    <div class="form-group">
                                        <label for="tic_type">Type de ticket</label>
                                        <select class="form-control" id="tic_type" name="tic_type" required>
                                            <option value="" selected disabled>-- Choisir un type --</option>
                                            <option value="intranet">Intranet</option>
                                            <option value="logiciel">Logiciel</option>
                                            <option value="materiel">Matériel</option>
                                            <option value="messagerie">Messagerie</option>
                                            <option value="navision">Navision</option>
                                            <option value="reseau">Réseau</option>
                                        </select>
                                    </div>

                                    <!-- Matériel -->
                                    <div class="form-group" id="materiel_div" style="display:none;">
                                        <label for="mat_code">Matériel concerné</label>
                                        <select class="form-control" id="mat_code" name="mat_code">
                                            <option value="">-- Choisir le matériel --</option>
                                            <?php foreach ($materiels as $mat): ?>
                                                <option value="<?= htmlspecialchars($mat['mat_code']) ?>">
                                                    <?= htmlspecialchars($mat['mat_categ']) ?> - <?= htmlspecialchars($mat['mat_marque']) ?> (<?= htmlspecialchars($mat['mat_code']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Urgence -->
                                    <div class="form-group">
                                        <label for="tic_urgence">Urgence</label>
                                        <select class="form-control" id="tic_urgence" name="tic_urgence" required>
                                            <option value="" selected disabled>-- Choisir l'urgence --</option>
                                            <option value="1">Basse</option>
                                            <option value="2">Moyenne</option>
                                            <option value="3">Haute</option>
                                            <option value="4">Urgente</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="tic_description">Description</label>
                                        <textarea class="form-control" id="tic_description" name="tic_description" rows="4"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> Créer le Ticket
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto text-center">
                <span>© <?= date("Y") ?> Gestion Tickets</span>
            </div>
        </footer>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // Afficher ou cacher le matériel
    $("#tic_type").on("change", function(){
        if(this.value === "materiel"){
            $("#materiel_div").slideDown();
        } else {
            $("#materiel_div").slideUp();
            $("#mat_code").val('');
        }
    });

    // Quand la structure change, remplir demandeurs
    $("#str_id").on("change", function(){
        const str_id = $(this).val();
        const demandeurSelect = $("#tic_demandeur");

        demandeurSelect.empty();
        demandeurSelect.append('<option value="" selected disabled>-- Choisir un demandeur --</option>');

        if(!str_id) return;

        $.ajax({
            url: "get_users_by_structure.php",
            method: "GET",
            data: { str_id: str_id, term: "" },
            dataType: "json",
            success: function(data){
                if(data.length > 0){
                    data.forEach(user => {
                        demandeurSelect.append(`<option value="${user.usr_id}">${user.usr_nom}</option>`);
                    });
                } else {
                    demandeurSelect.append('<option value="" disabled>Aucun utilisateur pour cette structure</option>');
                }
            },
            error: function(){
                demandeurSelect.append('<option value="" disabled>Erreur de chargement</option>');
            }
        });
    });

    // Quand on change le demandeur, remplir le matériel
    $("#tic_demandeur").on("change", function(){
        const usr_id = $(this).val();
        const materielSelect = $("#mat_code");

        materielSelect.empty();
        materielSelect.append('<option value="">-- Choisir le matériel --</option>');

        if(!usr_id) return;

        $.ajax({
            url: "get_materiel_by_user.php",
            method: "GET",
            data: { usr_id: usr_id },
            dataType: "json",
            success: function(data){
                if(data.length > 0){
                    data.forEach(mat => {
                        materielSelect.append(`<option value="${mat.mat_code}">${mat.mat_categ} - ${mat.mat_marque} (${mat.mat_code})</option>`);
                    });
                } else {
                    materielSelect.append('<option value="" disabled>Aucun matériel affecté</option>');
                }
            },
            error: function(){
                materielSelect.append('<option value="" disabled>Erreur de chargement</option>');
            }
        });
    });

});

</script>

</body>
</html>
