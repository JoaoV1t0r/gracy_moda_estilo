<?php

namespace App\Controllers;

use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class DepartamentoControllers extends Action
{
    // ========================================================================================
    public function getTodosProdutos()
    {
        //Renderização da index
        $this->view->clienteLogado = Store::clienteLogado();
        $produtos = Container::getModel('Produto');
        $this->view->produto = $produtos->getTodos();
        $this->view->categoria = 'Todos os Produtos';
        $this->render('produtos');
    }

    // ========================================================================================
    public function getCategoria()
    {
        //Renderização da index
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'Todos';
        $this->view->clienteLogado = Store::clienteLogado();
        $this->view->categoria = $categoria;
        $produtos = Container::getModel('Produto');
        $this->view->produto = $produtos->getProdutos($categoria);
        if (count($this->view->produto) == 0) {
            $this->render('produtosVazio');
        } else {
            $this->render('produtos');
        }
    }
}
