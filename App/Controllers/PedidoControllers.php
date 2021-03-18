<?php

namespace App\Controllers;

use App\Classes\EnviarEmail;
use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class PedidoControllers extends Action
{
    // ====================================================================================
    public function finalizarPedido()
    {
        $this->view->clienteLogado = Store::clienteLogado();
        if ($this->view->clienteLogado) {
            $retorno = Store::constroiCarrinho();
            if ($retorno == null) {
                $this->view->carrinho = null;
                $this->render('finalizar_pedido');
            } else {
                $this->view->carrinho = $retorno->carrinho;
                $this->view->total = $retorno->total;
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
                if (!isset($_SESSION['codigo_pedido'])) {
                    $_SESSION['codigo_pedido'] = Store::codigoPedido();
                }
                $_SESSION['total_pedido'] = $this->view->total;
                $this->render('finalizar_pedido');
            }
        } else {
            $_SESSION['login_finalizar_pedido'] = true;
            header('Location: /login?finalizarPedido=true');
        }
    }

    // ====================================================================================
    public function confirmarPedido()
    {
        if (Store::clienteLogado()) {
            //Verifica se foi feito o post e se existe um pedido
            if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_SESSION['carrinho']) || !isset($_SESSION['codigo_pedido'])) {
                header('Location: ' . BASE_URL);
                return;
            }
            //dados pagamentos
            $numeroCartao = $_POST['numero_cartao'];
            $validadeCartao = $_POST['validade_cartao'];
            $cvvCartao = $_POST['cvv_cartao'];
            $cpfUser = $_POST['cpf_user'];
            if ($numeroCartao == '' || $validadeCartao == '' || $cvvCartao == '' || $cpfUser == '') {
                header('Location: ' . BASE_URL . 'finalizar_pedido?erro=campoVazio');
                return;
            }
            $_SESSION['dados_pagamento'] = [
                'numero_cartao' => $numeroCartao,
                'validade_cartao' => $validadeCartao,
                'cvv_cartao' => $cvvCartao,
                'cpf_user' => $cpfUser
            ];
            $retorno = Store::constroiCarrinho();
            $produtos = '<ul>';
            foreach ($retorno->carrinho as $carrinho) {
                $carrinho->valorQuantidade = number_format((float)$carrinho->valorQuantidade, 2, ',', '');
                $carrinho->preco = number_format((float)$carrinho->preco, 2, ',', '');
                $produtos .= "<li><p>$carrinho->quantidade x $carrinho->nome_produto(R$$carrinho->preco) = R$$carrinho->valorQuantidade</p></li>";
            }
            $produtos .= '</ul>';
            $this->view->x = $produtos;
            $this->view->codigoPedido = $_SESSION['codigo_pedido'];
            $this->view->valorPedido = $_SESSION['total_pedido'];
            $enviarEmail = new EnviarEmail();
            $enviarEmail->EnviarEmailConfirmacaoPedido($this->view->codigoPedido, $produtos, $this->view->valorPedido);


            //-----------------------------------------------------------------------------------------------------------
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
