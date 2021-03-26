<?php

namespace App\Controllers;

use App\Classes\EnviarEmail;
use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;
use stdClass;

class PedidoControllers extends Action
{
    // ====================================================================================
    public function finalizarPedido()
    {
        $this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
        $this->view->categorias = Store::getCategoriasView();
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
                    'bairro' => $_SESSION['bairro'],
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
            if (!isset($_SESSION['carrinho']) || !isset($_SESSION['codigo_pedido'])) {
                header('Location: ' . BASE_URL);
                return;
            }

            // Constroi os dados para o emvio do e-mail
            $produtos_pedido = Store::constroiCarrinho();
            $produtos = '<ul>';
            foreach ($produtos_pedido->carrinho as $carrinho) {
                $carrinho->valorQuantidade = number_format((float)$carrinho->valorQuantidade, 2, ',', '');
                $carrinho->preco = number_format((float)$carrinho->preco, 2, ',', '');
                $produtos .= "<li><p>$carrinho->quantidade x $carrinho->nome_produto(R$$carrinho->preco) = R$$carrinho->valorQuantidade</p></li>";
            }
            $produtos .= '</ul>';
            $this->view->codigoPedido = $_SESSION['codigo_pedido'];
            $this->view->valorPedido = $_SESSION['total_pedido'];
            $enviarEmail = new EnviarEmail();


            //Guardar o pedido na base de dados
            $pedido = Container::getModel('Pedido');

            // Dados do Pedido
            $pedido->status_pedido = 'PENDENTE';
            $pedido->id_cliente = $_SESSION['id_cliente'];
            $pedido->cep_pedido = isset($_SESSION['dados_pagamento']) && !empty($_SESSION['dados_pagamento']['cepAlternativa']) ? $_SESSION['dados_pagamento']['cepAlternativa'] : $_SESSION['cep'];
            $pedido->numero_residencia_pedido = isset($_SESSION['dados_pagamento']) && !empty($_SESSION['dados_pagamento']['numeroResidencia']) ? $_SESSION['dados_pagamento']['numeroResidencia'] : $_SESSION['numero_residencia'];
            $pedido->rua_pedido = isset($_SESSION['dados_pagamento']) && !empty($_SESSION['dados_pagamento']['ruaAlternativa']) ? $_SESSION['dados_pagamento']['ruaAlternativa'] : $_SESSION['rua'];
            $pedido->bairro_pedido = isset($_SESSION['dados_pagamento']) && !empty($_SESSION['dados_pagamento']['bairroAlternativa']) ? $_SESSION['dados_pagamento']['bairroAlternativa'] : $_SESSION['bairro'];
            $pedido->cidade_pedido = isset($_SESSION['dados_pagamento']) && !empty($_SESSION['dados_pagamento']['cidadeAlternativa']) ? $_SESSION['dados_pagamento']['cidadeAlternativa'] : $_SESSION['cidade'];
            $pedido->telefone_pedido = $_SESSION['telefone'];
            $pedido->codigo_pedido = $_SESSION['codigo_pedido'];
            $pedido->metodo_envio = '';
            $pedido->mensagem = '';

            //Dados dos Produtos do pedido
            $dados_podutos_pedido = [];
            foreach ($produtos_pedido->carrinho as $value) {
                $podutos_pedido = new \stdClass();

                $podutos_pedido->designacao_produto  = $value->nome_produto;
                $podutos_pedido->peco_unidade  = $value->preco;
                $podutos_pedido->quantidade  = $value->quantidade;

                array_push($dados_podutos_pedido, $podutos_pedido);
                unset($produtos_pedido);
            }
            $pedido->dados_podutos_pedido = (object)$dados_podutos_pedido;

            //Salva o pedido
            $pedido->salvarPedido();

            //Enviar e-mail do pedido
            $enviarEmail->EnviarEmailConfirmacaoPedido($this->view->codigoPedido, $produtos, $this->view->valorPedido);

            // -----------------------------------------------------------------------------------------------------------
            //Limpar dados da Sessão relacionados ao pedido
            unset($_SESSION['carrinho']);
            unset($_SESSION['total_pedido']);
            unset($_SESSION['codigo_pedido']);
            if (isset($_SESSION['dadosAlternativos'])) {
                unset($_SESSION['dadosAlternativos']);
            }
            if (isset($_SESSION['codigo_pedido'])) {
                unset($_SESSION['codigo_pedido']);
            }
            $this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
            $this->view->categorias = Store::getCategoriasView();
            $this->view->clienteLogado = Store::clienteLogado();
            $this->render('confirmar_pedido');
        } else {
            header('Location: /login?finalizarPedido=true');
        }
    }
}
