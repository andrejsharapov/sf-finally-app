<?php

class ControllerHome extends Controller
{
    public function __construct()
    {
        $this->model = new ModelHome();
        $this->view = new View();
    }

    public function index()
    {
        $data = $this->model->handle();
        $this->view->generate('Home.php', 'Layout.php', $data);
    }
}
