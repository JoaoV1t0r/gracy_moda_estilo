<?php

namespace App\Controllers;

use App\Classes\EnviarEmail;
use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class UserControllers extends Action
{
	// ===========================================================================
	public function registraUsuario()
	{
		if (Store::clienteLogado()) {
			header('Location: /');
		}

		//verifica se houve submissão de formulario
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
		}

		//Verifica a senha
		if ($_POST['senha'] !== $_POST['senhaConfirmada']) {
			header('Location: /criar_conta?erro=senha');
			return;
		}

		//Definindo variaveis
		$user = Container::getModel('User');
		$user->email = trim($_POST['email']);
		$user->nome = trim($_POST['nome']);
		$user->senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
		$user->rua = trim($_POST['rua']);
		$user->numero_residencia = trim($_POST['numero_residencia']);
		$user->cidade = trim($_POST['cidade']);
		$user->cep = trim($_POST['cep']);
		$user->telefone = trim($_POST['telefone']);
		$user->purl = Store::criarHash();
		$user->ativo = 0;

		//Verifica os campos
		if ($user->email == '' || $user->nome == '' || $user->senha == '' || $user->numero_residencia == '' || $user->rua == '' || $user->cidade == '' || $user->cep == '' || $user->telefone == '') {
			header('Location: /criar_conta?erro=campoVazio');
			return;
		}

		//verificação de email e registro de novo cliente
		if ($user->validacaoEmail()) {
			header('Location: /criar_conta?erro=email');
			return;
		} else {
			if ($user->registraCliente()) {
				//Criar link purlpara enviar email
				$email = new EnviarEmail();
				$resultado = $email->EnviarEmailConfirmacaoNovoCLiente($user->email, $user->purl);
				if ($resultado) {
					//Carrega o layout
					$this->view->clienteLogado = Store::clienteLogado();
					$this->render('envio_email');
					return;
				}
			}
		}
	}

	// ===========================================================================
	public function confirmarEmail()
	{
		if (Store::clienteLogado()) {
			header('Location: /');
		}

		//Verificar se existe um purl
		if (!isset($_GET['purl'])) {
			header('Location: /');
		}

		//Verifica se o purl é valido
		$user = Container::getModel('User');
		$user->purl = $_GET['purl'];
		if (strlen($user->purl) != 12) {
			header('Location: /');
		}

		if ($user->confirmaEmail()) {
			//Carrega o layout
			$this->view->clienteLogado = Store::clienteLogado();
			$this->render('email_confirmado');
			return;
		} else {
			header('Location: /');
			return;
		}
	}

	// ===========================================================================
	public function validaLogin()
	{
		if (Store::clienteLogado()) {
			header('Location: /');
		}

		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
		}

		//define os dados
		$user = Container::getModel('User');
		$user->email = trim($_POST['email']);
		$user->senha = trim($_POST['senha']);

		//verifica os campos
		if ($user->email == '' || $user->senha == '' || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
			header('Location: /login?erro=campoVazio');
			return;
		}

		//Valida email
		if (!$user->validacaoEmail()) {
			header('Location: /login?erro=email');
			return;
		}

		$usuario = $user->validaLogin();
		if (is_bool($usuario)) {
			header('Location: /login?erro=senha');
			return;
		} else {
			$_SESSION['cliente'] = $usuario->nome;
			$_SESSION['email'] = $usuario->email;
			$_SESSION['rua'] = $usuario->rua;
			$_SESSION['numero_residencia'] = $usuario->numero_residencia;
			$_SESSION['cidade'] = $usuario->cidade;
			$_SESSION['cep'] = $usuario->cep;
			$_SESSION['telefone'] = $usuario->contato;
			$_SESSION['logado'] = true;
			if (isset($_SESSION['login_finalizar_pedido']) && $_SESSION['login_finalizar_pedido'] == true) {
				unset($_SESSION['login_finalizar_pedido']);
				header('Location: /finalizar_pedido');
			} else {
				header('Location: /');
			}
		}
	}

	// ===========================================================================
	public function sair()
	{
		//remover  as variaveis de sessão e retorna para o inicio
		unset($_SESSION['cliente']);
		unset($_SESSION['email']);
		unset($_SESSION['rua']);
		unset($_SESSION['numero_residencia']);
		unset($_SESSION['cidade']);
		unset($_SESSION['cep']);
		unset($_SESSION['telefone']);
		unset($_SESSION['logado']);
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
		header('Location: /');
	}

	// ===========================================================================
	public function adicionarDadosAlternativos()
	{
		if (Store::clienteLogado()) {
			header('Location: /');
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
		}
		$_SESSION['dadosAlternativos'] = [
			'cidadeAlternativa' => $_POST['cidadeAlternativa'],
			'ruaAlternativa' => $_POST['ruaAlternativa'],
			'numeroResidencia' => $_POST['numeroResidencia'],
			'cepAlternativa' => $_POST['cepAlternativa']
		];
	}

	// ===========================================================================
	public function removerDadosAlternativos()
	{
		if (Store::clienteLogado()) {
			header('Location: /');
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
		}
		if ($_POST['remover'] == true) {
			if (isset($_SESSION['dadosAlternativos'])) {
				unset($_SESSION['dadosAlternativos']);
			}
		}
	}
}
