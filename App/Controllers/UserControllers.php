<?php

namespace App\Controllers;

use App\Classes\EnviarEmail;
use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class UserControllers extends Action
{
	// ===========================================================================
	public function clienteLogado()
	{
		if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
			return true;
		} else {
			return false;
		}
	}

	// ===========================================================================
	public function registraUsuario()
	{
		if ($this->clienteLogado()) {
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
		$user->endereco = trim($_POST['endereco']);
		$user->cidade = trim($_POST['cidade']);
		$user->cep = trim($_POST['cep']);
		$user->telefone = trim($_POST['telefone']);
		$user->purl = Store::criarHash();
		$user->ativo = 0;

		//Verifica os campos
		if ($user->email == '' || $user->nome == '' || $user->senha == '' || $user->endereco == '' || $user->cidade == '' || $user->cep == '' || $user->telefone == '') {
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
					$this->view->clienteLogado = $this->clienteLogado();
					$this->render('envio_email', 'layout_fixed');
					return;
				}
			}
		}
	}

	// ===========================================================================
	public function confirmarEmail()
	{
		if ($this->clienteLogado()) {
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
			$this->view->clienteLogado = $this->clienteLogado();
			$this->render('email_confirmado', 'layout_fixed');
			return;
		} else {
			header('Location: /');
			return;
		}
	}

	// ===========================================================================
	public function validaLogin()
	{
		if ($this->clienteLogado()) {
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
			header('Location: /criar_conta?erro=campoVazio');
		}

		//Valida email
		if (!$user->validacaoEmail()) {
			header('Location: /criar_conta?erro=email');
			return;
		}

		$usuario = $user->validaLogin();
		if (is_bool($usuario)) {
			header('Location: /criar_conta?erro=senha');
		} else {
			$_SESSION['cliente'] = $usuario->nome;
			$_SESSION['email'] = $usuario->email;
			$_SESSION['endereco'] = $usuario->endereco;
			$_SESSION['cidade'] = $usuario->cidade;
			$_SESSION['cep'] = $usuario->cep;
			$_SESSION['telefone'] = $usuario->contato;
			$_SESSION['logado'] = true;
			header('Location: /');
		}
	}

	// ===========================================================================
	public function sair()
	{
		//remover  as variaveis de sessão e retorna para o inicio
		unset($_SESSION['cliente']);
		unset($_SESSION['email']);
		unset($_SESSION['endereco']);
		unset($_SESSION['cidade']);
		unset($_SESSION['cep']);
		unset($_SESSION['telefone']);
		unset($_SESSION['logado']);
		header('Location: /');
	}
}
