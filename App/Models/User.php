<?php

namespace App\Models;

use MF\Model\Model;

class User extends Model
{
    private $id_cliente;
    private $email;
    private $nome;
    private $senha;
    private $rua;
    private $numero_residencia;
    private $cidade;
    private $cep;
    private $telefone;
    private $purl;
    private $ativo;

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
    public function validacaoEmail()
    {
        $query = "
            SELECT
                email
            FROM
                clientes
            where
                email = :email
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->email);

        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($resultado) != 0) {
            return true;
        }
    }

    //=============================================================================================
    public function registraCliente()
    {
        $query = "
            insert into
                clientes
            values(
                0, :nome, :email, :senha, :rua, :numero_residencia, :cidade, :cep, :telefone, :purl, :ativo, NOW(), NOW(), NULL
        )";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->nome);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':senha', $this->senha);
        $stmt->bindValue(':rua', $this->rua);
        $stmt->bindValue(':numero_residencia', $this->numero_residencia);
        $stmt->bindValue(':cidade', $this->cidade);
        $stmt->bindValue(':cep', $this->cep);
        $stmt->bindValue(':telefone', $this->telefone);
        $stmt->bindValue(':purl', $this->purl);
        $stmt->bindValue(':ativo', $this->ativo);

        $stmt->execute();

        return true;
    }

    //=============================================================================================
    public function confirmaEmail()
    {
        //validar e-mail do novo cliente
        $query = "
            SELECT
                *
            FROM
                clientes
            where
                purl = :purl
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':purl', $this->purl);
        $stmt->execute();

        $resultados = $stmt->fetchAll(\PDO::FETCH_CLASS);
        //verifica se foi encontrado o cliente
        if (count($resultados) != 1) {
            return false;
        }

        //Cliente encontrado
        $this->id_cliente = $resultados[0]->id_cliente;

        //atualizar os dados do cliente

        $query2 = "
            update
                clientes
            set
                purl = null,
                ativo = 1,
                update_at = NOW()
            where
                id_cliente = :id_cliente
        ";

        $stmt2 = $this->db->prepare($query2);
        $stmt2->bindValue(':id_cliente', $this->id_cliente);
        $stmt2->execute();

        return true;
    }

    //=============================================================================================
    public function validaLogin()
    {
        //validarlogin do novo cliente
        $query = "
            SELECT
                *
            FROM
                clientes
            where
                email = :email and ativo = 1 and deleted_at is null
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->email);
        $stmt->execute();

        $resultados =  $stmt->fetchAll(\PDO::FETCH_CLASS);
        $usuario = $resultados[0];
        if (!password_verify($this->senha, $usuario->senha)) {
            return false;
        } else {
            return $usuario;
        }
    }
}
