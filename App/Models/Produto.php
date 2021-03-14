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
            ORDER BY
                estoque desc
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
            ORDER BY
                estoque desc
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function validaProduto()
    {
        //Todos os produtos
        $query = "
            SELECT
                *
            FROM
                produtos
            WHERE
                id_produto = :id_produto and estoque > 0 and ativo = 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_produto', $this->id_produto);
        $stmt->execute();

        if (count($stmt->fetchAll(\PDO::FETCH_CLASS)) != 0) {
            return true;
        } else {
            return false;
        }
    }

    //=============================================================================================
    public function getProdutoCarrinho()
    {
        //Todos os produtos
        $query = "
            SELECT
                id_produto, nome_produto, imagem, categoria, preco, estoque
            FROM
                produtos
            WHERE
                id_produto = :id_produto and estoque > 0 and ativo = 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_produto', $this->id_produto);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function validaEstoque()
    {
        //Todos os produtos
        $query = "
            SELECT
                estoque
            FROM
                produtos
            WHERE
                id_produto = :id_produto and ativo = 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_produto', $this->id_produto);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function getMaisVendidos()
    {
        //Todos os produtos
        $query = "
            SELECT
                *
            FROM
                produtos
            WHERE
                ativo = 1 and estoque > 0
            ORDER BY
                total_vendidos desc
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }
}
