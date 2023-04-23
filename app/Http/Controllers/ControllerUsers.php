<?php

class ControllerUsers extends Controller
{
    public function __construct()
    {
        $this->model = new ModelUsers();
        $this->view = new View();
    }

    function index()
    {
        $data = $this->model->handle();
        $this->view->generate('Users.php', 'Layout.php', $data);
    }
}
