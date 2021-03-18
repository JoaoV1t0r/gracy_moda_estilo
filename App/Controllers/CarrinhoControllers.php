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
        $this->view->clienteLogado = Store::clienteLogado();
        $retorno = Store::constroiCarrinho();
        if ($retorno == null) {
            $this->view->carrinho = null;
        } else {
            $this->view->carrinho = $retorno->carrinho;
            $this->view->total = $retorno->total;
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
