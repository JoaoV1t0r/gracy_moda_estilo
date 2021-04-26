<?php

namespace App\Controllers;

use App\Classes\Store;
use MF\Controller\Action;
use MF\Model\Container;

class AdminControllers extends Action
{
    // ====================================================================================
    public function adminLogin()
    {
        if (Store::clienteLogado()) {
            header('Location:' . BASE_URL);
            return;
        }

        if (Store::adminLogado()) {
            header('Location:' . BASE_URL . 'admin/home');
            return;
        }

        $this->renderAdmin('login_admin', 'layout_admin_login');
    }

    // ====================================================================================
    public function validaLogin()
    {
        if (Store::clienteLogado()) {
            header('Location:' . BASE_URL);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location:' . BASE_URL . 'admin/login');
            return;
        }

        $admin = Container::getModel('Admin');
        $admin->nome_admin = trim($_POST['nome_admin']);
        $admin->senha_admin = trim($_POST['senha_admin']);

        $adminLogado = $admin->validaLoginAdmin();
        if (is_bool($adminLogado)) {
            header('Location:' . BASE_URL . 'admin/login?erro=true');
            return;
        } else {
            $_SESSION['id_admin'] = $adminLogado->id_admin;
            $_SESSION['nome_admin'] = $adminLogado->nome_admin;
            $_SESSION['adminLogado'] = true;
            header('Location:' . BASE_URL . 'admin/home');
        }
    }

    // ====================================================================================
    public function adminSair()
    {
        if (Store::clienteLogado()) {
            header('Location:' . BASE_URL);
            return;
        }

        if (Store::adminLogado()) {
            unset($_SESSION['id_admin']);
            unset($_SESSION['admin_nome']);
            unset($_SESSION['adminLogado']);
            header('Location:' . BASE_URL);
        } else {
            header('Location:' . BASE_URL . 'admin/login');
            return;
        }
    }

    // ====================================================================================
    public function adminHome()
    {
        if (Store::clienteLogado()) {
            header('Location:' . BASE_URL);
            return;
        }

        if (!Store::adminLogado()) {
            header('Location:' . BASE_URL . 'admin/login');
            return;
        }

        $pedidos = Container::getModel('Pedido');
        $this->view->listaPedidos = [
            'pedidosTotal' => $pedidos->totalPedidos(),
            'pedidosPendentes' => $pedidos->totalPedidosPendentes(),
            'pedidosPreparo' => $pedidos->totalPedidosPreparo(),
            'pedidosEnviados' => $pedidos->totalPedidosEnviados(),
            'pedidosFinalizados' => $pedidos->totalPedidosFinalizados()
        ];

        $this->renderAdmin('home_admin', 'layout_admin');
    }
}
