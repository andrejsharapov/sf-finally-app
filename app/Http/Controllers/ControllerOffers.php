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
        $data['activateOffer'] = $this->model->activateOffer();
        $data['unActivateOffer'] = $this->model->unActivateOffer();

        $this->view->generate('Offers.php', 'Layout.php', $data);
    }
}
