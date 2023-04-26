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

        $this->update($data);
        $this->increment();
        $this->show($data);
    }

    public function increment(): ?array
    {
        $data = null;

        if (isset($_POST)) {
            $data['increment'] = $_POST;
            $data = $data['increment'];

            $this->model->incrementPaymentCount($data);
        }

        return $data;
    }

    public function show($data)
    {
        $data['offers'] = $this->model->offerList();

        $this->view->generate('Offers.php', 'Layout.php', $data);
    }

    public function update($data)
    {
        $data['activateOffer'] = $this->model->activateOffer();
        $data['unActivateOffer'] = $this->model->unActivateOffer();
    }
}
