<?php
class Filme {
    private $conn;
    private $table_name = "filmes";

    public $id;
    public $titulo;
    public $sinopse;
    public $genero;
    public $capa;
    public $trailer;
    public $data_lancamento;
    public $duracao;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY data_lancamento DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " (titulo, sinopse, genero, capa, trailer, data_lancamento, duracao)
                  VALUES (:titulo, :sinopse, :genero, :capa, :trailer, :data_lancamento, :duracao)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":sinopse", $this->sinopse);
        $stmt->bindParam(":genero", $this->genero);
        $stmt->bindParam(":capa", $this->capa);
        $stmt->bindParam(":trailer", $this->trailer);
        $stmt->bindParam(":data_lancamento", $this->data_lancamento);
        $stmt->bindParam(":duracao", $this->duracao);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function editar() {
        $query = "UPDATE " . $this->table_name . " SET 
            titulo = :titulo,
            sinopse = :sinopse,
            genero = :genero,
            capa = :capa,
            trailer = :trailer,
            data_lancamento = :data_lancamento,
            duracao = :duracao
        WHERE id = :id";  // Correção na cláusula WHERE
        
        $stmt = $this->conn->prepare($query);
    
        // Correção da vinculação de parâmetros
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":sinopse", $this->sinopse);
        $stmt->bindParam(":genero", $this->genero);
        $stmt->bindParam(":capa", $this->capa);
        $stmt->bindParam(":trailer", $this->trailer);
        $stmt->bindParam(":data_lancamento", $this->data_lancamento);
        $stmt->bindParam(":duracao", $this->duracao);
    
        return $stmt->execute();  
    }
    
}
