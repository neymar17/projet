<?php
require 'db.php';
session_start();

/*
  Cette page permet à l'admin de créer :
  - un USER normal  => table user
  - un TECHNICIEN   => table technicien
  - un ADMIN        => table technicien
*/


// --- Partie AJAX pour récupérer les départements ---
if (isset($_GET['action']) && $_GET['action'] === 'get_departements' && isset($_GET['id_direction'])) {
    $id_direction = $_GET['id_direction'];

    $stmt = $pdo->prepare("SELECT ID_DEPARTEMENT, NOM_DEPARTEMENT 
                           FROM departement 
                           WHERE ID_DIRECTION = ?");
    $stmt->execute([$id_direction]);
    $departements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($departements);
    exit;
}


// --- Vérifier si admin connecté ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}


// --- Partie inscription normale ---
$selectedRole = $_POST['role'] ?? 'user';


// Récupérer toutes les directions
try {
    $directions = $pdo->query("SELECT ID_DIRECTION, NOM_DIRECTION 
                              FROM direction 
                              ORDER BY NOM_DIRECTION ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $directions = [];
    $error = "Erreur récupération directions : " . $e->getMessage();
}


// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'user';

    $id_direction = $_POST['id_direction'] ?? null;
    $id_departement = $_POST['id_departement'] ?? null;


    if (empty($nom) || empty($email) || empty($password) || empty($password2)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $password2) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {

        try {
            // Vérifier email dans user
            $stmtCheck1 = $pdo->prepare("SELECT COUNT(*) FROM user WHERE usr_email = :email");
            $stmtCheck1->execute([':email' => $email]);

            // Vérifier email dans technicien
            $stmtCheck2 = $pdo->prepare("SELECT COUNT(*) FROM technicien WHERE tech_email = :email");
            $stmtCheck2->execute([':email' => $email]);

            if ($stmtCheck1->fetchColumn() > 0 || $stmtCheck2->fetchColumn() > 0) {
                $error = "Cet email est déjà utilisé.";
            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // ==========================
                // INSERT حسب role
                // ==========================

                // USER normal
                if ($role === 'user') {

                    $sql = "INSERT INTO user (usr_nom, usr_email, usr_password, usr_role, usr_date_creation, usr_date_update)
                            VALUES (:nom, :email, :password, 'user', NOW(), NOW())";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nom' => $nom,
                        ':email' => $email,
                        ':password' => $passwordHash
                    ]);

                    $userId = $pdo->lastInsertId();

                    // Affectation département (si existe)
                    if (!empty($id_departement)) {
                        $stmtAff = $pdo->prepare("INSERT INTO user_affectation (user_id, id_departement, id_service, id_centre, date_affectation)
                                                  VALUES (:user_id, :id_departement, NULL, NULL, NOW())");
                        $stmtAff->execute([
                            ':user_id' => $userId,
                            ':id_departement' => $id_departement
                        ]);
                    }

                    $success = "Compte utilisateur créé avec succès !";

                }
                // TECHNICIEN ou ADMIN
                elseif ($role === 'technicien' || $role === 'admin') {

                    $sql = "INSERT INTO technicien (tech_nom, tech_email, tech_password, tech_role, tech_date_creation, tech_date_update)
                            VALUES (:nom, :email, :password, :role, NOW(), NOW())";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nom' => $nom,
                        ':email' => $email,
                        ':password' => $passwordHash,
                        ':role' => $role
                    ]);

                    $success = "Compte $role créé avec succès !";

                } else {
                    $error = "Rôle invalide.";
                }
            }

        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                <div class="col-lg-7">
                    <div class="p-5">

                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Créer un compte</h1>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="POST" class="user">

                            <div class="form-group">
                                <label>Nom</label>
                                <input type="text" name="nom" class="form-control form-control-user" required
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control form-control-user" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Mot de passe</label>
                                    <input type="password" name="password" class="form-control form-control-user" required>
                                </div>
                                <div class="col-sm-6">
                                    <label>Confirmation</label>
                                    <input type="password" name="password_confirm" class="form-control form-control-user" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Rôle</label>
                                <select id="roleSelect" name="role" class="form-control" onchange="toggleDirection()">
                                    <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="technicien" <?= $selectedRole === 'technicien' ? 'selected' : '' ?>>Technicien</option>
                                    <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>

                            <div id="direction-container" style="<?= $selectedRole === 'user' ? 'display:block;' : 'display:none;' ?>">
                                <label>Direction</label>
                                <select name="id_direction" class="form-control" id="directionSelect">
                                    <option value="">-- Sélectionnez une direction --</option>
                                    <?php foreach ($directions as $dir): ?>
                                        <option value="<?= $dir['ID_DIRECTION'] ?>"
                                            <?= (isset($_POST['id_direction']) && $_POST['id_direction'] == $dir['ID_DIRECTION']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dir['NOM_DIRECTION']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <label style="margin-top:10px;">Département</label>
                                <select name="id_departement" class="form-control" id="departementSelect">
                                    <option value="">-- Sélectionnez un département --</option>
                                </select>
                            </div>

                            <br>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Créer le compte
                            </button>
                        </form>

                        <hr>
                        <div class="text-center">
                            <a class="small" href="login.php">Déjà un compte ? Login</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDirection() {
    const role = document.getElementById('roleSelect').value;
    const dirContainer = document.getElementById('direction-container');
    const dirSelect = document.getElementById('directionSelect');
    const depSelect = document.getElementById('departementSelect');

    if (role === 'user') {
        dirContainer.style.display = 'block';
        dirSelect.setAttribute('required', 'required');
        depSelect.setAttribute('required', 'required');
    } else {
        dirContainer.style.display = 'none';
        dirSelect.removeAttribute('required');
        depSelect.removeAttribute('required');
    }
}

// Charger les départements quand la direction change
document.getElementById('directionSelect').addEventListener('change', function() {
    const directionId = this.value;
    const depSelect = document.getElementById('departementSelect');
    depSelect.innerHTML = '<option value="">-- Sélectionnez un département --</option>';

    if (!directionId) {
        depSelect.disabled = true;
        return;
    }

    fetch('register.php?action=get_departements&id_direction=' + directionId)
        .then(response => response.json())
        .then(data => {
            data.forEach(dep => {
                const option = document.createElement('option');
                option.value = dep.ID_DEPARTEMENT;
                option.textContent = dep.NOM_DEPARTEMENT;
                depSelect.appendChild(option);
            });
            depSelect.disabled = false;

            <?php if(isset($_POST['id_departement'])): ?>
                depSelect.value = "<?= $_POST['id_departement'] ?>";
            <?php endif; ?>
        })
        .catch(err => console.error(err));
});

document.addEventListener('DOMContentLoaded', function() {
    toggleDirection();
    document.getElementById('departementSelect').disabled = true;
});
</script>

</body>
</html>
