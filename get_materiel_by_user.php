<?php
header('Content-Type: application/json');
require 'db.php';

$user_id = (int)($_GET['usr_id'] ?? 0);
if(!$user_id){
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT mat_code, mat_categ, mat_marque FROM materiel WHERE usr_id = :usr_id ORDER BY mat_categ ASC, mat_marque ASC");
$stmt->execute([':usr_id' => $user_id]);
$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($materiels);
