<?php

namespace App\Controllers;

use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class PedidoControllers extends Action
{
    // ====================================================================================
    public function finalizarPedido()
    {
        if (Store::clienteLogado()) {
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
                    $produtoCarrinho->preco = $consulta[0]->preco;
                    $produtoCarrinho->valorQuantidade = $consulta[0]->preco * $pedido['quantidade'];;
                    $produtoCarrinho->quantidade = $pedido['quantidade'];

                    $this->view->total += $produtoCarrinho->preco * $pedido['quantidade'];;
                    array_push($this->view->carrinho, $produtoCarrinho);
                    unset($consulta);
                }
                // -----------------------------------------------------------------------------------------------------
                $_SESSION['cliente'];
                $_SESSION['email'];
                $_SESSION['endereco'];
                $_SESSION['cidade'];
                $_SESSION['cep'];
                $_SESSION['telefone'];
                $dadosCliente = [
                    'nome' => $_SESSION['cliente'],
                    'email' => $_SESSION['email'],
                    'endereco' => $_SESSION['endereco'],
                    'cidade' => $_SESSION['cidade'],
                    'cep' => $_SESSION['cep'],
                    'telefone' => $_SESSION['telefone']
                ];
                $this->view->dadosCliente = (object)$dadosCliente;
            }
            $this->render('finalizar_pedido');
        } else {
            $_SESSION['login_finalizar_pedido'] = true;
            header('Location: /login?finalizarPedido=true');
        }
    }
}
