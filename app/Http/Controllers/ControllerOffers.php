<?php

class ControllerOffers extends Controller
{
    public function __construct()
    {
        $this->model = new ModelOffers();
        $this->view = new View();
    }

    function index()
    {
        $data = $this->model->handle();
        $data['offers'] = $this->model->offerList();
        $this->view->generate('Offers.php', 'Layout.php', $data);
    }
}
