<?php
// app/Core/Controller.php
namespace App\Core;

class Controller {
    protected function view($path, $data = []) {
        extract($data);
        require __DIR__ . "/../Views/layout/header.php";
        require __DIR__ . "/../Views/{$path}.php";
        require __DIR__ . "/../Views/layout/footer.php";
    }
}
