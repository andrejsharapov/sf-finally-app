<?php

session_start();

class Model
{
    protected ?mysqli $db = null;

    public function __construct()
    {
        $this->db = (new DB)->getDatabase();
    }
}