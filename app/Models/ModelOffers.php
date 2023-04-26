<?php

class ModelOffers extends Model
{
    protected string $db_table = 'offers';
    public $offers = null;

    /**
     * @param array $data
     * @return array
     */
    public function getOffersInfo(array $data): array
    {
        return [
            'form' => $data['form'],
            'user_id' => $_SESSION['user']['id'] ?? null,
            'created' => (new DateTime())->format('Y-m-d H:i:s') ?? null,
            'title' => $data['title'],
            'payment' => $data['count'],
            'url' => $data['path'],
            'theme' => $data['theme'],
        ];
    }

    public function handle()
    {
        $form = $_POST['form'] ?? null;

        if (!empty($_POST) && $form == 'create_offer') {
            $db_link = $this->dataBaseLink();
            $offer = $this->getOffersInfo($_POST);
            $query = mysqli_prepare($db_link, "INSERT INTO $this->db_table (title, payment, url, theme, user_id, created) " . " VALUES (?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($query, "ssssss", $offer['title'], $offer['payment'], $offer['url'], $offer['theme'], $offer['user_id'], $offer['created']);
            mysqli_stmt_execute($query);
            mysqli_stmt_close($query);
            mysqli_close($db_link);

            if ($query) {
                $_SESSION['checkOffer'] = 'Ваше предложение успешно создано!';
            }

            header('Refresh: 0');
        }
    }

    /**
     * @return array|void
     */
    public function offerList()
    {
        $db_link = $this->dataBaseLink();
        $table_offers = $this->db_table;
        $table_users = 'users';
        $role_id = $_SESSION['user']['role_id'];

        if ($role_id == '1') {
            $query_offer = "SELECT * FROM $table_offers";
        } else if ($role_id == '2') {
            $query_offer = "SELECT * FROM $table_users LEFT JOIN $table_offers ON $table_offers.user_id = $table_users.id";
        } else if ($role_id == '3') {
            $query_offer = "SELECT * FROM $table_offers WHERE state = 1";
        }

        $set_list = mysqli_query($db_link, $query_offer) or die(mysqli_error($db_link));

        for (
            $this->offers = [];
            $row = mysqli_fetch_assoc($set_list);
            $this->offers[] = $row
        ) {
        }

        return $this->offers ?? [];
    }

    public function unActivateOffer()
    {
        $this->updateOfferState('inactive_offer');
    }

    public function activateOffer()
    {
        $this->updateOfferState('active_offer');
    }

    public function incrementPaymentCount($data = null)
    {
        $db_link = $this->dataBaseLink();
        $id = $data['send_id'] ?? null;
        $trans = $data['send_transition'] ?? null;
//        $payment = $data['send_payment'] ?? null;

        $trans_i = $trans + 1;
//        $total_cost = $trans_i * $payment;

        if (!empty($data)) {
            $increment_offer_trans = "UPDATE $this->db_table SET transitions = '$trans_i' WHERE id = '$id'";
//            $update_total_cost = "UPDATE $this->db_table SET transitions = '$total_cost' WHERE id = '$id'";

            mysqli_query($db_link, $increment_offer_trans) or die(mysqli_error($db_link));
//            mysqli_query($db_link, $update_total_cost) or die(mysqli_error($db_link));

            header('Refresh: 0');
        }
    }

    /**
     * @return mysqli|void
     */
    public function dataBaseLink()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];

        // charset
        mysqli_query($db_link, "SET NAMES 'utf8'");

        // check connection
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            die();
        }

        return $db_link;
    }

    /**
     * @param string $form
     */
    public function updateOfferState(string $form = null)
    {
        $db_link = $this->dataBaseLink();
        $data = $_POST;
        $id = $data['val_id'] ?? null;
        $val = $data['set_state'] ?? null;

        if (!empty($data) && $data['form'] == $form) {
            $update_offer_state = "UPDATE $this->db_table SET state = '$val' WHERE id = '$id'";

            mysqli_query($db_link, $update_offer_state) or die(mysqli_error($db_link));
            header('Refresh: 0');
        }
    }
}
