<?php

namespace App\Classes;

use MF\Model\Container;
use stdClass;

class Store extends Container
{
    // ===========================================================================
    public static function criarHash($numeroCaracteres = 12)
    {
        $chars = '01234567890123456789abcdefghijklmnopqrstuwvxzABCDEFGHIJKLMNOPQRSTUWVXZABCDEFGHIJKLMNOPQRSTUWVXZ';
        return substr(str_shuffle($chars),  0, $numeroCaracteres);
    }

    // ===========================================================================
    public static function clienteLogado()
    {
        if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
            return true;
        } else {
            return false;
        }
    }

    // ===========================================================================
    public static function codigoPedido()
    {
        $chars = 'abcdefghijklmnopqrstuwvxzABCDEFGHIJKLMNOPQRSTUWVXZABCDEFGHIJKLMNOPQRSTUWVXZ';
        $codigo =  substr(str_shuffle($chars),  0, 2);
        $codigo .= rand(100000, 999999);
        return $codigo;
    }

    // ===========================================================================
    public static function constroiCarrinho()
    {
        if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
            return null;
        } else {
            $idProdutoCarrinho = [];

            foreach ($_SESSION['carrinho'] as $id_produto => $produto_quantidade) {
                array_push($idProdutoCarrinho, [
                    'id_produto' => $id_produto,
                    'quantidade' => $produto_quantidade
                ]);
            }
            $carrinho = [];
            $total = 0;
            foreach ($idProdutoCarrinho as $pedido) {
                $produto = Container::getModel('Produto');
                $produto->id_produto = $pedido['id_produto'];

                $consulta = $produto->getProdutoCarrinho();
                $produtoCarrinho = new \stdClass();

                $produtoCarrinho->id_produto = $produto->id_produto;
                $produtoCarrinho->nome_produto = $consulta[0]->nome_produto;
                $produtoCarrinho->imagem = $consulta[0]->imagem;
                $produtoCarrinho->categoria = $consulta[0]->categoria;
                $produtoCarrinho->preco = $consulta[0]->preco;
                $produtoCarrinho->valorQuantidade = $consulta[0]->preco * $pedido['quantidade'];;
                $produtoCarrinho->quantidade = $pedido['quantidade'];

                $total += $produtoCarrinho->preco * $pedido['quantidade'];;
                array_push($carrinho, $produtoCarrinho);
                unset($consulta);
            }
            $retorno = new \stdClass();
            $retorno->carrinho = $carrinho;
            $retorno->total = $total;
            return $retorno;
        }
    }
}
