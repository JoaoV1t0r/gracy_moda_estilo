<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class IndexControllers extends Action
{

	public function clienteLogado()
	{
		if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
			return true;
		} else {
			return false;
		}
	}

	public function index()
	{
		//Renderização da index
		$this->view->clienteLogado = $this->clienteLogado();
		$this->render('index');
	}
	public function login()
	{
		//Renderização da index
		$this->view->clienteLogado = $this->clienteLogado();
		$this->render('login', 'layout_fixed');
	}

	public function criarUsuario()
	{
		//Renderização da index
		$this->view->clienteLogado = $this->clienteLogado();
		$this->render('criar_conta');
	}
}
