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
			return;
		}

		//verifica se houve submissão de formulario
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
			return;
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
		$user->bairro = trim($_POST['bairro']);
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
					$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
					$this->view->categorias = Store::getCategoriasView();
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
			return;
		}

		//Verificar se existe um purl
		if (!isset($_GET['purl'])) {
			header('Location: /');
			return;
		}

		//Verifica se o purl é valido
		$user = Container::getModel('User');
		$user->purl = $_GET['purl'];
		if (strlen($user->purl) != 12) {
			header('Location: /');
			return;
		}

		if ($user->confirmaEmail()) {
			//Carrega o layout
			$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
			$this->view->categorias = Store::getCategoriasView();
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
			header('Location: ' . BASE_URL);
			return;
		}

		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: ' . BASE_URL);
			return;
		}

		//define os dados
		$user = Container::getModel('User');
		$user->email = trim($_POST['email']);
		$user->senha = trim($_POST['senha']);

		//verifica os campos
		if ($user->email == '' || $user->senha == '' || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
			header('Location: ' . BASE_URL . 'login?erro=campoVazio');
			return;
		}

		//Valida email
		if (!$user->validacaoEmail()) {
			header('Location: ' . BASE_URL . 'login?erro=2');
			return;
		}

		$usuario = $user->validaLogin();
		if (is_bool($user->validaLogin())) {
			header('Location:' . BASE_URL . 'login?erro=1');
			return;
		} else {
			$_SESSION['id_cliente'] = $usuario->id_cliente;
			$_SESSION['cliente'] = $usuario->nome;
			$_SESSION['email'] = $usuario->email;
			$_SESSION['rua'] = $usuario->rua;
			$_SESSION['numero_residencia'] = $usuario->numero_residencia;
			$_SESSION['bairro'] = $usuario->bairro;
			$_SESSION['cidade'] = $usuario->cidade;
			$_SESSION['cep'] = $usuario->cep;
			$_SESSION['telefone'] = $usuario->contato;
			$_SESSION['logado'] = true;
			if (isset($_SESSION['login_finalizar_pedido']) && $_SESSION['login_finalizar_pedido'] == true) {
				unset($_SESSION['login_finalizar_pedido']);
				header('Location: ' . BASE_URL . 'finalizar_pedido');
			} else {
				header('Location: ' . BASE_URL . 'minha_conta');
			}
		}
	}

	// ===========================================================================
	public function exibirUser()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}

		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();

		$user = Container::getModel('User');
		$user->id_cliente = $_SESSION['id_cliente'];
		$this->view->dadosUser = $user->getDadosCliente()[0];

		$this->render('perfil');
	}

	// ===========================================================================
	public function sair()
	{
		//remover  as variaveis de sessão e retorna para o inicio
		unset($_SESSION['id_cliente']);
		unset($_SESSION['cliente']);
		unset($_SESSION['email']);
		unset($_SESSION['rua']);
		unset($_SESSION['numero_residencia']);
		unset($_SESSION['bairro']);
		unset($_SESSION['cidade']);
		unset($_SESSION['cep']);
		unset($_SESSION['telefone']);
		unset($_SESSION['logado']);
		unset($_SESSION['codigo_pedido']);
		if (isset($_SESSION['dadosAlternativos'])) {
			unset($_SESSION['dadosAlternativos']);
		}
		if (isset($_SESSION['codigo_pedido'])) {
			unset($_SESSION['codigo_pedido']);
		}
		if (isset($_SESSION['metodo_envio'])) {
			unset($_SESSION['metodo_envio']);
		}
		header('Location: /');
	}

	// ===========================================================================
	public function adicionarDadosAlternativos()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
			return;
		}
		$_SESSION['dadosAlternativos'] = [
			'cepAlternativa' => $_POST['cepAlternativa'],
			'numeroResidencia' => $_POST['numeroResidencia'],
			'ruaAlternativa' => $_POST['ruaAlternativa'],
			'bairroAlternativa' =>  $_POST['bairroAlternativa'],
			'cidadeAlternativa' => $_POST['cidadeAlternativa'],
		];
	}

	// ===========================================================================
	public function removerDadosAlternativos()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
			return;
		}
		if ($_POST['remover'] == true) {
			if (isset($_SESSION['dadosAlternativos'])) {
				unset($_SESSION['dadosAlternativos']);
			}
		}
	}

	// ===========================================================================
	public function editarDadosUser()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
		}
		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();
		$this->render('editar_dados_usuario');
	}

	// ===========================================================================
	public function salvarNewDadosUser()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
			return;
		}
		//Definindo variaveis
		$user = Container::getModel('User');
		$user->nome = trim($_POST['nome']);
		$user->rua = trim($_POST['rua']);
		$user->numero_residencia = trim($_POST['numero_residencia']);
		$user->bairro = trim($_POST['bairro']);
		$user->cidade = trim($_POST['cidade']);
		$user->cep = trim($_POST['cep']);
		$user->telefone = trim($_POST['telefone']);

		//Verifica os campos
		if ($user->nome == '' || $user->numero_residencia == '' || $user->rua == '' || $user->cidade == '' || $user->cep == '' || $user->telefone == '') {
			header('Location: ' . BASE_URL . 'alterar_dados_pessoais?erro=campoVazio');
			return;
		} else {
			//Salva a alteração no banco de dados
			$user->id_cliente = intval($_SESSION['id_cliente']);
			$user->SalvarEdicaoDadosUser();

			//Altera dados da sessão
			$_SESSION['email'] = $user->email;
			$_SESSION['cliente'] = $user->nome;
			$_SESSION['rua'] = $user->rua;
			$_SESSION['numero_residencia'] = $user->numero_residencia;
			$_SESSION['bairro'] = $user->bairro;
			$_SESSION['cidade'] = $user->cidade;
			$_SESSION['cep'] = $user->cep;
			$_SESSION['telefone'] = $user->telefone;
			header('Location: ' . BASE_URL . 'minha_conta?alteracao=dadosPessoais');
			return;
		}
	}

	// ===========================================================================
	public function alterarSenhaUser()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}
		$this->view->quantidadeCarrinho = Store::quantidadeCarrinho();
		$this->view->categorias = Store::getCategoriasView();
		$this->view->clienteLogado = Store::clienteLogado();
		$this->render('editar_senha_usuario');
	}

	// ===========================================================================
	public function salvarNewSenhaUser()
	{
		if (!Store::clienteLogado()) {
			header('Location: /');
			return;
		}
		//Verifica se foi feito o post
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: /');
			return;
		}

		//Verifica a senha
		if ($_POST['novaSenha'] !== $_POST['senhaNovaConfirmada']) {
			header('Location: ' . BASE_URL . 'alterar_senha?erro=senha');
			return;
		}

		//Definindo variaveis
		$user = Container::getModel('User');
		$user->id_cliente = $_SESSION['id_cliente'];
		$senhaAtual = trim($_POST['senhaAtual']);
		$novaSenha = password_hash($_POST['novaSenha'], PASSWORD_DEFAULT);

		//Validações das senhas
		if ($senhaAtual == ''  || $novaSenha == '') {
			header('Location: ' . BASE_URL . 'alterar_senha?erro=campoVazio');
			return;
		}

		//Verifica se a senha atual está correta
		if ($user->validaSenha($senhaAtual)) {
			$user->senha = $novaSenha;
			$user->alterarSenha();
			header('Location: ' . BASE_URL . 'minha_conta?alteracao=senha');
			return;
		} else {
			header('Location: ' . BASE_URL . 'alterar_senha?erro=senha2');
			return;
		}
	}
}
