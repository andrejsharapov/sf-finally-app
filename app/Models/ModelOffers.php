<?php

class ModelOffers extends Model
{
    public array $offers = [];

    /**
     * @param array $data
     * @return array
     */
    public function getOffersInfo(array $data): array
    {
        return [
            'form' => $data['form'],
            'creator_id' => $_SESSION['user']['id'] ?? null,
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
            $query = mysqli_prepare($db_link, "INSERT INTO " . self::OFFERS . " (title, payment, url, theme, creator_id, created) " . " VALUES (?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($query, "ssssss", $offer['title'], $offer['payment'], $offer['url'], $offer['theme'], $offer['creator_id'], $offer['created']);
            mysqli_stmt_execute($query);
            mysqli_stmt_close($query);
            mysqli_close($db_link);

            if ($query) {
                $_SESSION['checkOffer'] = 'Ваше предложение успешно создано!';
            }

            header('Refresh: 0');

            unset($_SESSION['checkOffer']);
        }
    }

    /**
     * @return array|void
     */
    public function offerList()
    {
        $db_link = $this->dataBaseLink();

        $table_offers = self::OFFERS;
        $table_users = self::USERS;

        $role_id = $_SESSION['user']['role_id'] ?? null;
        $user_id = $_SESSION['user']['id'] ?? null;

        $query_offer = null;

        if ($role_id == '1') {
            $query_offer = "SELECT * FROM $table_offers";
        } else if ($role_id == '2') {
            $query_offer = "SELECT * FROM $table_users JOIN $table_offers ON $table_offers.creator_id = $table_users.id WHERE $table_users.id = $user_id";
        } else if ($role_id == '3') {
            $query_offer = "SELECT * FROM $table_offers WHERE state = 1";
        }

        if (!empty($query_offer)) {
            $set_list = mysqli_query($db_link, $query_offer) or die(mysqli_error($db_link));

            for (
                $this->offers = [];
                $row = mysqli_fetch_assoc($set_list);
                $this->offers[] = $row
            ) {
            }
        }

        $this->offers = array_map(function ($el) {
            $el['followers'] = $this->followersCount($el['id']);

            return $el;
        }, $this->offers);

        $this->offers = array_map(function ($el) {
            $el['following'] = $this->getFollowingState($el['id']);

            return $el;
        }, $this->offers);

        $this->offers = array_map(function ($el) {
            $el['user_id'] = $_SESSION['user']['id'];
            $el['master_amount'] = $this->totalCostByMaster($el['id'], $el['user_id']);

            return $el;
        }, $this->offers);

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

    /**
     * @param $offer_id
     * @return int|string|void
     */
    public function getFollowingState($offer_id)
    {
        $db_link = $this->dataBaseLink();

        $user_id = $_SESSION['user']['id'];

        $follow_state = "SELECT * FROM " . self::FOLLOWERS . " as f WHERE f.offer_id = $offer_id AND f.follower_id = $user_id";
        $count_rows = mysqli_query($db_link, $follow_state) or die(mysqli_error($db_link));

        return mysqli_num_rows($count_rows);
    }

    /**
     * @param $offer_id
     * @return false|mixed|void
     */
    public function followersCount($offer_id)
    {
        $db_link = $this->dataBaseLink();

        $count_followers = "SELECT count(*) as count FROM " . self::OFFERS . " AS o JOIN " . self::FOLLOWERS . " AS f ON o.id = f.offer_id WHERE o.id = $offer_id";
        $count_rows = mysqli_query($db_link, $count_followers) or die(mysqli_error($db_link));

        $row = mysqli_fetch_row($count_rows);

        return reset($row);
    }

    public function followToOffer()
    {
        $db_link = $this->dataBaseLink();
        $follower = $this->followsData($_POST) ?? null;

        $table_offers = self::OFFERS;
        $table_follows = self::FOLLOWERS;

        $offer_id = $follower['offer_id'];
        $u_id = $follower['follower_id'];

        if (!empty($follower) && $follower['form'] == 'following') {
            $check_follow = "SELECT * FROM $table_offers AS o JOIN $table_follows AS f ON o.id = f.offer_id WHERE o.id = $offer_id AND f.follower_id = $u_id";
            $get_info = mysqli_query($db_link, $check_follow) or die(mysqli_error($db_link));

            if (mysqli_num_rows($get_info) > 0) {
                $check_follow = "DELETE FROM $table_follows WHERE offer_id = $offer_id AND follower_id = $u_id";

                mysqli_query($db_link, $check_follow) or die(mysqli_error($db_link));

            } else {
                $set_follower = mysqli_prepare($db_link, "INSERT INTO $table_follows (offer_id, author_id, follower_id, date) " . " VALUES (?, ?, ?, ?)");

                mysqli_stmt_bind_param($set_follower, "ssss", $follower['offer_id'], $follower['author_id'], $follower['follower_id'], $follower['date']);
                mysqli_stmt_execute($set_follower);
                mysqli_stmt_close($set_follower);
                mysqli_close($db_link);
            }
        }
    }

    /**
     * @param null $data
     */
    public function incrementPaymentCount($data = null)
    {
        $db_link = $this->dataBaseLink();

        $id = $data['send_id'] ?? null;
        $trans = $data['send_transition'] ?? null;
        $payment = $data['send_payment'] ?? null;

        $trans_i = $trans + 1;
        $total_cost = (int)$trans_i * $payment;

        if (!empty($data) && $data['form'] == 'form_send') {
            $increment_offer_trans = "UPDATE " . self::OFFERS . " SET transitions = '$trans_i', total_cost = '$total_cost' WHERE id = '$id'";

            mysqli_query($db_link, $increment_offer_trans) or die(mysqli_error($db_link));

            $this->setMasterMoves($db_link, $this->movesData($data));

            // TODO
//            if ($data['form'] == 'form_send') {
//                $select_offer = "SELECT * FROM $this->db_table WHERE id = $id";
//                $get_offer = mysqli_query($db_link, $select_offer) or die(mysqli_error($db_link));
//                $offer_found = mysqli_num_rows($get_offer) > 0;
//
//                if (!$offer_found) {
//                    $_SESSION['offerNotFound'] = 'Предложение было удалено или скрыто рекламодателем.';
//                }
//            }

            header('Location: /?url=offers');
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function movesData(array $data): array
    {
        return [
            'form' => $data['form'],
            'date' => (new DateTime())->format('Y-m-d H:i:s'),
            'offer_id' => $data['send_id'],
            'master_id' => $data['send_user_id'],
            'payment_offer' => $data['send_payment'],
        ];
    }

    /**
     * @param $offer_id
     * @param $user_id
     * @return false|mixed|void
     */
    public function totalCostByMaster($offer_id, $user_id)
    {
        $db_link = $this->dataBaseLink();

        $amount_query = "SELECT sum(payment_offer) FROM " . self::MOVES . " WHERE offer_id = $offer_id AND master_id = $user_id";
        $count_rows = mysqli_query($db_link, $amount_query) or die(mysqli_error($db_link));

//        return mysqli_num_rows($count_rows);
        $row = mysqli_fetch_row($count_rows);

        return reset($row);
    }

    /**
     * @param $db_link
     * @param $data
     * @return void
     */
    public function setMasterMoves($db_link, $data): void
    {
        $set_row = mysqli_prepare($db_link, "INSERT INTO " . self::MOVES . " (date, offer_id, master_id, payment_offer) VALUES (?, ?, ?, ?)");

        mysqli_stmt_bind_param($set_row, "ssss", $data['date'], $data['offer_id'], $data['master_id'], $data['payment_offer']);
        mysqli_stmt_execute($set_row);
        mysqli_stmt_close($set_row);
        mysqli_close($db_link);
    }

    /**
     * @param string|null $form
     */
    public function updateOfferState(string $form = null)
    {
        $db_link = $this->dataBaseLink();

        $data = $_POST;

        $id = $data['val_id'] ?? null;
        $val = $data['set_state'] ?? null;

        if (!empty($data) && $data['form'] == $form) {
            $update_offer_state = "UPDATE " . self::OFFERS . " SET state = '$val' WHERE id = '$id'";

            mysqli_query($db_link, $update_offer_state) or die(mysqli_error($db_link));

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
}
