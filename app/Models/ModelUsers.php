<?php

session_start();

class ModelUsers extends Model
{

    protected string $db_table = 'users';

    /**
     * @return array
     */
    public function handle()
    {
        $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];
        $query = "SELECT * FROM " . $this->db_table . " WHERE id > 0";
        $result = mysqli_query($db_link, $query) or die(mysqli_error($db_link));

        for (
            $data = [];
            $row = mysqli_fetch_assoc($result);
            $data[] = $row
        ) ;

        return $data;
    }
}