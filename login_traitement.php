<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['erreur'] = "Tous les champs sont obligatoires.";
    header("Location: login.php");
    exit;
}

try {
    $account = null;

    // Chercher dans table user
$stmt = $pdo->prepare("SELECT usr_id AS id, usr_nom AS nom,usr_prenom AS prenom, usr_email AS email, usr_password AS pass, str_id 
                       FROM user 
                       WHERE usr_email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account) {
        $account['type'] = 'user';
        $account['role'] = 'user'; // utilisateur standard
    } else {
        // Chercher dans table technicien
        $stmt = $pdo->prepare("SELECT tech_id AS id, tech_nom AS nom, tech_email AS email, tech_password AS pass, tech_role AS role FROM technicien WHERE tech_email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($account) {
            $account['type'] = 'technicien';
            // tech_role peut être 'technicien' ou 'admin'
        }
    }

    // Vérification du mot de passe
    if (!$account || !password_verify($password, $account['pass'])) {
        $_SESSION['erreur'] = "Email ou mot de passe incorrect.";
        header("Location: login.php");
        exit;
    }

    // Création des sessions et redirection selon type/role
    if ($account['type'] === 'user') {
        $_SESSION['usr_id'] = $account['id'];
        $_SESSION['usr_nom'] = $account['nom'];
        $_SESSION['usr_prenom'] = $account['prenom'];
        $_SESSION['usr_email'] = $account['email'];
        $_SESSION['usr_type'] = 'user';

        // Récupérer structure
        $stmt = $pdo->prepare("SELECT STR_ID, STR_NAME, STR_TYPE, PARENT_ID FROM structure WHERE STR_ID = :id");
        $stmt->execute([':id' => $account['str_id']]);
        $_SESSION['structure'] = $stmt->fetch(PDO::FETCH_ASSOC);

        header("Location: dashboard_user.php");
        exit;

    } elseif ($account['type'] === 'technicien') {
        $_SESSION['tech_id'] = $account['id'];
        $_SESSION['tech_nom'] = $account['nom'];
        $_SESSION['tech_email'] = $account['email'];
        $_SESSION['tech_role'] = $account['role'];
        $_SESSION['tech_type'] = 'technicien';

        // Redirection selon le rôle
        if ($account['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_technicien.php");
        }
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['erreur'] = "Erreur serveur : " . $e->getMessage();
    header("Location: login.php");
    exit;
}
