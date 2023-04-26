<?php

require_once __DIR__ . '/../../dotenv.php';

$app_name = $_ENV['APP_NAME'];
$header_items = [
    ['name' => 'Главная', 'path' => '/'],
    ['name' => 'Предложения', 'path' => '/?url=offers'],
    ['name' => 'Пользователи', 'path' => '/?url=users'],
];

if (!isset($_SESSION['token'])) {
    $token = hash('gost-crypto', random_int(0, 999999));
} else {
    $token = $_SESSION['token'];
}

function setColor(): string
{
    $url = $_GET['url'] ?? null;
    $colors = [
        'secondary', 'info'
    ];

    if ($url === 'home') {
        return $colors[0];
    } else if ($url === 'users') {
        return $colors[1];
//    } else if ($url === 'error') {
//        return $colors[2];
    } else {
        return 'primary';
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    >
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $app_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Open+Sans&display=swap" rel="stylesheet">
    <link href="/public/css/app.css" rel="stylesheet">
</head>

<body>
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<header class="px-4 text-white shadow-md bg-<?= setColor(); ?>">
    <div class="container mx-auto d-flex flex-row justify-content-between align-items-center">
        <div class="text-2xl grow">
            <?= mb_strtoupper($app_name); ?>
        </div>
        <?php
        echo '<ul class="list-group list-group-horizontal align-items-center' . count($header_items) . ' ">';
        foreach ($header_items as $name => $link) {
            $l_name = $link['name'];
            $l_path = $link['path'];

            echo "<li class='border-0 list-group-item bg-transparent'>";
            echo "<a class='text-decoration-none align-items-center bg-" . setColor() . " text-white font-bold rounded' href='$l_path' title='$l_name'>$l_name</a>";
            echo "</li>";
        }

        echo "<li class='border-0 list-group-item bg-transparent'>";
        echo "<a class='text-decoration-none align-items-center bg-" . setColor() . " text-white font-bold rounded' href='https://getbootstrap.com/docs/5.3/getting-started/introduction/' title='Bootstrap' target='_blank'>Bootstrap</a>";
        echo "</li>";

        if (isset($_SESSION['user']['id'])) {
            echo "<li class='border-0 list-group-item bg-transparent'>";
            echo "<a class='text-decoration-none align-items-center bg-" . setColor() . " text-white font-bold rounded' href='/logout.php' title='Выход'>Выход</a>";
            echo "</li>";
        }

        echo '</ul>';
        ?>
    </div>
</header>

<main class="py-4">
    <div class="container mx-auto">
        <?php include $content_view; ?>
    </div>
</main>

<footer class="bg-secondary-subtle">
    <div class="container p-6 mx-auto">
        <div class="py-4 mb-auto d-flex justify-content-between">
            <?php
            $footerItems = [
                ['name' => 'Widgets 1', 'link' => [
                    ['name' => 'Link 1', 'link' => '#'],
                    ['name' => 'Link 1', 'link' => '#'],
                    ['name' => 'Link 1', 'link' => '#'],
                    ['name' => 'Link 1', 'link' => '#'],
                ]
                ],
                ['name' => 'Widgets 2', 'link' => [
                    ['name' => 'Link 2', 'link' => '#'],
                    ['name' => 'Link 2', 'link' => '#'],
                    ['name' => 'Link 2', 'link' => '#'],
                    ['name' => 'Link 2', 'link' => '#'],
                ],
                ],
                ['name' => 'Widgets 3', 'link' => [
                    ['name' => 'Link 3', 'link' => '#'],
                    ['name' => 'Link 3', 'link' => '#'],
                    ['name' => 'Link 3', 'link' => '#'],
                    ['name' => 'Link 3', 'link' => '#'],
                ],
                ],
                ['name' => 'Widgets 4', 'link' => [
                    ['name' => 'Link 4', 'link' => '#'],
                    ['name' => 'Link 4', 'link' => '#'],
                    ['name' => 'Link 4', 'link' => '#'],
                    ['name' => 'Link 4', 'link' => '#'],
                ],
                ]
            ];

            foreach ($footerItems as $name => $link) {
                $l_name = $link['name'];
                $l_path = $link['link'];

                echo '<div>';
                echo "<h5 class='uppercase font-bold mb-2.5 text-secondary' >$l_name</h5>";
                echo '<ul class="list-group pl-0">';

                foreach ($link['link'] as $key => $val) {
                    $v_name = $val['name'];
                    $v_path = $val['link'];

                    echo "<li class='list-group-item bg-transparent'><a class='text-secondary' href='$v_path' title='$v_name'>$v_name</a></li>";
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="text-center text-white p-2 bg-<?= setColor(); ?>">
        &copy; 2023
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/public/js/app.js"></script>

</body>

</html>

<?php
$_SESSION["CSRF"] = $token;
?>
