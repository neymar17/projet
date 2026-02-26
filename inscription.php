<?php
require 'db.php';
session_start();

// Récupérer tous les départements et structures
try {
    $departements = $pdo->query("SELECT STR_ID, STR_NAME FROM structure WHERE STR_TYPE='departement' ORDER BY STR_NAME ASC")->fetchAll(PDO::FETCH_ASSOC);
    $structures = $pdo->query("SELECT STR_ID, STR_NAME, STR_TYPE, PARENT_ID FROM structure WHERE STR_TYPE IN ('centre','service')")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $departements = [];
    $structures = [];
    $error = "Erreur récupération structures : " . $e->getMessage();
}

// Récupération du rôle sélectionné
$selectedRole = $_POST['role'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $id_departement = $_POST['id_departement'] ?? null;
    $id_structure = $_POST['id_structure'] ?? null;

    // Validation
    if (empty($nom) || empty($prenom) |empty($email) || empty($password) || empty($password2)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $password2) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif ($role === 'user' && empty($id_departement)) {
        $error = "Vous devez choisir au moins un département.";
    } else {
        try {
            // Vérifier email existant
            $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM user WHERE usr_email = :email");
            $stmtCheckUser->execute([':email' => $email]);
            $stmtCheckTech = $pdo->prepare("SELECT COUNT(*) FROM technicien WHERE tech_email = :email");
            $stmtCheckTech->execute([':email' => $email]);

            if ($stmtCheckUser->fetchColumn() > 0 || $stmtCheckTech->fetchColumn() > 0) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                if ($role === 'user') {
                    $structureId = !empty($id_structure) ? $id_structure : $id_departement;

                  $stmt = $pdo->prepare("
                                    INSERT INTO user (usr_nom, usr_prenom, usr_email, usr_password, usr_date_creation, usr_date_update, str_id)
                                    VALUES (:nom, :prenom, :email, :pass, NOW(), NOW(), :str_id)
                                ");

                                $stmt->execute([
                                    ':nom' => $nom,
                                    ':prenom' => $prenom,
                                    ':email' => $email,
                                    ':pass' => $passwordHash,
                                    ':str_id' => $structureId
                                ]);

                    $success = "Utilisateur créé avec succès !";

                } elseif ($role === 'technicien') {
                    $stmt = $pdo->prepare("
                        INSERT INTO technicien (tech_nom, tech_email, tech_password, tech_role, tech_date_creation, tech_date_update)
                        VALUES (:nom, :email, :pass, :role, NOW(), NOW())
                    ");
                    $stmt->execute([
                        ':nom' => $nom,
                        ':email' => $email,
                        ':pass' => $passwordHash,
                        ':role' => 'technicien'
                    ]);
                    $success = "Technicien créé avec succès !";
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

</div>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if(isset($success)): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<div class="container-fluid">
    <div class="card shadow mb-4 form-container">
        <div class="card-body">

            <h1 class="h4 text-gray-900 mb-4">Créer un compte</h1>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control form-control-user"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                 <div class="form-group">
                    <label>prenom</label>
                    <input type="text" name="prenom" class="form-control form-control-user"
                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-user"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
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
                    <select id="roleSelect" name="role" class="form-control">
                        <option value="user" <?= $selectedRole==='user'?'selected':'' ?>>User</option>
                        <option value="technicien" <?= $selectedRole==='technicien'?'selected':'' ?>>Technicien</option>
                    </select>
                </div>

                <div id="structure-container" style="<?= $selectedRole==='user'?'display:block;':'display:none;' ?>">
                    <div class="form-group">
                        <label>Département</label>
                        <select name="id_departement" id="departementSelect" class="form-control">
                            <option value="">-- Sélectionnez un département --</option>
                            <?php foreach($departements as $dep): ?>
                                <option value="<?= $dep['STR_ID'] ?>"
                                    <?= (isset($_POST['id_departement']) && $_POST['id_departement']==$dep['STR_ID'])?'selected':'' ?>>
                                    <?= htmlspecialchars($dep['STR_NAME']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Centre ou Service (optionnel)</label>
                        <select name="id_structure" id="structureSelect" class="form-control">
                            <option value="">-- Choisir un centre ou service --</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="border-radius:10px; height:45px;">
                    Créer le compte
                </button>

            </form>

        </div>
    </div>
</div>


<script>
document.getElementById('roleSelect').addEventListener('change', function(){
    const container = document.getElementById('structure-container');
    container.style.display = this.value==='user'?'block':'none';
});

const structures = <?= json_encode($structures) ?>;

document.getElementById('departementSelect').addEventListener('change', function(){
    const depId = this.value;
    const structSelect = document.getElementById('structureSelect');
    structSelect.innerHTML = '<option value="">-- Choisir un centre ou service --</option>';
    structures.forEach(s => {
        if(s.PARENT_ID == depId){
            const option = document.createElement('option');
            option.value = s.STR_ID;
            option.textContent = s.STR_NAME + ' (' + s.STR_TYPE + ')';
            structSelect.appendChild(option);
        }
    });
});
</script>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
