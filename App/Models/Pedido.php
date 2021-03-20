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
    private $id_produto_pedido;
    private $designacao_produto;
    private $peco_unidade;
    private $quantidade;

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
    }
}
