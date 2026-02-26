<?php
session_start();

if (!isset($_SESSION['tech_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer tous les tickets avec nom demandeur et technicien
    $stmt = $pdo->query("
        SELECT 
            t.*, 
            u.usr_nom AS demandeur_nom,
            tech.usr_nom AS technicien_nom
        FROM ticket t
        LEFT JOIN user u ON t.tic_demandeur = u.usr_id
        LEFT JOIN user tech ON t.tech_id = tech.usr_id
        ORDER BY t.tic_date DESC
    ");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier si au moins un ticket de type 'materiel' existe
    $hasMateriel = false;
    foreach ($tickets as $ticket) {
        if ($ticket['tic_type'] === 'materiel') {
            $hasMateriel = true;
            break;
        }
    }

} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Liste des Tickets</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet" />
  <link href="css/sb-admin-2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
</head>

<body id="page-top">
  <div id="wrapper">

    <?php include('fragment/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include('fragment/navbar.php'); ?>

        <div class="container-fluid">
          <h1 class="h3 mb-4 text-gray-800">Liste des Tickets</h1>

          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover" id="techTable" width="100%" cellspacing="0">
                  <thead class="bg-light">
                    <tr>
                      <th>ID</th>
                      <th>Demandeur</th>
                      <th>Technicien</th>
                      <th>Type</th>
                      <th>Statut</th>
                      <th>Description</th>
                      <th>Urgence</th>
                      <th>Date</th>
                      <?php if ($hasMateriel): ?>
                        <th>Matériel</th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($tickets)): ?>
                      <?php foreach ($tickets as $ticket): ?>
                        <tr>
                          <td><?= htmlspecialchars($ticket['tic_id']) ?></td>
                          <td><?= htmlspecialchars($ticket['demandeur_nom'] ?? 'Inconnu') ?></td>
                          <td><?= htmlspecialchars($ticket['technicien_nom'] ?? 'Non attribué') ?></td>
                          <td><?= htmlspecialchars($ticket['tic_type']) ?></td>
                          <td><?= htmlspecialchars($ticket['tic_statut']) ?></td>
                          <td><?= htmlspecialchars($ticket['tic_description']) ?></td>
                          <td>
                            <?php
                              switch ($ticket['tic_urgence']) {
                                case 1: echo 'Basse'; break;
                                case 2: echo 'Moyenne'; break;
                                case 3: echo 'Haute'; break;
                                case 4: echo 'Urgente'; break;
                                default: echo 'Inconnue';
                              }
                            ?>
                          </td>
                          <td><?= htmlspecialchars($ticket['tic_date']) ?></td>
                          <?php if ($hasMateriel): ?>
                            <td>
                              <?= $ticket['tic_type'] === 'materiel' ? htmlspecialchars($ticket['mat_code'] ?? '-') : '-' ?>
                            </td>
                          <?php endif; ?>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="<?= $hasMateriel ? 9 : 8 ?>" class="text-center">Aucun ticket trouvé</td>
                      </tr>
                    <?php endif; ?>
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
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../js/sb-admin-2.min.js"></script>

  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#techTable').DataTable({
        // si la colonne Matériel n'est pas affichée, on adapte DataTables
        <?php if (!$hasMateriel): ?>
        "columnDefs": [
          { "visible": false, "targets": [8] }
        ]
        <?php endif; ?>
      });
    });
  </script>

</body>

</html>