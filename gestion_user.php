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

// Récupérer tous les users avec la structure
$stmt = $pdo->prepare("
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
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Dashboard</title>

  <!-- Custom fonts for this template-->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
<?php include('fragment/sidebar.php'); ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
<?php include('fragment/navbar.php'); ?>
      <!-- End Navbar -->

      <div class="container-fluid mt-4">
        <!-- Flash messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="flashMessage" class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div id="flashMessage" class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users"></i> Liste des utilisateurs
                </h6>
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
                                        <button class="btn btn-sm btn-warning"
                                            data-toggle="modal"
                                            data-target="#editModal<?= $u['usr_id'] ?>">
                                            <i class="fas fa-edit"></i> 
                                        </button>

                                        <a href="supprimer.php?id=<?= urlencode($u['usr_id']) ?>&type=user"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce user ?')">
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
      <!-- End container-fluid -->

      </div>
      <!-- End content -->

    </div>
    <!-- End content-wrapper -->

  </div>
  <!-- End wrapper -->

  <!-- MODALS -->
  <?php foreach ($users as $u): ?>
      <div class="modal fade" id="editModal<?= $u['usr_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <form action="update.php" method="POST">
                      <input type="hidden" name="action_type" value="update_user">
                      <input type="hidden" name="usr_id" value="<?= $u['usr_id'] ?>">

                      <div class="modal-header">
                          <h5 class="modal-title">
                              Modifier User #<?= htmlspecialchars($u['usr_id']) ?>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                          </button>
                      </div>

                      <div class="modal-body">
                          <div class="form-group">
                              <label>Nom</label>
                              <input type="text" name="usr_nom" class="form-control"
                                  value="<?= htmlspecialchars($u['usr_nom']) ?>" required>
                          </div>

                          <div class="form-group">
                              <label>Téléphone</label>
                              <input type="text" name="usr_telephone" class="form-control"
                                  value="<?= htmlspecialchars($u['usr_telephone'] ?? '') ?>">
                          </div>

                          <div class="form-group">
                              <label>Email</label>
                              <input type="email" name="usr_email" class="form-control"
                                  value="<?= htmlspecialchars($u['usr_email']) ?>" required>
                          </div>

                          <div class="form-group">
                              <label>Structure</label>
                              <select name="str_id" class="form-control">
                                  <option value="">-- Choisir structure --</option>
                                  <?php foreach ($structures as $s): ?>
                                      <option value="<?= $s['str_id'] ?>"
                                          <?= (!empty($u['str_id']) && $u['str_id'] == $s['str_id']) ? 'selected' : '' ?>>
                                          <?= htmlspecialchars($s['str_name']) ?>
                                      </option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                      </div>

                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">
                              Annuler
                          </button>
                          <button type="submit" class="btn btn-primary">
                              Enregistrer
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  <?php endforeach; ?>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/sb-admin-2.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script>
  $(document).ready(function(){

      // DataTable activée
      $('#usersTable').DataTable({
          "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" },
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50, 100],
          "responsive": true,
          "order": [[0, "desc"]]
      });

      // Flash message disparition
      setTimeout(function() { $('#flashMessage').fadeOut(1000); }, 3000);

  });
  </script>

</body>
</html>
