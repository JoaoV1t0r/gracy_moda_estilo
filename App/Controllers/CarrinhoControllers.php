<?php

namespace App\Controllers;

use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class CarrinhoControllers extends Action
{
    //=============================================================================================
    public function limparCarrinho()
    {
        //esvazia o carrinho
        unset($_SESSION['carrinho']);
        $this->carrinho();
    }

    //=============================================================================================
    public function carrinho()
    {
        //Renderização da index
        $this->view->clienteLogado = Store::clienteLogado();
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
            $this->view->total = 0;
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

                $this->view->total += $produtoCarrinho->preco * $pedido['quantidade'];;
                array_push($this->view->carrinho, $produtoCarrinho);
                unset($consulta);
            }
        }
        $this->render('carrinho');
    }

    //=============================================================================================
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
        $validacao = null;
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
            $consulta = $produto->validaEstoque();
            if ($consulta[0]->estoque <= $carrinho[$produto->id_produto]) {
                $total_produtos = 0;
                $validacao = false;

                foreach ($carrinho as $produto_quantidade) {
                    $total_produtos += $produto_quantidade;
                }

                $resultado = new \stdClass();
                $resultado->total_produtos = $total_produtos;
                $resultado->validacao = $validacao;
                $resultado->estoque = $consulta[0]->estoque;

                echo json_encode($resultado);
                return;
            } else {
                $validacao = true;
                $carrinho[$produto->id_produto]++;
            }
        } else {
            //Adiciona um novo produto ao carinho
            $validacao = true;
            $carrinho[$produto->id_produto] = 1;
        }

        //Atualiza o carrinho da sessão
        $_SESSION['carrinho'] = $carrinho;

        //Resposta
        $total_produtos = 0;
        foreach ($carrinho as $produto_quantidade) {
            $total_produtos += $produto_quantidade;
        }
        $resultado = new \stdClass();
        $resultado->total_produtos = $total_produtos;
        $resultado->validacao = $validacao;

        echo json_encode($resultado);
    }

    //=============================================================================================
    public function removerUnidadeProdutoCarrinho()
    {
        //Validação
        if (!isset($_GET['id_produto'])) {
            echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : '';
            return;
        }
        $carrinho = $_SESSION['carrinho'];
        $produto = Container::getModel('Produto');
        $produto->id_produto = $_GET['id_produto'];

        //Remover uma unidade do produto ao carrinho
        if (key_exists($produto->id_produto, $carrinho)) {
            //Diminui uma unidade
            $carrinho[$produto->id_produto]--;

            foreach ($carrinho as $key => $value) {
                if ($value == 0) {
                    unset($carrinho[$key]);
                }
            }
        }

        $_SESSION['carrinho'] = $carrinho;

        $total_produtos = 0;
        foreach ($carrinho as $produto_quantidade) {
            $total_produtos += $produto_quantidade;
        }

        echo json_encode($total_produtos);
    }

    //=============================================================================================
    public function removerProdutoCarrinho()
    {
        //Validação
        if (!isset($_GET['id_produto'])) {
            echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : '';
            return;
        }
        $carrinho = $_SESSION['carrinho'];
        $produto = Container::getModel('Produto');
        $produto->id_produto = $_GET['id_produto'];

        //Remover uma unidade do produto ao carrinho
        if (key_exists($produto->id_produto, $carrinho)) {
            //Diminui uma unidade
            unset($carrinho[$produto->id_produto]);
        }

        $_SESSION['carrinho'] = $carrinho;

        $total_produtos = 0;
        foreach ($carrinho as $produto_quantidade) {
            $total_produtos += $produto_quantidade;
        }

        echo json_encode($total_produtos);
    }
}
