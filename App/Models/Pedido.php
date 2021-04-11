<?php

namespace App\Models;

use MF\Model\Model;

class Pedido extends Model
{
    //Dados do Pedido
    private $status_pedido;
    private $id_cliente;
    private $data_pedido;
    private $cep_pedido;
    private $numero_residencia_pedido;
    private $rua_pedido;
    private $bairro_pedido;
    private $cidade_pedido;
    private $telefone_pedido;
    private $codigo_pedido;
    private $metodo_envio;
    private $mensagem;

    //Dados dos Produtos do pedido
    private $dados_podutos_pedido;

    //Usado nas duas tabelas
    private $id_pedido;

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
    public function salvarPedido()
    {
        //Salvar dados dos produtos
        $query_salvar_pedido = "
            INSERT INTO
                pedidos
            VALUES(
                0,
                :status_pedido,
                :id_cliente,
                NOW(),
                :cep_pedido,
                :numero_residencia_pedido,
                :rua_pedido,
                :bairro_pedido,
                :cidade_pedido,
                :telefone_pedido,
                :codigo_pedido,
                :metodo_envio,
                :mensagem
        )";
        $stmt_salvar_pedido = $this->db->prepare($query_salvar_pedido);

        $stmt_salvar_pedido->bindValue(':status_pedido', $this->status_pedido);
        $stmt_salvar_pedido->bindValue(':id_cliente', $this->id_cliente);
        $stmt_salvar_pedido->bindValue(':cep_pedido', $this->cep_pedido);
        $stmt_salvar_pedido->bindValue(':numero_residencia_pedido', $this->numero_residencia_pedido);
        $stmt_salvar_pedido->bindValue(':rua_pedido', $this->rua_pedido);
        $stmt_salvar_pedido->bindValue(':bairro_pedido', $this->bairro_pedido);
        $stmt_salvar_pedido->bindValue(':cidade_pedido', $this->cidade_pedido);
        $stmt_salvar_pedido->bindValue(':telefone_pedido', $this->telefone_pedido);
        $stmt_salvar_pedido->bindValue(':codigo_pedido', $this->codigo_pedido);
        $stmt_salvar_pedido->bindValue(':metodo_envio', $this->metodo_envio);
        $stmt_salvar_pedido->bindValue(':mensagem', $this->mensagem);

        $stmt_salvar_pedido->execute();

        //Get do id_pedido salvo
        $id_pedido_atual = $this->getIdPedido();

        //Salvar dados dos produtos
        foreach ($this->dados_podutos_pedido as $dados_produtos) {
            $query_salvar_dados_produtos = "
                INSERT INTO
                    produtos_pedido
                VALUES(
                    0,
                    :id_produto_pedido,
                    :designacao_produto,
                    :peco_unidade,
                    :quantidade

            )";
            $stmt_salvar_dados_produtos = $this->db->prepare($query_salvar_dados_produtos);

            $stmt_salvar_dados_produtos->bindValue(':id_produto_pedido', $id_pedido_atual[0]->id_pedido);
            $stmt_salvar_dados_produtos->bindValue(':designacao_produto', $dados_produtos->designacao_produto);
            $stmt_salvar_dados_produtos->bindValue(':peco_unidade', $dados_produtos->peco_unidade);
            $stmt_salvar_dados_produtos->bindValue(':quantidade', $dados_produtos->quantidade);

            $stmt_salvar_dados_produtos->execute();
        }
    }

    //=============================================================================================
    public function getIdPedido()
    {
        $query = " SELECT MAX(id_pedido) AS id_pedido FROM pedidos";

        $stmt = $this->db->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function getHistoricoPedidos()
    {
        $query = "
            SELECT
                id_pedido,
                status_pedido,
                metodo_envio,
                data_pedido,
                codigo_pedido,
                mensagem
            FROM
                pedidos
            WHERE
                id_cliente = :id_cliente
            ORDER BY
                data_pedido desc
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_cliente', $this->id_cliente);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function verificaPedidoCliente()
    {
        $query = "
            SELECT
                id_pedido
            FROM
                pedidos
            WHERE
                id_cliente = :id_cliente AND id_pedido = :id_pedido
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_cliente', $this->id_cliente);
        $stmt->bindValue(':id_pedido', $this->id_pedido);
        $stmt->execute();

        return count($stmt->fetchAll(\PDO::FETCH_CLASS)) == 1 ? true : false;
    }

    //=============================================================================================
    public function getDetalhesPedido()
    {
        $query = "
            SELECT
                *
            FROM
                produtos_pedido
            WHERE
                id_pedido = :id_pedido
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_pedido', $this->id_pedido);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    //=============================================================================================
    public function getPedido()
    {
        $query = "
            SELECT
                *
            FROM
                pedidos
            WHERE
                id_cliente = :id_cliente AND id_pedido = :id_pedido
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_cliente', $this->id_cliente);
        $stmt->bindValue(':id_pedido', $this->id_pedido);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }
}
