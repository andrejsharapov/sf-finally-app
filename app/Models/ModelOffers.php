<?php

session_start();

class ModelOffers extends Model
{
    protected string $db_table = 'offers';
    public $offers = null;

    public function getOffersInfo(array $data): array
    {
        return [
            'form' => $data['form'],
            'user_id' => $_SESSION['user']['id'] ?? null,
            'created' => (new DateTime())->format('Y-m-d H:i:s') ?? null,
            'title' => $data['title'],
            'count' => $data['count'],
            'url' => $data['path'],
            'theme' => $data['theme'],
        ];
    }

    public function handle()
    {
        if (!empty($_POST) && $_POST['form'] == 'create_offer') {
            $db_link = $this->dataBaseLink();
            $offer = $this->getOffersInfo($_POST);
            $query = mysqli_prepare($db_link, "INSERT INTO $this->db_table (title, count, url, theme, user_id, created) " . " VALUES (?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($query, "ssssss", $offer['title'], $offer['count'], $offer['url'], $offer['theme'], $offer['user_id'], $offer['created']);
            mysqli_stmt_execute($query);
            mysqli_stmt_close($query);
            mysqli_close($db_link);

            if ($query) {
                $_SESSION['checkOffer'] = 'Ваше предложение успешно создано!';
            }

            header('refresh: 0');
        }
    }

    public function offerList()
    {
        $db_link = $this->dataBaseLink();
        $table_offers = $this->db_table;
        $table_users = 'users';

        $query_offer = "SELECT * FROM $table_users LEFT JOIN $table_offers ON $table_offers.user_id = $table_users.id";
        $set_list = mysqli_query($db_link, $query_offer) or die(mysqli_error($db_link));

        for (
            $this->offers = [];
            $row = mysqli_fetch_assoc($set_list);
            $this->offers[] = $row
        ) {
        }

        return $this->offers;
    }

    public function unActivateOffer()
    {
        $db_link = $this->dataBaseLink();
        $data = $_POST;
        $id = $data['val_id'] ?? null;
        $val = $data['set_state'] ?? null;

        if (!empty($_POST) && $_POST['form'] == 'inactive_offer') {
            $update_offer_state = "UPDATE $this->db_table SET state = '$val' WHERE id = '$id'";

            mysqli_query($db_link, $update_offer_state) or die(mysqli_error($db_link));
            header('refresh : 0');
        }
    }

    public function activateOffer()
    {
        $db_link = $this->dataBaseLink();
        $data = $_POST;
        $id = $data['val_id'] ?? null;
        $val = $data['set_state'] ?? null;

        if (!empty($_POST) && $_POST['form'] == 'active_offer') {
            $update_offer_state = "UPDATE $this->db_table SET state = '$val' WHERE id = '$id'";

            mysqli_query($db_link, $update_offer_state) or die(mysqli_error($db_link));
            header('refresh : 0');
        }
    }

    public function dataBaseLink()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];

        /* charset */
        mysqli_query($db_link, "SET NAMES 'utf8'");

        /* check connection */
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            die();
        }

        return $db_link;
    }
}
