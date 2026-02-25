<?php
header('Content-Type: application/json');
require 'db.php';

$term = $_GET['term'] ?? '';

$stmt = $pdo->prepare("SELECT usr_id, usr_nom FROM user WHERE usr_nom LIKE :term LIMIT 10");
$stmt->execute([':term' => $term . '%']);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
