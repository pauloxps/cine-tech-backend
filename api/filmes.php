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
} if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['titulo']) && isset($_FILES['capa'])) {
        $filme->titulo = $_POST['titulo'];
        $filme->sinopse = $_POST['sinopse'];
        $filme->genero = $_POST['genero'];
        $filme->trailer = $_POST['trailer'];
        $filme->data_lancamento = $_POST['data_lancamento'];
        $filme->duracao = $_POST['duracao'];

        // Configuração do diretório de uploads
        $diretorioUploads = __DIR__ . '/../uploads/';
        if (!is_dir($diretorioUploads)) {
            mkdir($diretorioUploads, 0777, true);
        }

        // Pegando informações do arquivo
        $nomeArquivo = basename($_FILES['capa']['name']);
        $caminhoArquivo = $diretorioUploads . $nomeArquivo;

        // Movendo o arquivo para a pasta de uploads
        if (move_uploaded_file($_FILES['capa']['tmp_name'], $caminhoArquivo)) {
            $filme->capa = "http://localhost:8000/uploads/" . $nomeArquivo;

            if ($filme->cadastrar()) {
                echo json_encode(["mensagem" => "Filme cadastrado com sucesso!"]);
            } else {
                echo json_encode(["erro" => "Erro ao cadastrar no banco"]);
            }
        } else {
            echo json_encode(["erro" => "Erro ao salvar a capa"]);
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
            echo json_encode(value: ["mensagem" => "Filme Atualizado com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao Atualizado"]);
        }
    } else {
        echo json_encode(["erro" => "ID ou Dados incompletos"]);
    }
}


