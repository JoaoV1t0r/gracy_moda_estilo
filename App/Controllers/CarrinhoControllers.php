<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class CarrinhoControllers extends Action
{

    public function clienteLogado()
    {
        if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
            return true;
        } else {
            return false;
        }
    }

    public function limparCarrinho()
    {
        //esvazia o carrinho
        unset($_SESSION['carrinho']);
        $this->carrinho();
    }

    public function carrinho()
    {
        //Renderização da index
        $this->view->clienteLogado = $this->clienteLogado();
        if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
            $this->view->carrinho = null;
        } else {
            $idProdutoCarrinho = [];

            foreach ($_SESSION['carrinho'] as $id_produto => $produto_quantidade) {
                array_push($idProdutoCarrinho, [
                    'id_produto' => $id_produto,
                    'quantidade' => $produto_quantidade
                ]);
            }
            $this->view->carrinho = [];
            foreach ($idProdutoCarrinho as $pedido) {
                $produto = Container::getModel('Produto');
                $produto->id_produto = $pedido['id_produto'];
                $produto->quantidade = $pedido['quantidade'];
                $consulta = $produto->getProdutoCarrinho();
                $produto->nome_produto = $consulta[0]->nome_produto;
                $produto->imagem = $consulta[0]->imagem;
                $produto->categoria = $consulta[0]->categoria;
                $produto->preco = $consulta[0]->preco;
                $produto->estoque = $consulta[0]->estoque;
                array_push($this->view->carrinho, $produto);
                unset($consulta);
            }
        }
        $this->render('carrinho');
    }

    public function adicionarCarrinho()
    {
        //Validação
        if (!isset($_GET['id_produto'])) {
            echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : '';
            return;
        }
        //adicona produtos ao carrinho
        //Valida se o produto existe na bd e se tem estoque
        $produto = Container::getModel('Produto');
        $produto->id_produto = $_GET['id_produto'];
        if (!$produto->validaProduto()) {
            echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : '';
            return;
        }
        $carrinho = [];
        if (isset($_SESSION['carrinho'])) {
            $carrinho = $_SESSION['carrinho'];
        }

        //Adicionar o produto ao carrinho
        if (key_exists($produto->id_produto, $carrinho)) {
            //Se o produto já existe acrescenta mais uma unidade
            $carrinho[$produto->id_produto]++;
        } else {
            //Adiciona um novo produto ao carinho
            $carrinho[$produto->id_produto] = 1;
        }

        //Atualiza o carrinho da sessão
        $_SESSION['carrinho'] = $carrinho;

        //Resposta
        $total_produtos = 0;
        foreach ($carrinho as $produto_quantidade) {
            $total_produtos += $produto_quantidade;
        }

        echo $total_produtos;
    }
}
