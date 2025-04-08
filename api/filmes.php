<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
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
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = intval(value: $_GET['id']);
        $filmeEncontrado = $filme->buscarPorId($id);

        if ($filmeEncontrado) {
            echo json_encode($filmeEncontrado);
        } else {
            http_response_code(404);
            echo json_encode(["erro" => "Filme não encontrado"]);
        }
    } else {
        $stmt = $filme->listar();
        $filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($filmes);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['titulo']) && isset($_FILES['capa'])) {
        $filme->titulo = $_POST['titulo'];
        $filme->sinopse = $_POST['sinopse'] ?? '';
        $filme->genero = $_POST['genero'] ?? '';
        $filme->trailer = $_POST['trailer'] ?? '';
        $filme->data_lancamento = $_POST['data_lancamento'] ?? null;
        $filme->duracao = $_POST['duracao'] ?? null;

        // Diretório de uploads
        $diretorioUploads = __DIR__ . '/../uploads/';
        if (!is_dir($diretorioUploads)) {
            mkdir($diretorioUploads, 0777, true);
        }

        // Nome do arquivo e caminho final
        $nomeArquivo = time() . '_' . basename($_FILES['capa']['name']);
        $caminhoArquivo = $diretorioUploads . $nomeArquivo;

        // Movendo o arquivo
        if (move_uploaded_file($_FILES['capa']['tmp_name'], $caminhoArquivo)) {
            $filme->capa = "http://localhost:8000/uploads/" . $nomeArquivo;

            if ($filme->cadastrar()) {
                http_response_code(201);
                echo json_encode(["mensagem" => "Filme cadastrado com sucesso!"]);
            } else {
                http_response_code(500);
                echo json_encode(["erro" => "Erro ao cadastrar no banco"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao salvar a capa"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Dados incompletos"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // DELETE ?id=1
    parse_str($_SERVER['QUERY_STRING'], $params);
    if (isset($params['id']) && is_numeric($params['id'])) {
        $id = intval($params['id']);
        $resultado = $filme->excluir($id);

        if ($resultado) {
            echo json_encode(["mensagem" => "Filme excluído com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao excluir o filme"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido"]);
    }
}
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $dados = json_decode(file_get_contents("php://input"), true);
    $id = $_GET["id"] ?? null;

    if ($id && !empty($dados)) {
        $filme->id = $id;
        $filme->titulo = $dados["titulo"] ?? "";
        $filme->sinopse = $dados["sinopse"] ?? "";
        $filme->genero = $dados["genero"] ?? "";
        $filme->capa = $dados["capa"] ?? "";
        $filme->trailer = $dados["trailer"] ?? "";
        $filme->data_lancamento = $dados["data_lancamento"] ?? "";
        $filme->duracao = $dados["duracao"] ?? "";

        if ($filme->editar()) {
            echo json_encode(["mensagem" => "Filme atualizado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao atualizar"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido ou dados incompletos"]);
    }
}

?>
