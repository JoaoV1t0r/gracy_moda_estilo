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
        $this->view->categorias = Store::getCategoriasView();
        $this->view->clienteLogado = Store::clienteLogado();
        $produtos = Container::getModel('Produto');
        $this->view->produto = $produtos->getTodos();
        $this->view->categoria = 'Todos os Produtos';
        if (count($this->view->produto) == 0) {
            $this->render('produtosVazio');
        } else {
            $this->render('produtos');
        }
    }

    // ========================================================================================
    public function getCategoria()
    {
        $this->view->clienteLogado = Store::clienteLogado();
        if (isset($_GET['categoria'])) {
            $categoria = $_GET['categoria'];
        } else {
            header('Location: ' . BASE_URL . 'todos');
        }

        $produtos = Container::getModel('Produto');
        $this->view->categoria = $categoria;
        $this->view->produto = $produtos->getProdutos($categoria);
        $this->view->categorias = Store::getCategoriasView();
        if (count($this->view->produto) == 0) {
            $this->render('produtosVazio');
        } else {
            $this->render('produtos');
        }
    }
}
