<?php
// core/Router.php

class Router
{
    private $url;
    private $controller;
    private $method = 'index';
    private $params = [];

    public function __construct()
    {

        $this->url = $this->parseUrl();
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            // تنظيف الـ URL ومنع الهجمات
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return ['Dashboard'];
    }

    public function dispatch($url = null)
    {

        if ($url !== null) {
            $this->url = $this->parseUrl();
        }

        // تحديد الـ Controller
        if (!empty($this->url) && isset($this->url[0])) {
            $controllerName = ucfirst(strtolower($this->url[0])) . 'Controller';
            $controllerFile = CONTROLLERS_PATH . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                error_log($controllerName . "  sid");
                unset($this->url[0]);
            }
        }

        // تضمين ملف الـ Controller
        // $controllerFile = CONTROLLERS_PATH . $this->controller . '.php';
        $controllerFile = CONTROLLERS_PATH . $controllerName . '.php';
        error_log($controllerFile . "  file");
        if (!file_exists($controllerFile)) {
            error_log($controllerFile . "  exit");
            $this->notFound();
        }
        error_log($controllerFile . "  fff");

        
        require_once $controllerFile;



        // إنشاء كائن الـ Controller
        if (!class_exists($this->controller)) {
            error_log($controllerFile . "  not found");
            $this->notFound();
        }
        $this->controller = new $this->controller();


        // تحديد الـ Method
        if (!empty($this->url) && isset($this->url[1])) {
            $methodName = $this->url[1];
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                error_log($this->method);
                unset($this->url[1]);
            }
        }

        // تحديد الـ Parameters
        $this->params = !empty($this->url) ? array_values($this->url) : [];
        

        if (method_exists($this->controller, 'middleware')) {
            $middleware = $this->controller->middleware();
         

            if (isset($middleware[$this->method])) {
                $requiredRoles = $middleware[$this->method];
                if (!is_array($requiredRoles)) {
                    die('خطاء');
                }
                if (!empty($requiredRoles) ) {
                    Middleware::requir_Any_role($requiredRoles);
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Middleware::requireCsrfToken();
        }
        error_log("hhhh");
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        if (file_exists(VIEWS_PATH . 'errors/404.php')) {
            require VIEWS_PATH . 'errors/404.php';
        } else {
            echo "<h1>404 - الصفحة غير موجودة</h1>";
        }
        exit;
    }
}
