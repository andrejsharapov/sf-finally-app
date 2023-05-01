<?php

session_start();

class Model
{
    protected ?mysqli $db = null;

    const ROLES = 'roles';
    const USERS = 'users';
    const OFFERS = 'offers';
    const FOLLOWERS = 'follows';
    const MOVES = 'moves';

    public function __construct()
    {
        $this->db = (new DB)->getDatabase();
    }
}