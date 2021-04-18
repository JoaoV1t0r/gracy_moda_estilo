<?php

namespace App;

use MF\Init\Bootstrap;

class RouteAdmin extends Bootstrap
{

    protected function initRoutes()
    {
        $routes['login'] = array(
            'route' => '/admin/login',
            'controller' => 'AdminControllers',
            'action' => 'adminLogin'
        );
        $routes['confirma_login'] = array(
            'route' => '/admin/confirma_login',
            'controller' => 'AdminControllers',
            'action' => 'validaLogin'
        );
        $routes['home_admin'] = array(
            'route' => '/admin/home',
            'controller' => 'AdminControllers',
            'action' => 'adminHome'
        );

        $this->setRoutes($routes);
    }
}
