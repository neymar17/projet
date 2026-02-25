<?php
session_start();
require 'db.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    try {
        if ($type === 'user') {
            // Supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM user WHERE usr_id = :id");
            $stmt->execute([':id' => $id]);
            header("Location: gestion_user.php");
            exit;

        } elseif ($type === 'materiel') {
            // Supprimer le matériel
            $stmt = $pdo->prepare("DELETE FROM materiel WHERE mat_code = :id");
            $stmt->execute([':id' => $id]);
            header("Location: materiel.php");
            exit;

        } else {
            echo "Type inconnu.";
        }

    } catch (PDOException $e) {
        echo "Impossible de supprimer : " . $e->getMessage();
    }

} else {
    echo "Paramètres manquants.";
}
