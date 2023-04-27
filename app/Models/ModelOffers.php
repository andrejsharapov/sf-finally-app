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
            'payment' => $data['payment'],
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
        $user_id = $_SESSION['user']['id'];
        $query_offer = null;

        if ($role_id == '1') {
            $query_offer = "SELECT * FROM $table_offers";
        } else if ($role_id == '2') {
            $query_offer = "SELECT * FROM $table_users JOIN $table_offers ON $table_offers.user_id = $table_users.id WHERE $table_users.id = $user_id";
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

    /**
     * @param array $data
     * @return array
     */
    public function followsData(array $data): array
    {
        return [
            'form' => $data['form'] ?? null,
            'offer_id' => $data['offer_id'] ?? null,
            'author_id' => $data['author_id'] ?? null,
            'follower_id' => $data['follower_id'] ?? null,
            'date' => (new DateTime())->format('Y-m-d H:i:s') ?? null,
        ];
    }

    public function followToOffer()
    {
        $db_link = $this->dataBaseLink();
        $follower = $this->followsData($_POST) ?? null;
//        $table_offers = $this->db_table;
        $table_follows = 'follows';

        if (!empty($follower) && $follower['form'] == 'following') {
//            $check_follow = null; // "SELECT * FROM $table_follows JOIN $this->db_table ON $table_follows.follower_id = $table_offers.user_id WHERE $table_users.id = $user_id";
//            $get_info = mysqli_query($db_link, $check_follow) or die(mysqli_error($db_link));
//
//            if (mysqli_num_rows($get_info) > 0) {
//                //
//            } else {
                $set_follower = mysqli_prepare($db_link, "INSERT INTO $table_follows (offer_id, author_id, follower_id, date) " . " VALUES (?, ?, ?, ?)");

                mysqli_stmt_bind_param($set_follower, "ssss", $follower['offer_id'], $follower['author_id'], $follower['follower_id'], $follower['date']);
                mysqli_stmt_execute($set_follower);
                mysqli_stmt_close($set_follower);
                mysqli_close($db_link);
//            }
        }
    }

    public function incrementPaymentCount($data = null)
    {
        $db_link = $this->dataBaseLink();
        $id = $data['send_id'] ?? null;
        $trans = $data['send_transition'] ?? null;
        $payment = $data['send_payment'] ?? null;

        $trans_i = $trans + 1;
        $total_cost = (int)$trans_i * $payment;

        if (!empty($data)) {
            $increment_offer_trans = "UPDATE $this->db_table SET transitions = '$trans_i' WHERE id = '$id'";
            $update_total_cost = "UPDATE $this->db_table SET total_cost = '$total_cost' WHERE id = '$id'";

            mysqli_query($db_link, $increment_offer_trans) or die(mysqli_error($db_link));
            mysqli_query($db_link, $update_total_cost) or die(mysqli_error($db_link));

//            if ($data['form'] == 'form_send') {
//                $select_offer = "SELECT * FROM $this->db_table WHERE id = $id";
//                $get_offer = mysqli_query($db_link, $select_offer) or die(mysqli_error($db_link));
//                $offer_found = mysqli_num_rows($get_offer) > 0;
//
//                if (!$offer_found) {
//                    $_SESSION['offerNotFound'] = 'Предложение было удалено или скрыто рекламодателем.';
//                }
//            }

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
