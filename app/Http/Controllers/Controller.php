<?php

class Controller
{
    public $model;
    public View $view;

    function __construct()
    {
        $this->view = new View();
    }
}
