<?php

namespace App\Controllers;

use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class IndexControllers extends Action
{
	// ====================================================================================
	public function index()
	{
		//Renderização da index
		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();
		$produto = Container::getModel('Produto');
		$this->view->maisVendidos = $produto->getMaisVendidos();
		$this->render('index');
	}

	// ====================================================================================
	public function login()
	{
		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();
		$this->render('login');
	}

	// ====================================================================================
	public function criarUsuario()
	{
		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();
		$this->render('criar_conta');
	}

	// ====================================================================================
	public function session()
	{
		//Renderização da index
		echo '<pre>';
		print_r($_SESSION);
	}
}
