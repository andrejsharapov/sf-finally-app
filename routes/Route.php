<?php

//namespace Routes;

class Route
{
    public static function start()
    {
        // контроллер и действие по умолчанию
        $controller_name = 'Home';
        $action_name = 'index';
        $routes = $_GET['url'] ?? null;

        // получаем имя контроллера
        if (!empty($routes)) {
            $controller_name = $routes;
        }

        // добавляем префиксы
        $model_name = 'Model' . ucfirst($controller_name);
        $controller_name = 'Controller' . ucfirst($controller_name);

        // подцепляем файл с классом модели (файла модели может и не быть)
        $model_file = strtolower($model_name) . '.php';
        $model_path = "app/models/" . $model_file;

        if (file_exists($model_path)) {
            include "app/models/" . $model_file;
        }
        // подцепляем файл с классом контроллера
        $controller_file = strtolower($controller_name) . '.php';
        $controller_path = "app/http/controllers/" . $controller_file;

        if (file_exists($controller_path)) {
            include "app/http/controllers/" . $controller_file;
        } else {

            Route::ErrorPage404();
        }

        // создаем контроллер
        $controller = new $controller_name;
        $action = $action_name;

        if (method_exists($controller, $action)) {
            // вызываем действие контроллера
            $controller->$action();
        } else {
            Route::ErrorPage404();
        }
    }

    function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';

        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:' . $host . '404');
    }
}