<?php

require_once __DIR__ . '/../dotenv.php';

class DB
{
    /**
     * @return mysqli
     */
    public function getDatabase(): mysqli
    {
        $db_host = $_ENV['DB_HOST'];
        $dp_port = $_ENV['DB_PORT'];
        $db_database = $_ENV['DB_DATABASE'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];

        return new mysqli($db_host, $db_username, $db_password, $db_database);
    }
}