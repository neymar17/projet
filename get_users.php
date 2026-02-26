<?php
header('Content-Type: application/json');
require 'db.php';

$term = $_GET['term'] ?? '';
$str_id = (int)($_GET['str_id'] ?? 0);

// Si pas de structure sélectionnée, on renvoie vide
if(!$str_id){
    echo json_encode([]);
    exit;
}

// Requête : filtrer par nom et par structure
$stmt = $pdo->prepare("
    SELECT usr_id, usr_nom 
    FROM user 
    WHERE str_id = :str_id AND usr_nom LIKE :term 
    ORDER BY usr_nom ASC
    LIMIT 10
");
$stmt->execute([
    ':str_id' => $str_id,
    ':term' => $term . '%'
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
