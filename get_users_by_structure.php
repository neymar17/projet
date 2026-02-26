<?php
header('Content-Type: application/json');
require 'db.php';

$term = $_GET['term'] ?? '';
$str_id = (int)($_GET['str_id'] ?? 0);

if (!$str_id) {
    echo json_encode([]);
    exit;
}

// Fonction pour récupérer tous les IDs enfants (centres/services) d'une structure
function getChildStrIds($pdo, $parentId) {
    $ids = [$parentId];

    $stmt = $pdo->prepare("SELECT STR_ID FROM structure WHERE parent_id = :parent");
    $stmt->execute([':parent' => $parentId]);
    $children = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($children as $childId) {
        $ids = array_merge($ids, getChildStrIds($pdo, $childId)); // récursif
    }

    return $ids;
}

// Récupérer tous les str_id liés à la structure choisie
$str_ids = getChildStrIds($pdo, $str_id);

// Préparer la requête pour récupérer les utilisateurs
$placeholders = implode(',', array_fill(0, count($str_ids), '?'));
$stmt = $pdo->prepare("
    SELECT usr_id, usr_nom 
    FROM user 
    WHERE str_id IN ($placeholders) AND usr_nom LIKE ?
    ORDER BY usr_nom ASC
    LIMIT 50
");

// Ajouter le term à la fin des paramètres
$params = array_merge($str_ids, [$term . '%']);
$stmt->execute($params);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
