<?php

namespace App;

class Connection
{
    public static function getDb()
    {
        try {
            $conn = new \PDO(
                'mysql:' .
                    'host=' . MYSQL_SERVER . ';' .
                    'dbname=' . MYSQL_DATABASE . ';' .
                    'charset=' . MYSQL_CHARSET,
                MYSQL_USER,
                MYSQL_PASS,
                array(\PDO::ATTR_PERSISTENT => true)
            );

            return $conn;
        } catch (\PDOException $e) {
            echo "Erro: " . $e;
        }
    }
}
