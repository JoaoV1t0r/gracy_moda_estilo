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
        $_SESSION['teste'] = $id_produto;
        echo 'Produto adicionado ' . $id_produto . ' ao carrinho.';
    }
}
