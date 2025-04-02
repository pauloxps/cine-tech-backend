<?php
class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nome;

    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " (nome)
                  VALUES (:nome)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $this->nome);
       
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function editar() {
        $query = "UPDATE " . $this->table_name . " SET 
            nome = :nome
            WHERE id = :id";  // Agora sem a vírgula incorreta
    
        $stmt = $this->conn->prepare($query);
    
        // Correção da vinculação de parâmetros
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":nome", $this->nome, PDO::PARAM_STR);
    
        return $stmt->execute();
    }
    
    
}
