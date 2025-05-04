<?php
// app/Controllers/HomeController.php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        // тут обычно запрос к модели, передаём данные в вид
        $this->view('home/index', [
            'title' => 'Главная — Ticket System',
            'welcome' => 'Добро пожаловать в систему тикетов!'
        ]);
    }
}
