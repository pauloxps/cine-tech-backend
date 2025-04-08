<?php
require_once "conexao.php"; // ajuste conforme sua estrutura

header("Content-Type: application/json");

$sql = "SELECT nome FROM categorias ORDER BY nome ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();

$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categorias);
