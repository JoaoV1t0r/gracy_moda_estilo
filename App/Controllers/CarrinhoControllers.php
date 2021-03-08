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
        $this->render('carrinho');
    }

    public function adicionarCarrinho()
    {
        //adicona produtos ao carrinho
        $id_produto = $_GET['id_produto'];
        $carrinho = [];
        if (isset($_SESSION['carrinho'])) {
            $carrinho = $_SESSION['carrinho'];
        }

        //Adicionar o produto ao carrinho
        if (key_exists($id_produto, $carrinho)) {
            //Se o produto já existe acrescenta mais uma unidade
            $carrinho[$id_produto]++;
        } else {
            //Adiciona um novo produto ao carinho
            $carrinho[$id_produto] = 1;
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
