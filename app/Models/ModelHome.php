<?php

class ModelHome extends Model
{
    /**
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        return [
            'form' => $data['register'] ?? null,
            'name' => $data['login'] ?? null,
            'email' => $data['email'] ?? '',
            'password' => openssl_digest($data['password'], "sha512") ?? null,
            'date' => (new DateTime())->format('Y-m-d H:i:s') ?? null,
            'role' => $data['role'] == 3 ? 'master' : 'user',
            'role_id' => $data['role'],
        ];
    }

    public function handle()
    {
        // get user info
        if (!empty($_POST)) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $db_link = (new DB)->getDatabase() or die(mysqli_error($db_link)) ?? [];

            mysqli_query($db_link, "SET NAMES 'utf8'");

            // check connection
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                die();
            }

            // register and auth
            if ($_POST['form'] == 'register') {
                $user = $this->register($_POST);

                // empty fields
                $el = empty($_POST['login']);
                $ee = empty($_POST['email']);
                $ep = empty($_POST['password']);

                if ($ee || $ep) {
                    $_SESSION['errors'] = 'danger';
                }

                if ($el) {
                    $_SESSION['checkReg'] = 'Придумайте логин для входа.';
                }

                if ($ee) {
                    $_SESSION['checkReg'] = 'Укажите ваш email.';
                }

                if ($ep) {
                    $_SESSION['checkReg'] = 'Заполните поле для ввода пароля.';
                }

                if (!$el && !$ee && !$ep) {
                    unset($_SESSION['errors']);

                    // check login in database
                    $findUserName = "SELECT * FROM " . self::USERS . " WHERE name = '$user[name]'";
                    $resultName = mysqli_query($db_link, $findUserName) or die(mysqli_error($db_link));
                    $rowsName = mysqli_num_rows($resultName) > 0;

                    $findUserEmail = "SELECT * FROM " . self::USERS . " WHERE email = '$user[email]'";
                    $resultEmail = mysqli_query($db_link, $findUserEmail) or die(mysqli_error($db_link));
                    $rowsEmail = mysqli_num_rows($resultEmail) > 0;

                    if ($rowsName) {
                        $_SESSION['checkReg'] = 'Пользователь с таким логином уже существует.';
                        $_SESSION['errors'] = 'warning';
                    } else if ($rowsEmail) {
                        $_SESSION['checkReg'] = 'Такая почта уже зарегистрирована.';
                        $_SESSION['errors'] = 'danger';
                    } else {
                        $query = mysqli_prepare($db_link, "INSERT INTO " . self::USERS . " (name, email, password, date, role, role_id) " . " VALUES (?, ?, ?, ?, ?, ?)");

                        mysqli_stmt_bind_param($query, "ssssss", $user['name'], $user['email'], $user['password'], $user['date'], $user['role'], $user['role_id']);
                        mysqli_stmt_execute($query);
                        mysqli_stmt_close($query);
                        mysqli_close($db_link);

                        if ($query) {
                            header('Location: /');

                            $_SESSION['checkReg'] = 'Вы успешно зарегистрированы!';
                            $_SESSION['errors'] = 'success';
                        }
                    }
                }

            } else if ($_POST['form'] == 'auth') {
                $name = $_POST['email'];
                $password = !empty($_POST['password']) ? openssl_digest($_POST['password'], "sha512") : null;
                $token = $_POST['token'];

                // check token
                if ($token == $_SESSION["CSRF"]) {
                    // get user info
                    $checkUser = "SELECT * FROM " . self::USERS . " WHERE `email` = '$name' AND `password` = '$password'";
                    $auth = mysqli_query($db_link, $checkUser) or die(mysqli_error($db_link));

                    // check user in database
                    if (mysqli_num_rows($auth) > 0) {
                        $user = mysqli_fetch_assoc($auth);

                        // write data to session
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'password' => openssl_digest($user['password'], "sha512"),
                            'date' => $user['date'],
                            'role' => $user['role'],
                            'role_id' => $user['role_id'],
                        ];

                        if (isset($_SESSION['user'])) {
                            $_SESSION['checkAuth'] = 'Авторизация прошла успешно.';
                            $_SESSION['errors'] = 'success';
                        }

                        header('Location: /?url=offers');
                    } else {
                        $_SESSION['errors'] = 'danger';
                        $checkUserName = "SELECT * FROM " . self::USERS . " WHERE `email` = '$name'";
                        $userName = mysqli_query($db_link, $checkUserName) or die(mysqli_error($db_link));

                        if (mysqli_num_rows($userName) > 0) {
                            $_SESSION['checkAuth'] = 'Введен не правильный пароль.';
                        } else {
                            $_SESSION['checkAuth'] = 'Не верный логин или пароль.';
                        }
                    }
                }
            }
        }
    }
}

