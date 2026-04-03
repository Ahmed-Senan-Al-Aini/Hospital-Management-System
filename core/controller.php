<?php

class Controller
{
    public function middleware()
    {
        return [];
    }

    protected function view($view, $data = [])
    {
        extract($data);

        $veiwfile = VIEWS_PATH . str_replace('.', DS, $view) . '.php';
        if (!file_exists($veiwfile)) {
            die(" File: " . $veiwfile . " Not Found");
        }

        require VIEWS_PATH . 'layouts' . DS . 'header.php';

        if (Auth::check()) {
            require VIEWS_PATH . 'layouts' . DS . 'sidebar.php';
        }

        require $veiwfile;

        require VIEWS_PATH . 'layouts' . DS . 'footer.php';
    }


    protected function json($data, $statuscode = 200)
    {
        http_response_code($statuscode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        error_log($url);
        header('Location:' . BASE_URL . $url);
        exit;
    }

    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
