<?php
require 'db.php';

$parent_id = $_GET['parent_id'] ?? 0;
$type = $_GET['type'] ?? '';

$stmt = $pdo->prepare("SELECT STR_ID, STR_NAME FROM structure WHERE STR_TYPE=? AND PARENT_ID=?");
$stmt->execute([$type, $parent_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
