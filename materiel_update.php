<?php
session_start();
require 'db.php';

// Vérifier si connecté (admin/tech)
if (!isset($_SESSION['tech_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$action_type = $_POST['action_type'] ?? '';

/* =====================================================
   UPDATE USER
===================================================== */
if ($action_type === "update_user") {

    $usr_id        = $_POST['usr_id'] ?? null;
    $usr_nom       = trim($_POST['usr_nom'] ?? '');
    $usr_telephone = trim($_POST['usr_telephone'] ?? '');
    $usr_email     = trim($_POST['usr_email'] ?? '');
    $str_id        = $_POST['str_id'] ?? null;

    if (empty($usr_id) || empty($usr_nom) || empty($usr_email)) {
        $_SESSION['error'] = "Erreur: Champs obligatoires manquants (Nom / Email).";
        header("Location: list_users.php");
        exit;
    }

    // si str_id vide => NULL
    if ($str_id === "") {
        $str_id = null;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE user 
            SET usr_nom = ?, usr_telephone = ?, usr_email = ?, str_id = ?, usr_date_update = NOW()
            WHERE usr_id = ?
        ");

        $stmt->execute([$usr_nom, $usr_telephone, $usr_email, $str_id, $usr_id]);

        $_SESSION['success'] = "Utilisateur modifié avec succès.";
        header("Location: list_users.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur SQL: " . $e->getMessage();
        header("Location: list_users.php");
        exit;
    }
}


/* =====================================================
   UPDATE MATERIEL
===================================================== */
if ($action_type === "update_materiel") {

    $mat_id      = $_POST['mat_id'] ?? null;
    $mat_nom     = trim($_POST['mat_nom'] ?? '');
    $mat_type    = trim($_POST['mat_type'] ?? '');
    $mat_etat    = trim($_POST['mat_etat'] ?? '');
    $mat_numserie = trim($_POST['mat_numserie'] ?? '');
    $usr_id      = $_POST['usr_id'] ?? null; // user affecté

    if (empty($mat_id) || empty($mat_nom) || empty($mat_type)) {
        $_SESSION['error'] = "Erreur: Champs obligatoires manquants (Nom / Type).";
        header("Location: list_materiels.php");
        exit;
    }

    // usr_id vide => NULL
    if ($usr_id === "") {
        $usr_id = null;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE materiel
            SET mat_nom = ?, mat_type = ?, mat_etat = ?, mat_numserie = ?, usr_id = ?
            WHERE mat_id = ?
        ");

        $stmt->execute([$mat_nom, $mat_type, $mat_etat, $mat_numserie, $usr_id, $mat_id]);

        $_SESSION['success'] = "Matériel modifié avec succès.";
        header("Location: list_materiels.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur SQL: " . $e->getMessage();
        header("Location: list_materiels.php");
        exit;
    }
}


/* =====================================================
   ACTION INCONNUE
===================================================== */
$_SESSION['error'] = "Action non reconnue.";
header("Location: index.php");
exit;
