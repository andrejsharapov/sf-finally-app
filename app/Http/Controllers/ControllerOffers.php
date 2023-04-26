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
        $model = $this->model;
        $data = $model->handle();

        $this->update($data, $model);
        $this->show($data, $model);
    }

    public function show($data, $model)
    {
        $data['offers'] = $model->offerList();

        $this->view->generate('Offers.php', 'Layout.php', $data);
    }

    public function update($data, $model)
    {
        $data['activateOffer'] = $model->activateOffer();
        $data['unActivateOffer'] = $model->unActivateOffer();
    }
}
