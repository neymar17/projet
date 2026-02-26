<?php
session_start();
require 'db.php';

// Vérifier si connecté
if (!isset($_SESSION['tech_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer toutes les structures
$structures = $pdo->query("SELECT str_id, str_name FROM structure ORDER BY str_name ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Tous les utilisateurs
// ---------------------------
$stmtUsers = $pdo->prepare("
    SELECT 
        u.usr_id, 
        u.usr_nom, 
        u.usr_telephone, 
        u.usr_email, 
        u.usr_date_creation, 
        u.usr_date_update, 
        s.str_name AS structure, 
        u.str_id
    FROM user u
    LEFT JOIN structure s ON u.str_id = s.str_id
    ORDER BY u.usr_id DESC
");
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Tous les techniciens
// ---------------------------
$stmtTech = $pdo->prepare("
    SELECT 
        t.tech_id, 
        t.tech_nom, 
        t.tech_email, 
        t.tech_date_creation, 
        t.tech_date_update,
        t.tech_role
    FROM technicien t
    ORDER BY t.tech_id DESC
");
$stmtTech->execute();
$techniciens = $stmtTech->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Gestion Users & Techniciens</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>
<body id="page-top">
<div id="wrapper">
<?php include('fragment/sidebar.php'); ?>
<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
  <?php include('fragment/navbar.php'); ?>

  <div class="container-fluid mt-4">

    <!-- TABLEAU USERS -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users"></i> Liste des utilisateurs</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="usersTable" width="100%" cellspacing="0">
            <thead class="bg-light">
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Structure</th>
                <th>Date création</th>
                <th>Date update</th>
                <th style="width:200px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['usr_id']) ?></td>
                <td><?= htmlspecialchars($u['usr_nom']) ?></td>
                <td><?= htmlspecialchars($u['usr_telephone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['usr_email']) ?></td>
                <td><?= !empty($u['structure']) ? htmlspecialchars($u['structure']) : '<span class="text-muted">Non défini</span>' ?></td>
                <td><?= htmlspecialchars($u['usr_date_creation'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['usr_date_update'] ?? '-') ?></td>
                <td>
                  <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editUser<?= $u['usr_id'] ?>">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="supprimer.php?id=<?= urlencode($u['usr_id']) ?>&type=user" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce user ?')">
                    <i class="fas fa-trash"></i> Supprimer
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TABLEAU TECHNICIENS -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-cog"></i> Liste des techniciens</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="techTable" width="100%" cellspacing="0">
            <thead class="bg-light">
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Date création</th>
                <th>Date update</th>
                <th style="width:200px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($techniciens as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['tech_id']) ?></td>
                <td><?= htmlspecialchars($t['tech_nom']) ?></td>
                <td><?= htmlspecialchars($t['tech_email']) ?></td>
                <td><?= htmlspecialchars($t['tech_role']) ?></td>
                <td><?= htmlspecialchars($t['tech_date_creation'] ?? '-') ?></td>
                <td><?= htmlspecialchars($t['tech_date_update'] ?? '-') ?></td>
                <td>
                  <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editTech<?= $t['tech_id'] ?>">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="supprimer.php?id=<?= urlencode($t['tech_id']) ?>&type=tech" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce technicien ?')">
                    <i class="fas fa-trash"></i> Supprimer
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function(){
    $('#usersTable').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" },
        "pageLength": 10,
        "lengthMenu": [5,10,25,50,100],
        "responsive": true,
        "order": [[0,"desc"]]
    });

    $('#techTable').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" },
        "pageLength": 10,
        "lengthMenu": [5,10,25,50,100],
        "responsive": true,
        "order": [[0,"desc"]]
    });
});
</script>
</body>
</html>
