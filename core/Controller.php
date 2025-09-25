<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        
        require_once __DIR__ . "/../app/views/$view.php";

    }
    public function abort($code = 404)
    {
        http_response_code($code);
        $error_code = $code;
        require_once __DIR__ . '/../app/views/error/error.php';
        die();
    }
}
