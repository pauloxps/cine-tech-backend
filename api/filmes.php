<?php
header("Access-Control-Allow-Origin: *");
header(header: "Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
header(header: "Access-Control-Allow-Headers: Content-Type");


header("Content-Type: application/json");
require_once(__DIR__ . '/../backend/Database.php');
require_once(__DIR__ . '/../models/Filme.php');

$database = new Database();
$db = $database->getConnection();
$filme = new Filme($db);

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $filme->listar();
    $filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($filmes);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->titulo)) {
        $filme->titulo = $data->titulo;
        $filme->sinopse = $data->sinopse;
        $filme->genero = $data->genero;
        $filme->capa = $data->capa;
        $filme->trailer = $data->trailer;
        $filme->data_lancamento = $data->data_lancamento;
        $filme->duracao = $data->duracao;

        if ($filme->cadastrar()) {
            echo json_encode(["mensagem" => "Filme cadastrado com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao cadastrar"]);
        }
    } else {
        echo json_encode(["erro" => "Dados incompletos"]);
    }
}elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents( "php://input"));
    if ($id && !empty($data->titulo)) {
        $filme->id = $id;
        $filme->titulo = $data->titulo;
        $filme->sinopse = $data->sinopse;
        $filme->genero = $data->genero;
        $filme->capa = $data->capa;
        $filme->trailer = $data->trailer;
        $filme->data_lancamento = $data->data_lancamento;
        $filme->duracao = $data->duracao;

        if ($filme->editar()) {
            echo json_encode(["mensagem" => "Filme Atualizado com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao Atualizado"]);
        }
    } else {
        echo json_encode(["erro" => "ID ou Dados incompletos"]);
    }
}


