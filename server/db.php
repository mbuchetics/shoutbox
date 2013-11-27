<?php

require_once 'helpers.php';

class Database
{
    private static $factory;
    public static function getFactory()
    {
        if (!self::$factory)
            self::$factory = new Database();
        return self::$factory;
    }

    private $db;

    public function getConnection() {
        $db_hostname = getConfig('db_hostname');
        $db_database = getConfig('db_database');
        $db_username = getConfig('db_username');
        $db_password = getConfig('db_password');

        if (!$this->db)
            $this->db = new PDO("mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password);
        return $this->db;
    }
}

?>