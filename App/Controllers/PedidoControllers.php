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
            if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
                header('Location: ' . BASE_URL . 'carrinho');
            }
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
                $dadosCliente = [
                    'nome' => $_SESSION['cliente'],
                    'email' => $_SESSION['email'],
                    'rua' => $_SESSION['rua'],
                    'numero_residencia' => $_SESSION['numero_residencia'],
                    'cidade' => $_SESSION['cidade'],
                    'cep' => $_SESSION['cep'],
                    'telefone' => $_SESSION['telefone']
                ];
                $this->view->dadosCliente = (object)$dadosCliente;
            }
            if (!isset($_SESSION['codigo_pedido'])) {
                $_SESSION['codigo_pedido'] = Store::codigoPedido();
            }
            $_SESSION['total_pedido'] = $this->view->total;
            $this->render('finalizar_pedido');
        } else {
            $_SESSION['login_finalizar_pedido'] = true;
            header('Location: /login?finalizarPedido=true');
        }
    }

    // ====================================================================================
    public function confirmarPedido()
    {
        if (Store::clienteLogado()) {
            //Verifica se foi feito o post
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                header('Location: /');
            }
            //dados pagamento
            $numeroCartao = $_POST['numero_cartao'];
            $validadeCartao = $_POST['validade_cartao'];
            $cvvCartao = $_POST['cvv_cartao'];
            $cpfUser = $_POST['cpf_user'];
            // if ($numeroCartao == '' || $validadeCartao == '' || $cvvCartao == '' || $cpfUser == '') {
            //     header('Location: ' . BASE_URL . 'finalizar_pedido?erro=campoVazio');
            // }
            $_SESSION['dados_pagamento'] = [
                'numero_cartao' => $numeroCartao,
                'validade_cartao' => $validadeCartao,
                'cvv_cartao' => $cvvCartao,
                'cpf_user' => $cpfUser
            ];
            $this->view->codigoPedido = $_SESSION['codigo_pedido'];
            $this->view->valorPedido = $_SESSION['total_pedido'];
            unset($_SESSION['carrinho']);
            unset($_SESSION['total_pedido']);
            unset($_SESSION['codigo_pedido']);
            if (isset($_SESSION['dadosAlternativos'])) {
                unset($_SESSION['dadosAlternativos']);
            }
            if (isset($_SESSION['dados_pagamento'])) {
                unset($_SESSION['dados_pagamento']);
            }
            if (isset($_SESSION['codigo_pedido'])) {
                unset($_SESSION['codigo_pedido']);
            }
            $this->view->clienteLogado = Store::clienteLogado();
            $this->render('confirmar_pedido');
        } else {
            header('Location: /login?finalizarPedido=true');
        }
    }
}
