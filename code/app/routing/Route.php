<?php

namespace App\Routing;

use App\Core\View;

class Route
{

    public static function start()
    {
        $controller_name = 'Home';
        $action_name = 'index';
        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (isset($routes[1]) && !empty($routes[1])) {
            $controller_name = ucfirst(strtolower($routes[1]));
        }

        if (isset($routes[2]) && !empty($routes[2])) {
            $action_name = strtolower($routes[2]);
        }

        $controller_name .= 'Controller';
        $controllerFilePath = APP_DIR . '/controllers/' . $controller_name . '.php';

        // class file existence check
        if (file_exists($controllerFilePath)) {
            include_once $controllerFilePath;
        } else {
            self::notFound();
        }

        $controller_name = "App\Controllers\\$controller_name";
        $controller = new $controller_name;

        if (!method_exists($controller, $action_name)) {
            self::notFound();
        }

        $controller->$action_name();
    }

    public static function notFound()
    {
        header('HTTP/1.1 404 Not Found');
        $view = new View();
        $view->render('notFound_view.php');
        exit;
    }
}
