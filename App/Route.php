<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap
{

	protected function initRoutes()
	{

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'IndexControllers',
			'action' => 'index'
		);
		$routes['criar_conta'] = array(
			'route' => '/criar_conta',
			'controller' => 'IndexControllers',
			'action' => 'criarUsuario'
		);
		$routes['registrar_conta'] = array(
			'route' => '/registrar_conta',
			'controller' => 'UserControllers',
			'action' => 'registraUsuario'
		);
		$routes['confirmar_email'] = array(
			'route' => '/confirmar_email',
			'controller' => 'UserControllers',
			'action' => 'confirmarEmail'
		);
		$routes['login'] = array(
			'route' => '/login',
			'controller' => 'IndexControllers',
			'action' => 'login'
		);
		$routes['valida_login'] = array(
			'route' => '/valida_login',
			'controller' => 'UserControllers',
			'action' => 'validaLogin'
		);
		$routes['minha_conta'] = array(
			'route' => '/minha_conta',
			'controller' => 'UserControllers',
			'action' => 'exibirUser'
		);
		$routes['sair'] = array(
			'route' => '/sair',
			'controller' => 'UserControllers',
			'action' => 'sair'
		);
		$routes['todos'] = array(
			'route' => '/todos',
			'controller' => 'DepartamentoControllers',
			'action' => 'getTodosProdutos'
		);
		$routes['produtos'] = array(
			'route' => '/produtos',
			'controller' => 'DepartamentoControllers',
			'action' => 'getCategoria'
		);
		$routes['carrinho'] = array(
			'route' => '/carrinho',
			'controller' => 'CarrinhoControllers',
			'action' => 'carrinho'
		);
		$routes['adicionar_carrinho'] = array(
			'route' => '/adiconar_carrinho',
			'controller' => 'CarrinhoControllers',
			'action' => 'adicionarCarrinho'
		);
		$routes['remover_unidade_produto_carrinho'] = array(
			'route' => '/remover_unidade_produto_carrinho',
			'controller' => 'CarrinhoControllers',
			'action' => 'removerUnidadeProdutoCarrinho'
		);
		$routes['remover_produto_carrinho'] = array(
			'route' => '/remover_produto_carrinho',
			'controller' => 'CarrinhoControllers',
			'action' => 'removerProdutoCarrinho'
		);
		$routes['limpar_carrinho'] = array(
			'route' => '/limpar_carrinho',
			'controller' => 'CarrinhoControllers',
			'action' => 'limparCarrinho'
		);
		$routes['valida_login_finaliar_pedido'] = array(
			'route' => '/valida_login_finaliar_pedido',
			'controller' => 'CarrinhoControllers',
			'action' => 'clienteLogadoFinalizarPedido'
		);
		$routes['finalizar_pedido'] = array(
			'route' => '/finalizar_pedido',
			'controller' => 'PedidoControllers',
			'action' => 'finalizarPedido'
		);
		$routes['confirmar_pedido'] = array(
			'route' => '/confirmar_pedido',
			'controller' => 'PedidoControllers',
			'action' => 'confirmarPedido'
		);
		$routes['adicionar_dados_alternativos'] = array(
			'route' => '/adicionar_dados_alternativos',
			'controller' => 'PedidoControllers',
			'action' => 'adicionarDadosAlternativos'
		);
		$routes['remover_dados_alternativos'] = array(
			'route' => '/remover_dados_alternativos',
			'controller' => 'PedidoControllers',
			'action' => 'removerDadosAlternativos'
		);
		$routes['metodo_envio'] = array(
			'route' => '/metodo_envio',
			'controller' => 'PedidoControllers',
			'action' => 'adicionarMetodoEnvio'
		);
		$routes['metodo_correios'] = array(
			'route' => '/metodo_correios',
			'controller' => 'PedidoControllers',
			'action' => 'correiosMetodoEnvio'
		);
		$routes['alterar_dados_pessoais'] = array(
			'route' => '/alterar_dados_pessoais',
			'controller' => 'UserControllers',
			'action' => 'editarDadosUser'
		);
		$routes['salvar_alteracao_dados_pessoais'] = array(
			'route' => '/salvar_alteracao_dados_pessoais',
			'controller' => 'UserControllers',
			'action' => 'salvarNewDadosUser'
		);
		$routes['alterar_senha'] = array(
			'route' => '/alterar_senha',
			'controller' => 'UserControllers',
			'action' => 'alterarSenhaUser'
		);
		$routes['salvar_alteracao_senha'] = array(
			'route' => '/salvar_alteracao_senha',
			'controller' => 'UserControllers',
			'action' => 'salvarNewSenhaUser'
		);
		$routes['historico_pedidos'] = array(
			'route' => '/historico_pedidos',
			'controller' => 'PedidoControllers',
			'action' => 'historicoPedidos'
		);
		$routes['detalhes_pedido'] = array(
			'route' => '/detalhes_pedido',
			'controller' => 'PedidoControllers',
			'action' => 'detalhesPedido'
		);
		$routes['confirmacao_pagamento'] = array(
			'route' => '/confirmacao_pagamento',
			'controller' => 'PedidoControllers',
			'action' => 'confirmacaoPagamento'
		);
		$routes['session'] = array(
			'route' => '/session',
			'controller' => 'IndexControllers',
			'action' => 'session'
		);

		$this->setRoutes($routes);
	}
}
