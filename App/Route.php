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

		$this->setRoutes($routes);
	}
}
