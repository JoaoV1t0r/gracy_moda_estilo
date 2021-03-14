<?php

namespace App\Classes;

class Store
{
    // ===========================================================================
    public static function criarHash($numeroCaracteres = 12)
    {
        $chars = '01234567890123456789abcdefghijklmnopqrstuwvxzABCDEFGHIJKLMNOPQRSTUWVXZABCDEFGHIJKLMNOPQRSTUWVXZ';
        return substr(str_shuffle($chars),  0, $numeroCaracteres);
    }


    // ===========================================================================
    public static function clienteLogado()
    {
        if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
            return true;
        } else {
            return false;
        }
    }
}
