<?php

session_start();

class ModelOffers extends Model
{
    protected string $db_table = 'offers';
    public $offers = null;

    public function offerInfo(array $data): array
    {
        return [
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
        if (!empty($_POST)) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];

            mysqli_query($db_link, "SET NAMES 'utf8'");

            /* check connection */
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                die();
            }

            $offer = $this->offerInfo($_POST);
            $query = mysqli_prepare($db_link, "INSERT INTO $this->db_table (title, count, url, theme, user_id, created) " . " VALUES (?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($query, "ssssss", $offer['title'], $offer['count'], $offer['url'], $offer['theme'], $offer['user_id'], $offer['created']);
            mysqli_stmt_execute($query);
            mysqli_stmt_close($query);
            mysqli_close($db_link);

            if ($query) {
                $_SESSION['checkOffer'] = 'Ваше предложение успешно создано!';
            }

            header('location: /?url=offers');
        }
    }

    public function offerList()
    {
        $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];

        $table_users = 'users';
        $table_offers = 'offers';

        $query_offer = "SELECT * FROM $table_offers LEFT JOIN $table_users ON $table_offers.user_id = $table_users.id";
        $query = mysqli_query($db_link, $query_offer) or die(mysqli_error($db_link));

        for (
            $this->offers = [];
            $row = mysqli_fetch_assoc($query);
            $this->offers[] = $row
        ) {
        }

        return $this->offers;
    }
}
