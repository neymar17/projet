<?php
require 'db.php';
session_start();

if (!isset($_SESSION['usr_id'])) {
    exit;
}

$user_id = $_SESSION['usr_id'];

$stmt = $pdo->prepare("
    SELECT mat_code, mat_categ ,mat_marque 
    FROM materiel 
    WHERE usr_id = ?
    ORDER BY mat_inv ASC
");
$stmt->execute([$user_id]);

$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($materiels);
