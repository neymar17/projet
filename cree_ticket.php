<?php
session_start();

if (!isset($_SESSION['usr_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$user_id = $_SESSION['usr_id'];
$message = '';

// Liste matériel

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tic_demandeur     = trim($_POST['tic_demandeur'] ?? '');
    $tic_type        = trim($_POST['tic_type'] ?? '');
    $tic_description = trim($_POST['tic_description'] ?? '');
    $tic_urgence     = (int)($_POST['tic_urgence'] ?? 0);
    $mat_code        = trim($_POST['mat_code'] ?? '');

    if ($tic_description === '') {
        $tic_description = null;
    }

    if ($mat_code === '') {
        $mat_code = null;
    }

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
            ':usr_id'          => $user_id,
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

                                    <!-- tic_demande -->
                                    <div class="form-group position-relative">
                                            <label for="tic_demandeur">Demandeur</label>
                                            <input type="text" class="form-control" id="tic_demandeur" name="tic_demandeur" autocomplete="on" required>

                                            <div id="autocomplete-list" class="list-group" style="
                                                position:absolute;
                                                width:100%;
                                                z-index:9999;
                                                max-height:200px;
                                                overflow-y:auto;
                                                display:none;
                                            "></div>
                                        </div>


                                    <!-- tic_type -->
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

                                    <!-- tic_urgence -->
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

                                    <!-- mat_code -->
                                    <div class="form-group" id="materiel_div" style="display:none;">
                                        <label for="mat_code">Matériel concerné</label>
                                        <select class="form-control" id="mat_code" name="mat_code">
                                            <option value="">-- Choisir le matériel --</option>
                                            <?php foreach ($materiels as $mat): ?>
                                                <option value="<?= htmlspecialchars($mat['mat_code']) ?>">
                                                    <?= htmlspecialchars($mat['mat_inv']) ?> (<?= htmlspecialchars($mat['mat_code']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- tic_description -->
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

<script>
    <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

const ticType = document.getElementById('tic_type');
const materielDiv = document.getElementById('materiel_div');

ticType.addEventListener('change', function () {
    if (this.value === 'materiel') {
        materielDiv.style.display = 'block';
    } else {
        materielDiv.style.display = 'none';
        document.getElementById('mat_code').value = '';
    }
});
</script>
<script>
console.log("jquery test =", typeof $);
</script>

<script>
$(document).ready(function () {

    $("#tic_demande").keyup(function () {
        let term = $(this).val();

        if (term.length < 1) {
            $("#autocomplete-list").hide();
            return;
        }

        $.ajax({
            url: "get_users.php",
            method: "GET",
            data: { term: term },
            dataType: "json",
            success: function (data) {
                let html = "";

                if (data.length > 0) {
                    data.forEach(function (user) {
                        html += `<a href="#" class="list-group-item list-group-item-action autocomplete-item" 
                                    data-id="${user.usr_id}">
                                    ${user.usr_nom}
                                 </a>`;
                    });
                } else {
                    html = `<div class="list-group-item">Aucun résultat</div>`;
                }

                $("#autocomplete-list").html(html).show();
            }
        });
    });

    // Click sur suggestion
   $(document).on("click", ".autocomplete-item", function () {
    $("#tic_demandeur").val($(this).text().trim());
    $("#autocomplete-list").hide();
});


    // cacher si clique dehors
    $(document).click(function (e) {
        if (!$(e.target).closest("#tic_demandeur, #autocomplete-list").length) {
            $("#autocomplete-list").hide();
        }
    });

});
</script>
</script>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
