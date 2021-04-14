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
        $this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
        $this->view->categorias = Store::getCategoriasView();
        $this->view->clienteLogado = Store::clienteLogado();

        $produtos = Container::getModel('Produto');
        $totalProdutos = $produtos->getTotalProdutos()[0]->total;
        //Variáveis de paginação
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $totalRegistrosPagina = 10;
        $this->view->totalPaginas = ceil($totalProdutos / $totalRegistrosPagina);
        $deslocamento = ($pagina - 1) * $totalRegistrosPagina;

        $this->view->paginaAtiva = $pagina;
        $this->view->produto = $produtos->getPorPagina($totalRegistrosPagina, $deslocamento);

        //$this->view->produto = $produtos->getTodos();
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
        $this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
        $this->view->clienteLogado = Store::clienteLogado();
        if (isset($_GET['categoria'])) {
            $categoria = $_GET['categoria'];
        } else {
            header('Location: ' . BASE_URL . 'todos');
        }

        $produtos = Container::getModel('Produto');
        $produtos->categoria = $categoria;
        $totalProdutos = $produtos->getTotalProdutosPorPagina()[0]->total;
        //Variáveis de paginação
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $totalRegistrosPagina = 10;
        $this->view->totalPaginas = ceil($totalProdutos / $totalRegistrosPagina);
        $deslocamento = ($pagina - 1) * $totalRegistrosPagina;

        $this->view->paginaAtiva = $pagina;
        $this->view->produto = $produtos->getProdutosPorCategoria($totalRegistrosPagina, $deslocamento);

        $this->view->categoria = $categoria;
        $this->view->categorias = Store::getCategoriasView();
        if (count($this->view->produto) == 0) {
            $this->render('produtosVazio');
        } else {
            $this->render('produtos');
        }
    }
}
