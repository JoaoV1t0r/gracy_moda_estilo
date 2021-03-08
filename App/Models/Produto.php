<?php

namespace App\Models;

use MF\Model\Model;

class Produto extends Model
{
    private $id_produto;
    private $categoria;
    private $nome_produto;
    private $descricao;
    private $imagem;
    private $preco;
    private $estoque;
    private $ativo;

    //=============================================================================================
    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }

    //=============================================================================================
    public function __get($value)
    {
        return $this->$value;
    }

    //=============================================================================================
    public function getTodos()
    {
        //Todos os produtos
        $query = "
            SELECT
                *
            FROM
                produtos
            WHERE
                ativo = 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function getProdutos($categoria)
    {
        //Todos os produtos
        $query = "
            SELECT
                *
            FROM
                produtos
            WHERE
            ativo = 1 and categoria = :categoria
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }
}
