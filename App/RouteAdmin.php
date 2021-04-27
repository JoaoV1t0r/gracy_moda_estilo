<?php

namespace App;

use MF\Init\Bootstrap;

class RouteAdmin extends Bootstrap
{

    protected function initRoutes()
    {
        $routes['admin/login'] = array(
            'route' => '/admin/login',
            'controller' => 'AdminControllers',
            'action' => 'adminLogin'
        );
        $routes['admin/confirma_login'] = array(
            'route' => '/admin/confirma_login',
            'controller' => 'AdminControllers',
            'action' => 'validaLogin'
        );
        $routes['admin/sair'] = array(
            'route' => '/admin/sair',
            'controller' => 'AdminControllers',
            'action' => 'adminSair'
        );
        $routes['home_admin'] = array(
            'route' => '/admin/home',
            'controller' => 'AdminControllers',
            'action' => 'adminHome'
        );
        $routes['pedidos_admin'] = array(
            'route' => '/admin/pedidos',
            'controller' => 'AdminControllers',
            'action' => 'adminPedidos'
        );

        $this->setRoutes($routes);
    }
}
