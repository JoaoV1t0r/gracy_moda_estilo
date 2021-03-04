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

		$this->setRoutes($routes);
	}
}
