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
        $this->render('login_admin', 'layout_admin');
    }

    // ====================================================================================
    public function validaLogin()
    {
        if (Store::clienteLogado()) {
            header('Location:' . BASE_URL);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location:' . BASE_URL . 'login_admin');
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
        $this->render('home_admin', 'layout_admin');
    }
}
