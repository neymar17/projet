<?php
session_start();
require 'db.php';

// Vérifier si connecté
if (!isset($_SESSION['tech_id'])) {
    header("Location: login.php");
    exit;
}

// Charger les matériels avec utilisateur et emplacement
$materiels = $pdo->query("
    SELECT 
        m.mat_code, 
        m.mat_categ, 
        m.mat_marque, 
        m.mat_statut, 
        m.mat_fin_gar,
        u.usr_id, 
        u.usr_nom,
        e.emp_id, 
        CONCAT(e.emp_localisation, ' - ', e.emp_batiment, ' - ', e.emp_etage, ' - ', e.emp_num_bureau) AS emp_label
    FROM materiel m
    LEFT JOIN user u ON m.usr_id = u.usr_id
    LEFT JOIN emplacement e ON m.emp_id = e.emp_id
    ORDER BY m.mat_code ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Charger utilisateurs et emplacements pour select
$users = $pdo->query("SELECT usr_id, usr_nom FROM user ORDER BY usr_nom ASC")->fetchAll(PDO::FETCH_ASSOC);

$emplacements = $pdo->query("
    SELECT emp_id, 
           CONCAT(emp_localisation,' - ',emp_batiment,' - ',emp_etage,' - ',emp_num_bureau) AS emp_label 
    FROM emplacement 
    ORDER BY emp_localisation ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Liste des Matériels</title>

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- SB Admin CSS -->
<link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <?php include('fragment/sidebar.php'); ?>
    <!-- End Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Navbar -->
            <?php include('fragment/navbar.php'); ?>
            <!-- End Navbar -->

            <div class="container-fluid mt-4">

                <!-- Flash messages -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" id="flashMessage"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" id="flashMessage"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-laptop"></i> Liste des Matériels
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Catégorie</th>
                                        <th>Marque</th>
                                        <th>Statut</th>
                                        <th>Utilisateur</th>
                                        <th>Emplacement</th>
                                        <th>Fin Garantie</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($materiels as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['mat_code']) ?></td>
                                        <td><?= htmlspecialchars($m['mat_categ'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($m['mat_marque'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($m['mat_statut'] ?? '-') ?></td>
                                        <td><?= $m['usr_nom'] ? htmlspecialchars($m['usr_nom']) : '<span class="text-muted">Aucun</span>' ?></td>
                                        <td><?= $m['emp_label'] ? htmlspecialchars($m['emp_label']) : '<span class="text-muted">Aucun</span>' ?></td>
                                        <td><?= $m['mat_fin_gar'] ? htmlspecialchars($m['mat_fin_gar']) : '<span class="text-muted">-</span>' ?></td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm editBtn"
                                                    data-code="<?= htmlspecialchars($m['mat_code'], ENT_QUOTES) ?>"
                                                    data-categ="<?= htmlspecialchars($m['mat_categ'], ENT_QUOTES) ?>"
                                                    data-marque="<?= htmlspecialchars($m['mat_marque'], ENT_QUOTES) ?>"
                                                    data-statut="<?= htmlspecialchars($m['mat_statut'], ENT_QUOTES) ?>"
                                                    data-fin="<?= htmlspecialchars($m['mat_fin_gar'], ENT_QUOTES) ?>"
                                                    data-usr="<?= htmlspecialchars($m['usr_id'], ENT_QUOTES) ?>"
                                                    data-emp="<?= htmlspecialchars($m['emp_id'], ENT_QUOTES) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <a href="supprimer.php?id=<?= urlencode($m['mat_code']) ?>&type=materiel" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Voulez-vous vraiment supprimer ce matériel ?')">
                                               <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="update.php" method="POST">
                <input type="hidden" name="action_type" value="update_materiel">
                <input type="hidden" id="mat_code_old" name="mat_code_old">

                <div class="modal-header">
                    <h5 class="modal-title text-primary"><i class="fas fa-edit"></i> Modifier Matériel</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="mat_code" id="mat_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <input type="text" name="mat_categ" id="mat_categ" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Marque</label>
                        <input type="text" name="mat_marque" id="mat_marque" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <input type="text" name="mat_statut" id="mat_statut" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Fin de Garantie</label>
                        <input type="date" name="mat_fin_gar" id="mat_fin_gar" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <select name="usr_id_mat" id="usr_id_mat" class="form-control">
                            <option value="">-- Choisir un utilisateur --</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['usr_id'] ?>"><?= htmlspecialchars($u['usr_nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Emplacement</label>
                        <select name="emp_id" id="emp_id" class="form-control" required>
                            <option value="">-- Choisir un emplacement --</option>
                            <?php foreach($emplacements as $e): ?>
                                <option value="<?= $e['emp_id'] ?>"><?= htmlspecialchars($e['emp_label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function(){

    // DataTable
    $('#dataTable').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
    });

    // Flash message disparition
    setTimeout(function() { $('#flashMessage').fadeOut('slow'); }, 3000);

    // Click bouton modifier
    $('.editBtn').click(function(){
        $('#mat_code_old').val($(this).data('code'));
        $('#mat_code').val($(this).data('code'));
        $('#mat_categ').val($(this).data('categ'));
        $('#mat_marque').val($(this).data('marque'));
        $('#mat_statut').val($(this).data('statut'));
        $('#mat_fin_gar').val($(this).data('fin'));
        $('#usr_id_mat').val($(this).data('usr'));
        $('#emp_id').val($(this).data('emp'));

        $('#editModal').modal('show');
    });

});
</script>

</body>
</html>
