<?php

namespace App\Models;

use MF\Model\Model;

class Admin extends Model
{
    private $id_admin;
    private $nome_admin;
    private $senha_admin;


    //=============================================================================================
    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }

    //=============================================================================================
    public function __get($value)
    {
        return $this->$value;
    }

    //=============================================================================================
    public function validaLoginAdmin()
    {
        //validarlogin do novo cliente
        $query = "
            SELECT
                *
            FROM
                admin
            where
                nome_admin = :nome_admin
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome_admin', $this->nome_admin);
        $stmt->execute();


        $admin = $stmt->fetchAll(\PDO::FETCH_CLASS)[0];

        if (!password_verify($this->senha_admin, $admin->senha_admin)) {
            return false;
        } else {
            return $admin;
        }
    }
}
