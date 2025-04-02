<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once(__DIR__ . '/../backend/Database.php');
require_once(__DIR__ . '/../models/Categoria.php');

$database = new Database();
$db = $database->getConnection();
$categorias = new Categoria($db);

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $categorias->listar();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->nome)) {
        // Corrigido: agora usa corretamente a instância $categorias
        $categorias->nome = $data->nome;

        if ($categorias->cadastrar()) {
            echo json_encode(["mensagem" => "Categoria cadastrada com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao cadastrar"]);
        }
    } else {
        echo json_encode(["erro" => "Dados incompletos"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents("php://input"));

    if ($id && !empty($data->nome)) {
        // Corrigido: agora usa corretamente a instância $categorias
        $categorias->id = $id;
        $categorias->nome = $data->nome;

        if ($categorias->editar()) {
            echo json_encode(["mensagem" => "Categoria atualizada com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao atualizar"]);
        }
    } else {
        echo json_encode(["erro" => "ID ou dados incompletos"]);
    }
}
