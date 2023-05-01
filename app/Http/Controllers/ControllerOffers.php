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

        $this->update();
        $this->increment();
        $this->followers();
        $this->show($data);
    }

    public function followers()
    {
        $this->model->followToOffer();
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

    public function update()
    {
        $this->model->activateOffer();
        $this->model->unActivateOffer();
    }

    public function show($data)
    {
        $data['offers'] = $this->model->offerList();

        $this->view->generate('Offers.php', 'Layout.php', $data);
    }
}
