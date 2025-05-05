<?php
// app/Controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        // тут обычно запрос к модели, передаём данные в вид
        $this->view('home/index', [
            'title' => 'Main — Ticket System',
            'welcome' => 'Welcome to Tickety!'
        ]);
    }
}
